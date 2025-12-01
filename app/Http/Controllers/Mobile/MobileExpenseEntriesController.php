<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ExpenseEntry;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSuperCategory;
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
        
        $categories = ExpenseCategory::where('user_id', Auth::id())
            ->orWhere('is_system', true)
            ->orderBy('name')
            ->get();
        
        return view('mobile.expense-entries.edit', compact('entry', 'categories'));
    }
    
    public function update(Request $request, $id)
    {
        $entry = ExpenseEntry::where('user_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:255',
            'is_save_for_later' => 'boolean',
        ]);
        
        $oldSaveForLater = $entry->is_save_for_later;
        $oldAmount = $entry->amount;
        
        $entry->update($validated);
        
        // Handle save for later changes
        if ($oldSaveForLater && !$entry->is_save_for_later) {
            // Remove from savings goals
            $this->removeFromSavingsGoals($entry, $oldAmount);
        } elseif (!$oldSaveForLater && $entry->is_save_for_later) {
            // Add to savings goals
            $this->addToSavingsGoals($entry);
        } elseif ($oldSaveForLater && $entry->is_save_for_later && $oldAmount != $entry->amount) {
            // Update amount in savings goals
            $this->removeFromSavingsGoals($entry, $oldAmount);
            $this->addToSavingsGoals($entry);
        }
        
        return redirect()->route('mobile.expense-entries.index')
            ->with('success', __('common.updated_successfully'));
    }
    
    public function destroy($id)
    {
        $entry = ExpenseEntry::where('user_id', Auth::id())->findOrFail($id);
        
        // Remove from savings goals if it was save for later
        if ($entry->is_save_for_later) {
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
            if ($goal->current_amount < 0) {
                $goal->update(['current_amount' => 0]);
            }
        }
    }
}
