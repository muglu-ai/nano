<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ELEVATE Registration Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f6f8;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f6f8; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Logo -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #ffffff; border-radius: 8px 8px 0 0; text-align: center;">
                            <img src="{{ asset('images/logos/elevate-logo.jpg') }}" 
                                 alt="ELEVATE Logo" 
                                 style="max-width: 100%; height: auto; display: block; margin: 0 auto;">
                        </td>
                    </tr>
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px; background-color: #6A1B9A; border-bottom: 2px solid #4A0072;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: bold; text-align: center;">
                                Registration Confirmation
                            </h1>
                            <p style="margin: 10px 0 0; color: #ffffff; font-size: 16px; text-align: center;">
                                Felicitation Ceremony for ELEVATE 2025, ELEVATE Unnati 2025 & ELEVATE Minorities 2025 Winners
                            </p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Dear {{ $registration->company_name }},
                            </p>
                            
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Thank you for registering for the <strong>Felicitation Ceremony for ELEVATE 2025, ELEVATE Unnati 2025 & ELEVATE Minorities 2025 Winners</strong>.
                            </p>

                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                We have successfully received your registration details. @if($registration->attendance == 'yes') We look forward to seeing you at the ceremony. @else We have noted your reason for not attending. @endif
                            </p>

                            <!-- Registration Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #6A1B9A; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #6A1B9A; font-weight: bold;">Your Registration Details</h2>
                                
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Company Name:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->company_name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Address:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->address }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">City:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->city }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">State:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->state }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Country:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->country }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Postal Code:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->postal_code }}</td>
                                    </tr>
                                    @if($registration->elevate_application_call_names && count($registration->elevate_application_call_names) > 0)
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0; vertical-align: top;">Elevate Application Call Name:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            @foreach($registration->elevate_application_call_names as $callName)
                                                • {{ $callName }}<br>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif
                                    @if($registration->elevate_2025_id)
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">ELEVATE 2025 ID:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->elevate_2025_id }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Attendance:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            <strong style="color: {{ $registration->attendance == 'yes' ? '#28a745' : '#dc3545' }};">
                                                {{ strtoupper($registration->attendance) }}
                                            </strong>
                                        </td>
                                    </tr>
                                    @if($registration->attendance == 'no' && $registration->attendance_reason)
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0; vertical-align: top;">Reason:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->attendance_reason }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            @if($registration->attendance == 'yes' && $registration->attendees->count() > 0)
                            <!-- Attendees Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #6A1B9A; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #6A1B9A; font-weight: bold;">Attendees Information</h2>
                                
                                @foreach($registration->attendees as $index => $attendee)
                                <div style="margin-bottom: 15px; padding-bottom: 15px; {{ !$loop->last ? 'border-bottom: 1px solid #e0e0e0;' : '' }}">
                                    <h3 style="margin: 0 0 10px; font-size: 16px; color: #6A1B9A; font-weight: bold;">Attendee {{ $index + 1 }}</h3>
                                    <table role="presentation" width="100%" cellpadding="5" cellspacing="0">
                                        <tr>
                                            <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Name:</td>
                                            <td style="color: #333333; padding: 5px 0;">{{ $attendee->salutation }} {{ $attendee->first_name }} {{ $attendee->last_name }}</td>
                                        </tr>
                                        @if($attendee->job_title)
                                        <tr>
                                            <td style="font-weight: bold; color: #555555; padding: 5px 0;">Designation:</td>
                                            <td style="color: #333333; padding: 5px 0;">{{ $attendee->job_title }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td style="font-weight: bold; color: #555555; padding: 5px 0;">Email:</td>
                                            <td style="color: #333333; padding: 5px 0;">{{ $attendee->email }}</td>
                                        </tr>
                                        <tr>
                                            <td style="font-weight: bold; color: #555555; padding: 5px 0;">Phone:</td>
                                            <td style="color: #333333; padding: 5px 0;">{{ $attendee->phone_number }}</td>
                                        </tr>
                                    </table>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <p style="margin: 20px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                If you have any questions or need to make changes to your registration, please contact us at your earliest convenience.
                            </p>

                            <p style="margin: 20px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Best regards,<br>
                                <strong>ELEVATE Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; border-radius: 0 0 8px 8px; text-align: center; font-size: 12px; color: #666666;">
                            <p style="margin: 0;">This is an automated email. Please do not reply to this message.</p>
                            <p style="margin: 10px 0 0;">© {{ date('Y') }} ELEVATE. All Rights Reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
