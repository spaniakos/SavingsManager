<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\ExpenseSuperCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileExpenseSuperCategoriesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $superCategories = ExpenseSuperCategory::where('is_system', true)
            ->orderBy('name')
            ->get();
        
        return view('mobile.expense-super-categories.index', compact('superCategories'));
    }
    
    public function edit($id)
    {
        $superCategory = ExpenseSuperCategory::where('is_system', true)
            ->findOrFail($id);
        
        return view('mobile.expense-super-categories.edit', compact('superCategory'));
    }
    
    public function update(Request $request, $id)
    {
        $superCategory = ExpenseSuperCategory::where('is_system', true)
            ->findOrFail($id);
        
        $validated = $request->validate([
            'emoji' => 'nullable|string|max:10',
            'allocation_percentage' => 'required|numeric|min:0|max:100',
        ]);
        
        $superCategory->update($validated);
        
        return redirect()->route('mobile.expense-super-categories.index')
            ->with('success', __('common.updated_successfully'));
    }
}

