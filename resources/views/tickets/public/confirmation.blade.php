@extends('enquiry.layout')

@section('title', 'Payment Confirmation')

@push('styles')
<style>
    .confirmation-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .confirmation-container .registration-progress {
        margin-bottom: 2rem;
    }

    .success-icon {
        font-size: 4rem;
        color: #28a745;
        margin-bottom: 1rem;
    }

    .preview-section {
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

    .payment-status-badge {
        display: inline-block;
        padding: 0.4rem 0.9rem;
        border-radius: 5px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .payment-status-badge.paid {
        background: #28a745;
        color: white;
    }

    .payment-status-badge.pending {
        background: #ffc107;
        color: #333;
    }

    .success-alert {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        border-left: 4px solid #28a745;
        border-radius: 8px;
        padding: 1rem;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        @php
            $isPaid = $order->status === 'paid';
        @endphp
        <h2>
            <i class="fas fa-check-circle me-2"></i>
            @if($isPaid)
                Payment Successful!
            @else
                Registration Confirmation
            @endif
        </h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
    <!-- Progress Bar -->
        @php
            $isPaid = $order->status === 'paid';
            $currentStep = $isPaid ? 4 : 3;
        @endphp
        @include('tickets.public.partials.progress-bar', ['currentStep' => $currentStep])
    
        <div class="text-center mb-4">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
            <p class="lead mb-3" style="color: var(--text-primary); font-size: 1rem;">
            Thank you for your registration. Your order has been confirmed.
        </p>
            </div>

        <!-- Order Details -->
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-receipt"></i>
                Order Details
            </h4>
            @php
                $isInternational = ($order->registration->nationality === 'International' || $order->registration->nationality === 'international');
                $currencySymbol = $isInternational ? '$' : '₹';
                $priceFormat = $isInternational ? 2 : 0; // 2 decimals for USD, 0 for INR
            @endphp
            <table class="info-table">
                <tr class="{{ $order->status === 'paid' ? 'status-row' : 'status-row pending' }}">
                    <td class="label-cell" style="color: {{ $order->status === 'paid' ? '#155724' : '#856404' }};">Payment Status</td>
                    <td class="value-cell">
                        <span class="payment-status-badge {{ $order->status === 'paid' ? 'paid' : 'pending' }}">
                            {{ $order->status === 'paid' ? '✓ PAID' : '⏳ PENDING' }}
                        </span>
                    </td>
                </tr>
                @php
                    // Fetch PIN from invoice table
                    $invoice = \App\Models\Invoice::where('invoice_no', $order->order_no)
                        ->where('type', 'ticket_registration')
                        ->first();
                    $pinNo = $invoice->pin_no ?? null;
                @endphp
                <tr>
                    <td class="label-cell">Order Number (TIN)</td>
                    <td class="value-cell"><strong>{{ $order->order_no }}</strong></td>
                </tr>
                @if($order->status === 'paid' && $pinNo)
                <tr>
                    <td class="label-cell">PIN No.</td>
                    <td class="value-cell"><strong style="color: #0066cc;">{{ $pinNo }}</strong></td>
                </tr>
                @endif
                <tr>
                    <td class="label-cell">Event</td>
                    <td class="value-cell">{{ $order->registration->event->event_name }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Ticket Type</td>
                    <td class="value-cell">
                        @foreach($order->items as $item)
                            <strong>{{ $item->ticketType->name }}</strong> ({{ $item->quantity }}x)
                        @endforeach
                    </td>
                </tr>
                @php
                    $firstTicketType = $order->items->first()?->ticketType;
                @endphp
                @if($firstTicketType && $firstTicketType->category)
                <tr>
                    <td class="label-cell">Category</td>
                    <td class="value-cell">{{ $firstTicketType->category->name }}</td>
                </tr>
                @endif
                @if($firstTicketType && $firstTicketType->subcategory)
                <tr>
                    <td class="label-cell">Subcategory</td>
                    <td class="value-cell">{{ $firstTicketType->subcategory->name }}</td>
                </tr>
                @endif
                {{-- Day Access row hidden as per requirement --}}
                {{-- <tr>
                    <td class="label-cell">Day Access</td>
                    <td class="value-cell">
                    @foreach($order->items as $item)
                            @if($item->selectedDay)
                                <span class="day-badge primary">{{ $item->selectedDay->label }}</span>
                                <small class="text-muted">({{ \Carbon\Carbon::parse($item->selectedDay->date)->format('M d, Y') }})</small>
                            @elseif($item->ticketType->all_days_access || ($item->ticketType->enable_day_selection && $item->ticketType->include_all_days_option && !$item->selected_event_day_id))
                                <span class="day-badge success">All 3 Days</span>
                            @else
                                @php
                                    $accessibleDays = $item->ticketType->getAllAccessibleDays();
                                @endphp
                                @if($accessibleDays->count() > 0)
                                    @foreach($accessibleDays as $day)
                                        <span class="day-badge primary">{{ $day->label }}</span>
                                    @endforeach
                                @else
                                    <span class="day-badge success">All 3 Days</span>
                                @endif
                            @endif
                    @endforeach
                    </td>
                </tr> --}}
                <tr>
                    <td class="label-cell">Number of Delegates</td>
                    <td class="value-cell">{{ $order->items->sum('quantity') }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Currency</td>
                    <td class="value-cell">{{ $isInternational ? 'USD ($)' : 'INR (₹)' }}</td>
                </tr>
                @if($order->group_discount_applied && $order->group_discount_amount > 0)
                <tr style="background-color: #e7f3ff;">
                    <td class="label-cell" style="color: #004085;">
                        <i class="fas fa-users me-1"></i>
                        Group Discount
                    </td>
                    <td class="value-cell" style="color: #004085;">
                        <strong>-{{ $currencySymbol }}{{ number_format($order->group_discount_amount, $priceFormat) }}</strong>
                        <small class="d-block" style="font-weight: normal; font-size: 0.75rem;">
                            ({{ number_format($order->group_discount_rate, 0) }}% off for {{ $order->items->sum('quantity') }}+ delegates)
                        </small>
                    </td>
                </tr>
                @endif
                @if($order->discount_amount > 0 && $order->promoCode)
                <tr style="background-color: #d4edda;">
                    <td class="label-cell" style="color: #155724;">
                        <i class="fas fa-tag me-1"></i>
                        Promocode Discount
                    </td>
                    <td class="value-cell" style="color: #155724;">
                        <strong>-{{ $currencySymbol }}{{ number_format($order->discount_amount, $priceFormat) }}</strong>
                        @if($order->promoCode->type === 'percentage')
                            <small class="d-block" style="font-weight: normal; font-size: 0.75rem;">
                                ({{ number_format($order->promoCode->value, 0) }}% off base amount)
                            </small>
                        @endif
                    </td>
                </tr>
                @endif
                <tr>
                    <td class="label-cell">Total Amount</td>
                    <td class="value-cell"><strong style="font-size: 1.1rem; color: var(--primary-color);">{{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}</strong></td>
                </tr>
                @if($order->payment_status === 'complimentary')
                <tr>
                    <td class="label-cell" style="color: #155724;">Payment Type</td>
                    <td class="value-cell">
                        <span class="badge bg-success" style="font-size: 0.875rem; padding: 0.375rem 0.75rem;">
                            <i class="fas fa-gift me-1"></i>Complimentary Registration
                        </span>
                    </td>
                </tr>
                @endif
            </table>
            </div>

        <!-- Organisation/Individual Information -->
        <div class="preview-section">
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
                    <td class="value-cell">{{ $order->registration->company_country ?? 'N/A' }}</td>
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
                    <td class="value-cell">{{ $order->registration->company_phone ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Contact Information -->
       
         {{-- <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-user"></i>
                Contact Information
            </h4>
            <table class="info-table">
                <tr>
                    <td class="label-cell">Name</td>
                    <td class="value-cell"><strong>{{ $order->registration->contact->name }}</strong></td>
                </tr>
                <tr>
                    <td class="label-cell">Email</td>
                    <td class="value-cell">{{ $order->registration->contact->email }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Phone</td>
                    <td class="value-cell">{{ $order->registration->contact->phone }}</td>
                </tr>
            </table>
        </div> --}}
        <!-- GST Information -->
            @if($order->registration->gst_required)
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-file-invoice-dollar"></i>
                GST / Invoice Details
            </h4>
            <table class="info-table">
                <tr>
                    <td class="label-cell">GSTIN</td>
                    <td class="value-cell"><strong>{{ $order->registration->gstin ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label-cell">Legal Name (For Invoice)</td>
                    <td class="value-cell">{{ $order->registration->gst_legal_name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Invoice Address</td>
                    <td class="value-cell">{{ $order->registration->gst_address ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">State</td>
                    <td class="value-cell">{{ $order->registration->gst_state ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>
        @endif

        <!-- Delegates Information -->
        @if($order->registration->delegates->count() > 0)
        @php 
            $ticketTypeName = $order->items->first()->ticketType->name ?? 'N/A';
            $hasLinkedIn = $order->registration->delegates->contains(function($delegate) {
                return !empty($delegate->linkedin_profile);
            });
        @endphp
        <div class="preview-section">
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
                @foreach($order->registration->delegates as $index => $delegate)
                    <tr>
                        <td>{{ $index + 1 }}</td>
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

        <!-- Payment Transaction Details -->
            @php
                $paymentDetails = session('payment_details');
                $primaryPayment = $order->primaryPayment();
            $isPaid = $order->status === 'paid';
            $isInternational = ($order->registration->nationality === 'International' || $order->registration->nationality === 'international');
            $currencySymbol = $isInternational ? '$' : '₹';
            $priceFormat = $isInternational ? 2 : 0; // 2 decimals for USD, 0 for INR
            @endphp

        @if($isPaid)
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-credit-card"></i>
                Payment Transaction Details
            </h4>
            
            <div class="success-alert mb-3">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-check-circle" style="color: #28a745; font-size: 1.2rem;"></i>
                    <div style="flex: 1;">
                        <strong style="color: #155724;">Payment Successful!</strong>
                        <p style="color: #155724; margin: 0.25rem 0 0; font-size: 0.9rem;">Your payment has been processed successfully.</p>
                    </div>
                </div>
            </div>

            <table class="info-table">
                <tr>
                    <td class="label-cell">Payment Status</td>
                    <td class="value-cell">
                        <span class="payment-status-badge paid">✓ PAID</span>
                    </td>
                </tr>
                @if($paymentDetails || $primaryPayment)
                <tr>
                    <td class="label-cell">Payment Method</td>
                    <td class="value-cell"><strong>{{ $paymentDetails['gateway'] ?? ($primaryPayment ? ucfirst($primaryPayment->gateway_name) : 'N/A') }}</strong></td>
                </tr>
                @if($primaryPayment && $primaryPayment->method)
                <tr>
                    <td class="label-cell">Payment Type</td>
                    <td class="value-cell"><strong>{{ strtoupper($primaryPayment->method) }}</strong></td>
                </tr>
                @endif
                @if(isset($paymentDetails['transaction_id']) || ($primaryPayment && $primaryPayment->gateway_txn_id))
                <tr>
                    <td class="label-cell">Transaction ID</td>
                    <td class="value-cell"><strong style="color: var(--primary-color);">{{ $paymentDetails['transaction_id'] ?? $primaryPayment->gateway_txn_id }}</strong></td>
                </tr>
                @endif
                @endif
                <tr>
                    <td class="label-cell">Amount Paid</td>
                    <td class="value-cell"><strong style="color: var(--primary-color); font-size: 1.1rem;">{{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}</strong></td>
                </tr>
                @if(($primaryPayment && $primaryPayment->paid_at) || $order->updated_at)
                <tr>
                    <td class="label-cell">Payment Date & Time</td>
                    <td class="value-cell">{{ ($primaryPayment && $primaryPayment->paid_at) ? $primaryPayment->paid_at->format('d M Y, h:i A') : $order->updated_at->format('d M Y, h:i A') }}</td>
                </tr>
                @endif
                @if($primaryPayment && $primaryPayment->pg_response_json)
                @php
                    $responseData = $primaryPayment->pg_response_json;
                    $paymentMode = $responseData['payment_mode'] ?? $responseData['payment_method'] ?? null;
                @endphp
                @if($paymentMode)
                <tr>
                    <td class="label-cell">Payment Mode</td>
                    <td class="value-cell"><strong>{{ strtoupper($paymentMode) }}</strong></td>
                </tr>
                @endif
                @endif
            </table>
            </div>
        @endif

        <div class="mt-4 text-center">
            <div class="success-alert">
                <p style="color: #155724; margin-bottom: 0.5rem;">
                    <i class="fas fa-envelope me-2"></i>
                A payment acknowledgement email has been sent to <strong>{{ $order->registration->contact->email }}</strong>
            </p>
                <p style="color: #155724; font-size: 0.85rem; margin: 0;">
                Please check your email for the receipt and further instructions.
            </p>
            </div>
        </div>
    </div>
</div>
@endsection
