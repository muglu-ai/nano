@extends('layouts.dashboard')
@section('title', 'Attendee Profile')

@section('content')

    <style>
        .custom-modal-form .modal-content {
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 0 8px;
        }

        .custom-modal-form .modal-header {
            border-bottom: 1px solid #f0f0f0;
            background: #f8f9fa;
        }

        .custom-modal-form .modal-title {
            font-weight: 600;
            color: #333;
        }

        .custom-modal-form .form-label {
            font-weight: 500;
            color: #555;
        }

        .custom-modal-form .form-control {
            border-radius: 6px;
            border: 1px solid #d1d5db;
            box-shadow: none;
            font-size: 1rem;
        }

        .custom-modal-form .form-control:focus {
            border-color: #0d8abc;
            box-shadow: 0 0 0 2px rgba(13, 138, 188, 0.1);
        }

        .custom-modal-form .modal-footer {
            border-top: 1px solid #f0f0f0;
            background: #f8f9fa;
        }

        .custom-modal-form .btn-success {
            background: #22bb33;
            border: none;
        }

        .custom-modal-form .btn-success:hover {
            background: #1da127;
        }

        .custom-modal-form .btn-secondary {
            background: #6c757d;
            border: none;
        }

        .custom-modal-form .btn-secondary:hover {
            background: #565e64;
        }
    </style>
    <div class="container mt-4">
        <h3 class="mb-4">Attendee Profile</h3>
        @if (auth()->user() && auth()->user()->role === 'admin')
            @if ($attendeeType == 'Attendee')
                <button class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#editAttendeeModal">
                    Edit
                </button>
            @endif
        @endif
        <div class="row g-4 align-items-stretch">
            <!-- Profile Card -->
            <div class="col-12 col-md-3">
                <div
                    class="card text-center shadow-sm p-4 h-100 d-flex flex-column align-items-center justify-content-center">
                    <img src="{{ $attendee->profile_picture
                        ? asset($attendee->profile_picture)
                        : ($attendee->profile_pic
                            ? asset('storage/' . ltrim($attendee->profile_pic, '/'))
                            : 'https://ui-avatars.com/api/?name=' .
                                urlencode($attendee->first_name . ' ' . $attendee->last_name) .
                                '&background=0D8ABC&color=fff') }}"
                        class="img-fluid rounded-circle mb-3 mx-auto d-block" style="width: 110px; height: 110px;"
                        alt="Profile">
                    <h5 class="fw-bold mb-1">
                        {{ $attendee->title ? ucfirst(strtolower($attendee->title)) . ' ' : '' }}{{ $attendee->first_name }}
                        {{ $attendee->middle_name }} {{ $attendee->last_name }}
                    </h5>
                    <div class="text-muted">
                        {{ $attendee->designation ?? ($attendee->job_title ?? 'Attendee') }}
                    </div>
                </div>
            </div>
            <!-- Details Card -->
            <div class="col-12 col-md-9">
                <div class="card shadow-sm p-4 h-100">
                    <div class="row g-2">
                        @php
                            // Helper function to output fields cleanly
                            function showField($label, $value)
                            {
                                if (isset($value) && $value !== '') {
                                    echo '<div class="col-12 col-sm-6 mb-2">
                                        <div class="small text-uppercase text-muted fw-semibold">' .
                                        $label .
                                        '</div>
                                        <div class="fw-bold">' .
                                        e($value) .
                                        '</div>
                                      </div>';
                                }
                            }

                            $products = $attendee->products;
                            if (!is_array($products) && $products) {
                                $products = json_decode($products, true);
                            }

                            //business_nature
                            if (is_string($attendee->business_nature || $attendee->buisness_nature)) {
                                $attendee->business_nature = json_decode($attendee->business_nature, true);
                            } elseif (is_string($attendee->buisness_nature)) {
                                $attendee->business_nature = json_decode($attendee->buisness_nature, true);
                            }

                            // purpose
                            if (is_string($attendee->purpose)) {
                                $attendee->purpose = json_decode($attendee->purpose, true);
                            }

                            // ["All"]
                            if (
                                is_array($attendee->event_days) &&
                                count($attendee->event_days) === 1 &&
                                $attendee->event_days[0] === 'All'
                            ) {
                                $attendee->event_days = 'All Days';
                            } elseif (is_string($attendee->event_days)) {
                                $attendee->event_days = json_decode($attendee->event_days, true);
                            }

                        @endphp
                        {!! showField('Registration Date', $attendee->created_at ? $attendee->created_at->format('Y-m-d') : '') !!}

                        {!! showField('Company', $attendee->company ?? $attendee->organisation_name) !!}
                        {!! showField('Email', $attendee->email) !!}
                        {!! showField('Mobile', $attendee->mobile) !!}
                        {!! showField('Address', $attendee->address) !!}
                        {!! showField('City', $attendee->city) !!}
                        {!! showField('State', $attendee->stateRelation->name ?? ($attendee->state->name ?? '')) !!} {!! showField('Country', $attendee->countryRelation->name ?? '') !!}
                        {!! showField('Postal Code', $attendee->postal_code) !!}
                        {!! showField('Unique ID', $attendee->unique_id) !!}
                        {!! showField('Registration Type', $attendee->registration_type) !!}
                        {!! showField(
                            'Event Days',
                            is_array($attendee->event_days) ? implode(', ', $attendee->event_days) : $attendee->event_days,
                        ) !!}
                        {!! showField('Job Category', $attendee->job_category) !!}
                        {!! showField('Job Subcategory', $attendee->job_subcategory) !!}
                        {!! showField('Other Job Category', $attendee->other_job_category) !!}
                        {!! showField('ID Card Type', $attendee->id_card_type) !!}
                        {!! showField('ID Card Number', $attendee->id_card_number) !!}
                        {!! showField('Products', is_array($products) ? implode(', ', $products) : $products ?? 'N/A') !!}
                        {!! showField(
                            'Business Nature',
                            is_array($attendee->business_nature) ? implode(', ', $attendee->business_nature) : $attendee->business_nature,
                        ) !!}
                        {!! showField('Startup', $attendee->startup ? 'Yes' : 'No') !!}
                        {!! showField('Promotion Consent', $attendee->promotion_consent ? 'Yes' : 'No') !!}
                        {!! showField('Inaugural Session', $attendee->inaugural_session ? 'Yes' : 'No') !!}
                        {!! showField('Purpose', is_array($attendee->purpose) ? implode(', ', $attendee->purpose) : $attendee->purpose) !!}
                        {{-- {!! showField('Status', ucfirst($attendee->status)) !!} --}}
                    </div>
                </div>
            </div>
        </div>

        @if (auth()->user() && auth()->user()->role === 'admin')
            <!-- Edit Modal -->
            <div class="modal fade" id="editAttendeeModal" tabindex="-1" aria-labelledby="editAttendeeModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <form method="POST" action="{{ route('attendee.update', $attendee->unique_id) }}"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="modal-content custom-modal-form">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editAttendeeModalLabel">Edit Attendee Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <label for="title" class="form-label">Prefix</label>
                                        <select class="form-select" id="title" name="title">
                                            <option value="">Select Prefix</option>
                                            <option value="Mr." {{ old('title', $attendee->title) == 'Mr.' ? 'selected' : '' }}>Mr.</option>
                                            <option value="Mrs." {{ old('title', $attendee->title) == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                            <option value="Ms." {{ old('title', $attendee->title) == 'Ms.' ? 'selected' : '' }}>Ms.</option>
                                            <option value="Dr." {{ old('title', $attendee->title) == 'Dr.' ? 'selected' : '' }}>Dr.</option>
                                            <option value="Prof." {{ old('title', $attendee->title) == 'Prof.' ? 'selected' : '' }}>Prof.</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $attendee->first_name }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="middle_name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ $attendee->middle_name }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $attendee->last_name }}">
                                    </div>
                                </div>
                                    
                                <div class="mb-3">
                                    <label for="profile_picture" class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control" id="profile_picture" name="profile_picture"
                                        accept="image/*">
                                    @if ($attendee->profile_picture)
                                        <img src="{{ asset($attendee->profile_picture) }}" class="img-thumbnail mt-2"
                                            style="width:60px;height:60px;">
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label for="company" class="form-label">Company</label>
                                    <input type="text" class="form-control" id="company" name="company"
                                        value="{{ $attendee->company }}">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">

                                        <label for="id_card_type" class="form-label">ID Card Type</label>
                                        <select class="form-select" id="id_card_type" name="id_card_type" required>
                                            <option value="">Select ID Card Type</option>
                                            <option value="Aadhaar Card"
                                                {{ $attendee->id_card_type == 'Aadhaar Card' ? 'selected' : '' }}>Aadhaar Card
                                            </option>
                                            <option value="PAN Card"
                                                {{ $attendee->id_card_type == 'PAN Card' ? 'selected' : '' }}>PAN Card</option>
                                            <option value="Driving License"
                                                {{ $attendee->id_card_type == 'Driving License' ? 'selected' : '' }}>Driving
                                                License</option>
                                            <option value="Passport"
                                                {{ $attendee->id_card_type == 'Passport' ? 'selected' : '' }}>Passport</option>
                                            <option value="Voter ID"
                                                {{ $attendee->id_card_type == 'Voter ID' ? 'selected' : '' }}>Voter ID</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="id_card_number" class="form-label">ID Card Number</label>
                                        <input type="text" class="form-control" id="id_card_number" name="id_card_number"
                                            value="{{ $attendee->id_card_number }}">
                                    </div>
                                 </div>
                                <div class="mb-3">
                                    <label class="form-label">Event Days</label>
                                    <div class="custom-dropdown-container">
                                        <div class="custom-dropdown" id="event_days_dropdown_modal">
                                            <div class="dropdown-selected" onclick="toggleCustomDropdown(this)">
                                                <div class="selected-tags" id="event_days_tags_modal"></div>
                                                <span class="selected-text" data-placeholder="Select event days">Select event days</span>
                                                <i class="dropdown-arrow">▼</i>
                                            </div>
                                            <div class="dropdown-options" id="event_days_options_modal">
                                                @php
                                                    $eventOptions = [
                                                        'All' => 'All Days',
                                                        'Day 1' => 'Day 1 - 2nd September',
                                                        'Day 2' => 'Day 2 - 3rd September',
                                                        'Day 3' => 'Day 3 - 4th September',
                                                    ];
                                                    $selectedDays = is_array($attendee->event_days) ? $attendee->event_days : (is_string($attendee->event_days) ? json_decode($attendee->event_days, true) : []);
                                                    if ($attendee->event_days == 'All' || $attendee->event_days == 'All Days') $selectedDays = ['All'];
                                                @endphp
                                                @foreach ($eventOptions as $val => $label)
                                                <label class="dropdown-option">
                                                    <input type="checkbox" name="event_days[]" value="{{ $val }}"
                                                        data-label="{{ $label }}"
                                                        {{ !empty($selectedDays) && in_array($val, $selectedDays) ? 'checked' : '' }}
                                                        onchange="handleEventDaySelectionModal(this)">
                                                    <span>{{ $label }}</span>
                                                </label>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="invalid-feedback" id="event_days_error_modal" style="display: none;">
                                            Please select at least one event day.
                                        </div>
                                    </div>
                                </div>
                                <style>
                                    .custom-dropdown-container { position: relative; width: 100%; }
                                    .custom-dropdown { position: relative; width: 100%; border: 1px solid #ced4da; border-radius: 0.375rem; background-color: #fff; }
                                    .dropdown-selected { display: flex !important; flex-wrap: wrap; align-items: flex-start; justify-content: flex-start !important; padding: 0.5rem 0.75rem; cursor: pointer; min-height: 38px; background-color: #fff; border-radius: 0.375rem; gap: 4px; text-align: left !important; width: 100%; }
                                    .dropdown-arrow { margin-left: auto; }
                                    .dropdown-options { display: none; position: absolute; top: 100%; left: 0; width: 100%; background: #fff; border: 1px solid #ced4da; border-radius: 0.375rem; z-index: 10; max-height: 200px; overflow-y: auto; }
                                    .custom-dropdown.open .dropdown-options { display: block; }
                                    .dropdown-option { display: flex; align-items: center; padding: 0.25rem 0.75rem; cursor: pointer; }
                                    .dropdown-option input[type="checkbox"] { margin-right: 8px; }
                                    .selected-tags { display: flex; flex-wrap: wrap; gap: 4px; }
                                    .selected-tag { background: #e9ecef; border-radius: 12px; padding: 2px 8px; font-size: 0.9em; margin-right: 2px; }
                                </style>
                                <script>
                                    function toggleCustomDropdown(el) {
                                        const dropdown = el.closest('.custom-dropdown');
                                        dropdown.classList.toggle('open');
                                    }
                                    function handleEventDaySelectionModal(checkbox) {
                                        const allDaysCheckbox = document.querySelector('input[name="event_days[]"][value="All"]');
                                        const dayCheckboxes = document.querySelectorAll('input[name="event_days[]"]:not([value="All"])');
                                        if (checkbox.value === 'All') {
                                            if (checkbox.checked) {
                                                dayCheckboxes.forEach(dayCheckbox => {
                                                    dayCheckbox.checked = true;
                                                    dayCheckbox.disabled = true;
                                                    dayCheckbox.parentElement.style.opacity = '0.5';
                                                });
                                            } else {
                                                dayCheckboxes.forEach(dayCheckbox => {
                                                    dayCheckbox.checked = false;
                                                    dayCheckbox.disabled = false;
                                                    dayCheckbox.parentElement.style.opacity = '1';
                                                });
                                            }
                                        } else {
                                            const allDaysChecked = Array.from(dayCheckboxes).every(cb => cb.checked);
                                            if (allDaysChecked) {
                                                allDaysCheckbox.checked = true;
                                                dayCheckboxes.forEach(dayCheckbox => {
                                                    dayCheckbox.disabled = true;
                                                    dayCheckbox.parentElement.style.opacity = '0.5';
                                                });
                                            } else {
                                                allDaysCheckbox.checked = false;
                                                dayCheckboxes.forEach(dayCheckbox => {
                                                    dayCheckbox.disabled = false;
                                                    dayCheckbox.parentElement.style.opacity = '1';
                                                });
                                            }
                                        }
                                        updateCustomDropdownTextModal();
                                    }
                                    function updateCustomDropdownTextModal() {
                                        const checked = document.querySelectorAll('input[name="event_days[]"]:checked');
                                        const tags = document.getElementById('event_days_tags_modal');
                                        const text = document.querySelector('#event_days_dropdown_modal .selected-text');
                                        tags.innerHTML = '';
                                        let values = [];
                                        checked.forEach(cb => {
                                            let label = cb.getAttribute('data-label');
                                            values.push(label);
                                            let tag = document.createElement('span');
                                            tag.className = 'selected-tag';
                                            tag.textContent = label;
                                            tags.appendChild(tag);
                                        });
                                        text.textContent = values.length ? '' : text.getAttribute('data-placeholder');
                                    }
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // Show tags for pre-checked checkboxes on load
                                        updateCustomDropdownTextModal();
                                        // Also update tags if modal is opened (in case values change dynamically)
                                        const modal = document.getElementById('editAttendeeModal');
                                        if (modal) {
                                            modal.addEventListener('shown.bs.modal', function() {
                                                updateCustomDropdownTextModal();
                                            });
                                        }
                                        document.addEventListener('click', function(e) {
                                            const dropdown = document.getElementById('event_days_dropdown_modal');
                                            if (dropdown && !dropdown.contains(e.target)) {
                                                dropdown.classList.remove('open');
                                            }
                                        });
                                    });
                                </script>
                            </div>
                            <div class="modal-footer d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2"
                                    data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
