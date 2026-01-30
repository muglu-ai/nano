<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon"/>
    <title>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }} - Poster Registration</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.5; color: #333333; max-width: 650px; margin: 0 auto; padding: 10px; background-color: #f5f5f5; font-size: 14px;">
    @php
        $currencySymbol = $registration->currency === 'INR' ? '₹' : '$';
        $priceFormat = $registration->currency === 'INR' ? 0 : 2;
    @endphp

    <div style="background: #ffffff; border-radius: 0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); width: 90%; margin: 0 auto;">
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #ffffff; border-bottom: 2px solid #e0e0e0;">
            <tr>
                <td style="padding: 2px 2px; width: 100%;">
                    @if(config('constants.EMAILER_HEADER_LOGO'))
                    <img src="{{ config('constants.EMAILER_HEADER_LOGO') }}" alt="{{ config('constants.EVENT_NAME') }}" style="max-width: 100%; width: 100%; height: auto;">
                    @endif
                </td>
            </tr>
        </table>

        <!-- Receipt Header -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #f5f5f5; border-bottom: 1px solid #e0e0e0;">
            <tr>
                <td style="padding: 10px 15px;">
                    <span style="background: #ffffff; color: #333333; padding: 5px 12px; display: inline-block; font-weight: 700; font-size: 12px; border: 1px solid #d0d0d0; text-transform: uppercase; letter-spacing: 0.5px;">
                    @if($isThankYouEmail)
                        ✓ CONFIRMATION RECEIPT
                    @else
                        ⏳ PROVISIONAL RECEIPT
                    @endif
                    </span>
                </td>
                <td style="padding: 10px 15px; text-align: right; font-size: 13px; color: #666666;">
                    @if(!$isThankYouEmail)
                    <div style="text-align: center; margin: 7px 0;">
                        <a href="{{ $paymentUrl }}" style="display: inline-block; background: #DAA520; color: #ffffff; padding: 14px 35px; text-decoration: none; border-radius: 5px; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                            💳 Pay Now - {{ $currencySymbol }}{{ number_format($registration->total_amount, $priceFormat) }}
                        </a>
                    </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Content -->
        <div style="padding: 15px 18px;">
            <p style="font-size: 14px; margin-bottom: 10px;">Dear <strong>{{ $registration->lead_author_name }}</strong>,</p>
            
            <p style="font-size: 14px; margin-bottom: 12px;">
                @if($isThankYouEmail)
                Thank you for completing the payment for your poster registration at <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>. Your payment has been successfully received and processed.
                @else
                Thank you for registering your poster for <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>.
                @endif
            </p>

            <!-- Alert -->
            @if(!$isThankYouEmail)
            <div style="background: #fff3cd; border: 1px solid #ffc107; border-left: 4px solid #ffc107; padding: 12px 15px; margin: 10px 0; font-size: 13px; color: #856404;">
                <strong>⚠️ Action Required:</strong> Please complete the payment to confirm your registration.
            </div>
            @else
            <div style="background: #d4edda; border: 1px solid #28a745; border-left: 4px solid #28a745; padding: 12px 15px; margin: 10px 0; font-size: 13px; color: #155724;">
                <strong>✓ Payment Confirmed:</strong> Your registration has been confirmed. Thank you for your payment!
            </div>
            @endif

            <!-- Registration Information -->
            <div style="color: #333333; font-size: 15px; font-weight: 700; margin: 15px 0 8px 0; padding-bottom: 6px; border-bottom: 2px solid #0066cc;">📋 Registration Information</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 10px;">
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Date:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ \Carbon\Carbon::parse($registration->created_at)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">TIN NO:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $registration->tin_no }}</td>
                </tr>
                @if($isThankYouEmail && isset($invoice->pin_no))
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">PIN NO:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%; font-weight: 700; color: #0066cc;">{{ $invoice->pin_no }}</td>
                </tr>
                @endif
                <tr style="background: {{ $isThankYouEmail ? '#d4edda' : '#fff3cd' }};">
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; font-weight: 600; color: {{ $isThankYouEmail ? '#155724' : '#856404' }}; width: 40%;">Payment Status</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; width: 60%;">
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: {{ $isThankYouEmail ? '#28a745' : '#ffc107' }}; color: {{ $isThankYouEmail ? '#ffffff' : '#333333' }};">
                            {{ $isThankYouEmail ? '✓ PAID' : '⏳ PENDING' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Presentation Mode</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ ucwords(str_replace('_', ' ', $registration->presentation_mode)) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Sector</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $registration->sector }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Currency</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $registration->currency === 'INR' ? 'INR (₹)' : 'USD ($)' }}</td>
                </tr>
            </table>

            <!-- Abstract/Poster Details -->
            <div style="color: #333333; font-size: 15px; font-weight: 700; margin: 15px 0 8px 0; padding-bottom: 6px; border-bottom: 2px solid #0066cc;">📝 Abstract / Poster Details</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 10px;">
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Category:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $registration->poster_category }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Title:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $registration->abstract_title }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Abstract:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $registration->abstract }}</td>
                </tr>
            </table>

            <!-- Authors -->
            <div style="color: #333333; font-size: 15px; font-weight: 700; margin: 15px 0 8px 0; padding-bottom: 6px; border-bottom: 2px solid #0066cc;">👥 Authors</div>
            @foreach($authors as $index => $author)
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 15px;">
                <tr>
                    <td colspan="2" style="padding: 10px; background: {{ $author->is_lead ? '#e7f3ff' : '#f8f9fa' }}; font-weight: 700; border: 1px solid #e0e0e0;">
                        {{ $loop->iteration }}. {{ $author->title }} {{ $author->first_name }} {{ $author->last_name }}
                        @if($author->is_lead)
                        <span style="background-color: #0066cc; color: white; padding: 2px 8px; border-radius: 3px; font-size: 11px; margin-left: 5px;">LEAD</span>
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
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Designation:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $author->designation }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Email:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $author->email }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Mobile:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $author->mobile }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Address:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $author->city }}, {{ $author->state_name ?? '' }}, {{ $author->country_name ?? '' }} - {{ $author->postal_code }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Institute / Organization:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $author->institution }}, {{ $author->affiliation_city }}, {{ $author->affiliation_country_name ?? '' }}</td>
                </tr>
            </table>
            @endforeach

            <!-- Payment Information -->
            <div style="color: #333333; font-size: 15px; font-weight: 700; margin: 15px 0 8px 0; padding-bottom: 6px; border-bottom: 2px solid #0066cc;">💳 Payment Information</div>
            @if($isThankYouEmail)
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 10px;">
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Invoice Number:</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $invoice->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Payment Status:</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #28a745; font-weight: 700; width: 60%;">PAID</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Base Amount:</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $currencySymbol }}{{ number_format($invoice->price, $priceFormat) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">GST Amount:</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $currencySymbol }}{{ number_format($invoice->gst_amount , $priceFormat) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Processing Fee:</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $currencySymbol }}{{ number_format($invoice->processing_fee, $priceFormat) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 15px; font-weight: 700; background: #0066cc; color: #ffffff; width: 40%;">Total Amount:</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 15px; font-weight: 700; text-align: right; background: #0066cc; color: #ffffff; width: 60%;">{{ $currencySymbol }}{{ number_format($invoice->total_amount, $priceFormat) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Amount Paid:</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%; font-weight: 700;">{{ $currencySymbol }}{{ number_format($invoice->amount_paid, $priceFormat) }}</td>
                    </tr>
                </table>
            @else
                @php
                    $attendingAuthors = $authors->filter(function($author) {
                        return $author->will_attend;
                    });
                    $attendeeRate = $registration->currency === 'INR' ? 2000 : 25;
                @endphp

                @if($attendingAuthors->count() > 0)
                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 8px 0 10px 0;">
                    <tr>
                        <td colspan="2" style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #f8f9fa; font-weight: 700; width: 70%;">Attendees ({{ $attendingAuthors->count() }}):</td>
                    </tr>
                    @foreach($attendingAuthors as $attendee)
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #ffffff; font-weight: 500; width: 70%;">{{ $attendee->title }} {{ $attendee->first_name }} {{ $attendee->last_name }}</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%;">{{ $currencySymbol }}{{ number_format($attendeeRate, $priceFormat) }}</td>
                    </tr>
                    @endforeach
                </table>
                @endif

                <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 8px 0;">
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #f8f9fa; font-weight: 500; width: 70%;">Base Amount:</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%;">{{ $currencySymbol }}{{ number_format($registration->base_amount, $priceFormat) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #f8f9fa; font-weight: 500; width: 70%;">GST (18%):</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%;">{{ $currencySymbol }}{{ number_format($registration->gst_amount, $priceFormat) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #f8f9fa; font-weight: 500; width: 70%;">Processing Fee:</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%;">{{ $currencySymbol }}{{ number_format($registration->processing_fee, $priceFormat) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 15px; font-weight: 700; background: #0066cc; color: #ffffff; width: 70%;">Total Amount:</td>
                        <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 15px; font-weight: 700; text-align: right; background: #0066cc; color: #ffffff; width: 30%;">{{ $currencySymbol }}{{ number_format($registration->total_amount, $priceFormat) }}</td>
                    </tr>
                </table>
            @endif

            @if($isThankYouEmail)
            <div style="background: #d4edda; border: 1px solid #28a745; border-left: 4px solid #28a745; padding: 12px 15px; margin: 15px 0 10px 0; font-size: 13px; color: #155724;">
                <strong>✓ Registration Complete:</strong> Your registration is now complete. We look forward to seeing you at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}!
            </div>
            @endif

            <p style="margin-top: 15px; font-size: 14px; line-height: 1.6;">
                @if($isThankYouEmail)
                If you have any questions or require further assistance, please feel free to contact us.
                @else
                Please complete your payment at the earliest to confirm your registration.
                @endif
            </p>
            <p style="margin-top: 10px; font-size: 14px; line-height: 1.6;">
                Best regards,<br>
                <strong>{{ config('constants.EVENT_NAME') }} Team</strong>
            </p>
        </div>

        <!-- Footer -->
        <div style="background: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666666; border-top: 2px solid #e0e0e0;">
            <p style="margin: 0 0 10px 0;">If you have any questions, please contact us at <a href="mailto:{{ config('constants.organizer.email') }}" style="color: #0066cc;">{{ config('constants.organizer.email') }}</a>.</p>
            <p style="margin: 0;">Thank you for registering at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}.</p>
        </div>
    </div>
</body>
</html>
