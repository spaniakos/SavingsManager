<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ExtendRememberMeCookie
{
    /**
     * Handle an incoming request.
     * Extend remember me cookie expiration to 1 year for authenticated users.
     * This middleware runs on every request and refreshes the remember me cookie.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // If user is authenticated, check for remember me cookie and extend it
        if (Auth::check() && Auth::viaRemember()) {
            // User is authenticated via remember me cookie
            // Check all cookies for remember me pattern
            // Laravel's remember me cookie contains '_remember_' in the name
            foreach ($request->cookies->all() as $name => $value) {
                if (str_contains($name, '_remember_')) {
                    // Extend this cookie to 1 year (525600 minutes = 1 year)
                    // This refreshes the cookie on every request, keeping it valid for 1 year
                    $response->cookie(
                        $name,
                        $value,
                        525600, // 1 year in minutes
                        config('session.path', '/'),
                        config('session.domain'),
                        config('session.secure', false),
                        true, // httpOnly
                        false,
                        config('session.same_site', 'lax')
                    );
                }
            }
        }

        return $response;
    }
}
