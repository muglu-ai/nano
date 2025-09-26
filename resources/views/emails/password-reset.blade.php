<html>
<head>
    <meta charset="UTF-8">
    <title>Password Reset Request</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 5px; padding: 20px;">
    <tr>
        <td align="center" style="padding-bottom: 20px;">
            <!-- Logo Placeholder -->
                <img src="{{config('constants.event_logo')}}" alt="{{config('constants.EVENT_NAME')}}" style="max-width: 150px; height: auto;">
               <div class="logo-text" style="margin-top: 10px;">{{config('constants.EVENT_NAME')}} {{config('constants.EVENT_YEAR')}}</div>
        </td>
    </tr>
    <tr>
        <td style="color: #333333; font-size: 16px; line-height: 1.5;">
            <p>Hello,</p>
            <p>You requested to reset your password. Click the link below to reset it:</p>
            <p style="text-align: center;">
                <a href="{{ url('/reset-password/' . $token . '/' . ($email)) }}"
                   style="display: inline-block; background-color: #007bff; color: #ffffff; text-decoration: none; padding: 12px 20px; border-radius: 5px; font-weight: bold;">
                    Reset Password
                </a>
            </p>
            <p style="text-align: center; word-break: break-word;">
                If the button above does not work, copy and paste the following link into your browser:
                <br>
                <a href="{{ url('/reset-password/' . $token . '/' . ($email)) }}" style="color: #007bff;">
                    {{ url('/reset-password/' . $token . '/' . ($email)) }}
                </a>
            </p>
            <p>This link will expire in 30 minutes.</p>
            <p>If you didn't request a password reset, please ignore this email.</p>
            <p>Best regards,</p>
            <p><strong>{{config('constants.EVENT_NAME')}} {{config('constants.EVENT_NAME')}} </strong></p>
        </td>
    </tr>
</table>
</body>
</html>
