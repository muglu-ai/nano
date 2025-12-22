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

        .header img {
            margin: 0 12px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            height: 70px;
            width: 150px;
            object-fit: contain;
        }

        .content {
            padding: 32px 32px 24px 32px;
        }

        .content h2 {
            color: #004aad;
            margin-bottom: 18px;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .otp-box {
            text-align: center;
            margin: 28px 0 24px 0;
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
            margin-bottom: 0;
        }

        .footer {
            background: #f9f9f9;
            padding: 18px 32px;
            border-top: 1px solid #e0e0e0;
            text-align: left;
            font-size: 14px;
            color: #333;
        }

        .footer a {
            color: #004aad;
            text-decoration: none;
        }

        @media (max-width: 600px) {

            .email-container,
            .content,
            .footer {
                padding-left: 12px !important;
                padding-right: 12px !important;
            }

            .header img {
                height: 50px;
                width: 70px;
                margin: 0 4px;
            }

            .otp-code {
                font-size: 24px;
                padding: 10px 16px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="header">
            <img src="{{ asset('asset/img/logos/meity-logo.png') }}?height=80&width=120" alt="MeitY Logo">
            <img src="{{ asset('asset/img/logos/ism_logo.png') }}?height=80&width=120" alt="ISM Logo">
            <img src="{{ asset('asset/img/logos/SEMI_IESA_logo.png') }}?height=80&width=120" alt="SEMI IESA Logo">


        </div>
        <div class="content">
            <h2>OTP Verification</h2>
            <p style="font-size:16px; color:#333; margin-bottom:10px;">Dear Attendee,</p>
            <p style="font-size:16px; color:#333; margin-bottom:20px;">
                Thank you for initiating your registration for <strong>{{ $eventName }}</strong>. To proceed, please use the One-Time Password (OTP) provided below for verification:
            </p>
            <div class="otp-box">
                <span class="otp-code">{{ $otp }}</span>
            </div>
            <p style="font-size:14px; color:#666; margin:24px 0 10px 0;">
                This OTP is valid for the next 10 minutes. Please do not share it with anyone.
            </p>
            <p style="font-size:14px; color:#666; margin-bottom:0;">
                If you did not request this OTP, please disregard this email.
            </p>
        </div>
        <div class="footer">
            Warm &amp; Regards,<br>
            <strong> {{ $eventName }} Team</strong><br>
            <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
        </div>
    </div>
</body>

</html>