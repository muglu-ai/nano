@php
$eventName = config('constants.EVENT_NAME', 'Event');
$eventYear = config('constants.EVENT_YEAR', date('Y'));
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password Reset - {{ $eventName }} {{ $eventYear }}</title>
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
        .content {
            padding: 32px 32px 24px 32px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #004aad;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
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
        <div class="content">
            <h2 style="color: #004aad; margin-bottom: 18px;">Password Reset Request</h2>
            <p style="font-size: 16px; color: #333; margin-bottom: 20px;">
                Hello {{ $contact->name }},
            </p>
            <p style="font-size: 16px; color: #333; margin-bottom: 20px;">
                You requested to reset your password for the delegate panel. Click the button below to reset your password:
            </p>
            <div style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </div>
            <p style="font-size: 14px; color: #666; margin-top: 24px;">
                If you did not request a password reset, please ignore this email.
            </p>
            <p style="font-size: 14px; color: #666;">
                This link will expire in 60 minutes.
            </p>
        </div>
        <div class="footer">
            Warm Regards,<br>
            <strong>{{ $eventName }} {{ $eventYear }} Team</strong>
        </div>
    </div>
</body>
</html>
