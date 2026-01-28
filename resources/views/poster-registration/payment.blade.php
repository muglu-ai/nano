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

            {{-- Registration Details --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Registration Details
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">TIN No</td>
                        <td class="value-cell">{{ $poster->tin_no ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Presentation</td>
                        <td class="value-cell">{{ $poster->presentation_mode ?? 'Poster' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Sector</td>
                        <td class="value-cell">{{ $poster->sector ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Currency</td>
                        <td class="value-cell">{{ $poster->currency ?? 'INR' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Abstract/Poster Details --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-file-alt"></i>
                    Abstract / Poster Details
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Poster Category</td>
                        <td class="value-cell">{{ $poster->poster_category ?? 'Breaking Boundaries' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Abstract Title</td>
                        <td class="value-cell"><strong>{{ $poster->abstract_title ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Abstract</td>
                        <td class="value-cell">{{ $poster->abstract ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Extended Abstract</td>
                        <td class="value-cell">
                            @if($poster->extended_abstract_path)
                                <a href="{{ asset('storage/' . $poster->extended_abstract_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Download Extended Abstract
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell">Lead Author CV</td>
                        <td class="value-cell">
                            @php
                                $leadAuthor = $authors->where('is_lead_author', true)->first();
                            @endphp
                            @if($leadAuthor && $leadAuthor->cv_path)
                                <a href="{{ asset('storage/' . $leadAuthor->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i> Download CV
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Authors --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-users"></i>
                    Authors ({{ $authors->count() }})
                </h4>
                
                @if($authors->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead style="background: #f8f9fa;">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 15%;">Name</th>
                                    <th style="width: 10%;">Designation</th>
                                    <th style="width: 15%;">Email</th>
                                    <th style="width: 10%;">Mobile</th>
                                    <th style="width: 15%;">Address</th>
                                    <th style="width: 15%;">Institute / Organization</th>
                                    <th style="width: 15%;">Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($authors as $index => $author)
                                    <tr class="{{ $author->is_lead_author ? 'table-primary' : '' }}">
                                        <td class="text-center"><strong>{{ $index + 1 }}</strong></td>
                                        <td>{{ $author->title ?? '' }} {{ $author->first_name }} {{ $author->last_name }}</td>
                                        <td>{{ $author->designation ?? 'N/A' }}</td>
                                        <td><small>{{ $author->email }}</small></td>
                                        <td style="white-space: nowrap;"><small>{{ $author->mobile }}</small></td>
                                        <td><small>{{ $author->city }}, {{ $author->state->name ?? 'N/A' }}, {{ $author->country->name ?? 'N/A' }} - {{ $author->postal_code }}</small></td>
                                        <td><small>{{ $author->institution }}, {{ $author->affiliation_city }}, {{ $author->affiliationCountry->name ?? 'N/A' }}</small></td>
                                        <td>
                                            @if($author->is_lead_author)
                                                <span class="badge bg-primary">Lead</span><br>
                                            @endif
                                            @if($author->is_presenter)
                                                <span class="badge bg-success">Presenter</span><br>
                                            @endif
                                            @if($author->will_attend)
                                                <span class="badge bg-info">Attending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No authors found.</p>
                @endif
            </div>

            {{-- Attendees Summary --}}
            {{-- <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-users"></i>
                    Attendees
                </h4>
                @php
                    $attendeesList = $authors->where('will_attend', true);
                @endphp
                
                @if($attendeesList->count() > 0)
                    <ul class="mb-0">
                        @foreach($attendeesList as $attendee)
                            <li>{{ $attendee->first_name }} {{ $attendee->last_name }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted mb-0">No attendees registered for the event.</p>
                @endif
            </div> --}}

            {{-- Payment Summary --}}
            <div class="price-section">
                <h4 class="section-title">
                    <i class="fas fa-calculator"></i>
                    Price Calculation
                </h4>
                
                @php
                    $attendeesList = $authors->where('will_attend', true);
                    $currency = $poster->currency ?? 'INR';
                    $attendeeCount = $attendeesList->count();
                    $currencySymbol = $currency === 'INR' ? '₹' : '$';
                    $pricePerAttendee = $currency === 'INR' ? 2000 : 25;
                    $gstRate = config('constants.GST_RATE', 18);
                    $processingRate = $invoice->processing_chargesRate ?? ($currency === 'INR' 
                        ? config('constants.IND_PROCESSING_CHARGE', 3) 
                        : config('constants.INT_PROCESSING_CHARGE', 9));
                    
                    // Recalculate amounts (in case old data has incorrect calculations)
                    $baseAmount = $poster->base_amount;
                    $gstAmount = ($baseAmount * $gstRate) / 100;
                    $processingFee = ($baseAmount * $processingRate) / 100;
                    $totalAmount = $baseAmount + $gstAmount + $processingFee;
                @endphp
                
                <div class="mb-3">
                    <strong>Attendees ({{ $attendeeCount }}):</strong>
                    @if($attendeesList->count() > 0)
                        <ol class="mb-0 mt-2">
                            @foreach($attendeesList as $attendee)
                                <li>{{ $attendee->title ?? '' }} {{ $attendee->first_name }} {{ $attendee->last_name }}</li>
                            @endforeach
                        </ol>
                    @else
                        <p class="text-muted mb-0">No attendees marked.</p>
                    @endif
                </div>
                
                <table class="price-table">
                    <tr>
                        <td class="label-cell">Base Amount</td>
                        <td class="value-cell">{{ $currencySymbol }} {{ number_format($invoice->price ?? $poster->base_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">GST ({{ $gstRate }}%)</td>
                        <td class="value-cell">{{ $currencySymbol }} {{ number_format($invoice->gst ?? $poster->gst_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Processing Charges ({{ $processingRate }}%)</td>
                        <td class="value-cell">{{ $currencySymbol }} {{ number_format($invoice->processing_charges ?? $poster->processing_fee, 2) }}</td>
                    </tr>
                    
                    <tr class="total-row">
                        <td>Total Amount Payable</td>
                        <td>{{ $currencySymbol }} {{ number_format($invoice->total_final_price ?? $poster->total_amount, 2) }}</td>
                    </tr>
                </table>
            </div>

            {{-- Payment Method Selection --}}
            <div class="preview-section">
                

                @if($currency === 'INR')
                    {{-- CCAvenue for Indian Payments --}}
                    {{-- <div class="payment-method-card selected" id="ccavenue-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-2">CCAvenue</h5>
                                <p class="mb-0 text-muted">Credit Card, Debit Card, Net Banking, UPI, Wallets</p>
                            </div>
                            <img src="{{ asset('asset/img/ccavenue-logo.png') }}" alt="CCAvenue" class="payment-logo">
                        </div>
                    </div> --}}

                    <form action="{{ route('poster.register.processPayment', ['tin_no' => $poster->tin_no]) }}" method="POST" id="paymentForm">
                        @csrf
                        <input type="hidden" name="payment_method" value="CCAvenue">
                        
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg" id="payBtn">
                                <i class="fas fa-lock"></i> Make Payment
                            </button>
                        </div>
                    </form>
                @else
                    {{-- PayPal for International Payments --}}
                    {{-- <div class="payment-method-card selected" id="paypal-card">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-2">PayPal</h5>
                                <p class="mb-0 text-muted">Credit Card, Debit Card, PayPal Balance</p>
                            </div>
                            <img src="{{ asset('asset/img/paypal-logo.png') }}" alt="PayPal" class="payment-logo">
                        </div>
                    </div> --}}

                    <form action="{{ route('poster.register.processPayment', ['tin_no' => $poster->tin_no]) }}" method="POST" id="paymentForm">
                        @csrf
                        <input type="hidden" name="payment_method" value="PayPal">
                        
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary btn-lg" id="payBtn">
                                <i class="fas fa-lock"></i> Make Payment
                            </button>
                        </div>
                    </form>
                @endif
            </div>

            {{-- Security Notice --}}
            <div class="alert alert-info mt-4">
                <i class="fas fa-shield-alt"></i>
                <strong>Secure Payment:</strong> Your payment information is processed securely.
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
