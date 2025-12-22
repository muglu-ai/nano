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
    <table width="100%" bgcolor="#e9ecef" cellpadding="0" cellspacing="0" border="0" style="margin:0; padding:0;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; margin:40px auto; background:#fff; border-radius:12px; box-shadow:0 4px 24px rgba(0,0,0,0.07); border:1px solid #e0e0e0;">
                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding:24px 0 16px 0;">
                            <table cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="padding:0 10px;">
                                        <img src="{{ config('constants.HOSTED_URL') }}/asset/img/logos/meity-logo.png?height=80&width=120"
                                            alt="MeitY Logo"
                                            style="background:#fff; border-radius:8px;  height:70px; width:120px; object-fit:contain; display:block;">
                                    </td>
                                    <td style="padding:0 10px;">
                                        <img src="{{ config('constants.HOSTED_URL') }}/asset/img/logos/ism_logo.png?height=80&width=120"
                                            alt="ISM Logo"
                                            style="background:#fff; border-radius:8px; height:70px; width:120px; object-fit:contain; display:block;">
                                    </td>
                                     <td style="padding: 0 10px;">
                                            <img src="{{ config('constants.HOSTED_URL') }}/asset/img/logos/DIC_Logo.webp?height=80&width=120"
                                                alt="Digital India Logo" style="max-height: 100px; max-width: 120px;">
                                        </td>
                                    <td style="padding:0 10px;">
                                        <img src="{{ config('constants.HOSTED_URL') }}/asset/img/logos/SEMI_IESA_logo.png?height=80&width=120"
                                            alt="SEMI IESA Logo"
                                            style="background:#fff; border-radius:8px;  height:70px; width:120px; object-fit:contain; display:block;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Content -->
                    <tr>
                        <td style="padding:32px 32px 24px 32px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td>
                                        <h2 style="color:#004aad; margin-bottom:18px; font-size:28px; font-weight:700; letter-spacing:1px; font-family:Arial,sans-serif;">OTP Verification</h2>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:16px; color:#333; margin-bottom:20px; font-family:Arial,sans-serif;">
                                        Thank you for initiating your registration for <strong>{{ $eventName }}</strong>.<br>
                                        To proceed, please use the One-Time Password (OTP) provided below for verification:
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center" style="padding:28px 0 24px 0;">
                                        <span style="font-size:32px; font-weight:bold; color:#004aad; letter-spacing:10px; background:#f5faff; padding:18px 36px; display:inline-block; border:2px dashed #004aad; border-radius:8px; margin-bottom:0; font-family:Arial,sans-serif;">
                                            {{ $otp }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px; color:#666; margin:24px 0 10px 0; font-family:Arial,sans-serif;">
                                        This OTP is valid for the next 10 minutes. Please do not share it with anyone.
                                    </td>
                                </tr>
                                <tr>
                                    <td style="font-size:14px; color:#666; margin-bottom:0; font-family:Arial,sans-serif;">
                                        If you did not request this OTP, you can safely ignore this email.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- Footer -->
                    <tr>
                        <td style="background:#f9f9f9; padding:18px 32px; border-top:1px solid #e0e0e0; text-align:left; font-size:14px; color:#333; font-family:Arial,sans-serif;">
                            Warm &amp; Regards,<br>
                            <strong>{{ $eventName }} Team</strong><br>
                            <a href="mailto:{{ $supportEmail }}" style="color:#004aad; text-decoration:none;">{{ $supportEmail }}</a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>