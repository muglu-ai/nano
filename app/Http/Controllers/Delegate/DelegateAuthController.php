<?php

namespace App\Http\Controllers\Delegate;

use App\Http\Controllers\Controller;
use App\Models\Ticket\TicketAccount;
use App\Models\Ticket\TicketContact;
use App\Models\Ticket\TicketOtpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DelegateAuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        // If delegate is already logged in, redirect to dashboard
        if (Auth::guard('delegate')->check()) {
            return redirect()->route('delegate.dashboard');
        }

        return view('delegate.auth.login');
    }

    /**
     * Handle email/password login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Find contact by email
        $contact = TicketContact::where('email', $credentials['email'])->first();

        if (!$contact) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }

        // Get or create account for this contact
        $account = $contact->account;
        if (!$account) {
            // Create account if it doesn't exist
            $account = TicketAccount::create([
                'contact_id' => $contact->id,
                'status' => 'active',
            ]);
        }

        // Check if account is active
        if (!$account->isActive()) {
            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact support.',
            ])->withInput($request->only('email'));
        }

        // Check if email is verified (optional check)
        if (!$contact->isEmailVerified()) {
            // Allow login but could require verification later
        }

        // Check password
        if (!$account->password || !Hash::check($credentials['password'], $account->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }

        // Login the delegate using manual login (since we can't use attempt with email on contact)
        Auth::guard('delegate')->login($account, $request->boolean('remember'));
        $request->session()->regenerate();

        // Update last login
        $account->update(['last_login_at' => now()]);

        return redirect()->intended(route('delegate.dashboard'));
    }

    /**
     * Send OTP to email
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $contact = TicketContact::where('email', $request->email)->first();

        if (!$contact) {
            return back()->withErrors([
                'email' => 'No account found with this email address.',
            ])->withInput($request->only('email'));
        }

        // Rate limiting: Check for recent OTP requests (max 3 per 15 minutes)
        $recentRequests = TicketOtpRequest::where('contact_id', $contact->id)
            ->where('created_at', '>=', now()->subMinutes(15))
            ->where('status', 'pending')
            ->count();

        if ($recentRequests >= 3) {
            return back()->withErrors([
                'email' => 'Too many OTP requests. Please try again after 15 minutes.',
            ])->withInput($request->only('email'));
        }

        // Generate OTP
        $otp = str_pad((string) rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Create OTP request
        $otpRequest = TicketOtpRequest::create([
            'contact_id' => $contact->id,
            'channel' => 'email',
            'otp_hash' => Hash::make($otp),
            'expires_at' => now()->addMinutes(10),
            'status' => 'pending',
            'ip_address' => $request->ip(),
        ]);

        // Send OTP email
        try {
            $eventName = config('constants.EVENT_NAME', 'Event');
            $eventYear = config('constants.EVENT_YEAR', date('Y'));
            $supportEmail = config('constants.SUPPORT_EMAIL', 'support@example.com');

            Mail::send('emails.delegate-otp', [
                'otp' => $otp,
                'eventName' => $eventName,
                'eventYear' => $eventYear,
                'supportEmail' => $supportEmail,
            ], function ($message) use ($contact, $eventName, $eventYear) {
                $message->to($contact->email, $contact->name)
                    ->subject("OTP for Delegate Login - {$eventName} {$eventYear}");
            });

            Log::info('Delegate OTP sent', ['contact_id' => $contact->id, 'email' => $contact->email]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'OTP has been sent to your email address.']);
            }

            return back()->with('success', 'OTP has been sent to your email address.');
        } catch (\Exception $e) {
            Log::error('Failed to send delegate OTP', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => ['email' => ['Failed to send OTP. Please try again.']]], 422);
            }

            return back()->withErrors([
                'email' => 'Failed to send OTP. Please try again.',
            ])->withInput($request->only('email'));
        }
    }

    /**
     * Verify OTP and login
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $contact = TicketContact::where('email', $request->email)->first();

        if (!$contact) {
            return back()->withErrors([
                'otp' => 'Invalid email or OTP.',
            ])->withInput($request->only('email'));
        }

        // Find pending OTP request
        $otpRequest = TicketOtpRequest::where('contact_id', $contact->id)
            ->where('status', 'pending')
            ->where('expires_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$otpRequest) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => ['otp' => ['OTP not found or expired. Please request a new OTP.']]], 422);
            }
            return back()->withErrors([
                'otp' => 'OTP not found or expired. Please request a new OTP.',
            ])->withInput($request->only('email'));
        }

        // Check attempts (max 5)
        if ($otpRequest->attempts >= 5) {
            $otpRequest->update(['status' => 'expired']);
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => ['otp' => ['Too many failed attempts. Please request a new OTP.']]], 422);
            }
            return back()->withErrors([
                'otp' => 'Too many failed attempts. Please request a new OTP.',
            ])->withInput($request->only('email'));
        }

        // Verify OTP
        if (!Hash::check($request->otp, $otpRequest->otp_hash)) {
            $otpRequest->increment('attempts');
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => false, 'errors' => ['otp' => ['Invalid OTP. Please try again.']]], 422);
            }
            return back()->withErrors([
                'otp' => 'Invalid OTP. Please try again.',
            ])->withInput($request->only('email'));
        }

        // Mark OTP as verified
        $otpRequest->update(['status' => 'verified']);

        // Get or create account
        $account = $contact->account;
        if (!$account) {
            $account = TicketAccount::create([
                'contact_id' => $contact->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]);
        } else {
            // Update email verification if not already verified
            if (!$account->email_verified_at) {
                $account->update(['email_verified_at' => now()]);
            }
        }

        // Check if account is active
        if (!$account->isActive()) {
            return back()->withErrors([
                'email' => 'Your account has been suspended. Please contact support.',
            ])->withInput($request->only('email'));
        }

        // Login the delegate
        Auth::guard('delegate')->login($account);
        $request->session()->regenerate();

        // Update last login
        $account->update(['last_login_at' => now()]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'redirect' => route('delegate.dashboard')]);
        }

        return redirect()->intended(route('delegate.dashboard'));
    }

    /**
     * Logout delegate
     */
    public function logout(Request $request)
    {
        Auth::guard('delegate')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('delegate.login');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('delegate.auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendPasswordResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $contact = TicketContact::where('email', $request->email)->first();

        if (!$contact) {
            // Don't reveal if email exists
            return back()->with('success', 'If an account exists with this email, a password reset link has been sent.');
        }

        $account = $contact->account;
        if (!$account || !$account->password) {
            return back()->with('success', 'If an account exists with this email, a password reset link has been sent.');
        }

        // Generate reset token
        $token = Str::random(64);
        
        // Store token in database (you may want to create a password_reset_tokens table for delegates)
        // For now, we'll use a simple approach with remember_token
        $account->update([
            'remember_token' => Hash::make($token),
        ]);

        // Send reset email
        try {
            $resetUrl = route('delegate.password.reset', ['token' => $token, 'email' => $contact->email]);
            $eventName = config('constants.EVENT_NAME', 'Event');
            $eventYear = config('constants.EVENT_YEAR', date('Y'));

            Mail::send('emails.delegate-password-reset', [
                'resetUrl' => $resetUrl,
                'contact' => $contact,
                'eventName' => $eventName,
                'eventYear' => $eventYear,
            ], function ($message) use ($contact, $eventName, $eventYear) {
                $message->to($contact->email, $contact->name)
                    ->subject("Password Reset Request - {$eventName} {$eventYear}");
            });

            return back()->with('success', 'Password reset link has been sent to your email address.');
        } catch (\Exception $e) {
            Log::error('Failed to send password reset email', [
                'contact_id' => $contact->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'email' => 'Failed to send password reset link. Please try again.',
            ]);
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('delegate.password.forgot')
                ->withErrors(['email' => 'Invalid reset link.']);
        }

        $contact = TicketContact::where('email', $email)->first();
        if (!$contact || !$contact->account) {
            return redirect()->route('delegate.password.forgot')
                ->withErrors(['email' => 'Invalid reset link.']);
        }

        // Verify token
        if (!Hash::check($token, $contact->account->remember_token)) {
            return redirect()->route('delegate.password.forgot')
                ->withErrors(['email' => 'Invalid or expired reset link.']);
        }

        return view('delegate.auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $contact = TicketContact::where('email', $request->email)->first();

        if (!$contact || !$contact->account) {
            return back()->withErrors([
                'email' => 'Invalid reset link.',
            ]);
        }

        // Verify token
        if (!Hash::check($request->token, $contact->account->remember_token)) {
            return back()->withErrors([
                'token' => 'Invalid or expired reset link.',
            ]);
        }

        // Update password
        $contact->account->update([
            'password' => $request->password,
            'remember_token' => null, // Clear reset token
        ]);

        return redirect()->route('delegate.login')
            ->with('success', 'Password has been reset successfully. Please login with your new password.');
    }
}
