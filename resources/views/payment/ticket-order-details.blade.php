@extends('enquiry.layout')

@section('title', 'Order Details - ' . ($order->order_no ?? ''))

@push('styles')
<style>
    .receipt-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .receipt-type {
        background: var(--primary-color);
        color: white;
        padding: 0.5rem 1.25rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .receipt-date {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .order-info-box {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-left: 4px solid #1976d2;
        border-radius: 8px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .order-info-box strong {
        color: #1565c0;
        font-size: 1.1rem;
        display: block;
        margin-bottom: 0.25rem;
    }

    .order-info-box p {
        color: #1976d2;
        font-size: 0.85rem;
        margin: 0.25rem 0 0 0;
    }

    .payment-status-badge {
        display: inline-block;
        padding: 0.4rem 0.9rem;
        border-radius: 5px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .payment-status-badge.paid {
        background: #28a745;
        color: #ffffff;
    }

    .payment-status-badge.pending {
        background: #ffc107;
        color: #333333;
    }

    .details-section {
        background: #ffffff;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-primary);
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary-color);
        font-size: 1rem;
    }

    /* Tabular Info Styles */
    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table td {
        padding: 0.6rem 0.75rem;
        border: 1px solid #e9ecef;
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .info-table .label-cell {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
        width: 40%;
    }

    .info-table .value-cell {
        color: #212529;
        width: 60%;
    }

    .info-table .status-row {
        background: #d4edda;
    }

    .info-table .status-row.pending {
        background: #fff3cd;
    }

    .delegates-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.5rem;
    }

    .delegates-table th {
        background: var(--primary-color);
        color: white;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .delegates-table td {
        padding: 0.65rem 0.75rem;
        border: 1px solid #e9ecef;
        font-size: 0.85rem;
        color: #495057;
    }

    .delegates-table tr:nth-child(even) {
        background: #f8f9fa;
    }

    .delegates-table tr:hover {
        background: #e9ecef;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .delegates-table {
            font-size: 0.75rem;
            overflow-x: auto;
            display: block;
            white-space: nowrap;
        }

        .delegates-table th,
        .delegates-table td {
            padding: 0.5rem 0.4rem;
            min-width: 100px;
            word-wrap: break-word;
            white-space: normal;
        }

        .delegates-table th {
            font-size: 0.7rem;
        }

        .btn-pay-now {
            padding: 0.7rem 1.5rem !important;
            font-size: 0.9rem !important;
            width: 100% !important;
            max-width: 280px;
            margin: 0 auto;
    }
    }

    /* Price Table Styles */
    .price-table {
        width: 100%;
        border-collapse: collapse;
    }

    .price-table td {
        padding: 0.65rem 0.85rem;
        border: 1px solid #e9ecef;
        font-size: 0.9rem;
    }

    .price-table .label-cell {
        background: #f8f9fa;
        font-weight: 500;
        color: #495057;
        width: 65%;
    }

    .price-table .value-cell {
        text-align: right;
        font-weight: 600;
        color: #212529;
        width: 35%;
    }

    .price-table .total-row td {
        background: var(--primary-color);
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 700;
        padding: 0.85rem;
    }

    .alert-box {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        border-radius: 8px;
        padding: 1rem 1.25rem;
        margin: 1rem 0 1.5rem 0;
    }

    .alert-box.success {
        background: #d4edda;
        border-left-color: #28a745;
    }

    .alert-box p {
        margin: 0;
        color: #856404;
        font-size: 0.875rem;
    }

    .alert-box.success p {
        color: #155724;
    }

    .btn-pay-now {
        background: linear-gradient(135deg, #28a745 0%, #218838 100%);
        color: white !important;
        padding: 0.9rem 2.25rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1rem;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-pay-now:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(40, 167, 69, 0.4);
        color: white !important;
    }

    .btn-back {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.875rem;
        transition: color 0.3s ease;
    }

    .btn-back:hover {
        color: var(--primary-color);
    }

    /* Badge styles */
    .day-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.25rem;
    }

    .day-badge.primary {
        background: var(--primary-color);
        color: white;
    }

    .day-badge.success {
        background: #28a745;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-receipt me-2"></i>Order Details</h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
        @if(session('error'))
        <div class="alert alert-warning mb-3" style="background: #fff3cd; border: 1px solid #ffc107; border-left: 4px solid #ffc107; border-radius: 8px; padding: 1rem;">
            <i class="fas fa-exclamation-triangle me-2" style="color: #856404;"></i>
            <span style="color: #856404;">{{ session('error') }}</span>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success mb-3" style="background: #d4edda; border: 1px solid #28a745; border-left: 4px solid #28a745; border-radius: 8px; padding: 1rem;">
            <i class="fas fa-check-circle me-2" style="color: #155724;"></i>
            <span style="color: #155724;">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="receipt-type">
                @if($order->status === 'paid')
                    ✓ CONFIRMATION RECEIPT
                @else
                    ⏳ PROVISIONAL RECEIPT
                @endif
            </div>
            <div class="receipt-date">
                <strong>Date:</strong> {{ $order->created_at->format('d-m-Y') }}
            </div>
        </div>

        <!-- Order Info -->
        @php
            // Fetch PIN from invoice table
            $invoice = \App\Models\Invoice::where('invoice_no', $order->order_no)
                ->where('type', 'ticket_registration')
                ->first();
            $pinNo = $invoice->pin_no ?? null;
        @endphp
        <div class="order-info-box">
            <strong><i class="fas fa-ticket-alt me-2"></i>TIN No.: {{ $order->order_no }}</strong>
            @if($order->status === 'paid' && $pinNo)
                <p><strong>PIN No.:</strong> <span style="color: #0066cc; font-weight: 700;">{{ $pinNo }}</span></p>
            @endif
            <p style="font-size: 0.8rem; margin-top: 0.5rem;">Please keep this TIN number for your records.</p>
        </div>

        <!-- Alert -->
        @if($order->status !== 'paid')
        <div class="alert-box">
            <p><strong>⚠️ Action Required:</strong> Your order is pending payment. Please complete the payment to confirm your registration.</p>
        </div>
        @else
        <div class="alert-box success">
            <p><strong>✓ Payment Confirmed:</strong> Your registration has been confirmed. Thank you for your payment!</p>
        </div>
        @endif

        <!-- Registration Information -->
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-clipboard-list"></i>
                Registration Information
            </h4>
            <table class="info-table">
                <tr class="{{ $order->status === 'paid' ? 'status-row' : 'status-row pending' }}">
                    <td class="label-cell" style="color: {{ $order->status === 'paid' ? '#155724' : '#856404' }};">Payment Status</td>
                    <td class="value-cell">
                        <span class="payment-status-badge {{ $order->status === 'paid' ? 'paid' : 'pending' }}">
                            {{ $order->status === 'paid' ? '✓ PAID' : '⏳ PENDING' }}
                        </span>
                    </td>
                </tr>
                {{--
                <tr>
                    <td class="label-cell">Registration Category</td>
                    <td class="value-cell">{{ $order->registration->registrationCategory->name ?? 'N/A' }}</td>
                </tr>
                --}}
                <tr>
                    <td class="label-cell">Ticket Type</td>
                    <td class="value-cell"><strong>{{ $order->items->first()->ticketType->name ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label-cell">Day Access</td>
                    <td class="value-cell">
                        @php
                            $firstItem = $order->items->first();
                            $selectedDay = $firstItem && $firstItem->selected_event_day_id ? $firstItem->selectedDay : null;
                            $ticketType = $firstItem ? $firstItem->ticketType : null;
                        @endphp
                        @if($selectedDay)
                            <span class="day-badge primary">{{ $selectedDay->label }}</span>
                            <small class="text-muted">({{ \Carbon\Carbon::parse($selectedDay->date)->format('M d, Y') }})</small>
                        @elseif($ticketType && ($ticketType->all_days_access || ($ticketType->enable_day_selection && $ticketType->include_all_days_option && !$firstItem->selected_event_day_id)))
                            <span class="day-badge success">All 3 Days</span>
                        @elseif($ticketType)
                            @php
                                $accessibleDays = $ticketType->getAllAccessibleDays();
                            @endphp
                            @if($accessibleDays->count() > 0)
                                @foreach($accessibleDays as $day)
                                    <span class="day-badge primary">{{ $day->label }}</span>
                                @endforeach
                            @else
                                <span class="day-badge success">All 3 Days</span>
                            @endif
                        @else
                            <span class="day-badge success">All 3 Days</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">Number of Delegates</td>
                    <td class="value-cell">{{ $order->items->sum('quantity') }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Currency</td>
                    <td class="value-cell">{{ $order->registration->nationality === 'International' ? 'USD ($)' : 'INR (₹)' }}</td>
                </tr>
            </table>
        </div>

        <!-- Organisation/Individual Information -->
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas {{ ($order->registration->registration_type ?? 'Organisation') === 'Individual' ? 'fa-user' : 'fa-building' }}"></i>
                {{ ($order->registration->registration_type ?? 'Organisation') === 'Individual' ? 'Individual' : 'Organisation' }} Information
            </h4>
            <table class="info-table">
                @if(($order->registration->registration_type ?? 'Organisation') === 'Organisation')
                <tr>
                    <td class="label-cell">Organisation Name</td>
                    <td class="value-cell"><strong>{{ $order->registration->company_name ?? 'N/A' }}</strong></td>
                </tr>
                @endif
                <tr>
                    <td class="label-cell">Industry Sector</td>
                    <td class="value-cell">{{ $order->registration->industry_sector ?? 'N/A' }}</td>
                </tr>
                @if(($order->registration->registration_type ?? 'Organisation') === 'Organisation')
                <tr>
                    <td class="label-cell">Organisation Type</td>
                    <td class="value-cell">{{ $order->registration->organisation_type ?? 'N/A' }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label-cell">Country</td>
                    <td class="value-cell">{{ $order->registration->company_country }}</td>
                </tr>
            @if($order->registration->company_state)
                <tr>
                    <td class="label-cell">State</td>
                    <td class="value-cell">{{ $order->registration->company_state }}</td>
                </tr>
            @endif
            @if($order->registration->company_city)
                <tr>
                    <td class="label-cell">City</td>
                    <td class="value-cell">{{ $order->registration->company_city }}</td>
                </tr>
            @endif
                <tr>
                    <td class="label-cell">Phone</td>
                    <td class="value-cell">{{ $order->registration->company_phone }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Email</td>
                    <td class="value-cell">{{ $email }}</td>
                </tr>
            </table>
        </div>

        <!-- Organisation Details for Invoice (GST) -->
        @if($order->registration->gst_required)
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-file-invoice-dollar"></i>
                GST / Invoice Details
            </h4>
            <table class="info-table">
                <tr>
                    <td class="label-cell">Legal Name (For Invoice)</td>
                    <td class="value-cell">{{ $order->registration->gst_legal_name ?? $order->registration->company_name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">GSTIN</td>
                    <td class="value-cell"><strong>{{ $order->registration->gstin ?? '-' }}</strong></td>
                </tr>
            @php
                $panNo = $order->registration->gstin ? substr($order->registration->gstin, 2, 10) : null;
            @endphp
            @if($panNo)
                <tr>
                    <td class="label-cell">PAN No.</td>
                    <td class="value-cell">{{ $panNo }}</td>
                </tr>
            @endif
                <tr>
                    <td class="label-cell">Invoice Address</td>
                    <td class="value-cell">{{ $order->registration->gst_address ?? '-' }}</td>
                </tr>
            @if($order->registration->gst_state)
                <tr>
                    <td class="label-cell">State</td>
                    <td class="value-cell">{{ $order->registration->gst_state }}</td>
                </tr>
            @endif
            @php
                $contactName = $order->registration->contact->name ?? null;
                $contactPhone = $order->registration->contact->phone ?? $order->registration->company_phone ?? null;
            @endphp
            @if($contactName)
                <tr>
                    <td class="label-cell">Contact Person</td>
                    <td class="value-cell">{{ $contactName }}</td>
                </tr>
            @endif
            @if($contactPhone)
                <tr>
                    <td class="label-cell">Contact Phone</td>
                    <td class="value-cell">{{ $contactPhone }}</td>
                </tr>
            @endif
            </table>
        </div>
        @endif

        <!-- Delegate Details -->
        @if($order->registration->delegates && $order->registration->delegates->count() > 0)
        @php
            $ticketTypeName = $order->items->first()->ticketType->name ?? 'N/A';
            $hasLinkedIn = $order->registration->delegates->contains(function($delegate) {
                return !empty($delegate->linkedin_profile);
            });
        @endphp
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-users"></i>
                Delegate Details
            </h4>
            <table class="delegates-table">
                <thead>
                    <tr>
                        <th style="width: {{ $hasLinkedIn ? '4%' : '5%' }};">#</th>
                        <th style="width: {{ $hasLinkedIn ? '24%' : '30%' }};">Delegate Name</th>
                        <th style="width: {{ $hasLinkedIn ? '24%' : '30%' }};">Email</th>
                        <th style="width: {{ $hasLinkedIn ? '12%' : '15%' }};">Phone</th>
                        <th style="width: {{ $hasLinkedIn ? '20%' : '20%' }};">Ticket Type</th>
                        @if($hasLinkedIn)
                        <th style="width: 16%;">LinkedIn Profile</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->registration->delegates as $delegate)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $delegate->salutation }} {{ $delegate->first_name }} {{ $delegate->last_name }}</strong></td>
                        <td>{{ $delegate->email }}</td>
                        <td>{{ $delegate->phone ?? '-' }}</td>
                        <td>{{ $ticketTypeName }}</td>
                        @if($hasLinkedIn)
                        <td>
                            @if(!empty($delegate->linkedin_profile))
                                <a href="{{ $delegate->linkedin_profile }}" target="_blank" rel="noopener noreferrer" style="color: #0077b5; text-decoration: none;">
                                    <i class="fab fa-linkedin me-1"></i>View Profile
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        @endif
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
            $priceFormat = $isInternational ? 2 : 0; // 2 decimals for USD, 0 for INR
        @endphp
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-calculator"></i>
                Price Breakdown
            </h4>
            <table class="price-table">
            @foreach($order->items as $item)
                <tr>
                    <td class="label-cell">Ticket Price ({{ $item->quantity }} × {{ $currencySymbol }}{{ number_format($item->unit_price, $priceFormat) }})</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($item->subtotal, $priceFormat) }}</td>
                </tr>
                @if($order->group_discount_applied && $order->group_discount_amount > 0)
                <tr style="background-color: #e7f3ff;">
                    <td class="label-cell" style="color: #004085;">
                        <i class="fas fa-users me-1"></i>
                        Group Discount
                        <small class="d-block" style="font-weight: normal; font-size: 0.75rem;">({{ number_format($order->group_discount_rate, 0) }}% off for {{ $item->quantity }}+ delegates)</small>
                    </td>
                    <td class="value-cell" style="color: #004085; font-weight: 600;">
                        -{{ $currencySymbol }}{{ number_format($order->group_discount_amount, $priceFormat) }}
                    </td>
                </tr>
                @php $subtotalAfterGroupDiscount = $item->subtotal - $order->group_discount_amount; @endphp
                @else
                @php $subtotalAfterGroupDiscount = $item->subtotal; @endphp
                @endif
                @if($order->discount_amount > 0 && $order->promoCode)
                <tr style="background-color: #d4edda;">
                    <td class="label-cell" style="color: #155724;">
                        <i class="fas fa-tag me-1"></i>
                        Promocode Discount
                        @if($order->promoCode->type === 'percentage')
                            <small class="d-block" style="font-weight: normal; font-size: 0.75rem;">({{ number_format($order->promoCode->value, 0) }}% off base amount)</small>
                        @endif
                    </td>
                    <td class="value-cell" style="color: #155724; font-weight: 600;">
                        -{{ $currencySymbol }}{{ number_format($order->discount_amount, $priceFormat) }}
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">Price After Discounts</td>
                    <td class="value-cell" style="font-weight: 600;">
                        {{ $currencySymbol }}{{ number_format($subtotalAfterGroupDiscount - $order->discount_amount, $priceFormat) }}
                    </td>
                </tr>
                @elseif($order->group_discount_applied && $order->group_discount_amount > 0)
                <tr>
                    <td class="label-cell">Price After Group Discount</td>
                    <td class="value-cell" style="font-weight: 600;">
                        {{ $currencySymbol }}{{ number_format($subtotalAfterGroupDiscount, $priceFormat) }}
                    </td>
                </tr>
                @endif
                @if($item->gst_type === 'cgst_sgst')
                <tr>
                    <td class="label-cell">CGST ({{ number_format($item->cgst_rate ?? 0, 0) }}%)</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($item->cgst_amount ?? 0, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">SGST ({{ number_format($item->sgst_rate ?? 0, 0) }}%)</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($item->sgst_amount ?? 0, $priceFormat) }}</td>
                </tr>
                @else
                <tr>
                    <td class="label-cell">IGST ({{ number_format($item->igst_rate ?? 0, 0) }}%)</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($item->igst_amount ?? 0, $priceFormat) }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label-cell">Processing Charge ({{ $item->processing_charge_rate }}%)</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($item->processing_charge_amount, $priceFormat) }}</td>
                </tr>
            @endforeach
                <tr class="total-row">
                    <td class="label-cell" style="background: var(--primary-color); color: white;">Total Amount</td>
                    <td class="value-cell" style="background: var(--primary-color); color: white;">{{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}</td>
                </tr>
                @if($order->discount_amount > 0 && $order->promoCode)
                {{-- <tr>
                    <td colspan="2" class="text-muted" style="font-size: 0.75rem; padding: 0.5rem 0.75rem; border: none;">
                        <i class="fas fa-info-circle me-1"></i>
                        Note: Discount applies to base amount. GST and processing charges are calculated on the discounted amount.
                    </td>
                </tr> --}}
                @endif
            </table>
        </div>

        <!-- Payment Transaction Details (shown only when paid) -->
        @if($order->status === 'paid')
        @php
            // Fetch payment details from payments table
            $payment = \App\Models\Payment::where('order_id', $order->order_no)
                ->where('status', 'successful')
                ->latest()
                ->first();
        @endphp
        @if($payment)
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-receipt"></i>
                Payment Transaction Details
            </h4>
            <table class="info-table">
                <tr>
                    <td class="label-cell">Payment Method</td>
                    <td class="value-cell">{{ $payment->payment_method ?? 'Online' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Transaction ID</td>
                    <td class="value-cell" style="font-weight: 700; color: var(--primary-color);">{{ $payment->transaction_id ?? $payment->track_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Amount Paid</td>
                    <td class="value-cell" style="font-weight: 700; color: #155724;">{{ $currencySymbol }}{{ number_format($payment->amount_paid ?? $payment->amount ?? $order->total, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Payment Date</td>
                    <td class="value-cell">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y, h:i A') : 'N/A' }}</td>
                </tr>
                <tr class="status-row">
                    <td class="label-cell" style="color: #155724;">Payment Status</td>
                    <td class="value-cell">
                        <span class="payment-status-badge paid">✓ CONFIRMED</span>
                    </td>
                </tr>
            </table>
        </div>
        @endif
        @endif

        <!-- Pay Now Button (only if unpaid) -->
        @if($order->status !== 'paid')
        <div class="text-center mt-4">
            <a href="{{ route('tickets.payment.process', ['eventSlug' => $event->slug ?? $event->id, 'orderNo' => $order->order_no]) }}" class="btn-pay-now" id="payNowBtn">
                <i class="fas fa-credit-card me-2"></i>
                Pay Now - {{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}
            </a>
            <p style="text-align: center; color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.75rem;">
                @if($isInternational)
                    Payment via <strong>PayPal</strong> (USD)
                @else
                    Payment via <strong>CCAvenue</strong> (INR)
                @endif
            </p>
        </div>
        @else
        <div class="text-center mt-4">
            <a href="{{ route('tickets.confirmation', ['eventSlug' => $event->slug ?? $event->id, 'token' => $order->secure_token]) }}" class="btn-pay-now" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%);">
                <i class="fas fa-check-circle me-2"></i>
                View Confirmation
            </a>
        </div>
        @endif

        <!-- Back Link -->
        {{--
        <div class="text-center mt-3">
            <a href="{{ route('tickets.payment.lookup', $event->slug ?? $event->id) }}" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>Back to Lookup
            </a>
        </div>
        --}}
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('payNowBtn')?.addEventListener('click', function(e) {
        e.preventDefault();
        
        const btn = this;
        const originalBtnText = btn.innerHTML;
        const paymentUrl = btn.href;
        
        // Disable button
        btn.style.pointerEvents = 'none';
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        
        Swal.fire({
            title: 'Redirecting to Payment Gateway',
            text: 'Please wait while we redirect you to the secure payment page.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Redirect to payment gateway
        window.location.href = paymentUrl;
    });
</script>
@endpush
