@extends('tickets.public.layout')

@section('title', 'Complete Payment')

@push('styles')
<style>
    .payment-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .payment-container .registration-progress {
        margin-bottom: 2rem;
    }

    .payment-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 2.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 2rem;
    }

    .payment-section {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #fff;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(102, 126, 234, 0.5);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: rgba(255, 255, 255, 0.7);
        flex: 1;
    }

    .info-value {
        color: #fff;
        flex: 1;
        text-align: right;
    }

    .price-breakdown {
        background: rgba(102, 126, 234, 0.1);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        font-size: 1rem;
    }

    .price-row.total {
        font-size: 1.5rem;
        font-weight: 700;
        padding-top: 1rem;
        margin-top: 1rem;
        border-top: 2px solid rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .price-label {
        color: rgba(255, 255, 255, 0.8);
    }

    .price-value {
        color: #fff;
        font-weight: 600;
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
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .delegates-table th {
        background: rgba(102, 126, 234, 0.2);
        color: #fff;
        font-weight: 600;
    }

    .delegates-table td {
        color: rgba(255, 255, 255, 0.9);
    }

    .delegates-table tr:last-child td {
        border-bottom: none;
    }

    .btn-pay-now {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 1rem 3rem;
        font-size: 1.25rem;
        font-weight: 600;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .btn-pay-now:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
    }

    .btn-pay-now:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }
</style>
@endpush

@section('content')
<div class="payment-container">
    <!-- Progress Bar -->
    @include('tickets.public.partials.progress-bar', ['currentStep' => 3])
    
    <div class="payment-card">
        <h2 class="text-center mb-4" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            Complete Your Payment
        </h2>

        @if(session('error'))
            <div class="alert alert-danger mb-4">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Order Information -->
        <div class="payment-section">
            <h4 class="section-title">
                <i class="fas fa-receipt me-2"></i>
                Order Information
            </h4>
            <div class="info-row">
                <span class="info-label">Order Number:</span>
                <span class="info-value"><strong>{{ $order->order_no }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Registration Category:</span>
                <span class="info-value">{{ $registrationCategory->name ?? 'N/A' }}</span>
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
        <div class="payment-section">
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
        <div class="payment-section">
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

        <!-- GST Information -->
        @if($order->registration->gst_required)
        <div class="payment-section">
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
        <div class="price-breakdown">
            <h4 class="section-title mb-3">
                <i class="fas fa-calculator me-2"></i>
                Price Breakdown
            </h4>
            @foreach($order->items as $item)
            <div class="price-row">
                <span class="price-label">Ticket Price ({{ $item->quantity }} × ₹{{ number_format($item->unit_price, 2) }}):</span>
                <span class="price-value">₹{{ number_format($item->subtotal, 2) }}</span>
            </div>
            <div class="price-row">
                <span class="price-label">GST ({{ $item->gst_rate }}%):</span>
                <span class="price-value">₹{{ number_format($item->gst_amount, 2) }}</span>
            </div>
            <div class="price-row">
                <span class="price-label">Processing Charge ({{ $item->processing_charge_rate }}%):</span>
                <span class="price-value">₹{{ number_format($item->processing_charge_amount, 2) }}</span>
            </div>
            @endforeach
            <div class="price-row total">
                <span class="price-label">Total Amount:</span>
                <span class="price-value">₹{{ number_format($order->total, 2) }}</span>
            </div>
        </div>

        <!-- Pay Now Button -->
        <div class="text-center mt-4">
            <form action="{{ route('tickets.payment.process', $order->id) }}" method="POST" id="paymentForm">
                @csrf
                <button type="submit" class="btn btn-pay-now" id="payNowBtn">
                    <i class="fas fa-credit-card me-2"></i>
                    Pay Now ₹{{ number_format($order->total, 2) }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('payNowBtn');
        const originalBtnText = submitBtn.innerHTML;
        
        // Disable button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        
        Swal.fire({
            title: 'Redirecting to Payment Gateway',
            text: 'Please wait while we redirect you to the secure payment page.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit the form
        form.submit();
    });
</script>
@endpush

