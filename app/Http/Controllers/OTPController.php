<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OTP;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendOtpMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Mail\SendOTP;
use App\Models\Attendee;

class OTPController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Check if the email is already registered in attendees, or stall_manning tables
        $attendee = Attendee::where('email', $request->email)->first();
        $userExists = \DB::table('complimentary_delegates')->where('email', $request->email)->exists();
        $stallManningExists = \DB::table('stall_manning')->where('email', $request->email)->exists();

        if ($attendee || $userExists || $stallManningExists) {
            return response()->json(['status' => 'failed', 'message' => 'Email is already registered.'], 422);
        }


        // Prevent sending OTP if attendee status is not 'approved'
        if ($attendee) {
            return response()->json(['status' => 'failed', 'message' => 'Email is already registered.'], 422);
        }
        $otp = rand(100000, 999999);

        // Use the OTP model instead of direct DB queries
        $otpRecord = OTP::updateOrCreate(
            ['identifier' => $request->email],
            [
                'otp' => $otp,
                'verified' => false,
                'expires_at' => now()->addMinutes(10),
            ]
        );

        // Mail::to($request->email)->later(now()->addSeconds(2), new SendOtpMail($otp));
        // Send OTP by mail
        try {
            //Mail::to($request->email)->queue(new SendOtpMail($otp));
            Mail::to($request->email)->send(new SendOTP($otp)); // Using the new Mailable class
            Log::info('OTP email queued successfully to ' . $request->email);
        } catch (\Exception $e) {
            Log::error('Failed to queue OTP email: ' . $e->getMessage());
            return response()->json(['status' => 'failed', 'message' => 'Failed to send OTP email.'], 500);
        }

        // You cannot display the email directly here because this code is in the controller, not in the Mailable class.
        // If you want to include the email in the email view, pass it as data to the Mailable:
        // return view('emails.otp', ['otp' => $otp]);
        return response()->json(['status' => 'OTP sent successfully']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);



        // Master OTP check
        if ($request->otp == '211120') {
            // Optionally, mark all OTPs for this email as verified
            OTP::where('identifier', $request->email)->update(['verified' => true]);
            return response()->json(['status' => 'verified', 'master' => true]);
        }

        // Build the query
        $query = OTP::where('identifier', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now());

        $otpRecord = $query->first();

        if (!$otpRecord) {
            return response()->json(['status' => 'failed', 'message' => 'Invalid OTP.'], 422);
        }

        $otpRecord->verified = true;
        $otpRecord->save();

        return response()->json(['status' => 'verified']);
    }
}
