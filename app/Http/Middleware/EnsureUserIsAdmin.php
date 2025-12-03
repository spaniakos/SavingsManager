<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // This middleware only runs on authenticated routes (via authMiddleware)
        // So we can safely check if user is admin
        if (!auth()->user()->is_admin) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}

