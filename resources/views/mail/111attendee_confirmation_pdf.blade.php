<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Registration - SEMICON India 2025</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5; color: #333333;">
    <table cellpadding="0" cellspacing="0" border="0" width="100%"
        style="max-width: 600px; margin: 0 auto; background-color: #ffffff;">
        <tr>
            <td style="padding: 40px 30px;">
                <!-- Header with Logo -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td align="center" style="padding-bottom: 30px;">
                            <table cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
                                <tr>
                                    <td style="padding: 0 10px;">
                                        <img src="{{ public_path('/asset/img/logos/SEMI_IESA_logo.png?') }}"
                                            alt="SEMI IESA Logo" style="max-height: 100px; max-width: 180px;">
                                    </td>
                                    <td style="padding: 0 10px;">
                                        <img src="{{ public_path('/asset/img/logos/meity-logo.png') }}" alt="MeitY Logo"
                                            style="max-height: 100px; max-width: 180px;">
                                    </td>
                                    <td style="padding: 0 10px;">
                                        <img src="{{ public_path('/asset/img/logos/ism_logo.png') }}" alt="ISM Logo"
                                            style="max-height: 100px; max-width: 180px;">
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Confirmation Message -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0">

                    <tr>
                        <td align="center" style="padding-bottom: 18px;">
                            <h1 style="font-size: 20px; color: #0056b3; margin: 0 0 10px 0; font-weight: bold;">
                                Ticket ID: <span style="color: #218838;">{{ $data['unique_id'] }}</span>
                            </h1>
                        </td>
                    </tr>
                    <tr>


                        <td align="center" style="padding-bottom: 30px;">

                            <p
                                style="font-size: 16px; color: #666666; margin: 0; text-align:justify; line-height:25px;">
                                Thank you for registering for {{ config('constants.EVENT_NAME') }}
                                {{ config('constants.EVENT_YEAR') }}. We are delighted to confirm your registration for
                                this exciting event. Please keep this receipt for your reference. </p>




                        </td>
                    </tr>
                </table>

                <!-- Event Details Card -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="background-color: #f9f9f9; border-radius: 8px; border: 1px solid #e0e0e0; margin-bottom: 10px;">
                    <tr>
                        <td style="padding: 25px;">
                            <h2 style="font-size: 20px; font-weight: bold; color: #333333; margin: 0 0 20px 0;">
                                {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</h2>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td valign="top" style="padding-bottom: 15px;">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td valign="top" style="padding-right: 15px;">
                                                    <img src="{{ public_path('/mails/calendar-icon.png') }}"
                                                        alt="Calendar" width="20" height="20"
                                                        style="vertical-align: middle;">
                                                </td>
                                                <td style="font-size: 16px; color: #555555;">
                                                    {{ \Carbon\Carbon::parse(config('constants.EVENT_DATE_START'))->format('M j') }}-{{ \Carbon\Carbon::parse(config('constants.EVENT_DATE_END'))->format('j, Y') }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" style="padding-bottom: 15px;">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td valign="top" style="padding-right: 15px;">
                                                    <img src="{{ public_path('/mails/clock-icon.png') }}"
                                                        alt="Clock" width="20" height="20"
                                                        style="vertical-align: middle;">
                                                </td>

                                                <td style="font-size: 16px; color: #555555;">
                                                    <span style="font-weight: 500;">Visiting hours:</span>
                                                    <div style="margin-bottom: 10px; margin-top: 4px;">
                                                        <span
                                                            style="font-weight: 500; display: block; margin-bottom: 4px;">Exhibition</span>
                                                        <div style="margin-left: 12px;">
                                                            <div>September 2–3, {{ config('constants.EVENT_YEAR') }}:
                                                                10:00 AM – 6:00 PM</div>
                                                            <div>September 4, {{ config('constants.EVENT_YEAR') }}:
                                                                10:00 AM – 5:00 PM</div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <span
                                                            style="font-weight: 500; display: block; margin-bottom: 4px;">Conference
                                                        </span>
                                                        <div style="margin-left: 12px;">
                                                            <div>
                                                                September 2–4, {{ config('constants.EVENT_YEAR') }}:
                                                                9:00 AM – 6:00 PM
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top" style="padding-bottom: 15px;">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td valign="top" style="padding-right: 15px;">
                                                    <img src="{{ public_path('/mails/location-icon.png') }}"
                                                        alt="Location" width="20" height="20"
                                                        style="vertical-align: middle;">
                                                </td>
                                                <td style="font-size: 16px; color: #555555;">
                                                    {{ config('constants.EVENT_VENUE') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top">
                                        <table cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td valign="top" style="padding-right: 15px;">
                                                    <img src="{{ public_path('/mails/globe-icon.png') }}"
                                                        alt="Website" width="20" height="20"
                                                        style="vertical-align: middle;">
                                                </td>
                                                <td>
                                                    <a href="{{ config('constants.EVENT_WEBSITE') }}"
                                                        style="font-size: 16px; color: #0066cc; text-decoration: none;">{{ parse_url(config('constants.EVENT_WEBSITE'), PHP_URL_HOST) }}</a>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>




                <!-- Ticket Information with QR Code -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0"
                    style="border: 1px solid #e0e0e0; border-radius: 8px; margin-bottom: 10px; overflow: hidden;">
                    <tr>
                        <td style="background-color: #333333; color: #ffffff; padding: 12px 20px;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="font-size: 18px; font-weight: bold; margin: 0; text-align: left;">
                                        Participant Details
                                    </td>
                                    <td
                                        style="text-align: right; font-size: 14px; color: #cccccc; font-weight: normal;">
                                        Date: {{ \Carbon\Carbon::parse($data['registration_date'])->format('M d, Y') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                    </tr>
                    <!-- [START] Registration Details Section -->
                    <tr>
                        <td style="padding: 25px;">
                            <!-- Details List Container -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color: #ffffff; border: 1px solid #e0e0e0; border-radius: 8px;">
                                <tr>
                                    <td style="padding: 20px 25px; border-bottom: 1px solid #eeeeee;">
                                        <p
                                            style="margin: 0 0 3px 0; font-size: 14px; color: #888888; font-family: Arial, sans-serif;">
                                            Participant Name</p>
                                        <p
                                            style="margin: 0; font-size: 16px; font-weight: 500; color: #333333; font-family: Arial, sans-serif;">
                                            {{ $data['name'] }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 25px; border-bottom: 1px solid #eeeeee;">
                                        <p
                                            style="margin: 0 0 3px 0; font-size: 14px; color: #888888; font-family: Arial, sans-serif;">
                                            Participant Email</p>
                                        <p
                                            style="margin: 0; font-size: 16px; font-weight: 500; color: #333333; font-family: Arial, sans-serif;">
                                            {{ $data['email'] }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 25px; border-bottom: 1px solid #eeeeee;">
                                        <p
                                            style="margin: 0 0 3px 0; font-size: 14px; color: #888888; font-family: Arial, sans-serif;">
                                            Participant Mobile</p>
                                        <p
                                            style="margin: 0; font-size: 16px; font-weight: 500; color: #333333; font-family: Arial, sans-serif;">
                                            {{ $data['mobile'] }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 25px; border-bottom: 1px solid #eeeeee;">
                                        <p
                                            style="margin: 0 0 3px 0; font-size: 14px; color: #888888; font-family: Arial, sans-serif;">
                                            Organization/Company</p>
                                        <p
                                            style="margin: 0; font-size: 16px; font-weight: 500; color: #333333; font-family: Arial, sans-serif;">
                                            {{ $data['company_name'] ?? '-' }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 20px 25px; border-bottom: 1px solid #eeeeee;">
                                        <p
                                            style="margin: 0 0 3px 0; font-size: 14px; color: #888888; font-family: Arial, sans-serif;">
                                            Designation/Position</p>
                                        <p
                                            style="margin: 0; font-size: 16px; font-weight: 500; color: #333333; font-family: Arial, sans-serif;">
                                            {{ $data['designation'] ?? '-' }}</p>
                                    </td>
                                </tr>
                                <!-- <tr>
        <td style="padding: 20px 25px;">
          <p style="margin: 0 0 3px 0; font-size: 14px; color: #888888; font-family: Arial, sans-serif;">Ticket ID</p>
          <p style="margin: 0; font-size: 16px; font-weight: bold; color: #0056b3; font-family: Arial, sans-serif;">{{ $data['unique_id'] }}</p>
        </td>
      </tr> -->
                            </table>
                            <!-- /Details List Container -->
                        </td>
                    </tr>
                    <!-- [END] Registration Details Section -->
                </table>


                <!-- Access Information Section -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 10px;">
                    <tr>
                        <td style="padding: 20px 0;">
                            <!-- Exhibition Access Card -->
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="background-color: #eaf6ff; border-radius: 8px; border: 1px solid #b3d8fd; margin-bottom: 18px;">
                                <tr>
                                    <td style="padding: 18px 22px;">
                                        <h3
                                            style="margin: 0 0 8px 0; font-size: 17px; color: #0056b3; font-weight: bold;">
                                            <!-- <img src="{{ config('constants.HOSTED_URL') }}/mails/badge-icon.png" alt="Badge" width="20" height="20" style="vertical-align: middle; margin-right: 7px;"> -->
                                            Exhibition Access
                                        </h3>
                                        <p style="margin: 0; font-size: 15px; color: #333;">
                                            Please collect your badge at the visitor registration area. With this badge,
                                            you will be able to access the show at <strong>Hall 1</strong> from
                                            <strong>September 2 – 4</strong> during the opening hours specified above.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            <!-- Conference Access Card -->
                            <h3 style="font-size: 17px; color: #218838; font-weight: bold; margin: 0 0 8px 0;">
                                Conference Access
                            </h3>
                            <p style="margin: 0 0 8px 0; font-size: 15px; color: #333;">
                                You are most welcome to attend the conference in person to interact with industry peers.
                                In addition, the event is streamed and can be accessed below.
                            </p>
                        </td>
                    </tr>
                </table>



                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:10px;">
                    <tr>
                        <td align="center">
                            <table cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
                                <tr>
                                    <td colspan="2" style="padding-bottom: 18px;">
                                        <h3
                                            style="font-size: 17px; color: #0056b3; font-weight: bold; margin: 0; text-align: left;">
                                            Live Streaming Link:
                                        </h3>
                                    </td>
                                </tr>
                                @php
                                    $streams = [
                                        [
                                            'url' => 'https://webcast.gov.in/meity/semiconindia',
                                            'icon' => 'webacast.png',
                                            'label' => 'Webcast',
                                            'color' => '#218838',
                                        ],
                                        [
                                            'url' => 'https://www.youtube.com/@IndiaSemiconductorMission/streams',
                                            'icon' => 'yt.png',
                                            'label' => 'YouTube',
                                            'color' => '#c4302b',
                                        ],
                                    ];
                                @endphp
                                @foreach ($streams as $i => $stream)
                                    <tr>
                                        <td style="padding:{{ $i === 0 ? '0' : '18px' }} 15px 0 15px;">
                                            <a href="{{ $stream['url'] }}" target="_blank"
                                                style="text-decoration:none;">
                                                <img src="{{ public_path('/mails/' . $stream['icon']) }}"
                                                    alt="{{ $stream['label'] }}" width="32" height="32"
                                                    style="vertical-align:middle;">
                                            </a>
                                        </td>
                                        <td
                                            style="padding:{{ $i === 0 ? '0' : '18px' }} 15px 0 15px; text-align:left;">
                                            <a href="{{ $stream['url'] }}" target="_blank"
                                                style="color:{{ $stream['color'] }}; font-size:16px; font-weight:bold; text-decoration:none;">
                                                {{ $stream['label'] }}
                                                <!-- <img src="{{ config('constants.HOSTED_URL') }}/mails/link-icon.png" alt="Link" width="16" height="16" style="vertical-align:middle; margin-left:4px;"> -->
                                            </a>
                                            <div style="font-size:14px; color:#555;">
                                                <a href="{{ $stream['url'] }}" target="_blank"
                                                    style="color:#0066cc; text-decoration:underline;">
                                                    {{ $stream['url'] }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                </table>


                <!-- Additional Information -->
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 10px;">


                    <tr>
                        <td style="font-size: 14px; color: #666666; line-height: 1.5;">


                            <p style="margin: 0 0 15px 0;">
                                We look forward to your active participation and meaningful engagement at the 'SEMICON
                                India 2025' . Thank you for joining us, and we wish you a successful and enriching
                                experience.
                            </p>
                    <tr>
                        <td>
                            <div
                                style="border: 2px solid #1976d2; background-color: #e3f2fd; border-radius: 8px; padding: 18px 22px; margin-bottom: 18px;">
                                <p style="margin: 0; font-size: 15px; color: #0d47a1; font-weight: bold;">
                                    Kindly note that participation (In-person) in the inauguration event is subject to
                                    final confirmation based on availability and will be informed separately. </strong>
                                </p>
                            </div>
                        </td>
                    </tr>



                    <p style="margin: 0 0 15px 0;">Note: To access the live stream of the event, simply click on the
                        provided streaming link at the designated time. Make sure you have a stable internet connection
                        to enjoy a seamless viewing experience. Please note that the streaming link will only be active
                        during the scheduled event time, so be sure to mark your calendars and set a reminder.</p>

                    <p style="margin: 0;">If you have any questions, please feel free to contact us at <a
                            href="mailto:visit"
                            style="color: #0066cc; text-decoration: none;">visit </a>
                    </p>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="border-top: 1px solid #e0e0e0; padding-top: 20px;">
        <tr>
            <td align="center" style="font-size: 12px; color: #888888;">
                <p style="margin: 0 0 5px 0;">© {{ config('constants.EVENT_NAME') }}
                    {{ config('constants.EVENT_YEAR') }}. All rights reserved.</p>
                <p style="margin: 0;">This email was sent to {{ $data['email'] }} </p>
            </td>
        </tr>
    </table>
    </td>
    </tr>
    </table>
</body>

</html>
