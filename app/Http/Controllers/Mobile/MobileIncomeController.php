<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\IncomeCategory;
use App\Models\IncomeEntry;
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
        
        return view('mobile.income.create', [
            'category' => $category,
        ]);
    }
    
    public function store(Request $request, $categoryId)
    {
        $userId = Auth::id();
        $category = IncomeCategory::forUser($userId)->findOrFail($categoryId);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);
        
        IncomeEntry::create([
            'user_id' => $userId,
            'income_category_id' => $categoryId,
            'amount' => $validated['amount'],
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
        ]);
        
        return redirect('/admin/mobile')->with('success', __('common.created_successfully'));
    }
}
