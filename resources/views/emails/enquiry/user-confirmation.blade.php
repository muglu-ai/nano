<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Enquiry</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f6f8;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f6f8; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Logo -->
                    @if(config('constants.EVENT_LOGO'))
                    <tr>
                        <td style="padding: 20px 30px; background-color: #ffffff; border-radius: 8px 8px 0 0;">
                            <img src="{{ config('constants.EVENT_LOGO') }}" 
                                 alt="{{ config('constants.EVENT_NAME', 'Event') }} Logo" 
                                 style="max-width: 200px; height: auto; display: block;">
                        </td>
                    </tr>
                    @endif
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px; background-color: #ffffff; border-top: {{ config('constants.EVENT_LOGO') ? '2px solid #667eea' : 'none' }}; border-bottom: 2px solid #667eea;">
                            <h1 style="margin: 0; color: #667eea; font-size: 24px; font-weight: bold; text-align: center;">
                                Thank You for Your Enquiry
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Dear {{ $enquiry->full_name }},
                            </p>
                            
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Thank you for your interest in <strong>{{ $enquiry->event ? $enquiry->event->event_name : config('constants.EVENT_NAME', 'Event') }} {{ $enquiry->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</strong>.
                            </p>

                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                We have received your enquiry and our team will review it and get back to you soon.
                            </p>

                            <!-- Enquiry Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #667eea; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #667eea; font-weight: bold;">Your Enquiry Details</h2>
                                
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Name:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $enquiry->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Organisation:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $enquiry->organisation }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Designation:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $enquiry->designation }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Email:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $enquiry->email }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Phone:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $enquiry->phone_full ?? $enquiry->phone_number }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">City:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $enquiry->city }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Country:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $enquiry->country }}</td>
                                    </tr>
                                    @if($enquiry->interests->count() > 0)
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0; vertical-align: top;">Interests:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            @foreach($enquiry->interests as $interest)
                                                • {{ \App\Models\EnquiryInterest::getInterestTypes()[$interest->interest_type] ?? $interest->interest_type }}<br>
                                            @endforeach
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>

                            <p style="margin: 20px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Best regards,<br>
                                <strong>{{ $enquiry->event ? $enquiry->event->event_name : config('constants.EVENT_NAME', 'Event') }} Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; border-radius: 0 0 8px 8px; text-align: center; font-size: 12px; color: #666666;">
                            <p style="margin: 0;">This is an automated email. Please do not reply to this message.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

