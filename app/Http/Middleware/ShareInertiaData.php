<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class ShareInertiaData
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Inertia::share([
            'auth.user.roles' => fn () => $request->user()?->getRoleNames(),
            'auth.user.permissions' => fn () => $request->user()?->getAllPermissions()->pluck('name'),
        ]);

        return $next($request);
    }
}
