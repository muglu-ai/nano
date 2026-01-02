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
        //verify if user is logged in and user type is admin or super-admin
        if (auth()->check() && !in_array(auth()->user()->role, ['admin', 'super-admin'])) {
            return redirect('/');
        }
        return $next($request);
    }
}
