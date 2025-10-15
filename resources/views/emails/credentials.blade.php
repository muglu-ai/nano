<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <link rel="icon" href="{{config('constants.event_logo')}}" type="image/vnd.microsoft.icon"/>
    <title>{{ config('constants.EVENT_NAME') }} — Exhibitor Login Credentials</title>
    <style>
        /* General resets */
        body {
            margin: 0;
            padding: 0;
            background: #f4f6f8;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #222;
        }

        a {
            color: #1a73e8;
            text-decoration: none;
        }

        .container {
            max-width: 700px;
            margin: 24px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(16, 24, 40, 0.08);
        }

        .header {
            padding: 20px;
            text-align: left;
            background: #ffffff;
        }

        .logo {
            height: 56px;
            display: block;
        }

        .content {
            padding: 28px;
        }

        h1 {
            margin: 0 0 12px 0;
            font-size: 20px;
            color: #0f172a;
        }

        p {
            margin: 0 0 16px 0;
            line-height: 1.45;
            color: #334155;
        }

        .cta {
            display: inline-block;
            background: #0b69ff;
            color: #fff;
            padding: 12px 18px;
            border-radius: 6px;
            font-weight: 600;
            margin-top: 8px;
        }

        .details {
            background: #f8fafc;
            padding: 16px;
            border-radius: 6px;
            margin: 16px 0;
            font-family: monospace;
            color: #0f172a;
        }

        .foot {
            font-size: 13px;
            color: #667085;
            padding: 20px;
            border-top: 1px solid #eef2f7;
            text-align: center;
        }

        .small {
            font-size: 13px;
            color: #475569;
        }

        .two-col {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .col {
            flex: 1;
            min-width: 180px;
        }

        .row-details {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .row-item {
            background: #f8fafc;
            padding: 12px 16px;
            border-radius: 6px;
            margin-bottom: 0;
            font-family: monospace;
            color: #0f172a;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
            word-break: break-all;
        }

        .side-by-side-details {
            display: flex;
            flex-direction: row;
            gap: 18px;
            justify-content: flex-start;
            align-items: flex-start;
        }

        .side-item {
            background: #f8fafc;
            padding: 12px 16px;
            border-radius: 6px;
            min-width: 180px;
            flex: 1 1 0;
            font-family: monospace;
            color: #0f172a;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .side-label {
            font-size: 12px;
            color: #475569;
            margin-bottom: 6px;
            font-family: inherit;
        }

        .side-value {
            font-family: monospace;
            font-size: 15px;
            word-break: break-word;
        }

        @media (max-width: 520px) {
            .two-col {
                flex-direction: column;
            }

            .logo {
                height: 48px;
            }

            .row-details {
                gap: 8px;
            }
        }

        @media (max-width: 700px) {
            .side-by-side-details {
                flex-direction: column;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
<center>
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:700px;margin:24px auto;">
        <tr>
            <td>
                <div class="container" role="article"
                     aria-label="{{ config('constants.EVENT_NAME') }} Exhibitor Credentials">
                    <!-- Header / Logo -->
                    <div class="header">
                        <img src="{{config('constants.event_logo')}}" alt="{{ config('constants.EVENT_NAME') }} Logo"
                             class="logo"/>
                    </div>

                    <!-- Body -->
                    <div class="content">
                        <h1>Dear {{ $name }},</h1>

                        <p>Welcome to <strong>{{config('constants.EVENT_NAME')}} {{config('constants.EVENT_YEAR')}}</strong>!</p>

                        <p>We are delighted to have you onboard as an exhibitor. The Exhibitor Portal allows you to set
                            up and manage
                            your company profile,
                            add team members.</p>

                        <div class="details" role="group" aria-label="Login credentials">
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
                                <a href="{{ $setupProfileUrl }}" class="cta">Portal URL</a>
                            </div>
                        </div>

                        <p class="small">If you need any help or experience technical issues, please contact our support
                            team at <a href="mailto:info@interlinks.in">info@interlinks.in</a>.</p>

                        <p>We look forward to your active participation and wish you a successful exhibition!</p>

                        <p style="margin-top:18px;">Warm
                            regards,<br><strong>Team {{ config('constants.EVENT_NAME') }} {{config('constants.EVENT_YEAR')}}</strong></p>
                    </div>

                    <!-- Footer -->
                    <div class="foot">
                        <div style="margin-bottom:6px;">{{ config('constants.EVENT_NAME') }} {{config('constants.EVENT_YEAR')}}
                        </div>
                        <div class="small">For assistance, email <a href="mailto:info@interlinks.in">info@interlinks.in</a></div>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</center>
</body>
</html>
