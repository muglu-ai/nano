@extends('enquiry.layout')

@section('title', 'Order Details - ' . ($order->order_no ?? ''))

@push('styles')
<style>
    .receipt-header {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e0e0e0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .receipt-type {
        background: var(--primary-color);
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .receipt-date {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .order-info-box {
        background: #f8f9fa;
        border-left: 4px solid var(--primary-color);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .order-info-box strong {
        color: var(--text-primary);
        font-size: 1.1rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .order-info-box p {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin: 0.5rem 0 0 0;
    }

    .payment-status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-left: 0.5rem;
    }

    .payment-status-badge.paid {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .payment-status-badge.pending {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    .details-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e0e0e0;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: var(--text-primary);
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--progress-inactive);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary-color);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--text-secondary);
        flex: 1;
    }

    .info-value {
        color: var(--text-primary);
        flex: 1;
        text-align: right;
    }

    .delegates-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 1rem;
    }

    .delegates-table th,
    .delegates-table td {
        padding: 0.75rem;
        text-align: left;
        border-bottom: 1px solid #e0e0e0;
    }

    .delegates-table th {
        background: #f8f9fa;
        color: var(--text-primary);
        font-weight: 600;
        font-size: 0.875rem;
    }

    .delegates-table td {
        color: var(--text-secondary);
        font-size: 0.875rem;
    }

    .price-breakdown {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border: 1px solid #e0e0e0;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        font-size: 1rem;
        color: var(--text-primary);
    }

    .price-row.total {
        font-size: 1.5rem;
        font-weight: 700;
        padding-top: 1rem;
        margin-top: 1rem;
        border-top: 2px solid var(--primary-color);
        color: var(--text-primary);
    }

    .alert-box {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin: 1.5rem 0;
    }

    .alert-box.success {
        background: #d4edda;
        border-left-color: #28a745;
    }

    .alert-box p {
        margin: 0;
        color: var(--text-primary);
        font-size: 0.875rem;
    }

    .btn-pay-now {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-dark) 100%);
        color: white !important;
        padding: 1rem 2.5rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 1.1rem;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-pay-now:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
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
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-receipt me-2"></i>Order Details</h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
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
                <strong>Date of Registration:</strong> {{ $order->created_at->format('d-m-Y') }}
            </div>
        </div>

        <!-- Order Info -->
        <div class="order-info-box">
            <strong>TIN No.: {{ $order->order_no }}</strong>
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
                <p><strong>PIN No.:</strong> {{ $pinNo }}</p>
                @endif
            @endif
            <p style="margin-top: 0.75rem;">
                <strong>Payment Status:</strong>
                <span class="payment-status-badge {{ $order->status === 'paid' ? 'paid' : 'pending' }}">
                    {{ ucfirst($order->status) }}
                </span>
            </p>
            @if($order->status === 'paid')
                @php
                    $payment = $order->primaryPayment();
                    $paymentMethod = $payment ? ($payment->payment_method ?? 'Credit Card') : 'Credit Card';
                @endphp
                <p style="margin-top: 0.5rem;">
                    <strong>Payment Method:</strong> {{ $paymentMethod }}
                </p>
            @endif
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
            <div class="info-row">
                <span class="info-label">Registration Category:</span>
                <span class="info-value">{{ $order->registration->registrationCategory->name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ticket Type:</span>
                <span class="info-value">{{ $order->items->first()->ticketType->name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Number of Delegates:</span>
                <span class="info-value">{{ $order->items->sum('quantity') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nationality:</span>
                <span class="info-value">{{ $order->registration->nationality }}</span>
            </div>
        </div>

        <!-- Organisation Information -->
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-building"></i>
                Organisation Information
            </h4>
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
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $email }}</span>
            </div>
        </div>

        <!-- Organisation Details for Invoice (GST) -->
        @if($order->registration->gst_required)
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-file-invoice-dollar"></i>
                Organisation Details for Raising the Invoice
            </h4>
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
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-users"></i>
                Delegate Details
            </h4>
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
            <h4 class="section-title" style="margin-top: 0; border-bottom: none;">
                <i class="fas fa-calculator"></i>
                Price Breakdown
            </h4>
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

        <!-- Pay Now Button (only if unpaid) -->
        @if($order->status !== 'paid')
        <div class="text-center mt-4">
            <a href="{{ route('tickets.payment.process', ['eventSlug' => $event->slug ?? $event->id, 'orderNo' => $order->order_no]) }}" class="btn-pay-now" id="payNowBtn">
                <i class="fas fa-credit-card me-2"></i>
                Complete Payment - {{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}
            </a>
            <p style="text-align: center; color: var(--text-secondary); font-size: 0.875rem; margin-top: 1rem;">
                @if($isInternational)
                    Payment will be processed via <strong>PayPal</strong> in <strong>USD</strong>
                @else
                    Payment will be processed via <strong>CCAvenue</strong> in <strong>INR</strong>
                @endif
            </p>
        </div>
        @else
        <div class="text-center mt-4">
            <a href="{{ route('tickets.confirmation', ['eventSlug' => $event->slug ?? $event->id, 'token' => $order->secure_token]) }}" class="btn-pay-now" style="background: #28a745; border: none;">
                <i class="fas fa-check-circle me-2"></i>
                View Confirmation Details
            </a>
        </div>
        @endif

        <!-- Back Link -->
        <div class="text-center mt-4">
            <a href="{{ route('tickets.payment.lookup', $event->slug ?? $event->id) }}" class="btn-back">
                <i class="fas fa-arrow-left me-2"></i>Back to Lookup
            </a>
        </div>
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
