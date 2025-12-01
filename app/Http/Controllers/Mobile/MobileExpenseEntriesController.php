<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ExpenseEntry;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSuperCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileExpenseEntriesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = ExpenseEntry::where('user_id', $user->id)
            ->with(['expenseCategory.expenseSuperCategory']);
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('amount', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('expenseCategory', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('expense_category_id', $request->category_id);
        }
        
        // Filter by super category
        if ($request->has('super_category_id') && $request->super_category_id) {
            $query->whereHas('expenseCategory', function($q) use ($request) {
                $q->where('expense_super_category_id', $request->super_category_id);
            });
        }
        
        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->where('date', '<=', $request->date_to);
        }
        
        $entries = $query->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $categories = ExpenseCategory::where('user_id', $user->id)
            ->orWhere('is_system', true)
            ->orderBy('name')
            ->get();
        
        $superCategories = ExpenseSuperCategory::where('is_system', true)
            ->orderBy('name')
            ->get();
        
        return view('mobile.expense-entries.index', compact('entries', 'categories', 'superCategories'));
    }
    
    public function edit($id)
    {
        $entry = ExpenseEntry::where('user_id', Auth::id())
            ->with(['expenseCategory.expenseSuperCategory'])
            ->findOrFail($id);
        
        // Check if previous month was calculated
        $previousMonthCalculated = $this->isPreviousMonthCalculated();
        $minDate = $previousMonthCalculated 
            ? Carbon::now()->startOfMonth() 
            : Carbon::now()->subMonth()->startOfMonth();
        
        $categories = ExpenseCategory::where('user_id', Auth::id())
            ->orWhere('is_system', true)
            ->orderBy('name')
            ->get();
        
        return view('mobile.expense-entries.edit', compact('entry', 'categories', 'minDate', 'previousMonthCalculated'));
    }
    
    public function update(Request $request, $id)
    {
        $entry = ExpenseEntry::where('user_id', Auth::id())->findOrFail($id);
        
        // Check if entry is from a month before previous month (more than 1 month ago)
        $entryMonth = Carbon::parse($entry->date)->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        if ($entryMonth->lt($previousMonth)) {
            return redirect()->route('mobile.expense-entries.index')
                ->with('error', __('common.cannot_edit_past_month_entry'));
        }
        
        // Check if previous month was calculated (for date validation)
        $previousMonthCalculated = $this->isPreviousMonthCalculated();
        $minDate = $previousMonthCalculated 
            ? Carbon::now()->startOfMonth() 
            : Carbon::now()->subMonth()->startOfMonth();
        
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
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
        
        $oldSaveForLater = $entry->is_save_for_later;
        $oldAmount = $entry->amount;
        $oldCategory = $entry->expenseCategory;
        $oldWasSavingsCategory = $oldCategory 
            && $oldCategory->expenseSuperCategory 
            && $oldCategory->expenseSuperCategory->name === 'savings';
        
        $entry->update($validated);
        $entry->refresh();
        
        $currentIsSavingsCategory = $entry->expenseCategory 
            && $entry->expenseCategory->expenseSuperCategory 
            && $entry->expenseCategory->expenseSuperCategory->name === 'savings';
        
        // If it was save for later or Savings category before, remove the previous amount
        if ($oldSaveForLater || $oldWasSavingsCategory) {
            $this->removeFromSavingsGoals($entry, $oldAmount);
        }
        
        // If it's now save for later or Savings category, add the current amount
        if ($entry->is_save_for_later || $currentIsSavingsCategory) {
            $this->addToSavingsGoals($entry);
        }
        
        return redirect()->route('mobile.expense-entries.index')
            ->with('success', __('common.updated_successfully'));
    }
    
    public function destroy($id)
    {
        $entry = ExpenseEntry::where('user_id', Auth::id())
            ->with(['expenseCategory.expenseSuperCategory'])
            ->findOrFail($id);
        
        // Check if entry is from a past month (month has ended)
        $entryMonth = Carbon::parse($entry->date)->startOfMonth();
        $currentMonth = Carbon::now()->startOfMonth();
        
        if ($entryMonth->lt($currentMonth)) {
            return redirect()->route('mobile.expense-entries.index')
                ->with('error', __('common.cannot_delete_past_month_entry'));
        }
        
        // Remove from savings goals if it was save for later or Savings category
        $isSavingsCategory = $entry->expenseCategory 
            && $entry->expenseCategory->expenseSuperCategory 
            && $entry->expenseCategory->expenseSuperCategory->name === 'savings';
        
        if ($entry->is_save_for_later || $isSavingsCategory) {
            $this->removeFromSavingsGoals($entry, $entry->amount);
        }
        
        $entry->delete();
        
        return redirect()->route('mobile.expense-entries.index')
            ->with('success', __('common.deleted_successfully'));
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
    
    protected function removeFromSavingsGoals(ExpenseEntry $entry, float $amount): void
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
            $goal->decrement('current_amount', $amount);
            // Allow negative values - don't clamp to 0
        }
    }
    
    protected function isPreviousMonthCalculated(): bool
    {
        $userId = Auth::id();
        $previousMonth = Carbon::now()->subMonth();
        $previousMonthEnd = $previousMonth->copy()->endOfMonth();
        
        $allGoals = \App\Models\SavingsGoal::where('user_id', $userId)->get();
        
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
