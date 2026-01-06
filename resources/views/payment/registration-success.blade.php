@extends('layouts.app')

@section('title', 'Payment Successful - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm text-center">
                <div class="card-body py-5">
                    @if(session('success'))
                        <div class="mb-4">
                            <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
                            <h2 class="text-success mb-3">Payment Successful!</h2>
                            <p class="lead">Thank you for your payment.</p>
                        </div>

                        @if($invoice)
                        <div class="alert alert-info text-left">
                            <h6 class="alert-heading"><i class="fas fa-file-invoice"></i> Payment Details</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Invoice Number:</strong><br>
                                    {{ $invoice->invoice_no }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Amount Paid:</strong><br>
                                    <h5 class="mb-0 text-primary">
                                        {{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->amount_paid ?? $invoice->total_final_price, 2) }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="alert alert-success">
                            <i class="fas fa-envelope"></i> 
                            A payment confirmation email has been sent to your registered email address.
                        </div>

                        <div class="mt-4">
                            <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-home"></i> Return to Home
                            </a>
                        </div>
                    @else
                        <div class="mb-4">
                            <i class="fas fa-info-circle fa-5x text-info mb-3"></i>
                            <h2 class="mb-3">Payment Status</h2>
                            <p class="lead">Please check your email for payment confirmation.</p>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('registration.payment.lookup') }}" class="btn btn-primary">
                                <i class="fas fa-search"></i> Lookup Another Order
                            </a>
                            <a href="{{ url('/') }}" class="btn btn-secondary">
                                <i class="fas fa-home"></i> Return to Home
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Help Section --}}
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-question-circle"></i> Need Help?</h6>
                    <p class="card-text small">
                        If you have any questions about your payment or need assistance, please contact our support team.
                    </p>
                    <a href="mailto:support@example.com" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-envelope"></i> Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    .fa-check-circle {
        animation: scaleIn 0.5s ease-out;
    }
    @keyframes scaleIn {
        from {
            transform: scale(0);
        }
        to {
            transform: scale(1);
        }
    }
</style>
@endsection

