@php
$eventName = "SEMICON INDIA 2025";
$supportEmail = "visit";
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification - {{ $eventName }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="background: #e9ecef; margin: 0; padding: 0; font-family: Arial, sans-serif;">
    <div style="max-width: 600px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07); border: 1px solid #e0e0e0; overflow: hidden;">
        <div style="padding: 24px 0 16px 0; text-align: center;">
            <img src="{{ asset('asset/img/logos/meity-logo.png') }}?height=80&width=120" alt="MeitY Logo" style="margin: 0 12px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); height: 70px; width: 150px; object-fit: contain;">
            <img src="{{ asset('asset/img/logos/ism_logo.png') }}?height=80&width=120" alt="ISM Logo" style="margin: 0 12px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); height: 70px; width: 150px; object-fit: contain;">
            <img src="{{ asset('asset/img/logos/SEMI_IESA_logo.png') }}?height=80&width=120" alt="SEMI IESA Logo" style="margin: 0 12px; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04); height: 70px; width: 150px; object-fit: contain;">
        </div>
        <div style="padding: 32px 32px 24px 32px;">
            <h2 style="color: #004aad; margin-bottom: 18px; font-size: 28px; font-weight: 700; letter-spacing: 1px;">OTP Verification</h2>
            <p style="font-size: 16px; color: #333; margin-bottom: 10px;">Dear Attendee,</p>
            <p style="font-size: 16px; color: #333; margin-bottom: 20px;">
                Thank you for initiating your registration for <strong>{{ $eventName }}</strong>. To proceed, please use the One-Time Password (OTP) provided below for verification:
            </p>
            <div style="text-align: center; margin: 28px 0 24px 0;">
                <span style="font-size: 32px; font-weight: bold; color: #004aad; letter-spacing: 10px; background: #f5faff; padding: 18px 36px; display: inline-block; border: 2px dashed #004aad; border-radius: 8px; margin-bottom: 0;">{{ $otp }}</span>
            </div>
            <p style="font-size: 14px; color: #666; margin: 24px 0 10px 0;">
                This OTP is valid for the next 10 minutes. Please do not share it with anyone.
            </p>
            <p style="font-size: 14px; color: #666; margin-bottom: 0;">
                If you did not request this OTP, please disregard this email.
            </p>
        </div>
        <div style="background: #f9f9f9; padding: 18px 32px; border-top: 1px solid #e0e0e0; text-align: left; font-size: 14px; color: #333;">
            Warm &amp; Regards,<br>
            <strong>{{ $eventName }} Team</strong><br>
            <a href="mailto:{{ $supportEmail }}" style="color: #004aad; text-decoration: none;">{{ $supportEmail }}</a>
        </div>
    </div>
</body>
</html>
