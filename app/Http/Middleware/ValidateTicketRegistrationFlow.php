<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateTicketRegistrationFlow
{
    /**
     * Handle an incoming request.
     * 
     * Validates that users can only access allowed ticket registration URLs.
     * If accessing an invalid URL, redirects to the registration page.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get current route name
        $currentRoute = $request->route()->getName();
        
        // Allowed routes for ticket registration flow
        $allowedRoutes = [
            'tickets.discover',
            'tickets.register',
            'tickets.store',
            'tickets.preview',
            'tickets.payment.initiate',
            'tickets.payment.by-tin',
            'tickets.payment',
            'tickets.payment.process',
            'tickets.payment.callback',
            'tickets.confirmation',
            'tickets.payment.lookup',
            'tickets.payment.lookup.submit',
            'tickets.validate-gst',
            'tickets.continue',
            'tickets.manage',
            'tickets.request-link',
            'tickets.verify-otp',
        ];
        
        // Check if current route is in allowed list
        if (!in_array($currentRoute, $allowedRoutes)) {
            // Extract eventSlug from route parameters if available
            $eventSlug = $request->route('eventSlug');
            
            if ($eventSlug) {
                return redirect()->route('tickets.register', $eventSlug)
                    ->with('error', 'Invalid access. Please complete the registration form first.');
            }
            
            // Fallback if no eventSlug available
            return redirect('/')
                ->with('error', 'Invalid access.');
        }
        
        return $next($request);
    }
}


