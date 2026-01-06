@extends('layouts.app')

@section('title', 'Select Payment Method - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Select Payment Method</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif

                    {{-- Order Summary --}}
                    <div class="alert alert-info">
                        <h6 class="alert-heading mb-2"><i class="fas fa-file-invoice"></i> Order Summary</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Invoice Number:</strong> {{ $invoice->invoice_no }}<br>
                                @if($application)
                                <strong>Application ID:</strong> {{ $application->application_id }}<br>
                                <strong>Company:</strong> {{ $application->company_name }}
                                @endif
                            </div>
                            <div class="col-md-6 text-md-end">
                                <strong>Amount Due:</strong><br>
                                <h4 class="mb-0 text-primary">
                                    {{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->total_final_price, 2) }}
                                </h4>
                            </div>
                        </div>
                    </div>

                    {{-- Billing Information --}}
                    @if($billingDetail)
                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                        <div class="card-header bg-light">
                            <h6 class="mb-0"><i class="fas fa-user"></i> Billing Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Name:</strong> {{ $billingDetail->contact_name ?? 'N/A' }}
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Email:</strong> {{ $billingDetail->email ?? 'N/A' }}
                                </div>
                                <div class="col-md-12 mb-2">
                                    <strong>Address:</strong> {{ $billingDetail->address ?? 'N/A' }}
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>City:</strong> {{ $billingDetail->city_name ?? 'N/A' }}
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>State:</strong> {{ $billingDetail->state->name ?? 'N/A' }}
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>Country:</strong> {{ $billingDetail->country->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Payment Method Selection --}}
                    <form method="POST" action="{{ route('registration.payment.process', $invoice->invoice_no) }}" id="paymentForm">
                        @csrf

                        <h6 class="mb-3"><i class="fas fa-wallet"></i> Choose Payment Gateway</h6>

                        <div class="row">
                            {{-- CCAvenue Option --}}
                            <div class="col-md-6 mb-3">
                                <div class="card payment-option" data-gateway="CCAvenue" style="cursor: pointer; border: 2px solid #dee2e6; transition: all 0.3s;">
                                    <div class="card-body text-center">
                                        <input type="radio" name="payment_method" value="CCAvenue" id="ccavenue" class="d-none" checked>
                                        <label for="ccavenue" class="w-100" style="cursor: pointer;">
                                            <i class="fas fa-university fa-3x text-primary mb-3"></i>
                                            <h5>CCAvenue</h5>
                                            <p class="text-muted small mb-0">
                                                Credit/Debit Cards, Net Banking, UPI, Wallets
                                            </p>
                                            <p class="text-success small mt-2 mb-0">
                                                <i class="fas fa-check-circle"></i> Recommended for INR payments
                                            </p>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            {{-- PayPal Option --}}
                            <div class="col-md-6 mb-3">
                                <div class="card payment-option" data-gateway="PayPal" style="cursor: pointer; border: 2px solid #dee2e6; transition: all 0.3s;">
                                    <div class="card-body text-center">
                                        <input type="radio" name="payment_method" value="PayPal" id="paypal" class="d-none">
                                        <label for="paypal" class="w-100" style="cursor: pointer;">
                                            <i class="fab fa-paypal fa-3x text-primary mb-3"></i>
                                            <h5>PayPal</h5>
                                            <p class="text-muted small mb-0">
                                                PayPal Account or Credit Card
                                            </p>
                                            <p class="text-success small mt-2 mb-0">
                                                <i class="fas fa-check-circle"></i> Recommended for USD payments
                                            </p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('registration.payment.lookup') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-lock"></i> Proceed to Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Security Notice --}}
            <div class="alert alert-warning">
                <i class="fas fa-shield-alt"></i> <strong>Secure Payment:</strong> 
                Your payment information is encrypted and secure. We do not store your card details.
            </div>
        </div>
    </div>
</div>

<style>
    .payment-option {
        transition: all 0.3s ease;
    }
    .payment-option:hover {
        border-color: #0d6efd !important;
        transform: translateY(-5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .payment-option input:checked + label {
        color: #0d6efd;
    }
    .payment-option input:checked ~ .card {
        border-color: #0d6efd !important;
    }
    input[type="radio"]:checked ~ .card {
        border-color: #0d6efd !important;
        background-color: #f0f7ff;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.querySelectorAll('.payment-option');
    const radioInputs = document.querySelectorAll('input[name="payment_method"]');
    
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            const gateway = this.dataset.gateway;
            const radio = document.getElementById(gateway.toLowerCase());
            if (radio) {
                radio.checked = true;
                updateSelection();
            }
        });
    });

    radioInputs.forEach(radio => {
        radio.addEventListener('change', updateSelection);
    });

    function updateSelection() {
        paymentOptions.forEach(option => {
            option.style.borderColor = '#dee2e6';
            option.style.backgroundColor = '';
        });
        
        const checked = document.querySelector('input[name="payment_method"]:checked');
        if (checked) {
            const selectedOption = document.querySelector(`[data-gateway="${checked.value}"]`);
            if (selectedOption) {
                selectedOption.style.borderColor = '#0d6efd';
                selectedOption.style.backgroundColor = '#f0f7ff';
            }
        }
    }

    // Initialize selection
    updateSelection();

    // Form submission
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    });
});
</script>
@endsection

