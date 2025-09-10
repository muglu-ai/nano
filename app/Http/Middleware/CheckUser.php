<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        //check user if it login or not if not then redirect to login page
        if (!auth()->check()) {
            return redirect('/login');
        }


        //check if user is authenticated and check if user role is exhibitor
        if (auth()->check() && auth()->user()->role == 'exhibitor') {
            return $next($request);
        } else {
            return redirect('/');
        }
    }
}
