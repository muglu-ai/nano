<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // First check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        // Verify user type is admin or super-admin
        $user = auth()->user();
        if (!in_array($user->role, ['admin', 'super-admin'])) {
            abort(403, 'Unauthorized access. Admin or Super Admin role required.');
        }

        return $next($request);
    }
}
