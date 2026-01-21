@extends('layouts.exhibitor-registration')

@section('title', 'Preview Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/custom.css') }}">
<style>
     .form-container {padding: 1rem 0px;}
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

    .btn-edit {
        background: #ffffff;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-edit:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(11, 94, 215, 0.3);
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

        .btn-edit,
        .btn-primary {
            padding: 0.75rem 1.25rem !important;
            font-size: 0.9rem !important;
            margin-bottom: 0.5rem;
        }

        .btn-lg {
            padding: 0.75rem 1.25rem !important;
            font-size: 0.9rem !important;
        }
    }
</style>
@endpush
@section('content')
<div class="container py-3">
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
                    // TAN Status from tan_compliance field (similar to gst_compliance)
                    $tanStatus = $application->tan_compliance ? 'Registered' : 'Unregistered';
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
                    
                    // Exhibitor data from application (if different from billing)
                    // For now, exhibitor info is same as billing in application
                    $exhibitorName = $application->company_name ?? '';
                    $exhibitorAddress = $application->address ?? '';
                    $exhibitorCity = is_numeric($application->city_id) ? (\App\Models\City::find($application->city_id)->name ?? $application->city_id) : ($application->city_id ?? '');
                    $exhibitorState = $application->state->name ?? 'N/A';
                    $exhibitorCountry = $application->country->name ?? 'N/A';
                    $exhibitorPostal = $application->postal_code ?? '';
                    $exhibitorPhone = $application->landline ?? '';
                    $exhibitorWebsite = $application->website ?? '';
                    $exhibitorEmail = $application->company_email ?? '';
                    
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
                    // First extract the JSON data fields from draft
                    $billingData = $draft->billing_data ?? [];
                    $exhibitorData = $draft->exhibitor_data ?? [];
                    $contactData = $draft->contact_data ?? [];
                    
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
                    $tanNo = $billingData['tan_no'] ?? null;
                    // TAN Status from billing data (similar to gst_status)
                    $tanStatus = ($billingData['tan_status'] ?? 'Unregistered') === 'Registered' ? 'Registered' : 'Unregistered';
                    
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
                    
                    // Exhibitor data from draft
                    $exhibitorName = $exhibitorData['name'] ?? '';
                    $exhibitorAddress = $exhibitorData['address'] ?? '';
                    $exhibitorCity = $exhibitorData['city'] ?? '';
                    $exhibitorStateId = $exhibitorData['state_id'] ?? null;
                    $exhibitorState = $exhibitorStateId ? (\App\Models\State::find($exhibitorStateId)->name ?? 'N/A') : 'N/A';
                    $exhibitorCountryId = $exhibitorData['country_id'] ?? null;
                    $exhibitorCountry = $exhibitorCountryId ? (\App\Models\Country::find($exhibitorCountryId)->name ?? 'N/A') : 'N/A';
                    $exhibitorPostal = $exhibitorData['postal_code'] ?? '';
                    $exhibitorPhoneRaw = $exhibitorData['telephone'] ?? '';
                    if ($exhibitorPhoneRaw && strpos($exhibitorPhoneRaw, '-') !== false) {
                        $parts = explode('-', $exhibitorPhoneRaw, 2);
                        $exhibitorPhone = '+' . $parts[0] . ' ' . $parts[1];
                    } else {
                        $exhibitorPhone = $exhibitorPhoneRaw;
                    }
                    $exhibitorWebsite = $exhibitorData['website'] ?? '';
                    $exhibitorEmail = $exhibitorData['email'] ?? '';
                    
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
                    // TAN Status from allData
                    $tanStatus = ($allData['tan_status'] ?? '') === 'Registered' ? 'Registered' : 'Unregistered';
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
                    
                    // Exhibitor data from session
                    $exhibitorDataSession = $submittedData['exhibitor_data'] ?? [];
                    $exhibitorName = $exhibitorDataSession['name'] ?? $allData['exhibitor_name'] ?? '';
                    $exhibitorAddress = $exhibitorDataSession['address'] ?? $allData['exhibitor_address'] ?? '';
                    $exhibitorCity = $exhibitorDataSession['city'] ?? $allData['exhibitor_city'] ?? '';
                    $exhibitorStateId = $exhibitorDataSession['state_id'] ?? $allData['exhibitor_state_id'] ?? null;
                    $exhibitorState = $exhibitorStateId ? (\App\Models\State::find($exhibitorStateId)->name ?? 'N/A') : 'N/A';
                    $exhibitorCountryId = $exhibitorDataSession['country_id'] ?? $allData['exhibitor_country_id'] ?? null;
                    $exhibitorCountry = $exhibitorCountryId ? (\App\Models\Country::find($exhibitorCountryId)->name ?? 'N/A') : 'N/A';
                    $exhibitorPostal = $exhibitorDataSession['postal_code'] ?? $allData['exhibitor_postal_code'] ?? '';
                    $exhibitorPhone = $submittedData['exhibitor_telephone'] ?? '';
                    $exhibitorWebsite = $exhibitorDataSession['website'] ?? $allData['exhibitor_website'] ?? '';
                    $exhibitorEmail = $exhibitorDataSession['email'] ?? $allData['exhibitor_email'] ?? '';
                    
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
            <div class="alert alert-success mb-4">
                <h5 class="mb-2"><i class="fas fa-check-circle"></i> Application Created Successfully</h5>
                <p class="mb-0">
                    <strong>Application ID:</strong> {{ $application->application_id }}<br>
                    Please review your details below and proceed to payment.
                </p>
            </div>
            @elseif($hasDraft || $hasSubmittedData)
            {{-- Review Before Submission --}}
            <div class="alert alert-info mb-4">
                <h5 class="mb-2"><i class="fas fa-info-circle"></i> Review Your Details</h5>
                <p class="mb-0">
                    Please review all details below. Click "Proceed to Payment" to finalize your registration.
                </p>
            </div>
            @else
            <div class="alert alert-danger mb-4">
                <strong>Error:</strong> No data found. Please submit the form again.
            </div>
            @endif

            {{-- Booth & Exhibition Details --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-cube"></i>
                    Booth & Exhibition Details
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Booth Space</td>
                        <td class="value-cell"><strong>{{ $boothSpace ?: 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Booth Size</td>
                        <td class="value-cell">{{ $boothSize ?: 'N/A' }}@if($boothSize) sqm @endif</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Sector</td>
                        <td class="value-cell">{{ $sector ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Subsector</td>
                        <td class="value-cell">{{ $subsector ?: 'N/A' }}</td>
                    </tr>
                    @if($otherSector)
                    <tr>
                        <td class="label-cell">Other Sector Name</td>
                        <td class="value-cell">{{ $otherSector }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label-cell">Category</td>
                        <td class="value-cell">{{ $category ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Sales Executive Name</td>
                        <td class="value-cell">{{ $salesExecutiveName ?: 'N/A' }}</td>
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
                        @if($billingData['tax_no'])
                        <tr>
                            <td class="label-cell">Tax Number</td>
                            <td class="value-cell">{{ $billingData['tax_no'] }}</td>
                        </tr>
                        @endif
                   
                </table>
            </div>

            {{-- Billing Information --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-building"></i>
                    Billing Information
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Company Name</td>
                        <td class="value-cell"><strong>{{ $billingCompany ?: 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Email</td>
                        <td class="value-cell">{{ $billingEmail ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Address</td>
                        <td class="value-cell">{{ $billingAddress ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">City</td>
                        <td class="value-cell">{{ $billingCity ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">State</td>
                        <td class="value-cell">{{ $billingState }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Postal Code</td>
                        <td class="value-cell">{{ $billingPostal ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Country</td>
                        <td class="value-cell">{{ $billingCountry }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Telephone</td>
                        <td class="value-cell">{{ $billingPhone ?: 'N/A' }}</td>
                    </tr>
                    @if($billingWebsite)
                    <tr>
                        <td class="label-cell">Website</td>
                        <td class="value-cell"><a href="{{ $billingWebsite }}" target="_blank">{{ $billingWebsite }}</a></td>
                    </tr>
                    @endif
                </table>
            </div>

            {{-- Exhibitor Information --}}
            @if(isset($exhibitorName) && !empty($exhibitorName))
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
                        <td class="value-cell">{{ $exhibitorState ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Postal Code</td>
                        <td class="value-cell">{{ $exhibitorPostal ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Country</td>
                        <td class="value-cell">{{ $exhibitorCountry ?? 'N/A' }}</td>
                    </tr>
                    @if(isset($exhibitorPhone) && !empty($exhibitorPhone))
                    <tr>
                        <td class="label-cell">Telephone</td>
                        <td class="value-cell">{{ $exhibitorPhone }}</td>
                    </tr>
                    @endif
                    @if(isset($exhibitorWebsite) && !empty($exhibitorWebsite))
                    <tr>
                        <td class="label-cell">Website</td>
                        <td class="value-cell"><a href="{{ $exhibitorWebsite }}" target="_blank">{{ $exhibitorWebsite }}</a></td>
                    </tr>
                    @endif
                    @if(isset($exhibitorEmail) && !empty($exhibitorEmail))
                    <tr>
                        <td class="label-cell">Email</td>
                        <td class="value-cell">{{ $exhibitorEmail }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            @endif

            {{-- Contact Person Details --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-user"></i>
                    Contact Person Details
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Name</td>
                        <td class="value-cell"><strong>{{ $contactTitle }} {{ $contactFirstName }} {{ $contactLastName }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Designation</td>
                        <td class="value-cell">{{ $contactDesignation ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Email</td>
                        <td class="value-cell">{{ $contactEmail ?: 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Mobile</td>
                        <td class="value-cell">{{ $contactMobile ?: 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Pricing Summary --}}
            @if($pricing)
            <div class="price-section">
                <h4 class="section-title">
                    <i class="fas fa-calculator"></i>
                    Pricing Summary
                </h4>
                @php
                    $currencySymbol = ($currency ?? 'INR') === 'USD' ? '$' : '₹';
                    $priceFormat = ($currency ?? 'INR') === 'USD' ? 2 : 2; // 2 decimals for both
                @endphp
                <table class="price-table">
                    <tr>
                        <td class="label-cell">Base Price</td>
                        <td class="value-cell">{{ $currencySymbol }}{{ number_format($pricing['base_price'], $priceFormat) }}</td>
                    </tr>
                    {{-- @if($pricing['gst_amount'])
                    <tr>
                        <td class="label-cell">GST ({{ $pricing['gst_rate'] }}%)</td>
                        <td class="value-cell">{{ $currencySymbol }}{{ number_format($pricing['gst_amount'], $priceFormat) }}</td>
                    </tr> 
                    @endif--}}
                    
                    @if($pricing['cgst_amount'])
                    <tr>
                        <td class="label-cell">CGST ({{ $pricing['cgst_rate'] }}%)</td>
                        <td class="value-cell">{{ $currencySymbol }}{{ number_format($pricing['cgst_amount'], $priceFormat) }}</td>
                    </tr>
                    @endif
                    @if($pricing['sgst_amount'])
                    <tr>
                        <td class="label-cell">SGST ({{ $pricing['sgst_rate'] }}%)</td>
                        <td class="value-cell">{{ $currencySymbol }}{{ number_format($pricing['sgst_amount'], $priceFormat) }}</td>
                    </tr>
                    @endif
                    @if($pricing['igst_amount'])
                    <tr>
                        <td class="label-cell">IGST ({{ $pricing['igst_rate'] }}%)</td>
                        <td class="value-cell">{{ $currencySymbol }}{{ number_format($pricing['igst_amount'], $priceFormat) }}</td>
                    </tr>
                    @endif
                    @if($pricing['processing_charges'])
                    <tr>
                        <td class="label-cell">Processing Charges ({{ $pricing['processing_rate'] }}%)</td>
                        <td class="value-cell">{{ $currencySymbol }}{{ number_format($pricing['processing_charges'], $priceFormat) }}</td>
                    </tr>
                    @endif
                    <tr class="total-row">
                        <td class="label-cell" style="background: var(--primary-color); color: white;">Total Amount</td>
                        <td class="value-cell" style="background: var(--primary-color); color: white;">{{ $currencySymbol }}{{ number_format($pricing['total_price'], $priceFormat) }}</td>
                    </tr>
                </table>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('exhibitor-registration.register', ['currency' => ($currency ?? 'INR') === 'USD' ? 'int' : 'ind']) }}" class="btn btn-outline-danger fs-6">
                    <i class="fas fa-arrow-left me-2"></i>
                    Edit Details
                </a>
                @if($hasApplication)
                    <a href="{{ route('exhibitor-registration.payment', $application->application_id) }}" class="btn btn-success fs-6">
                        <i class="fas fa-arrow-right me-2"></i>
                        Proceed to Payment
                    </a>
                @elseif($hasDraft || $hasSubmittedData)
                    <button type="button" class="btn btn-success fs-6" id="proceedToPaymentBtn">
                        <i class="fas fa-arrow-right me-2"></i>
                        Proceed to Payment
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
        /* margin-bottom: 2rem; */
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
