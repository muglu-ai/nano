@extends('enquiry.layout')

@section('title', 'VISA Clearance Registration')

@push('styles')
<style>
    .section-subtitle {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
    }

    /* Validation styling */
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }

    .invalid-feedback:not(:empty) {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-passport me-2"></i>VISA Clearance Registration</h2>
        <p>{{ config('constants.EVENT_NAME', 'Event') }} {{ config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
        <!-- Progress Indicator -->
        <div class="progress-container">
            <div class="step-indicator">
                <div class="step-item active">
                    <div class="step-number">1</div>
                    <div class="step-label">Delegate Details</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-label">Confirmation</div>
                </div>
            </div>
            <div class="progress-bar-custom">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('visa.clearance.submit') }}" method="POST" id="visaClearanceForm">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">
            <input type="hidden" name="event_year" value="{{ $event->event_year ?? date('Y') }}">

            <!-- Delegate Details -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-user-circle"></i>
                    <span>Delegate Details</span>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Organisation Name <span class="required">*</span></label>
                        <input type="text" name="organisation_name" class="form-control"
                               value="{{ old('organisation_name') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Designation <span class="required">*</span></label>
                        <input type="text" name="designation" class="form-control"
                               value="{{ old('designation') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Passport Name <span class="required">*</span></label>
                        <input type="text" name="passport_name" class="form-control"
                               value="{{ old('passport_name') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Father's / Husband's Name <span class="required">*</span></label>
                        <input type="text" name="father_husband_name" class="form-control"
                               value="{{ old('father_husband_name') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Birth <span class="required">*</span></label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Place of Birth <span class="required">*</span></label>
                        <input type="text" name="place_of_birth" class="form-control"
                               value="{{ old('place_of_birth') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nationality <span class="required">*</span></label>
                        <select name="nationality" id="nationality" class="form-select" required>
                            <option value="">-- Select Nationality --</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->name }}" {{ old('nationality') == $country->name ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                        @error('nationality')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Passport Details -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-id-card"></i>
                    <span>Passport Details</span>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Passport Number <span class="required">*</span></label>
                        <input type="text" name="passport_number" class="form-control"
                               value="{{ old('passport_number') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Issue <span class="required">*</span></label>
                        <input type="date" name="passport_issue_date" class="form-control"
                               value="{{ old('passport_issue_date') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Place of Issue <span class="required">*</span></label>
                        <input type="text" name="passport_issue_place" class="form-control"
                               value="{{ old('passport_issue_place') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Expiry <span class="required">*</span></label>
                        <input type="date" name="passport_expiry_date" class="form-control"
                               value="{{ old('passport_expiry_date') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Entry Date in India <span class="required">*</span></label>
                        <input type="date" name="entry_date_india" class="form-control"
                               value="{{ old('entry_date_india') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Exit Date from India <span class="required">*</span></label>
                        <input type="date" name="exit_date_india" class="form-control"
                               value="{{ old('exit_date_india') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <!-- Contact Details -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-phone"></i>
                    <span>Contact Details</span>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mobile Number <span class="required">*</span></label>
                        <input type="tel"
                               name="phone_number"
                               id="phone_number"
                               class="form-control"
                               value="{{ old('phone_number') }}"
                               maxlength="20"
                               required>
                        <input type="hidden" name="phone_country_code" id="phone_country_code">
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <!-- Address in Country of Residence -->
            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-home"></i>
                    <span>Address in Country of Residence</span>
                </div>

                <div class="mb-3">
                    <label class="form-label">Address Line 1 <span class="required">*</span></label>
                    <input type="text" name="address_line1" class="form-control"
                           value="{{ old('address_line1') }}" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address Line 2</label>
                    <input type="text" name="address_line2" class="form-control"
                           value="{{ old('address_line2') }}">
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Country <span class="required">*</span></label>
                        <select name="country" id="country" class="form-select" required>
                            <option value="">-- Select Country --</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country->name }}" {{ old('country') == $country->name ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                        @error('country')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                   
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State <span class="required">*</span></label>
                        <select name="state" id="state" class="form-select" required>
                            <option value="">-- Select State --</option>
                        </select>
                        <div class="invalid-feedback"></div>
                        @error('state')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                     <div class="col-md-4 mb-3">
                        <label class="form-label">City <span class="required">*</span></label>
                        <input type="text" name="city" class="form-control"
                               value="{{ old('city') }}" required>
                        <div class="invalid-feedback"></div>
                        @error('city')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Postal Code <span class="required">*</span></label>
                        <input type="text" name="postal_code" class="form-control"
                               value="{{ old('postal_code') }}" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="form-section">
                <button type="submit" class="btn-submit" id="submitBtn">
                    SUBMIT <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialize intl-tel-input similarly to enquiry form
    const phoneInputVisa = document.getElementById('phone_number');
    const phoneCountryCodeVisa = document.getElementById('phone_country_code');
    let itiVisa = null;

    if (phoneInputVisa) {
        phoneInputVisa.placeholder = '';
        itiVisa = window.intlTelInput(phoneInputVisa, {
            initialCountry: 'in',
            preferredCountries: ['in', 'us', 'gb'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            separateDialCode: true,
            nationalMode: false,
            autoPlaceholder: 'off',
        });

        phoneInputVisa.placeholder = '';

        phoneInputVisa.addEventListener('countrychange', function () {
            const countryData = itiVisa.getSelectedCountryData();
            phoneCountryCodeVisa.value = countryData.dialCode;
        });

        const initialCountryDataVisa = itiVisa.getSelectedCountryData();
        phoneCountryCodeVisa.value = initialCountryDataVisa.dialCode;

        // Basic numeric restriction
        phoneInputVisa.addEventListener('input', function (e) {
            let value = e.target.value.replace(/[^0-9]/g, '');
            if (value.length > 20) {
                value = value.substring(0, 20);
            }
            if (e.target.value !== value) {
                e.target.value = value;
            }
        });
    }

    // Form validation function
    function validateVisaForm() {
        let isValid = true;
        const form = document.getElementById('visaClearanceForm');
        
        // Clear previous validation
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
        
        // Get all required fields
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            // Skip hidden fields
            if (field.type === 'hidden') {
                return;
            }
            
            let fieldValue = field.value;
            
            // Handle select fields
            if (field.tagName === 'SELECT') {
                if (!fieldValue || fieldValue.trim() === '' || fieldValue === '-- Select --' || fieldValue === '-- Select Country --' || fieldValue === '-- Select State --' || fieldValue === '-- Select Nationality --') {
                    field.classList.add('is-invalid');
                    const feedback = field.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'This field is required.';
                    }
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
                return;
            }
            
            // Handle text inputs, date inputs, etc.
            if (!fieldValue || fieldValue.trim() === '') {
                field.classList.add('is-invalid');
                const feedback = field.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = 'This field is required.';
                }
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
                
                // Additional validations
                if (field.type === 'email' && !isValidEmail(fieldValue)) {
                    field.classList.add('is-invalid');
                    const feedback = field.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = 'Please enter a valid email address.';
                    }
                    isValid = false;
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

    // Add event listeners to clear validation on input/change
    const formVisa = document.getElementById('visaClearanceForm');
    if (formVisa) {
        formVisa.querySelectorAll('input, select, textarea').forEach(field => {
            field.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    const feedback = this.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = '';
                    }
                }
            });
            
            field.addEventListener('change', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    const feedback = this.nextElementSibling;
                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = '';
                    }
                }
            });
        });
    }

    // Simple progress bar update
    function updateProgressVisa() {
        const inputs = formVisa.querySelectorAll('input[required], select[required]');
        let filled = 0;
        inputs.forEach(input => {
            if (input.value && input.value.trim() !== '' && input.value !== '-- Select Country --' && input.value !== '-- Select State --' && input.value !== '-- Select Nationality --') {
                filled++;
            }
        });
        const progress = (filled / inputs.length) * 100;
        document.getElementById('progressFill').style.width = progress + '%';
    }
    formVisa.querySelectorAll('input, select').forEach(el => {
        el.addEventListener('input', updateProgressVisa);
        el.addEventListener('change', updateProgressVisa);
    });
    updateProgressVisa();

    // Add form submission validation
    formVisa.addEventListener('submit', function(e) {
        if (!validateVisaForm()) {
            e.preventDefault();
            return false;
        }
    });

    // Load states based on country selection using GeoController API (similar to enquiry form)
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');
    
    function loadStatesForCountry(countryName) {
        if (!countryName || countryName === '' || countryName === '-- Select Country --') {
            stateSelect.innerHTML = '<option value="">-- Select State --</option>';
            stateSelect.disabled = false;
            return;
        }
        
        stateSelect.innerHTML = '<option value="">Loading states...</option>';
        stateSelect.disabled = true;
        
        // Use the GeoController API route: /api/states/{country}
        // GeoController handles country names, codes, or IDs automatically
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
            stateSelect.innerHTML = '<option value="">-- Select State --</option>';
            if (data && Array.isArray(data) && data.length > 0) {
                data.forEach(state => {
                    const option = document.createElement('option');
                    // Store state name (not ID) to match the form requirement
                    const stateName = state.name || state.state_name || state;
                    option.value = stateName;
                    option.textContent = stateName;
                    stateSelect.appendChild(option);
                });
            }
            stateSelect.disabled = false;
            
            // Restore old value if exists
            const oldState = '{{ old("state") }}';
            if (oldState) {
                stateSelect.value = oldState;
            }
        })
        .catch(error => {
            console.error('Error loading states:', error);
            stateSelect.innerHTML = '<option value="">-- Select State --</option>';
            stateSelect.disabled = false;
        });
    }
    
    if (countrySelect && stateSelect) {
        // Load states on country change
        countrySelect.addEventListener('change', function() {
            loadStatesForCountry(this.value);
        });
        
        // Load states on page load if country is pre-selected
        const initialCountry = countrySelect.value;
        if (initialCountry && initialCountry !== '' && initialCountry !== '-- Select Country --') {
            loadStatesForCountry(initialCountry);
        }
    }
</script>
@endpush


