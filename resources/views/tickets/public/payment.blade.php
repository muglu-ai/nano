@extends('enquiry.layout')

@section('title', 'Complete Payment')

@push('styles')
<style>
    .preview-section {
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
        color: var(--text-secondary);
    }

    .price-row.total {
        font-size: 1.5rem;
        font-weight: 700;
        padding-top: 1rem;
        margin-top: 1rem;
        border-top: 2px solid #e0e0e0;
        color: var(--text-primary);
    }

    .price-label {
        color: var(--text-secondary);
    }

    .price-value {
        color: var(--text-primary);
        font-weight: 600;
    }

    .price-row.total .price-label {
        color: var(--text-primary);
        font-weight: 700;
    }

    .price-row.total .price-value {
        color: var(--text-primary);
        font-weight: 700;
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
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .delegates-table th {
        background: var(--primary-color);
        color: white;
        font-weight: 600;
    }

    .delegates-table td {
        color: var(--text-primary);
    }

    .delegates-table tr:last-child td {
        border-bottom: none;
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

    .btn-pay-now {
        background: var(--primary-gradient);
        color: white;
        border: none;
        padding: 1rem 3rem;
        font-size: 1.25rem;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .btn-pay-now:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(11, 94, 215, 0.3);
        background: linear-gradient(135deg, var(--primary-color-dark) 0%, var(--primary-color) 100%);
    }

    .btn-pay-now:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-credit-card me-2"></i>Complete Your Payment</h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
    <!-- Progress Bar -->
    @include('tickets.public.partials.progress-bar', ['currentStep' => 3])

        @if(session('error'))
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Order Information -->
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-receipt me-2"></i>
                Order Information
            </h4>
            <div class="alert alert-info mb-3" style="background: #e7f3ff; border: 1px solid #b3d9ff; border-radius: 8px; padding: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fas fa-info-circle" style="color: var(--primary-color);"></i>
                    <div style="flex: 1;">
                        <strong style="color: var(--text-primary);">Order Number (TIN):</strong>
                        <span style="color: var(--primary-color); font-size: 1.1rem; font-weight: 700; margin-left: 0.5rem;">{{ $order->order_no }}</span>
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary);">
                        <i class="fas fa-link me-1"></i>
                        <span>Share this URL to make payment later</span>
                    </div>
                </div>
                <div style="margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1px solid #b3d9ff;">
                    <div style="font-size: 0.85rem; color: var(--text-secondary); word-break: break-all;">
                        <strong>Payment URL:</strong> 
                        <code style="background: white; padding: 0.25rem 0.5rem; border-radius: 4px; color: var(--primary-color);">
                            {{ url()->current() }}
                        </code>
                    </div>
                </div>
            </div>
            <div class="info-row">
                <span class="info-label">Order Number:</span>
                <span class="info-value"><strong>{{ $order->order_no }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Ticket Type:</span>
                <span class="info-value">{{ $ticketType->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Number of Delegates:</span>
                <span class="info-value">{{ $order->items->sum('quantity') }}</span>
            </div>
        </div>

        <!-- Organisation Information -->
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-building me-2"></i>
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
        </div>

        <!-- Delegate Details -->
        @if($order->registration->delegates && $order->registration->delegates->count() > 0)
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-users me-2"></i>
                Delegate Details
            </h4>
            <table class="delegates-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Delegate Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Designation</th>
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

        <!-- GST Information -->
        @if($order->registration->gst_required)
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                GST Information
            </h4>
            <div class="info-row">
                <span class="info-label">GSTIN:</span>
                <span class="info-value">{{ $order->registration->gstin }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">GST Legal Name:</span>
                <span class="info-value">{{ $order->registration->gst_legal_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">GST Address:</span>
                <span class="info-value">{{ $order->registration->gst_address }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">GST State:</span>
                <span class="info-value">{{ $order->registration->gst_state }}</span>
            </div>
        </div>
        @endif

        <!-- Price Breakdown -->
        @php
            $isInternational = ($order->registration->nationality === 'International' || $order->registration->nationality === 'international');
            $currencySymbol = $isInternational ? '$' : '₹';
            $priceFormat = $isInternational ? 2 : 0; // 2 decimals for USD, 0 for INR
        @endphp
        <div class="price-breakdown">
            <h4 class="section-title mb-3">
                <i class="fas fa-calculator me-2"></i>
                Price Breakdown
            </h4>
            @foreach($order->items as $item)
            <div class="price-row">
                <span class="price-label">Ticket Price ({{ $item->quantity }} × {{ $currencySymbol }}{{ number_format($item->unit_price, $priceFormat) }}):</span>
                <span class="price-value">{{ $currencySymbol }}{{ number_format($item->subtotal, $priceFormat) }}</span>
            </div>
            <div class="price-row">
                <span class="price-label">GST ({{ $item->gst_rate }}%):</span>
                <span class="price-value">{{ $currencySymbol }}{{ number_format($item->gst_amount, $priceFormat) }}</span>
            </div>
            <div class="price-row">
                <span class="price-label">Processing Charge ({{ $item->processing_charge_rate }}%):</span>
                <span class="price-value">{{ $currencySymbol }}{{ number_format($item->processing_charge_amount, $priceFormat) }}</span>
            </div>
            @endforeach
            <div class="price-row total">
                <span class="price-label">Total Amount:</span>
                <span class="price-value">{{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}</span>
            </div>
        </div>

        <!-- Pay Now Button -->
        <div class="text-center mt-4">
            <a href="{{ route('tickets.payment.process', ['eventSlug' => $event->slug ?? $event->id, 'orderNo' => $order->order_no]) }}" class="btn btn-pay-now" id="payNowBtn">
                    <i class="fas fa-credit-card me-2"></i>
                Pay Now {{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}
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

