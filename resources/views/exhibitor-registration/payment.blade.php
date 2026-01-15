@extends('layouts.exhibitor-registration')

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

                    <!-- Booth & Exhibition Details -->
                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Booth & Exhibition Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Booth Space:</strong><br>
                                <small>{{ $application->stall_category ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Booth Type:</strong><br>
                                <small>{{ $application->interested_sqm ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Sector:</strong><br>
                                <small>{{ $application->sector_id ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Subsector:</strong><br>
                                <small>{{ $application->subSector ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Category:</strong><br>
                                <small>{{ $application->exhibitorType ?? 'N/A' }}</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Sales Executive:</strong><br>
                                <small>{{ $application->salesPerson ?? 'N/A' }}</small>
                            </div>
                        </div>
                    </div>
                    
                   
                    {{-- Billing Information --}}
                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Billing Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Company Name:</strong><br>
                                    <small>{{ $application->company_name ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Email:</strong><br>
                                    <small>{{ $application->company_email ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <strong>Address:</strong><br>
                                    <small>{{ $application->address ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <strong>City:</strong><br>
                                    @php
                                        $billingCity = 'N/A';
                                        if ($application->city_id) {
                                            if (is_numeric($application->city_id)) {
                                                $city = \App\Models\City::find($application->city_id);
                                                $billingCity = $city ? $city->name : $application->city_id;
                                            } else {
                                                $billingCity = $application->city_id;
                                            }
                                        }
                                    @endphp
                                    <small>{{ $billingCity }}</small>
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

                    {{-- Contact Person Information --}}
                    @if($application->eventContact)
                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-user"></i> Primary Contact Person</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Name:</strong><br>
                                    <small>{{ $application->eventContact->salutation ?? '' }} {{ $application->eventContact->first_name }} {{ $application->eventContact->last_name }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Designation:</strong><br>
                                    <small>{{ $application->eventContact->designation ?? $application->eventContact->job_title ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Email:</strong><br>
                                    <small>{{ $application->eventContact->email ?? 'N/A' }}</small>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Mobile:</strong><br>
                                    <small>{{ $application->eventContact->contact_number ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Invoice Details --}}
                    <h5 class="mb-3">Invoice Details</h5>
                    <table class="table table-bordered mb-4">
                        <tr>
                            <td><strong>Base Price:</strong></td>
                            <td class="text-end">{{ $application->invoice->currency ?? 'INR' }} {{ number_format($application->invoice->price ?? $application->invoice->amount, 2) }}</td>
                        </tr>
                        @if($application->invoice->gst_amount || $application->invoice->gst)
                        <tr>
                            <td><strong>GST ({{ $application->invoice->gst_rate ?? 18 }}%):</strong></td>
                            <td class="text-end">{{ $application->invoice->currency ?? 'INR' }} {{ number_format($application->invoice->gst_amount ?? $application->invoice->gst ?? 0, 2) }}</td>
                        </tr>
                        @endif
                        @if($application->invoice->processing_charges)
                        <tr>
                            <td><strong>Processing Charges ({{ $application->invoice->processing_chargesRate ?? 3 }}%):</strong></td>
                            <td class="text-end">{{ $application->invoice->currency ?? 'INR' }} {{ number_format($application->invoice->processing_charges, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="table-success">
                            <td><strong>Total Amount:</strong></td>
                            <td class="text-end"><strong>{{ $application->invoice->currency ?? 'INR' }} {{ number_format($application->invoice->total_final_price ?? $application->invoice->amount, 2) }}</strong></td>
                        </tr>
                    </table>

                    @if($application->invoice->payment_status === 'paid')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Payment already completed!
                        </div>
                        <a href="{{ route('exhibitor-registration.confirmation', $application->id) }}" class="btn btn-success">
                            View Confirmation <i class="fas fa-arrow-right"></i>
                        </a>
                    @else
                        {{-- Payment Options --}}
                        <h5 class="mb-3">Select Payment Method</h5>
                        
                        <form id="paymentForm" method="POST" action="{{ route('exhibitor-registration.payment.process', $application->application_id) }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card payment-option-card border-primary" 
                                         onclick="document.getElementById('ccavenue').checked = true;">
                                        <div class="card-body text-center">
                                            <input class="form-check-input" type="radio" name="payment_method" id="ccavenue" 
                                                   value="CCAvenue" checked style="position: absolute; top: 10px; right: 10px;">
                                            <div class="mb-2">
                                                <i class="fas fa-credit-card fa-3x text-primary"></i>
                                            </div>
                                            <h6 class="card-title"><strong>CCAvenue</strong></h6>
                                            <p class="card-text text-muted small mb-0">Indian Payments</p>
                                            <p class="card-text text-muted small">Credit Card, Debit Card, Net Banking, UPI, Wallets</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-warning">
                                <strong>Note:</strong> After clicking "Proceed to Payment", you will be redirected to the payment gateway.
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('exhibitor-registration.preview') }}" 
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

