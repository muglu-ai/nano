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

    .confirmation-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 2.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: center;
    }

    .success-icon {
        font-size: 5rem;
        color: #28a745;
        margin-bottom: 1.5rem;
    }

    .order-details {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        padding: 1.5rem;
        margin: 2rem 0;
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-align: left;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-weight: 600;
        color: rgba(255, 255, 255, 0.7);
    }

    .detail-value {
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="confirmation-container">
    <!-- Progress Bar -->
    @include('tickets.public.partials.progress-bar', ['currentStep' => 3])
    
    <div class="confirmation-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h2 class="mb-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            Payment Successful!
        </h2>
        
        <p class="lead mb-4" style="color: rgba(255, 255, 255, 0.8);">
            Thank you for your registration. Your order has been confirmed.
        </p>

        <div class="order-details">
            <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Details</h5>
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value"><strong>{{ $order->order_no }}</strong></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Event:</span>
                <span class="detail-value">{{ $order->registration->event->event_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Ticket Type:</span>
                <span class="detail-value">
                    @foreach($order->items as $item)
                        {{ $item->ticketType->name }} ({{ $item->quantity }}x)
                    @endforeach
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount:</span>
                <span class="detail-value"><strong>₹{{ number_format($order->total, 2) }}</strong></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="badge bg-success">{{ strtoupper($order->status) }}</span>
                </span>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="order-details">
            <h5 class="mb-3"><i class="fas fa-user me-2"></i>Contact Information</h5>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $order->registration->contact->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $order->registration->contact->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $order->registration->contact->phone }}</span>
            </div>
        </div>

        <!-- Company Information -->
        <div class="order-details">
            <h5 class="mb-3"><i class="fas fa-building me-2"></i>Company Information</h5>
            <div class="detail-row">
                <span class="detail-label">Company Name:</span>
                <span class="detail-value">{{ $order->registration->company_name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Country:</span>
                <span class="detail-value">{{ $order->registration->company_country ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">State:</span>
                <span class="detail-value">{{ $order->registration->company_state ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">City:</span>
                <span class="detail-value">{{ $order->registration->company_city ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $order->registration->company_phone ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Industry Sector:</span>
                <span class="detail-value">{{ $order->registration->industry_sector ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Organisation Type:</span>
                <span class="detail-value">{{ $order->registration->organisation_type ?? 'N/A' }}</span>
            </div>
            @if($order->registration->gst_required)
                <div class="detail-row">
                    <span class="detail-label">GST Required:</span>
                    <span class="detail-value">Yes</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">GSTIN:</span>
                    <span class="detail-value">{{ $order->registration->gstin ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">GST Legal Name:</span>
                    <span class="detail-value">{{ $order->registration->gst_legal_name ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">GST Address:</span>
                    <span class="detail-value">{{ $order->registration->gst_address ?? 'N/A' }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">GST State:</span>
                    <span class="detail-value">{{ $order->registration->gst_state ?? 'N/A' }}</span>
                </div>
            @endif
        </div>

        <!-- Delegates Information -->
        @if($order->registration->delegates->count() > 0)
            <div class="order-details">
                <h5 class="mb-3"><i class="fas fa-users me-2"></i>Delegate Information</h5>
                @foreach($order->registration->delegates as $index => $delegate)
                    <div class="mb-3" style="border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 1rem;">
                        <h6>Delegate {{ $index + 1 }}</h6>
                        <div class="detail-row">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value">{{ $delegate->salutation }} {{ $delegate->first_name }} {{ $delegate->last_name }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Email:</span>
                            <span class="detail-value">{{ $delegate->email }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Phone:</span>
                            <span class="detail-value">{{ $delegate->phone }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Job Title:</span>
                            <span class="detail-value">{{ $delegate->job_title ?? 'N/A' }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if(session('payment_details'))
            @php
                $paymentDetails = session('payment_details');
                $primaryPayment = $order->primaryPayment();
            @endphp
            <div class="order-details mt-3">
                <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Gateway Details</h5>
                <div class="detail-row">
                    <span class="detail-label">Payment Gateway:</span>
                    <span class="detail-value">
                        <strong>{{ $paymentDetails['gateway'] ?? ($primaryPayment ? ucfirst($primaryPayment->gateway_name) : 'N/A') }}</strong>
                    </span>
                </div>
                @if(isset($paymentDetails['transaction_id']) || ($primaryPayment && $primaryPayment->gateway_txn_id))
                    <div class="detail-row">
                        <span class="detail-label">Transaction ID:</span>
                        <span class="detail-value">
                            {{ $paymentDetails['transaction_id'] ?? $primaryPayment->gateway_txn_id }}
                        </span>
                    </div>
                @endif
                @if(isset($paymentDetails['amount']))
                    <div class="detail-row">
                        <span class="detail-label">Amount Paid:</span>
                        <span class="detail-value">
                            <strong>{{ $paymentDetails['currency'] ?? 'INR' }} {{ number_format($paymentDetails['amount'], 2) }}</strong>
                        </span>
                    </div>
                @endif
                @if($primaryPayment && $primaryPayment->paid_at)
                    <div class="detail-row">
                        <span class="detail-label">Payment Date:</span>
                        <span class="detail-value">
                            {{ $primaryPayment->paid_at->format('d M Y, h:i A') }}
                        </span>
                    </div>
                @endif
            </div>
        @elseif($order->primaryPayment())
            @php $primaryPayment = $order->primaryPayment(); @endphp
            <div class="order-details mt-3">
                <h5 class="mb-3"><i class="fas fa-credit-card me-2"></i>Payment Gateway Details</h5>
                <div class="detail-row">
                    <span class="detail-label">Payment Gateway:</span>
                    <span class="detail-value">
                        <strong>{{ ucfirst($primaryPayment->gateway_name) }}</strong>
                    </span>
                </div>
                @if($primaryPayment->gateway_txn_id)
                    <div class="detail-row">
                        <span class="detail-label">Transaction ID:</span>
                        <span class="detail-value">
                            {{ $primaryPayment->gateway_txn_id }}
                        </span>
                    </div>
                @endif
                @if($primaryPayment->paid_at)
                    <div class="detail-row">
                        <span class="detail-label">Payment Date:</span>
                        <span class="detail-value">
                            {{ $primaryPayment->paid_at->format('d M Y, h:i A') }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-4">
            <p style="color: rgba(255, 255, 255, 0.8);">
                A payment acknowledgement email has been sent to <strong>{{ $order->registration->contact->email }}</strong>
            </p>
            <p style="color: rgba(255, 255, 255, 0.6); font-size: 0.9rem;">
                Please check your email for the receipt and further instructions.
            </p>
        </div>

       
    </div>
</div>
@endsection

