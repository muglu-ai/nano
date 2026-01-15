<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</title>
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
        }
        .header {
            background: #ffffff;
            color: #333333;
            padding: 12px 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        .event-logo {
            max-width: 100%;
            height: auto;
            max-height: 80px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 4px;
            text-decoration: none;
            vertical-align: middle;
        }
        .social-links img {
            width: 28px;
            height: 28px;
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
        .content {
            padding: 15px 18px;
        }
        .order-info {
            background: #f5f5f5;
            border-left: 4px solid #0066cc;
            padding: 12px 15px;
            margin: 10px 0;
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
        .delegates-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 12px;
        }
        .delegates-table th {
            background: #0066cc;
            color: #ffffff;
            padding: 10px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
        }
        .delegates-table td {
            padding: 8px;
            border: 1px solid #e0e0e0;
            font-size: 12px;
        }
        .delegates-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        /* Mobile Responsiveness for Email */
        @media only screen and (max-width: 600px) {
            .delegates-table {
                font-size: 11px;
                overflow-x: auto;
                display: block;
                white-space: nowrap;
            }

            .delegates-table th,
            .delegates-table td {
                padding: 6px 4px;
                min-width: 80px;
                word-wrap: break-word;
                white-space: normal;
            }

            .delegates-table th {
                font-size: 10px;
            }
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
        .btn-pay-now {
            display: inline-block;
            background: #28a745;
            color: #ffffff !important;
            padding: 14px 35px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
        .secretariat-table {
                width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-top: 1px solid #e0e0e0;
            padding-top: 10px;
        }
        .secretariat-table td {
            padding: 10px;
            vertical-align: top;
        }
    </style>
</head>
<body>
 <!-- Price Breakdown -->
            @php
                $isInternational = ($order->registration->nationality === 'International' || $order->registration->nationality === 'international');
                $currencySymbol = $isInternational ? '$' : '₹';
                $priceFormat = $isInternational ? 2 : 0; // 2 decimals for USD, 0 for INR
            @endphp

    <div class="email-container">
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #ffffff; border-bottom: 2px solid #e0e0e0;">
            <tr>
                <td style="padding: 12px 15px; width: 65%;">
                @if(config('constants.event_logo'))
                <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.EVENT_NAME') }}" class="event-logo">
                @endif
                </td>
                <td style="padding: 12px 15px; text-align: right; width: 35%;">
                <div class="social-links">
                    @if(config('constants.SOCIAL_LINKS.facebook'))
                        <a href="{{ config('constants.SOCIAL_LINKS.facebook') }}" target="_blank">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook">
                    </a>
                    @endif
                    @if(config('constants.SOCIAL_LINKS.twitter'))
                        <a href="{{ config('constants.SOCIAL_LINKS.twitter') }}" target="_blank">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/6/6f/Logo_of_Twitter.svg" alt="Twitter">
                    </a>
                    @endif
                    @if(config('constants.SOCIAL_LINKS.linkedin'))
                        <a href="{{ config('constants.SOCIAL_LINKS.linkedin') }}" target="_blank">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/c/ca/LinkedIn_logo_initials.png" alt="LinkedIn">
                    </a>
                    @endif
                    @if(config('constants.SOCIAL_LINKS.instagram'))
                        <a href="{{ config('constants.SOCIAL_LINKS.instagram') }}" target="_blank">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png" alt="Instagram">
                    </a>
                    @endif
                </div>
                </td>
            </tr>
        </table>

        <!-- Receipt Header -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #f5f5f5; border-bottom: 1px solid #e0e0e0;">
            <tr>
                <td style="padding: 10px 15px;">
                    <span class="receipt-type">
                    @if($order->status === 'paid')
                        ✓ CONFIRMATION RECEIPT
                    @else
                        ⏳ PROVISIONAL RECEIPT
                    @endif
                    </span>
                </td>
                 <td style="padding: 10px 15px; text-align: right; font-size: 13px; color: #666666;">
                    @if($order->status !== 'paid')
            <div style="text-align: center; margin: 7px 0;">
                <a href="{{ route('tickets.payment.by-tin', ['eventSlug' => $event->slug ?? $event->id, 'tin' => $order->order_no]) }}" class="btn-pay-now">
                    💳 Pay Now - {{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}
                </a>
            </div>
            @endif
                </td>
               
            </tr>
        </table>

        <!-- Content -->
        <div class="content">
            <p style="font-size: 14px; margin-bottom: 10px;">Dear <strong>{{ $order->registration->contact->name ?? 'Valued Customer' }}</strong>,</p>
            
            <p style="font-size: 14px; margin-bottom: 12px;">Thank you for registering for <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>.</p>

            <!-- TIN Information -->
            {{--
            <div class="order-info">
                <table width="100%" cellpadding="0" cellspacing="0">
                
                
                    <tr>
                        <td style="font-size: 16px; font-weight: 700; color: #0066cc;">TIN No.: {{ $order->order_no }}</td>
                    </tr>
                     <tr>
                        <td style="font-size: 16px; font-weight: 700; color: #0066cc;">Date: {{ $order->created_at->format('d-m-Y') }}</td>
                    </tr>
                @if($order->status === 'paid')
                @php
                    $pinNo = $order->pin_no ?? null;
                    if (!$pinNo && $order->status === 'paid') {
                        $prefix = config('constants.PIN_NO_PREFIX', 'PRN-BTS-2026-EXHP-');
                        $randomNumber = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                        $pinNo = $prefix . $randomNumber;
                    }
                @endphp
                @if($pinNo)
                    <tr>
                        <td style="font-size: 14px; padding-top: 5px;"><strong>PIN No.:</strong> {{ $pinNo }}</td>
                    </tr>
                @endif
                @endif
                    <tr>
                        <td style="font-size: 12px; color: #666666; padding-top: 5px;">Please keep this TIN number for your records.</td>
                    </tr>
                </table>
            </div>
            --}}

            <!-- Alert -->
            @if($order->status !== 'paid')
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
            @php
                // Fetch PIN from invoice table
                $invoice = \App\Models\Invoice::where('invoice_no', $order->order_no)
                    ->where('type', 'ticket_registration')
                    ->first();
                $pinNo = $invoice->pin_no ?? null;
            @endphp
            <table class="info-table">
             <tr>
                    <td class="label">TIN NO:</td>
                    <td class="value">{{ $order->order_no }}</td>
                </tr>
                @if($order->status === 'paid' && $pinNo)
                <tr>
                    <td class="label">PIN NO:</td>
                    <td class="value" style="font-weight: 700; color: #0066cc;">{{ $pinNo }}</td>
                </tr>
                @endif
                
                <tr style="background: {{ $order->status === 'paid' ? '#d4edda' : '#fff3cd' }};">
                    <td class="label" style="color: {{ $order->status === 'paid' ? '#155724' : '#856404' }};">Payment Status</td>
                    <td class="value">
                        <span class="payment-badge {{ $order->status === 'paid' ? 'paid' : 'pending' }}">
                            {{ $order->status === 'paid' ? '✓ PAID' : '⏳ PENDING' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label">Registration Category</td>
                    <td class="value">{{ $order->registration->registrationCategory->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Ticket Type</td>
                    <td class="value">{{ $order->items->first()->ticketType->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Day Access</td>
                    <td class="value">
                        @php
                            $firstItem = $order->items->first();
                            $selectedDay = $firstItem && $firstItem->selected_event_day_id ? $firstItem->selectedDay : null;
                            $ticketType = $firstItem ? $firstItem->ticketType : null;
                        @endphp
                        @if($selectedDay)
                            {{ $selectedDay->label }} ({{ \Carbon\Carbon::parse($selectedDay->date)->format('M d, Y') }})
                        @elseif($ticketType && ($ticketType->all_days_access || ($ticketType->enable_day_selection && $ticketType->include_all_days_option && !$firstItem->selected_event_day_id)))
                            All 3 Days
                        @elseif($ticketType)
                            @php $accessibleDays = $ticketType->getAllAccessibleDays(); @endphp
                            @if($accessibleDays->count() > 0)
                                {{ $accessibleDays->pluck('label')->implode(', ') }}
                            @else
                                All 3 Days
                            @endif
                        @else
                            All 3 Days
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Number of Delegates</td>
                    <td class="value">{{ $order->items->sum('quantity') }}</td>
                </tr>
                <tr>
                    <td class="label">Currency</td>
                    <td class="value">{{ $order->registration->nationality === 'International' ? 'USD ($)' : 'INR (₹)' }}</td>
                </tr>
            </table>

            <!-- Organisation Information -->
            <div class="section-title">🏢 Organisation Information</div>
            <table class="info-table">
                <tr>
                    <td class="label">Organisation Name</td>
                    <td class="value">{{ $order->registration->company_name }}</td>
                </tr>
                <tr>
                    <td class="label">Industry Sector</td>
                    <td class="value">{{ $order->registration->industry_sector }}</td>
                </tr>
                <tr>
                    <td class="label">Organisation Type</td>
                    <td class="value">{{ $order->registration->organisation_type }}</td>
                </tr>
                <tr>
                    <td class="label">Country</td>
                    <td class="value">{{ $order->registration->company_country }}</td>
                </tr>
                @if($order->registration->company_state)
                <tr>
                    <td class="label">State</td>
                    <td class="value">{{ $order->registration->company_state }}</td>
                </tr>
                @endif
                @if($order->registration->company_city)
                <tr>
                    <td class="label">City</td>
                    <td class="value">{{ $order->registration->company_city }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Phone</td>
                    <td class="value">{{ $order->registration->company_phone }}</td>
                </tr>
            </table>

            <!-- GST Information (if required) -->
            @if($order->registration->gst_required)
            <div class="section-title">🧾 GST / Invoice Details</div>
            <table class="info-table">
                <tr>
                    <td class="label">Legal Name (For Invoice)</td>
                    <td class="value">{{ $order->registration->gst_legal_name ?? $order->registration->company_name }}</td>
                </tr>
                <tr>
                    <td class="label">GSTIN</td>
                    <td class="value">{{ $order->registration->gstin ?? '-' }}</td>
                </tr>
                @php $panNo = $order->registration->gstin ? substr($order->registration->gstin, 2, 10) : null; @endphp
                @if($panNo)
                <tr>
                    <td class="label">PAN No.</td>
                    <td class="value">{{ $panNo }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Invoice Address</td>
                    <td class="value">{{ $order->registration->gst_address ?? '-' }}</td>
                </tr>
                @if($order->registration->gst_state)
                <tr>
                    <td class="label">State</td>
                    <td class="value">{{ $order->registration->gst_state }}</td>
                </tr>
                @endif
                @php
                    $contactName = $order->registration->contact->name ?? null;
                    $contactPhone = $order->registration->contact->phone ?? $order->registration->company_phone ?? null;
                @endphp
                @if($contactName)
                <tr>
                    <td class="label">Contact Person</td>
                    <td class="value">{{ $contactName }}</td>
                </tr>
                @endif
                @if($contactPhone)
                <tr>
                    <td class="label">Contact Phone</td>
                    <td class="value">{{ $contactPhone }}</td>
                </tr>
                @endif
            </table>
            @endif

            <!-- Delegate Details -->
            @if($order->registration->delegates && $order->registration->delegates->count() > 0)
            <div class="section-title">👥 Delegate Details</div>
            @php $ticketTypeName = $order->items->first()->ticketType->name ?? 'N/A'; @endphp
                <table class="delegates-table">
                    <thead>
                        <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 30%;">Delegate Name</th>
                        <th style="width: 30%;">Email</th>
                        <th style="width: 15%;">Phone</th>
                        <th style="width: 20%;">Ticket Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->registration->delegates as $delegate)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $delegate->salutation }} {{ $delegate->first_name }} {{ $delegate->last_name }}</td>
                            <td>{{ $delegate->email }}</td>
                            <td>{{ $delegate->phone ?? '-' }}</td>
                        <td>{{ $ticketTypeName }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

           
            <div class="section-title">💰 Price Breakdown</div>
            <table class="price-table">
                @foreach($order->items as $item)
                <tr>
                    <td class="label-col">Ticket Price ({{ $item->quantity }} × {{ $currencySymbol }}{{ number_format($item->unit_price, $priceFormat) }})</td>
                    <td class="value-col">{{ $currencySymbol }}{{ number_format($item->subtotal, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td class="label-col">GST ({{ $item->gst_rate }}%)</td>
                    <td class="value-col">{{ $currencySymbol }}{{ number_format($item->gst_amount, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td class="label-col">Processing Charge ({{ $item->processing_charge_rate }}%)</td>
                    <td class="value-col">{{ $currencySymbol }}{{ number_format($item->processing_charge_amount, $priceFormat) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td class="label-col" style="background: #0066cc; color: #ffffff;">Total Amount</td>
                    <td class="value-col" style="background: #0066cc; color: #ffffff;">{{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}</td>
                </tr>
            </table>

            <!-- Pay Now Button -->
            @if($order->status !== 'paid')
            <div style="text-align: center; margin: 7px 0;">
                <a href="{{ route('tickets.payment.by-tin', ['eventSlug' => $event->slug ?? $event->id, 'tin' => $order->order_no]) }}" class="btn-pay-now">
                    💳 Pay Now - {{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}
                </a>
            </div>
            <p style="text-align: center; color: #666666; font-size: 12px; margin-top: 8px;">
                Click the button above to complete your payment securely.
            </p>
            <p style="text-align: center; color: #888888; font-size: 11px; font-style: italic;">
                <strong>Note:</strong> After payment, a final acknowledgement receipt will be provided.
            </p>
            @else
            <!-- Payment Transaction Details (shown only when paid) -->
            @php
                // Fetch payment details from payments table
                $payment = \App\Models\Payment::where('order_id', $order->order_no)
                    ->where('status', 'successful')
                    ->latest()
                    ->first();
            @endphp
            @if($payment)
            <div class="section-title" style="margin-top: 20px;">🧾 Payment Transaction Details</div>
            <table class="info-table">
                <tr>
                    <td class="label">Payment Method</td>
                    <td class="value">{{ $payment->payment_method ?? 'Online' }}</td>
                </tr>
                <tr>
                    <td class="label">Transaction ID</td>
                    <td class="value" style="font-weight: 700; color: #0066cc;">{{ $payment->transaction_id ?? $payment->track_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Amount Paid</td>
                    <td class="value" style="font-weight: 700; color: #155724;">{{ $currencySymbol }}{{ number_format($payment->amount_paid ?? $payment->amount ?? $order->total, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Date</td>
                    <td class="value">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y, h:i A') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label">Payment Status</td>
                    <td class="value"><span style="background: #28a745; color: #fff; padding: 3px 10px; border-radius: 3px; font-weight: 600;">✓ CONFIRMED</span></td>
                </tr>
            </table>
            @endif
            <div style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 8px 0; text-align: center; border-radius: 4px;">
                <p style="margin: 0; color: #155724; font-size: 15px; font-weight: 700;">
                    ✓ Payment Completed Successfully
                </p>
                <p style="margin: 5px 0 0 0; color: #155724; font-size: 13px;">
                    Your registration is confirmed. You will receive further communication regarding the event.
                </p>
            </div>
            @endif
        </div>

        <!-- Secretariat Information -->
        <table class="secretariat-table" style="background: #f8f9fa;">
            <tr>
                <td style="width: 35%; text-align: center; border-right: 1px solid #e0e0e0;">
                @if(config('constants.organizer_logo'))
                    <img src="{{ config('constants.organizer_logo') }}" alt="{{ config('constants.organizer.name') }}" style="width: 120px; height: 120px; object-fit: contain; display: block; margin: 0 auto;">
                @endif
                </td>
                <td style="width: 65%; padding-left: 15px;">
                    <div style="font-size: 13px; font-weight: 700; color: #333333; margin-bottom: 5px;">{{ config('constants.EVENT_NAME') }} Secretariat</div>
                    <div style="font-size: 12px; color: #666666; line-height: 1.5;">
                        <p style="margin: 2px 0;"><strong>{{ config('constants.organizer.name') }}</strong></p>
                        <p style="margin: 2px 0;">{!! config('constants.organizer.address') !!}</p>
                        <p style="margin: 2px 0;"><strong>Tel:</strong> {{ config('constants.organizer.phone') }}</p>
                        <p style="margin: 2px 0;"><strong>Email:</strong> <a href="mailto:{{ config('constants.organizer.email') }}" style="color: #0066cc;">{{ config('constants.organizer.email') }}</a></p>
                        <p style="margin: 2px 0;"><strong>Website:</strong> <a href="{{ config('constants.EVENT_WEBSITE') }}" style="color: #0066cc;">{{ config('constants.EVENT_WEBSITE') }}</a></p>
            </div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p style="margin: 5px 0; font-size: 11px; color: #999999;">This is an automated email. Please do not reply to this message.</p>
            <p style="margin: 5px 0; font-size: 11px; color: #999999;">&copy; {{ date('Y') }} {{ config('constants.organizer.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
