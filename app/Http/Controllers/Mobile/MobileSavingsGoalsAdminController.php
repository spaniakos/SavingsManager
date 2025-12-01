<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\SavingsGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileSavingsGoalsAdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $goals = SavingsGoal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mobile.savings-goals-admin.index', compact('goals'));
    }

    public function create()
    {
        return view('mobile.savings-goals-admin.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric',
            'initial_checkpoint' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'target_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        // Ensure current_amount defaults to 0 if not provided or empty
        $currentAmount = isset($validated['current_amount']) && $validated['current_amount'] !== null && $validated['current_amount'] !== ''
            ? (float) $validated['current_amount']
            : 0;

        // Ensure initial_checkpoint defaults to 0 if not provided
        $initialCheckpoint = isset($validated['initial_checkpoint']) && $validated['initial_checkpoint'] !== null && $validated['initial_checkpoint'] !== ''
            ? (float) $validated['initial_checkpoint']
            : 0;

        $goal = SavingsGoal::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'current_amount' => $currentAmount,
            'initial_checkpoint' => $initialCheckpoint,
            'start_date' => $validated['start_date'],
            'target_date' => $validated['target_date'],
        ]);

        return redirect()->route('mobile.savings-goals-admin.index')
            ->with('success', __('common.created_successfully'));
    }

    public function edit($id)
    {
        $goal = SavingsGoal::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('mobile.savings-goals-admin.edit', compact('goal'));
    }

    public function update(Request $request, $id)
    {
        $goal = SavingsGoal::where('user_id', Auth::id())
            ->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric',
            'initial_checkpoint' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'target_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string',
        ]);

        // Ensure current_amount defaults to 0 if not provided or empty
        $currentAmount = isset($validated['current_amount']) && $validated['current_amount'] !== null && $validated['current_amount'] !== ''
            ? (float) $validated['current_amount']
            : 0;

        // Ensure initial_checkpoint defaults to 0 if not provided
        $initialCheckpoint = isset($validated['initial_checkpoint']) && $validated['initial_checkpoint'] !== null && $validated['initial_checkpoint'] !== ''
            ? (float) $validated['initial_checkpoint']
            : 0;

        $goal->update([
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'current_amount' => $currentAmount,
            'initial_checkpoint' => $initialCheckpoint,
            'start_date' => $validated['start_date'],
            'target_date' => $validated['target_date'],
        ]);

        return redirect()->route('mobile.savings-goals-admin.index')
            ->with('success', __('common.updated_successfully'));
    }

    public function destroy($id)
    {
        $goal = SavingsGoal::where('user_id', Auth::id())
            ->findOrFail($id);

        $goal->delete();

        return redirect()->route('mobile.savings-goals-admin.index')
            ->with('success', __('common.deleted_successfully'));
    }
}
