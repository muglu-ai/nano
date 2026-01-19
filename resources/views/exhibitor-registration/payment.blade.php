@extends('layouts.exhibitor-registration')

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

    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">Payment</h2>

          
        
            
            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif
              {{-- Approval Pending Message --}}
            @if(isset($approval_pending) && $approval_pending)
            <div class="alert alert-warning mb-4">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Approval Pending:</strong> Your profile is not approved yet for payment. Please wait for admin approval. You will be notified once your application is approved.
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

            @php
                // Extract GST and TAN status from application
                $gstStatus = $application->gst_compliance ? 'Registered' : 'Unregistered';
                $gstNo = $application->gst_no ?? null;
                $panNo = $application->pan_no ?? '';
                $tanNo = $application->tan_no ?? null;
                // TAN Status from tan_compliance field (similar to gst_compliance)
                $tanStatus = $application->tan_compliance ? 'Registered' : 'Unregistered';
                
                // Get exhibitor information from application table (exhibitor_data is stored in applications)
                $exhibitorName = $application->company_name ?? '';
                $exhibitorAddress = $application->address ?? '';
                $exhibitorCity = is_numeric($application->city_id) ? (\App\Models\City::find($application->city_id)->name ?? $application->city_id) : ($application->city_id ?? '');
                $exhibitorState = $application->state ? $application->state->name : 'N/A';
                $exhibitorCountry = $application->country ? $application->country->name : 'N/A';
                $exhibitorPostal = $application->postal_code ?? '';
                $exhibitorPhone = $application->landline ?? '';
                $exhibitorWebsite = $application->website ?? '';
                $exhibitorEmail = $application->company_email ?? '';
                
                // Check if exhibitor info is different from billing (to show separate section)
                $showExhibitorSection = $billingDetail && (
                    ($billingDetail->billing_company ?? '') !== ($application->company_name ?? '') ||
                    ($billingDetail->address ?? '') !== ($application->address ?? '')
                );
            @endphp

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
                        <td class="value-cell">{{ $application->interested_sqm ?? 'N/A' }}@if($application->interested_sqm) sqm @endif</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Sector</td>
                        <td class="value-cell">{{ $application->sector_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Subsector</td>
                        <td class="value-cell">{{ $application->subSector ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Category</td>
                        <td class="value-cell">{{ $application->exhibitorType ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Sales Executive Name</td>
                        <td class="value-cell">{{ $application->salesPerson ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            
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
                        <td class="label-cell">TAN Status</td>
                        <td class="value-cell"><strong>{{ $tanStatus }}</strong></td>
                    </tr>
                    @if($tanStatus === 'Registered' && $tanNo)
                    <tr>
                        <td class="label-cell">TAN Number</td>
                        <td class="value-cell">{{ $tanNo }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label-cell">PAN Number</td>
                        <td class="value-cell">{{ $panNo ?: 'N/A' }}</td>
                    </tr>
                   
                </table>
            </div>
            {{-- Billing Information --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-building"></i>
                    Billing Information
                </h4>
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
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Company Name</td>
                        <td class="value-cell"><strong>{{ $application->company_name ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Email</td>
                        <td class="value-cell">{{ $application->company_email ?? 'N/A' }}</td>
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
                        <td class="label-cell">Postal Code</td>
                        <td class="value-cell">{{ $application->postal_code ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Country</td>
                        <td class="value-cell">{{ $application->country ? $application->country->name : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Telephone</td>
                        <td class="value-cell">{{ $application->landline ?? 'N/A' }}</td>
                    </tr>
                    @if($application->website)
                    <tr>
                        <td class="label-cell">Website</td>
                        <td class="value-cell"><a href="{{ $application->website }}" target="_blank">{{ $application->website }}</a></td>
                    </tr>
                    @endif
                </table>
            </div>

            
             {{-- Exhibitor Information --}}
             @if($showExhibitorSection || !empty($exhibitorName))
             <div class="preview-section">
                 <h4 class="section-title">
                     <i class="fas fa-building"></i>
                     Exhibitor Information
                 </h4>
                 <table class="info-table">
                     <tr>
                         <td class="label-cell">Name of Exhibitor</td>
                         <td class="value-cell"><strong>{{ $exhibitorName }}</strong></td>
                     </tr>
                     <tr>
                         <td class="label-cell">Address</td>
                         <td class="value-cell">{{ $exhibitorAddress ?: 'N/A' }}</td>
                     </tr>
                     <tr>
                         <td class="label-cell">City</td>
                         <td class="value-cell">{{ $exhibitorCity ?: 'N/A' }}</td>
                     </tr>
                     <tr>
                         <td class="label-cell">State</td>
                         <td class="value-cell">{{ $exhibitorState }}</td>
                     </tr>
                     <tr>
                         <td class="label-cell">Postal Code</td>
                         <td class="value-cell">{{ $exhibitorPostal ?: 'N/A' }}</td>
                     </tr>
                     <tr>
                         <td class="label-cell">Country</td>
                         <td class="value-cell">{{ $exhibitorCountry }}</td>
                     </tr>
                     @if(!empty($exhibitorPhone))
                     <tr>
                         <td class="label-cell">Telephone</td>
                         <td class="value-cell">{{ $exhibitorPhone }}</td>
                     </tr>
                     @endif
                     @if(!empty($exhibitorWebsite))
                     <tr>
                         <td class="label-cell">Website</td>
                         <td class="value-cell"><a href="{{ $exhibitorWebsite }}" target="_blank">{{ $exhibitorWebsite }}</a></td>
                     </tr>
                     @endif
                     @if(!empty($exhibitorEmail))
                     <tr>
                         <td class="label-cell">Email</td>
                         <td class="value-cell">{{ $exhibitorEmail }}</td>
                     </tr>
                     @endif
                 </table>
             </div>
             @endif
            {{-- Contact Person Information --}}
            @if($application->eventContact)
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-user"></i>
                    Contact Person Details
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Name</td>
                        <td class="value-cell"><strong>{{ $application->eventContact->salutation ?? '' }} {{ $application->eventContact->first_name }} {{ $application->eventContact->last_name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Designation</td>
                        <td class="value-cell">{{ $application->eventContact->designation ?? $application->eventContact->job_title ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Email</td>
                        <td class="value-cell">{{ $application->eventContact->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Mobile</td>
                        <td class="value-cell">{{ $application->eventContact->contact_number ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            @endif

            {{-- Invoice Details --}}
            @if($application->invoice)
            <div class="price-section">
                <h4 class="section-title">
                    <i class="fas fa-calculator"></i>
                    Invoice Details
                </h4>
                @php
                    $currencySymbol = ($application->invoice->currency ?? 'INR') === 'USD' ? '$' : '₹';
                    $priceFormat = 2;
                @endphp
                <table class="price-table">
                    <tr>
                        <td class="label-cell">Base Price</td>
                        <td class="value-cell">{{ $currencySymbol }}{{ number_format($application->invoice->price ?? $application->invoice->amount, $priceFormat) }}</td>
                    </tr>
                    @if($application->invoice->gst_amount || $application->invoice->gst)
                    <tr>
                        <td class="label-cell">GST ({{ $application->invoice->gst_rate ?? 18 }}%)</td>
                        <td class="value-cell">{{ $currencySymbol }}{{ number_format($application->invoice->gst_amount ?? $application->invoice->gst ?? 0, $priceFormat) }}</td>
                    </tr>
                    @endif
                    @if($application->invoice->processing_charges)
                    <tr>
                        <td class="label-cell">Processing Charges ({{ $application->invoice->processing_chargesRate ?? 3 }}%)</td>
                        <td class="value-cell">{{ $currencySymbol }}{{ number_format($application->invoice->processing_charges, $priceFormat) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="label-cell" style="background: var(--primary-color); color: white;">Total Amount</td>
                        <td class="value-cell" style="background: var(--primary-color); color: white;">{{ $currencySymbol }}{{ number_format($application->invoice->total_final_price ?? $application->invoice->amount, $priceFormat) }}</td>
                    </tr>
                </table>
            </div>
            @endif
                    <?php /* 
                    <!-- @if($application->invoice->payment_status === 'paid')
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
                    @endif -->
                     */ ?>

            {{-- Payment Notice --}}
            <!-- <div class="alert alert-warning mb-4">
                <h5 class="mb-2"><i class="fas fa-clock"></i> Payment Notice</h5>
                <p class="mb-0">Payment options will be available once your application is approved by the admin.</p>
            </div> -->

            @if($application->invoice->payment_status === 'paid')
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> Payment already completed!
                        </div>
                        <a href="{{ route('exhibitor-registration.confirmation', $application->application_id) }}" class="btn btn-success">
                            View Confirmation <i class="fas fa-arrow-right"></i>
                        </a>
                    @elseif(isset($approval_pending) && $approval_pending)
                        {{-- Approval Pending - Disable Payment --}}
                        <div class="alert alert-warning">
                            <i class="fas fa-clock"></i> Payment options will be available once your application is approved by the admin.
                        </div>
                    @else
                        {{-- Payment Options --}}
                        <!-- <h5 class="mb-3">Select Payment Method</h5> -->
                        
                        <form id="paymentForm" method="POST" action="{{ route('exhibitor-registration.payment.process', $application->application_id) }}">
                            @csrf
                            
                         <?php /*   <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card payment-option-card {{ $application->invoice->currency === 'INR' ? 'border-primary' : '' }}" 
                                         onclick="document.getElementById('ccavenue').checked = true;">
                                        <div class="card-body text-center">
                                            <input class="form-check-input" type="radio" name="payment_method" id="ccavenue" 
                                                   value="CCAvenue" {{ $application->invoice->currency === 'INR' ? 'checked' : '' }} style="position: absolute; top: 10px; right: 10px;">
                                            <div class="mb-2">
                                                <i class="fas fa-credit-card fa-3x text-primary"></i>
                                            </div>
                                            <h6 class="card-title"><strong>CCAvenue</strong></h6>
                                            <p class="card-text text-muted small mb-0">Indian Payments</p>
                                            <p class="card-text text-muted small">Credit Card, Debit Card, Net Banking, UPI, Wallets</p>
                                        </div>
                                    </div>
                                </div>
                                 */ ?>
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
                                <a href="{{ route('exhibitor-registration.preview', ['application_id' => $application->application_id]) }}" 
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

