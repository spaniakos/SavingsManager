<?php

namespace App\Services;

use App\Models\ExpenseEntry;
use App\Models\IncomeEntry;
use App\Models\SavingsGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Calculate total saved money (seed_capital + sum(initial_checkpoint + current_amount for all goals))
     * Note: save_for_later expenses are NOT added separately because they already modify current_amount
     */
    public function calculateTotalSaved(User $user): float
    {
        $seedCapital = $user->seed_capital ?? 0;
        
        // Sum of all savings goals (initial_checkpoint + current_amount)
        // Note: current_amount already includes contributions from Savings category expenses and save_for_later expenses
        $savingsGoalsTotal = SavingsGoal::where('user_id', $user->id)
            ->get()
            ->sum(function ($goal) {
                return ($goal->initial_checkpoint ?? 0) + ($goal->current_amount ?? 0);
            });
        
        return round($seedCapital + $savingsGoalsTotal, 2);
    }

    /**
     * Generate monthly report for a user
     */
    public function generateMonthlyReport(User $user, Carbon $month): array
    {
        $startDate = $month->copy()->startOfMonth();
        $endDate = $month->copy()->endOfMonth();

        $income = IncomeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $expenses = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');

        $savings = $income - $expenses;
        $savingsRate = $income > 0 ? ($savings / $income) * 100 : 0;
        $totalSaved = $this->calculateTotalSaved($user);

        $incomeByCategory = IncomeEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('income_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('income_category_id')
            ->with('incomeCategory')
            ->get()
            ->mapWithKeys(function ($entry) {
                return [$entry->incomeCategory->getTranslatedName() => $entry->total];
            });

        $expensesByCategory = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->select('expense_category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('expense_category_id')
            ->with('expenseCategory')
            ->get()
            ->mapWithKeys(function ($entry) {
                return [$entry->expenseCategory->getTranslatedName() => $entry->total];
            });

        return [
            'month' => $month->format('F Y'),
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_income' => $income,
                'total_expenses' => $expenses,
                'net_savings' => $savings,
                'savings_rate' => round($savingsRate, 2),
                'total_saved' => $totalSaved,
            ],
            'income_by_category' => $incomeByCategory,
            'expenses_by_category' => $expensesByCategory,
            'savings_goals_progress' => $this->getSavingsGoalsProgress($user, $month),
        ];
    }

    /**
     * Generate category-wise expense report
     */
    public function generateCategoryExpenseReport(User $user, Carbon $startDate, Carbon $endDate, ?int $categoryId = null): array
    {
        $query = ExpenseEntry::where('user_id', $user->id)
            ->whereBetween('date', [$startDate, $endDate]);

        if ($categoryId) {
            $query->where('expense_category_id', $categoryId);
        }

        $expenses = $query->with('expenseCategory.expenseSuperCategory')
            ->get()
            ->groupBy(function ($entry) {
                return $entry->expenseCategory->expenseSuperCategory->getTranslatedName();
            })
            ->map(function ($group) {
                return [
                    'total' => $group->sum('amount'),
                    'count' => $group->count(),
                    'categories' => $group->groupBy('expense_category_id')
                        ->map(function ($categoryGroup) {
                            $category = $categoryGroup->first()->expenseCategory;
                            return [
                                'name' => $category->getTranslatedName(),
                                'total' => $categoryGroup->sum('amount'),
                                'count' => $categoryGroup->count(),
                            ];
                        })->values(),
                ];
            });

        $totalSaved = $this->calculateTotalSaved($user);
        
        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'expenses_by_super_category' => $expenses,
            'total_expenses' => $expenses->sum('total'),
            'total_saved' => $totalSaved,
        ];
    }

    /**
     * Generate savings goal progress report
     */
    public function generateSavingsGoalReport(User $user, ?Carbon $month = null): array
    {
        $month = $month ?? Carbon::now();

        $goals = SavingsGoal::where('user_id', $user->id)
            ->get();

        $calculator = app(SavingsCalculatorService::class);

        $totalSaved = $this->calculateTotalSaved($user);
        
        return [
            'month' => $month->format('F Y'),
            'goals' => $goals->map(function ($goal) use ($calculator) {
                return [
                    'id' => $goal->id,
                    'name' => $goal->name,
                    'target_amount' => $goal->target_amount,
                    'current_amount' => $goal->current_amount,
                    'progress_percentage' => $calculator->calculateOverallProgress($goal),
                    'monthly_saving_needed' => $calculator->calculateMonthlySavingNeeded($goal),
                    'months_remaining' => $calculator->calculateMonthsRemaining($goal),
                ];
            }),
            'total_target' => $goals->sum('target_amount'),
            'total_current' => $goals->sum('current_amount'),
            'total_progress' => $goals->sum('target_amount') > 0 
                ? ($goals->sum('current_amount') / $goals->sum('target_amount')) * 100 
                : 0,
            'total_saved' => $totalSaved,
        ];
    }

    /**
     * Get savings goals progress for a month
     */
    protected function getSavingsGoalsProgress(User $user, Carbon $month): array
    {
        $goals = SavingsGoal::where('user_id', $user->id)
            ->get();

        $calculator = app(SavingsCalculatorService::class);

        return $goals->map(function ($goal) use ($calculator) {
            return [
                'name' => $goal->name,
                'progress' => $calculator->calculateOverallProgress($goal),
            ];
        })->toArray();
    }

    /**
     * Export data to CSV format
     */
    public function exportToCsv(array $data, string $filename = 'report.csv'): string
    {
        $csv = fopen('php://temp', 'r+');

        // Write headers
        if (!empty($data)) {
            $headers = array_keys(is_array($data[0]) ? $data[0] : $data);
            fputcsv($csv, $headers);
        }

        // Write data
        foreach ($data as $row) {
            if (is_array($row)) {
                fputcsv($csv, array_values($row));
            } else {
                fputcsv($csv, [$row]);
            }
        }

        rewind($csv);
        $content = stream_get_contents($csv);
        fclose($csv);

        return $content;
    }
}

