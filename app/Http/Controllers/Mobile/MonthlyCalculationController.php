<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ExpenseEntry;
use App\Models\IncomeEntry;
use App\Models\SavingsGoal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonthlyCalculationController extends Controller
{
    public function calculate(Request $request)
    {
        $user = Auth::user();
        $now = Carbon::now();
        
        // Calculate previous month
        $previousMonth = $now->copy()->subMonth();
        $previousMonthStart = $previousMonth->copy()->startOfMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();
        
        // Check if already calculated - check ALL goals
        $allGoals = $user->savingsGoals()->get();
        
        // Check if any goal was already calculated for previous month
        // If last_monthly_calculation_at is after the end of previous month, it means previous month was calculated
        $alreadyCalculated = false;
        foreach ($allGoals as $goal) {
            if ($goal->last_monthly_calculation_at) {
                $lastCalc = Carbon::parse($goal->last_monthly_calculation_at);
                // If calculation was done after the previous month ended, it means previous month was calculated
                if ($lastCalc->isAfter($previousMonthEnd)) {
                    $alreadyCalculated = true;
                    break;
                }
            }
        }
        
        if ($alreadyCalculated) {
            return redirect()->route('mobile.dashboard')
                ->with('error', __('common.monthly_calculation_already_done'));
        }
        
        // Calculate previous month's net savings (income - expenses)
        $previousMonthIncome = IncomeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$previousMonthStart, $previousMonthEnd])
            ->sum('amount');
        
        $previousMonthExpenses = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$previousMonthStart, $previousMonthEnd])
            ->sum('amount');
        
        $netSavings = $previousMonthIncome - $previousMonthExpenses;
        
        // Get ALL goals (not just active ones) - we want to update all goals with the net savings
        $goalsToUpdate = $user->savingsGoals()->get();
        
        if ($goalsToUpdate->isEmpty()) {
            return redirect()->route('mobile.dashboard')
                ->with('error', __('common.no_savings_goals'));
        }
        
        // Directly modify all goals' current_amount
        $calculationDate = $now;
        DB::beginTransaction();
        try {
            foreach ($goalsToUpdate as $goal) {
                $goal->increment('current_amount', $netSavings);
                $goal->update(['last_monthly_calculation_at' => $calculationDate]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('mobile.dashboard')
                ->with('error', 'Error calculating monthly savings: ' . $e->getMessage());
        }
        
        return redirect()->route('mobile.dashboard')
            ->with('success', __('common.monthly_calculation_completed', [
                'month' => $previousMonth->format('F Y'),
                'amount' => number_format($netSavings, 2)
            ]));
    }
}
