<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\SavingsGoal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileSavingsGoalsController extends Controller
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
        
        return view('mobile.savings-goals', compact('goals'));
    }
}
