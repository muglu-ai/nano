@extends('layouts.startup-zone')

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

            {{--Booth Details --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Booth Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Booth Space:</strong><br>
                            {{ $application->stall_category ?? $draft->stall_category ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Booth Type:</strong><br>
                            {{ $application->interested_sqm ?? $draft->interested_sqm ?? 'N/A' }}
                        </div>
                    </div>
                   
            </div>

            {{-- Billing Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Billing Information</h4>
                </div>
                <div class="card-body">
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
                            $billingPhone = $billingDetail->phone ?? 'N/A';
                            $billingWebsite = 'N/A';
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Company Name:</strong><br>
                            {{ $billingCompany }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong><br>
                            {{ $billingEmail }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Address:</strong><br>
                            {{ $billingAddress }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>City:</strong><br>
                            {{ $billingCity }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>State:</strong><br>
                            {{ $billingState }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Postal Code:</strong><br>
                            {{ $billingPostalCode }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Country:</strong><br>
                            {{ $billingCountry }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Telephone:</strong><br>
                            {{ $billingPhone }}
                        </div>
                        @if($billingWebsite !== 'N/A')
                        <div class="col-md-6 mb-3">
                            <strong>Website:</strong><br>
                            <a href="{{ $billingWebsite }}" target="_blank">{{ $billingWebsite }}</a>
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <strong>Certificate:</strong><br>
                            @if($billingCertificatePath && $billingCertificatePath !== 'N/A')
                                <a href="{{ asset('storage/' . $billingCertificatePath) }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-file-pdf"></i> View Certificate
                                </a>
                            @else
                                <span class="text-muted">No certificate uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Exhibitor Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Exhibitor Information</h4>
                </div>
                <div class="card-body">
                    @php
                        if (isset($application)) {
                            // From application (after creation)
                            $exhibitorName = $application->company_name ?? 'N/A';
                            $exhibitorEmail = $application->company_email ?? 'N/A';
                            $exhibitorAddress = $application->address ?? 'N/A';
                            // Handle city - could be ID or name string
                            $exhibitorCity = 'N/A';
                            if ($application->city_id) {
                                if (is_numeric($application->city_id)) {
                                    $city = \App\Models\City::find($application->city_id);
                                    $exhibitorCity = $city ? $city->name : $application->city_id;
                                } else {
                                    $exhibitorCity = $application->city_id; // It's already a city name
                                }
                            }
                            $exhibitorState = $application->state ? $application->state->name : 'N/A';
                            $exhibitorCountry = $application->country ? $application->country->name : 'N/A';
                            $exhibitorPostalCode = $application->postal_code ?? 'N/A';
                            $exhibitorPhone = $application->landline ?? 'N/A';
                            $exhibitorWebsite = $application->website ?? 'N/A';
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Name of Exhibitor:</strong><br>
                            {{ $exhibitorName }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Company Email:</strong><br>
                            {{ $exhibitorEmail }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Address:</strong><br>
                            {{ $exhibitorAddress }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>City:</strong><br>
                            {{ $exhibitorCity }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>State:</strong><br>
                            {{ $exhibitorState }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Postal Code:</strong><br>
                            {{ $exhibitorPostalCode }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Country:</strong><br>
                            {{ $exhibitorCountry }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Telephone:</strong><br>
                            {{ $exhibitorPhone }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Website:</strong><br>
                            @if($exhibitorWebsite !== 'N/A')
                                <a href="{{ $exhibitorWebsite }}" target="_blank">{{ $exhibitorWebsite }}</a>
                            @else
                                N/A
                            @endif
                        </div>
                        @if($companyAge)
                        <div class="col-md-6 mb-3">
                            <strong>Company Age:</strong><br>
                            {{ $companyAge }} Year{{ $companyAge > 1 ? 's' : '' }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Tax Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tax Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>GST Status:</strong><br>
                            {{ (($application->gst_compliance ?? $draft->gst_compliance ?? false) ? 'Registered' : 'Unregistered') }}
                        </div>
                        @if(($application->gst_compliance ?? $draft->gst_compliance ?? false))
                        <div class="col-md-6 mb-3">
                            <strong>GST Number:</strong><br>
                            {{ $application->gst_no ?? $draft->gst_no ?? 'N/A' }}
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <strong>PAN Number:</strong><br>
                            {{ $application->pan_no ?? $draft->pan_no ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sector Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Sector Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Sector:</strong><br>
                            @if(isset($application))
                                {{ \DB::table('sectors')->where('id', $application->sector_id)->value('name') ?? 'N/A' }}
                            @elseif(isset($draft) && $draft->sector_id)
                                {{ \DB::table('sectors')->where('id', $draft->sector_id)->value('name') ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Subsector:</strong><br>
                            @if(isset($application) && $application->subSector)
                                {{ $application->subSector }}
                            @elseif(isset($draft) && $draft->subSector)
                                {{ $draft->subSector }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Person Details --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Contact Person Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($contact))
                            <div class="col-md-6 mb-3">
                                <strong>Name:</strong><br>
                                {{ $contact->salutation ?? '' }} {{ $contact->first_name }} {{ $contact->last_name }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Designation:</strong><br>
                                {{ $contact->job_title ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong><br>
                                {{ $contact->email }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Mobile:</strong><br>
                                {{ $contact->contact_number }}
                            </div>
                        @elseif(isset($draft) && $draft->contact_data)
                            <div class="col-md-6 mb-3">
                                <strong>Name:</strong><br>
                                {{ ($draft->contact_data['title'] ?? '') }} {{ $draft->contact_data['first_name'] ?? '' }} {{ $draft->contact_data['last_name'] ?? '' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Designation:</strong><br>
                                {{ $draft->contact_data['designation'] ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong><br>
                                {{ $draft->contact_data['email'] ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Mobile:</strong><br>
                                @php
                                    $mobile = $draft->contact_data['mobile'] ?? null;
                                    $countryCode = $draft->contact_data['country_code'] ?? '91';
                                    
                                    // Mobile is stored as "91-9806575432" (country_code-national_number)
                                    // Extract just the national number to avoid duplicate country code
                                    if ($mobile && strpos($mobile, '-') !== false) {
                                        // Format: "91-9806575432" -> display as "+91 9806575432"
                                        // Extract only the national number part (after the hyphen)
                                        $parts = explode('-', $mobile, 2);
                                        if (count($parts) == 2) {
                                            // Use the country code from the mobile field itself, or fallback to country_code
                                            $mobileCountryCode = $parts[0];
                                            $nationalNumber = $parts[1];
                                            $displayMobile = '+' . $mobileCountryCode . ' ' . $nationalNumber;
                                        } else {
                                            // Fallback: if format is unexpected, just use country_code + mobile
                                            $displayMobile = '+' . $countryCode . ' ' . $mobile;
                                        }
                                    } elseif ($mobile) {
                                        // If no hyphen, assume it's just the national number
                                        $displayMobile = '+' . $countryCode . ' ' . $mobile;
                                    } else {
                                        $displayMobile = 'N/A';
                                    }
                                @endphp
                                {{ $displayMobile }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pricing Summary --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Pricing Summary</h4>
                </div>
                <div class="card-body">
                    @if(isset($invoice))
                        <table class="table table-bordered">
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
                    @elseif(isset($pricing))
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Base Price:</strong></td>
                                <td class="text-end">{{ $currency ?? $pricing['currency'] ?? 'INR' }} {{ number_format($pricing['base_price'], 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>GST (18%):</strong></td>
                                <td class="text-end">{{ $currency ?? $pricing['currency'] ?? 'INR' }} {{ number_format($pricing['gst'], 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Processing Charges ({{ $pricing['processing_rate'] }}%):</strong></td>
                                <td class="text-end">{{ $currency ?? $pricing['currency'] ?? 'INR' }} {{ number_format($pricing['processing_charges'], 2) }}</td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Total Amount:</strong></td>
                                <td class="text-end"><strong>{{ $currency ?? $pricing['currency'] ?? 'INR' }} {{ number_format($pricing['total'], 2) }}</strong></td>
                            </tr>
                        </table>
                    @endif
                </div>
            </div>

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
