<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SharedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * This middleware allows access only to users with the 'exhibitor' or 'co-exhibitor' role.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !in_array($user->role, ['exhibitor', 'co-exhibitor'])) {
            // Optionally, redirect or abort with 403
            return response('Unauthorized.', 403);
        }

        return $next($request);
    }
}
