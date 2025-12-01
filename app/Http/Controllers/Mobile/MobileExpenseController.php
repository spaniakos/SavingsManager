<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\ExpenseEntry;
use App\Models\ExpenseSuperCategory;
use App\Models\SavingsGoal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileExpenseController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $superCategories = ExpenseSuperCategory::forUser($userId)->get();
        
        return view('mobile.expense.index', [
            'superCategories' => $superCategories,
        ]);
    }
    
    public function showCategories($superCategoryId)
    {
        $userId = Auth::id();
        $superCategory = ExpenseSuperCategory::forUser($userId)->findOrFail($superCategoryId);
        $categories = ExpenseCategory::forUser($userId)
            ->where('expense_super_category_id', $superCategoryId)
            ->get();
        
        return view('mobile.expense.categories', [
            'superCategory' => $superCategory,
            'categories' => $categories,
        ]);
    }
    
    public function create($categoryId)
    {
        $userId = Auth::id();
        $category = ExpenseCategory::forUser($userId)->findOrFail($categoryId);
        
        // Check if previous month was calculated
        $previousMonthCalculated = $this->isPreviousMonthCalculated($userId);
        $minDate = $previousMonthCalculated 
            ? Carbon::now()->startOfMonth() 
            : Carbon::now()->subMonth()->startOfMonth();
        
        return view('mobile.expense.create', [
            'category' => $category,
            'minDate' => $minDate,
            'maxDate' => Carbon::now()->endOfMonth(),
            'previousMonthCalculated' => $previousMonthCalculated,
        ]);
    }
    
    public function store(Request $request, $categoryId)
    {
        $userId = Auth::id();
        $category = ExpenseCategory::forUser($userId)->findOrFail($categoryId);
        
        // Check if previous month was calculated
        $previousMonthCalculated = $this->isPreviousMonthCalculated($userId);
        $minDate = $previousMonthCalculated 
            ? Carbon::now()->startOfMonth() 
            : Carbon::now()->subMonth()->startOfMonth();
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date|after_or_equal:' . $minDate->format('Y-m-d') . '|before_or_equal:' . Carbon::now()->endOfMonth()->format('Y-m-d'),
            'notes' => 'nullable|string|max:255',
            'is_save_for_later' => 'boolean',
        ], [
            'date.after_or_equal' => $previousMonthCalculated 
                ? __('common.cannot_create_past_month_entry') 
                : __('common.can_only_create_current_or_previous_month'),
            'date.before_or_equal' => __('common.can_only_create_current_month'),
        ]);
        
        $expense = ExpenseEntry::create([
            'user_id' => $userId,
            'expense_category_id' => $categoryId,
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
            'is_save_for_later' => $validated['is_save_for_later'] ?? false,
        ]);
        
        // Handle save for later - add to savings goals
        if ($expense->is_save_for_later) {
            $this->addToSavingsGoals($expense);
        }
        
        // Handle Savings category expenses - add to savings goals
        if ($category->expenseSuperCategory && $category->expenseSuperCategory->name === 'savings') {
            $this->addToSavingsGoals($expense);
        }
        
        return redirect('/admin/mobile')->with('success', __('common.created_successfully'));
    }
    
    protected function addToSavingsGoals(ExpenseEntry $entry): void
    {
        $user = $entry->user;
        $goals = $user->savingsGoals()
            ->where('start_date', '<=', $entry->date)
            ->where(function ($query) use ($entry) {
                $query->whereNull('target_date')
                    ->orWhere('target_date', '>=', $entry->date);
            })
            ->get();
        
        foreach ($goals as $goal) {
            $goal->increment('current_amount', $entry->amount);
        }
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
