<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }} - Poster Registration</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f6f8;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f6f8; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 30px 20px; background-color: #ffffff; border-radius: 8px 8px 0 0; border-bottom: 2px solid #1a237e;">
                            <!-- Event Logo on Left -->
                            <div style="text-align: left; margin-bottom: 20px;">
                                @if(config('constants.event_logo'))
                                <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.EVENT_NAME') }}" style="max-width: 200px; height: auto; display: block;">
                                @endif
                            </div>
                            
                            <!-- Event Name and Year -->
                            <div style="text-align: center; padding-top: 15px; border-top: 1px solid #e9ecef;">
                                <h1 style="margin: 0; color: #1a237e; font-size: 24px; font-weight: bold;">{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</h1>
                                <p style="margin: 10px 0 0; color: #666666; font-size: 16px; font-weight: 600;">
                                    @if($isThankYouEmail)
                                        Poster Registration - Payment Confirmation
                                    @else
                                        Poster Registration - Provisional Receipt
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Dear {{ $registration->lead_author_name }},
                            </p>
                            
                            @if($isThankYouEmail)
                            <p style="margin: 0 0 25px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Thank you for completing the payment for your poster registration at <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>. Your payment has been successfully received and processed.
                            </p>
                            @else
                            <p style="margin: 0 0 25px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Thank you for registering your poster for <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>. We have received your registration details.
                            </p>
                            @endif

                            <!-- Registration Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Registration Details</h2>
                                
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">TIN Number:</td>
                                        <td style="color: #333333; padding: 5px 0;"><strong>{{ $registration->tin_no }}</strong></td>
                                    </tr>
                                </table>

                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Registration Date:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ \Carbon\Carbon::parse($registration->created_at)->format('d M Y') }}</td>
                                    </tr>
                                </table>

                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Presentation Mode:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ ucwords(str_replace('_', ' ', $registration->presentation_mode)) }}</td>
                                    </tr>
                                </table>

                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Sector:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->sector }}</td>
                                    </tr>
                                </table>

                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Currency:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->currency }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Abstract/Poster Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Abstract / Poster Details</h2>
                                
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Category:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->poster_category }}</td>
                                    </tr>
                                </table>

                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0; vertical-align: top;">Title:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->abstract_title }}</td>
                                    </tr>
                                </table>

                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0; vertical-align: top;">Abstract:</td>
                                        <td style="color: #333333; padding: 5px 0; line-height: 1.6;">{{ $registration->abstract }}</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Authors -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Authors</h2>
                                
                                @foreach($authors as $index => $author)
                                <div style="margin-bottom: {{ $loop->last ? '0' : '20px' }}; padding-bottom: {{ $loop->last ? '0' : '15px' }}; border-bottom: {{ $loop->last ? 'none' : '1px solid #dee2e6' }};">
                                    <div style="margin-bottom: 10px;">
                                        <strong style="color: #1a237e; font-size: 16px;">
                                            {{ $loop->iteration }}. {{ $author->title }} {{ $author->first_name }} {{ $author->last_name }}
                                            @if($author->is_lead)
                                            <span style="background-color: #1a237e; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 5px;">LEAD</span>
                                            @endif
                                            @if($author->is_presenter)
                                            <span style="background-color: #28a745; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 5px;">PRESENTER</span>
                                            @endif
                                            @if($author->will_attend)
                                            <span style="background-color: #17a2b8; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 5px;">ATTENDING</span>
                                            @endif
                                        </strong>
                                    </div>
                                    
                                    <table role="presentation" width="100%" cellpadding="3" cellspacing="0">
                                        <tr>
                                            <td style="width: 30%; color: #666666; padding: 3px 0; font-size: 14px;">Designation:</td>
                                            <td style="color: #333333; padding: 3px 0; font-size: 14px;">{{ $author->designation }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color: #666666; padding: 3px 0; font-size: 14px;">Email:</td>
                                            <td style="color: #333333; padding: 3px 0; font-size: 14px;">{{ $author->email }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color: #666666; padding: 3px 0; font-size: 14px;">Mobile:</td>
                                            <td style="color: #333333; padding: 3px 0; font-size: 14px;">{{ $author->mobile }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color: #666666; padding: 3px 0; font-size: 14px;">Address:</td>
                                            <td style="color: #333333; padding: 3px 0; font-size: 14px;">
                                                {{ $author->city }}, {{ $author->state_name ?? '' }}, {{ $author->country_name ?? '' }} - {{ $author->postal_code }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="color: #666666; padding: 3px 0; font-size: 14px;">Institution:</td>
                                            <td style="color: #333333; padding: 3px 0; font-size: 14px;">
                                                {{ $author->institution }}, {{ $author->affiliation_city }}, {{ $author->affiliation_country_name ?? '' }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                @endforeach
                            </div>

                            <!-- Payment Information -->
                            <div style="background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #856404; font-weight: bold;">Payment Information</h2>
                                
                                @if($isThankYouEmail)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Invoice Number:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->invoice_no }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Payment Status:</td>
                                        <td style="color: #28a745; padding: 5px 0; font-weight: bold;">PAID</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Amount Paid:</td>
                                        <td style="color: #333333; padding: 5px 0; font-weight: bold;">{{ $registration->currency }} {{ number_format($invoice->amount_paid, 2) }}</td>
                                    </tr>
                                </table>
                                @else
                                <!-- Attendee List -->
                                @php
                                    $attendingAuthors = $authors->filter(function($author) {
                                        return $author->will_attend;
                                    });
                                    $pricePerAttendee = $currency === 'INR' ? 2000 : 25;
                                @endphp

                                @if($attendingAuthors->count() > 0)
                                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #dee2e6;">
                                    <p style="margin: 0 0 10px; font-weight: bold; color: #555555;">Attendees ({{ $attendingAuthors->count() }}):</p>
                                    @foreach($attendingAuthors as $attendee)
                                    <div style="margin-bottom: 5px; color: #333333; font-size: 14px;">
                                        • {{ $attendee->title }} {{ $attendee->first_name }} {{ $attendee->last_name }} - {{ $registration->currency }} {{ number_format($attendeeRate, 2) }}
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Base Amount:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->currency }} {{ number_format($registration->base_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">GST ({{ $registration->currency === 'INR' ? '18%' : '0%' }}):</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->currency }} {{ number_format($registration->gst_amount, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="font-weight: bold; color: #555555; padding: 5px 0;">Processing Fee:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registration->currency }} {{ number_format($registration->processing_fee, 2) }}</td>
                                    </tr>
                                    <tr style="border-top: 2px solid #dee2e6;">
                                        <td style="font-weight: bold; color: #333333; padding: 10px 0; font-size: 16px;">Total Amount:</td>
                                        <td style="color: #333333; padding: 10px 0; font-weight: bold; font-size: 16px;">{{ $registration->currency }} {{ number_format($registration->total_amount, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                <!-- Payment Link -->
                                @if(!$isThankYouEmail)
                                <div style="text-align: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid #dee2e6;">
                                    <p style="margin: 0 0 15px; font-size: 14px; color: #666666;">Please complete your payment using the button below:</p>
                                    <a href="{{ $paymentUrl }}" style="display: inline-block; padding: 12px 30px; background-color: #1a237e; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 16px;">Complete Payment</a>
                                </div>
                                @endif
                            </div>

                            @if($isThankYouEmail)
                            <!-- Thank You Message -->
                            <div style="background-color: #d4edda; border-left: 4px solid #28a745; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <p style="margin: 0; color: #155724; font-size: 16px; line-height: 1.6;">
                                    Your registration is now complete. We look forward to seeing you at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}!
                                </p>
                            </div>
                            @endif

                            <!-- Closing -->
                            <p style="margin: 25px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                @if($isThankYouEmail)
                                If you have any questions or require further assistance, please feel free to contact us.
                                @else
                                Please complete your payment at the earliest to confirm your registration.
                                @endif
                            </p>

                            <p style="margin: 15px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Best regards,<br>
                                <strong>{{ config('constants.EVENT_NAME') }} Team</strong>
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; border-radius: 0 0 8px 8px; border-top: 1px solid #dee2e6;">
                            <p style="margin: 0 0 10px; font-size: 14px; color: #666666; line-height: 1.6;">
                                This is an automated email from {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #999999;">
                                © {{ config('constants.EVENT_YEAR') }} {{ config('constants.EVENT_NAME') }}. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
