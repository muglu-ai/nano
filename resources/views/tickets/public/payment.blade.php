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
                    
                </div>
                
            </div>
            <div class="info-row">
                <span class="info-label">Order Number:</span>
                <span class="info-value"><strong>{{ $order->order_no }}</strong></span>
            </div>
            @if($ticketType && $ticketType->category)
            <div class="info-row">
                <span class="info-label">Category:</span>
                <span class="info-value">{{ $ticketType->category->name }}</span>
            </div>
            @endif
            @if($ticketType && $ticketType->subcategory)
            <div class="info-row">
                <span class="info-label">Subcategory:</span>
                <span class="info-value">{{ $ticketType->subcategory->name }}</span>
            </div>
            @endif
            @if(isset($order->registration->registration_type))
            <div class="info-row">
                <span class="info-label">Registration Type:</span>
                <span class="info-value"><strong>{{ $order->registration->registration_type }}</strong></span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Number of Delegates:</span>
                <span class="info-value">{{ $order->items->sum('quantity') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Currency:</span>
                <span class="info-value">{{ ($order->registration->nationality === 'International' || $order->registration->nationality === 'international') ? 'USD ($)' : 'INR (₹)' }}</span>
            </div>
        </div>

        <!-- Organisation/Individual Information -->
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas {{ ($order->registration->registration_type ?? 'Organisation') === 'Individual' ? 'fa-user' : 'fa-building' }} me-2"></i>
                {{ ($order->registration->registration_type ?? 'Organisation') === 'Individual' ? 'Individual' : 'Organisation' }} Information
            </h4>
            @if(($order->registration->registration_type ?? 'Organisation') === 'Organisation')
            <div class="info-row">
                <span class="info-label">Organisation Name:</span>
                <span class="info-value">{{ $order->registration->company_name ?? 'N/A' }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Industry Sector:</span>
                <span class="info-value">{{ $order->registration->industry_sector ?? 'N/A' }}</span>
            </div>
            @if(($order->registration->registration_type ?? 'Organisation') === 'Organisation')
            <div class="info-row">
                <span class="info-label">Organisation Type:</span>
                <span class="info-value">{{ $order->registration->organisation_type ?? 'N/A' }}</span>
            </div>
            @endif
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
        @php
            $paymentDelegateTicketType = $order->items->first()->ticketType ?? null;
            $hasLinkedIn = $order->registration->delegates->contains(function($delegate) {
                return !empty($delegate->linkedin_profile);
            });
        @endphp
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
                        <th>Category</th>
                        <th>Subcategory</th>
                        @if($hasLinkedIn)
                        <th>LinkedIn Profile</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->registration->delegates as $delegate)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $delegate->salutation }} {{ $delegate->first_name }} {{ $delegate->last_name }}</td>
                        <td>{{ $delegate->email }}</td>
                        <td>{{ $delegate->phone ?? '-' }}</td>
                        <td>{{ $paymentDelegateTicketType?->category?->name ?? '-' }}</td>
                        <td>{{ $paymentDelegateTicketType?->subcategory?->name ?? '-' }}</td>
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
            @if($order->group_discount_applied && $order->group_discount_amount > 0)
            <div class="price-row" style="background-color: #e7f3ff;">
                <span class="price-label" style="color: #004085;">
                    <i class="fas fa-users me-1"></i>
                    Group Discount ({{ number_format($order->group_discount_rate, 0) }}%):
                </span>
                <span class="price-value" style="color: #004085;">-{{ $currencySymbol }}{{ number_format($order->group_discount_amount, $priceFormat) }}</span>
            </div>
            @endif
            @if($order->discount_amount > 0 && $order->promoCode)
            <div class="price-row" style="background-color: #d4edda;">
                <span class="price-label" style="color: #155724;">
                    <i class="fas fa-tag me-1"></i>
                    Promocode Discount:
                </span>
                <span class="price-value" style="color: #155724;">-{{ $currencySymbol }}{{ number_format($order->discount_amount, $priceFormat) }}</span>
            </div>
            @endif
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

        <!-- Pay Now Button: redirect to lookup page with TIN so user can complete payment from there -->
        <div class="text-center mt-4">
            <a href="{{ route('tickets.payment.lookup', ['eventSlug' => $event->slug ?? $event->id]) }}?tin={{ urlencode($order->order_no) }}" class="btn btn-pay-now" id="payNowBtn">
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
        const paymentUrl = btn.href;
        
        // Disable button
        btn.style.pointerEvents = 'none';
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        
        Swal.fire({
            title: 'Redirecting to Payment',
            text: 'Taking you to the payment page...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Redirect to lookup page with TIN; that page will show order details and Pay Now for gateway
        window.location.href = paymentUrl;
    });
</script>
@endpush

