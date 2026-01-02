@extends('tickets.public.layout')

@section('title', 'Payment')

@push('styles')
<style>
    .payment-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .payment-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 2.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .order-summary {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .summary-row:last-child {
        border-bottom: none;
        font-size: 1.25rem;
        font-weight: 700;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 2px solid rgba(255, 255, 255, 0.2);
    }
</style>
@endpush

@section('content')
<div class="payment-container">
    <div class="payment-card">
        <h2 class="text-center mb-4" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            Complete Payment
        </h2>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="order-summary">
            <h5 class="mb-3"><i class="fas fa-receipt me-2"></i>Order Summary</h5>
            <div class="summary-row">
                <span>Order Number:</span>
                <strong>{{ $order->order_no }}</strong>
            </div>
            <div class="summary-row">
                <span>Total Amount:</span>
                <strong>₹{{ number_format($order->total, 2) }}</strong>
            </div>
        </div>

        <div class="text-center">
            <form action="{{ route('tickets.payment.process', $order->id) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-credit-card me-2"></i>
                    Retry Payment
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

