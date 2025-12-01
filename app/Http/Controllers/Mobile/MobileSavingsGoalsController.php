<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\SavingsGoal;
use Illuminate\Support\Facades\Auth;

class MobileSavingsGoalsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $goals = SavingsGoal::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mobile.savings-goals', compact('goals'));
    }
}
