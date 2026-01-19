<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DelegateAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('delegate')->check()) {
            return redirect()->route('delegate.login')
                ->with('error', 'Please login to access the delegate panel.');
        }

        $account = Auth::guard('delegate')->user();
        
        // Check if account is active
        if (!$account->isActive()) {
            Auth::guard('delegate')->logout();
            return redirect()->route('delegate.login')
                ->with('error', 'Your account has been suspended. Please contact support.');
        }

        return $next($request);
    }
}
