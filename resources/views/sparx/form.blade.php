@extends('sparx.layout')

@section('title', 'NANO SparX Application Form')

@push('styles')
<style>
    .section-title { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1rem; }
    .word-counter { font-size: 0.85rem; color: var(--text-secondary); margin-top: 0.25rem; text-align: right; }
    .word-counter.warning { color: #ff9800; }
    .word-counter.danger { color: #dc3545; }
    .conditional { display: none; }
    /* Validator: red border + red circle icon on the right (match first page / ticket form) */
    .form-control.field-invalid,
    .form-select.field-invalid {
        border-color: #dc3545 !important;
        border-width: 2px !important;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    .form-control.field-invalid:focus,
    .form-select.field-invalid:focus {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    /* intl-tel-input: ensure invalid phone input shows icon (it has extra padding-left) */
    .iti .form-control.phone-input.field-invalid {
        padding-right: calc(1.5em + 0.75rem);
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-lightbulb me-2"></i>NanoSparX Application Form</h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'NanoSparX Program') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
        <!-- Progress Indicator -->
        <div class="progress-container">
            <div class="step-indicator">
                <div class="step-item active">
                    <div class="step-number">1</div>
                    <div class="step-label">Application</div>
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

        {{-- Errors shown via SweetAlert --}}

        <form action="{{ route('sparx.store') }}" method="POST" id="sparxForm">
            @csrf
            <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">
            <input type="hidden" name="event_year" value="{{ $event->event_year ?? date('Y') }}">

            <!-- SECTION 1: Personal Information -->
            <div class="form-section">
                <h3 class="section-title">1. Personal Information</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name <span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $application?->name ?? '') }}" required>
                        @error('name') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Designation <span class="required">*</span></label>
                        <input type="text" name="designation" class="form-control" value="{{ old('designation', $application?->designation ?? '') }}" required>
                        @error('designation') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Organization <span class="required">*</span></label>
                        <input type="text" name="organization" class="form-control" value="{{ old('organization', $application?->organization ?? '') }}" required>
                        @error('organization') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Contact Information -->
            <div class="form-section">
                <h3 class="section-title">2. Contact Information</h3>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Email Address <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $application?->email ?? '') }}" required>
                        @error('email') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Number <span class="required">*</span></label>
                        <input type="tel" name="phone_number" id="phone_number" class="form-control phone-input" value="{{ old('phone_number', $application?->phone_number ?? '') }}" required>
                        <input type="hidden" name="phone_country_code" id="phone_country_code" value="{{ old('phone_country_code', $application?->phone_country_code ?? '') }}">
                        @error('phone_number') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address', $application?->address ?? '') }}</textarea>
                        @error('address') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $application?->city ?? '') }}">
                        @error('city') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state', $application?->state ?? '') }}">
                        @error('state') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Country <span class="required">*</span></label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', $application?->country ?? 'India') }}" required>
                        @error('country') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $application?->postal_code ?? '') }}">
                        @error('postal_code') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Startup / Idea Details -->
            <div class="form-section">
                <h3 class="section-title">3. Startup / Idea Details</h3>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Startup / Idea Name <span class="required">*</span></label>
                        <input type="text" name="startup_idea_name" class="form-control" value="{{ old('startup_idea_name', $application?->startup_idea_name ?? '') }}" required maxlength="120">
                        @error('startup_idea_name') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Website (if any)</label>
                        <input type="url" name="website" class="form-control" value="{{ old('website', $application?->website ?? '') }}" placeholder="https://example.com">
                        @error('website') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Sector</label>
                        <select name="sector" class="form-select">
                            <option value="">-- Select Sector --</option>
                            @foreach(['Medicine', 'Electronics', 'Agriculture', 'Healthcare', 'Manufacturing', 'Environment/Energy', 'Others'] as $sector)
                                <option value="{{ $sector }}" {{ old('sector', $application?->sector ?? '') == $sector ? 'selected' : '' }}>{{ $sector }}</option>
                            @endforeach
                        </select>
                        @error('sector') <div class="error-message">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <!-- SECTION 4: Idea Description & Details -->
            <div class="form-section">
                <h3 class="section-title">4. Idea Description & Details</h3>
                <div class="mb-4">
                    <label class="form-label">Idea Description (max 500 words) <span class="required">*</span></label>
                    <textarea name="idea_description" class="form-control" rows="8" maxlength="3000" required>{{ old('idea_description', $application?->idea_description ?? '') }}</textarea>
                    <div class="word-counter" id="descCounter">500 words remaining</div>
                    @error('idea_description') <div class="error-message">{{ $message }}</div> @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label">Any Products <span class="required">*</span></label>
                    <textarea name="products" class="form-control" rows="5" required>{{ old('products', $application?->products ?? '') }}</textarea>
                    @error('products') <div class="error-message">{{ $message }}</div> @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label">Key Successes so far <span class="required">*</span></label>
                    <textarea name="key_successes" class="form-control" rows="5" required>{{ old('key_successes', $application?->key_successes ?? '') }}</textarea>
                    @error('key_successes') <div class="error-message">{{ $message }}</div> @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label">Potential Market Size <span class="required">*</span></label>
                    <input type="text" name="potential_market_size" class="form-control" value="{{ old('potential_market_size', $application?->potential_market_size ?? '') }}" required>
                    @error('potential_market_size') <div class="error-message">{{ $message }}</div> @enderror
                </div>
                <div class="mb-4">
                    <label class="form-label">Company Size (No. of Employees) <span class="required">*</span></label>
                    <input type="number" name="company_size_employees" class="form-control" min="0" value="{{ old('company_size_employees', $application?->company_size_employees ?? 0) }}" required>
                    @error('company_size_employees') <div class="error-message">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- SECTION 5: Registration Status -->
            <div class="form-section">
                <h3 class="section-title">5. Registration Status</h3>
                <div class="mb-4">
                    <label class="form-label">Is your start-up registered? <span class="required">*</span></label>
                    <select name="is_registered" id="is_registered" class="form-select" required>
                        <option value="">-- Select --</option>
                        <option value="1" {{ old('is_registered', $application?->is_registered ? 1 : 0) == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('is_registered', $application?->is_registered ? 1 : 0) == 0 ? 'selected' : '' }}>No</option>
                    </select>
                    @error('is_registered') <div class="error-message">{{ $message }}</div> @enderror
                </div>
                <div class="mb-4 conditional" id="registrationDateField">
                    <label class="form-label">Date of Start-up Registration</label>
                    <input type="date" name="registration_date" class="form-control" value="{{ old('registration_date', $application?->registration_date?->format('Y-m-d') ?? '') }}">
                    @error('registration_date') <div class="error-message">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- Consent -->
            <div class="form-section">
                <div class="form-check">
                    <input type="checkbox" name="consent_given" id="consent" class="form-check-input" value="1" {{ old('consent_given', true) ? 'checked' : '' }} required>
                    <label class="form-check-label" for="consent">
                        I consent to receive emails from the organizer regarding this application.
                    </label>
                    @error('consent_given') <div class="error-message">{{ $message }}</div> @enderror
                </div>
            </div>

            <!-- reCAPTCHA -->
            @if(config('constants.RECAPTCHA_ENABLED', false))
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            @error('recaptcha')
                <div class="error-message">{{ $message }}</div>
            @enderror
            @endif

            <!-- Submit -->
            <div class="form-section">
                <button type="submit" class="btn-submit" id="submitBtn">
                    SUBMIT APPLICATION <i class="fas fa-arrow-right ms-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<script>
    // Show validation errors via SweetAlert if any
    @if ($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        const errors = @json($errors->all());
        let errorMessage = '<ul style="text-align: left; margin: 10px 0; padding-left: 20px;">';
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

    // Phone input (match enquiry: phone-input class, utils, country code)
    const phoneInput = document.getElementById('phone_number');
    const phoneCountryCode = document.getElementById('phone_country_code');
    let iti = null;
    if (phoneInput) {
        phoneInput.placeholder = '';
        iti = window.intlTelInput(phoneInput, {
            initialCountry: 'in',
            preferredCountries: ['in', 'us', 'gb'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            separateDialCode: true,
            nationalMode: false,
            autoPlaceholder: 'off'
        });
        phoneInput.addEventListener('countrychange', function() {
            const d = iti.getSelectedCountryData();
            phoneCountryCode.value = d.dialCode;
            phoneInput.placeholder = '';
        });
        phoneCountryCode.value = iti.getSelectedCountryData().dialCode;
    }

    // Conditional registration date
    const isRegistered = document.getElementById('is_registered');
    const regDateField = document.getElementById('registrationDateField');
    if (isRegistered && regDateField) {
        function toggleRegDate() {
            regDateField.style.display = isRegistered.value === '1' ? 'block' : 'none';
        }
        isRegistered.addEventListener('change', toggleRegDate);
        toggleRegDate();
    }

    // Word counter for idea_description (500 words)
    const descTextarea = document.querySelector('textarea[name="idea_description"]');
    const descCounter = document.getElementById('descCounter');
    if (descTextarea && descCounter) {
        descTextarea.addEventListener('input', function() {
            const words = descTextarea.value.trim().split(/\s+/).filter(Boolean).length;
            const remaining = Math.max(0, 500 - words);
            descCounter.textContent = remaining + ' words remaining';
            descCounter.className = 'word-counter';
            if (remaining < 50) descCounter.classList.add('danger');
            else if (remaining < 100) descCounter.classList.add('warning');
        });
    }

    // Inline validator: red when empty (after touch/submit), normal when filled
    const form = document.getElementById('sparxForm');
    const submitBtn = document.getElementById('submitBtn');

    function isFieldValid(el) {
        if (el.type === 'checkbox' || el.type === 'radio') {
            return el.checked;
        }
        if (el.tagName === 'SELECT') {
            return el.value !== '' && el.value !== null;
        }
        if (el.name === 'phone_number' && typeof iti !== 'undefined' && iti) {
            return iti.isValidNumber ? iti.isValidNumber() : el.value.trim().length > 0;
        }
        return el.value.trim() !== '';
    }

    function updateFieldValidity(el) {
        if (!el.hasAttribute('required')) return;
        if (isFieldValid(el)) {
            el.classList.remove('field-invalid');
        } else {
            el.classList.add('field-invalid');
        }
    }

    function attachValidators() {
        if (!form) return;
        const required = form.querySelectorAll('input[required]:not([type="hidden"]), select[required], textarea[required]');
        required.forEach(function(el) {
            el.addEventListener('blur', function() {
                updateFieldValidity(el);
            });
            el.addEventListener('input', function() {
                updateFieldValidity(el);
            });
            el.addEventListener('change', function() {
                updateFieldValidity(el);
            });
        });
    }
    attachValidators();

    // Mark all empty required fields as invalid on page load (match first page: red + icon from the start)
    if (form) {
        setTimeout(function() {
            var required = form.querySelectorAll('input[required]:not([type="hidden"]), select[required], textarea[required]');
            required.forEach(function(el) {
                if (el.type === 'hidden' || el.disabled) return;
                if (el.offsetParent === null) return;
                updateFieldValidity(el);
            });
        }, 300);
    }

    // On submit attempt, mark all empty required as invalid so they turn red
    function markInvalidFields() {
        if (!form) return;
        const required = form.querySelectorAll('input[required]:not([type="hidden"]), select[required], textarea[required]');
        required.forEach(function(el) {
            if (!isFieldValid(el)) {
                el.classList.add('field-invalid');
            } else {
                el.classList.remove('field-invalid');
            }
        });
    }

    function updateProgress() {
        if (!form) return;
        const inputs = form.querySelectorAll('input[required]:not([type="hidden"]), select[required], textarea[required]');
        let filled = 0;
        inputs.forEach(function(el) {
            if (el.type === 'checkbox') {
                if (el.checked) filled++;
            } else if (el.value.trim() !== '') {
                filled++;
            }
        });
        const pct = inputs.length ? (filled / inputs.length) * 100 : 0;
        const fill = document.getElementById('progressFill');
        if (fill) fill.style.width = pct + '%';
    }
    if (form) {
        form.querySelectorAll('input, select, textarea').forEach(function(el) {
            el.addEventListener('input', updateProgress);
            el.addEventListener('change', updateProgress);
        });
        updateProgress();
    }

    // Form submit with reCAPTCHA (match enquiry)
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Mark all empty required fields as invalid (red)
            markInvalidFields();

            // Client-side validation: collect errors
            const required = form.querySelectorAll('input[required]:not([type="hidden"]), select[required], textarea[required]');
            const errors = [];
            required.forEach(function(el) {
                if (!isFieldValid(el)) {
                    const label = form.querySelector('label[for="' + el.id + '"]') || el.closest('.form-section')?.querySelector('.form-label');
                    const name = label ? label.textContent.replace(/\s*\*$/, '').trim() : el.name;
                    errors.push((name || el.name) + ' is required.');
                }
            });
            if (errors.length > 0) {
                let errorHtml = '<ul style="text-align: left; margin: 10px 0; padding-left: 20px;">';
                errors.forEach(function(err) { errorHtml += '<li>' + err + '</li>'; });
                errorHtml += '</ul>';
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorHtml,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#20b2aa',
                    width: '600px'
                });
                return;
            }

            // Update phone country code before submit
            if (iti) {
                phoneCountryCode.value = iti.getSelectedCountryData().dialCode;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';

            function resetSubmitBtn() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'SUBMIT APPLICATION <i class="fas fa-arrow-right ms-2"></i>';
            }

            @if(config('constants.RECAPTCHA_ENABLED', false))
            var siteKey = '{{ config('services.recaptcha.site_key') }}';
            if (!siteKey) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Configuration',
                    text: 'reCAPTCHA is enabled but not configured. Submitting without verification.',
                    confirmButtonColor: '#20b2aa'
                }).then(function() { form.submit(); });
                return;
            }
            if (typeof grecaptcha === 'undefined' || !grecaptcha.enterprise) {
                Swal.fire({
                    icon: 'error',
                    title: 'Security check not loaded',
                    text: 'Please refresh the page and try again. If the problem continues, check your connection.',
                    confirmButtonColor: '#20b2aa'
                });
                resetSubmitBtn();
                return;
            }
            grecaptcha.enterprise.ready(function() {
                grecaptcha.enterprise.execute(siteKey, { action: 'submit' })
                    .then(function(token) {
                        var inp = document.getElementById('g-recaptcha-response');
                        if (inp) {
                            inp.value = token;
                        } else {
                            var hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = 'g-recaptcha-response';
                            hidden.value = token;
                            form.appendChild(hidden);
                        }
                        form.submit();
                    })
                    .catch(function(err) {
                        console.error('reCAPTCHA error:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Security check failed',
                            text: 'reCAPTCHA could not complete. Please try again or refresh the page.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#20b2aa'
                        });
                        resetSubmitBtn();
                    });
            });
            @else
            form.submit();
            @endif
        });
    }
</script>
@endpush
