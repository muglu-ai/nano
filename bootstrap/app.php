<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Define route middleware aliases
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'admin.auth' => \App\Http\Middleware\Auth::class, // Admin and Super Admin authentication
            'validate.ticket.flow' => \App\Http\Middleware\ValidateTicketRegistrationFlow::class,
            'delegate.auth' => \App\Http\Middleware\DelegateAuthMiddleware::class,
        ]);
        //
        // Don't append Auth middleware globally - apply it only to specific routes
        // $middleware->append(Auth::class);
        
        // Exclude payment gateway callbacks from CSRF verification
        // Payment gateways (CCAvenue, PayPal) redirect back via POST without CSRF tokens
        $middleware->validateCsrfTokens(except: [
            // CCAvenue payment callbacks
            '/payment/ccavenue-success',           // POST - CCAvenue success callback
            '/ccavenue/webhook',                   // POST - CCAvenue webhook
            
            // PayPal payment callbacks
            '/paypal/success',                     // GET - PayPal success redirect
            '/paypal/cancel',                      // GET - PayPal cancel redirect
            '/paypal/webhook',                     // GET/POST - PayPal webhook
            '/paypal/capture-order/*',             // POST - PayPal order capture
            
            // Registration payment callbacks (RegistrationPaymentController)
            '/registration/payment/callback/*',    // GET/POST - Registration payment callbacks (ccavenue/paypal)
            
            // Ticket payment callbacks (RegistrationPaymentController)
            '/tickets/*/payment/callback/*',       // GET/POST - Ticket payment callbacks (ccavenue/paypal)
            
            // Legacy ticket payment callbacks
            '/ticket-payment/*/callback',          // GET - Old ticket payment callback
            '/ticket-payment/webhook',             // POST - Ticket payment webhook
            
            // Delegate upgrade payment webhooks
            '/delegate/upgrades/payment/webhook',  // POST - Upgrade payment webhook
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
