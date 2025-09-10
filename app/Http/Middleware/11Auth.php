<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth as AuthClass;

class Auth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip middleware for login/register routes to prevent infinite loops
        if ($request->routeIs('login') ||
            $request->routeIs('register') ||
            $request->routeIs('password.*') ||
            $request->is('login') ||
            $request->is('register')) {
            return $next($request);
        }

        // Check if user is not logged in
        if (!AuthClass::check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
            return redirect()->route('login');
        }

        // Check if user exists and has admin role
        $user = AuthClass::user();
        if (!$user || $user->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Access denied. Admin privileges required.'], 403);
            }

            // Log out user if they're not admin to prevent confusion
            AuthClass::logout();
            return redirect()->route('login')->with('error', 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }
}
