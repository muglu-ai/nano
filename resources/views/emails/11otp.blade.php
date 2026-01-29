@component('mail::message')
@php
    $eventName = "SEMICON INDIA 2025";
    $supportEmail = "visit";
@endphp

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px; margin:0 auto; font-family:Arial, sans-serif; border:1px solid #ddd; background:#f9f9f9;">
    <tr>
        <td align="center" style="padding:20px 0 0 0;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
                <tr>
                    <td align="center" style="padding:0 10px;">
                        <img src="{{ asset('asset/img/logos/SEMI_IESA_logo.png') }}?height=80&width=120" alt="SEMI IESA Logo" style="height:80px; width:120px; display:inline-block;">
                    </td>
                    <td align="center" style="padding:0 10px;">
                        <img src="{{ asset('asset/img/logos/meity-logo.png') }}?height=80&width=120" alt="MeitY Logo" style="height:80px; width:120px; display:inline-block;">
                    </td>
                    <td align="center" style="padding:0 10px;">
                        <img src="{{ asset('asset/img/logos/ism_logo.png') }}?height=80&width=120" alt="ISM Logo" style="height:80px; width:120px; display:inline-block;">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding:0 20px 20px 20px;">
            <h2 style="text-align:center; color:#004aad; margin:0 0 20px 0;">OTP Verification</h2>
            <p style="font-size:16px; color:#333; margin:0 0 10px 0;">Dear Attendee,</p>
            <p style="font-size:16px; color:#333; margin:0 0 20px 0;">
                You are attempting to register for <strong>{{ $eventName }}</strong>. To continue, please use the following One-Time Password (OTP) for verification:
            </p>
            <div style="text-align:center;">
                <p style="font-size:24px; font-weight:bold; color:#004aad; letter-spacing:5px; background:#fff; padding:10px 20px; display:inline-block; border:1px dashed #004aad; margin:0 0 20px 0;">
                    {{ $otp }}
                </p>
            </div>
            <p style="font-size:14px; color:#666; margin:20px 0 10px 0;">
                This OTP is valid for the next 10 minutes. Please do not share it with anyone.
            </p>
            <p style="font-size:14px; color:#666; margin:0 0 20px 0;">
                If you did not request this OTP, you can safely ignore this email.
            </p>
            <p style="margin-top:30px; font-size:14px; color:#333;">Thanks & Regards,<br><strong>Team {{ $eventName }}</strong><br><a href="mailto:{{ $supportEmail }}" style="color:#004aad;">{{ $supportEmail }}</a></p>
        </td>
    </tr>
</table>
@endcomponent
