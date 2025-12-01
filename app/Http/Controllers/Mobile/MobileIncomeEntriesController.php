<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\IncomeEntry;
use App\Models\IncomeCategory;
use Carbon\Carbon;
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
        
        // Check if previous month was calculated
        $previousMonthCalculated = $this->isPreviousMonthCalculated();
        
        return view('mobile.income-entries.index', compact('entries', 'categories', 'previousMonthCalculated'));
    }
    
    public function edit($id)
    {
        $entry = IncomeEntry::where('user_id', Auth::id())
            ->with('incomeCategory')
            ->findOrFail($id);
        
        // Check if previous month was calculated
        $previousMonthCalculated = $this->isPreviousMonthCalculated();
        $minDate = $previousMonthCalculated 
            ? Carbon::now()->startOfMonth() 
            : Carbon::now()->subMonth()->startOfMonth();
        
        $categories = IncomeCategory::where('user_id', Auth::id())
            ->orWhere('is_system', true)
            ->orderBy('name')
            ->get();
        
        return view('mobile.income-entries.edit', compact('entry', 'categories', 'minDate', 'previousMonthCalculated'));
    }
    
    public function update(Request $request, $id)
    {
        $entry = IncomeEntry::where('user_id', Auth::id())->findOrFail($id);
        
        // Check if entry is from a month before previous month (more than 1 month ago)
        $entryMonth = Carbon::parse($entry->date)->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();
        
        if ($entryMonth->lt($previousMonth)) {
            return redirect()->route('mobile.income-entries.index')
                ->with('error', __('common.cannot_edit_past_month_entry'));
        }
        
        // Check if previous month was calculated (for date validation)
        $previousMonthCalculated = $this->isPreviousMonthCalculated();
        $minDate = $previousMonthCalculated 
            ? Carbon::now()->startOfMonth() 
            : Carbon::now()->subMonth()->startOfMonth();
        
        $validated = $request->validate([
            'income_category_id' => 'required|exists:income_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date|after_or_equal:' . $minDate->format('Y-m-d') . '|before_or_equal:' . Carbon::now()->endOfMonth()->format('Y-m-d'),
            'notes' => 'nullable|string|max:255',
        ], [
            'date.after_or_equal' => $previousMonthCalculated 
                ? __('common.cannot_create_past_month_entry') 
                : __('common.can_only_create_current_or_previous_month'),
            'date.before_or_equal' => __('common.can_only_create_current_month'),
        ]);
        
        $entry->update($validated);
        
        return redirect()->route('mobile.income-entries.index')
            ->with('success', __('common.updated_successfully'));
    }
    
    public function destroy($id)
    {
        $entry = IncomeEntry::where('user_id', Auth::id())->findOrFail($id);
        
        // Check if entry is from a past month (month has ended)
        $entryMonth = Carbon::parse($entry->date)->startOfMonth();
        $currentMonth = Carbon::now()->startOfMonth();
        
        if ($entryMonth->lt($currentMonth)) {
            return redirect()->route('mobile.income-entries.index')
                ->with('error', __('common.cannot_delete_past_month_entry'));
        }
        
        $entry->delete();
        
        return redirect()->route('mobile.income-entries.index')
            ->with('success', __('common.deleted_successfully'));
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
