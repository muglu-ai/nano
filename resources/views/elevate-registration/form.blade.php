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
        <h2><i class="fas fa-file-alt me-2"></i>Felicitation Ceremony for ELEVATE 2025, ELEVATE Unnati 2025 & ELEVATE Minorities 2025 Winners</h2>
    </div>

    <div class="form-body">
        <form action="{{ route('elevate-registration.save-preview') }}" method="POST" id="elevateRegistrationForm">
            @csrf

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

            <!-- Attendees Information Section (shown when Yes is selected) -->
            <div class="form-section" id="attendeesSection" style="display: none;">
                <div class="section-header">
                    <h5>Attendees Information</h5>
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
            
            if (this.value === 'yes') {
                attendeesSection.style.display = 'block';
                justificationSection.classList.remove('show');
                attendanceReason.removeAttribute('required');
                
                // Add first attendee if none exist
                if (attendeeCount === 0) {
                    addAttendeeBlock();
                }
            } else {
                attendeesSection.style.display = 'none';
                justificationSection.classList.add('show');
                attendanceReason.setAttribute('required', 'required');
                
                // Clear attendees
                document.getElementById('attendeesContainer').innerHTML = '';
                attendeeCount = 0;
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
    } else if (attendanceValue === 'no') {
        document.getElementById('justificationSection').classList.add('show');
        document.getElementById('attendance_reason').setAttribute('required', 'required');
    }

    // Add attendee block
    function addAttendeeBlock(index = null, data = null) {
        attendeeCount++;
        const attendeeIndex = index !== null ? index : attendeeCount - 1;
        const isFirst = attendeeCount === 1;
        
        const attendeeBlock = document.createElement('div');
        attendeeBlock.className = 'attendee-block';
        attendeeBlock.id = `attendee-${attendeeIndex}`;
        
        attendeeBlock.innerHTML = `
            <div class="attendee-header">
                <div class="attendee-title">Name of the Attendees ${attendeeCount} ${isFirst ? '<span class="required">*</span>' : ''}</div>
                ${!isFirst ? '<button type="button" class="btn-remove-attendee" onclick="removeAttendee(' + attendeeIndex + ')"><i class="fas fa-times"></i> Remove</button>' : ''}
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
                    <label class="form-label">Job Title</label>
                    <input type="text" class="form-control" name="attendees[${attendeeIndex}][job_title]" 
                           value="${data ? (data.job_title || '') : ''}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email ${isFirst ? '<span class="required">*</span>' : ''}</label>
                    <input type="email" class="form-control" name="attendees[${attendeeIndex}][email]" 
                           value="${data ? (data.email || '') : ''}" ${isFirst ? 'required' : ''}>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Phone Number ${isFirst ? '<span class="required">*</span>' : ''}</label>
                    <input type="tel" class="form-control" name="attendees[${attendeeIndex}][phone_number]" 
                           value="${data ? (data.phone_number || '') : ''}" ${isFirst ? 'required' : ''}>
                </div>
            </div>
        `;
        
        document.getElementById('attendeesContainer').appendChild(attendeeBlock);
    }

    // Remove attendee block
    function removeAttendee(index) {
        const attendeeBlock = document.getElementById(`attendee-${index}`);
        if (attendeeBlock) {
            attendeeBlock.remove();
            attendeeCount--;
        }
    }

    // Add attendee button
    document.getElementById('addAttendeeBtn').addEventListener('click', function() {
        addAttendeeBlock();
    });

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

    // Form submission
    document.getElementById('elevateRegistrationForm').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    });
</script>
@endpush
@endsection
