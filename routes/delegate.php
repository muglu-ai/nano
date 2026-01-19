<?php

use App\Http\Controllers\Delegate\DelegateAuthController;
use App\Http\Controllers\Delegate\DelegateDashboardController;
use App\Http\Controllers\Delegate\DelegateBadgeController;
use App\Http\Controllers\Delegate\DelegateNotificationController;
use App\Http\Controllers\Delegate\DelegateUpgradeController;
use App\Http\Controllers\Delegate\DelegateUpgradePaymentController;
use App\Http\Controllers\Delegate\DelegateReceiptController;
use App\Http\Controllers\Delegate\DelegateRegistrationController;
use App\Http\Middleware\DelegateAuthMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Delegate Panel Routes
|--------------------------------------------------------------------------
|
| Routes for delegate authentication and panel access
|
*/

// Delegate Auth Routes (Public)
Route::prefix('delegate')->name('delegate.')->group(function () {
    Route::get('/login', [DelegateAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [DelegateAuthController::class, 'login']);
    Route::post('/otp/send', [DelegateAuthController::class, 'sendOtp'])->name('otp.send');
    Route::post('/otp/verify', [DelegateAuthController::class, 'verifyOtp'])->name('otp.verify');
    
    // Password Reset
    Route::get('/password/forgot', [DelegateAuthController::class, 'showForgotPasswordForm'])->name('password.forgot');
    Route::post('/password/email', [DelegateAuthController::class, 'sendPasswordResetLink'])->name('password.email');
    Route::get('/password/reset/{token}', [DelegateAuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/password/reset', [DelegateAuthController::class, 'resetPassword'])->name('password.update');
    
    // Protected Routes
    Route::middleware([DelegateAuthMiddleware::class])->group(function () {
        Route::get('/dashboard', [DelegateDashboardController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [DelegateAuthController::class, 'logout'])->name('logout');
        
        // Badges (Coming Soon)
        Route::get('/badges/{delegateId}', [DelegateBadgeController::class, 'show'])->name('badges.show');
        
        // Notifications
        Route::get('/notifications', [DelegateNotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{id}/read', [DelegateNotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/notifications/read-all', [DelegateNotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('/notifications/unread-count', [DelegateNotificationController::class, 'unreadCount'])->name('notifications.unread-count');
        
        // Upgrades
        Route::get('/upgrades', [DelegateUpgradeController::class, 'index'])->name('upgrades.index');
        Route::get('/upgrades/individual/{ticketId}', [DelegateUpgradeController::class, 'showIndividualUpgradeForm'])->name('upgrades.individual.form');
        Route::get('/upgrades/group/{registrationId}', [DelegateUpgradeController::class, 'showGroupUpgradeForm'])->name('upgrades.group.form');
        Route::post('/upgrades/preview', [DelegateUpgradeController::class, 'previewUpgrade'])->name('upgrades.preview');
        Route::post('/upgrades/individual/process', [DelegateUpgradeController::class, 'processIndividualUpgrade'])->name('upgrades.individual.process');
        Route::post('/upgrades/group/process', [DelegateUpgradeController::class, 'processGroupUpgrade'])->name('upgrades.group.process');
        Route::post('/upgrades/{requestId}/confirm', [DelegateUpgradeController::class, 'confirmUpgrade'])->name('upgrades.confirm');
        Route::get('/upgrades/history', [DelegateUpgradeController::class, 'history'])->name('upgrades.history');
        Route::get('/upgrades/{requestId}/receipt', [DelegateUpgradeController::class, 'showReceipt'])->name('upgrades.receipt');
        
        // Upgrade Payments
        Route::post('/upgrades/{requestId}/payment', [DelegateUpgradePaymentController::class, 'initiatePayment'])->name('upgrades.payment.initiate');
        Route::get('/upgrades/{requestId}/payment/success', [DelegateUpgradePaymentController::class, 'paymentSuccess'])->name('upgrades.payment.success');
        Route::get('/upgrades/{requestId}/payment/failure', [DelegateUpgradePaymentController::class, 'paymentFailure'])->name('upgrades.payment.failure');
        Route::post('/upgrades/payment/webhook', [DelegateUpgradePaymentController::class, 'webhook'])
            ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
            ->name('upgrades.payment.webhook');
        
        // Receipts
        Route::get('/receipts', [DelegateReceiptController::class, 'index'])->name('receipts.index');
        Route::get('/receipts/{id}', [DelegateReceiptController::class, 'show'])->name('receipts.show');
        Route::get('/receipts/{id}/download', [DelegateReceiptController::class, 'download'])->name('receipts.download');
        
        // Registrations
        Route::get('/registrations', [DelegateRegistrationController::class, 'index'])->name('registrations.index');
        Route::get('/registrations/{id}', [DelegateRegistrationController::class, 'show'])->name('registrations.show');
    });
});
