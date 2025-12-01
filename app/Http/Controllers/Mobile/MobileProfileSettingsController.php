<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MobileProfileSettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return view('mobile.profile-settings', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'seed_capital' => 'required|numeric|min:0',
            'median_monthly_income' => 'nullable|numeric|min:0',
            'income_last_verified_at' => 'nullable|date',
        ]);

        $user->update($validated);

        return redirect()->route('mobile.profile-settings')
            ->with('success', __('common.saved_successfully'));
    }
}
