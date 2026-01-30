<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <link rel="icon" href="{{config('constants.event_logo')}}" type="image/vnd.microsoft.icon"/>
    <title>{{ config('constants.EVENT_NAME') }} — Exhibitor Login Credentials</title>
</head>
<body style="margin: 0; padding: 0; background: #f4f6f8; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; color: #222;">
<center>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:700px;margin:24px auto;">
        <tr>
            <td>
                <div role="article" aria-label="{{ config('constants.EVENT_NAME') }} Exhibitor Credentials" style="max-width: 700px; margin: 24px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 6px 20px rgba(16, 24, 40, 0.08);">
                    <!-- Header / Logo -->
                    <div style="padding: 20px; text-align: left; background: #ffffff;">
                        <img src="{{config('constants.event_logo')}}" alt="{{ config('constants.EVENT_NAME') }} Logo" style="height: 56px; display: block;"/>
                    </div>

                    <!-- Body -->
                    <div style="padding: 28px;">
                        <h1 style="margin: 0 0 12px 0; font-size: 20px; color: #0f172a;">Dear {{ $name }},</h1>

                        <p style="margin: 0 0 16px 0; line-height: 1.45; color: #334155;">Welcome to <strong>{{config('constants.EVENT_NAME')}} {{config('constants.EVENT_YEAR')}}</strong>!</p>

                        <p style="margin: 0 0 16px 0; line-height: 1.45; color: #334155;">
                            We are delighted to have you onboard as an exhibitor. The Exhibitor Portal allows you to set up and manage your company profile, add team members.
                        </p>

                        <div role="group" aria-label="Login credentials" style="background: #f8fafc; padding: 16px; border-radius: 6px; margin: 16px 0; font-family: monospace; color: #0f172a;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse; background:#f8fafc;">
                                <tbody>
                                <tr>
                                    <td style="padding:12px 10px; font-size:12px; color:#475569; width:140px;">Portal URL</td>
                                    <td style="padding:12px 10px; font-family:monospace; color:#0f172a; word-break:break-all;">
                                        <a href="{{ $setupProfileUrl }}" style="color:#0b69ff; text-decoration:underline;">{{ $setupProfileUrl }}</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 10px; font-size:12px; color:#475569;">USERNAME</td>
                                    <td style="padding:12px 10px; font-family:monospace; color:#0f172a; word-break:break-all;">{{ $username }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:12px 10px; font-size:12px; color:#475569;">PASSWORD</td>
                                    <td style="padding:12px 10px; font-family:monospace; color:#0f172a; word-break:break-all;">{{ $password ?? '' }}</td>
                                </tr>
                                </tbody>
                            </table>
                            <div style="text-align:center; margin-top:18px;">
                                <a href="{{ $setupProfileUrl }}" style="display: inline-block; background: #0b69ff; color: #fff; padding: 12px 18px; border-radius: 6px; font-weight: 600; margin-top: 8px; text-decoration: none;">Login</a>
                            </div>
                        </div>

                        <p style="font-size: 13px; color: #475569; margin: 0 0 16px 0;">If you need any help or experience technical issues, please contact our support team at <a href="mailto:info@interlinks.in" style="color: #1a73e8; text-decoration: none;">info@interlinks.in</a>.</p>

                        <p style="margin: 0 0 16px 0; line-height: 1.45; color: #334155;">We look forward to your active participation and wish you a successful exhibition!</p>

                        <p style="margin-top:18px; margin-bottom: 0; line-height: 1.45; color: #334155;">Warm regards,<br><strong>Team {{ config('constants.EVENT_NAME') }} {{config('constants.EVENT_YEAR')}}</strong></p>
                    </div>

                    <!-- Footer -->
                    <div style="font-size: 13px; color: #667085; padding: 20px; border-top: 1px solid #eef2f7; text-align: center;">
                        <div style="margin-bottom:6px;">{{ config('constants.EVENT_NAME') }} {{config('constants.EVENT_YEAR')}}</div>
                        <div style="font-size: 13px; color: #475569;">For assistance, email <a href="mailto:info@interlinks.in" style="color: #1a73e8; text-decoration: none;">info@interlinks.in</a></div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</center>
</body>
</html>
