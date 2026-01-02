@extends('tickets.public.layout')

@section('title', 'Payment Confirmation')

@push('styles')
<style>
    .confirmation-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
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

        <div class="mt-4">
            <p style="color: rgba(255, 255, 255, 0.8);">
                A confirmation email has been sent to <strong>{{ $order->registration->contact->email }}</strong>
            </p>
            <p style="color: rgba(255, 255, 255, 0.6); font-size: 0.9rem;">
                Please check your email for the receipt and further instructions.
            </p>
        </div>

        <div class="mt-4">
            <a href="{{ route('tickets.discover', $event->slug ?? $event->id) }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>
                Back to Event Page
            </a>
        </div>
    </div>
</div>
@endsection

