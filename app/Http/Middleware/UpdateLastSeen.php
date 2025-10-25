<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only update for authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            
            // Only update if last_seen is older than 5 minutes to avoid too many DB updates
            if (!$user->last_seen || $user->last_seen->diffInMinutes(now()) >= 5) {
                $user->update(['last_seen' => now()]);
            }
        }

        return $next($request);
    }
}
