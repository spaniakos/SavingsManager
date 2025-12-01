<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSuperCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileExpenseCategoriesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $categories = ExpenseCategory::where('user_id', $user->id)
            ->orWhere('is_system', true)
            ->with('expenseSuperCategory')
            ->orderBy('name')
            ->get();
        
        $superCategories = ExpenseSuperCategory::where('is_system', true)
            ->orderBy('name')
            ->get();
        
        return view('mobile.expense-categories.index', compact('categories', 'superCategories'));
    }
    
    public function create()
    {
        $user = Auth::user();
        $superCategories = ExpenseSuperCategory::where('is_system', true)
            ->orderBy('name')
            ->get();
        
        return view('mobile.expense-categories.create', compact('superCategories'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emoji' => 'nullable|string|max:10',
            'expense_super_category_id' => 'required|exists:expense_super_categories,id',
        ]);
        
        ExpenseCategory::create([
            'user_id' => Auth::id(),
            'is_system' => false,
            'name' => $validated['name'],
            'emoji' => $validated['emoji'] ?? null,
            'expense_super_category_id' => $validated['expense_super_category_id'],
        ]);
        
        return redirect()->route('mobile.expense-categories.index')
            ->with('success', __('common.created_successfully'));
    }
    
    public function edit($id)
    {
        $category = ExpenseCategory::where('user_id', Auth::id())
            ->where('is_system', false)
            ->findOrFail($id);
        
        $superCategories = ExpenseSuperCategory::where('is_system', true)
            ->orderBy('name')
            ->get();
        
        return view('mobile.expense-categories.edit', compact('category', 'superCategories'));
    }
    
    public function update(Request $request, $id)
    {
        $category = ExpenseCategory::where('user_id', Auth::id())
            ->where('is_system', false)
            ->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emoji' => 'nullable|string|max:10',
            'expense_super_category_id' => 'required|exists:expense_super_categories,id',
        ]);
        
        $category->update($validated);
        
        return redirect()->route('mobile.expense-categories.index')
            ->with('success', __('common.updated_successfully'));
    }
    
    public function destroy($id)
    {
        $category = ExpenseCategory::where('user_id', Auth::id())
            ->where('is_system', false)
            ->findOrFail($id);
        
        $category->delete();
        
        return redirect()->route('mobile.expense-categories.index')
            ->with('success', __('common.deleted_successfully'));
    }
}
