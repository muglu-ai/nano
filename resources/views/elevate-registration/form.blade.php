@extends('elevate-registration.layout')

@section('title', 'ELEVATE Registration Form')

@push('styles')
<style>
    .attendance-section {
        margin-top: 2rem;
    }
    
    .justification-section {
        display: none;
        margin-top: 1rem;
    }
    
    .justification-section.show {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-file-alt me-2"></i>Register for Felicitation Ceremony for ELEVATE 2025, ELEVATE Unnati 2025 & ELEVATE Minorities 2025 Winners</h2>
    </div>

    <div class="form-body">
        <form action="{{ route('elevate-registration.save-preview') }}" method="POST" id="elevateRegistrationForm">
            @csrf

            <!-- Elevate Application Information Section (Moved to Top) -->
            <div class="form-section">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Elevate Application Call Name <span class="required">*</span></label>
                        <div class="checkbox-group" id="elevateCallNamesGroup">
                            <div class="checkbox-item">
                                <input type="checkbox" 
                                       name="elevate_application_call_names[]" 
                                       id="elevate_2025" 
                                       value="ELEVATE 2025"
                                       {{ in_array('ELEVATE 2025', old('elevate_application_call_names', $formData['elevate_application_call_names'] ?? [])) ? 'checked' : '' }}
                                       class="elevate-call-checkbox">
                                <label for="elevate_2025">ELEVATE 2025</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" 
                                       name="elevate_application_call_names[]" 
                                       id="elevate_unnati_2025" 
                                       value="ELEVATE Unnati 2025"
                                       {{ in_array('ELEVATE Unnati 2025', old('elevate_application_call_names', $formData['elevate_application_call_names'] ?? [])) ? 'checked' : '' }}
                                       class="elevate-call-checkbox">
                                <label for="elevate_unnati_2025">ELEVATE Unnati 2025</label>
                            </div>
                            <div class="checkbox-item">
                                <input type="checkbox" 
                                       name="elevate_application_call_names[]" 
                                       id="elevate_minorities_2025" 
                                       value="ELEVATE MINORITIES 2025"
                                       {{ in_array('ELEVATE MINORITIES 2025', old('elevate_application_call_names', $formData['elevate_application_call_names'] ?? [])) ? 'checked' : '' }}
                                       class="elevate-call-checkbox">
                                <label for="elevate_minorities_2025">ELEVATE MINORITIES 2025</label>
                            </div>
                        </div>
                        <small class="text-muted" id="elevateCallNamesError" style="display: none; color: #dc3545;">Maximum 2 selections allowed</small>
                        @error('elevate_application_call_names')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="elevate_2025_id" class="form-label">ELEVATE 2025 ID (For Ex: EL20250000XXX) <span class="required">*</span></label>
                        <input type="text" 
                               class="form-control @error('elevate_2025_id') is-invalid @enderror" 
                               id="elevate_2025_id" 
                               name="elevate_2025_id" 
                               value="{{ old('elevate_2025_id', $formData['elevate_2025_id'] ?? '') }}" 
                               placeholder="EL20250000XXX"
                               required>
                        @error('elevate_2025_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Company Information Section -->
            <div class="form-section">
                <div class="section-header">
                    <h5>Company Information</h5>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="company_name" class="form-label">Company Name <span class="required">*</span></label>
                        <input type="text" 
                               class="form-control @error('company_name') is-invalid @enderror" 
                               id="company_name" 
                               name="company_name" 
                               value="{{ old('company_name', $formData['company_name'] ?? '') }}" 
                               required>
                        @error('company_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="address" class="form-label">Address <span class="required">*</span></label>
                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                  id="address" 
                                  name="address" 
                                  rows="3" 
                                  required>{{ old('address', $formData['address'] ?? '') }}</textarea>
                        @error('address')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="country" class="form-label">Country <span class="required">*</span></label>
                        <select class="form-select @error('country') is-invalid @enderror" 
                                id="country" 
                                name="country" 
                                required>
                            <option value="">-- Select Country --</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->name }}" 
                                        {{ (old('country', $formData['country'] ?? '') == $country->name) ? 'selected' : '' }}
                                        data-country-id="{{ $country->id }}">
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('country')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="state" class="form-label">State <span class="required">*</span></label>
                        <select class="form-select @error('state') is-invalid @enderror" 
                                id="state" 
                                name="state" 
                                required>
                            <option value="">-- Select State --</option>
                            @if($indiaCountry && $states)
                                @foreach($states as $state)
                                    <option value="{{ $state->name }}" 
                                            {{ old('state') == $state->name ? 'selected' : '' }}
                                            data-state-id="{{ $state->id }}">
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('state')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="city" class="form-label">City <span class="required">*</span></label>
                        <input type="text" 
                               class="form-control @error('city') is-invalid @enderror" 
                               id="city" 
                               name="city" 
                               value="{{ old('city') }}" 
                               required>
                        @error('city')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="postal_code" class="form-label">Postal Code <span class="required">*</span></label>
                        <input type="text" 
                               class="form-control @error('postal_code') is-invalid @enderror" 
                               id="postal_code" 
                               name="postal_code" 
                               value="{{ old('postal_code') }}" 
                               required>
                        @error('postal_code')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Attendance Section -->
            <div class="form-section attendance-section">
                <div class="section-header">
                    <h5>Attendance Confirmation</h5>
                </div>

                <div class="form-label">Are you attending Felicitation Ceremony? <span class="required">*</span></div>
                <div class="radio-group">
                    <div class="radio-item">
                        <input type="radio" 
                               id="attendance_yes" 
                               name="attendance" 
                               value="yes" 
                               {{ (old('attendance', $formData['attendance'] ?? '') == 'yes') ? 'checked' : '' }}
                               required>
                        <label for="attendance_yes">Yes</label>
                    </div>
                    <div class="radio-item">
                        <input type="radio" 
                               id="attendance_no" 
                               name="attendance" 
                               value="no" 
                               {{ (old('attendance', $formData['attendance'] ?? '') == 'no') ? 'checked' : '' }}
                               required>
                        <label for="attendance_no">No</label>
                    </div>
                </div>
                @error('attendance')
                    <div class="error-message">{{ $message }}</div>
                @enderror

                <!-- Justification (shown when No is selected) -->
                <div class="justification-section" id="justificationSection">
                    <label for="attendance_reason" class="form-label">If no, justify the reason <span class="required">*</span></label>
                    <textarea class="form-control @error('attendance_reason') is-invalid @enderror" 
                              id="attendance_reason" 
                              name="attendance_reason" 
                              rows="3">{{ old('attendance_reason', $formData['attendance_reason'] ?? '') }}</textarea>
                    @error('attendance_reason')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Attendees/Contact Information Section -->
            <div class="form-section" id="attendeesSection" style="display: none;">
                <div class="section-header">
                    <h5 id="attendeesSectionTitle">Attendees Information</h5>
                </div>

                <div id="attendeesContainer">
                    <!-- Attendee blocks will be added here dynamically -->
                </div>

                <button type="button" class="btn-add-attendee" id="addAttendeeBtn">
                    <i class="fas fa-plus me-2"></i>Add Another Attendee
                </button>
            </div>

            <!-- Submit Button -->
            <div class="form-section mt-4">
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-eye me-2"></i>Preview Registration
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let attendeeCount = 0;
    const salutations = @json($salutations);

    // Handle Elevate Application Call Names checkboxes (max 2)
    const elevateCheckboxes = document.querySelectorAll('.elevate-call-checkbox');
    const elevateErrorMsg = document.getElementById('elevateCallNamesError');
    const maxSelections = 2;

    elevateCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.elevate-call-checkbox:checked').length;
            
            if (checkedCount > maxSelections) {
                this.checked = false;
                elevateErrorMsg.style.display = 'block';
                setTimeout(() => {
                    elevateErrorMsg.style.display = 'none';
                }, 3000);
            } else {
                elevateErrorMsg.style.display = 'none';
            }
        });
    });

    // Handle attendance radio buttons
    document.querySelectorAll('input[name="attendance"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const attendeesSection = document.getElementById('attendeesSection');
            const justificationSection = document.getElementById('justificationSection');
            const attendanceReason = document.getElementById('attendance_reason');
            
            const attendeesSectionTitle = document.getElementById('attendeesSectionTitle');
            
            if (this.value === 'yes') {
                attendeesSection.style.display = 'block';
                attendeesSectionTitle.textContent = 'Attendees Information';
                justificationSection.classList.remove('show');
                attendanceReason.removeAttribute('required');
                
                // Add first attendee if none exist
                if (attendeeCount === 0) {
                    addAttendeeBlock();
                }
            } else {
                attendeesSection.style.display = 'block';
                attendeesSectionTitle.textContent = 'Contact Information';
                justificationSection.classList.add('show');
                attendanceReason.setAttribute('required', 'required');
                
                // Clear attendees and add one contact
                document.getElementById('attendeesContainer').innerHTML = '';
                attendeeCount = 0;
                addAttendeeBlock();
                
                // Hide "Add Another" button for contact (only one contact allowed)
                updateAddAttendeeButton();
            }
        });
    });

    // Initialize based on old input or session data
    const attendanceValue = @json(old('attendance', $formData['attendance'] ?? ''));
    const attendeesData = @json(old('attendees', $formData['attendees'] ?? []));
    
    if (attendanceValue === 'yes') {
        document.getElementById('attendeesSection').style.display = 'block';
        if (attendeesData && attendeesData.length > 0) {
            attendeesData.forEach((attendee, index) => {
                addAttendeeBlock(index, attendee);
            });
        } else {
            addAttendeeBlock();
        }
        // Update button visibility after loading
        setTimeout(() => {
            updateAddAttendeeButton();
            // Initialize intlTelInput for existing phone fields
            document.querySelectorAll('.attendee-phone-input').forEach(phoneInput => {
                if (!phoneInput.closest('.iti')) {
                    phoneInput.placeholder = '';
                    window.intlTelInput(phoneInput, {
                        initialCountry: 'in',
                        preferredCountries: ['in', 'us', 'gb'],
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                        separateDialCode: true,
                        nationalMode: false,
                        autoPlaceholder: 'off',
                    });
                    setTimeout(() => {
                        phoneInput.placeholder = '';
                    }, 100);
                }
            });
            // Check and display email errors
            checkAndDisplayEmailErrors();
        }, 200);
    } else if (attendanceValue === 'no') {
        document.getElementById('attendeesSection').style.display = 'block';
        document.getElementById('attendeesSectionTitle').textContent = 'Contact Information';
        document.getElementById('justificationSection').classList.add('show');
        document.getElementById('attendance_reason').setAttribute('required', 'required');
        if (attendeesData && attendeesData.length > 0) {
            // Only take first contact if multiple exist (only one contact allowed)
            addAttendeeBlock(0, attendeesData[0]);
            attendeeCount = 1; // Reset count to 1
        } else {
            addAttendeeBlock();
        }
        setTimeout(() => {
            updateAddAttendeeButton(); // Hide "Add Another" button for contact
            // Initialize intlTelInput for existing phone fields
            document.querySelectorAll('.attendee-phone-input').forEach(phoneInput => {
                if (!phoneInput.closest('.iti')) {
                    phoneInput.placeholder = '';
                    const iti = window.intlTelInput(phoneInput, {
                        initialCountry: 'in',
                        preferredCountries: ['in', 'us', 'gb'],
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                        separateDialCode: true,
                        nationalMode: false,
                        autoPlaceholder: 'off',
                    });
                    setTimeout(() => {
                        phoneInput.placeholder = '';
                    }, 100);
                    // Format phone number on blur
                    phoneInput.addEventListener('blur', function() {
                        const countryData = iti.getSelectedCountryData();
                        const dialCode = countryData.dialCode;
                        const number = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
                        const cleanNumber = number.replace(/\D/g, '');
                        if (cleanNumber) {
                            phoneInput.value = dialCode + '-' + cleanNumber;
                        }
                    });
                }
            });
            // Check and display email errors
            checkAndDisplayEmailErrors();
        }, 200);
    }

    // Add attendee block
    function addAttendeeBlock(index = null, data = null) {
        attendeeCount++;
        const attendeeIndex = index !== null ? index : attendeeCount - 1;
        const isFirst = attendeeCount === 1;
        const attendanceValue = document.querySelector('input[name="attendance"]:checked')?.value || '';
        const isContact = attendanceValue === 'no';
        const titleText = isContact ? `Name of the Contact ${attendeeCount}` : `Name of the Attendees ${attendeeCount}`;
        // For contacts (attendance = no), never show remove button (only one contact allowed)
        const showRemoveButton = !isContact && !isFirst;
        
        const attendeeBlock = document.createElement('div');
        attendeeBlock.className = 'attendee-block';
        attendeeBlock.id = `attendee-${attendeeIndex}`;
        
        attendeeBlock.innerHTML = `
            <div class="attendee-header">
                <div class="attendee-title">${titleText} ${isFirst ? '<span class="required">*</span>' : ''}</div>
                ${showRemoveButton ? '<button type="button" class="btn-remove-attendee" onclick="removeAttendee(' + attendeeIndex + ')"><i class="fas fa-times"></i> Remove</button>' : ''}
            </div>
            
            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="form-label">Salutation ${isFirst ? '<span class="required">*</span>' : ''}</label>
                    <select class="form-select" name="attendees[${attendeeIndex}][salutation]" ${isFirst ? 'required' : ''}>
                        <option value="">Select</option>
                        ${salutations.map(s => `<option value="${s}" ${data && data.salutation == s ? 'selected' : ''}>${s}</option>`).join('')}
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">First Name ${isFirst ? '<span class="required">*</span>' : ''}</label>
                    <input type="text" class="form-control" name="attendees[${attendeeIndex}][first_name]" 
                           value="${data ? (data.first_name || '') : ''}" ${isFirst ? 'required' : ''}>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Last Name ${isFirst ? '<span class="required">*</span>' : ''}</label>
                    <input type="text" class="form-control" name="attendees[${attendeeIndex}][last_name]" 
                           value="${data ? (data.last_name || '') : ''}" ${isFirst ? 'required' : ''}>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Designation</label>
                    <input type="text" class="form-control" name="attendees[${attendeeIndex}][job_title]" 
                           value="${data ? (data.job_title || '') : ''}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email ${isFirst ? '<span class="required">*</span>' : ''}</label>
                    <input type="email" class="form-control attendee-email-input" 
                           id="attendee_email_${attendeeIndex}" 
                           name="attendees[${attendeeIndex}][email]" 
                           value="${data ? (data.email || '') : ''}" 
                           ${isFirst ? 'required' : ''}>
                    <div class="error-message" id="email_error_${attendeeIndex}" style="display: none;"></div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Phone Number ${isFirst ? '<span class="required">*</span>' : ''}</label>
                    <input type="tel" 
                           class="form-control attendee-phone-input" 
                           id="attendee_phone_${attendeeIndex}" 
                           name="attendees[${attendeeIndex}][phone_number]" 
                           value="" 
                           ${isFirst ? 'required' : ''}
                           pattern="[0-9-]+"
                           inputmode="numeric">
                    <input type="hidden" 
                           class="attendee-phone-country-code" 
                           id="attendee_phone_country_code_${attendeeIndex}" 
                           name="attendees[${attendeeIndex}][phone_country_code]" 
                           value="">
                    <div class="error-message" id="phone_error_${attendeeIndex}" style="display: none;"></div>
                </div>
            </div>
        `;
        
        document.getElementById('attendeesContainer').appendChild(attendeeBlock);
        
        // Initialize intlTelInput for the new phone field
        const phoneInput = document.getElementById(`attendee_phone_${attendeeIndex}`);
        if (phoneInput && window.intlTelInput) {
            phoneInput.placeholder = '';
            
            // Parse existing phone number if data exists
            let initialValue = '';
            let initialCountry = 'in';
            let initialCountryCode = '91';
            const countryCodeInput = document.getElementById(`attendee_phone_country_code_${attendeeIndex}`);
            
            if (data && data.phone_number) {
                const phoneValue = data.phone_number.toString().trim();
                // Handle format like "91-9878787878"
                if (phoneValue.includes('-')) {
                    const parts = phoneValue.split('-');
                    if (parts.length === 2) {
                        const dialCode = parts[0];
                        const nationalNumber = parts[1];
                        initialCountryCode = dialCode;
                        // Try to get country from dial code
                        if (dialCode === '91') {
                            initialCountry = 'in';
                        } else if (dialCode === '1') {
                            initialCountry = 'us';
                        } else if (dialCode === '44') {
                            initialCountry = 'gb';
                        }
                        initialValue = nationalNumber;
                    } else {
                        // Malformed, try to extract
                        const match = phoneValue.match(/^(\d{1,3})-(.+)$/);
                        if (match) {
                            initialCountryCode = match[1];
                            initialValue = match[2];
                        }
                    }
                } else {
                    // No dash - might be just number or full number with country code
                    if (phoneValue.startsWith('91') && phoneValue.length > 10) {
                        initialCountry = 'in';
                        initialCountryCode = '91';
                        initialValue = phoneValue.substring(2);
                    } else if (phoneValue.length <= 15) {
                        initialValue = phoneValue;
                    }
                }
                
                // Set country code in hidden field
                if (countryCodeInput && initialCountryCode) {
                    countryCodeInput.value = initialCountryCode;
                }
            }
            
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: initialCountry,
                preferredCountries: ['in', 'us', 'gb'],
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                separateDialCode: true,
                nationalMode: false,
                autoPlaceholder: 'off',
            });
            
            // Set the national number if we have initial value
            if (initialValue) {
                setTimeout(() => {
                    const dialCode = iti.getSelectedCountryData().dialCode;
                    iti.setNumber('+' + dialCode + initialValue);
                    // Ensure country code is set
                    if (countryCodeInput) {
                        countryCodeInput.value = dialCode;
                    }
                }, 200);
            }
            
            // Ensure placeholder stays empty
            setTimeout(() => {
                phoneInput.placeholder = '';
            }, 100);
            
            // Restrict input to numbers only (intlTelInput handles this, but add extra protection)
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
            
            // Store country code and national number separately on blur
            phoneInput.addEventListener('blur', function() {
                const countryCodeInput = document.getElementById(`attendee_phone_country_code_${attendeeIndex}`);
                
                if (iti.isValidNumber()) {
                    const countryData = iti.getSelectedCountryData();
                    const dialCode = countryData.dialCode;
                    // Get only the national number (without country code)
                    const nationalNumber = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
                    const cleanNumber = nationalNumber.replace(/\D/g, ''); // Remove all non-digits
                    
                    // Validate length (should be reasonable, e.g., 6-15 digits)
                    if (cleanNumber.length >= 6 && cleanNumber.length <= 15) {
                        // Store country code in hidden field
                        if (countryCodeInput) {
                            countryCodeInput.value = dialCode;
                        }
                        // Store only national number in visible field
                        phoneInput.value = cleanNumber;
                        
                        const errorDiv = document.getElementById(`phone_error_${attendeeIndex}`);
                        if (errorDiv) {
                            errorDiv.style.display = 'none';
                        }
                        phoneInput.classList.remove('is-invalid');
                    } else {
                        const errorDiv = document.getElementById(`phone_error_${attendeeIndex}`);
                        if (errorDiv) {
                            errorDiv.textContent = 'Please enter a valid phone number (6-15 digits)';
                            errorDiv.style.display = 'block';
                        }
                        phoneInput.classList.add('is-invalid');
                    }
                } else if (phoneInput.value.trim() !== '') {
                    const errorDiv = document.getElementById(`phone_error_${attendeeIndex}`);
                    if (errorDiv) {
                        errorDiv.textContent = 'Please enter a valid phone number';
                        errorDiv.style.display = 'block';
                    }
                    phoneInput.classList.add('is-invalid');
                }
            });
            
            // Clear error on input
            phoneInput.addEventListener('input', function() {
                const errorDiv = document.getElementById(`phone_error_${attendeeIndex}`);
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
                this.classList.remove('is-invalid');
            });
        }
        
        // Update "Add Another Attendee" button visibility
        updateAddAttendeeButton();
        
        // Check for email errors and highlight fields
        setTimeout(() => {
            checkAndDisplayEmailErrors();
        }, 100);
    }
    
    // Function to check and display email field errors
    function checkAndDisplayEmailErrors() {
        const emailErrors = @json($errors->get('attendees.*.email'));
        const allErrors = @json($errors->all());
        
        // Parse errors for attendees.{index}.email pattern
        const attendeeEmailErrors = {};
        allErrors.forEach((error, index) => {
            // Check if error key matches attendees.{index}.email pattern
            const errorKey = Object.keys(@json($errors->getMessageBag()->toArray()))[index];
            if (errorKey && errorKey.match(/^attendees\.(\d+)\.email$/)) {
                const match = errorKey.match(/^attendees\.(\d+)\.email$/);
                if (match) {
                    attendeeEmailErrors[match[1]] = error;
                }
            }
        });
        
        // Also check direct error keys
        @php
            $emailErrorMap = [];
            foreach($errors->getMessageBag()->toArray() as $key => $messages) {
                if (preg_match('/^attendees\.(\d+)\.email$/', $key, $matches)) {
                    $emailErrorMap[$matches[1]] = $messages[0] ?? '';
                }
            }
        @endphp
        
        const emailErrorMap = @json($emailErrorMap);
        
        // Apply errors to email fields
        Object.keys(emailErrorMap).forEach(index => {
            const emailInput = document.getElementById(`attendee_email_${index}`);
            const errorDiv = document.getElementById(`email_error_${index}`);
            
            if (emailInput) {
                emailInput.classList.add('is-invalid');
                emailInput.style.borderColor = '#dc3545';
                emailInput.style.borderWidth = '2px';
                
                if (errorDiv) {
                    errorDiv.textContent = emailErrorMap[index];
                    errorDiv.style.display = 'block';
                    errorDiv.style.color = '#dc3545';
                }
            }
        });
        
        // Scroll to first error field
        const firstErrorIndex = Object.keys(emailErrorMap)[0];
        if (firstErrorIndex !== undefined) {
            const firstErrorInput = document.getElementById(`attendee_email_${firstErrorIndex}`);
            if (firstErrorInput) {
                setTimeout(() => {
                    firstErrorInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        }
    }
    
    // Update "Add Another Attendee" button visibility based on attendee count and attendance
    function updateAddAttendeeButton() {
        const addBtn = document.getElementById('addAttendeeBtn');
        if (addBtn) {
            const attendanceValue = document.querySelector('input[name="attendance"]:checked')?.value || '';
            const isContact = attendanceValue === 'no';
            
            // Hide button if: attendance is "no" (only one contact allowed) OR attendee count is 2 or more
            if (isContact || attendeeCount >= 2) {
                addBtn.style.display = 'none';
            } else {
                addBtn.style.display = 'block';
            }
        }
    }

    // Remove attendee block
    function removeAttendee(index) {
        const attendeeBlock = document.getElementById(`attendee-${index}`);
        if (attendeeBlock) {
            attendeeBlock.remove();
            attendeeCount--;
            // Update button visibility
            updateAddAttendeeButton();
        }
    }

    // Add attendee button
    const addAttendeeBtn = document.getElementById('addAttendeeBtn');
    if (addAttendeeBtn) {
        addAttendeeBtn.addEventListener('click', function() {
            const attendanceValue = document.querySelector('input[name="attendance"]:checked')?.value || '';
            const isContact = attendanceValue === 'no';
            
            // Don't allow adding if it's a contact (attendance = no) or if already 2 attendees
            if (!isContact && attendeeCount < 2) {
                addAttendeeBlock();
            }
        });
    }

    // Handle country change to load states
    document.getElementById('country').addEventListener('change', function() {
        const countryId = this.options[this.selectedIndex].dataset.countryId;
        const stateSelect = document.getElementById('state');
        
        if (countryId) {
            fetch(`{{ route('elevate-registration.get-states') }}?country_id=${countryId}`)
                .then(response => response.json())
                .then(data => {
                    stateSelect.innerHTML = '<option value="">-- Select State --</option>';
                    data.states.forEach(state => {
                        const option = document.createElement('option');
                        option.value = state.name;
                        option.textContent = state.name;
                        option.dataset.stateId = state.id;
                        stateSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error loading states:', error);
                });
        } else {
            stateSelect.innerHTML = '<option value="">-- Select State --</option>';
        }
    });

    // Validate Elevate Application Call Name
    function validateElevateCallNames() {
        const checkedCount = document.querySelectorAll('.elevate-call-checkbox:checked').length;
        if (checkedCount === 0) {
            elevateErrorMsg.style.display = 'block';
            elevateErrorMsg.textContent = 'Please select at least one Elevate Application Call Name';
            elevateErrorMsg.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        elevateErrorMsg.style.display = 'none';
        return true;
    }

    // Validate duplicate emails in attendees
    function validateDuplicateEmails() {
        const emailInputs = document.querySelectorAll('input[name*="[email]"]');
        const emails = [];
        const duplicateEmails = [];
        
        emailInputs.forEach(input => {
            const email = input.value.trim().toLowerCase();
            if (email) {
                if (emails.includes(email)) {
                    duplicateEmails.push(email);
                } else {
                    emails.push(email);
                }
            }
        });
        
        if (duplicateEmails.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Duplicate Email',
                text: 'The same email address cannot be used for multiple attendees/contacts.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6A1B9A'
            });
            return false;
        }
        return true;
    }

    // Form submission with reCAPTCHA
    document.getElementById('elevateRegistrationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = document.getElementById('submitBtn');
        
        // Validate Elevate Application Call Name
        if (!validateElevateCallNames()) {
            return;
        }
        
        // Validate duplicate emails
        if (!validateDuplicateEmails()) {
            return;
        }
        
        // Ensure country code and phone number are set separately before submission
        let phoneValidationError = false;
        document.querySelectorAll('.attendee-phone-input').forEach(phoneInput => {
            // Get the intlTelInput instance - try multiple methods
            let itiInstance = null;
            if (window.intlTelInput && typeof window.intlTelInput.getInstance === 'function') {
                itiInstance = window.intlTelInput.getInstance(phoneInput);
            } else if (window.intlTelInputGlobals && typeof window.intlTelInputGlobals.getInstance === 'function') {
                itiInstance = window.intlTelInputGlobals.getInstance(phoneInput);
            } else if (phoneInput.intlTelInput) {
                itiInstance = phoneInput.intlTelInput;
            }
            
            // Find the corresponding country code input
            const phoneInputId = phoneInput.id;
            const attendeeIndex = phoneInputId.replace('attendee_phone_', '');
            const countryCodeInput = document.getElementById(`attendee_phone_country_code_${attendeeIndex}`);
            
            if (itiInstance && typeof itiInstance.getSelectedCountryData === 'function') {
                try {
                    // Check if number is valid
                    if (!itiInstance.isValidNumber() && phoneInput.value.trim() !== '') {
                        phoneInput.classList.add('is-invalid');
                        phoneInput.style.borderColor = '#dc3545';
                        phoneValidationError = true;
                        return;
                    }
                    
                    const countryData = itiInstance.getSelectedCountryData();
                    const dialCode = countryData.dialCode;
                    // Get only the national number (without country code)
                    const nationalNumber = itiInstance.getNumber(intlTelInputUtils.numberFormat.NATIONAL);
                    const cleanNumber = nationalNumber.replace(/\D/g, ''); // Remove all non-digits
                    
                    // Validate length (6-15 digits)
                    if (cleanNumber && dialCode && cleanNumber.length >= 6 && cleanNumber.length <= 15) {
                        // Set country code in hidden field
                        if (countryCodeInput) {
                            countryCodeInput.value = dialCode;
                        }
                        // Set only national number in visible field
                        phoneInput.value = cleanNumber;
                        phoneInput.classList.remove('is-invalid');
                    } else if (cleanNumber && dialCode) {
                        // Invalid length
                        phoneInput.classList.add('is-invalid');
                        phoneInput.style.borderColor = '#dc3545';
                        phoneValidationError = true;
                    }
                } catch (e) {
                    console.error('Error formatting phone number:', e);
                    phoneInput.classList.add('is-invalid');
                    phoneValidationError = true;
                }
            } else if (phoneInput.value.trim() !== '') {
                // If intlTelInput not available, validate format manually
                const phoneValue = phoneInput.value.trim();
                // Check if we have country code
                if (countryCodeInput && countryCodeInput.value) {
                    const countryCode = countryCodeInput.value;
                    const phonePattern = /^\d{6,15}$/;
                    if (!phonePattern.test(phoneValue)) {
                        phoneInput.classList.add('is-invalid');
                        phoneInput.style.borderColor = '#dc3545';
                        phoneValidationError = true;
                    }
                } else {
                    // No country code, try to validate combined format
                    const phonePattern = /^\d{1,3}-\d{6,15}$/;
                    if (!phonePattern.test(phoneValue)) {
                        phoneInput.classList.add('is-invalid');
                        phoneInput.style.borderColor = '#dc3545';
                        phoneValidationError = true;
                    }
                }
            }
        });
        
        if (phoneValidationError) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid Phone Number',
                text: 'Please enter valid phone numbers for all attendees/contacts.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6A1B9A'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-eye me-2"></i>Preview Registration';
            return;
        }
        
        // Basic form validation
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
        
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
                            confirmButtonColor: '#6A1B9A'
                        });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-eye me-2"></i>Preview Registration';
                    });
            });
        } else {
            form.submit();
        }
        @else
        form.submit();
        @endif
    });
</script>
@endpush
@endsection
