@extends('layouts.poster-registration')

@section('title', 'Payment - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/custom.css') }}">
<style>
    .preview-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .preview-section {
        background: #ffffff;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-primary);
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary-color);
        font-size: 1rem;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table td {
        padding: 0.6rem 0.75rem;
        border: 1px solid #e9ecef;
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .info-table .label-cell {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
        width: 40%;
    }

    .info-table .value-cell {
        color: #212529;
        width: 60%;
    }

    .price-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1.25rem;
        margin-top: 1.5rem;
        border: 1px solid #dee2e6;
    }

    .price-table {
        width: 100%;
        border-collapse: collapse;
    }

    .price-table td {
        padding: 0.65rem 0.85rem;
        border: 1px solid #e9ecef;
        font-size: 0.9rem;
    }

    .price-table .label-cell {
        background: #ffffff;
        font-weight: 500;
        color: #495057;
        width: 65%;
    }

    .price-table .value-cell {
        background: #ffffff;
        text-align: right;
        font-weight: 600;
        color: #212529;
        width: 35%;
    }

    .price-table .total-row td {
        background: var(--primary-color);
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 700;
        padding: 0.85rem;
    }

    .payment-method-card {
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-method-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 12px rgba(11, 94, 215, 0.2);
    }

    .payment-method-card.selected {
        border-color: var(--primary-color);
        background: #f0f5ff;
    }

    .payment-logo {
        max-height: 50px;
        max-width: 150px;
    }

    .form-container {padding: 1rem 0px;}
</style>
@endpush

@section('poster-content')
<div class="container py-3">
    {{-- Step Indicator --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="step-indicator">
                <div class="step-item completed">
                    <div class="step-number">1</div>
                    <div class="step-label">Registration Details</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item completed">
                    <div class="step-number">2</div>
                    <div class="step-label">Preview Details</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item active">
                    <div class="step-number">3</div>
                    <div class="step-label">Payment</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">Complete Your Payment</h2>

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Registration Summary --}}
            <div class="alert alert-info mb-4">
                <h5 class="mb-2"><i class="fas fa-info-circle"></i> Registration Information</h5>
                <p class="mb-0">
                    <strong>TIN No:</strong> {{ $poster->tin_no ?? 'N/A' }}<br>
                    <strong>Abstract Title:</strong> {{ $poster->abstract_title ?? 'N/A' }}<br>
                    <strong>Lead Author:</strong> 
                    @if(isset($poster->authors) && is_array($poster->authors))
                        @php
                            $leadAuthor = collect($poster->authors)->firstWhere('is_lead', true);
                        @endphp
                        @if($leadAuthor)
                            {{ $leadAuthor['first_name'] ?? '' }} {{ $leadAuthor['last_name'] ?? '' }}
                        @else
                            N/A
                        @endif
                    @else
                        N/A
                    @endif
                </p>
            </div>

            {{-- Registration Details --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-file-alt"></i>
                    Registration Details
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Sector</td>
                        <td class="value-cell">{{ $poster->sector ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Poster Category</td>
                        <td class="value-cell">{{ $poster->poster_category ?? 'Breaking Boundaries' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Abstract Title</td>
                        <td class="value-cell"><strong>{{ $poster->abstract_title ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Presentation Mode</td>
                        <td class="value-cell">{{ $poster->presentation_mode ?? 'Poster only' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Attendees Summary --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-users"></i>
                    Attendees
                </h4>
                @php
                    $attendees = [];
                    if(isset($poster->authors) && is_array($poster->authors)) {
                        foreach($poster->authors as $author) {
                            if(isset($author['will_attend']) && $author['will_attend']) {
                                $attendees[] = ($author['first_name'] ?? '') . ' ' . ($author['last_name'] ?? '');
                            }
                        }
                    }
                @endphp
                
                @if(count($attendees) > 0)
                    <ul class="mb-0">
                        @foreach($attendees as $attendee)
                            <li>{{ $attendee }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">No attendees registered for the event.</p>
                @endif
            </div>

            {{-- Payment Summary --}}
            <div class="price-section">
                <h4 class="section-title">
                    <i class="fas fa-calculator"></i>
                    Payment Summary
                </h4>
                
                @php
                    $currency = $poster->currency ?? 'INR';
                    $attendeeCount = count($attendees);
                    $pricePerAttendee = $currency === 'INR' ? 2000 : 25;
                    $subtotal = $attendeeCount * $pricePerAttendee;
                    $gst = $currency === 'INR' ? $subtotal * 0.18 : 0;
                    $total = $subtotal + $gst;
                    $currencySymbol = $currency === 'INR' ? '₹' : '$';
                @endphp
                
                <table class="price-table">
                    <tr>
                        <td class="label-cell">Number of Attendees</td>
                        <td class="value-cell">{{ $attendeeCount }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Registration Fee ({{ $currencySymbol }}{{ number_format($pricePerAttendee) }} × {{ $attendeeCount }})</td>
                        <td class="value-cell">{{ $currencySymbol }} {{ number_format($subtotal, 2) }}</td>
                    </tr>
                    @if($gst > 0)
                    <tr>
                        <td class="label-cell">GST (18%)</td>
                        <td class="value-cell">{{ $currencySymbol }} {{ number_format($gst, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td>Total Amount Payable</td>
                        <td>{{ $currencySymbol }} {{ number_format($total, 2) }}</td>
                    </tr>
                </table>
            </div>

            {{-- Payment Method Selection --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-credit-card"></i>
                    Select Payment Method
                </h4>

                @if($currency === 'INR')
                    {{-- CCAvenue for Indian Payments --}}
                    <div class="payment-method-card selected" id="ccavenue-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-2">CCAvenue</h5>
                                <p class="mb-0 text-muted">Credit Card, Debit Card, Net Banking, UPI, Wallets</p>
                            </div>
                            <img src="{{ asset('asset/img/ccavenue-logo.png') }}" alt="CCAvenue" class="payment-logo">
                        </div>
                    </div>

                    <form action="{{ route('poster.payment.callback', ['gateway' => 'ccavenue']) }}" method="POST" id="paymentForm">
                        @csrf
                        <input type="hidden" name="tin_no" value="{{ $poster->tin_no }}">
                        <input type="hidden" name="amount" value="{{ $total }}">
                        <input type="hidden" name="currency" value="{{ $currency }}">
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="payBtn">
                                <i class="fas fa-lock"></i> Proceed to Secure Payment
                            </button>
                        </div>
                    </form>
                @else
                    {{-- PayPal for International Payments --}}
                    <div class="payment-method-card selected" id="paypal-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-2">PayPal</h5>
                                <p class="mb-0 text-muted">Credit Card, Debit Card, PayPal Balance</p>
                            </div>
                            <img src="{{ asset('asset/img/paypal-logo.png') }}" alt="PayPal" class="payment-logo">
                        </div>
                    </div>

                    <form action="{{ route('poster.payment.callback', ['gateway' => 'paypal']) }}" method="POST" id="paymentForm">
                        @csrf
                        <input type="hidden" name="tin_no" value="{{ $poster->tin_no }}">
                        <input type="hidden" name="amount" value="{{ $total }}">
                        <input type="hidden" name="currency" value="{{ $currency }}">
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="payBtn">
                                <i class="fas fa-lock"></i> Proceed to PayPal
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Security Notice --}}
            <div class="alert alert-info mt-4">
                <i class="fas fa-shield-alt"></i>
                <strong>Secure Payment:</strong> Your payment information is processed securely. We do not store your credit card details.
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('payBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirecting to Payment Gateway...';
});
</script>
@endpush
@endsection
