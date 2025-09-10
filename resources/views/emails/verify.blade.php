<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Email - SEMICON India 2025</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; margin: 0; padding: 0;">
<div style="max-width: 600px; margin: 40px auto; background: #ffffff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);">
    <div style="text-align: center; padding-bottom: 20px;">
        <img src="https://www.mmactiv.in/images/semicon_logo.png" alt="SEMICON India 2025" style="max-width: 180px;">
    </div>
    <div style="font-size: 16px; color: #333; line-height: 1.6; text-align: center;">
        <p>Dear {{ $user->name }},</p>
        <p>Thank you for registering for <strong>SEMICON India 2025</strong>. To complete your registration, please verify your email address by clicking the button below.</p>
        <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 12px 24px; background-color: #007bff; color: #ffffff; text-decoration: none; font-weight: bold; font-size: 16px; border-radius: 5px; transition: background 0.3s ease;">Verify Email</a>
        <p>Or copy and paste the following link into your browser:</p>
        <p style="margin-top: 10px; font-size: 14px; word-wrap: break-word; color: #007bff;">{{ $verificationUrl }}</p>
        <p>If you did not register for this event, please ignore this email.</p>
    </div>
    <div style="text-align: center; margin-top: 20px; font-size: 14px; color: #666;">
        <p>SEMICON India 2025.</p>
    </div>
</div>
</body>
</html>
