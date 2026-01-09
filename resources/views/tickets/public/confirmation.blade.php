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
        font-size: 5rem;
        color: #28a745;
        margin-bottom: 1.5rem;
    }

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
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-check-circle me-2"></i>Payment Successful!</h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
        <!-- Progress Bar -->
        @include('tickets.public.partials.progress-bar', ['currentStep' => 3])
        
        <div class="text-center mb-4">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <p class="lead mb-4" style="color: var(--text-primary); font-size: 1.1rem;">
                Thank you for your registration. Your order has been confirmed.
            </p>
        </div>

        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-receipt me-2"></i>Order Details</h4>
            <div class="info-row">
                <span class="info-label">Order Number:</span>
                <span class="info-value"><strong>{{ $order->order_no }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Event:</span>
                <span class="info-value">{{ $order->registration->event->event_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ticket Type:</span>
                <span class="info-value">
                    @foreach($order->items as $item)
                        {{ $item->ticketType->name }} ({{ $item->quantity }}x)
                    @endforeach
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Amount:</span>
                <span class="info-value"><strong>₹{{ number_format($order->total, 2) }}</strong></span>
            </div>
            <div class="info-row">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="badge bg-success">{{ strtoupper($order->status) }}</span>
                </span>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-user me-2"></i>Contact Information</h4>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $order->registration->contact->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $order->registration->contact->email }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $order->registration->contact->phone }}</span>
            </div>
        </div>

        <!-- Company Information -->
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-building me-2"></i>Company Information</h4>
            <div class="info-row">
                <span class="info-label">Company Name:</span>
                <span class="info-value">{{ $order->registration->company_name ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Country:</span>
                <span class="info-value">{{ $order->registration->company_country ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">State:</span>
                <span class="info-value">{{ $order->registration->company_state ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">City:</span>
                <span class="info-value">{{ $order->registration->company_city ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $order->registration->company_phone ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Industry Sector:</span>
                <span class="info-value">{{ $order->registration->industry_sector ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Organisation Type:</span>
                <span class="info-value">{{ $order->registration->organisation_type ?? 'N/A' }}</span>
            </div>
            @if($order->registration->gst_required)
                <div class="info-row">
                    <span class="info-label">GST Required:</span>
                    <span class="info-value">Yes</span>
                </div>
                <div class="info-row">
                    <span class="info-label">GSTIN:</span>
                    <span class="info-value">{{ $order->registration->gstin ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">GST Legal Name:</span>
                    <span class="info-value">{{ $order->registration->gst_legal_name ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">GST Address:</span>
                    <span class="info-value">{{ $order->registration->gst_address ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">GST State:</span>
                    <span class="info-value">{{ $order->registration->gst_state ?? 'N/A' }}</span>
                </div>
            @endif
        </div>

        <!-- Delegates Information -->
        @if($order->registration->delegates->count() > 0)
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-users me-2"></i>Delegate Information</h4>
                @foreach($order->registration->delegates as $index => $delegate)
                    <div class="mb-3" style="border-bottom: 1px solid #e0e0e0; padding-bottom: 1rem;">
                        <h6 style="color: var(--text-primary); font-weight: 600;">Delegate {{ $index + 1 }}</h6>
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value">{{ $delegate->salutation }} {{ $delegate->first_name }} {{ $delegate->last_name }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value">{{ $delegate->email }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value">{{ $delegate->phone }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Job Title:</span>
                            <span class="info-value">{{ $delegate->job_title ?? 'N/A' }}</span>
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
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-credit-card me-2"></i>Payment Gateway Details</h4>
                <div class="info-row">
                    <span class="info-label">Payment Gateway:</span>
                    <span class="info-value">
                        <strong>{{ $paymentDetails['gateway'] ?? ($primaryPayment ? ucfirst($primaryPayment->gateway_name) : 'N/A') }}</strong>
                    </span>
                </div>
                @if(isset($paymentDetails['transaction_id']) || ($primaryPayment && $primaryPayment->gateway_txn_id))
                    <div class="info-row">
                        <span class="info-label">Transaction ID:</span>
                        <span class="info-value">
                            {{ $paymentDetails['transaction_id'] ?? $primaryPayment->gateway_txn_id }}
                        </span>
                    </div>
                @endif
                @if(isset($paymentDetails['amount']))
                    <div class="info-row">
                        <span class="info-label">Amount Paid:</span>
                        <span class="info-value">
                            <strong>{{ $paymentDetails['currency'] ?? 'INR' }} {{ number_format($paymentDetails['amount'], 2) }}</strong>
                        </span>
                    </div>
                @endif
                @if($primaryPayment && $primaryPayment->paid_at)
                    <div class="info-row">
                        <span class="info-label">Payment Date:</span>
                        <span class="info-value">
                            {{ $primaryPayment->paid_at->format('d M Y, h:i A') }}
                        </span>
                    </div>
                @endif
            </div>
        @elseif($order->primaryPayment())
            @php $primaryPayment = $order->primaryPayment(); @endphp
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-credit-card me-2"></i>Payment Gateway Details</h4>
                <div class="info-row">
                    <span class="info-label">Payment Gateway:</span>
                    <span class="info-value">
                        <strong>{{ ucfirst($primaryPayment->gateway_name) }}</strong>
                    </span>
                </div>
                @if($primaryPayment->gateway_txn_id)
                    <div class="info-row">
                        <span class="info-label">Transaction ID:</span>
                        <span class="info-value">
                            {{ $primaryPayment->gateway_txn_id }}
                        </span>
                    </div>
                @endif
                @if($primaryPayment->paid_at)
                    <div class="info-row">
                        <span class="info-label">Payment Date:</span>
                        <span class="info-value">
                            {{ $primaryPayment->paid_at->format('d M Y, h:i A') }}
                        </span>
                    </div>
                @endif
            </div>
        @endif

        <div class="mt-4 text-center">
            <div class="alert alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; border-radius: 8px; padding: 1rem;">
                <p style="color: var(--text-primary); margin-bottom: 0.5rem;">
                    <i class="fas fa-envelope me-2"></i>
                    A payment acknowledgement email has been sent to <strong>{{ $order->registration->contact->email }}</strong>
                </p>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0;">
                    Please check your email for the receipt and further instructions.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

