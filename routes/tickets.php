<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Ticket\PublicTicketController;
use App\Http\Controllers\Ticket\GuestTicketController;
use App\Http\Controllers\Ticket\TicketPaymentController;

/*
|--------------------------------------------------------------------------
| Ticket Registration Routes (Public)
|--------------------------------------------------------------------------
|
| All public ticket registration routes are defined here.
| These routes are separate from admin routes for better organization.
|
*/

// Public ticket discovery and registration
// Support both slug and ID for flexibility
Route::get('/tickets/{eventSlug}', [PublicTicketController::class, 'discover'])->name('tickets.discover');
Route::get('/tickets/{eventSlug}/register', [PublicTicketController::class, 'register'])->name('tickets.register');
Route::post('/tickets/{eventSlug}/register', [PublicTicketController::class, 'store'])->name('tickets.store');
Route::get('/tickets/{eventSlug}/preview', [PublicTicketController::class, 'preview'])->name('tickets.preview');
Route::post('/tickets/validate-gst', [PublicTicketController::class, 'validateGst'])->name('tickets.validate-gst');
Route::get('/tickets/{eventSlug}/register/{token}', [PublicTicketController::class, 'continueRegistration'])->name('tickets.continue');

// Guest management (magic links)
Route::get('/manage-booking/{token}', [GuestTicketController::class, 'manage'])->name('tickets.manage');
Route::post('/manage-booking/request-link', [GuestTicketController::class, 'requestLink'])->name('tickets.request-link');
Route::post('/manage-booking/verify-otp', [GuestTicketController::class, 'verifyOtp'])->name('tickets.verify-otp');

// Payment
// POST route for initiating payment (must come before GET route to avoid conflict)
Route::post('/tickets/{eventSlug}/payment/initiate', [TicketPaymentController::class, 'initiate'])->name('tickets.payment.initiate');
// New payment flow with auto gateway selection (handles Pay Now click) - GET route for order numbers
Route::get('/tickets/{eventSlug}/payment/{orderNo}', [\App\Http\Controllers\RegistrationPaymentController::class, 'processTicketPayment'])
    ->where('orderNo', '[A-Z0-9-]+') // Only match order numbers (alphanumeric with dashes), not "initiate"
    ->name('tickets.payment.process');
Route::get('/tickets/{eventSlug}/payment/{tin}', [TicketPaymentController::class, 'initiateByTin'])->name('tickets.payment.by-tin');
Route::get('/ticket-payment/{token}', [TicketPaymentController::class, 'show'])->name('tickets.payment');
Route::get('/ticket-payment/{token}/callback', [TicketPaymentController::class, 'callback'])->name('tickets.payment.callback');
Route::post('/ticket-payment/{token}/process', [TicketPaymentController::class, 'process'])->name('tickets.payment.process.old'); // Old route - kept for backward compatibility
Route::get('/tickets/{eventSlug}/confirmation/{token}', [TicketPaymentController::class, 'confirmation'])->name('tickets.confirmation');
Route::post('/ticket-payment/webhook', [TicketPaymentController::class, 'webhook'])->name('tickets.payment.webhook')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

