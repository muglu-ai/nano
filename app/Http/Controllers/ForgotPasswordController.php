<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forget-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email'], ['email.exists' => 'If you are registered, Please check your inbox to reset the password.']);

        $user = User::where('email', $request->email)->first();

       // dd($user);

        // Generate token and expiration time
        $token = Str::random(60);
        $user->password_reset_token = Hash::make($token);
        $user->password_reset_expires_at = Carbon::now()->addMinutes(30);
        $user->save();

        // Send reset email
        Mail::send('emails.password-reset', ['token' => $token, 'email' => $user->email], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Password Reset Request');
        });

        return back()->with('message', 'We have emailed your password reset link! Please check your inbox and spam box.');
    }

    public function showResetPasswordForm($token, $email)
    {

        //check if the token and email are valid
        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($token, $user->password_reset_token) || Carbon::now()->gt($user->password_reset_expires_at)) {
            return redirect('/forgot-password')->withErrors(['token' => 'Invalid or expired token. Please request a new password reset link.']);
        }

        //get the email from the token and pass it to the view
        return view('auth.reset-password', compact('token', 'email'));
    }

    public function resetPassword(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->token, $user->password_reset_token)) {
            return back()->withErrors(['token' => 'Invalid or expired token.']);
        }

        if (Carbon::now()->gt($user->password_reset_expires_at)) {
            return back()->withErrors(['token' => 'Token expired.']);
        }

        // Update password
        $user->simplePass = $request->password;
        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->password_reset_expires_at = null;
        $user->save();

        return redirect('/login')->with('message', 'Password reset successful. You can now log in.');
    }
}
