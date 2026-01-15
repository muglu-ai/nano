<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.4;
            color: #333333;
            max-width: 600px;
            margin: 0 auto;
            padding: 10px;
            background-color: #f5f5f5;
            font-size: 12px;
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
            display: table;
            width: 100%;
            table-layout: fixed;
            box-sizing: border-box;
        }
        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: middle;
            padding: 0 10px;
        }
        .header-left {
            width: 65%;
            text-align: left;
        }
        .header-right {
            width: 35%;
            text-align: right;
            padding-right: 0;
        }
        .event-logo {
            max-width: 100%;
            height: auto;
            max-height: 80px;
        }
        .social-links {
            display: inline-block;
            white-space: nowrap;
        }
        .social-links a {
            display: inline-block;
            margin: 0 4px;
            text-decoration: none;
            vertical-align: middle;
            line-height: 0;
        }
        .social-links img {
            width: 20px;
            height: 20px;
            display: block;
            object-fit: contain;
        }
        .receipt-header {
            display: table;
            width: 100%;
            padding: 10px 15px;
            background: #f5f5f5;
            border-bottom: 1px solid #e0e0e0;
            table-layout: fixed;
            box-sizing: border-box;
        }
        .receipt-left,
        .receipt-right {
            display: table-cell;
            vertical-align: middle;
            padding: 0 10px;
            word-wrap: break-word;
        }
        .receipt-left {
            width: 50%;
            text-align: left;
        }
        .receipt-right {
            width: 50%;
            text-align: right;
            padding-right: 0;
        }
        .receipt-type {
            background: #ffffff;
            color: #333333;
            padding: 5px 12px;
            border-radius: 0;
            display: inline-block;
            font-weight: 700;
            font-size: 11px;
            border: 1px solid #d0d0d0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
        .receipt-date {
            font-size: 11px;
            color: #666666;
            white-space: nowrap;
            display: inline-block;
        }
        .content {
            padding: 15px 18px;
        }
        .order-info {
            background: #f5f5f5;
            border-left: 3px solid #666666;
            padding: 10px 12px;
            margin: 8px 0;
            border-radius: 0;
        }
        .order-info strong {
            color: #333333;
            font-size: 12px;
        }
        .order-info p {
            margin: 5px 0 0 0;
            font-size: 11px;
            color: #666666;
        }
        .section {
            margin: 8px 0;
            padding-bottom: 6px;
            border-bottom: 1px solid #e0e0e0;
        }
        .section:last-child {
            border-bottom: none;
        }
        .section-title {
            color: #333333;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 4px;
        }
        .section-title i {
            margin-right: 6px;
            color: #666666;
            font-size: 11px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 11px;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #666666;
            flex: 1;
            font-size: 11px;
        }
        .info-value {
            color: #333333;
            flex: 1;
            text-align: right;
            font-size: 11px;
        }
        .delegates-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 10px;
        }
        .delegates-table th,
        .delegates-table td {
            padding: 4px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        .delegates-table th {
            background: #f5f5f5;
            color: #333333;
            font-weight: 600;
            font-size: 10px;
        }
        .price-breakdown {
            background: #f5f5f5;
            border-radius: 0;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #e0e0e0;
        }
        .price-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            color: #333333;
            font-size: 11px;
        }
        .price-row.total {
            font-size: 13px;
            font-weight: 700;
            padding-top: 6px;
            margin-top: 6px;
            border-top: 2px solid #666666;
            color: #333333;
        }
        .btn-container {
            text-align: center;
            margin: 10px 0;
        }
        .btn-pay-now {
            display: inline-block;
            background: #0066cc;
            color: #ffffff !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 13px;
            border: 1px solid #0066cc;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 102, 204, 0.3);
        }
        .btn-pay-now:hover {
            background: #0052a3;
            border-color: #0052a3;
        }
        .footer {
            background: #f5f5f5;
            padding: 10px 15px;
            text-align: center;
            font-size: 10px;
            color: #666666;
            border-top: 2px solid #e0e0e0;
        }
        .organizer-logo {
            max-width: 220px;
            height: auto;
            margin-bottom: 10px;
        }
        .footer-content {
            margin-top: 10px;
        }
        .footer-content a {
            color: #333333;
            text-decoration: underline;
        }
        .secretariat-info {
            display: table;
            width: 100%;
            margin-top: 8px;
            border-top: 1px solid #e0e0e0;
            padding-top: 8px;
        }
        .secretariat-left,
        .secretariat-right {
            display: table-cell;
            vertical-align: top;
        }
        .secretariat-left {
            width: 40%;
            padding: 0 10px;
            border-right: 1px solid #e0e0e0;
            text-align: center;
            vertical-align: middle;
        }
        .secretariat-right {
            width: 60%;
            padding: 0 8px 0 15px;
        }
        .secretariat-title {
            color: #333333;
            font-size: 11px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .secretariat-details {
            font-size: 10px;
            color: #666666;
            line-height: 1.4;
        }
        .secretariat-details p {
            margin: 2px 0;
        }
        .secretariat-details strong {
            color: #333333;
        }
        .secretariat-details a {
            color: #333333;
            text-decoration: underline;
        }
        .alert {
            background: #f5f5f5;
            border-left: 3px solid #666666;
            padding: 10px 12px;
            margin: 8px 0;
            border-radius: 0;
            font-size: 11px;
        }
        .alert p {
            margin: 0;
            color: #333333;
            font-size: 11px;
        }
        .success-alert {
            background: #f5f5f5;
            border-left: 3px solid #666666;
            padding: 10px 12px;
            margin: 8px 0;
            border-radius: 0;
            font-size: 11px;
        }
        .success-alert p {
            margin: 0;
            color: #333333;
            font-size: 11px;
        }
        .payment-status {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 0;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .payment-status.paid {
            background: #f5f5f5;
            color: #333333;
            border: 1px solid #666666;
        }
        .payment-status.pending {
            background: #f5f5f5;
            color: #666666;
            border: 1px solid #d0d0d0;
        }
        @media only screen and (max-width: 600px) {
            .receipt-left,
            .receipt-right,
            .secretariat-left,
            .secretariat-right {
                display: block;
                width: 100%;
                padding: 10px 0;
                text-align: left !important;
            }
            .receipt-right {
                text-align: left !important;
            }
            .secretariat-left {
                border-right: none;
                border-bottom: 1px solid #e0e0e0;
                text-align: center !important;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                @if(config('constants.event_logo'))
                <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.EVENT_NAME') }}" class="event-logo">
                @endif
            </div>
            <div class="header-right">
                <div class="social-links">
                    @if(config('constants.SOCIAL_LINKS.facebook'))
                    <a href="{{ config('constants.SOCIAL_LINKS.facebook') }}" target="_blank" title="Facebook">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg" alt="Facebook" style="width: 32px; height: 32px; display: block;">
                    </a>
                    @endif
                    @if(config('constants.SOCIAL_LINKS.twitter'))
                    <a href="{{ config('constants.SOCIAL_LINKS.twitter') }}" target="_blank" title="Twitter/X">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6f/Logo_of_Twitter.svg" alt="Twitter" style="width: 32px; height: 32px; display: block;">
                    </a>
                    @endif
                    @if(config('constants.SOCIAL_LINKS.linkedin'))
                    <a href="{{ config('constants.SOCIAL_LINKS.linkedin') }}" target="_blank" title="LinkedIn">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/c/ca/LinkedIn_logo_initials.png" alt="LinkedIn" style="width: 32px; height: 32px; display: block;">
                    </a>
                    @endif
                    @if(config('constants.SOCIAL_LINKS.instagram'))
                    <a href="{{ config('constants.SOCIAL_LINKS.instagram') }}" target="_blank" title="Instagram">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/a/a5/Instagram_icon.png" alt="Instagram" style="width: 32px; height: 32px; display: block;">
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="receipt-left">
                <div class="receipt-type">
                    @if($order->status === 'paid')
                        ✓ CONFIRMATION RECEIPT
                    @else
                        ⏳ PROVISIONAL RECEIPT
                    @endif
                </div>
            </div>
            <div class="receipt-right">
                <div class="receipt-date">
                    <strong>Date of Registration:</strong> {{ $order->created_at->format('d-m-Y') }}
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content">
            <p style="font-size: 12px; margin-bottom: 8px;">Dear {{ $order->registration->contact->name ?? 'Valued Customer' }},</p>
            
            <p style="font-size: 12px; margin-bottom: 8px;">Thank you for Submitting Information on <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>. Please complete the payment using following pay now link / button.</p>

            <!-- TIN and PIN Information -->
            <div class="order-info">
                <strong>TIN No.: {{ $order->order_no }}</strong>
                @if($order->status === 'paid')
                @php
                    // Generate or retrieve PIN number for paid orders
                    // PIN format: PRN-BTS-2026-EXHP-XXXXXX
                    $pinNo = $order->pin_no ?? null;
                    if (!$pinNo && $order->status === 'paid') {
                        // Generate PIN if not exists (this should ideally be done in controller)
                        $prefix = config('constants.PIN_NO_PREFIX', 'PRN-BTS-2026-EXHP-');
                        $randomNumber = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
                        $pinNo = $prefix . $randomNumber;
                    }
                @endphp
                @if($pinNo)
                <p style="margin-top: 8px;"><strong>PIN No.:</strong> {{ $pinNo }}</p>
                @endif
                @endif
                <p style="margin-top: 8px;">Please keep this TIN number for your records.</p>
            </div>

            <!-- Payment Status -->
            <div class="order-info" style="margin-top: 10px;">
                <div style="margin-bottom: 8px;">
                    <strong>Payment Status:</strong> 
                    <span class="payment-status {{ $order->status === 'paid' ? 'paid' : 'pending' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>
                @if($order->status === 'paid')
                @php
                    $payment = $order->primaryPayment();
                    $paymentMethod = $payment ? ($payment->payment_method ?? 'Credit Card') : 'Credit Card';
                @endphp
                <div style="margin-top: 8px;">
                    <strong>Payment Method:</strong> 
                    <span style="font-size: 12px; color: #333333;">{{ $paymentMethod }}</span>
                </div>
                @endif
            </div>

            <!-- Alert (only show if unpaid) -->
            @if($order->status !== 'paid')
            <div class="alert">
                <p><strong>⚠️ Action Required:</strong> Your order is pending payment. Please complete the payment to confirm your registration.</p>
            </div>
            @else
            <div class="success-alert">
                <p><strong>✓ Payment Confirmed:</strong> Your registration has been confirmed. Thank you for your payment!</p>
            </div>
            @endif

            <!-- Registration Information -->
            <div class="section">
                <div class="section-title">
                    <i>📋</i> Registration Information
                </div>
                <div class="info-row">
                    <span class="info-label">Registration Category:</span>
                    <span class="info-value">{{ $order->registration->registrationCategory->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Ticket Type:</span>
                    <span class="info-value">{{ $order->items->first()->ticketType->name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Day Access:</span>
                    <span class="info-value">
                        @php
                            $firstItem = $order->items->first();
                            $selectedDay = $firstItem && $firstItem->selected_event_day_id ? $firstItem->selectedDay : null;
                            $ticketType = $firstItem ? $firstItem->ticketType : null;
                        @endphp
                        @if($selectedDay)
                            {{ $selectedDay->label }} ({{ \Carbon\Carbon::parse($selectedDay->date)->format('M d, Y') }})
                        @elseif($ticketType && ($ticketType->all_days_access || ($ticketType->enable_day_selection && $ticketType->include_all_days_option && !$firstItem->selected_event_day_id)))
                            All Days
                        @elseif($ticketType)
                            @php
                                $accessibleDays = $ticketType->getAllAccessibleDays();
                            @endphp
                            @if($accessibleDays->count() > 0)
                                {{ $accessibleDays->pluck('label')->implode(', ') }}
                            @else
                                All Days
                            @endif
                        @else
                            All Days
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Number of Delegates:</span>
                    <span class="info-value">{{ $order->items->sum('quantity') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Currency:</span>
                    <span class="info-value">{{ $order->registration->nationality === 'International' ? 'USD ($)' : 'INR (₹)' }}</span>
                </div>
            </div>

            <!-- Organisation Information -->
            <div class="section">
                <div class="section-title">
                    <i>🏢</i> Organisation Information
                </div>
                <div class="info-row">
                    <span class="info-label">Organisation Name:</span>
                    <span class="info-value">{{ $order->registration->company_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Industry Sector:</span>
                    <span class="info-value">{{ $order->registration->industry_sector }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Organisation Type:</span>
                    <span class="info-value">{{ $order->registration->organisation_type }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Country:</span>
                    <span class="info-value">{{ $order->registration->company_country }}</span>
                </div>
                @if($order->registration->company_state)
                <div class="info-row">
                    <span class="info-label">State:</span>
                    <span class="info-value">{{ $order->registration->company_state }}</span>
                </div>
                @endif
                @if($order->registration->company_city)
                <div class="info-row">
                    <span class="info-label">City:</span>
                    <span class="info-value">{{ $order->registration->company_city }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">{{ $order->registration->company_phone }}</span>
                </div>
            </div>

            <!-- Organisation Details for Raising the Invoice (Only if GST required) -->
            @if($order->registration->gst_required)
            <div class="section">
                <div class="section-title">
                    <i>🧾</i> Organisation Details for Raising the Invoice
                </div>
                <div class="info-row">
                    <span class="info-label">Organisation Name (To create invoice in the name of):</span>
                    <span class="info-value">{{ $order->registration->gst_legal_name ?? $order->registration->company_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Invoice Address:</span>
                    <span class="info-value">{{ $order->registration->gst_address ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Organisation GST Registration No:</span>
                    <span class="info-value">{{ $order->registration->gstin ?? '-' }}</span>
                </div>
                @php
                    // Extract PAN from GSTIN (first 2 digits are state code, next 10 are PAN)
                    $panNo = $order->registration->gstin ? substr($order->registration->gstin, 2, 10) : null;
                @endphp
                @if($panNo)
                <div class="info-row">
                    <span class="info-label">Organisation PAN No:</span>
                    <span class="info-value">{{ $panNo }}</span>
                </div>
                @endif
                @if($order->registration->gst_state)
                <div class="info-row">
                    <span class="info-label">State:</span>
                    <span class="info-value">{{ $order->registration->gst_state }}</span>
                </div>
                @endif
                @php
                    // Get contact person details from registration or contact
                    $contactName = $order->registration->contact->name ?? null;
                    $contactPhone = $order->registration->contact->phone ?? $order->registration->company_phone ?? null;
                @endphp
                @if($contactName)
                <div class="info-row">
                    <span class="info-label">Contact Person Name:</span>
                    <span class="info-value">{{ $contactName }}</span>
                </div>
                @endif
                @if($contactPhone)
                <div class="info-row">
                    <span class="info-label">Phone No:</span>
                    <span class="info-value">{{ $contactPhone }}</span>
                </div>
                @endif
            </div>
            @endif

            <!-- Delegate Details -->
            @if($order->registration->delegates && $order->registration->delegates->count() > 0)
            <div class="section">
                <div class="section-title">
                    <i>👥</i> Delegate Details
                </div>
                <table class="delegates-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Delegate Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Job Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->registration->delegates as $delegate)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $delegate->salutation }} {{ $delegate->first_name }} {{ $delegate->last_name }}</td>
                            <td>{{ $delegate->email }}</td>
                            <td>{{ $delegate->phone ?? '-' }}</td>
                            <td>{{ $delegate->job_title ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Price Breakdown -->
            @php
                $isInternational = ($order->registration->nationality === 'International' || $order->registration->nationality === 'international');
                $currencySymbol = $isInternational ? '$' : '₹';
                $priceFormat = 2; // Both use 2 decimal places
            @endphp
            <div class="price-breakdown">
                <div class="section-title" style="margin-top: 0;">
                    <i>💰</i> Price Breakdown
                </div>
                @foreach($order->items as $item)
                <div class="price-row">
                    <span>Ticket Price ({{ $item->quantity }} × {{ $currencySymbol }}{{ number_format($item->unit_price, $priceFormat) }}):</span>
                    <span>{{ $currencySymbol }}{{ number_format($item->subtotal, $priceFormat) }}</span>
                </div>
                <div class="price-row">
                    <span>GST ({{ $item->gst_rate }}%):</span>
                    <span>{{ $currencySymbol }}{{ number_format($item->gst_amount, $priceFormat) }}</span>
                </div>
                <div class="price-row">
                    <span>Processing Charge ({{ $item->processing_charge_rate }}%):</span>
                    <span>{{ $currencySymbol }}{{ number_format($item->processing_charge_amount, $priceFormat) }}</span>
                </div>
                @endforeach
                <div class="price-row total">
                    <span>Total Amount:</span>
                    <span>{{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}</span>
                </div>
            </div>

            <!-- Pay Now Button (only show if unpaid) -->
            @if($order->status !== 'paid')
            <div class="btn-container">
                <a href="{{ route('tickets.payment.by-tin', ['eventSlug' => $event->slug ?? $event->id, 'tin' => $order->order_no]) }}" class="btn-pay-now">
                    Click Here to Pay Now ({{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }})
                </a>
            </div>

            <p style="text-align: center; color: #666666; font-size: 11px; margin-top: 8px;">
                Click the button above to complete your payment securely. This link will remain valid until your payment is completed.
            </p>
            <p style="text-align: center; color: #666666; font-size: 10px; margin-top: 5px; font-style: italic;">
                <strong>Note:</strong> After payment realization, a final payment acknowledgement receipt will be provided.
            </p>
            @else
            <div style="background: #f5f5f5; padding: 10px; border: 1px solid #e0e0e0; margin: 8px 0; text-align: center;">
                <p style="margin: 0; color: #333333; font-size: 12px; font-weight: 600;">
                    ✓ Payment Completed Successfully
                </p>
                <p style="margin: 5px 0 0 0; color: #666666; font-size: 11px;">
                    Your registration is confirmed. You will receive further communication regarding the event.
                </p>
            </div>
            @endif

        </div>

        <!-- Secretariat Information -->
        <div class="secretariat-info">
            <div class="secretariat-left">
                @if(config('constants.organizer_logo'))
                <img src="{{ config('constants.organizer_logo') }}" alt="{{ config('constants.organizer.name') }}" class="organizer-logo" style="max-width: 150px; height: auto; margin-bottom: 10px;">
                @endif
            </div>
            <div class="secretariat-right">
                <div class="secretariat-title">{{ config('constants.EVENT_NAME') }} Secretariat</div>
                <div class="secretariat-details">
                    <p><strong>{{ config('constants.organizer.name') }}</strong></p>
                    <p>{!! config('constants.organizer.address') !!}</p>
                    <p><strong>Tel:</strong> {{ config('constants.organizer.phone') }}</p>
                    <p><strong>Email:</strong> <a href="mailto:{{ config('constants.organizer.email') }}">{{ config('constants.organizer.email') }}</a></p>
                    <p><strong>Website:</strong> <a href="{{ config('constants.EVENT_WEBSITE') }}">{{ config('constants.EVENT_WEBSITE') }}</a></p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-content">
                <p style="margin: 5px 0; font-size: 10px; color: #999999;">This is an automated email. Please do not reply to this message.</p>
                <p style="margin: 5px 0; font-size: 10px; color: #999999;">&copy; {{ date('Y') }} {{ config('constants.organizer.name') }}. All rights reserved.</p>
            </div>
        </div>
    </div>
</body>
</html>
