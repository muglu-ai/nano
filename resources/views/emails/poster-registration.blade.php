<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon"/>
    <title>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }} - Poster Registration</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.5;
            color: #333333;
            max-width: 650px;
            margin: 0 auto;
            padding: 10px;
            background-color: #f5f5f5;
            font-size: 14px;
        }
        .email-container {
            background: #ffffff;
            border-radius: 0;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            width: 90%;
            margin: 0 auto;
        }
        .emailer-header {
            max-width: 100%;
            width: 100%;
            height: auto;
        }
        .receipt-header {
            width: 100%;
            padding: 10px 15px;
            background: #f5f5f5;
            border-bottom: 1px solid #e0e0e0;
        }
        .receipt-type {
            background: #ffffff;
            color: #333333;
            padding: 5px 12px;
            display: inline-block;
            font-weight: 700;
            font-size: 12px;
            border: 1px solid #d0d0d0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-pay-now {
            display: inline-block;
            background: goldenrod;
            color: #ffffff !important;
            padding: 14px 35px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .content {
            padding: 15px 18px;
        }
        .section-title {
            color: #333333;
            font-size: 15px;
            font-weight: 700;
            margin: 15px 0 8px 0;
            padding-bottom: 6px;
            border-bottom: 2px solid #0066cc;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-table td {
            padding: 8px 10px;
            border: 1px solid #e0e0e0;
            font-size: 13px;
            vertical-align: top;
        }
        .info-table .label {
            background: #f8f9fa;
            font-weight: 600;
            color: #555555;
            width: 40%;
        }
        .info-table .value {
            color: #333333;
            width: 60%;
        }
        .price-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        .price-table td {
            padding: 8px 10px;
            border: 1px solid #e0e0e0;
            font-size: 13px;
        }
        .price-table .label-col {
            background: #f8f9fa;
            font-weight: 500;
            width: 70%;
        }
        .price-table .value-col {
            text-align: right;
            font-weight: 600;
            width: 30%;
        }
        .price-table .total-row td {
            background: #0066cc;
            color: #ffffff;
            font-size: 15px;
            font-weight: 700;
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-left: 4px solid #ffc107;
            padding: 12px 15px;
            margin: 10px 0;
            font-size: 13px;
            color: #856404;
        }
        .success-alert {
            background: #d4edda;
            border: 1px solid #28a745;
            border-left: 4px solid #28a745;
            padding: 12px 15px;
            margin: 10px 0;
            font-size: 13px;
            color: #155724;
        }
        .payment-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .payment-badge.paid {
            background: #28a745;
            color: #ffffff;
        }
        .payment-badge.pending {
            background: #ffc107;
            color: #333333;
        }
        .footer {
            background: #f5f5f5;
            padding: 15px;
            text-align: center;
            font-size: 12px;
            color: #666666;
            border-top: 2px solid #e0e0e0;
        }
    </style>
</head>
<body>
    @php
        $currencySymbol = $registration->currency === 'INR' ? '₹' : '$';
        $priceFormat = $registration->currency === 'INR' ? 0 : 2;
    @endphp

    <div class="email-container">
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #ffffff; border-bottom: 2px solid #e0e0e0;">
            <tr>
                <td style="padding: 2px 2px; width: 100%;">
                    @if(config('constants.EMAILER_HEADER_LOGO'))
                    <img src="{{ config('constants.EMAILER_HEADER_LOGO') }}" alt="{{ config('constants.EVENT_NAME') }}" class="emailer-header">
                    @endif
                </td>
            </tr>
        </table>

        <!-- Receipt Header -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #f5f5f5; border-bottom: 1px solid #e0e0e0;">
            <tr>
                <td style="padding: 10px 15px;">
                    <span class="receipt-type">
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
                        <a href="{{ $paymentUrl }}" class="btn-pay-now">
                            💳 Pay Now - {{ $currencySymbol }}{{ number_format($registration->total_amount, $priceFormat) }}
                        </a>
                    </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Content -->
        <div class="content">
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
            <div class="alert">
                <strong>⚠️ Action Required:</strong> Please complete the payment to confirm your registration.
            </div>
            @else
            <div class="success-alert">
                <strong>✓ Payment Confirmed:</strong> Your registration has been confirmed. Thank you for your payment!
            </div>
            @endif

            <!-- Registration Information -->
            <div class="section-title">📋 Registration Information</div>
            <table class="info-table">
                <tr>
                    <td class="label">Date:</td>
                    <td class="value">{{ \Carbon\Carbon::parse($registration->created_at)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td class="label">TIN NO:</td>
                    <td class="value">{{ $registration->tin_no }}</td>
                </tr>
                @if($isThankYouEmail && isset($invoice->pin_no))
                <tr>
                    <td class="label">PIN NO:</td>
                    <td class="value" style="font-weight: 700; color: #0066cc;">{{ $invoice->pin_no }}</td>
                </tr>
                @endif
                <tr style="background: {{ $isThankYouEmail ? '#d4edda' : '#fff3cd' }};">
                    <td class="label" style="color: {{ $isThankYouEmail ? '#155724' : '#856404' }};">Payment Status</td>
                    <td class="value">
                        <span class="payment-badge {{ $isThankYouEmail ? 'paid' : 'pending' }}">
                            {{ $isThankYouEmail ? '✓ PAID' : '⏳ PENDING' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Presentation Mode</td>
                    <td class="value">{{ ucwords(str_replace('_', ' ', $registration->presentation_mode)) }}</td>
                </tr>
                <tr>
                    <td class="label">Sector</td>
                    <td class="value">{{ $registration->sector }}</td>
                </tr>
                <tr>
                    <td class="label">Currency</td>
                    <td class="value">{{ $registration->currency === 'INR' ? 'INR (₹)' : 'USD ($)' }}</td>
                </tr>
            </table>

            <!-- Abstract/Poster Details -->
            <div class="section-title">📝 Abstract / Poster Details</div>
            <table class="info-table">
                <tr>
                    <td class="label">Category:</td>
                    <td class="value">{{ $registration->poster_category }}</td>
                </tr>
                <tr>
                    <td class="label">Title:</td>
                    <td class="value">{{ $registration->abstract_title }}</td>
                </tr>
                <tr>
                    <td class="label">Abstract:</td>
                    <td class="value">{{ $registration->abstract }}</td>
                </tr>
            </table>

            <!-- Authors -->
            <div class="section-title">👥 Authors</div>
            @foreach($authors as $index => $author)
            <table class="info-table" style="margin-bottom: 15px;">
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
                    <td class="label">Designation:</td>
                    <td class="value">{{ $author->designation }}</td>
                </tr>
                <tr>
                    <td class="label">Email:</td>
                    <td class="value">{{ $author->email }}</td>
                </tr>
                <tr>
                    <td class="label">Mobile:</td>
                    <td class="value">{{ $author->mobile }}</td>
                </tr>
                <tr>
                    <td class="label">Address:</td>
                    <td class="value">{{ $author->city }}, {{ $author->state_name ?? '' }}, {{ $author->country_name ?? '' }} - {{ $author->postal_code }}</td>
                </tr>
                <tr>
                    <td class="label">Institute / Organization:</td>
                    <td class="value">{{ $author->institution }}, {{ $author->affiliation_city }}, {{ $author->affiliation_country_name ?? '' }}</td>
                </tr>
            </table>
            @endforeach

            <!-- Payment Information -->
            <div class="section-title">💳 Payment Information</div>
            @if($isThankYouEmail)
                <table class="info-table">
                    <tr>
                        <td class="label">Invoice Number:</td>
                        <td class="value">{{ $invoice->invoice_no }}</td>
                    </tr>
                    <tr>
                        <td class="label">Payment Status:</td>
                        <td class="value" style="color: #28a745; font-weight: 700;">PAID</td>
                    </tr>
                    <tr>
                        <td class="label">Amount Paid:</td>
                        <td class="value" style="font-weight: 700;">{{ $currencySymbol }}{{ number_format($invoice->amount_paid, $priceFormat) }}</td>
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
                <table class="price-table" style="margin-bottom: 10px;">
                    <tr>
                        <td colspan="2" class="label-col" style="font-weight: 700;">Attendees ({{ $attendingAuthors->count() }}):</td>
                    </tr>
                    @foreach($attendingAuthors as $attendee)
                    <tr>
                        <td class="label-col" style="background: #ffffff;">{{ $attendee->title }} {{ $attendee->first_name }} {{ $attendee->last_name }}</td>
                        <td class="value-col">{{ $currencySymbol }}{{ number_format($attendeeRate, $priceFormat) }}</td>
                    </tr>
                    @endforeach
                </table>
                @endif

                <table class="price-table">
                    <tr>
                        <td class="label-col">Base Amount:</td>
                        <td class="value-col">{{ $currencySymbol }}{{ number_format($registration->base_amount, $priceFormat) }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">GST (18%):</td>
                        <td class="value-col">{{ $currencySymbol }}{{ number_format($registration->gst_amount, $priceFormat) }}</td>
                    </tr>
                    <tr>
                        <td class="label-col">Processing Fee:</td>
                        <td class="value-col">{{ $currencySymbol }}{{ number_format($registration->processing_fee, $priceFormat) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Amount:</td>
                        <td style="text-align: right;">{{ $currencySymbol }}{{ number_format($registration->total_amount, $priceFormat) }}</td>
                    </tr>
                </table>
            @endif

            @if($isThankYouEmail)
            <div class="success-alert" style="margin-top: 15px;">
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
        <div class="footer">
            <p style="margin: 0 0 10px 0;">If you have any questions, please contact us at <a href="mailto:{{ config('constants.organizer.email') }}">{{ config('constants.organizer.email') }}</a>.</p>
            <p style="margin: 0;">Thank you for registering at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}.</p>
        </div>
    </div>
</body>
</html>
