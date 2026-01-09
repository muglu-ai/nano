@extends('enquiry.layout')

@section('title', 'Register for Tickets')

@push('head-links')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css">
@endpush

@push('styles')
<style>
    .registration-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .registration-container .registration-progress {
        margin-bottom: 2rem;
    }

    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e0e0e0;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: var(--text-primary);
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--progress-inactive);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary-color);
    }

    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-control, .form-select {
        background: #ffffff;
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        color: var(--text-primary);
        transition: all 0.3s;
    }

    .form-control:focus, .form-select:focus {
        background: #ffffff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(106, 27, 154, 0.25);
        color: var(--text-primary);
        outline: none;
    }

    .form-control::placeholder {
        color: var(--text-light);
    }

    .form-control option {
        background: #ffffff;
        color: var(--text-primary);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-dark) 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
    }

    .delegate-form {
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .text-danger {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .text-muted {
        color: var(--text-secondary);
    }

    .alert {
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-danger {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }

    .alert-success {
        background: #d4edda;
        border-left: 4px solid #28a745;
        color: #155724;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-dark) 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 1rem 3rem;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-ticket-alt me-2"></i>Register for Tickets</h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
        <!-- Progress Bar -->
        @include('tickets.public.partials.progress-bar', ['currentStep' => 1])
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tickets.store', $event->slug ?? $event->id) }}" method="POST" id="registrationForm">
            @csrf

            <!-- Registration Information Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Registration Information
                </h4>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label required-field">Ticket Type</label>
                        @if(isset($isTicketTypeLocked) && $isTicketTypeLocked)
                            {{-- Hidden field to submit the value --}}
                            <input type="hidden" name="ticket_type_id" value="{{ $selectedTicketType->slug }}">
                            {{-- Display field (readonly) --}}
                            <input type="text" class="form-control" 
                                   value="{{ $selectedTicketType->name }} - ₹{{ number_format($selectedTicketType->getCurrentPrice($selectedNationality ?? 'national'), 0) }}" 
                                   readonly 
                                   style="background-color: #e9ecef; cursor: not-allowed;">
                        @else
                            <select name="ticket_type_id" class="form-select" required>
                                <option value="">Select Ticket Type</option>
                                @foreach($ticketTypes as $ticketType)
                                    <option value="{{ $ticketType->slug }}" 
                                            data-price="{{ $ticketType->getCurrentPrice('national') }}"
                                            {{ (old('ticket_type_id') == $ticketType->slug || (isset($selectedTicketType) && $selectedTicketType && $selectedTicketType->id == $ticketType->id)) ? 'selected' : '' }}>
                                        {{ $ticketType->name }} - ₹{{ number_format($ticketType->getCurrentPrice('national'), 0) }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('ticket_type_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Number of Delegates</label>
                        <input type="number" name="delegate_count" class="form-control" 
                               value="{{ old('delegate_count', 1) }}" 
                               min="1" 
                               max="50" 
                               required 
                               id="delegate_count">
                        @error('delegate_count')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Nationality</label>
                        @if(isset($isNationalityLocked) && $isNationalityLocked)
                            {{-- Hidden field to submit the value --}}
                            <input type="hidden" name="nationality" value="{{ $selectedNationality }}">
                            {{-- Display field (readonly) --}}
                            <input type="text" class="form-control" 
                                   value="{{ $selectedNationality == 'national' ? 'Indian' : 'International' }}" 
                                   readonly 
                                   style="background-color: #e9ecef; cursor: not-allowed;">
                        @else
                            <select name="nationality" class="form-select" required>
                                <option value="">Select Nationality</option>
                                <option value="national" {{ old('nationality', $selectedNationality ?? '') == 'national' ? 'selected' : '' }}>Indian</option>
                                <option value="international" {{ old('nationality', $selectedNationality ?? '') == 'international' ? 'selected' : '' }}>International</option>
                            </select>
                        @endif
                        @error('nationality')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Organisation Information Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-building"></i>
                    Organisation Information
                </h4>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label required-field">Organisation Name</label>
                        <input type="text" name="organisation_name" class="form-control" 
                               value="{{ old('organisation_name') }}" 
                               placeholder="Enter organisation name" required>
                        @error('organisation_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Industry Sector</label>
                        <select name="industry_sector" class="form-select" required>
                            <option value="">Select Industry Sector</option>
                            @foreach($sectors as $sector)
                                <option value="{{ $sector }}" {{ old('industry_sector') == $sector ? 'selected' : '' }}>
                                    {{ $sector }}
                                </option>
                            @endforeach
                        </select>
                        @error('industry_sector')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Organisation Type</label>
                        <select name="organisation_type" class="form-select" required>
                            <option value="">Select Organisation Type</option>
                            @foreach($organizationTypes as $orgType)
                                <option value="{{ $orgType }}" {{ old('organisation_type') == $orgType ? 'selected' : '' }}>
                                    {{ $orgType }}
                                </option>
                            @endforeach
                        </select>
                        @error('organisation_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Country</label>
                        <input type="text" name="company_country" class="form-control" id="company_country"
                               value="{{ old('company_country', 'India') }}" 
                               placeholder="Enter country" required>
                        @error('company_country')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">State</label>
                        <select name="company_state" class="form-select" id="company_state" required>
                            <option value="">-- Select State --</option>
                            @if(old('company_state'))
                                <option value="{{ old('company_state') }}" selected>{{ old('company_state') }}</option>
                            @endif
                        </select>
                        @error('company_state')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">City</label>
                        <input type="text" name="company_city" class="form-control" 
                               value="{{ old('company_city') }}" 
                               placeholder="Enter city" required>
                        @error('company_city')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control" 
                               value="{{ old('postal_code') }}" 
                               placeholder="Enter postal code" required>
                        @error('postal_code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" id="company_phone" 
                               value="{{ old('phone') }}" 
                               placeholder="Enter phone number" 
                               pattern="[0-9]*"
                               inputmode="numeric"
                               required>
                        <input type="hidden" name="phone_country_code" id="company_phone_country_code" value="{{ old('phone_country_code', '+91') }}">
                        @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email') }}" 
                               placeholder="Enter email address" required>
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Delegates Information Section -->
            <div class="form-section" id="delegates_section" style="display: {{ old('delegate_count', 1) >= 1 ? 'block' : 'none' }};">
                <h4 class="section-title">
                    <i class="fas fa-users"></i>
                    Delegates Information
                </h4>
                <p class="text-muted mb-3">Please provide details for each delegate attending the event.</p>
                <div id="delegates_container">
                    <!-- Delegates will be dynamically added here -->
                </div>
            </div>

            <!-- GST Information Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    GST Information
                </h4>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label required-field">Do you require GST Invoice?</label>
                        <select name="gst_required" class="form-select" id="gst_required" required>
                            <option value="0" {{ old('gst_required') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('gst_required') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('gst_required')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="gst_fields" style="display: {{ old('gst_required') == '1' ? 'block' : 'none' }};">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">GSTIN</label>
                            <div class="input-group">
                                <input type="text" name="gstin" class="form-control" 
                                       value="{{ old('gstin') }}" 
                                       placeholder="Enter GSTIN" 
                                       id="gstin_input"
                                       maxlength="15">
                                <button type="button" class="btn btn-outline-primary" id="validateGstBtn" style="display: none;">
                                    <i class="fas fa-search me-1"></i>Validate GST
                                </button>
                            </div>
                            <small class="form-text text-muted">Enter 15-character GSTIN and click "Validate GST" to auto-fill details</small>
                            @error('gstin')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <div id="gst_loading" class="d-none mt-2">
                                <small class="text-info"><i class="fas fa-spinner fa-spin"></i> Validating GST...</small>
                            </div>
                            <div id="gst_validation_message" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">GST Legal Name</label>
                            <input type="text" name="gst_legal_name" class="form-control" 
                                   value="{{ old('gst_legal_name') }}" 
                                   placeholder="Enter legal name for invoice">
                            @error('gst_legal_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">GST Address</label>
                            <textarea name="gst_address" class="form-control" rows="3" 
                                      placeholder="Enter address for invoice">{{ old('gst_address') }}</textarea>
                            @error('gst_address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GST State</label>
                            <input type="text" name="gst_state" class="form-control" 
                                   value="{{ old('gst_state') }}" 
                                   placeholder="Enter state">
                            @error('gst_state')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Primary Contact Information Section (Only shown when GST is required) -->
            <div class="form-section" id="primary_contact_section" style="display: {{ old('gst_required') == '1' ? 'block' : 'none' }};">
                <h4 class="section-title">
                    <i class="fas fa-user"></i>
                    Primary Contact Information
                </h4>
                <p class="text-muted mb-3">Contact person details for invoice purposes.</p>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Full Name</label>
                        <input type="text" name="contact_name" class="form-control" 
                               value="{{ old('contact_name') }}" 
                               placeholder="Enter full name" id="contact_name">
                        @error('contact_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Email Address</label>
                        <input type="email" name="contact_email" class="form-control" 
                               value="{{ old('contact_email') }}" 
                               placeholder="Enter email address" id="contact_email">
                        @error('contact_email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Mobile Number</label>
                        <input type="tel" name="contact_phone" class="form-control" 
                               value="{{ old('contact_phone') }}" 
                               placeholder="Enter mobile number" 
                               id="contact_phone"
                               pattern="[0-9]*"
                               inputmode="numeric">
                        <input type="hidden" name="contact_phone_country_code" id="contact_phone_country_code" value="{{ old('contact_phone_country_code', '+91') }}">
                        @error('contact_phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- reCAPTCHA Token (hidden) -->
            @if(config('constants.RECAPTCHA_ENABLED', false))
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            @error('recaptcha')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            @endif

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-arrow-right me-2"></i>Continue to Preview
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- intl-tel-input -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"></script>

@if(config('constants.RECAPTCHA_ENABLED', false))
<script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delegate form generation
    const delegateCountInput = document.getElementById('delegate_count');
    const delegatesContainer = document.getElementById('delegates_container');
    const delegatesSection = document.getElementById('delegates_section');
    
    // Old values from validation errors
    const oldDelegates = @json(old('delegates', []));
    const oldDelegateCount = {{ old('delegate_count', 1) }};

    function generateDelegateForms(count) {
        delegatesContainer.innerHTML = '';
        
        for (let i = 0; i < count; i++) {
            const delegateData = oldDelegates[i] || {};
            const delegateForm = document.createElement('div');
            delegateForm.className = 'delegate-form';
            delegateForm.innerHTML = `
                <h5 class="mb-3" style="color: var(--text-primary);">Delegate ${i + 1}</h5>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label required-field">Salutation</label>
                        <select name="delegates[${i}][salutation]" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Mr" ${delegateData.salutation === 'Mr' ? 'selected' : ''}>Mr</option>
                            <option value="Mrs" ${delegateData.salutation === 'Mrs' ? 'selected' : ''}>Mrs</option>
                            <option value="Ms" ${delegateData.salutation === 'Ms' ? 'selected' : ''}>Ms</option>
                            <option value="Dr" ${delegateData.salutation === 'Dr' ? 'selected' : ''}>Dr</option>
                        </select>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label required-field">First Name</label>
                        <input type="text" name="delegates[${i}][first_name]" class="form-control" 
                               value="${delegateData.first_name || ''}" 
                               placeholder="Enter first name" required>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label required-field">Last Name</label>
                        <input type="text" name="delegates[${i}][last_name]" class="form-control" 
                               value="${delegateData.last_name || ''}" 
                               placeholder="Enter last name" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Email</label>
                        <input type="email" name="delegates[${i}][email]" class="form-control" 
                               value="${delegateData.email || ''}" 
                               placeholder="Enter email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Mobile Number</label>
                        <input type="tel" name="delegates[${i}][phone]" class="form-control delegate-phone" 
                               id="delegate_phone_${i}"
                               value="${delegateData.phone || ''}" 
                               placeholder="Enter mobile number" 
                               pattern="[0-9]*"
                               inputmode="numeric"
                               required>
                        <input type="hidden" name="delegates[${i}][phone_country_code]" id="delegate_phone_country_code_${i}" value="${delegateData.phone_country_code || '+91'}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label required-field">Job Title</label>
                        <input type="text" name="delegates[${i}][job_title]" class="form-control" 
                               value="${delegateData.job_title || ''}" 
                               placeholder="Enter job title" required>
                    </div>
                </div>
            `;
            delegatesContainer.appendChild(delegateForm);
            
            // Initialize intl-tel-input for delegate phone after a short delay to ensure DOM is ready
            setTimeout(() => {
                const delegatePhoneInput = document.getElementById(`delegate_phone_${i}`);
                const delegatePhoneCountryCode = document.getElementById(`delegate_phone_country_code_${i}`);
                if (delegatePhoneInput && typeof window.intlTelInput !== 'undefined') {
                    // Apply restriction BEFORE initializing intl-tel-input
                    restrictToNumbers(delegatePhoneInput);
                    
                    delegatePhoneInput.placeholder = '';
                    const itiDelegate = window.intlTelInput(delegatePhoneInput, {
                        initialCountry: 'in',
                        preferredCountries: ['in', 'us', 'gb'],
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                        separateDialCode: true,
                        nationalMode: false,
                        autoPlaceholder: 'off',
                    });
                    
                    // Store the instance for later use
                    delegatePhoneInstances.set(delegatePhoneInput, itiDelegate);
                    
                    // Re-apply restriction after intl-tel-input initialization to ensure it still works
                    restrictToNumbers(delegatePhoneInput);
                    
                    // Set old value if exists (clean spaces first)
                    if (delegateData.phone) {
                        let cleanPhone = delegateData.phone.toString().replace(/\s/g, '').replace(/[^\d+]/g, '');
                        itiDelegate.setNumber(cleanPhone);
                        // Clean again after setting
                        setTimeout(function() {
                            cleanPhoneNumber(delegatePhoneInput);
                        }, 100);
                    }
                    
                    delegatePhoneInput.addEventListener('countrychange', function () {
                        const countryData = itiDelegate.getSelectedCountryData();
                        delegatePhoneCountryCode.value = '+' + countryData.dialCode;
                    });
                    
                    const initialCountryData = itiDelegate.getSelectedCountryData();
                    if (delegateData.phone_country_code) {
                        delegatePhoneCountryCode.value = delegateData.phone_country_code;
                    } else {
                        delegatePhoneCountryCode.value = '+' + initialCountryData.dialCode;
                    }
                }
            }, 100);
        }
    }

    // Initialize delegate forms
    generateDelegateForms(oldDelegateCount);

    // Update delegate forms when count changes
    delegateCountInput.addEventListener('change', function() {
        const count = parseInt(this.value) || 1;
        generateDelegateForms(count);
        delegatesSection.style.display = 'block';
    });
    
    // Load states for company country
    const companyCountryInput = document.querySelector('input[name="company_country"]');
    const companyStateSelect = document.getElementById('company_state');
    
    function loadStatesForCountry(countryName) {
        if (!countryName || countryName === '' || !companyStateSelect) {
            if (companyStateSelect) {
                companyStateSelect.innerHTML = '<option value="">-- Select State --</option>';
            }
            return;
        }
        
        companyStateSelect.innerHTML = '<option value="">Loading states...</option>';
        companyStateSelect.disabled = true;
        
        const countryParam = encodeURIComponent(countryName);
        fetch(`{{ url('/api/states') }}/${countryParam}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch states');
            }
            return response.json();
        })
        .then(data => {
            companyStateSelect.innerHTML = '<option value="">-- Select State --</option>';
            if (data && Array.isArray(data) && data.length > 0) {
                data.forEach(state => {
                    const option = document.createElement('option');
                    const stateName = state.name || state.state_name || state;
                    option.value = stateName;
                    option.textContent = stateName;
                    if ('{{ old("company_state") }}' === stateName) {
                        option.selected = true;
                    }
                    companyStateSelect.appendChild(option);
                });
            }
            companyStateSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading states:', error);
            companyStateSelect.innerHTML = '<option value="">-- Select State --</option>';
            companyStateSelect.disabled = false;
        });
    }
    
    // Load states when country changes
    if (companyCountryInput && companyStateSelect) {
        companyCountryInput.addEventListener('change', function() {
            loadStatesForCountry(this.value);
        });
        
        // Load states on page load if country is already set
        if (companyCountryInput.value) {
            loadStatesForCountry(companyCountryInput.value);
        }
    }
    
    // Store delegate phone instances
    const delegatePhoneInstances = new Map();
    let itiCompany = null;
    let itiContact = null;
    
    // Add numeric-only validation for phone inputs
    function restrictToNumbers(input) {
        // Mark input as restricted to avoid duplicate handlers
        if (input.dataset.restricted === 'true') {
            return;
        }
        input.dataset.restricted = 'true';
        
        // Use beforeinput event (modern browsers) to prevent non-numeric input
        input.addEventListener('beforeinput', function(e) {
            // Allow deletion operations
            if (e.inputType === 'deleteContentBackward' || 
                e.inputType === 'deleteContentForward' || 
                e.inputType === 'deleteByCut') {
                return;
            }
            // For any text insertion, only allow numbers
            if (e.data && !/^\d+$/.test(e.data)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }, { capture: true, passive: false });
        
        // Use input event to filter out non-numeric characters and spaces in real-time (most reliable)
        input.addEventListener('input', function(e) {
            let value = e.target.value;
            // Get the current cursor position
            const cursorPos = e.target.selectionStart || 0;
            
            // Remove all non-numeric characters including spaces (keep only digits)
            const numbersOnly = value.replace(/[^\d]/g, '').replace(/\s/g, '');
            
            // If value changed (had non-numeric chars or spaces), update it immediately
            if (value !== numbersOnly) {
                e.target.value = numbersOnly;
                // Restore cursor position (adjusted for removed characters)
                const removedChars = value.length - numbersOnly.length;
                const newCursorPos = Math.max(0, cursorPos - removedChars);
                setTimeout(() => {
                    e.target.setSelectionRange(newCursorPos, newCursorPos);
                }, 0);
            }
        }, { capture: true });
        
        // Prevent non-numeric keypress (fallback for older browsers)
        input.addEventListener('keypress', function(e) {
            // Allow: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Only allow numeric keys (0-9) - both regular and numpad
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }, { capture: true });
        
        // Clean paste events - extract only numbers
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numbersOnly = paste.replace(/[^\d]/g, '');
            if (numbersOnly) {
                // Insert at cursor position
                const start = this.selectionStart || 0;
                const end = this.selectionEnd || 0;
                const currentValue = this.value;
                const newValue = currentValue.substring(0, start) + numbersOnly + currentValue.substring(end);
                this.value = newValue;
                setTimeout(() => {
                    this.setSelectionRange(start + numbersOnly.length, start + numbersOnly.length);
                }, 0);
            }
        }, { capture: true });
        
        // Also clean on blur as a safety measure (remove spaces and non-numeric)
        input.addEventListener('blur', function(e) {
            let value = e.target.value;
            // Remove spaces and non-numeric characters
            const numbersOnly = value.replace(/[^\d]/g, '').replace(/\s/g, '');
            if (value !== numbersOnly && numbersOnly) {
                // If intl-tel-input is initialized, use its setNumber method
                const itiInstance = delegatePhoneInstances.get(e.target) || 
                                 (e.target.id === 'company_phone' ? itiCompany : null) ||
                                 (e.target.id === 'contact_phone' ? itiContact : null);
                if (itiInstance) {
                    try {
                        const countryCode = itiInstance.getSelectedCountryData().dialCode;
                        itiInstance.setNumber('+' + countryCode + numbersOnly);
                    } catch(err) {
                        e.target.value = numbersOnly;
                    }
                } else {
                    e.target.value = numbersOnly;
                }
            }
        });
    }
    
    // Function to clean phone numbers by removing spaces and non-numeric characters
    function cleanPhoneNumber(input) {
        if (!input) return;
        let value = input.value || '';
        // Remove all spaces and non-numeric characters
        const cleaned = value.replace(/\s/g, '').replace(/[^\d]/g, '');
        if (value !== cleaned && cleaned) {
            // If intl-tel-input is initialized, use its setNumber method
            const itiInstance = delegatePhoneInstances.get(input) || 
                             (input.id === 'company_phone' ? itiCompany : null) ||
                             (input.id === 'contact_phone' ? itiContact : null);
            if (itiInstance) {
                try {
                    const countryCode = itiInstance.getSelectedCountryData().dialCode;
                    itiInstance.setNumber('+' + countryCode + cleaned);
                } catch(err) {
                    input.value = cleaned;
                }
            } else {
                input.value = cleaned;
            }
        } else if (value !== cleaned) {
            input.value = cleaned;
        }
    }
    
    // Wait for intlTelInput to be available
    function initializePhoneInputs() {
        if (typeof window.intlTelInput === 'undefined') {
            // Retry after a short delay if library not loaded yet
            setTimeout(initializePhoneInputs, 100);
            return;
        }
        
        // Initialize intl-tel-input for company phone
        const companyPhoneInput = document.getElementById('company_phone');
        const companyPhoneCountryCode = document.getElementById('company_phone_country_code');
        
        if (companyPhoneInput) {
            // Clean existing value (remove spaces) before initializing
            let initialValue = companyPhoneInput.value || '';
            if (initialValue) {
                initialValue = initialValue.replace(/\s/g, '').replace(/[^\d+]/g, '');
                companyPhoneInput.value = initialValue;
            }
            
            // Apply restriction BEFORE initializing intl-tel-input
            restrictToNumbers(companyPhoneInput);
            
            companyPhoneInput.placeholder = '';
            itiCompany = window.intlTelInput(companyPhoneInput, {
                initialCountry: 'in',
                preferredCountries: ['in', 'us', 'gb'],
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                separateDialCode: true,
                nationalMode: false,
                autoPlaceholder: 'off',
            });
            
            // Re-apply restriction after intl-tel-input initialization
            restrictToNumbers(companyPhoneInput);
            
            // Clean value again after initialization
            setTimeout(function() {
                cleanPhoneNumber(companyPhoneInput);
            }, 100);
            
            companyPhoneInput.addEventListener('countrychange', function () {
                const countryData = itiCompany.getSelectedCountryData();
                companyPhoneCountryCode.value = '+' + countryData.dialCode;
            });
            
            const initialCountryData = itiCompany.getSelectedCountryData();
            companyPhoneCountryCode.value = '+' + initialCountryData.dialCode;
        }
        
        // Initialize intl-tel-input for contact phone (primary contact)
        const contactPhoneInput = document.getElementById('contact_phone');
        const contactPhoneCountryCode = document.getElementById('contact_phone_country_code');
        
        if (contactPhoneInput) {
            // Clean existing value (remove spaces) before initializing
            let initialValue = contactPhoneInput.value || '';
            if (initialValue) {
                initialValue = initialValue.replace(/\s/g, '').replace(/[^\d+]/g, '');
                contactPhoneInput.value = initialValue;
            }
            
            // Apply restriction BEFORE initializing intl-tel-input
            restrictToNumbers(contactPhoneInput);
            
            contactPhoneInput.placeholder = '';
            itiContact = window.intlTelInput(contactPhoneInput, {
                initialCountry: 'in',
                preferredCountries: ['in', 'us', 'gb'],
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                separateDialCode: true,
                nationalMode: false,
                autoPlaceholder: 'off',
            });
            
            // Re-apply restriction after intl-tel-input initialization
            restrictToNumbers(contactPhoneInput);
            
            // Clean value again after initialization
            setTimeout(function() {
                cleanPhoneNumber(contactPhoneInput);
            }, 100);
            
            contactPhoneInput.addEventListener('countrychange', function () {
                const countryData = itiContact.getSelectedCountryData();
                contactPhoneCountryCode.value = '+' + countryData.dialCode;
            });
            
            const initialCountryData = itiContact.getSelectedCountryData();
            contactPhoneCountryCode.value = '+' + initialCountryData.dialCode;
        }
    }
    
    // Initialize phone inputs
    initializePhoneInputs();
    
    // Clean phone numbers on page load (remove spaces from old values)
    document.addEventListener('DOMContentLoaded', function() {
        // Wait a bit for intl-tel-input to initialize
        setTimeout(function() {
            const phoneInputs = document.querySelectorAll('input[type="tel"]');
            phoneInputs.forEach(function(input) {
                // Clean existing values (remove spaces)
                cleanPhoneNumber(input);
            });
        }, 1000);
    });
    
    // Also clean when intl-tel-input is initialized
    setTimeout(function() {
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(function(input) {
            // Clean existing values
            cleanPhoneNumber(input);
            
            // Only apply restriction if not already restricted
            if (!input.hasAttribute('data-restricted')) {
                restrictToNumbers(input);
                input.setAttribute('data-restricted', 'true');
            }
        });
    }, 500);
    
    // Clean phone numbers when form is shown (after coming back from preview)
    const form = document.getElementById('ticketRegistrationForm');
    if (form) {
        // Use MutationObserver to detect when form becomes visible
        const observer = new MutationObserver(function(mutations) {
            const phoneInputs = document.querySelectorAll('input[type="tel"]');
            phoneInputs.forEach(function(input) {
                cleanPhoneNumber(input);
            });
        });
        
        // Observe form visibility
        observer.observe(form, { attributes: true, attributeFilter: ['style', 'class'] });
        
        // Also clean on focus (when user clicks on phone field)
        document.addEventListener('focusin', function(e) {
            if (e.target && e.target.type === 'tel') {
                cleanPhoneNumber(e.target);
            }
        }, true);
    }

    // GST toggle
    const gstRequired = document.getElementById('gst_required');
    const gstFields = document.getElementById('gst_fields');
    const primaryContactSection = document.getElementById('primary_contact_section');
    const contactName = document.getElementById('contact_name');
    const contactEmail = document.getElementById('contact_email');
    const contactPhone = document.getElementById('contact_phone');

    const validateGstBtn = document.getElementById('validateGstBtn');
    const gstLoading = document.getElementById('gst_loading');
    
    gstRequired.addEventListener('change', function() {
        if (this.value === '1') {
            gstFields.style.display = 'block';
            primaryContactSection.style.display = 'block';
            if (contactName) contactName.required = true;
            if (contactEmail) contactEmail.required = true;
            if (contactPhone) contactPhone.required = true;
            if (validateGstBtn) validateGstBtn.style.display = 'inline-block';
        } else {
            gstFields.style.display = 'none';
            primaryContactSection.style.display = 'none';
            if (contactName) contactName.required = false;
            if (contactEmail) contactEmail.required = false;
            if (contactPhone) contactPhone.required = false;
            if (validateGstBtn) validateGstBtn.style.display = 'none';
        }
    });
    
    // Initialize validate button visibility
    if (gstRequired.value === '1' && validateGstBtn) {
        validateGstBtn.style.display = 'inline-block';
    }

    // GST validation via button
    const gstinInput = document.getElementById('gstin_input');
    const gstValidationMessage = document.getElementById('gst_validation_message');

    if (validateGstBtn && gstinInput) {
        validateGstBtn.addEventListener('click', function() {
            const gstin = gstinInput.value.trim().toUpperCase();
            
            // Validate format
            if (gstin.length !== 15) {
                gstValidationMessage.innerHTML = '<div class="alert alert-warning mt-2"><i class="fas fa-exclamation-triangle"></i> GSTIN must be 15 characters</div>';
                return;
            }
            
            // Validate pattern
            const gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
            if (!gstPattern.test(gstin)) {
                gstValidationMessage.innerHTML = '<div class="alert alert-warning mt-2"><i class="fas fa-exclamation-triangle"></i> Invalid GSTIN format</div>';
                return;
            }
            
            // Show loading
            validateGstBtn.disabled = true;
            gstLoading.classList.remove('d-none');
            gstValidationMessage.innerHTML = '';
            
            // Make API call
            fetch('{{ route("tickets.validate-gst") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ gstin: gstin })
            })
            .then(response => {
                const status = response.status;
                return response.json().then(data => ({ status, data }));
            })
            .then(({ status, data }) => {
                gstLoading.classList.add('d-none');
                validateGstBtn.disabled = false;
                
                if (data.success) {
                    // Auto-fill form fields
                    const legalNameInput = document.querySelector('input[name="gst_legal_name"]');
                    const addressInput = document.querySelector('textarea[name="gst_address"]');
                    const stateInput = document.querySelector('input[name="gst_state"]');
                    
                    if (data.gst.company_name && legalNameInput && !legalNameInput.value) {
                        legalNameInput.value = data.gst.company_name;
                    }
                    if (data.gst.billing_address && addressInput && !addressInput.value) {
                        addressInput.value = data.gst.billing_address;
                    }
                    if (data.gst.state_name && stateInput && !stateInput.value) {
                        stateInput.value = data.gst.state_name;
                    }
                    
                    gstValidationMessage.innerHTML = '<div class="alert alert-success mt-2"><i class="fas fa-check-circle"></i> GST validated successfully</div>';
                } else if (status === 429 || data.limit_exceeded) {
                    // Rate limit exceeded
                    gstValidationMessage.innerHTML = '<div class="alert alert-warning mt-2"><i class="fas fa-exclamation-triangle"></i> ' + data.message + '</div>';
                } else {
                    // Error or not found
                    gstValidationMessage.innerHTML = '<div class="alert alert-info mt-2"><i class="fas fa-info-circle"></i> ' + (data.message || 'GST not found. Please fill details manually.') + '</div>';
                }
            })
            .catch(error => {
                gstLoading.classList.add('d-none');
                validateGstBtn.disabled = false;
                console.error('GST validation error:', error);
                gstValidationMessage.innerHTML = '<div class="alert alert-danger mt-2"><i class="fas fa-times-circle"></i> Error validating GST. Please fill details manually.</div>';
            });
        });
    }

    // Form submission with reCAPTCHA
    const registrationForm = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');

    registrationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Remove spaces from all phone numbers before submission
        function removeSpacesFromPhone(input) {
            if (input && input.value) {
                input.value = input.value.replace(/\s/g, '');
            }
        }
        
        // Update phone numbers with full international format before submission
        // Company phone
        const companyPhoneInput = document.getElementById('company_phone');
        if (itiCompany && companyPhoneInput) {
            if (itiCompany.isValidNumber()) {
                companyPhoneInput.value = itiCompany.getNumber();
            }
            // Remove spaces
            removeSpacesFromPhone(companyPhoneInput);
        }
        
        // Contact phone
        const contactPhoneInput = document.getElementById('contact_phone');
        if (itiContact && contactPhoneInput) {
            if (itiContact.isValidNumber()) {
                contactPhoneInput.value = itiContact.getNumber();
            }
            // Remove spaces
            removeSpacesFromPhone(contactPhoneInput);
        }
        
        // Delegate phones - use stored instances
        document.querySelectorAll('.delegate-phone').forEach(function(phoneInput) {
            const iti = delegatePhoneInstances.get(phoneInput);
            if (iti && iti.isValidNumber()) {
                phoneInput.value = iti.getNumber();
            }
            // Remove spaces
            removeSpacesFromPhone(phoneInput);
        });
        
        // Also remove spaces from hidden country code fields
        document.querySelectorAll('input[name*="phone_country_code"]').forEach(function(input) {
            removeSpacesFromPhone(input);
        });
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

        @if(config('constants.RECAPTCHA_ENABLED', false))
        grecaptcha.enterprise.ready(function() {
            grecaptcha.enterprise.execute('{{ config("services.recaptcha.site_key") }}', {action: 'submit'})
                .then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                    registrationForm.submit();
                });
        });
        @else
        registrationForm.submit();
        @endif
    });

    // Display validation errors with SweetAlert
    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: '<ul style="text-align: left;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: 'var(--primary-color)'
        }).then(() => {
            // Scroll to first error
            const firstError = document.querySelector('.is-invalid, .text-danger');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    @endif
}); // End of DOMContentLoaded
</script>
@endpush
