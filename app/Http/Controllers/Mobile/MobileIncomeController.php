<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\IncomeCategory;
use App\Models\IncomeEntry;
use App\Models\Person;
use App\Models\SavingsGoal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileIncomeController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $categories = IncomeCategory::forUser($userId)->get();

        return view('mobile.income.index', [
            'categories' => $categories,
        ]);
    }

    public function create($categoryId)
    {
        $userId = Auth::id();
        $category = IncomeCategory::forUser($userId)->findOrFail($categoryId);

        // Check if previous month was calculated
        $previousMonthCalculated = $this->isPreviousMonthCalculated($userId);
        $minDate = $previousMonthCalculated
            ? Carbon::now()->startOfMonth()
            : Carbon::now()->subMonth()->startOfMonth();

        $persons = Person::where('user_id', $userId)
            ->orderBy('fullname')
            ->get();

        return view('mobile.income.create', [
            'category' => $category,
            'minDate' => $minDate,
            'maxDate' => Carbon::now()->endOfMonth(),
            'previousMonthCalculated' => $previousMonthCalculated,
            'persons' => $persons,
        ]);
    }

    public function store(Request $request, $categoryId)
    {
        $userId = Auth::id();
        $category = IncomeCategory::forUser($userId)->findOrFail($categoryId);

        // Check if previous month was calculated
        $previousMonthCalculated = $this->isPreviousMonthCalculated($userId);
        $minDate = $previousMonthCalculated
            ? Carbon::now()->startOfMonth()
            : Carbon::now()->subMonth()->startOfMonth();

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date|after_or_equal:'.$minDate->format('Y-m-d').'|before_or_equal:'.Carbon::now()->endOfMonth()->format('Y-m-d'),
            'notes' => 'nullable|string|max:255',
            'person_id' => 'nullable|exists:persons,id',
        ], [
            'date.after_or_equal' => $previousMonthCalculated
                ? __('common.cannot_create_past_month_entry')
                : __('common.can_only_create_current_or_previous_month'),
            'date.before_or_equal' => __('common.can_only_create_current_month'),
        ]);

        IncomeEntry::create([
            'user_id' => $userId,
            'income_category_id' => $categoryId,
            'person_id' => $validated['person_id'] ?? null,
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('mobile.dashboard')->with('success', __('common.created_successfully'));
    }

    protected function isPreviousMonthCalculated(int $userId): bool
    {
        $previousMonth = Carbon::now()->subMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();

        $allGoals = SavingsGoal::where('user_id', $userId)->get();

        foreach ($allGoals as $goal) {
            if ($goal->last_monthly_calculation_at) {
                $lastCalc = Carbon::parse($goal->last_monthly_calculation_at);
                if ($lastCalc->isAfter($previousMonthEnd)) {
                    return true;
                }
            }
        }

        return false;
    }
}
