<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CoExhibitorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'co-exhibitor') {
            // Redirect to login or unauthorized page
            return redirect()->route('login')->withErrors('Unauthorized access.');
        }

        return $next($request);
    }
}
