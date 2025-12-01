<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\ExpenseEntry;
use App\Models\ExpenseSuperCategory;
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
        
        return view('mobile.expense.create', [
            'category' => $category,
        ]);
    }
    
    public function store(Request $request, $categoryId)
    {
        $userId = Auth::id();
        $category = ExpenseCategory::forUser($userId)->findOrFail($categoryId);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:255',
            'is_save_for_later' => 'boolean',
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
}
