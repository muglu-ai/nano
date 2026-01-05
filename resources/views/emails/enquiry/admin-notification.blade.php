<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Enquiry Received</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f6f8;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f6f8; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px; background-color: #dc3545; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: bold; text-align: center;">
                                New Enquiry Received
                            </h1>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Hello Admin,
                            </p>
                            
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                A new enquiry has been received for <strong>{{ $enquiry->event ? $enquiry->event->event_name : config('constants.EVENT_NAME', 'Event') }} {{ $enquiry->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</strong>.
                            </p>

                            <!-- Enquiry Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #dc3545; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #dc3545; font-weight: bold;">Enquiry Details</h2>
                                
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
                                        <td style="color: #333333; padding: 5px 0;"><a href="mailto:{{ $enquiry->email }}" style="color: #667eea;">{{ $enquiry->email }}</a></td>
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
                                    @if($enquiry->referral_source)
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Referral Source:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $enquiry->referral_source }}</td>
                                    </tr>
                                    @endif
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
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0; vertical-align: top;">Comments:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $enquiry->comments }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Submitted:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $enquiry->created_at->format('F d, Y h:i A') }}</td>
                                    </tr>
                                </table>
                            </div>

                            <p style="margin: 20px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Please review and follow up with this enquiry as soon as possible.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; border-radius: 0 0 8px 8px; text-align: center; font-size: 12px; color: #666666;">
                            <p style="margin: 0;">This is an automated notification email.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

