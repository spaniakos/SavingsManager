<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        // If the route expects JSON (API), no redirect - return null for JSON 401 response
        if ($request->expectsJson()) {
            return null;
        }

        // Redirect web requests to Filament login page
        return '/admin/login';
    }
}
