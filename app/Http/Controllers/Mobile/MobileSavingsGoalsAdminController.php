<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\SavingsGoal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileSavingsGoalsAdminController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $goals = SavingsGoal::where('user_id', $user->id)
            ->orWhereHas('members', function ($query) use ($user) {
                $query->where('users.id', $user->id)
                    ->where('savings_goal_members.status', 'accepted');
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('mobile.savings-goals-admin.index', compact('goals'));
    }
    
    public function create()
    {
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->get();
        return view('mobile.savings-goals-admin.create', compact('users'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'initial_checkpoint' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'target_date' => 'required|date|after:start_date',
            'is_joint' => 'boolean',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
            'notes' => 'nullable|string',
        ]);
        
        $goal = SavingsGoal::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'target_amount' => $validated['target_amount'],
            'current_amount' => $validated['current_amount'] ?? 0,
            'initial_checkpoint' => $validated['initial_checkpoint'] ?? 0,
            'start_date' => $validated['start_date'],
            'target_date' => $validated['target_date'],
            'is_joint' => $validated['is_joint'] ?? false,
        ]);
        
        if ($goal->is_joint && isset($validated['members'])) {
            $goal->members()->sync($validated['members']);
        }
        
        return redirect()->route('mobile.savings-goals-admin.index')
            ->with('success', __('common.created_successfully'));
    }
    
    public function edit($id)
    {
        $goal = SavingsGoal::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $users = User::where('id', '!=', Auth::id())->orderBy('name')->get();
        
        return view('mobile.savings-goals-admin.edit', compact('goal', 'users'));
    }
    
    public function update(Request $request, $id)
    {
        $goal = SavingsGoal::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:0.01',
            'current_amount' => 'nullable|numeric|min:0',
            'initial_checkpoint' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'target_date' => 'required|date|after:start_date',
            'is_joint' => 'boolean',
            'members' => 'nullable|array',
            'members.*' => 'exists:users,id',
            'notes' => 'nullable|string',
        ]);
        
        $goal->update($validated);
        
        if ($goal->is_joint && isset($validated['members'])) {
            $goal->members()->sync($validated['members']);
        } else {
            $goal->members()->detach();
        }
        
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

