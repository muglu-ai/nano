@extends('enquiry.layout')

@section('title', 'VISA Clearance Registration')

@push('styles')
<style>
    .section-subtitle {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-bottom: 0.75rem;
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
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Designation <span class="required">*</span></label>
                        <input type="text" name="designation" class="form-control"
                               value="{{ old('designation') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Passport Name <span class="required">*</span></label>
                        <input type="text" name="passport_name" class="form-control"
                               value="{{ old('passport_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Father's / Husband's Name <span class="required">*</span></label>
                        <input type="text" name="father_husband_name" class="form-control"
                               value="{{ old('father_husband_name') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Birth <span class="required">*</span></label>
                        <input type="date" name="dob" class="form-control" value="{{ old('dob') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Place of Birth <span class="required">*</span></label>
                        <input type="text" name="place_of_birth" class="form-control"
                               value="{{ old('place_of_birth') }}" required>
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
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Issue <span class="required">*</span></label>
                        <input type="date" name="passport_issue_date" class="form-control"
                               value="{{ old('passport_issue_date') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Place of Issue <span class="required">*</span></label>
                        <input type="text" name="passport_issue_place" class="form-control"
                               value="{{ old('passport_issue_place') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Date of Expiry <span class="required">*</span></label>
                        <input type="date" name="passport_expiry_date" class="form-control"
                               value="{{ old('passport_expiry_date') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Entry Date in India <span class="required">*</span></label>
                        <input type="date" name="entry_date_india" class="form-control"
                               value="{{ old('entry_date_india') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Exit Date from India <span class="required">*</span></label>
                        <input type="date" name="exit_date_india" class="form-control"
                               value="{{ old('exit_date_india') }}" required>
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
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="required">*</span></label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}" required>
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
                </div>
                <div class="mb-3">
                    <label class="form-label">Address Line 2</label>
                    <input type="text" name="address_line2" class="form-control"
                           value="{{ old('address_line2') }}">
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">City <span class="required">*</span></label>
                        <input type="text" name="city" class="form-control"
                               value="{{ old('city') }}" required>
                        @error('city')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">State <span class="required">*</span></label>
                        <select name="state" id="state" class="form-select" required>
                            <option value="">-- Select State --</option>
                        </select>
                        @error('state')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
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
                        @error('country')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Postal Code <span class="required">*</span></label>
                        <input type="text" name="postal_code" class="form-control"
                               value="{{ old('postal_code') }}" required>
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

    // Simple progress bar update
    const formVisa = document.getElementById('visaClearanceForm');
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


