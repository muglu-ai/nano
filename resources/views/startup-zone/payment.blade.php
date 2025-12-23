@extends('layouts.startup-zone')

@section('title', 'Payment - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@section('content')
<div class="container py-5">
    {{-- Step Indicator --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="step-indicator">
                <div class="step-item completed">
                    <div class="step-number">1</div>
                    <div class="step-label">Exhibitor Details</div>
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
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card"></i> Payment</h4>
                </div>
                <div class="card-body">
                    {{-- Approval Pending Message --}}
                    @if(isset($approval_pending) && $approval_pending)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Approval Pending:</strong> Your profile is not approved yet for payment. Please wait for admin approval. You will be notified once your application is approved.
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif
                    
                    {{-- Application Summary --}}
                    <div class="alert alert-info">
                        <strong>Application ID:</strong> {{ $application->application_id }}<br>
                        <strong>Exhibitor:</strong> {{ $application->company_name }}
                    </div>

                    {{-- Billing Information --}}
                    @if($billingDetail)
                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Billing Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Company Name:</strong><br>
                                    <small>{{ $billingDetail->billing_company ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Contact Name:</strong><br>
                                    <small>{{ $billingDetail->contact_name ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Email:</strong><br>
                                    <small>{{ $billingDetail->email ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Phone:</strong><br>
                                    <small>{{ $billingDetail->phone ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <strong>Address:</strong><br>
                                    <small>{{ $billingDetail->address ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>City:</strong><br>
                                    @php
                                        $billingCity = 'N/A';
                                        if ($billingDetail->city_id) {
                                            if (is_numeric($billingDetail->city_id)) {
                                                $city = \App\Models\City::find($billingDetail->city_id);
                                                $billingCity = $city ? $city->name : $billingDetail->city_id;
                                            } else {
                                                $billingCity = $billingDetail->city_id; // It's already a city name
                                            }
                                        }
                                    @endphp
                                    <small>{{ $billingCity }}</small>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>State:</strong><br>
                                    <small>{{ $billingDetail->state_id ? (\App\Models\State::find($billingDetail->state_id)->name ?? 'N/A') : 'N/A' }}</small>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>Country:</strong><br>
                                    <small>{{ $billingDetail->country_id ? (\App\Models\Country::find($billingDetail->country_id)->name ?? 'N/A') : 'N/A' }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Postal Code:</strong><br>
                                    <small>{{ $billingDetail->postal_code ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Exhibitor Information --}}
                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-building"></i> Exhibitor Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Name of Exhibitor:</strong><br>
                                    <small>{{ $application->company_name }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Company Email:</strong><br>
                                    <small>{{ $application->company_email }}</small>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <strong>Address:</strong><br>
                                    <small>{{ $application->address ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>City:</strong><br>
                                    @php
                                        $exhibitorCity = 'N/A';
                                        if ($application->city_id) {
                                            if (is_numeric($application->city_id)) {
                                                $city = \App\Models\City::find($application->city_id);
                                                $exhibitorCity = $city ? $city->name : $application->city_id;
                                            } else {
                                                $exhibitorCity = $application->city_id; // It's already a city name
                                            }
                                        }
                                    @endphp
                                    <small>{{ $exhibitorCity }}</small>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>State:</strong><br>
                                    <small>{{ $application->state ? $application->state->name : 'N/A' }}</small>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>Country:</strong><br>
                                    <small>{{ $application->country ? $application->country->name : 'N/A' }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Postal Code:</strong><br>
                                    <small>{{ $application->postal_code ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Telephone:</strong><br>
                                    <small>{{ $application->landline ?? 'N/A' }}</small>
                                </div>
                                @if($application->website)
                                <div class="col-md-6 mb-2">
                                    <strong>Website:</strong><br>
                                    <small><a href="{{ $application->website }}" target="_blank">{{ $application->website }}</a></small>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Invoice Details --}}
                    <h5 class="mb-3">Invoice Details</h5>
                    <table class="table table-bordered mb-4">
                        <tr>
                            <td><strong>Base Price:</strong></td>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->price, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>GST (18%):</strong></td>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->gst, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Processing Charges ({{ $invoice->processing_chargesRate ?? 3 }}%):</strong></td>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->processing_charges, 2) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>Total Amount:</strong></td>
                            <td class="text-end"><strong>{{ $invoice->currency }} {{ number_format($invoice->total_final_price, 2) }}</strong></td>
                        </tr>
                    </table>

                    @if($invoice->payment_status === 'paid')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Payment already completed!
                        </div>
                        <a href="{{ route('startup-zone.confirmation', $application->application_id) }}" class="btn btn-success">
                            View Confirmation <i class="fas fa-arrow-right"></i>
                        </a>
                    @elseif(isset($approval_pending) && $approval_pending)
                        {{-- Approval Pending - Disable Payment --}}
                        <div class="alert alert-warning">
                            <i class="fas fa-clock"></i> Payment options will be available once your application is approved by the admin.
                        </div>
                    @else
                        {{-- Payment Options --}}
                        <h5 class="mb-3">Select Payment Method</h5>
                        
                        <form id="paymentForm" method="POST" action="{{ route('startup-zone.payment.process', $application->application_id) }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card payment-option-card {{ $invoice->currency === 'INR' ? 'border-primary' : '' }}" 
                                         onclick="document.getElementById('ccavenue').checked = true;">
                                        <div class="card-body text-center">
                                            <input class="form-check-input" type="radio" name="payment_method" id="ccavenue" 
                                                   value="CCAvenue" {{ $invoice->currency === 'INR' ? 'checked' : '' }} style="position: absolute; top: 10px; right: 10px;">
                                            <div class="mb-2">
                                                <i class="fas fa-credit-card fa-3x text-primary"></i>
                                            </div>
                                            <h6 class="card-title"><strong>CCAvenue</strong></h6>
                                            <p class="card-text text-muted small mb-0">Indian Payments</p>
                                            <p class="card-text text-muted small">Credit Card, Debit Card, Net Banking, UPI, Wallets</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <div class="card payment-option-card {{ $invoice->currency === 'USD' ? 'border-primary' : '' }}" 
                                         onclick="document.getElementById('paypal').checked = true;">
                                        <div class="card-body text-center">
                                            <input class="form-check-input" type="radio" name="payment_method" id="paypal" 
                                                   value="PayPal" {{ $invoice->currency === 'USD' ? 'checked' : '' }} style="position: absolute; top: 10px; right: 10px;">
                                            <div class="mb-2">
                                                <i class="fab fa-paypal fa-3x text-primary"></i>
                                            </div>
                                            <h6 class="card-title"><strong>PayPal</strong></h6>
                                            <p class="card-text text-muted small mb-0">International Payments</p>
                                            <p class="card-text text-muted small">PayPal Account or Credit Card</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" 
                                           value="Bank Transfer">
                                    <label class="form-check-label" for="bank_transfer">
                                        <strong>Bank Transfer</strong> (Contact us for instructions)
                                    </label>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <strong>Note:</strong> After clicking "Proceed to Payment", you will be redirected to the payment gateway.
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('startup-zone.preview', ['application_id' => $application->application_id]) }}" 
                                   class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    Proceed to Payment <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .step-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 10px;
    }
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        flex: 1;
    }
    .step-number {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        border: 3px solid #e0e0e0;
    }
    .step-item.active .step-number {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(27, 55, 131, 0.2);
    }
    .step-item.completed .step-number {
        background: #28a745;
        color: white;
        border-color: #28a745;
        font-size: 0;
    }
    .step-item.completed .step-number::before {
        content: '✓';
        font-size: 1.5rem;
        display: block;
    }
    .step-label {
        font-size: 0.9rem;
        color: #666;
        font-weight: 500;
        text-align: center;
    }
    .step-item.active .step-label {
        color: var(--primary-color);
        font-weight: 600;
    }
    .step-item.completed .step-label {
        color: #28a745;
    }
    .step-connector {
        flex: 1;
        height: 3px;
        background: #e0e0e0;
        margin: 0 1rem;
        margin-top: -25px;
        position: relative;
        z-index: 0;
    }
    .step-item.completed ~ .step-connector,
    .step-item.active ~ .step-connector {
        background: var(--primary-color);
    }
    .payment-option-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e0e0e0;
    }
    .payment-option-card:hover {
        border-color: #007bff;
        box-shadow: 0 4px 8px rgba(0,123,255,0.2);
        transform: translateY(-2px);
    }
    .payment-option-card.border-primary {
        border-color: #007bff !important;
        background-color: #f0f8ff;
    }
    .payment-option-card input[type="radio"]:checked + div {
        color: #007bff;
    }
    @media (max-width: 768px) {
        .step-indicator {
            padding: 1rem 0.5rem;
        }
        .step-number {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        .step-label {
            font-size: 0.75rem;
        }
        .step-connector {
            margin: 0 0.5rem;
            margin-top: -20px;
        }
    }
</style>
@endpush

@endsection
