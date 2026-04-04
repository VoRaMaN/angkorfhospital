<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $user = $request->user();
            $now = now();

            // Only update if last active was more than 1 minute ago to reduce DB writes
            if (! $user->last_active_at || $user->last_active_at->diffInMinutes($now) >= 1) {
                $user->forceFill(['last_active_at' => $now])->saveQuietly();
            }
        }

        return $next($request);
    }
}
