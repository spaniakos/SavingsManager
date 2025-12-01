<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DetectMobile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Always redirect authenticated users to mobile interface
        // Only redirect if user is authenticated and on admin routes (not login/register)
        if ($request->user() && $request->is('admin*') && ! $request->is('admin/mobile*') && ! $request->is('admin/login*') && ! $request->is('admin/register*')) {
            // Convert any admin route to mobile equivalent
            $path = $request->path();
            if ($path === 'admin') {
                return redirect('/admin/mobile');
            }

            // For other admin routes, redirect to mobile dashboard
            return redirect('/admin/mobile');
        }

        return $next($request);
    }

    /**
     * Check if user agent indicates a mobile device
     */
    private function isMobileDevice(string $userAgent): bool
    {
        $mobileAgents = [
            'Mobile', 'Android', 'iPhone', 'iPad', 'iPod',
            'BlackBerry', 'Windows Phone', 'Opera Mini',
        ];

        foreach ($mobileAgents as $agent) {
            if (stripos($userAgent, $agent) !== false) {
                return true;
            }
        }

        return false;
    }
}
