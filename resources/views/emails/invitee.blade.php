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
                                    
                                    <td style="width: 25%; padding: 0 5px; text-align: center;">
                                        <img src="{{ config('constants.HOSTED_URL') }}/asset/img/logos/meity-logo.png?height=80&width=120"
                                            alt="MeitY Logo" style="max-height: 60px; max-width: 100px; width: 100%; height: auto; display: block; margin: 0 auto;">
                                    </td>
                                    <td style="width: 25%; padding: 0 5px; text-align: center;">
                                        <img src="{{ config('constants.HOSTED_URL') }}/asset/img/logos/ism_logo.png?height=80&width=120"
                                            alt="ISM Logo" style="max-height: 60px; max-width: 100px; width: 100%; height: auto; display: block; margin: 0 auto;">
                                    </td>
                                    <td style="width: 25%; padding: 0 5px; text-align: center;">
                                        <img src="{{ config('constants.HOSTED_URL') }}/asset/img/logos/DIC_Logo.webp?height=80&width=120"
                                            alt="DIC Logo" style="max-height: 60px; max-width: 100px; width: 100%; height: auto; display: block; margin: 0 auto;">
                                    </td>
                                    <td style="width: 25%; padding: 0 5px; text-align: center;">
                                        <img src="{{ config('constants.HOSTED_URL') }}/asset/img/logos/SEMI_IESA_logo.png?height=80&width=120"
                                            alt="SEMI IESA Logo" style="max-height: 60px; max-width: 100px; width: 100%; height: auto; display: block; margin: 0 auto;">
                                    </td>
                                    
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
                                    You're Invited to SEMICON India 2025!</h2>


                                <p style="color: #475569; line-height: 1.6; margin-bottom: 15px; font-size: 16px;">
                                    Dear Representative,
                                </p>

                                <p style="color: #475569; line-height: 1.6; margin-bottom: 15px; font-size: 16px;">
                                    <strong>{{ $companyName }}</strong> has invited you to participate in SEMICON India
                                    2025.
                                </p>
                            @endif
                            @if ($delegateType == 'delegate')
                                <p style="color: #475569; line-height: 1.6; margin-bottom: 15px; font-size: 16px;">
                                    Dear Invitee, <br>
                                    Please complete your registration for SEMICON India 2025 by filling out the form to
                                    participate in the SEMICON Inaugural.
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
                                        ? 'visitsemiconindia@semi.org'
                                        : 'semiconindia@semi.org';
                            @endphp

                            @if ($delegateType == 'delegate')
                                <p
                                    style="color: #b91c1c; background-color: #fef2f2; border-left: 4px solid #b91c1c; padding: 12px 16px; margin-bottom: 20px; font-size: 15px;">
                                    <strong>Note:</strong> Kindly note that participation (in-person) in the Inaugural
                                    event is subject to final confirmation and will be informed separately from 3rd week
                                    of August onwards.
                                </p>
                            @endif


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
                                SEMICON India 2025. All rights reserved.
                            </p>
                            <p style="margin: 0; font-size: 14px;">
                                <a href="https://www.semiconindia.org/"
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
