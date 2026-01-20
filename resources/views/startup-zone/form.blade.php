@extends('layouts.startup-zone')

@section('title', 'Startup Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/custom.css') }}">
<style>
   

    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e0e0e0;
    }
    .form-container {padding: 0 !important;}
</style>
@endpush

@section('content')
<div class="form-card">
    {{-- Form Header --}}
    <div class="form-header">
        <h2><i class="fas fa-building"></i> Startup Zone Registration Form</h2>
        <p>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</p>
    </div>

    <div class="form-body">
        {{-- Step Indicator --}}
        <div class="progress-container">
            <div class="step-indicator">
                <div class="step-item active">
                    <div class="step-number">1</div>
                    <div class="step-label">Exhibitor Details</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-label">Preview Details</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-label">Payment</div>
                </div>
            </div>

            {{-- Progress Bar --}}
            <!-- <div class="progress-bar-custom" style="position: relative; display: flex; align-items: center;">
                <div class="progress-fill" id="progressBar" style="width: 33%; position: relative; overflow: visible;">
                    <span id="progressText" style="position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); color: white; font-size: 0.75rem; font-weight: 600; white-space: nowrap; text-shadow: 0 1px 2px rgba(0,0,0,0.3);">Step 1 of 3</span>
                </div>
            </div> -->
        </div>

        {{-- Auto-save Indicator --}}
        <div id="autoSaveIndicator" class="alert alert-info d-none" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 150px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); padding: 12px 20px; border-radius: 5px; transition: opacity 0.3s ease;">
            <i class="fas fa-spinner fa-spin"></i> <span>Saving...</span>
        </div>

        {{-- Form Container --}}
        <form id="startupZoneForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="session_id" value="{{ session()->getId() }}">
                {{-- Association Pricing Display --}}
                <div id="associationInfo" class="alert alert-success d-none mb-4">
                    <h5 id="associationName"></h5>
                    <p id="associationPrice"></p>
                </div>
                {{-- -Entitlement --}}
                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-cube"></i> Entitlement</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>TYPE</th>
                                    <th>Price</th>
                                    <th>Special Offer</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Startup Booth / POD.</td>
                                    @if($hasTV)
                                    <td ><s>₹ 60000</s></td>
                                    <td>₹ 37500</td>
                                    @elseif(!$hasTV)
                                    <td><s>₹ 52000</s></td>
                                    <td>₹ 30000</td>
                                    @endif

                                </tr>
                            </tbody>
                        </table>
                        <ul >
                            <li>Startup Booth / POD.</li>
                            {{-- passing the tv parameter in the URL will show the TV screen in the form --}}
                            @if($hasTV)
                            <li>43" TV Screen.</li>
                            @endif
                            {{-- end passing the tv parameter in the URL will show the TV screen in the form --}}
                            <li>One Standard Delegate Registration.</li>
                            <li>Two Exhibitor Badges.</li>
                            <li>Listing of Organization in the e-directory.</li>
                        </ul>
                       
                       
                    </div>
                    <div class="col-md-6">
                        @if($hasTV)
                        <img src="{{ asset('asset/img/POD-V01.png') }}" alt="TV" class="img-fluid" style="width: 100%; height: 90%;">
                        @else
                        <img src="{{ asset('asset/img/POD-V02.png') }}" alt="TV" class="img-fluid" style="width: 100%; height: 90%;">
                        @endif
                    </div>
                </div>

                {{-- Booth Information --}}
                <div class="form-section">
                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-cube"></i> Booth Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="stall_category" class="form-label">Booth Space <span class="text-danger">*</span></label>
                        <select class="form-select" id="stall_category" name="stall_category" required>
                            <option value="">Select Booth Space</option>
                            <option value="Startup Booth" {{ ($draft->stall_category ?? '') == 'Startup Booth' ? 'selected' : '' }}>Startup Booth</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="interested_sqm" class="form-label">Booth Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="interested_sqm" name="interested_sqm" required>
                            <option value="">Select Booth Type</option>
                            @if($hasTV)
                            <option value="POD with TV" {{ ($draft->interested_sqm ?? '') == 'POD with TV' ? 'selected' : '' }}>POD with TV</option>
                            @else
                            <option value="POD" {{ ($draft->interested_sqm ?? '') == 'POD' ? 'selected' : '' }}>POD</option>
                            @endif
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                </div>
                {{-- Sector Information --}}
                <div class="form-section">
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-industry"></i> Sector Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sector_id" class="form-label">Sector <span class="text-danger">*</span></label>
                        <select class="form-select" id="sector_id" name="sector_id" required>
                            <option value="">Select Sector</option>
                            @foreach($sectors as $sector)
                            <option value="{{ $sector }}" {{ ($draft->sector ?? '') == $sector ? 'selected' : '' }}>
                                {{ $sector }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="subSector" class="form-label">Subsector <span class="text-danger">*</span></label>
                        <select class="form-select" id="subSector" name="subSector" required>
                            <option value="">Select Subsector</option>
                            @foreach($subSectors as $subSector)
                            @php
                                // Use name as value since we're getting from config
                                $subSectorValue = is_object($subSector) ? $subSector->name : $subSector;
                                $subSectorName = is_object($subSector) ? $subSector->name : $subSector;
                                // Check if selected (handle both name and id for backward compatibility)
                                $isSelected = ($draft->subSector ?? '') == $subSectorValue || 
                                             (is_object($subSector) && ($draft->subSector ?? '') == $subSector->id);
                            @endphp
                            <option value="{{ $subSectorValue }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $subSectorName }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6" id="other_sector_container" style="display: none;">
                        <label for="type_of_business" class="form-label">Other Sector Name</label>
                        <input type="text" class="form-control" id="type_of_business" name="type_of_business" 
                               value="{{ $draft->type_of_business ?? '' }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                </div>
                {{-- Tax Information --}}
                <div class="form-section">
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-file-invoice-dollar"></i> Tax Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="gst_compliance" class="form-label">GST Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="gst_compliance" name="gst_compliance" required>
                            <option value="">Select GST Status</option>
                            <option value="1" {{ ($draft->gst_compliance ?? '') == '1' ? 'selected' : '' }}>Registered</option>
                            <option value="0" {{ ($draft->gst_compliance ?? '') == '0' ? 'selected' : '' }}>Unregistered</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6" id="gst_no_container" style="display: none;">
                        <label for="gst_no" class="form-label">GST Number <span class="text-danger" id="gst_required_indicator" style="display: none;">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="gst_no" name="gst_no" 
                                   value="{{ $draft->gst_no ?? '' }}" 
                                   pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}" 
                                   placeholder="">
                            <button type="button" class="btn btn-outline-primary" id="validateGstBtn">
                                <i class="fas fa-search"></i> Validate
                            </button>
                        </div>
                        <div id="gst_loading" class="d-none mt-1">
                            <small class="text-info"><i class="fas fa-spinner fa-spin"></i> Fetching details...</small>
                        </div>
                        <div id="gst_feedback" class="mt-1"></div>
                        <div class="invalid-feedback"></div>
                        <small class="form-text text-muted">Click "Validate" to auto-fill company details from GST database</small>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="pan_no" class="form-label">PAN Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pan_no" name="pan_no" 
                               value="{{ $draft->pan_no ?? '' }}" 
                               pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" 
                               maxlength="10" placeholder="" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                </div>
                {{-- Billing Information --}}
                <div class="form-section">
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-building"></i> Billing Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="billing_company_name" class="form-label">Billing Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="billing_company_name" name="billing_company_name" 
                               value="{{ isset($draft->billing_data['company_name']) ? $draft->billing_data['company_name'] : ($draft->company_name ?? '') }}" 
                               maxlength="100" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="certificate" class="form-label">Company Registration Certificate (PDF, Max 2MB) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="certificate" name="certificate" 
                               accept=".pdf" required>
                        @if($draft && isset($draft->certificate_path) && $draft->certificate_path)
                        <small class="text-muted">Current file: {{ basename($draft->certificate_path) }}</small>
                        @endif
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="how_old_startup" class="form-label">Company Age (Years) <span class="text-danger">*</span></label>
                        <select class="form-select" id="how_old_startup" name="how_old_startup" required>
                            <option value="">Select Age</option>
                            @for($i = 1; $i <= 7; $i++)
                            <option value="{{ $i }}" {{ ($draft->how_old_startup ?? '') == $i ? 'selected' : '' }}>{{ $i }} Year{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="billing_address" class="form-label">Billing Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="billing_address" name="billing_address" rows="2" required>{{ isset($draft->billing_data['address']) ? $draft->billing_data['address'] : ($draft->address ?? '') }}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="billing_country_id" class="form-label">Billing Country <span class="text-danger">*</span></label>
                        <select class="form-select" id="billing_country_id" name="billing_country_id" required>
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                            @php
                                $isSelected = (isset($draft->billing_data['country_id']) && $draft->billing_data['country_id'] == $country->id) || 
                                             (!isset($draft->billing_data['country_id']) && !isset($draft->country_id) && $country->code === 'IN') ||
                                             (isset($draft->country_id) && $draft->country_id == $country->id && !isset($draft->billing_data['country_id']));
                            @endphp
                            <option value="{{ $country->id }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="billing_state_id" class="form-label">Billing State <span class="text-danger">*</span></label>
                        <select class="form-select" id="billing_state_id" name="billing_state_id" required>
                            <option value="">Select State</option>
                            @if(isset($draft->billing_data['state_id']) && $draft->billing_data['state_id'])
                                @php
                                    $billingState = \App\Models\State::find($draft->billing_data['state_id']);
                                @endphp
                                @if($billingState)
                                    <option value="{{ $billingState->id }}" selected>{{ $billingState->name }}</option>
                                @endif
                            @elseif(isset($draft->state_id) && $draft->state_id)
                                @php
                                    $billingState = \App\Models\State::find($draft->state_id);
                                @endphp
                                @if($billingState)
                                    <option value="{{ $billingState->id }}" selected>{{ $billingState->name }}</option>
                                @endif
                            @endif
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                   
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="billing_city" class="form-label">Billing City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="billing_city" name="billing_city" 
                               value="{{ isset($draft->billing_data['city']) ? $draft->billing_data['city'] : ($draft->city_id ?? '') }}" 
                               maxlength="100" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="billing_postal_code" class="form-label">Billing Postal Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="billing_postal_code" name="billing_postal_code" 
                               value="{{ isset($draft->billing_data['postal_code']) ? $draft->billing_data['postal_code'] : ($draft->postal_code ?? '') }}" 
                               pattern="[0-9]{6}" maxlength="6" required>
                        <div class="invalid-feedback"></div>
                    </div>
                   
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="billing_telephone" class="form-label">Billing Telephone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="billing_telephone" name="billing_telephone" 
                               value="{{ isset($draft->billing_data['telephone']) ? $draft->billing_data['telephone'] : ($draft->landline ?? '') }}" 
                               placeholder="" required>
                        <input type="hidden" id="billing_telephone_country_code" name="billing_telephone_country_code">
                        <input type="hidden" id="billing_telephone_national" name="billing_telephone_national">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="billing_website" class="form-label">Website <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="billing_website" name="billing_website" 
                               value="{{ isset($draft->billing_data['website']) ? $draft->billing_data['website'] : ($draft->website ?? '') }}" 
                               placeholder="" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="billing_email" class="form-label">Billing Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="billing_email" name="billing_email" 
                               value="{{ isset($draft->billing_data['email']) ? $draft->billing_data['email'] : ($draft->company_email ?? '') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                </div>
                {{-- Exhibitor Information --}}
                <div class="form-section">
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-building"></i> Exhibitor Information</h5>
                <div class="row mb-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-primary btn-sm mb-3" id="copy_from_billing" style="color: #fff;">
                            <i class="fas fa-copy"></i> Click here to Copy from Billing Information
                        </button>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="exhibitor_name" class="form-label">Name of Exhibitor (Organisation Name) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="exhibitor_name" name="exhibitor_name" 
                               value="{{ isset($draft->exhibitor_data['name']) ? $draft->exhibitor_data['name'] : '' }}" 
                               maxlength="100" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="exhibitor_address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="exhibitor_address" name="exhibitor_address" rows="2" required>{{ isset($draft->exhibitor_data['address']) ? $draft->exhibitor_data['address'] : '' }}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="exhibitor_country_id" class="form-label">Country <span class="text-danger">*</span></label>
                        <select class="form-select" id="exhibitor_country_id" name="exhibitor_country_id" required>
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                            @php
                                $isSelected = (isset($draft->exhibitor_data['country_id']) && $draft->exhibitor_data['country_id'] == $country->id) || 
                                             (!isset($draft->exhibitor_data['country_id']) && $country->code === 'IN');
                            @endphp
                            <option value="{{ $country->id }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="exhibitor_state_id" class="form-label">State <span class="text-danger">*</span></label>
                        <select class="form-select" id="exhibitor_state_id" name="exhibitor_state_id" required>
                            <option value="">Select State</option>
                            @if(isset($draft->exhibitor_data['state_id']) && $draft->exhibitor_data['state_id'])
                                @php
                                    $exhibitorState = \App\Models\State::find($draft->exhibitor_data['state_id']);
                                @endphp
                                @if($exhibitorState)
                                    <option value="{{ $exhibitorState->id }}" selected>{{ $exhibitorState->name }}</option>
                                @endif
                            @endif
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="exhibitor_city" class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="exhibitor_city" name="exhibitor_city" 
                               value="{{ isset($draft->exhibitor_data['city']) ? $draft->exhibitor_data['city'] : '' }}" 
                               maxlength="100" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="exhibitor_postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="exhibitor_postal_code" name="exhibitor_postal_code" 
                               value="{{ isset($draft->exhibitor_data['postal_code']) ? $draft->exhibitor_data['postal_code'] : '' }}" 
                               pattern="[0-9]{6}" maxlength="6" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="exhibitor_telephone" class="form-label">Telephone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="exhibitor_telephone" name="exhibitor_telephone" 
                               value="{{ isset($draft->exhibitor_data['telephone']) ? $draft->exhibitor_data['telephone'] : '' }}" 
                               placeholder="" required>
                        <input type="hidden" id="exhibitor_telephone_country_code" name="exhibitor_telephone_country_code">
                        <input type="hidden" id="exhibitor_telephone_national" name="exhibitor_telephone_national">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="exhibitor_website" class="form-label">Website <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="exhibitor_website" name="exhibitor_website" 
                               value="{{ isset($draft->exhibitor_data['website']) ? $draft->exhibitor_data['website'] : '' }}" 
                               placeholder="https://example.com" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="exhibitor_email" class="form-label">Company Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="exhibitor_email" name="exhibitor_email" 
                               value="{{ isset($draft->exhibitor_data['email']) ? $draft->exhibitor_data['email'] : '' }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                </div>

                {{-- Contact Person Details --}}
                <div class="form-section">
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-user"></i> Contact Person Details</h5>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="contact_title" class="form-label">Title <span class="text-danger">*</span></label>
                        <select class="form-select" id="contact_title" name="contact_title" required>
                            <option value="">Select Title</option>
                            <option value="Mr." {{ (isset($draft->contact_data['title']) && $draft->contact_data['title'] == 'Mr.') ? 'selected' : '' }}>Mr.</option>
                            <option value="Mrs." {{ (isset($draft->contact_data['title']) && $draft->contact_data['title'] == 'Mrs.') ? 'selected' : '' }}>Mrs.</option>
                            <option value="Ms." {{ (isset($draft->contact_data['title']) && $draft->contact_data['title'] == 'Ms.') ? 'selected' : '' }}>Ms.</option>
                            <option value="Dr." {{ (isset($draft->contact_data['title']) && $draft->contact_data['title'] == 'Dr.') ? 'selected' : '' }}>Dr.</option>
                            <option value="Prof." {{ (isset($draft->contact_data['title']) && $draft->contact_data['title'] == 'Prof.') ? 'selected' : '' }}>Prof.</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="contact_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact_first_name" name="contact_first_name" 
                               value="{{ isset($draft->contact_data['first_name']) ? $draft->contact_data['first_name'] : '' }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-5">
                        <label for="contact_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact_last_name" name="contact_last_name" 
                               value="{{ isset($draft->contact_data['last_name']) ? $draft->contact_data['last_name'] : '' }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="contact_designation" class="form-label">Designation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact_designation" name="contact_designation" 
                               value="{{ isset($draft->contact_data['designation']) ? $draft->contact_data['designation'] : '' }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="contact_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                               value="{{ isset($draft->contact_data['email']) ? $draft->contact_data['email'] : '' }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="contact_mobile" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="contact_mobile" name="contact_mobile" 
                               value="{{ isset($draft->contact_data['mobile']) && isset($draft->contact_data['country_code']) ? '+' . $draft->contact_data['country_code'] . $draft->contact_data['mobile'] : '' }}" 
                               placeholder="" required>
                        <input type="hidden" id="contact_country_code" name="contact_country_code">
                        <input type="hidden" id="contact_mobile_national" name="contact_mobile_national">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                </div>
                {{-- Payment Mode --}}
                <div class="form-section">
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-credit-card"></i> Payment Mode</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="payment_mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                        <select class="form-select" id="payment_mode" name="payment_mode" required>
                            <option value="CCAvenue" {{ ($draft->payment_mode ?? '') == 'CCAvenue' ? 'selected' : '' }}>CCAvenue (Indian Payments)</option>
                            
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                </div>
                {{-- Promocode Section --}}
                <!-- <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-ticket-alt" style="display: none;"></i> Promocode (Optional)</h5> -->
                <div class="form-section" style="display: none;">
                <div class="row mb-3" style="display: none;">
                    <div class="col-md-6">
                        <label for="promocode" class="form-label">Promocode</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="promocode" name="promocode" 
                                   value="{{ $draft->promocode ?? '' }}" 
                                   placeholder="Enter promocode">
                            <button type="button" class="btn btn-outline-primary" id="validatePromocodeBtn">
                                Validate
                            </button>
                        </div>
                        <div id="promocodeFeedback" class="mt-2"></div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Note:</strong> After submitting this form, you will be redirected to preview your registration details before making payment.
                </div>
                </div>
                {{-- Google reCAPTCHA temporarily disabled --}}

                {{-- Submit Button --}}
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-primary btn-lg" id="submitForm">
                        <i class="fas fa-check fa-6 me-2"></i> Submit & Preview
                    </button>
                </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    /* Step indicator styles are now in the common layout */
    .step-content {
        animation: fadeIn 0.3s;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .form-control:invalid, .form-select:invalid {
        border-color: #dc3545;
    }
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    h5.border-bottom {
        color: var(--primary-color);
        font-weight: 600;
    }
    /* Responsive styles for step indicators are in the common layout */
</style>
@endpush

@push('scripts')
<script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize progress based on current step (Step 1 by default)
    updateProgressByStep(1);

    // Initialize intl-tel-input for mobile number
    const mobileInput = document.getElementById('contact_mobile');
    let iti = null;
    
    // Initialize intl-tel-input for landline/telephone number
    const landlineInput = document.getElementById('landline');
    let itiLandline = null;
    
    if (mobileInput) {
        // Get initial values from draft
        @php
            $initialCountry = 'in';
            $initialMobile = '';
            if (isset($draft->contact_data['country_code']) && isset($draft->contact_data['mobile'])) {
                // Find country code (e.g., 'in' from phonecode '91')
                $country = \App\Models\Country::where('phonecode', $draft->contact_data['country_code'])->first();
                if ($country) {
                    $initialCountry = strtolower($country->code);
                }
                $initialMobile = '+' . $draft->contact_data['country_code'] . $draft->contact_data['mobile'];
            }
        @endphp
        
        // Set initial value if exists
        @if(!empty($initialMobile))
        mobileInput.value = '{{ $initialMobile }}';
        @endif
        
        iti = window.intlTelInput(mobileInput, {
            initialCountry: '{{ $initialCountry }}',
            preferredCountries: ['in', 'us', 'gb'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            separateDialCode: true,
            nationalMode: false,
            autoPlaceholder: 'off' // Disable automatic placeholder
        });
        
        // Ensure placeholder is always empty
        mobileInput.placeholder = '';
        
        // Remove placeholder that intl-tel-input might add (multiple attempts)
        setTimeout(function() {
            mobileInput.placeholder = '';
        }, 100);
        setTimeout(function() {
            mobileInput.placeholder = '';
        }, 300);
        setTimeout(function() {
            mobileInput.placeholder = '';
        }, 500);
        
        // Also remove on focus/blur events
        mobileInput.addEventListener('focus', function() {
            if (this.placeholder) {
                this.placeholder = '';
            }
        });
        
        // Update hidden fields when phone number changes (make it accessible globally)
        window.updatePhoneFields = function() {
            if (!iti) return;
            
            const countryCode = iti.getSelectedCountryData().dialCode;
            const fullNumber = iti.getNumber();
            
            // Get national number (without country code) and remove all spaces and non-digits
            let nationalNumber = '';
            if (window.intlTelInputUtils && iti.isValidNumber()) {
                nationalNumber = iti.getNumber(window.intlTelInputUtils.numberFormat.NATIONAL);
                nationalNumber = nationalNumber.replace(/\s/g, '').replace(/[^0-9]/g, '').replace(/^0+/, ''); // Remove spaces, non-digits, and leading zeros
            } else {
                // Fallback: extract number from full number
                const dialCode = '+' + countryCode;
                if (fullNumber.startsWith(dialCode)) {
                    nationalNumber = fullNumber.substring(dialCode.length).replace(/\s/g, '').replace(/[^0-9]/g, '').replace(/^0+/, '');
                } else {
                    // If no country code prefix, try to extract from the input value
                    const inputValue = mobileInput.value.replace(/\s/g, ''); // Remove all spaces
                    nationalNumber = inputValue.replace(/^\+?\d{1,3}/, '').replace(/[^0-9]/g, ''); // Remove country code and non-digits
                }
            }
            
            // Remove all spaces and non-digits from national number (only digits allowed)
            nationalNumber = nationalNumber.replace(/\s+/g, '').replace(/[^0-9]/g, '');
            
            const countryCodeField = document.getElementById('contact_country_code');
            const mobileNationalField = document.getElementById('contact_mobile_national');
            if (countryCodeField) countryCodeField.value = countryCode.replace(/[^0-9]/g, ''); // Only digits for country code
            if (mobileNationalField) mobileNationalField.value = nationalNumber;
        };
        
        mobileInput.addEventListener('change', window.updatePhoneFields);
        mobileInput.addEventListener('blur', window.updatePhoneFields);
        mobileInput.addEventListener('countrychange', window.updatePhoneFields);
        
        // Filter out non-digit characters in real-time as user types
        mobileInput.addEventListener('input', function(e) {
            // Get the current value and filter out non-digits (keep +, spaces, and digits for intl-tel-input formatting)
            // The intl-tel-input library will handle formatting, but we need to prevent alphabetic characters
            let value = this.value;
            // Allow +, spaces, digits, and parentheses (for formatting) but remove alphabetic characters
            value = value.replace(/[a-zA-Z]/g, '');
            if (value !== this.value) {
                // If we removed characters, update the value
                const cursorPos = this.selectionStart;
                this.value = value;
                // Restore cursor position (adjust for removed characters)
                this.setSelectionRange(Math.max(0, cursorPos - 1), Math.max(0, cursorPos - 1));
                // Trigger update to sync hidden fields
                if (typeof window.updatePhoneFields === 'function') {
                    window.updatePhoneFields();
                }
            }
        });
        
        // Initialize hidden fields on load
        setTimeout(window.updatePhoneFields, 100);
    }
    
    // Initialize intl-tel-input for landline/telephone
    if (landlineInput) {
        // Get initial values from draft
        @php
            $initialLandlineCountry = 'in';
            $initialLandline = '';
            if (isset($draft->landline)) {
                $landlineValue = $draft->landline;
                // Check if we have country code stored separately in session
                if (isset($draft->landline_country_code) && !empty($draft->landline_country_code)) {
                    // Reconstruct full number with country code
                    $country = \App\Models\Country::where('phonecode', $draft->landline_country_code)->first();
                    if ($country) {
                        $initialLandlineCountry = strtolower($country->code);
                    }
                    $initialLandline = '+' . $draft->landline_country_code . $landlineValue;
                } elseif (strpos($landlineValue, '+') === 0) {
                    // Already has country code
                    $initialLandline = $landlineValue;
                    // Try to detect country from the number
                    if (strpos($landlineValue, '+91') === 0) {
                        $initialLandlineCountry = 'in';
                    } elseif (strpos($landlineValue, '+1') === 0) {
                        $initialLandlineCountry = 'us';
                    } elseif (strpos($landlineValue, '+44') === 0) {
                        $initialLandlineCountry = 'gb';
                    }
                } else {
                    // Just the number, default to India
                    $initialLandline = $landlineValue;
                }
            }
        @endphp
        
        // Set initial value if exists
        @if(!empty($initialLandline))
        landlineInput.value = '{{ $initialLandline }}';
        @endif
        
        itiLandline = window.intlTelInput(landlineInput, {
            initialCountry: '{{ $initialLandlineCountry }}',
            preferredCountries: ['in', 'us', 'gb'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            separateDialCode: true,
            nationalMode: false,
            autoPlaceholder: 'off' // Disable automatic placeholder
        });
        
        // Ensure placeholder is always empty
        landlineInput.placeholder = '';
        
        // Remove placeholder that intl-tel-input might add (multiple attempts)
        setTimeout(function() {
            landlineInput.placeholder = '';
        }, 100);
        setTimeout(function() {
            landlineInput.placeholder = '';
        }, 300);
        setTimeout(function() {
            landlineInput.placeholder = '';
        }, 500);
        
        // Also remove on focus/blur events
        landlineInput.addEventListener('focus', function() {
            if (this.placeholder) {
                this.placeholder = '';
            }
        });
        
        // Update hidden fields for landline when phone number changes
        window.updateLandlineFields = function() {
            if (!itiLandline) return;
            
            const countryCode = itiLandline.getSelectedCountryData().dialCode;
            const fullNumber = itiLandline.getNumber();
            
            // Get national number (without country code) and remove all spaces and non-digits
            let nationalNumber = '';
            if (window.intlTelInputUtils && itiLandline.isValidNumber()) {
                nationalNumber = itiLandline.getNumber(window.intlTelInputUtils.numberFormat.NATIONAL);
                nationalNumber = nationalNumber.replace(/\s/g, '').replace(/[^0-9]/g, '').replace(/^0+/, ''); // Remove spaces, non-digits, and leading zeros
            } else {
                // Fallback: extract number from full number
                const dialCode = '+' + countryCode;
                if (fullNumber.startsWith(dialCode)) {
                    nationalNumber = fullNumber.substring(dialCode.length).replace(/\s/g, '').replace(/[^0-9]/g, '').replace(/^0+/, '');
                } else {
                    // If no country code prefix, try to extract from the input value
                    const inputValue = landlineInput.value.replace(/\s/g, ''); // Remove all spaces
                    nationalNumber = inputValue.replace(/^\+?\d{1,3}/, '').replace(/[^0-9]/g, ''); // Remove country code and non-digits
                }
            }
            
            const countryCodeField = document.getElementById('landline_country_code');
            const landlineNationalField = document.getElementById('landline_national');
            if (countryCodeField) countryCodeField.value = countryCode.replace(/[^0-9]/g, ''); // Only digits for country code
            if (landlineNationalField) landlineNationalField.value = nationalNumber;
        };
        
        landlineInput.addEventListener('change', window.updateLandlineFields);
        landlineInput.addEventListener('blur', window.updateLandlineFields);
        landlineInput.addEventListener('countrychange', window.updateLandlineFields);
        
        // Filter out non-digit characters in real-time as user types
        landlineInput.addEventListener('input', function(e) {
            // Get the current value and filter out non-digits (keep +, spaces, and digits for intl-tel-input formatting)
            let value = this.value;
            // Allow +, spaces, digits, and parentheses (for formatting) but remove alphabetic characters
            value = value.replace(/[a-zA-Z]/g, '');
            if (value !== this.value) {
                // If we removed characters, update the value
                const cursorPos = this.selectionStart;
                this.value = value;
                // Restore cursor position (adjust for removed characters)
                this.setSelectionRange(Math.max(0, cursorPos - 1), Math.max(0, cursorPos - 1));
                // Trigger update to sync hidden fields
                if (typeof window.updateLandlineFields === 'function') {
                    window.updateLandlineFields();
                }
            }
        });
        
        // Initialize hidden fields on load
        setTimeout(window.updateLandlineFields, 100);
    }
    
    // Initialize intl-tel-input for exhibitor telephone
    const exhibitorTelephoneInput = document.getElementById('exhibitor_telephone');
    let exhibitorTelephoneIti = null;
    if (exhibitorTelephoneInput) {
        @php
            $initialExhibitorTelephoneCountry = 'in';
            $initialExhibitorTelephone = '';
            if (isset($draft->exhibitor_data['telephone']) && $draft->exhibitor_data['telephone']) {
                // Extract country code from telephone (format: 91-9801217815 or +919801217815)
                $telephoneValue = $draft->exhibitor_data['telephone'];
                if (strpos($telephoneValue, '-') !== false) {
                    $telephoneParts = explode('-', $telephoneValue);
                    if (count($telephoneParts) == 2) {
                        $telephoneCountryCode = $telephoneParts[0];
                        $telephoneNational = $telephoneParts[1];
                        $country = \App\Models\Country::where('phonecode', $telephoneCountryCode)->first();
                        if ($country) {
                            $initialExhibitorTelephoneCountry = strtolower($country->code);
                        }
                        $initialExhibitorTelephone = '+' . $telephoneCountryCode . $telephoneNational;
                    }
                } elseif (strpos($telephoneValue, '+') === 0) {
                    $initialExhibitorTelephone = $telephoneValue;
                }
            }
        @endphp
        
        @if(!empty($initialExhibitorTelephone))
        exhibitorTelephoneInput.value = '{{ $initialExhibitorTelephone }}';
        @endif
        
        exhibitorTelephoneIti = window.intlTelInput(exhibitorTelephoneInput, {
            initialCountry: '{{ $initialExhibitorTelephoneCountry }}',
            preferredCountries: ['in', 'us', 'gb'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            separateDialCode: true,
            nationalMode: false,
            autoPlaceholder: 'off'
        });
        
        exhibitorTelephoneInput.placeholder = '';
        setTimeout(function() {
            exhibitorTelephoneInput.placeholder = '';
        }, 100);
        setTimeout(function() {
            exhibitorTelephoneInput.placeholder = '';
        }, 300);
        setTimeout(function() {
            exhibitorTelephoneInput.placeholder = '';
        }, 500);
        
        exhibitorTelephoneInput.addEventListener('focus', function() {
            if (this.placeholder) {
                this.placeholder = '';
            }
        });
        
        // Update hidden fields for exhibitor telephone
        window.updateExhibitorTelephoneFields = function() {
            if (!exhibitorTelephoneIti) return;
            
            const countryCode = exhibitorTelephoneIti.getSelectedCountryData().dialCode;
            const fullNumber = exhibitorTelephoneIti.getNumber();
            
            let nationalNumber = '';
            if (window.intlTelInputUtils && exhibitorTelephoneIti.isValidNumber()) {
                nationalNumber = exhibitorTelephoneIti.getNumber(window.intlTelInputUtils.numberFormat.NATIONAL);
                nationalNumber = nationalNumber.replace(/\s/g, '').replace(/[^0-9]/g, '').replace(/^0+/, ''); // Remove spaces, non-digits, and leading zeros
            } else {
                const dialCode = '+' + countryCode;
                if (fullNumber.startsWith(dialCode)) {
                    nationalNumber = fullNumber.substring(dialCode.length).replace(/\s/g, '').replace(/[^0-9]/g, '').replace(/^0+/, '');
                } else {
                    const inputValue = exhibitorTelephoneInput.value.replace(/\s/g, '');
                    nationalNumber = inputValue.replace(/^\+?\d{1,3}/, '').replace(/[^0-9]/g, ''); // Remove country code and non-digits
                }
            }
            
            // Remove all spaces and non-digit characters from national number (only digits allowed)
            nationalNumber = nationalNumber.replace(/\s+/g, '').replace(/[^0-9]/g, '');
            
            const exhibitorCountryCodeField = document.getElementById('exhibitor_telephone_country_code');
            const exhibitorTelephoneNationalField = document.getElementById('exhibitor_telephone_national');
            if (exhibitorCountryCodeField) exhibitorCountryCodeField.value = countryCode.replace(/[^0-9]/g, ''); // Only digits for country code
            if (exhibitorTelephoneNationalField) exhibitorTelephoneNationalField.value = nationalNumber;
        };
        
        exhibitorTelephoneInput.addEventListener('change', window.updateExhibitorTelephoneFields);
        exhibitorTelephoneInput.addEventListener('blur', window.updateExhibitorTelephoneFields);
        exhibitorTelephoneInput.addEventListener('countrychange', window.updateExhibitorTelephoneFields);
        
        // Filter out non-digit characters in real-time as user types
        exhibitorTelephoneInput.addEventListener('input', function(e) {
            // Get the current value and filter out non-digits (keep +, spaces, and digits for intl-tel-input formatting)
            let value = this.value;
            // Allow +, spaces, digits, and parentheses (for formatting) but remove alphabetic characters
            value = value.replace(/[a-zA-Z]/g, '');
            if (value !== this.value) {
                // If we removed characters, update the value
                const cursorPos = this.selectionStart;
                this.value = value;
                // Restore cursor position (adjust for removed characters)
                this.setSelectionRange(Math.max(0, cursorPos - 1), Math.max(0, cursorPos - 1));
                // Trigger update to sync hidden fields
                if (typeof window.updateExhibitorTelephoneFields === 'function') {
                    window.updateExhibitorTelephoneFields();
                }
            }
        });
        
        setTimeout(window.updateExhibitorTelephoneFields, 100);
        
        // Make iti accessible globally for copy function
        window.exhibitorTelephoneIti = exhibitorTelephoneIti;
    }
    
    // Initialize intl-tel-input for billing telephone
    const billingTelephoneInput = document.getElementById('billing_telephone');
    let billingTelephoneIti = null;
    if (billingTelephoneInput) {
        @php
            $initialBillingTelephoneCountry = 'in';
            $initialBillingTelephone = '';
            if (isset($draft->billing_data['telephone']) && $draft->billing_data['telephone']) {
                $telephoneValue = $draft->billing_data['telephone'];
                if (strpos($telephoneValue, '-') !== false) {
                    $telephoneParts = explode('-', $telephoneValue);
                    if (count($telephoneParts) == 2) {
                        $telephoneCountryCode = $telephoneParts[0];
                        $telephoneNational = $telephoneParts[1];
                        $country = \App\Models\Country::where('phonecode', $telephoneCountryCode)->first();
                        if ($country) {
                            $initialBillingTelephoneCountry = strtolower($country->code);
                        }
                        $initialBillingTelephone = '+' . $telephoneCountryCode . $telephoneNational;
                    }
                } elseif (strpos($telephoneValue, '+') === 0) {
                    $initialBillingTelephone = $telephoneValue;
                }
            } elseif (isset($draft->landline) && $draft->landline) {
                $landlineValue = $draft->landline;
                if (strpos($landlineValue, '-') !== false) {
                    $landlineParts = explode('-', $landlineValue);
                    if (count($landlineParts) == 2) {
                        $landlineCountryCode = $landlineParts[0];
                        $landlineNational = $landlineParts[1];
                        $country = \App\Models\Country::where('phonecode', $landlineCountryCode)->first();
                        if ($country) {
                            $initialBillingTelephoneCountry = strtolower($country->code);
                        }
                        $initialBillingTelephone = '+' . $landlineCountryCode . $landlineNational;
                    }
                }
            }
        @endphp
        
        @if(!empty($initialBillingTelephone))
        billingTelephoneInput.value = '{{ $initialBillingTelephone }}';
        @endif
        
        billingTelephoneIti = window.intlTelInput(billingTelephoneInput, {
            initialCountry: '{{ $initialBillingTelephoneCountry }}',
            preferredCountries: ['in', 'us', 'gb'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            separateDialCode: true,
            nationalMode: false,
            autoPlaceholder: 'off'
        });
        
        billingTelephoneInput.placeholder = '';
        setTimeout(function() {
            billingTelephoneInput.placeholder = '';
        }, 100);
        setTimeout(function() {
            billingTelephoneInput.placeholder = '';
        }, 300);
        setTimeout(function() {
            billingTelephoneInput.placeholder = '';
        }, 500);
        
        billingTelephoneInput.addEventListener('focus', function() {
            if (this.placeholder) {
                this.placeholder = '';
            }
        });
        
        // Update hidden fields for billing telephone
        window.updateBillingTelephoneFields = function() {
            if (!billingTelephoneIti) return;
            
            const countryCode = billingTelephoneIti.getSelectedCountryData().dialCode;
            const fullNumber = billingTelephoneIti.getNumber();
            
            let nationalNumber = '';
            if (window.intlTelInputUtils && billingTelephoneIti.isValidNumber()) {
                nationalNumber = billingTelephoneIti.getNumber(window.intlTelInputUtils.numberFormat.NATIONAL);
                nationalNumber = nationalNumber.replace(/\s/g, '').replace(/[^0-9]/g, '').replace(/^0+/, ''); // Remove spaces, non-digits, and leading zeros
            } else {
                const dialCode = '+' + countryCode;
                if (fullNumber.startsWith(dialCode)) {
                    nationalNumber = fullNumber.substring(dialCode.length).replace(/\s/g, '').replace(/[^0-9]/g, '').replace(/^0+/, '');
                } else {
                    const inputValue = billingTelephoneInput.value.replace(/\s/g, '');
                    nationalNumber = inputValue.replace(/^\+?\d{1,3}/, '').replace(/[^0-9]/g, ''); // Remove country code and non-digits
                }
            }
            
            // Remove all spaces and non-digit characters from national number (only digits allowed)
            nationalNumber = nationalNumber.replace(/\s+/g, '').replace(/[^0-9]/g, '');
            
            const billingCountryCodeField = document.getElementById('billing_telephone_country_code');
            const billingTelephoneNationalField = document.getElementById('billing_telephone_national');
            if (billingCountryCodeField) billingCountryCodeField.value = countryCode.replace(/[^0-9]/g, ''); // Only digits for country code
            if (billingTelephoneNationalField) billingTelephoneNationalField.value = nationalNumber;
        };
        
        billingTelephoneInput.addEventListener('change', window.updateBillingTelephoneFields);
        billingTelephoneInput.addEventListener('blur', window.updateBillingTelephoneFields);
        billingTelephoneInput.addEventListener('countrychange', window.updateBillingTelephoneFields);
        
        // Filter out non-digit characters in real-time as user types
        billingTelephoneInput.addEventListener('input', function(e) {
            // Get the current value and filter out non-digits (keep +, spaces, and digits for intl-tel-input formatting)
            let value = this.value;
            // Allow +, spaces, digits, and parentheses (for formatting) but remove alphabetic characters
            value = value.replace(/[a-zA-Z]/g, '');
            if (value !== this.value) {
                // If we removed characters, update the value
                const cursorPos = this.selectionStart;
                this.value = value;
                // Restore cursor position (adjust for removed characters)
                this.setSelectionRange(Math.max(0, cursorPos - 1), Math.max(0, cursorPos - 1));
                // Trigger update to sync hidden fields
                if (typeof window.updateBillingTelephoneFields === 'function') {
                    window.updateBillingTelephoneFields();
                }
            }
        });
        
        setTimeout(window.updateBillingTelephoneFields, 100);
    }

    // Normalize website URL - add https:// if protocol is missing
    function normalizeWebsiteUrl(url) {
        if (!url) return url;
        
        url = url.trim();
        
        // If URL doesn't start with http:// or https://, add https://
        if (!/^https?:\/\//i.test(url)) {
            url = 'https://' + url;
        }
        
        return url;
    }
    
    const websiteInput = document.getElementById('website');
    if (websiteInput) {
        websiteInput.addEventListener('blur', function() {
            const currentValue = this.value.trim();
            if (currentValue && !currentValue.match(/^https?:\/\//i)) {
                const normalizedUrl = normalizeWebsiteUrl(currentValue);
                this.value = normalizedUrl;
                resetAutoSaveTimer();
            }
        });
        
        websiteInput.addEventListener('change', function() {
            const currentValue = this.value.trim();
            if (currentValue && !currentValue.match(/^https?:\/\//i)) {
                const normalizedUrl = normalizeWebsiteUrl(currentValue);
                this.value = normalizedUrl;
                resetAutoSaveTimer();
            }
        });
    }
    
    // Normalize exhibitor website URL
    const exhibitorWebsiteInput = document.getElementById('exhibitor_website');
    if (exhibitorWebsiteInput) {
        exhibitorWebsiteInput.addEventListener('blur', function() {
            const currentValue = this.value.trim();
            if (currentValue && !currentValue.match(/^https?:\/\//i)) {
                const normalizedUrl = normalizeWebsiteUrl(currentValue);
                this.value = normalizedUrl;
                resetAutoSaveTimer();
            }
        });
        
        exhibitorWebsiteInput.addEventListener('change', function() {
            const currentValue = this.value.trim();
            if (currentValue && !currentValue.match(/^https?:\/\//i)) {
                const normalizedUrl = normalizeWebsiteUrl(currentValue);
                this.value = normalizedUrl;
                resetAutoSaveTimer();
            }
        });
    }
    
    // Normalize billing website URL
    const billingWebsiteInput = document.getElementById('billing_website');
    if (billingWebsiteInput) {
        billingWebsiteInput.addEventListener('blur', function() {
            const currentValue = this.value.trim();
            if (currentValue && !currentValue.match(/^https?:\/\//i)) {
                const normalizedUrl = normalizeWebsiteUrl(currentValue);
                this.value = normalizedUrl;
                resetAutoSaveTimer();
            }
        });
        
        billingWebsiteInput.addEventListener('change', function() {
            const currentValue = this.value.trim();
            if (currentValue && !currentValue.match(/^https?:\/\//i)) {
                const normalizedUrl = normalizeWebsiteUrl(currentValue);
                this.value = normalizedUrl;
                resetAutoSaveTimer();
            }
        });
    }

    // Show/hide GST number field based on GST compliance
    const gstCompliance = document.getElementById('gst_compliance');
    const gstNoContainer = document.getElementById('gst_no_container');
    const gstNo = document.getElementById('gst_no');
    const gstLoading = document.getElementById('gst_loading');
    const gstFeedback = document.getElementById('gst_feedback');
    
    if (gstCompliance) {
        const gstRequiredIndicator = document.getElementById('gst_required_indicator');
        
        gstCompliance.addEventListener('change', function() {
            if (this.value === '1') {
                gstNoContainer.style.display = 'block';
                gstNo.setAttribute('required', 'required');
                if (gstRequiredIndicator) {
                    gstRequiredIndicator.style.display = 'inline';
                }
            } else {
                gstNoContainer.style.display = 'none';
                gstNo.removeAttribute('required');
                if (gstRequiredIndicator) {
                    gstRequiredIndicator.style.display = 'none';
                }
                gstNo.value = '';
                gstFeedback.innerHTML = '';
            }
            resetAutoSaveTimer();
        });
        
        // Initialize on load
        if (gstCompliance.value === '1') {
            gstNoContainer.style.display = 'block';
            gstNo.setAttribute('required', 'required');
            if (gstRequiredIndicator) {
                gstRequiredIndicator.style.display = 'inline';
            }
        }
    }
    
    // GST API integration - validate button click
    const validateGstBtn = document.getElementById('validateGstBtn');
    
    if (validateGstBtn && gstNo) {
        validateGstBtn.addEventListener('click', function() {
            const gstNumber = gstNo.value.trim().toUpperCase();
            
            // Validate GST format
            const gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
            
            if (!gstNumber) {
                gstFeedback.innerHTML = '<small class="text-danger">Please enter a GST number</small>';
                gstNo.focus();
                return;
            }
            
            if (!gstPattern.test(gstNumber)) {
                gstFeedback.innerHTML = '<small class="text-danger">Invalid GST format. Format: 22AAAAA0000A1Z5</small>';
                gstNo.focus();
                return;
            }
            
            // Disable button and show loading
            validateGstBtn.disabled = true;
            validateGstBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validating...';
            gstLoading.classList.remove('d-none');
            gstFeedback.innerHTML = '';
            
            // Fetch GST details
            fetch('{{ route("startup-zone.fetch-gst-details") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ gst_no: gstNumber })
            })
            .then(response => response.json())
            .then(data => {
                gstLoading.classList.add('d-none');
                validateGstBtn.disabled = false;
                validateGstBtn.innerHTML = '<i class="fas fa-search"></i> Validate';
                
                if (data.success) {
                    // Auto-fill billing company name and billing address
                    const billingCompanyNameField = document.getElementById('billing_company_name');
                    const billingAddressField = document.getElementById('billing_address');
                    const billingStateField = document.getElementById('billing_state_id');
                    const billingPostalCodeField = document.getElementById('billing_postal_code');
                    const panField = document.getElementById('pan_no');
                    const billingCityField = document.getElementById('billing_city');
                    
                    if (data.data.company_name && billingCompanyNameField) {
                        billingCompanyNameField.value = data.data.company_name;
                    }
                    
                    if (data.data.billing_address && billingAddressField) {
                        billingAddressField.value = data.data.billing_address;
                    }
                    
                    if (data.data.state_id && billingStateField) {
                        billingStateField.value = data.data.state_id;
                        // Trigger state change event to ensure proper handling
                        const changeEvent = new Event('change', { bubbles: true });
                        billingStateField.dispatchEvent(changeEvent);
                    }
                    
                    if (data.data.pincode && billingPostalCodeField) {
                        billingPostalCodeField.value = data.data.pincode;
                    }
                    
                    if (data.data.pan && panField) {
                        panField.value = data.data.pan;
                    }
                    
                    if (data.data.city && billingCityField) {
                        billingCityField.value = data.data.city;
                    }
                    
                    let successMsg = '<small class="text-success"><i class="fas fa-check"></i> GST details fetched successfully';
                    if (data.from_cache) {
                        successMsg += ' ';
                    }
                    // Only show remaining requests on the last API call (when 1 request remaining)
                    if (data.rate_limit_remaining !== undefined && data.rate_limit_remaining !== null && data.rate_limit_remaining === 1) {
                        successMsg += ' - ' + data.rate_limit_remaining + ' request remaining';
                    }
                    successMsg += '</small>';
                    gstFeedback.innerHTML = successMsg;
                    
                    resetAutoSaveTimer();
                } else {
                    let errorMsg = data.message || 'Failed to fetch GST details';
                    if (data.rate_limit_exceeded) {
                        const minutes = Math.ceil(data.reset_in_minutes || 10);
                        errorMsg = 'Rate limit exceeded. Please try again after ' + minutes + ' minutes, or fill the details manually.';
                    } else {
                        errorMsg += '. Please fill the details manually.';
                    }
                    gstFeedback.innerHTML = '<small class="text-danger">' + errorMsg + '</small>';
                }
            })
            .catch(error => {
                gstLoading.classList.add('d-none');
                validateGstBtn.disabled = false;
                validateGstBtn.innerHTML = '<i class="fas fa-search"></i> Validate';
                gstFeedback.innerHTML = '<small class="text-danger">Error fetching GST details. Please fill the details manually.</small>';
                console.error('GST API Error:', error);
            });
        });
    }

    // Show/hide other sector field
    const subSector = document.getElementById('subSector');
    const otherSectorContainer = document.getElementById('other_sector_container');
    
    if (subSector) {
        subSector.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.text.toLowerCase().includes('other')) {
                otherSectorContainer.style.display = 'block';
            } else {
                otherSectorContainer.style.display = 'none';
            }
            resetAutoSaveTimer();
        });
    }

    // Promocode validation
    const promocodeBtn = document.getElementById('validatePromocodeBtn');
    if (promocodeBtn) {
        promocodeBtn.addEventListener('click', validatePromocode);
    }

    // Form submission
    document.getElementById('submitForm')?.addEventListener('click', function() {
        if (validateForm()) {
            submitForm();
        }
    });

    // Billing country/state loading
    const billingCountrySelect = document.getElementById('billing_country_id');
    const billingStateSelect = document.getElementById('billing_state_id');
    
    function loadBillingStatesForCountry(countryId, preserveSelectedStateId = null) {
        if (!countryId) {
            billingStateSelect.innerHTML = '<option value="">Select State</option>';
            billingStateSelect.disabled = false;
            return;
        }
        
        billingStateSelect.innerHTML = '<option value="">Loading states...</option>';
        billingStateSelect.disabled = true;
        
        fetch('{{ route("get.states") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ country_id: countryId })
        })
        .then(response => response.json())
        .then(data => {
            billingStateSelect.innerHTML = '<option value="">Select State</option>';
            if (data && data.length > 0) {
                data.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.id;
                    option.textContent = state.name;
                    if (preserveSelectedStateId && preserveSelectedStateId == state.id) {
                        option.selected = true;
                    }
                    billingStateSelect.appendChild(option);
                });
            }
            billingStateSelect.disabled = false;
            resetAutoSaveTimer();
        })
        .catch(error => {
            console.error('Error loading billing states:', error);
            billingStateSelect.innerHTML = '<option value="">Error loading states</option>';
            billingStateSelect.disabled = false;
        });
    }
    
    if (billingCountrySelect && billingStateSelect) {
        // Load states on page load if country is already selected
        const initialBillingCountryId = billingCountrySelect.value;
        const initialBillingStateId = billingStateSelect.value;
        if (initialBillingCountryId) {
            loadBillingStatesForCountry(initialBillingCountryId, initialBillingStateId);
        }
        
        // Handle billing country change
        billingCountrySelect.addEventListener('change', function() {
            loadBillingStatesForCountry(this.value);
        });
    }

    // Exhibitor country/state loading
    const exhibitorCountrySelect = document.getElementById('exhibitor_country_id');
    const exhibitorStateSelect = document.getElementById('exhibitor_state_id');
    
    function loadExhibitorStatesForCountry(countryId, preserveSelectedStateId = null, callback = null) {
        if (!countryId) {
            exhibitorStateSelect.innerHTML = '<option value="">Select State</option>';
            exhibitorStateSelect.disabled = false;
            if (callback) callback();
            return Promise.resolve();
        }
        
        exhibitorStateSelect.innerHTML = '<option value="">Loading states...</option>';
        exhibitorStateSelect.disabled = true;
        
        return fetch('{{ route("get.states") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ country_id: countryId })
        })
        .then(response => response.json())
        .then(data => {
            exhibitorStateSelect.innerHTML = '<option value="">Select State</option>';
            if (data && data.length > 0) {
                data.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.id;
                    option.textContent = state.name;
                    if (preserveSelectedStateId && preserveSelectedStateId == state.id) {
                        option.selected = true;
                    }
                    exhibitorStateSelect.appendChild(option);
                });
            }
            exhibitorStateSelect.disabled = false;
            resetAutoSaveTimer();
            if (callback) callback();
        })
        .catch(error => {
            console.error('Error loading exhibitor states:', error);
            exhibitorStateSelect.innerHTML = '<option value="">Error loading states</option>';
            exhibitorStateSelect.disabled = false;
            if (callback) callback();
        });
    }
    
    if (exhibitorCountrySelect && exhibitorStateSelect) {
        // Load states on page load if country is already selected
        const initialExhibitorCountryId = exhibitorCountrySelect.value;
        const initialExhibitorStateId = exhibitorStateSelect.value;
        if (initialExhibitorCountryId) {
            loadExhibitorStatesForCountry(initialExhibitorCountryId, initialExhibitorStateId);
        }
        
        // Handle exhibitor country change
        exhibitorCountrySelect.addEventListener('change', function() {
            loadExhibitorStatesForCountry(this.value);
        });
    }

    // Copy from Billing Information to Exhibitor Information
    const copyFromBillingBtn = document.getElementById('copy_from_billing');
    if (copyFromBillingBtn) {
        copyFromBillingBtn.addEventListener('click', function() {
            // Copy company name
            const billingCompanyName = document.getElementById('billing_company_name')?.value || '';
            document.getElementById('exhibitor_name').value = billingCompanyName;
            
            // Copy address
            const billingAddress = document.getElementById('billing_address')?.value || '';
            document.getElementById('exhibitor_address').value = billingAddress;
            
            // Copy country
            const billingCountryId = document.getElementById('billing_country_id')?.value || '';
            const billingStateId = document.getElementById('billing_state_id')?.value || '';
            if (billingCountryId && exhibitorCountrySelect) {
                exhibitorCountrySelect.value = billingCountryId;
                
                // Load states and then copy state value after states are loaded
                loadExhibitorStatesForCountry(billingCountryId, null, function() {
                    // This callback runs after states are loaded
                    if (billingStateId && exhibitorStateSelect) {
                        exhibitorStateSelect.value = billingStateId;
                        resetAutoSaveTimer();
                    }
                });
            }
            
            // Copy city
            const billingCity = document.getElementById('billing_city')?.value || '';
            document.getElementById('exhibitor_city').value = billingCity;
            
            // Copy postal code
            const billingPostalCode = document.getElementById('billing_postal_code')?.value || '';
            document.getElementById('exhibitor_postal_code').value = billingPostalCode;
            
            // Copy telephone
            const billingTelephone = document.getElementById('billing_telephone')?.value || '';
            const exhibitorTelephone = document.getElementById('exhibitor_telephone');
            if (billingTelephone) {
                // Wait for exhibitorTelephoneIti to be initialized
                setTimeout(() => {
                    if (window.exhibitorTelephoneIti) {
                        window.exhibitorTelephoneIti.setNumber(billingTelephone);
                        if (typeof window.updateExhibitorTelephoneFields === 'function') {
                            window.updateExhibitorTelephoneFields();
                        }
                    } else {
                        exhibitorTelephone.value = billingTelephone;
                    }
                }, 100);
            }
            
            // Copy website
            const billingWebsite = document.getElementById('billing_website')?.value || '';
            document.getElementById('exhibitor_website').value = billingWebsite;
            
            // Copy email
            const billingEmail = document.getElementById('billing_email')?.value || '';
            document.getElementById('exhibitor_email').value = billingEmail;
            
            resetAutoSaveTimer();
        });
    }

    // Store form data in session after 30 seconds of inactivity (no database writes)
    let autoSaveTimer = null;
    const AUTO_SAVE_DELAY = 10000; // 10 seconds
    
    // Function to reset auto-save timer
    function resetAutoSaveTimer() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(() => {
            saveToSession(); // Actually save after 30 seconds of inactivity
        }, AUTO_SAVE_DELAY);
    }
    
    // Attach event listeners to all form fields
    const formFields = document.querySelectorAll('#startupZoneForm input, #startupZoneForm select, #startupZoneForm textarea');
    formFields.forEach(field => {
        // Skip country field as it has its own handler
        if (field.id === 'country_id') {
            return;
        }
        
        // Listen to input, change, and blur events
        field.addEventListener('input', resetAutoSaveTimer);
        field.addEventListener('change', resetAutoSaveTimer);
        field.addEventListener('blur', resetAutoSaveTimer);
    });

    function validateForm() {
        let isValid = true;
        const form = document.getElementById('startupZoneForm');
        const billingDifferent = document.getElementById('billing_different')?.checked;
        
        // Clear previous validation
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        // Get all required fields, but exclude billing fields if billing is not different
        let requiredFields = form.querySelectorAll('[required]');
        if (!billingDifferent) {
            // Remove billing fields from validation if billing is not different
            requiredFields = Array.from(requiredFields).filter(field => {
                return !field.id.startsWith('billing_');
            });
        }
        
        requiredFields.forEach(field => {
            // Skip hidden fields
            if (field.type === 'hidden') {
                return;
            }
            
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                const feedback = field.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = 'This field is required.';
                }
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
                
                // Additional validations
                if (field.type === 'email' && !isValidEmail(field.value)) {
                    field.classList.add('is-invalid');
                    const feedback = field.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Please enter a valid email address.';
                    }
                    isValid = false;
                }
                
                if (field.id === 'pan_no' && !isValidPAN(field.value)) {
                    field.classList.add('is-invalid');
                    const feedback = field.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Invalid PAN format. Format: ABCDE1234F';
                    }
                    isValid = false;
                }
                
                if (field.id === 'gst_no' && field.value && !isValidGST(field.value)) {
                    field.classList.add('is-invalid');
                    const feedback = field.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Invalid GST format.';
                    }
                    isValid = false;
                }
                
                if (field.id === 'postal_code' && !isValidPostalCode(field.value)) {
                    field.classList.add('is-invalid');
                    const feedback = field.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Postal code must be 6 digits.';
                    }
                    isValid = false;
                }
                
                if (field.id === 'contact_mobile') {
                    // Validate using intl-tel-input if available
                    if (iti) {
                        if (!iti.isValidNumber()) {
                            field.classList.add('is-invalid');
                            const feedback = field.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = 'Please enter a valid mobile number.';
                            }
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                            // Update hidden fields
                            if (typeof window.updatePhoneFields === 'function') {
                                window.updatePhoneFields();
                            }
                        }
                    } else if (field.value && !isValidMobile(field.value)) {
                        field.classList.add('is-invalid');
                        const feedback = field.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = 'Mobile number must be valid.';
                        }
                        isValid = false;
                    }
                }
                
                if (field.id === 'landline') {
                    // Validate using intl-tel-input if available
                    if (itiLandline) {
                        if (!itiLandline.isValidNumber()) {
                            field.classList.add('is-invalid');
                            const feedback = field.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = 'Please enter a valid telephone number.';
                            }
                            isValid = false;
                        } else {
                            field.classList.remove('is-invalid');
                            // Update hidden fields
                            if (typeof window.updateLandlineFields === 'function') {
                                window.updateLandlineFields();
                            }
                        }
                    } else if (field.value && field.value.trim().length < 5) {
                        field.classList.add('is-invalid');
                        const feedback = field.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = 'Please enter a valid telephone number.';
                        }
                        isValid = false;
                    }
                }
            }
        });
        
        // Scroll to first error
        if (!isValid) {
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        return isValid;
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    function isValidPAN(pan) {
        return /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/.test(pan);
    }

    function isValidGST(gst) {
        return /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/.test(gst);
    }

    function isValidPostalCode(code) {
        return /^[0-9]{6}$/.test(code);
    }

    function isValidMobile(mobile) {
        return /^[0-9]{10}$/.test(mobile);
    }

    // Save to session (lightweight, no database writes)
    function saveToSession() {
        // Update phone fields before saving
        if (typeof window.updatePhoneFields === 'function') {
            window.updatePhoneFields();
        }
        if (typeof window.updateLandlineFields === 'function') {
            window.updateLandlineFields();
        }
        if (typeof window.updateExhibitorTelephoneFields === 'function') {
            window.updateExhibitorTelephoneFields();
        }
        
        const formData = new FormData(document.getElementById('startupZoneForm'));
        
        // Show saving indicator - ensure it's visible
        const indicator = document.getElementById('autoSaveIndicator');
        if (!indicator) {
            console.error('Auto-save indicator element not found');
            return;
        }
        
        // Make sure indicator is visible
        indicator.classList.remove('d-none');
        indicator.style.display = 'block';
        indicator.style.visibility = 'visible';
        indicator.style.opacity = '1';
        indicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Saving...</span>';
        indicator.classList.remove('alert-success', 'alert-warning');
        indicator.classList.add('alert-info');
        
        fetch('{{ route("startup-zone.auto-save") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Auto-save failed with status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                indicator.innerHTML = '<i class="fas fa-check"></i> <span>Saved</span>';
                indicator.classList.remove('alert-info');
                indicator.classList.add('alert-success');
                // Update progress based on current step (always step 1 for form page)
                if (typeof updateProgressByStep === 'function') {
                    updateProgressByStep(1);
                }
                setTimeout(() => {
                    indicator.classList.add('d-none');
                    indicator.style.display = 'none';
                    indicator.classList.remove('alert-success');
                    indicator.classList.add('alert-info');
                }, 2000);
            } else {
                throw new Error('Auto-save returned success: false - ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Auto-save error:', error);
            indicator.innerHTML = '<i class="fas fa-exclamation-triangle"></i> <span>Save failed</span>';
            indicator.classList.remove('alert-info', 'alert-success');
            indicator.classList.add('alert-warning');
            setTimeout(() => {
                indicator.classList.add('d-none');
                indicator.style.display = 'none';
                indicator.classList.remove('alert-warning');
                indicator.classList.add('alert-info');
            }, 3000);
        });
    }

    function updateProgressByStep(stepNumber) {
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        if (progressBar && progressText) {
            // Step-based progress: Step 1 = 33%, Step 2 = 66%, Step 3 = 100%
            const stepPercentages = {
                1: 33,
                2: 66,
                3: 100
            };
            const percentage = stepPercentages[stepNumber] || 0;
            progressBar.style.width = percentage + '%';
            progressText.textContent = 'Step ' + stepNumber + ' of 3';
            
            // Update step indicators
            updateStepIndicators(stepNumber);
        }
    }

    function updateStepIndicators(currentStep) {
        // Update step indicators - step items are separated by step-connector divs
        const stepItems = document.querySelectorAll('.step-item');
        stepItems.forEach((item, index) => {
            const stepNum = index + 1;
            item.classList.remove('active', 'completed');
            
            if (stepNum < currentStep) {
                item.classList.add('completed');
            } else if (stepNum === currentStep) {
                item.classList.add('active');
            }
        });
    }

    // Legacy function for backward compatibility (if needed)
    function updateProgress(percentage) {
        // Convert percentage to step number
        let stepNumber = 1;
        if (percentage >= 100) {
            stepNumber = 3;
        } else if (percentage >= 66) {
            stepNumber = 3;
        } else if (percentage >= 33) {
            stepNumber = 2;
        } else {
            stepNumber = 1;
        }
        updateProgressByStep(stepNumber);
    }

    function validatePromocode() {
        const promocode = document.getElementById('promocode').value;
        const feedback = document.getElementById('promocodeFeedback');
        
        if (!promocode) {
            feedback.innerHTML = '<div class="text-danger">Please enter a promocode.</div>';
            return;
        }
        
        feedback.innerHTML = '<div class="text-info"><i class="fas fa-spinner fa-spin"></i> Validating...</div>';
        
        fetch('{{ route("startup-zone.validate-promocode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ promocode: promocode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                feedback.innerHTML = '<div class="text-success"><i class="fas fa-check"></i> Valid promocode!</div>';
                document.getElementById('associationInfo').classList.remove('d-none');
                document.getElementById('associationName').textContent = data.association.display_name;
                document.getElementById('associationPrice').textContent = 
                    data.association.is_complimentary ? 
                    'Complimentary Registration' : 
                    'Price: ₹' + data.association.price.toLocaleString('en-IN');
                resetAutoSaveTimer();
            } else {
                feedback.innerHTML = '<div class="text-danger">' + data.message + '</div>';
            }
        })
        .catch(error => {
            feedback.innerHTML = '<div class="text-danger">Error validating promocode.</div>';
        });
    }

    function submitForm() {
        // Update phone fields before submission
        if (typeof window.updatePhoneFields === 'function') {
            window.updatePhoneFields();
        }
        if (typeof window.updateLandlineFields === 'function') {
            window.updateLandlineFields();
        }
        if (typeof window.updateExhibitorTelephoneFields === 'function') {
            window.updateExhibitorTelephoneFields();
        }
        
        const form = document.getElementById('startupZoneForm');

        // Prepare form data creator so we can call it after reCAPTCHA v3 token is ready
        const createFormDataAndSubmit = function (recaptchaToken) {
            const formData = new FormData(form);
            if (recaptchaToken) {
                formData.append('g-recaptcha-response', recaptchaToken);
            }

            // Show loading state
            const submitBtn = document.getElementById('submitForm');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

            // Save all form data first
            // Submit complete form
            fetch('{{ route("startup-zone.submit-form") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
        .then(response => {
            // Check if response is ok (status 200-299)
            if (!response.ok) {
                // Get content type to check if it's JSON
                const contentType = response.headers.get('content-type');
                const isJson = contentType && contentType.includes('application/json');
                
                // For 422 validation errors, parse JSON
                if (response.status === 422) {
                    return response.json().then(data => {
                        console.log('Validation errors:', data.errors);
                        // Convert Laravel errors object to plain object if needed
                        const errors = data.errors || {};
                        throw { type: 'validation', errors: errors, message: data.message || 'Validation failed' };
                    }).catch(err => {
                        // If JSON parsing fails, it's likely HTML error page
                        if (err.type === 'validation') throw err;
                        throw { type: 'validation', errors: {}, message: 'Validation failed. Please check all fields.' };
                    });
                }
                
                // For 500 errors, try to parse JSON, but handle HTML response
                if (response.status === 500) {
                    if (isJson) {
                        return response.json().then(data => {
                            throw { type: 'error', message: data.message || 'Server error occurred. Please try again.' };
                        });
                    } else {
                        // Server returned HTML error page
                        return response.text().then(html => {
                            console.error('Server returned HTML error page:', html.substring(0, 500));
                            throw { type: 'error', message: 'Server error occurred. Please check the console for details or contact support.' };
                        });
                    }
                }
                
                // For other errors, try JSON first
                if (isJson) {
                    return response.json().then(data => {
                        throw { type: 'error', message: data.message || 'Failed to submit form' };
                    });
                } else {
                    // Non-JSON response
                    return response.text().then(text => {
                        throw { type: 'error', message: 'Unexpected server response. Please try again.' };
                    });
                }
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Form saved to draft successfully - redirect to preview page
                // User will click "Proceed to Payment" on preview page to create application
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    // Fallback: redirect to preview page
                    window.location.href = '{{ route("startup-zone.preview") }}';
                }
            } else {
                throw { type: 'error', message: data.message || 'Failed to submit form' };
            }
        })
        .catch(error => {
            console.error('Error object:', error);
            console.error('Error type:', error.type);
            console.error('Error errors:', error.errors);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            // CRITICAL: Check if email already exists error
            if (error.errors && error.errors.contact_email) {
                const contactEmailInput = document.getElementById('contact_email');
                if (contactEmailInput) {
                    contactEmailInput.classList.add('is-invalid');
                    const feedback = contactEmailInput.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Email already exists';
                    }
                    contactEmailInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    contactEmailInput.focus();
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Email Already Exists',
                    text: error.message || 'This email is already registered. Please use a different email address.',
                    confirmButtonText: 'OK'
                });
                return; // Stop here, don't show other errors
            }
            
            // Handle validation errors
            if (error.type === 'validation' && error.errors) {
                // Clear previous validation
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
                // Display validation errors
                let errorMessages = [];
                const errors = error.errors;
                
                // Handle Laravel error format: { field: ['error1', 'error2'] }
                Object.keys(errors).forEach(field => {
                    // Get error message (first one if array, or the message itself)
                    const errorMsg = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                    
                    // reCAPTCHA disabled
                    
                    // Try to find the field element
                    let fieldElement = form.querySelector('[name="' + field + '"]');
                    
                    // If not found, try with different variations
                    if (!fieldElement) {
                        // Try with underscore instead of dot
                        fieldElement = form.querySelector('[name="' + field.replace('.', '_') + '"]');
                    }
                    if (!fieldElement) {
                        // Try with brackets notation
                        fieldElement = form.querySelector('[name="' + field.replace('.', '[') + ']"]');
                    }
                    
                    if (fieldElement) {
                        fieldElement.classList.add('is-invalid');
                        // Find or create invalid feedback
                        let feedback = fieldElement.nextElementSibling;
                        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                            // Create feedback element if it doesn't exist
                            feedback = document.createElement('div');
                            feedback.className = 'invalid-feedback';
                            fieldElement.parentNode.insertBefore(feedback, fieldElement.nextSibling);
                        }
                        feedback.textContent = errorMsg;
                        errorMessages.push(field + ': ' + errorMsg);
                    } else {
                        // Field not found, still add to error messages
                        errorMessages.push(field + ': ' + errorMsg);
                        console.warn('Field not found in form:', field);
                    }
                });
                
                // Scroll to first error
                const firstError = form.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                // Show alert with errors
                if (errorMessages.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        html: '<div style="text-align: left; max-height: 400px; overflow-y: auto;"><ul style="margin: 0; padding-left: 20px;"><li>' + errorMessages.join('</li><li>') + '</li></ul></div>',
                        confirmButtonText: 'OK',
                        width: '600px'
                    });
                } else {
                    // Fallback if no errors were processed
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: error.message || 'Please check the form for errors.',
                        confirmButtonText: 'OK'
                    });
                }
            } else {
                // Handle other errors
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message || 'An error occurred. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        });
        };

        @if(config('constants.RECAPTCHA_ENABLED'))
        // Use reCAPTCHA v3 / Enterprise style execution
        if (typeof grecaptcha !== 'undefined' && grecaptcha.enterprise) {
            grecaptcha.enterprise.ready(function () {
                grecaptcha.enterprise.execute('{{ config('services.recaptcha.site_key') }}', { action: 'submit' })
                    .then(function (token) {
                        createFormDataAndSubmit(token);
                    })
                    .catch(function (err) {
                        console.error('reCAPTCHA execution error:', err);
                        // Fallback: submit without token (backend will fail if strictly required)
                        createFormDataAndSubmit('');
                    });
            });
        } else {
            console.warn('reCAPTCHA v3 not loaded, submitting without token.');
            createFormDataAndSubmit('');
        }
        @else
        // reCAPTCHA disabled via config
        createFormDataAndSubmit('');
        @endif
    }
});
</script>
<!-- Postal Code Validation -->
<script>
document.getElementById('billing_postal_code','exhibitor_postal_code,contact_mobile').addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endpush
@endsection
