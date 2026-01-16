@extends('layouts.startup-zone')

@section('title', 'Payment - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@push('styles')
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

    /* Tabular Info Styles */
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

    /* Price Table Styles */
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

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .info-table {
            font-size: 0.75rem;
        }

        .info-table td {
            padding: 0.5rem 0.4rem;
        }

        .price-table {
            font-size: 0.8rem;
        }

        .price-table td {
            padding: 0.5rem 0.4rem;
        }
    }
</style>
@endpush

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
                    <div class="alert alert-info mb-4">
                        <h5 class="mb-2"><i class="fas fa-info-circle"></i> Application Information</h5>
                        <p class="mb-0">
                            <strong>Application ID:</strong> {{ $application->application_id }}<br>
                            <strong>Exhibitor:</strong> {{ $application->company_name }}
                        </p>
                    </div>

                    {{-- Booth & Exhibition Details --}}
                    <div class="preview-section">
                        <h4 class="section-title">
                            <i class="fas fa-cube"></i>
                            Booth & Exhibition Details
                        </h4>
                        <table class="info-table">
                            <tr>
                                <td class="label-cell">Booth Space</td>
                                <td class="value-cell"><strong>{{ $application->stall_category ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="label-cell">Booth Size</td>
                                <td class="value-cell">{{ $application->interested_sqm ?? 'N/A' }}</td>
                            </tr>
                            @if($application->sector_id)
                            <tr>
                                <td class="label-cell">Sector</td>
                                <td class="value-cell">{{ $application->sector_id }}</td>
                            </tr>
                            @endif
                            @if($application->subSector)
                            <tr>
                                <td class="label-cell">Subsector</td>
                                <td class="value-cell">{{ $application->subSector }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>

                    @php
                        // Extract GST status from application
                        $gstStatus = $application->gst_compliance ? 'Registered' : 'Unregistered';
                        $gstNo = $application->gst_no ?? null;
                        $panNo = $application->pan_no ?? 'N/A';
                        
                        // Get billing city
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

                    {{-- Tax & Compliance Details --}}
                    <div class="preview-section">
                        <h4 class="section-title">
                            <i class="fas fa-file-invoice-dollar"></i>
                            Tax & Compliance Details
                        </h4>
                        <table class="info-table">
                            <tr>
                                <td class="label-cell">GST Status</td>
                                <td class="value-cell"><strong>{{ $gstStatus }}</strong></td>
                            </tr>
                            @if($gstStatus === 'Registered' && $gstNo)
                            <tr>
                                <td class="label-cell">GST Number</td>
                                <td class="value-cell">{{ $gstNo }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="label-cell">PAN Number</td>
                                <td class="value-cell">{{ $panNo }}</td>
                            </tr>
                        </table>
                    </div>

                    {{-- Billing Information --}}
                    @if($application)
                    <div class="preview-section">
                        <h4 class="section-title">
                            <i class="fas fa-building"></i>
                            Billing Information
                        </h4>
                        <table class="info-table">
                            <tr>
                                <td class="label-cell">Company Name</td>
                                <td class="value-cell"><strong>{{ $application->company_name ?? 'N/A' }}</strong></td>
                            </tr>
                            @if($application->eventContact)
                            <tr>
                                <td class="label-cell">Contact Name</td>
                                <td class="value-cell">{{ $application->eventContact->first_name }} {{ $application->eventContact->last_name ?? '' }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="label-cell">Email</td>
                                <td class="value-cell">{{ $application->company_email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Phone</td>
                                <td class="value-cell">{{ $application->landline ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Address</td>
                                <td class="value-cell">{{ $application->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">City</td>
                                <td class="value-cell">{{ $billingCity }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">State</td>
                                <td class="value-cell">{{ $application->state ? $application->state->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Country</td>
                                <td class="value-cell">{{ $application->country ? $application->country->name : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Postal Code</td>
                                <td class="value-cell">{{ $application->postal_code ?? 'N/A' }}</td>
                            </tr>
                            @if($application->certificate && $application->certificate !== 'N/A')
                            <tr>
                                <td class="label-cell">Certificate</td>
                                <td class="value-cell">
                                    <a href="{{ asset('storage/' . $application->certificate) }}" target="_blank" class="btn btn-sm btn-primary">
                                        <i class="fas fa-file-pdf"></i> View Certificate
                                    </a>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    @endif

                    {{-- Exhibitor Information --}}
                    @if($exhibitorInfo)
                    <div class="preview-section">
                        <h4 class="section-title">
                            <i class="fas fa-building"></i>
                            Exhibitor Information
                        </h4>
                        <table class="info-table">
                            <tr>
                                <td class="label-cell">Name of Exhibitor</td>
                                <td class="value-cell"><strong>{{ $exhibitorInfo->company_name ?? 'N/A' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="label-cell">Company Email</td>
                                <td class="value-cell">{{ $exhibitorInfo->email ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Address</td>
                                <td class="value-cell">{{ $exhibitorInfo->address ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">City</td>
                                <td class="value-cell">{{ $exhibitorInfo->city ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">State</td>
                                <td class="value-cell">{{ $exhibitorInfo->state ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Country</td>
                                <td class="value-cell">{{ $exhibitorInfo->country ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Postal Code</td>
                                <td class="value-cell">{{ $exhibitorInfo->zip_code ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Telephone</td>
                                <td class="value-cell">{{ $exhibitorInfo->telPhone ?? 'N/A' }}</td>
                            </tr>
                            @if($exhibitorInfo->website)
                            <tr>
                                <td class="label-cell">Website</td>
                                <td class="value-cell"><a href="{{ $exhibitorInfo->website }}" target="_blank">{{ $exhibitorInfo->website }}</a></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    @endif

                    {{-- Contact Person Details --}}
                    @if($application->eventContact)
                    <div class="preview-section">
                        <h4 class="section-title">
                            <i class="fas fa-user"></i>
                            Contact Person Details
                        </h4>
                        <table class="info-table">
                            <tr>
                                <td class="label-cell">Name</td>
                                <td class="value-cell"><strong>{{ $application->eventContact->first_name }} {{ $application->eventContact->last_name }}</strong></td>
                            </tr>
                            <tr>
                                <td class="label-cell">Email</td>
                                <td class="value-cell">{{ $application->eventContact->email }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Mobile</td>
                                <td class="value-cell">{{ $application->eventContact->contact_number }}</td>
                            </tr>
                            @if($application->eventContact->job_title)
                            <tr>
                                <td class="label-cell">Designation</td>
                                <td class="value-cell">{{ $application->eventContact->job_title }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    @endif

                    {{-- Invoice Details --}}
                    @if($invoice)
                    <div class="price-section">
                        <h4 class="section-title">
                            <i class="fas fa-file-invoice"></i>
                            Invoice Details
                        </h4>
                        <table class="price-table">
                            <tr>
                                <td class="label-cell">Base Price</td>
                                <td class="value-cell">{{ $invoice->currency }} {{ number_format($invoice->price, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">GST (18%)</td>
                                <td class="value-cell">{{ $invoice->currency }} {{ number_format($invoice->gst, 2) }}</td>
                            </tr>
                            <tr>
                                <td class="label-cell">Processing Charges ({{ $invoice->processing_chargesRate ?? 3 }}%)</td>
                                <td class="value-cell">{{ $invoice->currency }} {{ number_format($invoice->processing_charges, 2) }}</td>
                            </tr>
                            <tr class="total-row">
                                <td class="label-cell">Total Amount</td>
                                <td class="value-cell">{{ $invoice->currency }} {{ number_format($invoice->total_final_price, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                    @endif

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
                                <?php 
                                /*
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
                                */
                                 ?>
                            </div>
                            <?php 
                                /*
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" 
                                           value="Bank Transfer">
                                    <label class="form-check-label" for="bank_transfer">
                                        <strong>Bank Transfer</strong> (Contact us for instructions)
                                    </label>
                                </div>
                            </div>

                            */ ?>

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
