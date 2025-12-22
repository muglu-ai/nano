@extends('layouts.startup-zone')

@section('title', 'Startup Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@section('content')
<div class="container py-5">
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
    <form id="startupZoneForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="session_id" value="{{ session()->getId() }}">

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0"><i class="fas fa-building"></i> Startup Zone Registration Form</h4>
            </div>
            <div class="card-body">
                {{-- Association Pricing Display --}}
                <div id="associationInfo" class="alert alert-success d-none mb-4">
                    <h5 id="associationName"></h5>
                    <p id="associationPrice"></p>
                </div>

                {{-- Booth Information --}}
                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-cube"></i> Booth Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="stall_category" class="form-label">Booth Type <span class="text-danger">*</span></label>
                        <select class="form-select" id="stall_category" name="stall_category" required>
                            <option value="">Select Booth Type</option>
                            <option value="Startup Booth" {{ ($draft->stall_category ?? '') == 'Startup Booth' ? 'selected' : '' }}>Startup Booth</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="interested_sqm" class="form-label">Booth Size <span class="text-danger">*</span></label>
                        <select class="form-select" id="interested_sqm" name="interested_sqm" required>
                            <option value="">Select Booth Size</option>
                            <option value="Booth / POD" {{ ($draft->interested_sqm ?? '') == 'Booth / POD' ? 'selected' : '' }}>Booth / POD</option>
                            <option value="4 SQM" {{ ($draft->interested_sqm ?? '') == '4 SQM' ? 'selected' : '' }}>4 SQM</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Sector Information --}}
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-industry"></i> Sector Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="sector_id" class="form-label">Sector <span class="text-danger">*</span></label>
                        <select class="form-select" id="sector_id" name="sector_id" required>
                            <option value="">Select Sector</option>
                            @foreach($sectors as $sector)
                            <option value="{{ $sector->id }}" {{ ($draft->sector_id ?? '') == $sector->id ? 'selected' : '' }}>
                                {{ $sector->name }}
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

                {{-- Tax Information --}}
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
                                   placeholder="22AAAAA0000A1Z5">
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
                               maxlength="10" placeholder="ABCDE1234F" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Company Information --}}
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-building"></i> Company Information</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="company_name" name="company_name" 
                               value="{{ $draft->company_name ?? '' }}" 
                               maxlength="100" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="certificate" class="form-label">Company Registration Certificate (PDF, Max 2MB)</label>
                        <input type="file" class="form-control" id="certificate" name="certificate" 
                               accept=".pdf">
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
                        <label for="address" class="form-label">Invoice Address <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="2" required>{{ $draft->address ?? '' }}</textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="country_id" class="form-label">Country <span class="text-danger">*</span></label>
                        <select class="form-select" id="country_id" name="country_id" required>
                            <option value="">Select Country</option>
                            @foreach($countries as $country)
                            @php
                                $isSelected = ($draft->country_id ?? '') == $country->id;
                                // If no country selected and this is India, select it
                                if (!isset($draft->country_id) && $country->code === 'IN') {
                                    $isSelected = true;
                                }
                            @endphp
                            <option value="{{ $country->id }}" {{ $isSelected ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="state_id" class="form-label">State <span class="text-danger">*</span></label>
                        <select class="form-select" id="state_id" name="state_id" required>
                            <option value="">Select State</option>
                            @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ ($draft->state_id ?? '') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="city_id" class="form-label">City <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="city_id" name="city_id" 
                               value="{{ $draft->city_id ?? '' }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="postal_code" name="postal_code" 
                               value="{{ $draft->postal_code ?? '' }}" 
                               pattern="[0-9]{6}" maxlength="6" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="landline" class="form-label">Telephone Number <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="landline" name="landline" 
                               value="{{ $draft->landline ?? '' }}" placeholder="" required>
                        <input type="hidden" id="landline_country_code" name="landline_country_code">
                        <input type="hidden" id="landline_national" name="landline_national">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4">
                        <label for="website" class="form-label">Website <span class="text-danger">*</span></label>
                        <input type="url" class="form-control" id="website" name="website" 
                               value="{{ $draft->website ?? '' }}" 
                               placeholder="https://example.com" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="company_email" class="form-label">Company Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="company_email" name="company_email" 
                               value="{{ $draft->company_email ?? '' }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>


                {{-- Contact Person Details --}}
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

                {{-- Payment Mode --}}
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-credit-card"></i> Payment Mode</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="payment_mode" class="form-label">Payment Mode <span class="text-danger">*</span></label>
                        <select class="form-select" id="payment_mode" name="payment_mode" required>
                            <option value="">Select Payment Mode</option>
                            <option value="CCAvenue" {{ ($draft->payment_mode ?? '') == 'CCAvenue' ? 'selected' : '' }}>CCAvenue (Indian Payments)</option>
                            <option value="PayPal" {{ ($draft->payment_mode ?? '') == 'PayPal' ? 'selected' : '' }}>PayPal (International Payments)</option>
                            <option value="Bank Transfer" {{ ($draft->payment_mode ?? '') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                {{-- Promocode Section --}}
                <h5 class="mb-3 mt-4 border-bottom pb-2"><i class="fas fa-ticket-alt"></i> Promocode (Optional)</h5>
                <div class="row mb-3">
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

                {{-- Submit Button --}}
                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-primary btn-lg" id="submitForm">
                        <i class="fas fa-check"></i> Submit & Preview
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load draft data if exists
    @if($draft && isset($draft->progress_percentage))
    updateProgress({{ $draft->progress_percentage }});
    @else
    updateProgress(0);
    @endif

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
            
            // Get national number (without country code) and remove all spaces
            let nationalNumber = '';
            if (window.intlTelInputUtils && iti.isValidNumber()) {
                nationalNumber = iti.getNumber(window.intlTelInputUtils.numberFormat.NATIONAL);
                nationalNumber = nationalNumber.replace(/\s/g, '').replace(/^0+/, ''); // Remove all spaces and leading zeros
            } else {
                // Fallback: extract number from full number
                const dialCode = '+' + countryCode;
                if (fullNumber.startsWith(dialCode)) {
                    nationalNumber = fullNumber.substring(dialCode.length).replace(/\s/g, '').replace(/^0+/, '');
                } else {
                    // If no country code prefix, try to extract from the input value
                    const inputValue = mobileInput.value.replace(/\s/g, ''); // Remove all spaces
                    nationalNumber = inputValue.replace(/^\+?\d{1,3}/, ''); // Remove country code if present
                }
            }
            
            // Remove all spaces from national number
            nationalNumber = nationalNumber.replace(/\s+/g, '');
            
            const countryCodeField = document.getElementById('contact_country_code');
            const mobileNationalField = document.getElementById('contact_mobile_national');
            if (countryCodeField) countryCodeField.value = countryCode;
            if (mobileNationalField) mobileNationalField.value = nationalNumber;
        };
        
        mobileInput.addEventListener('change', window.updatePhoneFields);
        mobileInput.addEventListener('blur', window.updatePhoneFields);
        mobileInput.addEventListener('countrychange', window.updatePhoneFields);
        
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
            
            // Get national number (without country code) and remove all spaces
            let nationalNumber = '';
            if (window.intlTelInputUtils && itiLandline.isValidNumber()) {
                nationalNumber = itiLandline.getNumber(window.intlTelInputUtils.numberFormat.NATIONAL);
                nationalNumber = nationalNumber.replace(/\s/g, '').replace(/^0+/, ''); // Remove all spaces and leading zeros
            } else {
                // Fallback: extract number from full number
                const dialCode = '+' + countryCode;
                if (fullNumber.startsWith(dialCode)) {
                    nationalNumber = fullNumber.substring(dialCode.length).replace(/\s/g, '').replace(/^0+/, '');
                } else {
                    // If no country code prefix, try to extract from the input value
                    const inputValue = landlineInput.value.replace(/\s/g, ''); // Remove all spaces
                    nationalNumber = inputValue.replace(/^\+?\d{1,3}/, ''); // Remove country code if present
                }
            }
            
            // Remove all spaces from national number
            nationalNumber = nationalNumber.replace(/\s+/g, '');
            
            const countryCodeField = document.getElementById('landline_country_code');
            const landlineNationalField = document.getElementById('landline_national');
            if (countryCodeField) countryCodeField.value = countryCode;
            if (landlineNationalField) landlineNationalField.value = nationalNumber;
        };
        
        landlineInput.addEventListener('change', window.updateLandlineFields);
        landlineInput.addEventListener('blur', window.updateLandlineFields);
        landlineInput.addEventListener('countrychange', window.updateLandlineFields);
        
        // Initialize hidden fields on load
        setTimeout(window.updateLandlineFields, 100);
    }

    // Normalize website URL - add https:// if protocol is missing
    const websiteInput = document.getElementById('website');
    if (websiteInput) {
        function normalizeWebsiteUrl(url) {
            if (!url) return url;
            
            url = url.trim();
            
            // If URL doesn't start with http:// or https://, add https://
            if (!/^https?:\/\//i.test(url)) {
                url = 'https://' + url;
            }
            
            return url;
        }
        
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
                    // Auto-fill company name and billing address
                    const companyNameField = document.getElementById('company_name');
                    const addressField = document.getElementById('address');
                    const stateField = document.getElementById('state_id');
                    const postalCodeField = document.getElementById('postal_code');
                    const panField = document.getElementById('pan_no');
                    const cityField = document.getElementById('city_id');
                    
                    if (data.data.company_name && companyNameField) {
                        companyNameField.value = data.data.company_name;
                    }
                    
                    if (data.data.billing_address && addressField) {
                        addressField.value = data.data.billing_address;
                    }
                    
                    if (data.data.state_id && stateField) {
                        stateField.value = data.data.state_id;
                        // Trigger state change event to ensure proper handling
                        const changeEvent = new Event('change', { bubbles: true });
                        stateField.dispatchEvent(changeEvent);
                    }
                    
                    if (data.data.pincode && postalCodeField) {
                        postalCodeField.value = data.data.pincode;
                    }
                    
                    if (data.data.pan && panField) {
                        panField.value = data.data.pan;
                    }
                    
                    if (data.data.city && cityField) {
                        cityField.value = data.data.city;
                    }
                    
                    let successMsg = '<small class="text-success"><i class="fas fa-check"></i> GST details fetched successfully';
                    if (data.from_cache) {
                        successMsg += ' (from cache)';
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

    // Dynamic state loading based on country selection
    const countrySelect = document.getElementById('country_id');
    const stateSelect = document.getElementById('state_id');
    
    function loadStatesForCountry(countryId, preserveSelectedStateId = null) {
        if (!countryId) {
            stateSelect.innerHTML = '<option value="">Select State</option>';
            stateSelect.disabled = false;
            return;
        }
        
        // Clear and disable state dropdown while loading
        stateSelect.innerHTML = '<option value="">Loading states...</option>';
        stateSelect.disabled = true;
        
        // Fetch states for selected country
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
            stateSelect.innerHTML = '<option value="">Select State</option>';
            if (data && data.length > 0) {
                data.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.id;
                    option.textContent = state.name;
                    // Preserve selected state if country hasn't changed
                    if (preserveSelectedStateId && preserveSelectedStateId == state.id) {
                        option.selected = true;
                    }
                    stateSelect.appendChild(option);
                });
            }
            stateSelect.disabled = false;
            resetAutoSaveTimer();
        })
        .catch(error => {
            console.error('Error loading states:', error);
            stateSelect.innerHTML = '<option value="">Error loading states</option>';
            stateSelect.disabled = false;
        });
    }
    
    if (countrySelect && stateSelect) {
        // Load states on page load if country is already selected
        const initialCountryId = countrySelect.value;
        const initialStateId = stateSelect.value;
        if (initialCountryId) {
            loadStatesForCountry(initialCountryId, initialStateId);
        }
        
        // Handle country change
        countrySelect.addEventListener('change', function() {
            loadStatesForCountry(this.value);
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
        const requiredFields = form.querySelectorAll('[required]');
        
        // Clear previous validation
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        requiredFields.forEach(field => {
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
        
        const formData = new FormData(document.getElementById('startupZoneForm'));
        
        // Show saving indicator (optional, can be removed for even less overhead)
        const indicator = document.getElementById('autoSaveIndicator');
        indicator.classList.remove('d-none');
        indicator.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        
        fetch('{{ route("startup-zone.auto-save") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                indicator.innerHTML = '<i class="fas fa-check"></i> Saved';
                updateProgress(data.progress);
                setTimeout(() => {
                    indicator.classList.add('d-none');
                }, 1500);
            }
        })
        .catch(error => {
            // Silently fail - session storage is optional
            indicator.classList.add('d-none');
        });
    }

    function updateProgress(percentage) {
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        if (progressBar && progressText) {
            progressBar.style.width = percentage + '%';
            progressText.textContent = percentage + '% Complete';
        }
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
        
        const form = document.getElementById('startupZoneForm');
        const formData = new FormData(form);
        
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
                // Restore draft to application
                return fetch('{{ route("startup-zone.restore-draft") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
            } else {
                throw { type: 'error', message: data.message || 'Failed to submit form' };
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw { type: 'error', message: data.message || 'Failed to create application' };
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message if provided
                if (data.message) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        confirmButtonText: 'OK',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = data.redirect;
                    });
                } else {
                    window.location.href = data.redirect;
                }
            } else {
                throw { type: 'error', message: data.message || 'Failed to create application' };
            }
        })
        .catch(error => {
            console.error('Error object:', error);
            console.error('Error type:', error.type);
            console.error('Error errors:', error.errors);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
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
    }
});
</script>
@endpush
@endsection
