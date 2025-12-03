<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class MobileAuthController extends Controller
{
    /**
     * Show the mobile login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('mobile.dashboard');
        }
        
        return view('mobile.auth.login');
    }

    /**
     * Handle mobile login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');
        
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // The ExtendRememberMeCookie middleware will handle extending the cookie
            // to 1 year on the next request, so we just redirect normally
            return redirect()->intended(route('mobile.dashboard'));
        }

        return back()->withErrors([
            'email' => __('common.invalid_credentials'),
        ])->onlyInput('email');
    }

    /**
     * Show the mobile registration form.
     */
    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('mobile.dashboard');
        }
        
        return view('mobile.auth.register');
    }

    /**
     * Handle mobile registration.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('mobile.dashboard');
    }

    /**
     * Handle mobile logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('mobile.auth.login');
    }
}

