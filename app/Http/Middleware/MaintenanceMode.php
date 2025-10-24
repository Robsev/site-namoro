<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled
        if (config('app.maintenance_mode', false)) {
            // Allow access for specific IPs (admin users)
            $allowedIPs = config('app.maintenance_allowed_ips', []);
            $clientIP = $request->ip();
            
            if (!in_array($clientIP, $allowedIPs)) {
                return response()->view('errors.503', [], 503);
            }
        }

        return $next($request);
    }
}
