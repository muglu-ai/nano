@extends('layouts.exhibitor-registration')

@section('title', 'Preview Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

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
                <div class="step-item active">
                    <div class="step-number">2</div>
                    <div class="step-label">Preview Details</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-label">Payment</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">Preview Your Registration</h2>
            
            @php
                // Determine if we have application (from DB), draft (from database), or submittedData (from session - legacy)
                $hasApplication = isset($application) && $application;
                $hasDraft = isset($draft) && $draft;
                $hasSubmittedData = isset($submittedData) && $submittedData;
                
                if ($hasApplication) {
                    // Data from database
                    $allData = [];
                    $boothSpace = $application->stall_category ?? '';
                    $boothSize = $application->interested_sqm ?? '';
                    $sector = $application->sector_id ?? '';
                    $subsector = $application->subSector ?? '';
                    $otherSector = $application->type_of_business ?? null;
                    $category = $application->exhibitorType ?? '';
                    $salesExecutiveName = $application->salesPerson ?? '';
                    $gstStatus = $application->gst_compliance ? 'Registered' : 'Unregistered';
                    $gstNo = $application->gst_no ?? null;
                    $panNo = $application->pan_no ?? '';
                    $tanNo = $application->tan_no ?? null;
                    $billingCompany = $application->company_name ?? '';
                    $billingEmail = $application->company_email ?? '';
                    $billingAddress = $application->address ?? '';
                    $billingCity = is_numeric($application->city_id) ? (\App\Models\City::find($application->city_id)->name ?? $application->city_id) : ($application->city_id ?? '');
                    $billingState = $application->state->name ?? 'N/A';
                    $billingCountry = $application->country->name ?? 'N/A';
                    $billingPostal = $application->postal_code ?? '';
                    $billingPhone = $application->landline ?? '';
                    $billingWebsite = $application->website ?? '';
                    $contactTitle = $application->eventContact->salutation ?? '';
                    $contactFirstName = $application->eventContact->first_name ?? '';
                    $contactLastName = $application->eventContact->last_name ?? '';
                    $contactDesignation = $application->eventContact->designation ?? '';
                    $contactEmail = $application->eventContact->email ?? '';
                    $contactMobile = $application->eventContact->contact_number ?? '';
                    $pricing = $application->invoice ? [
                        'base_price' => $application->invoice->price ?? $application->invoice->amount,
                        'gst_amount' => $application->invoice->gst_amount ?? $application->invoice->gst ?? 0,
                        'processing_charges' => $application->invoice->processing_charges ?? 0,
                        'processing_rate' => $application->invoice->processing_chargesRate ?? 3,
                        'gst_rate' => 18,
                        'total_price' => $application->invoice->total_final_price ?? $application->invoice->amount,
                    ] : null;
                } elseif ($hasDraft) {
                    // Data from draft table
                    $boothSpace = $draft->stall_category ?? '';
                    $boothSize = $draft->interested_sqm ?? '';
                    $sector = $draft->sector_id ?? '';
                    $subsector = $draft->subSector ?? '';
                    $otherSector = $draft->type_of_business ?? null;
                    $category = $exhibitorData['category'] ?? 'Exhibitor';
                    $salesExecutiveName = $exhibitorData['sales_executive_name'] ?? '';
                    $gstStatus = $draft->gst_compliance ? 'Registered' : 'Unregistered';
                    $gstNo = $draft->gst_no ?? null;
                    $panNo = $draft->pan_no ?? '';
                    $tanNo = $exhibitorData['tan_no'] ?? null;
                    
                    // Billing data from draft
                    $billingCompany = $billingData['company_name'] ?? $draft->company_name ?? '';
                    $billingEmail = $billingData['email'] ?? $draft->company_email ?? '';
                    $billingAddress = $billingData['address'] ?? $draft->address ?? '';
                    $billingCity = $billingData['city'] ?? $draft->city_id ?? '';
                    $billingStateId = $billingData['state_id'] ?? $draft->state_id ?? null;
                    $billingState = $billingStateId ? (\App\Models\State::find($billingStateId)->name ?? 'N/A') : 'N/A';
                    $billingCountryId = $billingData['country_id'] ?? $draft->country_id ?? null;
                    $billingCountry = $billingCountryId ? (\App\Models\Country::find($billingCountryId)->name ?? 'N/A') : 'N/A';
                    $billingPostal = $billingData['postal_code'] ?? $draft->postal_code ?? '';
                    // Format telephone: extract national number from "country_code-national_number" format
                    $billingPhoneRaw = $billingData['telephone'] ?? $draft->landline ?? '';
                    if ($billingPhoneRaw && strpos($billingPhoneRaw, '-') !== false) {
                        $parts = explode('-', $billingPhoneRaw, 2);
                        $billingPhone = '+' . $parts[0] . ' ' . $parts[1];
                    } else {
                        $billingPhone = $billingPhoneRaw;
                    }
                    $billingWebsite = $billingData['website'] ?? $draft->website ?? '';
                    
                    // Contact data from draft
                    $contactTitle = $contactData['title'] ?? '';
                    $contactFirstName = $contactData['first_name'] ?? '';
                    $contactLastName = $contactData['last_name'] ?? '';
                    $contactDesignation = $contactData['designation'] ?? '';
                    $contactEmail = $contactData['email'] ?? '';
                    // Format mobile: extract national number from "country_code-national_number" format
                    $contactMobileRaw = $contactData['mobile'] ?? '';
                    if ($contactMobileRaw && strpos($contactMobileRaw, '-') !== false) {
                        $parts = explode('-', $contactMobileRaw, 2);
                        $contactMobile = '+' . $parts[0] . ' ' . $parts[1];
                    } else {
                        $contactMobile = $contactMobileRaw;
                    }
                    
                    // Pricing from passed variable
                    $pricing = $pricing ?? null;
                } else {
                    // Legacy: Data from session
                    $allData = $submittedData['all_data'] ?? [];
                    $boothSpace = $allData['booth_space'] ?? '';
                    $boothSize = $allData['booth_size'] ?? '';
                    $sector = $allData['sector'] ?? '';
                    $subsector = $allData['subsector'] ?? '';
                    $otherSector = $allData['other_sector_name'] ?? null;
                    $category = $allData['category'] ?? '';
                    $salesExecutiveName = $allData['sales_executive_name'] ?? '';
                    $gstStatus = ($allData['gst_status'] ?? '') === 'Registered' ? 'Registered' : 'Unregistered';
                    $gstNo = $allData['gst_no'] ?? null;
                    $panNo = $allData['pan_no'] ?? '';
                    $tanNo = $allData['tan_no'] ?? null;
                    $billingData = $submittedData['billing_data'] ?? [];
                    $billingCompany = $billingData['company_name'] ?? $allData['billing_company_name'] ?? '';
                    $billingEmail = $billingData['email'] ?? $allData['billing_email'] ?? '';
                    $billingAddress = $billingData['address'] ?? $allData['billing_address'] ?? '';
                    $billingCity = $billingData['city'] ?? $allData['billing_city'] ?? '';
                    $billingStateId = $billingData['state_id'] ?? $allData['billing_state_id'] ?? null;
                    $billingState = $billingStateId ? (\App\Models\State::find($billingStateId)->name ?? 'N/A') : 'N/A';
                    $billingCountryId = $billingData['country_id'] ?? $allData['billing_country_id'] ?? null;
                    $billingCountry = $billingCountryId ? (\App\Models\Country::find($billingCountryId)->name ?? 'N/A') : 'N/A';
                    $billingPostal = $billingData['postal_code'] ?? $allData['billing_postal_code'] ?? '';
                    $billingPhone = $submittedData['billing_telephone'] ?? '';
                    $billingWebsite = $billingData['website'] ?? $allData['billing_website'] ?? '';
                    $contactData = $submittedData['contact_data'] ?? [];
                    $contactTitle = $contactData['title'] ?? $allData['contact_title'] ?? '';
                    $contactFirstName = $contactData['first_name'] ?? $allData['contact_first_name'] ?? '';
                    $contactLastName = $contactData['last_name'] ?? $allData['contact_last_name'] ?? '';
                    $contactDesignation = $contactData['designation'] ?? $allData['contact_designation'] ?? '';
                    $contactEmail = $contactData['email'] ?? $allData['contact_email'] ?? '';
                    $contactMobile = $submittedData['contact_mobile'] ?? '';
                    $pricing = $submittedData['pricing'] ?? null;
                }
            @endphp
            
            @if($hasApplication)
            {{-- Application Created Successfully --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle"></i> Application Created Successfully</h4>
                </div>
                <div class="card-body">
                    <p class="alert alert-info mb-0">
                        <strong>Application ID:</strong> {{ $application->application_id }}<br>
                        Please review your details below and proceed to payment.
                    </p>
                </div>
            </div>
            @elseif($hasDraft || $hasSubmittedData)
            {{-- Review Before Submission --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="fas fa-info-circle"></i> Review Your Details</h4>
                </div>
                <div class="card-body">
                    <p class="alert alert-warning mb-0">
                        Please review all details below. Click "Proceed to Payment" to finalize your registration.
                    </p>
                </div>
            </div>
            @else
            <div class="alert alert-danger">
                <strong>Error:</strong> No data found. Please submit the form again.
            </div>
            @endif

            {{-- Booth & Exhibition Details --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-cube"></i> Booth & Exhibition Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <strong>Booth Space:</strong><br>
                            {{ $boothSpace ?: 'N/A' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Booth Size:</strong><br>
                            {{ $boothSize ?: 'N/A' }} @if($boothSize) sqm @endif
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Sector:</strong><br>
                            {{ $sector ?: 'N/A' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Subsector:</strong><br>
                            {{ $subsector ?: 'N/A' }}
                        </div>
                        @if($otherSector)
                        <div class="col-md-4 mb-3">
                            <strong>Other Sector Name:</strong><br>
                            {{ $otherSector }}
                        </div>
                        @endif
                        <div class="col-md-4 mb-3">
                            <strong>Category:</strong><br>
                            {{ $category ?: 'N/A' }}
                        </div>
                        <div class="col-md-4 mb-3">
                            <strong>Sales Executive Name:</strong><br>
                            {{ $salesExecutiveName ?: 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tax & Compliance Details --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Tax & Compliance Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <strong>GST Status:</strong><br>
                            {{ $gstStatus }}
                        </div>
                        @if($gstStatus === 'Registered' && $gstNo)
                        <div class="col-md-4 mb-3">
                            <strong>GST Number:</strong><br>
                            {{ $gstNo }}
                        </div>
                        @endif
                        <div class="col-md-4 mb-3">
                            <strong>PAN Number:</strong><br>
                            {{ $panNo ?: 'N/A' }}
                        </div>
                        @if($tanNo)
                        <div class="col-md-4 mb-3">
                            <strong>TAN Number:</strong><br>
                            {{ $tanNo }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Billing Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-building"></i> Billing Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Company Name:</strong><br>
                            {{ $billingCompany ?: 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong><br>
                            {{ $billingEmail ?: 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Address:</strong><br>
                            {{ $billingAddress ?: 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>City:</strong><br>
                            {{ $billingCity ?: 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>State:</strong><br>
                            {{ $billingState }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Postal Code:</strong><br>
                            {{ $billingPostal ?: 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Country:</strong><br>
                            {{ $billingCountry }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Telephone:</strong><br>
                            {{ $billingPhone ?: 'N/A' }}
                        </div>
                        @if($billingWebsite)
                        <div class="col-md-6 mb-3">
                            <strong>Website:</strong><br>
                            <a href="{{ $billingWebsite }}" target="_blank">{{ $billingWebsite }}</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Contact Person Details --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-user"></i> Primary Contact Person</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Name:</strong><br>
                            {{ $contactTitle }} {{ $contactFirstName }} {{ $contactLastName }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Designation:</strong><br>
                            {{ $contactDesignation ?: 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong><br>
                            {{ $contactEmail ?: 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Mobile:</strong><br>
                            {{ $contactMobile ?: 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pricing Summary --}}
            @if($pricing)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-money-bill-wave"></i> Pricing Summary</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <td><strong>Base Price:</strong></td>
                            <td class="text-end">INR {{ number_format($pricing['base_price'], 2) }}</td>
                        </tr>
                        @if($pricing['gst_amount'])
                        <tr>
                            <td><strong>GST ({{ $pricing['gst_rate'] }}%):</strong></td>
                            <td class="text-end">INR {{ number_format($pricing['gst_amount'], 2) }}</td>
                        </tr>
                        @endif
                        @if($pricing['processing_charges'])
                        <tr>
                            <td><strong>Processing Charges ({{ $pricing['processing_rate'] }}%):</strong></td>
                            <td class="text-end">INR {{ number_format($pricing['processing_charges'], 2) }}</td>
                        </tr>
                        @endif
                        <tr class="table-success">
                            <td><strong>Total Amount:</strong></td>
                            <td class="text-end"><strong>INR {{ number_format($pricing['total_price'], 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('exhibitor-registration.register') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Edit Details
                </a>
                @if($hasApplication)
                    <a href="{{ route('exhibitor-registration.payment', $application->application_id) }}" class="btn btn-success btn-lg">
                        Proceed to Payment <i class="fas fa-arrow-right"></i>
                    </a>
                @elseif($hasDraft || $hasSubmittedData)
                    <button type="button" class="btn btn-success btn-lg" id="proceedToPaymentBtn">
                        Proceed to Payment <i class="fas fa-arrow-right"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@if(!$hasApplication && ($hasDraft || $hasSubmittedData))
@push('scripts')
<script>
document.getElementById('proceedToPaymentBtn')?.addEventListener('click', function() {
    const btn = this;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting Application...';
    
    fetch('{{ route("exhibitor-registration.create-application") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect_url;
        } else {
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('Error: ' + (data.message || 'Failed to create application. Please try again.'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = originalText;
        alert('An error occurred. Please try again.');
    });
});
</script>
@endpush
@endif

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
