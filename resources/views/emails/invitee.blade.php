<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEMICON India 2025 Invitation</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 5px;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table role="presentation"
                    style="max-width: 600px; width: 100%; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center" style="padding-bottom: 30px;">
                            <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                <tr>
                                    <div style="width: 25%; display: flex; justify-content: center; align-items: center;">
                                        <img src="{{ config('constants.EMAILER_HEADER_LOGO') }}" alt="{{ config('constants.EMAILER_HEADER_LOGO') }}">
                                    </div>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                    <!-- Content -->
                    <tr>
                        <td style="background-color: #ffffff; padding: 30px 20px;">
                            @if ($delegateType != 'delegate')
                                <h2 style="color: #1e293b; margin-bottom: 20px; font-size: 24px; text-align: center;">
                                    You're Invited to {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}!</h2>


                                <p style="color: #475569; line-height: 1.6; margin-bottom: 15px; font-size: 16px;">
                                    Dear Representative,
                                </p>

                                <p style="color: #475569; line-height: 1.6; margin-bottom: 15px; font-size: 16px;">
                                    <strong>{{ $companyName }}</strong> has invited you to participate in {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}.
                                </p>
                            @endif
                            @php
                                if ($delegateType == 'delegate') {
                                    $route = 'exhibition.invited.inaugural';
                                    $buttonText = 'Enroll Your Participation';
                                } else {
                                    $route = 'exhibition.invited';
                                    $buttonText = 'Confirm Your Participation';
                                }
                            @endphp
                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ route($route, ['token' => $token]) }}"
                                    style="background-color:rgb(0, 0, 0); color: white; padding: 12px 25px; border-radius: 4px; text-decoration: none; font-weight: bold; display: inline-block; font-size: 16px;">
                                    {{ $buttonText }}
                                </a>

                                <p>Or copy and paste the following URL into your browser:</p>
                                <p>{{ route($route, ['token' => $token]) }}</p>
                            </div>
                            @php
                                $email =
                                    $delegateType == 'delegate'
                                        ? config('constants.organizer.email')
                                        : config('constants.organizer.email');
                            @endphp

                            <p
                                style="color: #475569; line-height: 1.6; font-size: 16px; border-top: 1px solid #e2e8f0; padding-top: 20px;">
                                If you have any questions, please contact us at <a href="mailto:{{ $email }}"
                                    style="color:rgb(15, 15, 14); text-decoration: none;">{{ $email }}</a>.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #1e293b; color: white; padding: 20px; text-align: center;">
                            <p style="margin: 0 0 10px 0; font-size: 14px;">
                                {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}. All rights reserved.
                            </p>
                            <p style="margin: 0; font-size: 14px;">
                                <a href="{{ config('constants.EVENT_WEBSITE') }}"
                                    style="color: white; text-decoration: underline;">Visit our website</a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
https://bengalurutechsummit.com/portal/public/asset/css/material-dashboard.min.css?v=3.1.0