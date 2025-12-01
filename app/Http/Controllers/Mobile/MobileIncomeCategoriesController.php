<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileIncomeCategoriesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $categories = IncomeCategory::where('user_id', $user->id)
            ->orWhere('is_system', true)
            ->orderBy('name')
            ->get();

        return view('mobile.income-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('mobile.income-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emoji' => 'nullable|string|max:10',
        ]);

        IncomeCategory::create([
            'user_id' => Auth::id(),
            'is_system' => false,
            'name' => $validated['name'],
            'emoji' => $validated['emoji'] ?? null,
        ]);

        return redirect()->route('mobile.income-categories.index')
            ->with('success', __('common.created_successfully'));
    }

    public function edit($id)
    {
        $category = IncomeCategory::where('user_id', Auth::id())
            ->where('is_system', false)
            ->findOrFail($id);

        return view('mobile.income-categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = IncomeCategory::where('user_id', Auth::id())
            ->where('is_system', false)
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'emoji' => 'nullable|string|max:10',
        ]);

        $category->update($validated);

        return redirect()->route('mobile.income-categories.index')
            ->with('success', __('common.updated_successfully'));
    }

    public function destroy($id)
    {
        $category = IncomeCategory::where('user_id', Auth::id())
            ->where('is_system', false)
            ->findOrFail($id);

        $category->delete();

        return redirect()->route('mobile.income-categories.index')
            ->with('success', __('common.deleted_successfully'));
    }
}
