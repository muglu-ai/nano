@extends('enquiry.layout')

@section('title', 'Enquiry Form')

@push('styles')
<style>
    /* SweetAlert Custom Styling */
    .swal2-popup {
        border-radius: 15px;
    }

    .swal2-title {
        color: #333;
        font-weight: 600;
    }

    .swal2-content {
        color: #666;
    }

    .swal2-confirm {
        background-color: #20b2aa !important;
        border-color: #20b2aa !important;
    }

    .swal2-confirm:hover {
        background-color: #1a9b94 !important;
        border-color: #1a9b94 !important;
    }

    .swal2-error {
        border-color: #dc3545;
    }

    .swal2-icon.swal2-error .swal2-x-mark {
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-file-alt me-2"></i>Enquiry Form</h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
        <!-- Progress Indicator -->
        <div class="progress-container">
            <div class="step-indicator">
                <div class="step-item active">
                    <div class="step-number">1</div>
                    <div class="step-label">Enquiry</div>
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

        {{-- Errors will be shown via SweetAlert --}}

        <form action="{{ route('enquiry.submit') }}" method="POST" id="enquiryForm">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">
            <input type="hidden" name="event_year" value="{{ $event->event_year ?? date('Y') }}">

            <!-- Select Sector -->
            <div class="form-section">
                <label class="form-label">Select Sector <span class="required">*</span></label>
                <select name="sector" class="form-select" required>
                    <option value="">-- Select Sector --</option>
                    @foreach($sectors ?? [] as $sector)
                        <option value="{{ $sector }}" {{ old('sector') == $sector ? 'selected' : '' }}>{{ $sector }}</option>
                    @endforeach
                </select>
                @error('sector')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Want Information About -->
            <div class="form-section">
                <label class="form-label">Want Information About <span class="required">*</span></label>
                <div class="checkbox-group">
                    @foreach(\App\Models\EnquiryInterest::getInterestTypes() as $key => $label)
                        <div class="checkbox-item">
                            <input type="checkbox" 
                                   name="interests[]" 
                                   id="interest_{{ $key }}" 
                                   value="{{ $key }}"
                                   {{ in_array($key, $preSelectedInterests ?? []) ? 'checked' : '' }}>
                            <label for="interest_{{ $key }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                <div id="interest_other_container" style="display: none; margin-top: 1rem;">
                    <input type="text" 
                           name="interest_other" 
                           class="form-control" 
                           placeholder="Please specify other interest">
                </div>
                @error('interests')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Name -->
            <div class="form-section">
                <label class="form-label">Name <span class="required">*</span></label>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <select name="title" class="form-select">
                            <option value="">Title</option>
                            <option value="Mr" {{ old('title') == 'Mr' ? 'selected' : '' }}>Mr</option>
                            <option value="Mrs" {{ old('title') == 'Mrs' ? 'selected' : '' }}>Mrs</option>
                            <option value="Ms" {{ old('title') == 'Ms' ? 'selected' : '' }}>Ms</option>
                            <option value="Dr" {{ old('title') == 'Dr' ? 'selected' : '' }}>Dr</option>
                            <option value="Prof" {{ old('title') == 'Prof' ? 'selected' : '' }}>Prof</option>
                        </select>
                    </div>
                    <div class="col-md-9 mb-3">
                        <input type="text" 
                               name="name" 
                               class="form-control" 
                               placeholder="Name" 
                               value="{{ old('name') }}" 
                               required>
                        @error('name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Organisation -->
            <div class="form-section">
                <label class="form-label">Organisation <span class="required">*</span></label>
                <input type="text" 
                       name="organisation" 
                       class="form-control" 
                       placeholder="Organisation" 
                       value="{{ old('organisation') }}" 
                       required>
                @error('organisation')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Designation -->
            <div class="form-section">
                <label class="form-label">Designation <span class="required">*</span></label>
                <input type="text" 
                       name="designation" 
                       class="form-control" 
                       placeholder="Designation" 
                       value="{{ old('designation') }}" 
                       required>
                @error('designation')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-section">
                <label class="form-label">Email Address <span class="required">*</span></label>
                <input type="email" 
                       name="email" 
                       class="form-control" 
                       placeholder="Email Address" 
                       value="{{ old('email') }}" 
                       required>
                @error('email')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Contact Number -->
            <div class="form-section">
                <label class="form-label">Contact Number <span class="required">*</span></label>
                <input type="tel" 
                       name="phone_number" 
                       id="phone_number" 
                       class="form-control" 
                       value="{{ old('phone_number') }}" 
                       placeholder=""
                       maxlength="15"
                       pattern="[0-9]*"
                       inputmode="numeric"
                       required>
                <input type="hidden" name="phone_country_code" id="phone_country_code">
                @error('phone_number')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Comment -->
            <div class="form-section">
                <label class="form-label">Comment <span class="required">*</span></label>
                <textarea name="comments" 
                          class="form-control" 
                          rows="4" 
                          maxlength="1000" 
                          placeholder="Enter your comment" 
                          required>{{ old('comments') }}</textarea>
                <div class="char-counter" id="charCounter">1000 characters remaining</div>
                @error('comments')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- Country -->
            <div class="form-section">
                <label class="form-label">Country <span class="required">*</span></label>
                <select name="country" id="country" class="form-select" required>
                    <option value="">-- Select Country --</option>
                    @foreach($countries ?? [] as $country)
                        <option value="{{ $country->name }}" {{ old('country', 'India') == $country->name ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('country')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- State -->
            <div class="form-section">
                <label class="form-label">State <span class="required">*</span></label>
                <select name="state" id="state" class="form-select" required>
                    <option value="">-- Select State --</option>
                    @if(isset($defaultStateId) && $defaultStateId)
                        <option value="{{ $defaultStateId }}" {{ old('state', $defaultStateId) == $defaultStateId ? 'selected' : '' }}>{{ $defaultStateId }}</option>
                    @endif
                </select>
                @error('state')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- City -->
            <div class="form-section">
                <label class="form-label">City <span class="required">*</span></label>
                <input type="text" 
                       name="city" 
                       class="form-control" 
                       placeholder="City" 
                       value="{{ old('city') }}" 
                       required>
                @error('city')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- How did you know about this event? -->
            <div class="form-section">
                <label class="form-label">How did you know about this event? <span class="required">*</span></label>
                <select name="referral_source" class="form-select" required>
                    <option value="">-- Select --</option>
                    <option value="Brochure" {{ old('referral_source') == 'Brochure' ? 'selected' : '' }}>Brochure</option>
                    <option value="Colleague" {{ old('referral_source') == 'Colleague' ? 'selected' : '' }}>Colleague</option>
                    <option value="Link on Site" {{ old('referral_source') == 'Link on Site' ? 'selected' : '' }}>Link on Site</option>
                    <option value="Previous Attendee" {{ old('referral_source') == 'Previous Attendee' ? 'selected' : '' }}>Previous Attendee</option>
                    <option value="Internet search" {{ old('referral_source') == 'Internet search' ? 'selected' : '' }}>Internet search</option>
                    <option value="Sales Call" {{ old('referral_source') == 'Sales Call' ? 'selected' : '' }}>Sales Call</option>
                    <option value="Association" {{ old('referral_source') == 'Association' ? 'selected' : '' }}>Association</option>
                    <option value="Direct Mailer" {{ old('referral_source') == 'Direct Mailer' ? 'selected' : '' }}>Direct Mailer</option>
                    <option value="News Paper Ad" {{ old('referral_source') == 'News Paper Ad' ? 'selected' : '' }}>News Paper Ad</option>
                    <option value="Trade Publication" {{ old('referral_source') == 'Trade Publication' ? 'selected' : '' }}>Trade Publication</option>
                    <option value="Invitation from Exhibitor" {{ old('referral_source') == 'Invitation from Exhibitor' ? 'selected' : '' }}>Invitation from Exhibitor</option>
                    <option value="Hoarding" {{ old('referral_source') == 'Hoarding' ? 'selected' : '' }}>Hoarding</option>
                    <option value="Others" {{ old('referral_source') == 'Others' ? 'selected' : '' }}>Others</option>
                </select>
                @error('referral_source')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <!-- reCAPTCHA (Enterprise v3 - invisible) -->
            @if(config('constants.RECAPTCHA_ENABLED', false))
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            @error('recaptcha')
                <div class="error-message">{{ $message }}</div>
            @enderror
            @endif

            <!-- Submit Button -->
            <div class="form-section">
                <button type="submit" class="btn-submit" id="submitBtn">
                    CONTINUE <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Show validation errors via SweetAlert if any
    @if ($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            const errors = @json($errors->all());
            let errorMessage = '<ul style="text-align: left; margin: 10px 0;">';
            errors.forEach(function(error) {
                errorMessage += '<li>' + error + '</li>';
            });
            errorMessage += '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: errorMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#20b2aa',
                width: '600px'
            });
        });
    @endif

    // Initialize intl-tel-input
    const phoneInput = document.getElementById('phone_number');
    const phoneCountryCode = document.getElementById('phone_country_code');
    let iti = null;

    if (phoneInput) {
        // Ensure placeholder is empty
        phoneInput.placeholder = '';
        
        iti = window.intlTelInput(phoneInput, {
            initialCountry: 'in',
            preferredCountries: ['in', 'us', 'gb'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            separateDialCode: true,
            nationalMode: false,
            autoPlaceholder: 'off', // Disable automatic placeholder
        });

        // Remove placeholder that intl-tel-input might add
        phoneInput.placeholder = '';
        
        // Ensure placeholder stays empty after initialization
        setTimeout(function() {
            phoneInput.placeholder = '';
        }, 100);
        setTimeout(function() {
            phoneInput.placeholder = '';
        }, 300);

        // Restrict input to numbers only and limit length
        phoneInput.addEventListener('input', function(e) {
            // Remove any non-numeric characters
            let value = e.target.value.replace(/[^0-9]/g, '');
            
            // Limit to 15 digits (max length for phone numbers)
            if (value.length > 15) {
                value = value.substring(0, 15);
            }
            
            // Update the input value
            if (e.target.value !== value) {
                e.target.value = value;
            }
        });

        // Prevent non-numeric characters on keypress
        phoneInput.addEventListener('keypress', function(e) {
            // Allow: backspace, delete, tab, escape, enter, and numbers
            if ([46, 8, 9, 27, 13, 110, 190].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Ensure that it is a number and stop the keypress
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        // Prevent paste of non-numeric characters
        phoneInput.addEventListener('paste', function(e) {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numbers = paste.replace(/[^0-9]/g, '');
            const currentValue = phoneInput.value.replace(/[^0-9]/g, '');
            const newValue = (currentValue + numbers).substring(0, 15);
            phoneInput.value = newValue;
        });

        phoneInput.addEventListener('countrychange', function() {
            const countryData = iti.getSelectedCountryData();
            phoneCountryCode.value = countryData.dialCode;
            // Ensure placeholder stays empty on country change
            phoneInput.placeholder = '';
        });

        // Set initial country code
        const initialCountryData = iti.getSelectedCountryData();
        phoneCountryCode.value = initialCountryData.dialCode;
    }

    // Character counter for comments
    const commentsTextarea = document.querySelector('textarea[name="comments"]');
    const charCounter = document.getElementById('charCounter');
    const maxLength = 1000;

    if (commentsTextarea && charCounter) {
        function updateCharCounter() {
            const remaining = maxLength - commentsTextarea.value.length;
            charCounter.textContent = remaining + ' characters remaining';
            
            charCounter.classList.remove('warning', 'danger');
            if (remaining < 50) {
                charCounter.classList.add('danger');
            } else if (remaining < 100) {
                charCounter.classList.add('warning');
            }
        }

        commentsTextarea.addEventListener('input', updateCharCounter);
        updateCharCounter();
    }

    // Show/hide "other" interest input
    const otherCheckbox = document.getElementById('interest_other');
    const otherContainer = document.getElementById('interest_other_container');
    const interestCheckboxes = document.querySelectorAll('input[name="interests[]"]');

    interestCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            if (this.value === 'other' && this.checked) {
                otherContainer.style.display = 'block';
            } else if (this.value === 'other' && !this.checked) {
                otherContainer.style.display = 'none';
            }
        });
    });

    // Check if "other" is pre-selected
    if (otherCheckbox && otherCheckbox.checked) {
        otherContainer.style.display = 'block';
    }

    // Form submission with reCAPTCHA
    const form = document.getElementById('enquiryForm');
    const submitBtn = document.getElementById('submitBtn');

    // Client-side validation function
    function validateForm() {
        const errors = [];

        // Validate interests
        const interests = Array.from(document.querySelectorAll('input[name="interests[]"]:checked'));
        if (interests.length === 0) {
            errors.push('Please select at least one interest.');
        }

        // Validate "other" interest detail if "other" is selected
        const otherChecked = interests.some(interest => interest.value === 'other');
        if (otherChecked) {
            const otherDetail = document.querySelector('input[name="interest_other"]').value.trim();
            if (!otherDetail) {
                errors.push('Please specify the other interest.');
            }
        }

        // Validate required fields
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (field.type === 'checkbox' || field.type === 'radio') {
                // Skip checkbox groups (handled separately)
                return;
            }
            
            if (!field.value.trim()) {
                const label = form.querySelector(`label[for="${field.id}"]`) || 
                             field.closest('.form-section')?.querySelector('.form-label');
                const fieldName = label ? label.textContent.replace('*', '').trim() : field.name;
                errors.push(`${fieldName} is required.`);
            }
        });

        // Validate email format
        const emailField = form.querySelector('input[name="email"]');
        if (emailField && emailField.value.trim()) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value.trim())) {
                errors.push('Please enter a valid email address.');
            }
        }

        // Validate phone number
        if (iti) {
            if (!iti.isValidNumber()) {
                errors.push('Please enter a valid contact number.');
            }
        }

        return errors;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // Client-side validation
        const validationErrors = validateForm();
        if (validationErrors.length > 0) {
            let errorMessage = '<ul style="text-align: left; margin: 10px 0; padding-left: 20px;">';
            validationErrors.forEach(function(error) {
                errorMessage += '<li>' + error + '</li>';
            });
            errorMessage += '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                html: errorMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#20b2aa',
                width: '600px'
            });
            return;
        }

        // Update phone country code before submit
        if (iti) {
            const countryData = iti.getSelectedCountryData();
            phoneCountryCode.value = countryData.dialCode;
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

        @if(config('constants.RECAPTCHA_ENABLED', false))
        // Execute reCAPTCHA
        if (typeof grecaptcha !== 'undefined' && grecaptcha.enterprise) {
            grecaptcha.enterprise.ready(function() {
                grecaptcha.enterprise.execute('{{ config('services.recaptcha.site_key') }}', { action: 'submit' })
                    .then(function(token) {
                        // Add token to form
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = 'g-recaptcha-response';
                        tokenInput.value = token;
                        form.appendChild(tokenInput);

                        // Submit form
                        form.submit();
                    })
                        .catch(function(err) {
                            console.error('reCAPTCHA error:', err);
                            Swal.fire({
                                icon: 'error',
                                title: 'reCAPTCHA Error',
                                text: 'reCAPTCHA verification failed. Please try again.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#20b2aa'
                            });
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'CONTINUE <i class="fas fa-arrow-right ms-2"></i>';
                        });
            });
        } else {
            form.submit();
        }
        @else
        form.submit();
        @endif
    });

    // Update progress bar
    function updateProgress() {
        const formElement = document.getElementById('enquiryForm');
        const inputs = formElement.querySelectorAll('input[required], select[required], textarea[required]');
        let filled = 0;
        inputs.forEach(input => {
            if (input.value.trim() !== '') {
                filled++;
            }
        });
        const progress = (filled / inputs.length) * 100;
        document.getElementById('progressFill').style.width = progress + '%';
    }

    form.querySelectorAll('input, select, textarea').forEach(element => {
        element.addEventListener('input', updateProgress);
        element.addEventListener('change', updateProgress);
    });
    updateProgress();

    // Load states based on country selection using GeoController API
    const countrySelect = document.getElementById('country');
    const stateSelect = document.getElementById('state');
    
    function loadStatesForCountry(countryName) {
        if (!countryName || countryName === 'Other' || countryName === '') {
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
            } else if (stateSelect.options.length > 1 && countryName === 'India') {
                // If no old value but states are loaded and country is India, select first state as default
                const defaultState = '{{ $defaultStateId ?? "" }}';
                if (defaultState) {
                    stateSelect.value = defaultState;
                } else if (stateSelect.options[1]) {
                    stateSelect.value = stateSelect.options[1].value;
                }
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
        
        // Load states on page load if country is pre-selected (India by default)
        const initialCountry = countrySelect.value;
        if (initialCountry && initialCountry === 'India') {
            loadStatesForCountry(initialCountry);
        }
    }
</script>
@endpush
