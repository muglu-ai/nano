@php
$eventName = config('constants.EVENT_NAME', 'Event');
$eventYear = config('constants.EVENT_YEAR', date('Y'));
$supportEmail = config('constants.SUPPORT_EMAIL', 'support@example.com');
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTP Verification - {{ $eventName }} {{ $eventYear }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: #e9ecef;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }
        .header {
            padding: 24px 0 16px 0;
            text-align: center;
        }
        .content {
            padding: 32px 32px 24px 32px;
        }
        .otp-box {
            text-align: center;
            margin: 24px 0;
        }
        .otp-code {
            font-size: 32px;
            font-weight: bold;
            color: #004aad;
            letter-spacing: 10px;
            background: #f5faff;
            padding: 18px 36px;
            display: inline-block;
            border: 2px dashed #004aad;
            border-radius: 8px;
        }
        .footer {
            padding: 24px 32px;
            background: #f8f9fa;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2 style="color: #004aad; margin: 0;">{{ $eventName }} {{ $eventYear }}</h2>
        </div>
        <div class="content">
            <h2 style="color: #004aad; margin-bottom: 18px;">OTP Verification</h2>
            <p style="font-size: 16px; color: #333; margin-bottom: 20px;">
                Thank you for logging into the delegate panel. Please use the One-Time Password (OTP) provided below:
            </p>
            <div class="otp-box">
                <span class="otp-code">{{ $otp }}</span>
            </div>
            <p style="font-size: 14px; color: #666; margin: 24px 0 10px 0;">
                This OTP is valid for the next 10 minutes. Please do not share it with anyone.
            </p>
        </div>
        <div class="footer">
            Warm Regards,<br>
            <strong>{{ $eventName }} Team</strong><br>
            <a href="mailto:{{ $supportEmail }}" style="color: #004aad;">{{ $supportEmail }}</a>
        </div>
    </div>
</body>
</html>
