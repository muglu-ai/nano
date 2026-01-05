@extends('layouts.exhibitor-registration')

@section('title', 'Exhibitor Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@section('content')
<div class="container py-5">
    {{-- Step Indicator --}}
    <div class="row mb-4">
        <div class="col-12">
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
        </div>
    </div>

    {{-- Progress Bar --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="progress" style="height: 30px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                     id="progressBar" style="width: 0%">
                    <span id="progressText">0% Complete</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Auto-save Indicator --}}
    <div id="autoSaveIndicator" class="alert alert-info d-none" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        <i class="fas fa-spinner fa-spin"></i> Saving...
    </div>

    

    {{-- Form Container --}}
    <form id="exhibitorRegistrationForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="session_id" value="{{ session()->getId() }}">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-building"></i> Exhibitor Registration Form</h4>
            </div>
            <div class="card-body">
                {{-- Booth & Exhibition Details --}}
                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-cube"></i> Booth & Exhibition Details</h5>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="booth_space" class="form-label">Booth Space <span class="text-danger">*</span></label>
                        <select class="form-select" id="booth_space" name="booth_space" required>
                            <option value="">Select Booth Space</option>
                            <option value="Raw" {{ ($draft->booth_space ?? '') == 'Raw' ? 'selected' : '' }}>Raw</option>
                            <option value="Shell" {{ ($draft->booth_space ?? '') == 'Shell' ? 'selected' : '' }}>Shell</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="booth_size" class="form-label">Booth Size <span class="text-danger">*</span></label>
                        <select class="form-select" id="booth_size" name="booth_size" required disabled>
                            <option value="">Select Booth Space First</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="sector" class="form-label">Sector <span class="text-danger">*</span></label>
                        <select class="form-select" id="sector" name="sector" required>
                            <option value="">Select Sector</option>
                            @foreach($sectors as $sector)
                            <option value="{{ $sector }}" {{ ($draft->sector ?? '') == $sector ? 'selected' : '' }}>
                                {{ $sector }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="subsector" class="form-label">Subsector <span class="text-danger">*</span></label>
                        <select class="form-select" id="subsector" name="subsector" required>
                            <option value="">Select Subsector</option>
                            @foreach($subSectors as $subSector)
                            <option value="{{ $subSector }}" {{ ($draft->subsector ?? '') == $subSector ? 'selected' : '' }}>
                                {{ $subSector }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4" id="other_sector_container" style="display: none;">
                        <label for="other_sector_name" class="form-label">Other Sector Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="other_sector_name" name="other_sector_name" 
                               value="{{ $draft->other_sector_name ?? '' }}">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="Exhibitor" {{ ($draft->category ?? '') == 'Exhibitor' ? 'selected' : '' }}>Exhibitor</option>
                            <option value="Sponsor" {{ ($draft->category ?? '') == 'Sponsor' ? 'selected' : '' }}>Sponsor</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Tax & Compliance Details --}}
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-file-invoice-dollar"></i> Tax & Compliance Details</h5>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="tan_status" class="form-label">TAN Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="tan_status" name="tan_status" required>
                            <option value="">Select TAN Status</option>
                            <option value="Registered" {{ ($draft->tan_status ?? '') == 'Registered' ? 'selected' : '' }}>Registered</option>
                            <option value="Unregistered" {{ ($draft->tan_status ?? '') == 'Unregistered' ? 'selected' : '' }}>Unregistered (Not Available)</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4" id="tan_no_container" style="display: none;">
                        <label for="tan_no" class="form-label">TAN Number <span class="text-danger" id="tan_required_indicator" style="display: none;">*</span></label>
                        <input type="text" class="form-control" id="tan_no" name="tan_no" 
                               value="{{ $draft->tan_no ?? '' }}">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="gst_status" class="form-label">GST Status <span class="text-danger">*</span></label>
                        <select class="form-select" id="gst_status" name="gst_status" required>
                            <option value="">Select GST Status</option>
                            <option value="Registered" {{ ($draft->gst_status ?? '') == 'Registered' ? 'selected' : '' }}>Registered</option>
                            <option value="Unregistered" {{ ($draft->gst_status ?? '') == 'Unregistered' ? 'selected' : '' }}>Unregistered (Not Available)</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6" id="gst_no_container" style="display: none;">
                        <label for="gst_no" class="form-label">GST Number <span class="text-danger" id="gst_required_indicator" style="display: none;">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="gst_no" name="gst_no" 
                                   value="{{ $draft->gst_no ?? '' }}" 
                                   pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}">
                            <button type="button" class="btn btn-outline-primary" id="validateGstBtn">
                                <i class="fas fa-search"></i> Validate
                            </button>
                        </div>
                        <div id="gst_loading" class="d-none mt-1">
                            <small class="text-info"><i class="fas fa-spinner fa-spin"></i> Fetching details...</small>
                        </div>
                        <div id="gst_feedback" class="mt-1"></div>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="pan_no" class="form-label">PAN Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="pan_no" name="pan_no" 
                               value="{{ $draft->pan_no ?? '' }}" 
                               pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" 
                               maxlength="10" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Billing Information --}}
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-building"></i> Billing Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="billing_company_name" class="form-label">Billing Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="billing_company_name" name="billing_company_name" 
                               value="{{ isset($draft->billing_data['company_name']) ? $draft->billing_data['company_name'] : ($draft->company_name ?? ($draft->organisation_name ?? '')) }}" 
                               maxlength="100" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="billing_address" class="form-label">Billing Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="billing_address" name="billing_address" rows="2" required>{{ isset($draft->billing_data['address']) ? $draft->billing_data['address'] : ($draft->address ?? ($draft->invoice_address ?? '')) }}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label for="billing_state_id" class="form-label">Billing State <span class="text-danger">*</span></label>
                        <select class="form-select" id="billing_state_id" name="billing_state_id" required>
                            <option value="">Select State</option>
                            @php
                                $selectedBillingStateId = isset($draft->billing_data['state_id']) && $draft->billing_data['state_id'] 
                                    ? $draft->billing_data['state_id'] 
                                    : (isset($draft->state_id) ? $draft->state_id : null);
                            @endphp
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ $selectedBillingStateId == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="billing_city" class="form-label">Billing City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="billing_city" name="billing_city" 
                               value="{{ isset($draft->billing_data['city']) ? $draft->billing_data['city'] : ($draft->city_id ?? ($draft->city ?? '')) }}" 
                               maxlength="100" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="billing_postal_code" class="form-label">Billing Postal Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="billing_postal_code" name="billing_postal_code" 
                               value="{{ isset($draft->billing_data['postal_code']) ? $draft->billing_data['postal_code'] : ($draft->postal_code ?? '') }}" 
                               pattern="[0-9]{6}" maxlength="6" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="billing_telephone" class="form-label">Billing Telephone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="billing_telephone" name="billing_telephone" 
                               value="{{ isset($draft->billing_data['telephone']) ? $draft->billing_data['telephone'] : ($draft->landline ?? ($draft->organisation_telephone ?? '')) }}" 
                               required>
                        <input type="hidden" id="billing_telephone_country_code" name="billing_telephone_country_code">
                        <input type="hidden" id="billing_telephone_national" name="billing_telephone_national">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="billing_website" class="form-label">Website <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="billing_website" name="billing_website" 
                               value="{{ isset($draft->billing_data['website']) ? $draft->billing_data['website'] : ($draft->website ?? ($draft->organisation_website ?? '')) }}" 
                               required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="billing_email" class="form-label">Billing Email</label>
                        <input type="email" class="form-control" id="billing_email" name="billing_email" 
                               value="{{ isset($draft->billing_data['email']) ? $draft->billing_data['email'] : ($draft->company_email ?? '') }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Exhibitor Information --}}
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-building"></i> Exhibitor Information</h5>
                <div class="row mb-3">
                    <div class="col-12">
                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="copy_from_billing">
                            <i class="fas fa-copy"></i> Copy from Billing Information
                        </button>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="exhibitor_name" class="form-label">Name of Exhibitor (Organisation Name) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="exhibitor_name" name="exhibitor_name" 
                               value="{{ isset($draft->exhibitor_data['name']) ? $draft->exhibitor_data['name'] : ($draft->organisation_name ?? '') }}" 
                               maxlength="100" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="exhibitor_address" class="form-label">Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="exhibitor_address" name="exhibitor_address" rows="2" required>{{ isset($draft->exhibitor_data['address']) ? $draft->exhibitor_data['address'] : ($draft->invoice_address ?? '') }}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
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
                    <div class="col-md-4">
                        <label for="exhibitor_state_id" class="form-label">State <span class="text-danger">*</span></label>
                        <select class="form-select" id="exhibitor_state_id" name="exhibitor_state_id" required>
                            <option value="">Select State</option>
                            @php
                                $selectedExhibitorStateId = isset($draft->exhibitor_data['state_id']) && $draft->exhibitor_data['state_id'] 
                                    ? $draft->exhibitor_data['state_id'] 
                                    : null;
                            @endphp
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" {{ $selectedExhibitorStateId == $state->id ? 'selected' : '' }}>
                                    {{ $state->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="exhibitor_city" class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="exhibitor_city" name="exhibitor_city" 
                               value="{{ isset($draft->exhibitor_data['city']) ? $draft->exhibitor_data['city'] : ($draft->city ?? '') }}" 
                               maxlength="100" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="exhibitor_postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="exhibitor_postal_code" name="exhibitor_postal_code" 
                               value="{{ isset($draft->exhibitor_data['postal_code']) ? $draft->exhibitor_data['postal_code'] : ($draft->postal_code ?? '') }}" 
                               pattern="[0-9]{6}" maxlength="6" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="exhibitor_telephone" class="form-label">Telephone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="exhibitor_telephone" name="exhibitor_telephone" 
                               value="{{ isset($draft->exhibitor_data['telephone']) ? $draft->exhibitor_data['telephone'] : ($draft->organisation_telephone ?? '') }}" 
                               required>
                        <input type="hidden" id="exhibitor_telephone_country_code" name="exhibitor_telephone_country_code">
                        <input type="hidden" id="exhibitor_telephone_national" name="exhibitor_telephone_national">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="exhibitor_website" class="form-label">Website <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="exhibitor_website" name="exhibitor_website" 
                               value="{{ isset($draft->exhibitor_data['website']) ? $draft->exhibitor_data['website'] : ($draft->organisation_website ?? '') }}" 
                               required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="exhibitor_email" class="form-label">Company Email</label>
                        <input type="email" class="form-control" id="exhibitor_email" name="exhibitor_email" 
                               value="{{ isset($draft->exhibitor_data['email']) ? $draft->exhibitor_data['email'] : '' }}">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Primary Contact Person --}}
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-user"></i> Primary Contact Person</h5>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="contact_title" class="form-label">Title <span class="text-danger">*</span></label>
                        <select class="form-select" id="contact_title" name="contact_title" required>
                            <option value=""></option>
                            <option value="Mr." {{ (isset($draft->contact_data['title']) && $draft->contact_data['title'] == 'Mr.') || ($draft->contact_title ?? '') == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                            <option value="Mrs." {{ (isset($draft->contact_data['title']) && $draft->contact_data['title'] == 'Mrs.') || ($draft->contact_title ?? '') == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                            <option value="Ms." {{ (isset($draft->contact_data['title']) && $draft->contact_data['title'] == 'Ms.') || ($draft->contact_title ?? '') == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                            <option value="Dr." {{ (isset($draft->contact_data['title']) && $draft->contact_data['title'] == 'Dr.') || ($draft->contact_title ?? '') == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                            <option value="Prof." {{ (isset($draft->contact_data['title']) && $draft->contact_data['title'] == 'Prof.') || ($draft->contact_title ?? '') == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="contact_first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact_first_name" name="contact_first_name" 
                               value="{{ isset($draft->contact_data['first_name']) ? $draft->contact_data['first_name'] : ($draft->contact_first_name ?? '') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="contact_last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact_last_name" name="contact_last_name" 
                               value="{{ isset($draft->contact_data['last_name']) ? $draft->contact_data['last_name'] : ($draft->contact_last_name ?? '') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-3">
                        <label for="contact_designation" class="form-label">Designation <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="contact_designation" name="contact_designation" 
                               value="{{ isset($draft->contact_data['designation']) ? $draft->contact_data['designation'] : ($draft->contact_designation ?? '') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="contact_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" 
                               value="{{ isset($draft->contact_data['email']) ? $draft->contact_data['email'] : ($draft->contact_email ?? '') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="contact_mobile" class="form-label">Mobile <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="contact_mobile" name="contact_mobile" 
                               value="{{ isset($draft->contact_data['mobile']) ? $draft->contact_data['mobile'] : ($draft->contact_mobile ?? '') }}" required>
                        <input type="hidden" id="contact_country_code" name="contact_country_code">
                        <input type="hidden" id="contact_mobile_national" name="contact_mobile_national">
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Sales Reference --}}
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-user-tie"></i> Sales Reference</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sales_executive_name" class="form-label">Sales Executive Name (From BTS Team) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="sales_executive_name" name="sales_executive_name" 
                               value="{{ $draft->sales_executive_name ?? '' }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Payment Mode --}}
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-credit-card"></i> Payment Mode</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label d-block mb-2">Payment Mode <span class="text-danger">*</span></label>
                        <div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_mode" id="payment_mode_ccavenue" value="CCAvenue"
                                    {{ ($draft->payment_mode ?? '') == 'CCAvenue' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="payment_mode_ccavenue">
                                    CCAvenue Payment Gateway
                                </label>
                            </div>
                        </div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Promocode Section --}}
                <div class="row mb-3" style="display: none;">
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-ticket-alt"></i> Promocode (Optional)</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="promocode" class="form-label">Promocode</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="promocode" name="promocode" 
                                   value="{{ $draft->promocode ?? '' }}">
                            <button type="button" class="btn btn-outline-primary" id="validatePromocodeBtn">
                                Validate
                            </button>
                        </div>
                        <div id="promocodeFeedback" class="mt-2"></div>
                    </div>
                </div>
                </div>

                {{-- Price Display --}}
                <div id="priceDisplay" class="alert alert-success d-none mb-4">
                    <h5><i class="fas fa-calculator"></i> Price Calculation</h5>
                    <div id="priceDetails"></div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Note:</strong> After submitting this form, you will be redirected to preview your registration details before making payment.
                </div>

                {{-- Submit Button --}}
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-check"></i> Submit & Preview
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    /* Fix intl-tel-input alignment with Bootstrap grid */
    .col-md-6 .iti,
    .col-md-4 .iti {
        width: 100%;
        display: block;
    }
    
    /* Ensure the input field container takes full width */
    .iti input[type=tel] {
        width: 100% !important;
    }
    
    /* Fix alignment for separate dial code mode */
    .iti--separate-dial-code {
        width: 100%;
    }
    
    .iti--separate-dial-code .iti__selected-flag {
        background-color: #f8f9fa;
        border-right: 1px solid #ced4da;
        border-radius: 0.375rem 0 0 0.375rem;
    }
    
    .iti--separate-dial-code .iti__selected-dial-code {
        padding: 0 8px;
    }
    
    /* Ensure proper height alignment */
    .iti input[type=tel],
    .iti .iti__selected-flag {
        height: calc(1.5em + 0.75rem + 2px);
    }
    
    /* Match Bootstrap form-control styling */
    .iti input[type=tel].form-control {
        border-left: 0;
        border-radius: 0 0.375rem 0.375rem 0;
    }
    
    .iti--separate-dial-code .iti__selected-flag {
        border-radius: 0.375rem 0 0 0.375rem;
    }
</style>
@endpush

@push('scripts')
@if(config('constants.RECAPTCHA_ENABLED'))
<script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endif
<script>
$(document).ready(function() {
    // Initialize intl-tel-input for phone fields
    const billingTelInput = intlTelInput(document.querySelector("#billing_telephone"), {
        initialCountry: "in",
        separateDialCode: true,
        placeholderNumberType: false,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
    });

    const exhibitorTelInput = intlTelInput(document.querySelector("#exhibitor_telephone"), {
        initialCountry: "in",
        separateDialCode: true,
        placeholderNumberType: false,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
    });

    const contactTelInput = intlTelInput(document.querySelector("#contact_mobile"), {
        initialCountry: "in",
        separateDialCode: true,
        placeholderNumberType: false,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
    });

    // Remove any placeholder text from phone inputs
    $('#billing_telephone').attr('placeholder', '');
    $('#exhibitor_telephone').attr('placeholder', '');
    $('#contact_mobile').attr('placeholder', '');

    // Update hidden fields on change
    $('#billing_telephone').on('blur', function() {
        const phoneNumber = billingTelInput.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
        const countryCode = billingTelInput.getSelectedCountryData().dialCode;
        const nationalNumber = phoneNumber.replace(/\D/g, '');
        $('#billing_telephone_country_code').val(countryCode);
        $('#billing_telephone_national').val(nationalNumber);
    });

    $('#exhibitor_telephone').on('blur', function() {
        const phoneNumber = exhibitorTelInput.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
        const countryCode = exhibitorTelInput.getSelectedCountryData().dialCode;
        const nationalNumber = phoneNumber.replace(/\D/g, '');
        $('#exhibitor_telephone_country_code').val(countryCode);
        $('#exhibitor_telephone_national').val(nationalNumber);
    });

    $('#contact_mobile').on('blur', function() {
        const phoneNumber = contactTelInput.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
        const countryCode = contactTelInput.getSelectedCountryData().dialCode;
        const nationalNumber = phoneNumber.replace(/\D/g, '');
        $('#contact_country_code').val(countryCode);
        $('#contact_mobile_national').val(nationalNumber);
    });

    // Function to load states for a country
    function loadStatesForCountry(countryId, stateSelectId, preserveSelectedStateId = null) {
        const stateSelect = $(stateSelectId);
        
        if (!countryId) {
            stateSelect.html('<option value="">Select Country First</option>');
            return;
        }
        
        stateSelect.html('<option value="">Loading states...</option>');
        stateSelect.prop('disabled', true);
        
        $.ajax({
            url: '{{ route("get.states") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            data: JSON.stringify({ country_id: countryId }),
            success: function(response) {
                stateSelect.html('<option value="">Select State</option>');
                if (response && response.length > 0) {
                    response.forEach(function(state) {
                        const selected = preserveSelectedStateId && preserveSelectedStateId == state.id ? 'selected' : '';
                        stateSelect.append(`<option value="${state.id}" ${selected}>${state.name}</option>`);
                    });
                }
                stateSelect.prop('disabled', false);
            },
            error: function() {
                stateSelect.html('<option value="">Error loading states</option>');
                stateSelect.prop('disabled', false);
            }
        });
    }

    // Billing Country change handler
    $('#billing_country_id').on('change', function() {
        const countryId = $(this).val();
        loadStatesForCountry(countryId, '#billing_state_id');
    });

    // Exhibitor Country change handler
    $('#exhibitor_country_id').on('change', function() {
        const countryId = $(this).val();
        loadStatesForCountry(countryId, '#exhibitor_state_id');
    });

    // Booth Space change handler
    $('#booth_space').on('change', function() {
        const boothSpace = $(this).val();
        const boothSizeSelect = $('#booth_size');
        
        if (!boothSpace) {
            boothSizeSelect.prop('disabled', true).html('<option value="">Select Booth Space First</option>');
            return;
        }
        
        // Fetch booth sizes
        $.ajax({
            url: '{{ route("exhibitor-registration.booth-sizes") }}',
            method: 'GET',
            data: { booth_space: boothSpace },
            success: function(response) {
                if (response.success) {
                    boothSizeSelect.prop('disabled', false).html('<option value="">Select Booth Size</option>');
                    response.booth_sizes.forEach(function(size) {
                        const selected = '{{ $draft->booth_size ?? "" }}' == size.value ? 'selected' : '';
                        boothSizeSelect.append(`<option value="${size.value}" ${selected}>${size.label}</option>`);
                    });
                }
            }
        });
    });

    // Trigger change if booth_space is already selected
    if ($('#booth_space').val()) {
        $('#booth_space').trigger('change');
    }

    // Calculate price when booth size changes
    $('#booth_size').on('change', function() {
        const boothSpace = $('#booth_space').val();
        const boothSize = $(this).val();
        
        if (boothSpace && boothSize) {
            calculatePrice(boothSpace, boothSize);
        }
    });

    function calculatePrice(boothSpace, boothSize) {
        $.ajax({
            url: '{{ route("exhibitor-registration.calculate-price") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                booth_space: boothSpace,
                booth_size: boothSize,
                gst_rate: {{ $gstRate }}
            },
            success: function(response) {
                if (response.success) {
                    const price = response.price;
                    let processingHtml = '';
                    if (price.processing_charges) {
                        processingHtml = `<p><strong>Processing Charges (${price.processing_rate}%):</strong> ₹${price.processing_charges.toLocaleString()}</p>`;
                    }
                    $('#priceDetails').html(`
                        <p><strong>Booth Size:</strong> ${price.sqm} sqm</p>
                        <p><strong>Rate per sqm:</strong> ₹${price.rate_per_sqm.toLocaleString()}</p>
                        <p><strong>Base Price:</strong> ₹${price.base_price.toLocaleString()}</p>
                        <p><strong>GST (${price.gst_rate}%):</strong> ₹${price.gst_amount.toLocaleString()}</p>
                        ${processingHtml}
                        <p><strong>Total Price:</strong> ₹${price.total_price.toLocaleString()}</p>
                    `);
                    $('#priceDisplay').removeClass('d-none');
                }
            }
        });
    }

    // Sector change handler
    $('#sector').on('change', function() {
        if ($(this).val() === 'Others') {
            $('#other_sector_container').show();
            $('#other_sector_name').prop('required', true);
        } else {
            $('#other_sector_container').hide();
            $('#other_sector_name').prop('required', false);
        }
    });

    // TAN Status change handler
    $('#tan_status').on('change', function() {
        if ($(this).val() === 'Registered') {
            $('#tan_no_container').show();
            $('#tan_required_indicator').show();
            $('#tan_no').prop('required', true);
        } else {
            $('#tan_no_container').hide();
            $('#tan_required_indicator').hide();
            $('#tan_no').prop('required', false);
        }
    });

    // GST Status change handler
    $('#gst_status').on('change', function() {
        if ($(this).val() === 'Registered') {
            $('#gst_no_container').show();
            $('#gst_required_indicator').show();
            $('#gst_no').prop('required', true);
        } else {
            $('#gst_no_container').hide();
            $('#gst_required_indicator').hide();
            $('#gst_no').prop('required', false);
        }
    });

    // Trigger change handlers on load
    $('#sector').trigger('change');
    $('#tan_status').trigger('change');
    $('#gst_status').trigger('change');

    // Copy from Billing Information to Exhibitor Information
    $('#copy_from_billing').on('click', function() {
        // Copy company name
        const billingCompanyName = $('#billing_company_name').val() || '';
        $('#exhibitor_name').val(billingCompanyName);
        
        // Copy address
        const billingAddress = $('#billing_address').val() || '';
        $('#exhibitor_address').val(billingAddress);
        
        // Copy country and state
        const billingCountryId = $('#billing_country_id').val() || '';
        const billingStateId = $('#billing_state_id').val() || '';
        if (billingCountryId) {
            $('#exhibitor_country_id').val(billingCountryId).trigger('change');
            // Wait for states to load, then set state
            setTimeout(function() {
                if (billingStateId) {
                    $('#exhibitor_state_id').val(billingStateId);
                }
            }, 500);
        }
        
        // Copy city
        const billingCity = $('#billing_city').val() || '';
        $('#exhibitor_city').val(billingCity);
        
        // Copy postal code
        const billingPostalCode = $('#billing_postal_code').val() || '';
        $('#exhibitor_postal_code').val(billingPostalCode);
        
        // Copy telephone
        const billingTelephone = $('#billing_telephone').val() || '';
        if (billingTelephone && exhibitorTelInput) {
            exhibitorTelInput.setNumber(billingTelephone);
        }
        
        // Copy website
        const billingWebsite = $('#billing_website').val() || '';
        $('#exhibitor_website').val(billingWebsite);
        
        // Copy email
        const billingEmail = $('#billing_email').val() || '';
        $('#exhibitor_email').val(billingEmail);
    });

    // GST Validation
    $('#validateGstBtn').on('click', function() {
        const gstNo = $('#gst_no').val();
        if (!gstNo) {
            alert('Please enter GST number first');
            return;
        }
        
        $('#gst_loading').removeClass('d-none');
        $('#gst_feedback').html('');
        
        $.ajax({
            url: '{{ route("exhibitor-registration.fetch-gst-details") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                gst_no: gstNo
            },
            success: function(response) {
                $('#gst_loading').addClass('d-none');
                if (response.success) {
                    const data = response.data;
                    $('#billing_company_name').val(data.company_name || '');
                    $('#billing_address').val(data.billing_address || '');
                    $('#billing_city').val(data.city || '');
                    if (data.state_id) {
                        const billingCountryId = $('#billing_country_id').val();
                        if (billingCountryId) {
                            loadStatesForCountry(billingCountryId, '#billing_state_id', data.state_id);
                        }
                    }
                    $('#billing_postal_code').val(data.pincode || '');
                    $('#pan_no').val(data.pan || '');
                    $('#gst_feedback').html('<small class="text-success"><i class="fas fa-check"></i> GST details fetched successfully!</small>');
                } else {
                    $('#gst_feedback').html(`<small class="text-danger"><i class="fas fa-times"></i> ${response.message}</small>`);
                }
            },
            error: function(xhr) {
                $('#gst_loading').addClass('d-none');
                const response = xhr.responseJSON;
                $('#gst_feedback').html(`<small class="text-danger"><i class="fas fa-times"></i> ${response?.message || 'Error fetching GST details'}</small>`);
            }
        });
    });

    // Promocode Validation
    $('#validatePromocodeBtn').on('click', function() {
        const promocode = $('#promocode').val();
        if (!promocode) {
            $('#promocodeFeedback').html('<small class="text-danger">Please enter a promocode</small>');
            return;
        }
        
        $.ajax({
            url: '{{ route("startup-zone.validate-promocode") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                promocode: promocode
            },
            success: function(response) {
                if (response.success) {
                    $('#promocodeFeedback').html(`<small class="text-success"><i class="fas fa-check"></i> Valid promocode! ${response.association.display_name || response.association.name}</small>`);
                } else {
                    $('#promocodeFeedback').html(`<small class="text-danger"><i class="fas fa-times"></i> ${response.message}</small>`);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                $('#promocodeFeedback').html(`<small class="text-danger"><i class="fas fa-times"></i> ${response?.message || 'Error validating promocode'}</small>`);
            }
        });
    });


    // Auto-save functionality
    let autoSaveTimeout;
    $('input, select, textarea').on('change', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            autoSave();
        }, 2000);
    });

    function autoSave() {
        // Update phone fields before saving
        if (billingTelInput) {
            const billingPhoneNumber = billingTelInput.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
            const billingCountryCode = billingTelInput.getSelectedCountryData().dialCode;
            const billingNationalNumber = billingPhoneNumber.replace(/\D/g, '');
            $('#billing_telephone_country_code').val(billingCountryCode);
            $('#billing_telephone_national').val(billingNationalNumber);
        }
        
        if (exhibitorTelInput) {
            const exhibitorPhoneNumber = exhibitorTelInput.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
            const exhibitorCountryCode = exhibitorTelInput.getSelectedCountryData().dialCode;
            const exhibitorNationalNumber = exhibitorPhoneNumber.replace(/\D/g, '');
            $('#exhibitor_telephone_country_code').val(exhibitorCountryCode);
            $('#exhibitor_telephone_national').val(exhibitorNationalNumber);
        }
        
        if (contactTelInput) {
            const contactPhoneNumber = contactTelInput.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
            const contactCountryCode = contactTelInput.getSelectedCountryData().dialCode;
            const contactNationalNumber = contactPhoneNumber.replace(/\D/g, '');
            $('#contact_country_code').val(contactCountryCode);
            $('#contact_mobile_national').val(contactNationalNumber);
        }
        
        const formData = new FormData($('#exhibitorRegistrationForm')[0]);
        
        $('#autoSaveIndicator').removeClass('d-none');
        
        $.ajax({
            url: '{{ route("exhibitor-registration.auto-save") }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#progressBar').css('width', response.progress + '%');
                    $('#progressText').text(response.progress + '% Complete');
                    setTimeout(function() {
                        $('#autoSaveIndicator').addClass('d-none');
                    }, 1000);
                }
            },
            error: function() {
                $('#autoSaveIndicator').addClass('d-none');
            }
        });
    }

    // Form submission
    $('#exhibitorRegistrationForm').on('submit', function(e) {
        e.preventDefault();
        
        // Update phone fields before submitting
        if (billingTelInput) {
            const billingPhoneNumber = billingTelInput.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
            const billingCountryCode = billingTelInput.getSelectedCountryData().dialCode;
            const billingNationalNumber = billingPhoneNumber.replace(/\D/g, '');
            $('#billing_telephone_country_code').val(billingCountryCode);
            $('#billing_telephone_national').val(billingNationalNumber);
        }
        
        if (exhibitorTelInput) {
            const exhibitorPhoneNumber = exhibitorTelInput.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
            const exhibitorCountryCode = exhibitorTelInput.getSelectedCountryData().dialCode;
            const exhibitorNationalNumber = exhibitorPhoneNumber.replace(/\D/g, '');
            $('#exhibitor_telephone_country_code').val(exhibitorCountryCode);
            $('#exhibitor_telephone_national').val(exhibitorNationalNumber);
        }
        
        if (contactTelInput) {
            const contactPhoneNumber = contactTelInput.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
            const contactCountryCode = contactTelInput.getSelectedCountryData().dialCode;
            const contactNationalNumber = contactPhoneNumber.replace(/\D/g, '');
            $('#contact_country_code').val(contactCountryCode);
            $('#contact_mobile_national').val(contactNationalNumber);
        }
        
        // Function to submit form with reCAPTCHA token
        const submitFormWithRecaptcha = function(recaptchaToken) {
            const formData = new FormData($('#exhibitorRegistrationForm')[0]);
            
            // Add reCAPTCHA token to form data
            if (recaptchaToken) {
                formData.append('g-recaptcha-response', recaptchaToken);
            }
            
            $.ajax({
                url: '{{ route("exhibitor-registration.submit-form") }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirect_url;
                    } else {
                        if (response.errors) {
                            // Display validation errors
                            Object.keys(response.errors).forEach(function(field) {
                                const input = $(`[name="${field}"]`);
                                input.addClass('is-invalid');
                                input.siblings('.invalid-feedback').text(response.errors[field][0]);
                            });
                        } else {
                            alert(response.message || 'An error occurred');
                        }
                    }
                },
                error: function(xhr) {
                    const response = xhr.responseJSON;
                    if (response && response.errors) {
                        Object.keys(response.errors).forEach(function(field) {
                            const input = $(`[name="${field}"]`);
                            input.addClass('is-invalid');
                            input.siblings('.invalid-feedback').text(response.errors[field][0]);
                        });
                    } else {
                        alert(response?.message || 'An error occurred. Please try again.');
                    }
                }
            });
        };
        
        // Execute reCAPTCHA if enabled
        @if(config('constants.RECAPTCHA_ENABLED'))
        if (typeof grecaptcha !== 'undefined' && grecaptcha.enterprise) {
            grecaptcha.enterprise.ready(function () {
                grecaptcha.enterprise.execute('{{ config('services.recaptcha.site_key') }}', { action: 'submit' })
                    .then(function (token) {
                        submitFormWithRecaptcha(token);
                    })
                    .catch(function (err) {
                        console.error('reCAPTCHA execution error:', err);
                        // Fallback: submit without token (backend will fail if strictly required)
                        submitFormWithRecaptcha('');
                    });
            });
        } else {
            console.warn('reCAPTCHA v3 not loaded, submitting without token.');
            submitFormWithRecaptcha('');
        }
        @else
        // reCAPTCHA disabled via config
        submitFormWithRecaptcha('');
        @endif
    });
});
</script>
@endpush
@endsection

