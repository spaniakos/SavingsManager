<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\IncomeEntry;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileIncomeEntriesController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = IncomeEntry::where('user_id', $user->id)
            ->with('incomeCategory');
        
        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('amount', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('incomeCategory', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->where('income_category_id', $request->category_id);
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
        
        $categories = IncomeCategory::where('user_id', $user->id)
            ->orWhere('is_system', true)
            ->orderBy('name')
            ->get();
        
        return view('mobile.income-entries.index', compact('entries', 'categories'));
    }
    
    public function edit($id)
    {
        $entry = IncomeEntry::where('user_id', Auth::id())
            ->with('incomeCategory')
            ->findOrFail($id);
        
        $categories = IncomeCategory::where('user_id', Auth::id())
            ->orWhere('is_system', true)
            ->orderBy('name')
            ->get();
        
        return view('mobile.income-entries.edit', compact('entry', 'categories'));
    }
    
    public function update(Request $request, $id)
    {
        $entry = IncomeEntry::where('user_id', Auth::id())->findOrFail($id);
        
        $validated = $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'notes' => 'nullable|string|max:255',
        ]);
        
        $entry->update($validated);
        
        return redirect()->route('mobile.income-entries.index')
            ->with('success', __('common.updated_successfully'));
    }
    
    public function destroy($id)
    {
        $entry = IncomeEntry::where('user_id', Auth::id())->findOrFail($id);
        $entry->delete();
        
        return redirect()->route('mobile.income-entries.index')
            ->with('success', __('common.deleted_successfully'));
    }
}
