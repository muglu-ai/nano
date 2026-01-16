@extends('layouts.startup-zone')

@section('title', 'Preview Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

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
            
            @if(isset($application))
                {{-- Application Preview (After Draft Restoration) --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-check-circle"></i> Application Created Successfully</h4>
                    </div>
                    <div class="card-body">
                        <p class="alert alert-info">
                            <strong>Application ID:</strong> {{ $application->application_id }}<br>
                            Please review your details below and proceed to payment.
                        </p>
                    </div>
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
                        <td class="value-cell"><strong>{{ $application->stall_category ?? $draft->stall_category ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Booth Size</td>
                        <td class="value-cell">{{ $application->interested_sqm ?? $draft->interested_sqm ?? 'N/A' }}</td>
                    </tr>
                    @if(isset($application->sector_id) || isset($draft->sector_id))
                    <tr>
                        <td class="label-cell">Sector</td>
                        <td class="value-cell">{{ $application->sector_id ?? $draft->sector_id ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    @if(isset($application->subSector) || isset($draft->subSector))
                    <tr>
                        <td class="label-cell">Subsector</td>
                        <td class="value-cell">{{ $application->subSector ?? $draft->subSector ?? 'N/A' }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            @php
                if (isset($application) && isset($billingDetail)) {
                    // From application (after creation)
                    $billingCompany = $billingDetail->billing_company ?? 'N/A';
                    $billingEmail = $billingDetail->email ?? 'N/A';
                    $billingAddress = $billingDetail->address ?? 'N/A';
                    // Handle city - could be ID or name string
                    $billingCity = 'N/A';
                    if ($billingDetail->city_id) {
                        if (is_numeric($billingDetail->city_id)) {
                            $city = \App\Models\City::find($billingDetail->city_id);
                            $billingCity = $city ? $city->name : $billingDetail->city_id;
                        } else {
                            $billingCity = $billingDetail->city_id; // It's already a city name
                        }
                    }
                    $billingState = $billingDetail->state_id ? (\App\Models\State::find($billingDetail->state_id)->name ?? 'N/A') : 'N/A';
                    $billingCountry = $billingDetail->country_id ? (\App\Models\Country::find($billingDetail->country_id)->name ?? 'N/A') : 'N/A';
                    $billingPostalCode = $billingDetail->postal_code ?? 'N/A';
                    // Get phone - check billingDetail first, then application, then billing_data from draft
                    $billingPhone = !empty($billingDetail->phone) ? $billingDetail->phone : (!empty($application->landline) ? $application->landline : 'N/A');
                    // Get website - check application first (billingDetail doesn't have website field), then billing_data from draft
                    $billingWebsite = !empty($application->website) ? $application->website : 'N/A';
                    // Certificate might be in billingDetail or application
                    $billingCertificatePath = $billingDetail->certificate_path ?? ($application->certificate_path ?? 'N/A');
                } elseif (isset($draft) && isset($draft->billing_data)) {
                    // From draft
                    $billingData = is_array($draft->billing_data) ? $draft->billing_data : json_decode($draft->billing_data, true);
                    $billingCompany = $billingData['company_name'] ?? 'N/A';
                    $billingEmail = $billingData['email'] ?? 'N/A';
                    $billingAddress = $billingData['address'] ?? 'N/A';
                    $billingCity = $billingData['city'] ?? 'N/A';
                    $billingState = $billingData['state_id'] ? (\App\Models\State::find($billingData['state_id'])->name ?? 'N/A') : 'N/A';
                    $billingCountry = $billingData['country_id'] ? (\App\Models\Country::find($billingData['country_id'])->name ?? 'N/A') : 'N/A';
                    $billingPostalCode = $billingData['postal_code'] ?? 'N/A';
                    $billingPhone = $billingData['telephone'] ?? 'N/A';
                    $billingWebsite = $billingData['website'] ?? 'N/A';
                    // Certificate is stored in draft->certificate_path directly, not in billing_data
                    // Check draft->certificate_path first, then billing_data as fallback
                    $billingCertificatePath = !empty($draft->certificate_path) ? $draft->certificate_path : ($billingData['certificate_path'] ?? 'N/A');
                } else {
                    $billingCompany = $billingEmail = $billingAddress = $billingCity = $billingState = $billingCountry = $billingPostalCode = $billingPhone = $billingWebsite = 'N/A';
                    // Check draft->certificate_path directly
                    $billingCertificatePath = isset($draft) && !empty($draft->certificate_path) ? $draft->certificate_path : 'N/A';
                }
            @endphp

            {{-- Billing Information --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-building"></i>
                    Billing Information
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Company Name</td>
                        <td class="value-cell"><strong>{{ $billingCompany }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Email</td>
                        <td class="value-cell">{{ $billingEmail }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Address</td>
                        <td class="value-cell">{{ $billingAddress }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">City</td>
                        <td class="value-cell">{{ $billingCity }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">State</td>
                        <td class="value-cell">{{ $billingState }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Postal Code</td>
                        <td class="value-cell">{{ $billingPostalCode }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Country</td>
                        <td class="value-cell">{{ $billingCountry }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Telephone</td>
                        <td class="value-cell">{{ $billingPhone }}</td>
                    </tr>
                    @if($billingWebsite !== 'N/A')
                    <tr>
                        <td class="label-cell">Website</td>
                        <td class="value-cell"><a href="{{ $billingWebsite }}" target="_blank">{{ $billingWebsite }}</a></td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label-cell">Certificate</td>
                        <td class="value-cell">
                            @if($billingCertificatePath && $billingCertificatePath !== 'N/A')
                                <a href="{{ asset('storage/' . $billingCertificatePath) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-file-pdf"></i> View Certificate
                                </a>
                            @else
                                <span class="text-muted">No certificate uploaded</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            @php
                if (isset($application)) {
                    // From application (after creation) - check exhibitorInfo first
                    $exhibitorInfo = \App\Models\ExhibitorInfo::where('application_id', $application->id)->first();
                    if ($exhibitorInfo) {
                        $exhibitorName = $exhibitorInfo->company_name ?? 'N/A';
                        $exhibitorEmail = $exhibitorInfo->email ?? 'N/A';
                        $exhibitorAddress = $exhibitorInfo->address ?? 'N/A';
                        $exhibitorCity = $exhibitorInfo->city ?? 'N/A';
                        $exhibitorState = $exhibitorInfo->state ?? 'N/A';
                        $exhibitorCountry = $exhibitorInfo->country ?? 'N/A';
                        $exhibitorPostalCode = $exhibitorInfo->zip_code ?? 'N/A';
                        $exhibitorPhone = $exhibitorInfo->telPhone ?? 'N/A';
                        $exhibitorWebsite = $exhibitorInfo->website ?? 'N/A';
                    } else {
                        // Fallback to application
                        $exhibitorName = $application->company_name ?? 'N/A';
                        $exhibitorEmail = $application->company_email ?? 'N/A';
                        $exhibitorAddress = $application->address ?? 'N/A';
                        $exhibitorCity = is_numeric($application->city_id) ? (\App\Models\City::find($application->city_id)->name ?? $application->city_id) : ($application->city_id ?? 'N/A');
                        $exhibitorState = $application->state ? $application->state->name : 'N/A';
                        $exhibitorCountry = $application->country ? $application->country->name : 'N/A';
                        $exhibitorPostalCode = $application->postal_code ?? 'N/A';
                        $exhibitorPhone = $application->landline ?? 'N/A';
                        $exhibitorWebsite = $application->website ?? 'N/A';
                    }
                    $companyAge = $application->companyYears ?? $application->how_old_startup ?? null;
                } elseif (isset($draft)) {
                    // From draft
                    $exhibitorData = isset($draft->exhibitor_data) ? (is_array($draft->exhibitor_data) ? $draft->exhibitor_data : json_decode($draft->exhibitor_data, true)) : null;
                    if ($exhibitorData && !empty($exhibitorData['name'])) {
                        $exhibitorName = $exhibitorData['name'] ?? 'N/A';
                        $exhibitorEmail = $exhibitorData['email'] ?? 'N/A';
                        $exhibitorAddress = $exhibitorData['address'] ?? 'N/A';
                        $exhibitorCity = $exhibitorData['city'] ?? 'N/A';
                        $exhibitorState = $exhibitorData['state_id'] ? (\App\Models\State::find($exhibitorData['state_id'])->name ?? 'N/A') : 'N/A';
                        $exhibitorCountry = $exhibitorData['country_id'] ? (\App\Models\Country::find($exhibitorData['country_id'])->name ?? 'N/A') : 'N/A';
                        $exhibitorPostalCode = $exhibitorData['postal_code'] ?? 'N/A';
                        $exhibitorPhone = $exhibitorData['telephone'] ?? 'N/A';
                        $exhibitorWebsite = $exhibitorData['website'] ?? 'N/A';
                    } else {
                        // Fallback to old draft fields
                        $exhibitorName = $draft->company_name ?? 'N/A';
                        $exhibitorEmail = $draft->company_email ?? 'N/A';
                        $exhibitorAddress = $draft->address ?? 'N/A';
                        $exhibitorCity = $draft->city_id ?? 'N/A';
                        $exhibitorState = $draft->state_id ? (\App\Models\State::find($draft->state_id)->name ?? 'N/A') : 'N/A';
                        $exhibitorCountry = $draft->country_id ? (\App\Models\Country::find($draft->country_id)->name ?? 'N/A') : 'N/A';
                        $exhibitorPostalCode = $draft->postal_code ?? 'N/A';
                        $exhibitorPhone = $draft->landline ?? 'N/A';
                        $exhibitorWebsite = $draft->website ?? 'N/A';
                    }
                    $companyAge = $draft->how_old_startup ?? null;
                } else {
                    $exhibitorName = $exhibitorEmail = $exhibitorAddress = $exhibitorCity = $exhibitorState = $exhibitorCountry = $exhibitorPostalCode = $exhibitorPhone = $exhibitorWebsite = 'N/A';
                    $companyAge = null;
                }
            @endphp

            {{-- Exhibitor Information --}}
            @if($exhibitorName !== 'N/A' || !empty($exhibitorData))
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
                        <td class="label-cell">Company Email</td>
                        <td class="value-cell">{{ $exhibitorEmail }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Address</td>
                        <td class="value-cell">{{ $exhibitorAddress }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">City</td>
                        <td class="value-cell">{{ $exhibitorCity }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">State</td>
                        <td class="value-cell">{{ $exhibitorState }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Postal Code</td>
                        <td class="value-cell">{{ $exhibitorPostalCode }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Country</td>
                        <td class="value-cell">{{ $exhibitorCountry }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Telephone</td>
                        <td class="value-cell">{{ $exhibitorPhone }}</td>
                    </tr>
                    @if($exhibitorWebsite !== 'N/A')
                    <tr>
                        <td class="label-cell">Website</td>
                        <td class="value-cell"><a href="{{ $exhibitorWebsite }}" target="_blank">{{ $exhibitorWebsite }}</a></td>
                    </tr>
                    @endif
                    @if($companyAge)
                    <tr>
                        <td class="label-cell">Company Age</td>
                        <td class="value-cell">{{ $companyAge }} Year{{ $companyAge > 1 ? 's' : '' }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            @endif

            {{-- Tax & Compliance Details --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Tax & Compliance Details
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">GST Status</td>
                        <td class="value-cell"><strong>{{ (($application->gst_compliance ?? $draft->gst_compliance ?? false) ? 'Registered' : 'Unregistered') }}</strong></td>
                    </tr>
                    @if(($application->gst_compliance ?? $draft->gst_compliance ?? false))
                    <tr>
                        <td class="label-cell">GST Number</td>
                        <td class="value-cell">{{ $application->gst_no ?? $draft->gst_no ?? 'N/A' }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="label-cell">PAN Number</td>
                        <td class="value-cell">{{ $application->pan_no ?? $draft->pan_no ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Contact Person Details --}}
            @php
                if (isset($contact)) {
                    $contactTitle = $contact->salutation ?? '';
                    $contactFirstName = $contact->first_name ?? '';
                    $contactLastName = $contact->last_name ?? '';
                    $contactDesignation = $contact->job_title ?? 'N/A';
                    $contactEmail = $contact->email ?? 'N/A';
                    $contactMobile = $contact->contact_number ?? 'N/A';
                } elseif (isset($draft) && $draft->contact_data) {
                    $contactData = is_array($draft->contact_data) ? $draft->contact_data : json_decode($draft->contact_data, true);
                    $contactTitle = $contactData['title'] ?? '';
                    $contactFirstName = $contactData['first_name'] ?? '';
                    $contactLastName = $contactData['last_name'] ?? '';
                    $contactDesignation = $contactData['designation'] ?? 'N/A';
                    $contactEmail = $contactData['email'] ?? 'N/A';
                    $mobile = $contactData['mobile'] ?? null;
                    $countryCode = $contactData['country_code'] ?? '91';
                    
                    // Mobile is stored as "91-9806575432" (country_code-national_number)
                    if ($mobile && strpos($mobile, '-') !== false) {
                        $parts = explode('-', $mobile, 2);
                        if (count($parts) == 2) {
                            $mobileCountryCode = $parts[0];
                            $nationalNumber = $parts[1];
                            $contactMobile = '+' . $mobileCountryCode . ' ' . $nationalNumber;
                        } else {
                            $contactMobile = '+' . $countryCode . ' ' . $mobile;
                        }
                    } elseif ($mobile) {
                        $contactMobile = '+' . $countryCode . ' ' . $mobile;
                    } else {
                        $contactMobile = 'N/A';
                    }
                } else {
                    $contactTitle = $contactFirstName = $contactLastName = $contactDesignation = $contactEmail = $contactMobile = 'N/A';
                }
            @endphp
            @if($contactFirstName !== 'N/A' || isset($contact))
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-user"></i>
                    Contact Person Details
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Name</td>
                        <td class="value-cell"><strong>{{ trim($contactTitle . ' ' . $contactFirstName . ' ' . $contactLastName) ?: 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Designation</td>
                        <td class="value-cell">{{ $contactDesignation }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Email</td>
                        <td class="value-cell">{{ $contactEmail }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Mobile</td>
                        <td class="value-cell">{{ $contactMobile }}</td>
                    </tr>
                </table>
            </div>
            @endif

            {{-- Pricing Summary --}}
            @if(isset($invoice) || isset($pricing))
            <div class="price-section">
                <h4 class="section-title">
                    <i class="fas fa-calculator"></i>
                    Pricing Summary
                </h4>
                <table class="price-table">
                    @if(isset($invoice))
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
                    @elseif(isset($pricing))
                    <tr>
                        <td class="label-cell">Base Price</td>
                        <td class="value-cell">{{ $currency ?? $pricing['currency'] ?? 'INR' }} {{ number_format($pricing['base_price'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">GST ({{ $pricing['gst_rate'] ?? 18 }}%)</td>
                        <td class="value-cell">{{ $currency ?? $pricing['currency'] ?? 'INR' }} {{ number_format($pricing['gst'], 2) }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Processing Charges ({{ $pricing['processing_rate'] }}%)</td>
                        <td class="value-cell">{{ $currency ?? $pricing['currency'] ?? 'INR' }} {{ number_format($pricing['processing_charges'], 2) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td class="label-cell">Total Amount</td>
                        <td class="value-cell">{{ $currency ?? $pricing['currency'] ?? 'INR' }} {{ number_format($pricing['total'], 2) }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between">
                @if(isset($application))
                    <a href="{{ route('startup-zone.register', isset($hasTV) && $hasTV ? ['tv' => '1'] : []) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Edit Details
                    </a>
                    <a href="{{ route('startup-zone.payment', $application->application_id) }}" class="btn btn-success btn-lg">
                        Proceed to Payment <i class="fas fa-arrow-right"></i>
                    </a>
                @else
                    <a href="{{ route('startup-zone.register', isset($hasTV) && $hasTV ? ['tv' => '1'] : []) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Edit Details
                    </a>
                    <button type="button" class="btn btn-success btn-lg" id="confirmAndProceed">
                        Proceed to Payment <i class="fas fa-arrow-right"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@if(!isset($application))
<script>
document.getElementById('confirmAndProceed')?.addEventListener('click', function() {
    const button = this;
    const originalText = button.innerHTML;
    
    // Disable button and show loading state
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    
    // Get form data from the form page (if available via session or hidden fields)
    // Since we're on preview page, we'll send a POST request
    // The backend will use the latest session data which was saved during submitForm
    const formData = new FormData();
    
    // Restore draft to application
    fetch('{{ route("startup-zone.restore-draft") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw { message: data.message || 'Failed to create application', errors: data.errors };
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            // Re-enable button
            button.disabled = false;
            button.innerHTML = originalText;
            
            // Display validation errors if any
            let errorMsg = data.message || 'Failed to create application';
            if (data.errors) {
                const errorList = Object.values(data.errors).flat().join('\\n');
                errorMsg += '\\n\\n' + errorList;
            }
            alert('Error: ' + errorMsg);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Re-enable button
        button.disabled = false;
        button.innerHTML = originalText;
        
        let errorMsg = 'An error occurred. Please try again.';
        if (error.errors) {
            const errorList = Object.values(error.errors).flat().join('\\n');
            errorMsg += '\\n\\n' + errorList;
        } else if (error.message) {
            errorMsg = error.message;
        }
        alert(errorMsg);
    });
});
</script>
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
