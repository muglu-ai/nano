<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon"/>
    <title>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }} - Poster Registration</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; color: #333333; background-color: #f7f7f7;">
<!-- Main Table -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 3px auto; background-color: #ffffff; border-collapse: collapse; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-top: 5px;">
    
    <!-- Header with Logo -->
    <tr>
        <td align="center" style="padding: 10px 0; background-color: #ffffff; border-bottom: 1px solid #eeeeee;">
            <table border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
                <tr>
                    <td colspan="5" style="text-align: center;">
                        <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.EVENT_NAME') }}" style="max-width: 300px;">
                        <p style="margin: 5px 0 10px 0;">{{ config('constants.EVENT_NAME') }}</p>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 0 5px;">
                        <a href="{{ config('constants.SOCIAL_LINKS.facebook') }}" target="_blank">
                            <img src="https://cdn-icons-png.flaticon.com/24/733/733547.png" alt="Facebook" style="width:24px; height:24px; vertical-align: middle;">
                        </a>
                    </td>
                    <td style="padding: 0 5px;">
                        <a href="{{ config('constants.SOCIAL_LINKS.twitter') }}" target="_blank">
                            <img src="{{ asset('assets/images/socials/twitter.png') }}" alt="X" style="width:24px; height:24px; vertical-align: middle;">
                        </a>
                    </td>
                    <td style="padding: 0 5px;">
                        <a href="{{ config('constants.SOCIAL_LINKS.instagram') }}" target="_blank">
                            <img src="https://cdn-icons-png.flaticon.com/24/733/733558.png" alt="Instagram" style="width:24px; height:24px; vertical-align: middle;">
                        </a>
                    </td>
                    <td style="padding: 0 5px;">
                        <a href="{{ config('constants.SOCIAL_LINKS.linkedin') }}" target="_blank">
                            <img src="https://cdn-icons-png.flaticon.com/24/733/733561.png" alt="LinkedIn" style="width:24px; height:24px; vertical-align: middle;">
                        </a>
                    </td>
                    <td style="padding: 0 5px;">
                        <a href="{{ config('constants.SOCIAL_LINKS.youtube') }}" target="_blank">
                            <img src="https://cdn-icons-png.flaticon.com/24/1384/1384060.png" alt="YouTube" style="width:24px; height:24px; vertical-align: middle;">
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
    <!-- Title -->
    <tr>
        <td align="center" style="padding: 15px 30px; background-color: #f8f9fa; border-bottom: 2px solid #dddddd;">
            <h1 style="margin: 0; font-size: 20px; font-weight: 600; color: #333333;">
                @if($isThankYouEmail)
                    Poster Registration - Payment Confirmation
                @else
                    Poster Registration - Provisional Receipt
                @endif
            </h1>
        </td>
    </tr>
    
    <!-- Greeting -->
    <tr>
        <td style="padding: 20px 30px;">
            <p style="margin: 0 0 20px; font-size: 14px; color: #333333; line-height: 1.6;">
                Dear {{ $registration->lead_author_name }},
            </p>
            
            @if($isThankYouEmail)
            <p style="margin: 0 0 20px; font-size: 14px; color: #333333; line-height: 1.6;">
                Thank you for completing the payment for your poster registration at <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>. Your payment has been successfully received and processed.
            </p>
            @else
            <p style="margin: 0 0 20px; font-size: 14px; color: #333333; line-height: 1.6;">
                Thank you for registering your poster for <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>. We have received your registration details.
            </p>
            @endif
        </td>
    </tr>

    <!-- Registration Details Header -->
    <tr>
        <td align="center" style="padding: 15px 30px; background-color: #f8f9fa; border-bottom: 2px solid #dddddd;">
            <h2 style="margin: 0; font-size: 18px; font-weight: 600; color: #333333;">Registration Details</h2>
        </td>
    </tr>
    
    <!-- Registration Details Content -->
    <tr>
        <td style="padding: 0;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; word-wrap: break-all; font-size:14px;">
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"><strong>TIN Number:</strong></td>
                    <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;">{{ $registration->tin_no }}</td>
                </tr>
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Registration Date:</strong></td>
                    <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;">{{ \Carbon\Carbon::parse($registration->created_at)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Presentation Mode:</strong></td>
                    <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;">{{ ucwords(str_replace('_', ' ', $registration->presentation_mode)) }}</td>
                </tr>
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Sector:</strong></td>
                    <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;">{{ $registration->sector }}</td>
                </tr>
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Currency:</strong></td>
                    <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;">{{ $registration->currency }}</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Abstract/Poster Details Header -->
    <tr>
        <td align="center" style="padding: 15px 30px; background-color: #f8f9fa; border-bottom: 2px solid #dddddd;">
            <h2 style="margin: 0; font-size: 18px; font-weight: 600; color: #333333;">Abstract / Poster Details</h2>
        </td>
    </tr>
    
    <!-- Abstract/Poster Details Content -->
    <tr>
        <td style="padding: 0;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Category:</strong></td>
                    <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;">{{ $registration->poster_category }}</td>
                </tr>
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa; vertical-align: top;"><strong>Title:</strong></td>
                    <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;">{{ $registration->abstract_title }}</td>
                </tr>
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; vertical-align: top;"><strong>Abstract:</strong></td>
                    <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; line-height: 1.6;">{{ $registration->abstract }}</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Authors Header -->
    <tr>
        <td align="center" style="padding: 15px 30px; background-color: #f8f9fa; border-bottom: 2px solid #dddddd;">
            <h2 style="margin: 0; font-size: 18px; font-weight: 600; color: #333333;">Authors</h2>
        </td>
    </tr>
    
    <!-- Authors Content -->
    @foreach($authors as $index => $author)
    <tr>
        <td style="padding: 0;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                <tr>
                    <td colspan="2" align="left" style="padding: 12px 20px; background-color: {{ $author->is_lead ? '#e7f3ff' : '#f2f2f2' }}; border: 1px solid #dddddd; font-weight: 600;">
                        {{ $loop->iteration }}. {{ $author->title }} {{ $author->first_name }} {{ $author->last_name }}
                        @if($author->is_lead)
                        <span style="background-color: #0B5ED7; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 5px;">LEAD</span>
                        @endif
                        @if($author->is_presenter)
                        <span style="background-color: #28a745; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 5px;">PRESENTER</span>
                        @endif
                        @if($author->will_attend)
                        <span style="background-color: #17a2b8; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 5px;">ATTENDING</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td width="30%" style="padding: 8px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Designation:</strong></td>
                    <td width="70%" style="padding: 8px 20px; border: 1px solid #dddddd;">{{ $author->designation }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Email:</strong></td>
                    <td style="padding: 8px 20px; border: 1px solid #dddddd;">{{ $author->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Mobile:</strong></td>
                    <td style="padding: 8px 20px; border: 1px solid #dddddd;">{{ $author->mobile }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Address:</strong></td>
                    <td style="padding: 8px 20px; border: 1px solid #dddddd;">
                        {{ $author->city }}, {{ $author->state_name ?? '' }}, {{ $author->country_name ?? '' }} - {{ $author->postal_code }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Institution:</strong></td>
                    <td style="padding: 8px 20px; border: 1px solid #dddddd;">
                        {{ $author->institution }}, {{ $author->affiliation_city }}, {{ $author->affiliation_country_name ?? '' }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    @endforeach

    <!-- Payment Information Header -->
    <tr>
        <td align="center" style="padding: 15px 30px; background-color: #f8f9fa; border-bottom: 2px solid #dddddd;">
            <h2 style="margin: 0; font-size: 18px; font-weight: 600; color: #333333;">Payment Information</h2>
        </td>
    </tr>
    
    <!-- Payment Information Content -->
    <tr>
        <td style="padding: 0;">
            @if($isThankYouEmail)
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Invoice Number:</strong></td>
                        <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd;">{{ $invoice->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Payment Status:</strong></td>
                        <td style="padding: 12px 20px; border: 1px solid #dddddd; color: #28a745; font-weight: bold;">PAID</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Amount Paid:</strong></td>
                        <td style="padding: 12px 20px; border: 1px solid #dddddd; font-weight: bold;">{{ $registration->currency }} {{ number_format($invoice->amount_paid, 2) }}</td>
                    </tr>
                </table>
            @else
                @php
                    $attendingAuthors = $authors->filter(function($author) {
                        return $author->will_attend;
                    });
                    $attendeeRate = $currency === 'INR' ? 2000 : 25;
                @endphp

                @if($attendingAuthors->count() > 0)
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-bottom: 10px;">
                    <tr>
                        <td colspan="2" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #f2f2f2;"><strong>Attendees ({{ $attendingAuthors->count() }}):</strong></td>
                    </tr>
                    @foreach($attendingAuthors as $attendee)
                    <tr>
                        <td width="70%" style="padding: 8px 20px; border: 1px solid #dddddd;">{{ $attendee->title }} {{ $attendee->first_name }} {{ $attendee->last_name }}</td>
                        <td width="30%" style="padding: 8px 20px; border: 1px solid #dddddd; text-align: right;">{{ $registration->currency }} {{ number_format($attendeeRate, 2) }}</td>
                    </tr>
                    @endforeach
                </table>
                @endif

                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                    <tr>
                        <td style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Base Amount:</strong></td>
                        <td style="padding: 12px 20px; border: 1px solid #dddddd; text-align: right;">{{ $registration->currency }} {{ number_format($registration->base_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>GST ({{ $registration->currency === 'INR' ? '18%' : '18%' }}):</strong></td>
                        <td style="padding: 12px 20px; border: 1px solid #dddddd; text-align: right;">{{ $registration->currency }} {{ number_format($registration->gst_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Processing Fee:</strong></td>
                        <td style="padding: 12px 20px; border: 1px solid #dddddd; text-align: right;">{{ $registration->currency }} {{ number_format($registration->processing_fee, 2) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 15px 20px; border: 1px solid #dddddd; background-color: #f2f7ff; font-size: 18px;"><strong>Total Amount:</strong></td>
                        <td style="padding: 15px 20px; border: 1px solid #dddddd; background-color: #f2f7ff; font-size: 18px; color: #2980b9; font-weight: bold; text-align: right;">{{ $registration->currency }} {{ number_format($registration->total_amount, 2) }}</td>
                    </tr>
                </table>

                <!-- Payment Link -->
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; margin-top: 20px;">
                    <tr>
                        <td align="center" style="padding: 10px 20px;">
                            <p style="margin: 0 0 15px; font-size: 14px; color: #666666;">Please complete your payment using the button below:</p>
                            <a href="{{ $paymentUrl }}" style="display: inline-block; padding: 12px 30px; background-color: #0B5ED7; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 16px;">Complete Payment</a>
                        </td>
                    </tr>
                </table>
            @endif
        </td>
    </tr>

    @if($isThankYouEmail)
    <!-- Thank You Message -->
    <tr>
        <td style="padding: 20px 30px;">
            <p style="margin: 0; color: #27ae60; font-size: 14px; line-height: 1.6;">
                Your registration is now complete. We look forward to seeing you at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}!
            </p>
        </td>
    </tr>
    @endif

    <!-- Closing Message -->
    <tr>
        <td style="padding: 20px 30px;">
            <p style="margin: 0 0 15px; font-size: 14px; color: #333333; line-height: 1.6;">
                @if($isThankYouEmail)
                If you have any questions or require further assistance, please feel free to contact us.
                @else
                Please complete your payment at the earliest to confirm your registration.
                @endif
            </p>
            <p style="margin: 0; font-size: 14px; color: #333333; line-height: 1.6;">
                Best regards,<br>
                <strong>{{ config('constants.EVENT_NAME') }} Team</strong>
            </p>
        </td>
    </tr>

    <!-- Footer -->
    <tr bgcolor='#FFFFFF'>
        <td style='font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif;'>
            <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                <tr>
                    <td width='4%' height='2'></td>
                    <td width='34%' bgcolor='#D0CAB0'></td>
                    <td width='59%' bgcolor='#D0CAB0'></td>
                    <td width='3%'></td>
                </tr>
                <tr>
                    <td height='10' colspan='4' align='center' valign='middle'></td>
                </tr>
                <tr>
                    <td colspan='2' align='center' valign='middle' style="padding: 10px 0;">
                        <div style="display: inline-block; max-width: 220px; width: 100%; text-align: center;">
                            <img src="{{ config('constants.organizer_logo') }}" style="max-width: 200px; height: auto; display: block; margin: 0 auto;" alt="{{ config('constants.organizer.name') }}"/>
                        </div>
                    </td>
                    <td>
                        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                            <tr>
                                <td style='font-family: Verdana, Arial, Helvetica, sans-serif; color: #666666; font-size: 11px; font-weight: bold;'>
                                    Office : {{ config('constants.organizer.name') }}
                                </td>
                            </tr>
                            <tr>
                                <td style='font-family: Verdana, Arial, Helvetica, sans-serif; color: #666666; font-size: 11px; font-weight: bold;'>
                                    Address : {!! config('constants.organizer.address') !!}
                                </td>
                            </tr>
                            <tr>
                                <td style='font-family: Verdana, Arial, Helvetica, sans-serif; color: #666666; font-size: 11px; font-weight: bold;'>
                                    Tel: {{ config('constants.organizer.phone') }}
                                </td>
                            </tr>
                            <tr>
                                <td style='font-family: Verdana, Arial, Helvetica, sans-serif; color: #666666; font-size: 11px; font-weight: bold;'>
                                    Website: <a href='{{ config('constants.EVENT_WEBSITE') }}' target='_blank'>{{ config('constants.EVENT_WEBSITE') }}</a>
                                </td>
                            </tr>
                            <tr>
                                <td style='font-family: Verdana, Arial, Helvetica, sans-serif; color: #666666; font-size: 11px; font-weight: bold;'>
                                    Karnataka GST No.: {{ config('constants.GSTIN') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td colspan='2' align='center' valign='middle'>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Contact Information -->
    <tr>
        <td style="padding: 20px; text-align: center; color: #666666; font-size: 14px; border-top: 1px solid #eeeeee;">
            <p style="margin: 0 0 10px 0;">If you have any questions, please contact us at <a href="mailto:{{ config('constants.organizer.email') }}">{{ config('constants.organizer.email') }}</a>.</p>
            <p style="margin: 0;">Thank you for poster registration at {{ config('constants.EVENT_NAME') }}.</p>
        </td>
    </tr>

</table>
</body>
</html>
