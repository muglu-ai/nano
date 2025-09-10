@props(['index', 'data', 'productCategories' => [], 'sectors' => [], 'captchaSvg' => '', 'countries' => []])

@php
$titles = ['mr' => 'Mr.', 'mrs' => 'Mrs.', 'ms' => 'Ms.', 'dr' => 'Dr.', 'prof' => 'Prof.'];
$jobCategories = ['Industry', 'Government', 'Media', 'Academic', 'Others'];
$purposes = [
'Purchase new products and services',
'Source new vendors for an ongoing project',
'Join the buyer-seller program & meet potential suppliers',
'To connect & engage with existing suppliers',
'Stay up to date with the latest innovations',
'Compare and Benchmark technologies / solutions',
];

$idTypes = ['Aadhaar Card', 'PAN Card', 'Driving License', 'Passport', 'Voter ID'];
//var_dump($countries);
//die;
@endphp
<link href="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.4.0/dist/css/coreui.min.css" rel="stylesheet"
    integrity="sha384-TjEsBrREQ8e4UQZBv0t+xyJqXlIR9Z0I2S84WzGcxjOpwG3287e0uXc5MqDVOLPh" crossorigin="anonymous">

<style>
    .ts-dropdown,
    .ts-control,
    .ts-control input {
        color: #000000 !important;
        line-height: 100% !important;
        font-family: inherit !important;
        font-size: inherit !important;
    }

    /* Custom Dropdown Styles */
    .custom-dropdown-container {
        position: relative;
        width: 100%;
    }

    .custom-dropdown {
        position: relative;
        width: 100%;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background-color: #fff;
    }

    .dropdown-selected {
        display: flex !important;
        flex-wrap: wrap;
        align-items: flex-start;
        justify-content: flex-start !important;
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        min-height: 38px;
        background-color: #fff;
        border-radius: 0.375rem;
        gap: 4px;
        text-align: left !important;
        width: 100%;
    }

    .dropdown-selected:hover {
        background-color: #f8f9fa;
    }

    .selected-text {
        flex: 1;
        color: #6c757d;
        font-size: 0.875rem;
        overflow: visible;
        white-space: normal;
        word-wrap: break-word;
        min-width: 100px;
        margin-right: 8px;
        text-align: left !important;
        padding: 0;
        margin-left: 0;
        line-height: 1.4;
    }

    .selected-text.has-selection {
        color: #212529;
        text-align: left !important;
        padding: 0;
        margin-left: 0;
        overflow: visible;
        white-space: normal;
        word-wrap: break-word;
        line-height: 1.4;
    }

    .selected-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        /* flex: 1; */
    }

    .selected-tag {
        background-color: #0d6efd;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        gap: 4px;
        max-width: 150px;
    }

    .selected-tag span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .selected-tag .remove-tag {
        cursor: pointer;
        font-weight: bold;
        font-size: 0.875rem;
        line-height: 1;
        padding: 0 2px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.3);
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .selected-tag .remove-tag:hover {
        background-color: rgba(255, 255, 255, 0.5);
    }

    .dropdown-arrow {
        margin-left: 8px;
        color: #6c757d;
        transition: transform 0.2s ease;
        font-size: 0.75rem;
    }

    .custom-dropdown.open .dropdown-arrow {
        transform: rotate(180deg);
    }

    .dropdown-options {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: #fff;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 0.375rem 0.375rem;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .custom-dropdown.open .dropdown-options {
        display: block;
    }

    .dropdown-option {
        display: flex;
        align-items: center;
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid #f8f9fa;
        margin: 0;
        font-weight: normal;
    }

    .dropdown-option:last-child {
        border-bottom: none;
    }

    .dropdown-option:hover {
        background-color: #f8f9fa;
    }

    .dropdown-option input[type="checkbox"] {
        margin-right: 8px;
        cursor: pointer;
    }

    .dropdown-option span {
        flex: 1;
        cursor: pointer;
        font-size: 0.875rem;
    }

    /* Focus styles */
    .custom-dropdown:focus-within {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Invalid state styles for mandatory Event Days */
    .custom-dropdown.invalid {
        border-color: #dc3545;
    }

    .custom-dropdown.invalid .dropdown-selected {
        border-color: #dc3545;
    }

    .custom-dropdown.invalid .selected-text {
        color: #dc3545;
    }

    /* Show validation error styling */
    .invalid-feedback {
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
        display: none;
    }

    .invalid-feedback[style*="block"] {
        display: block !important;
    }

    /* Disabled state */
    .custom-dropdown.disabled {
        background-color: #f8f9fa;
        opacity: 0.6;
        cursor: not-allowed;
    }

    .custom-dropdown.disabled .dropdown-selected {
        cursor: not-allowed;
        background-color: #f8f9fa;
    }
</style>

<div class="mb-4">
    <ul class="nav nav-pills justify-content-center">
        <li class="nav-item">
            <button class="nav-link active" id="step1-tab-{{ $index }}" type="button">Step 1: Basic
                Details</button>
        </li>
        <li class="nav-item">
            <button class="nav-link disabled" id="step2-tab-{{ $index }}" type="button" tabindex="-1"
                aria-disabled="true">
                Step 2: Participation Details
            </button>
        </li>
    </ul>
</div>

<div class="card mb-4">
    <div class="card-body">
        {{-- Step 1: Personal Details --}}
        <div class="attendee-step attendee-step-1" id="attendee-step-1-{{ $index }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label>Prefix <span class="text-danger">*</span></label>
                    <select name="attendees[{{ $index }}][title]" class="form-control" required>
                        <option value="" disabled {{ empty($data['title']) ? 'selected' : '' }}>--- Select ---
                        </option>
                        @foreach ($titles as $val => $label)
                        <option value="{{ $val }}" {{ ($data['title'] ?? '') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>First Name <span class="text-danger">*</span></label>
                    <input type="text" name="attendees[{{ $index }}][first_name]" class="form-control"
                        required value="{{ $data['first_name'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label>Middle Name</label>
                    <input type="text" name="attendees[{{ $index }}][middle_name]" class="form-control"
                        value="{{ $data['middle_name'] ?? '' }}">
                </div>
                <div class="col-md-3">
                    <label>Last Name <span class="text-danger">*</span></label>
                    <input type="text" name="attendees[{{ $index }}][last_name]" class="form-control"
                        required value="{{ $data['last_name'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label>Designation <span class="text-danger">*</span></label>
                    <input type="text" name="attendees[{{ $index }}][designation]" class="form-control"
                        required value="{{ $data['designation'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label>Organization <span class="text-danger">*</span></label>
                    <input type="text" name="attendees[{{ $index }}][company]" class="form-control" required
                        value="{{ $data['company'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label>Address <span class="text-danger">*</span></label>
                    <input type="text" name="attendees[{{ $index }}][address]" class="form-control" required
                        value="{{ $data['address'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label>Country <span class="text-danger">*</span></label>
                    <select class="form-select country-dropdown" name="attendees[{{ $index }}][country]"
                        data-index="{{ $index }}" required>
                        <option value="">--- Select ---</option>
                        @foreach ($countries as $country)
                        @if (!in_array($country->id, [251, 354, 416, 457, 460]))
                        <option value="{{ $country->id }}"
                            {{ isset($data['country']) && $data['country'] == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                        @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>State <span class="text-danger">*</span></label>
                    <select class="form-select state-dropdown" name="attendees[{{ $index }}][state]"
                        data-index="{{ $index }}" required>
                        <option value="">{{ $data['state'] ?? '--- Select ---' }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>City <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="attendees[{{ $index }}][city]" required
                        value="{{ $data['city'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label>Postal/Pin Code <span class="text-danger">*</span></label>
                    <input type="text" name="attendees[{{ $index }}][postal_code]" class="form-control"
                        required value="{{ $data['postal_code'] ?? '' }}">
                </div>
                <div class="col-md-4">
                    <label>Mobile Number <span class="text-danger">*</span></label>
                    <input type="tel" id="phone" name="attendees[{{ $index }}][mobile]" placeholder=""
                        class="form-control phone-input" required value="{{ $data['mobile'] ?? '' }}">
                    <small class="form-text text-muted">Preferably WhatsApp number to receive badge</small>
                </div>
                <div class="col-md-4">
                    <label>Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="email" name="attendees[{{ $index }}][email]" class="form-control"
                            required value="{{ $data['email'] ?? '' }}">
                        <button type="button" class="btn btn-outline-primary"
                            onclick="sendOtp(this, {{ $index }})">Verify</button>
                        <span id="verification_{{ $index }}" class="input-group-text"
                            style="display:none;"></span>
                        <small class="form-text text-muted">Click the Verify button to confirm your email
                            address.</small>
                    </div>
                </div>

            </div>
            <div class="mt-4 text-end">
                <button type="button" class="btn btn-primary attendee-next-btn"
                    data-index="{{ $index }}">Next</button>
            </div>
        </div>

        {{-- Step 2: Event & Other Details --}}
        <div class="attendee-step attendee-step-2" id="attendee-step-2-{{ $index }}" style="display:none;">
            <div class="row g-3">
                <div class="col-md-6">
                    <label>Registration Type <span class="important">*</span></label>
                    <select name="attendees[{{ $index }}][registration_type]" class="form-select" required>
                        <option value="">--- Select --- </option>
                        <option value="In-Person"
                            {{ ($data['registration_type'] ?? '') === 'In-Person' ? 'selected' : '' }}>In-Person
                        </option>
                        <option value="Online"
                            {{ ($data['registration_type'] ?? '') === 'Online' ? 'selected' : '' }}>Online</option>

                    </select>
                </div>

                {{-- Event Days --}}
                @php
                $eventOptions = [
                'All' => 'All Days',
                'Day 1' => 'Day 1 - 2nd September',
                'Day 2' => 'Day 2 - 3rd September',
                'Day 3' => 'Day 3 - 4th September',
                ];
                @endphp
                <div class="col-md-6 mt-3">
                    <label>Event Days <span class="important">*</span></label>
                    <div class="custom-dropdown-container">
                        <div class="custom-dropdown" id="event_days_dropdown_{{ $index }}">
                            <div class="dropdown-selected" onclick="toggleCustomDropdown(this)">
                                <div class="selected-tags" id="event_days_tags_{{ $index }}"></div>
                                <span class="selected-text" data-placeholder="Select event days">Select event days</span>
                                <i class="dropdown-arrow">▼</i>
                            </div>
                            <div class="dropdown-options" id="event_days_options_{{ $index }}">
                                @foreach ($eventOptions as $val => $label)
                                <label class="dropdown-option">
                                    <input type="checkbox" name="attendees[{{ $index }}][event_days][]" value="{{ $val }}"
                                        data-label="{{ $label }}"
                                        {{ !empty($data['event_days']) && in_array($val, $data['event_days']) ? 'checked' : '' }}
                                        onchange="handleEventDaySelection(this, {{ $index }})">
                                    <span>{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="invalid-feedback" id="event_days_error_{{ $index }}" style="display: none;">

                            <script>
                                function handleEventDaySelection(checkbox, index) {
                                    const allDaysCheckbox = document.querySelector(`input[name="attendees[${index}][event_days][]"][value="All"]`);
                                    const dayCheckboxes = document.querySelectorAll(`input[name="attendees[${index}][event_days][]"]:not([value="All"])`);

                                    if (checkbox.value === 'All') {
                                        // If "All" is selected
                                        if (checkbox.checked) {
                                            // Check and disable all other days
                                            dayCheckboxes.forEach(dayCheckbox => {
                                                dayCheckbox.checked = true;
                                                dayCheckbox.disabled = true;
                                                dayCheckbox.parentElement.style.opacity = '0.5';
                                            });
                                        } else {
                                            // Enable and uncheck all other days
                                            dayCheckboxes.forEach(dayCheckbox => {
                                                dayCheckbox.checked = false;
                                                dayCheckbox.disabled = false;
                                                dayCheckbox.parentElement.style.opacity = '1';
                                            });
                                        }
                                    } else {
                                        // If individual day is selected/unselected
                                        const allDaysChecked = Array.from(dayCheckboxes).every(cb => cb.checked);

                                        if (allDaysChecked) {
                                            // If all individual days are selected, check "All" too
                                            allDaysCheckbox.checked = true;
                                            dayCheckboxes.forEach(dayCheckbox => {
                                                dayCheckbox.disabled = true;
                                                dayCheckbox.parentElement.style.opacity = '0.5';
                                            });
                                        } else {
                                            // If not all days are selected, uncheck "All"
                                            allDaysCheckbox.checked = false;
                                            dayCheckboxes.forEach(dayCheckbox => {
                                                dayCheckbox.disabled = false;
                                                dayCheckbox.parentElement.style.opacity = '1';
                                            });
                                        }
                                    }

                                    // Update the dropdown text
                                    updateCustomDropdownText(checkbox);
                                }
                            </script>
                            Please select at least one event day.
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.querySelector('form');
                        if (form) {
                            form.addEventListener('submit', function(e) {
                                e.preventDefault(); // Prevent default submit initially
                                const index = {
                                    {
                                        $index
                                    }
                                };
                                const catSelect = document.querySelector('.category-select[data-index="' + index + '"]');
                                const selected = catSelect ? catSelect.value : '';

                                let isValid = true;
                                let errorMessages = [];

                                // Check event days
                                const eventDayCheckboxes = document.querySelectorAll(`input[name="attendees[${index}][event_days][]"]`);
                                const isEventDayChecked = Array.from(eventDayCheckboxes).some(cb => cb.checked);
                                const eventDaysDropdown = document.getElementById(`event_days_dropdown_${index}`);

                                if (!isEventDayChecked) {
                                    isValid = false;
                                    errorMessages.push('Event Days');
                                    if (eventDaysDropdown) {
                                        eventDaysDropdown.querySelector('.dropdown-selected').style.border = '2px solid red';
                                    }
                                } else if (eventDaysDropdown) {
                                    eventDaysDropdown.querySelector('.dropdown-selected').style.border = '';
                                }

                                // Additional validations for Industry category
                                if (selected === 'Industry') {
                                    // Check Nature of Business
                                    const businessNatureChecks = document.querySelectorAll(`input[name="attendees[${index}][business_nature][]"]:checked`);
                                    const businessNatureDropdown = document.getElementById(`business_nature_dropdown_${index}`);

                                    // Check Purpose of Visit
                                    const purposeChecks = document.querySelectorAll(`input[name="attendees[${index}][purpose][]"]:checked`);
                                    const purposeDropdown = document.getElementById(`purpose_dropdown_${index}`);

                                    // Check Product Categories
                                    const productChecks = document.querySelectorAll(`input[name="attendees[${index}][products][]"]:checked`);
                                    const productDropdown = document.getElementById(`product_categories_dropdown_${index}`);

                                    // Check startup radio
                                    const startupRadios = document.querySelectorAll(`input[name="attendees[${index}][startup]"]:checked`);
                                    const startupContainer = document.querySelector('.startup-radio').closest('.col-md-12');

                                    if (businessNatureChecks.length === 0) {
                                        isValid = false;
                                        errorMessages.push('Nature of Business');
                                        if (businessNatureDropdown) {
                                            businessNatureDropdown.querySelector('.dropdown-selected').style.border = '2px solid red';
                                        }
                                    }

                                    if (purposeChecks.length === 0) {
                                        isValid = false;
                                        errorMessages.push('Purpose of Visit');
                                        if (purposeDropdown) {
                                            purposeDropdown.querySelector('.dropdown-selected').style.border = '2px solid red';
                                        }
                                    }

                                    if (productChecks.length === 0) {
                                        isValid = false;
                                        errorMessages.push('Product Categories');
                                        if (productDropdown) {
                                            productDropdown.querySelector('.dropdown-selected').style.border = '2px solid red';
                                        }
                                    }

                                    if (startupRadios.length === 0) {
                                        isValid = false;
                                        errorMessages.push('Startup Status');
                                        if (startupContainer) {
                                            startupContainer.style.border = '2px solid red';
                                            startupContainer.style.padding = '10px';
                                            startupContainer.style.borderRadius = '4px';
                                        }
                                    }
                                }

                                if (!isValid) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Required Fields',
                                        text: 'Please complete the following required fields: ' + errorMessages.join(', ')
                                    });
                                    return false;
                                }

                                // If all validations pass, submit the form
                                ensureCountryCodeInPhoneNumbers();
                                form.submit();
                            });

                            // Add change listeners to remove red borders when selections are made
                            function removeRedBorderOnChange(selector, dropdownId) {
                                document.querySelectorAll(selector).forEach(element => {
                                    element.addEventListener('change', function() {
                                        const dropdown = document.getElementById(dropdownId);
                                        if (dropdown) {
                                            dropdown.querySelector('.dropdown-selected').style.border = '';
                                        }
                                        if (this.closest('.col-md-12')) {
                                            this.closest('.col-md-12').style.border = '';
                                            this.closest('.col-md-12').style.padding = '';
                                        }
                                    });
                                });
                            }

                            const index = {
                                {
                                    $index
                                }
                            };
                            removeRedBorderOnChange(`input[name="attendees[${index}][event_days][]"]`, `event_days_dropdown_${index}`);
                            removeRedBorderOnChange(`input[name="attendees[${index}][business_nature][]"]`, `business_nature_dropdown_${index}`);
                            removeRedBorderOnChange(`input[name="attendees[${index}][purpose][]"]`, `purpose_dropdown_${index}`);
                            removeRedBorderOnChange(`input[name="attendees[${index}][products][]"]`, `product_categories_dropdown_${index}`);
                            removeRedBorderOnChange(`input[name="attendees[${index}][startup]"]`, null);
                        }
                    });
                </script>

                <div class="col-md-6">
                    <label>Category <span class="text-danger">*</span></label>
                    <select class="form-select category-select" name="attendees[{{ $index }}][job_category]"
                        data-index="{{ $index }}" required>
                        <option value="">--- Select ---</option>
                        @foreach ($jobCategories as $category)
                        <option value="{{ $category }}"
                            {{ ($data['job_category'] ?? '') === $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <script>
                    //add are you startup  we have to hide and make it as non-mandatory in case of category as Industry
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        function updateMandatoryFields(idx) {
                            const catSelect = document.querySelector('.category-select[data-index="' + idx + '"]');
                            const selected = catSelect ? catSelect.value : '';

                            // Nature of Business
                            const businessSelect = document.querySelector(`[name="attendees[${idx}][business_nature]"]`);
                            const startupRadios = document.querySelectorAll(`input[name="attendees[${idx}][startup]"]`);
                            //hide startupRadios in case of Industry
                            if (startupRadios.length) {
                                if (selected != 'Industry') {
                                    startupRadios.forEach(radio => {
                                        radio.closest('.form-check').style.display = 'none';
                                        radio.required = false;
                                    });
                                    // Also hide the label and parent container if needed
                                    const startupContainer = startupRadios[0].closest('.col-md-12');
                                    if (startupContainer) {
                                        startupContainer.style.display = 'none';
                                    }
                                } else {
                                    startupRadios.forEach(radio => {
                                        radio.closest('.form-check').style.display = '';
                                        radio.required = true;
                                    });
                                    const startupContainer = startupRadios[0].closest('.col-md-12');
                                    if (startupContainer) {
                                        startupContainer.style.display = '';
                                    }
                                }
                            }


                            if (businessSelect) {
                                businessSelect.required = (selected === 'Exhibitor' || selected === 'Industry');

                            }
                            // Add or remove required indicator in label
                            [{
                                    selector: '#purpose_0_visit',
                                    label: 'The purpose of your visits:'
                                },
                                {
                                    selector: '[name="attendees[0][products][]"]',
                                    label: 'Product Categories of your interest:'
                                },
                                {
                                    selector: '[name="attendees[' + idx + '][business_nature]"]',
                                    label: 'Nature of your Business:'
                                }
                                //add are you startup  we have to hide and make it as non-mandatory in case of Industry


                            ].forEach(item => {
                                const label = Array.from(document.querySelectorAll(
                                        '.col-md-12 label, .col-md-12.mt-3 label, .col-md-12.d-flex label'))
                                    .find(l => l.textContent.trim().startsWith(item.label));
                                if (label) {
                                    if (selected === 'Exhibitor' || selected === 'Industry') {
                                        if (!label.innerHTML.includes('<span class="important">*</span>')) {
                                            label.innerHTML = label.innerHTML + ' <span class="important">*</span>';
                                        }
                                    } else {
                                        label.innerHTML = label.innerHTML.replace(/<span class="important">\*<\/span>/g,
                                            '');
                                    }
                                }
                            });
                        }
                        document.querySelectorAll('.category-select').forEach(function(select) {
                            const idx = select.getAttribute('data-index');
                            select.addEventListener('change', function() {
                                updateMandatoryFields(idx);
                                // Reset borders when category changes
                                const dropdowns = [
                                    document.getElementById(`business_nature_dropdown_${idx}`),
                                    document.getElementById(`purpose_dropdown_${idx}`),
                                    document.getElementById(`product_categories_dropdown_${idx}`)
                                ];
                                dropdowns.forEach(dropdown => {
                                    if (dropdown) {
                                        dropdown.querySelector('.dropdown-selected').style.border = '';
                                    }
                                });
                                // Add listeners for the new category
                                addCheckboxListeners(idx);
                            });
                            // Initial state
                            updateMandatoryFields(idx);
                            addCheckboxListeners(idx);
                        });
                    });
                </script>

                <div class="col-md-6">
                    <label>Subcategory <span class="text-danger">*</span></label>
                    <select class="form-select subcategory-select"
                        name="attendees[{{ $index }}][job_subcategory]" data-index="{{ $index }}"
                        data-selected="{{ $data['job_subcategory'] ?? '' }}" required>
                        {{-- <option value="">--- Select ---</option> --}}
                    </select>
                </div>
                <div class="col-md-12 others-category-input" id="others-category-input-{{ $index }}"
                    style="display: {{ ($data['job_category'] ?? '') === 'Others' || ($data['job_subcategory'] ?? '') === 'Others'
                        ? 'block'
                        : 'none' }};">
                    <label>
                        Please specify your Category/Subcategory <span class="important">*</span>
                    </label>
                    <input type="text" class="form-control"
                        name="attendees[{{ $index }}][other_job_category]"
                        value="{{ $data['other_job_category'] ?? '' }}"
                        placeholder="Enter your category/subcategory">
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        function toggleOthersInput(idx) {
                            const catSelect = document.querySelector('.category-select[data-index="' + idx + '"]');
                            const subcatSelect = document.querySelector('.subcategory-select[data-index="' + idx + '"]');
                            const othersInput = document.getElementById('others-category-input-' + idx);
                            if (catSelect && subcatSelect && othersInput) {
                                if (catSelect.value === 'Others' || subcatSelect.value === 'Others') {
                                    othersInput.style.display = 'block';
                                    othersInput.querySelector('input').required = true;
                                } else {
                                    othersInput.style.display = 'none';
                                    othersInput.querySelector('input').required = false;
                                }
                            }
                        }
                        document.querySelectorAll('.category-select, .subcategory-select').forEach(function(select) {
                            select.addEventListener('change', function() {
                                const idx = select.getAttribute('data-index');
                                toggleOthersInput(idx);
                            });
                        });
                        // Initial state
                        document.querySelectorAll('.category-select').forEach(function(select) {
                            const idx = select.getAttribute('data-index');
                            toggleOthersInput(idx);
                        });
                    });
                </script>
                {{-- Are you startup? --}}
                <div class="col-md-12 mt-3">
                    <label>Are you a Startup? <span class="important">*</span></label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input startup-radio" type="radio"
                            name="attendees[{{ $index }}][startup]" id="startup_yes_{{ $index }}"
                            value="1" {{ isset($data['startup']) && $data['startup'] == 1 ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="startup_yes_{{ $index }}">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input startup-radio" type="radio"
                            name="attendees[{{ $index }}][startup]" id="startup_no_{{ $index }}"
                            value="0" {{ isset($data['startup']) && $data['startup'] == 0 ? 'checked' : '' }}
                            required>
                        <label class="form-check-label" for="startup_no_{{ $index }}">No</label>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Ensure at least one radio is selected before submit
                        const form = document.querySelector('form');
                        const radios = document.querySelectorAll('input[name="attendees[{{ $index }}][startup]"]');
                        if (form && radios.length) {
                            form.addEventListener('submit', function(e) {
                                //get the value of the category whether it is Industry or not 
                                const selectedCategory = document.querySelector('.category-select[data-index="{{ $index }}"]').value;
                                // If category is Industry, skip validation
                                if (selectedCategory != 'Industry') {
                                    return;
                                }
                                const checked = Array.from(radios).some(r => r.checked);
                                if (!checked) {
                                    e.preventDefault();
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Required Field',
                                        text: 'Please select Yes or No for "Are you a Startup?"'
                                    });
                                }
                            });
                        }
                    });
                </script>


                {{-- Nature of Business --}}
                <div class="col-md-12 mt-3">
                    <label>Nature of your Business: <span class="important">*</span></label>
                    <div class="custom-dropdown-container">
                        <div class="custom-dropdown" id="business_nature_dropdown_{{ $index }}">
                            <div class="dropdown-selected" onclick="toggleCustomDropdown(this)">
                                <div class="selected-tags" id="business_nature_tags_{{ $index }}"></div>
                                <span class="selected-text" data-placeholder="Select business nature...">Select business nature...</span>
                                <i class="dropdown-arrow">▼</i>
                            </div>
                            <div class="dropdown-options" id="business_nature_options_{{ $index }}">
                                @foreach ($sectors as $i => $sector)
                                @if (!empty($sector['name']))
                                <label class="dropdown-option">
                                    <input type="checkbox" name="attendees[{{ $index }}][business_nature][]"
                                        value="{{ $sector['name'] }}"
                                        data-label="{{ $sector['name'] }}"
                                        {{ !empty($data['business_nature']) && in_array($sector['name'], (array) $data['business_nature']) ? 'checked' : '' }}
                                        onchange="updateCustomDropdownText(this)">
                                    <span>{{ $sector['name'] }}</span>
                                </label>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Function to remove red border
                        function removeRedBorder(element) {
                            if (element) {
                                element.querySelector('.dropdown-selected').style.border = '';
                            }
                        }

                        // Function to add red border
                        function addRedBorder(element) {
                            if (element) {
                                element.querySelector('.dropdown-selected').style.border = '2px solid red';
                            }
                        }

                        // Add change event listeners to all checkboxes
                        function addCheckboxListeners(idx) {
                            const catSelect = document.querySelector('.category-select[data-index="' + idx + '"]');

                            // Only add listeners if category is Industry or Exhibitor
                            if (catSelect && (catSelect.value === 'Industry' || catSelect.value === 'Exhibitor')) {
                                // Business Nature
                                document.querySelectorAll(`input[name="attendees[${idx}][business_nature][]"]`).forEach(checkbox => {
                                    checkbox.addEventListener('change', function() {
                                        const hasChecked = document.querySelectorAll(`input[name="attendees[${idx}][business_nature][]"]:checked`).length > 0;
                                        const dropdown = document.getElementById(`business_nature_dropdown_${idx}`);
                                        if (hasChecked) {
                                            removeRedBorder(dropdown);
                                        }
                                    });
                                });

                                // Purpose
                                document.querySelectorAll(`input[name="attendees[${idx}][purpose][]"]`).forEach(checkbox => {
                                    checkbox.addEventListener('change', function() {
                                        const hasChecked = document.querySelectorAll(`input[name="attendees[${idx}][purpose][]"]:checked`).length > 0;
                                        const dropdown = document.getElementById(`purpose_dropdown_${idx}`);
                                        if (hasChecked) {
                                            removeRedBorder(dropdown);
                                        }
                                    });
                                });

                                // Products
                                document.querySelectorAll(`input[name="attendees[${idx}][products][]"]`).forEach(checkbox => {
                                    checkbox.addEventListener('change', function() {
                                        const hasChecked = document.querySelectorAll(`input[name="attendees[${idx}][products][]"]:checked`).length > 0;
                                        const dropdown = document.getElementById(`product_categories_dropdown_${idx}`);
                                        if (hasChecked) {
                                            removeRedBorder(dropdown);
                                        }
                                    });
                                });
                            }
                        }

                        function validateRequiredFields(form, idx) {
                            const catSelect = document.querySelector('.category-select[data-index="' + idx + '"]');
                            const selected = catSelect ? catSelect.value : '';

                            if (selected === 'Industry') {
                                // Check Nature of Business
                                const businessNatureChecks = document.querySelectorAll(`input[name="attendees[${idx}][business_nature][]"]:checked`);
                                const businessNatureDropdown = document.getElementById(`business_nature_dropdown_${idx}`);

                                // Check Purpose of Visit
                                const purposeChecks = document.querySelectorAll(`input[name="attendees[${idx}][purpose][]"]:checked`);
                                const purposeDropdown = document.getElementById(`purpose_dropdown_${idx}`);

                                // Check Product Categories
                                const productChecks = document.querySelectorAll(`input[name="attendees[${idx}][products][]"]:checked`);
                                const productDropdown = document.getElementById(`product_categories_dropdown_${idx}`);

                                let errorMessage = [];

                                if (businessNatureChecks.length === 0) {
                                    errorMessage.push('Nature of Business');
                                    businessNatureDropdown.querySelector('.dropdown-selected').style.border = '1px solid red';
                                } else {
                                    businessNatureDropdown.querySelector('.dropdown-selected').style.border = '';
                                }

                                if (purposeChecks.length === 0) {
                                    errorMessage.push('Purpose of Visit');
                                    purposeDropdown.querySelector('.dropdown-selected').style.border = '1px solid red';
                                } else {
                                    purposeDropdown.querySelector('.dropdown-selected').style.border = '';
                                }

                                if (productChecks.length === 0) {
                                    errorMessage.push('Product Categories');
                                    productDropdown.querySelector('.dropdown-selected').style.border = '1px solid red';
                                } else {
                                    productDropdown.querySelector('.dropdown-selected').style.border = '';
                                }

                                if (errorMessage.length > 0) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Required Fields',
                                        text: 'Please select at least one option for: ' + errorMessage.join(', ')
                                    });
                                    return false;
                                }
                            }
                            return true;
                        }

                        function validateBusinessNatureRequired(idx, selected) {
                            const select = document.getElementById(`business_nature_${idx}`);
                            const form = select ? select.closest('form') : null;

                            // Only validate if Exhibitor or Industry is selected
                            if (selected === 'Exhibitor' || selected === 'Industry') {
                                if (form) {
                                    // Remove previous handler if exists
                                    if (form._businessNatureHandlerFn) {
                                        form.removeEventListener('submit', form._businessNatureHandlerFn);
                                    }
                                    const handler = function(e) {
                                        const selectedOptions = Array.from(select.selectedOptions);
                                        if (!selectedOptions.length) {
                                            e.preventDefault();
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'Required Field',
                                                text: 'Please select at least one option for Nature of your Business.'
                                            });
                                        }
                                    };
                                    form._businessNatureHandlerFn = handler;
                                    form.addEventListener('submit', handler);
                                }
                            } else {
                                // Remove handler if not required
                                if (form && form._businessNatureHandlerFn) {
                                    form.removeEventListener('submit', form._businessNatureHandlerFn);
                                    form._businessNatureHandlerFn = null;
                                }
                            }
                        }

                        // Get selected job category for this index
                        function getSelectedCategory(idx) {
                            const catSelect = document.querySelector('.category-select[data-index="' + idx + '"]');
                            return catSelect ? catSelect.value : '';
                        }

                        // CoreUI multi-select is initialized automatically by the coreui.js bundle
                        // based on the form-multi-select class

                        // Initial call
                        const idx = {
                            {
                                $index
                            }
                        };
                        let selected = getSelectedCategory(idx);
                        //validateBusinessNatureRequired(idx, selected);

                        // Listen for category changes
                        document.querySelectorAll('.category-select[data-index="' + idx + '"]').forEach(function(select) {
                            select.addEventListener('change', function() {
                                selected = select.value;
                                //validateBusinessNatureRequired(idx, selected);
                            });
                        });
                    });
                </script>

                {{-- Purpose of visit --}}
                <div class="col-md-12">
                    <label>The purpose of your visits: </label>
                    <div class="custom-dropdown-container">
                        <div class="custom-dropdown" id="purpose_dropdown_{{ $index }}">
                            <div class="dropdown-selected" onclick="toggleCustomDropdown(this)">
                                <div class="selected-tags" id="purpose_tags_{{ $index }}"></div>
                                <span class="selected-text" data-placeholder="Select purpose of visit...">Select purpose of visit...</span>
                                <i class="dropdown-arrow">▼</i>
                            </div>
                            <div class="dropdown-options" id="purpose_options_{{ $index }}">
                                @foreach ($purposes as $i => $label)
                                <label class="dropdown-option">
                                    <input type="checkbox" name="attendees[{{ $index }}][purpose][]"
                                        value="{{ $label }}"
                                        data-label="{{ $label }}"
                                        {{ in_array($label, $data['purpose'] ?? []) ? 'checked' : '' }}
                                        onchange="updateCustomDropdownText(this)">
                                    <span>{{ $label }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Product Categories of Interest --}}
                <div class="col-md-12 mt-3">
                    <label>Product Categories of your interest: <span class="important">*</span></label>
                    <div class="custom-dropdown-container">
                        <div class="custom-dropdown" id="product_categories_dropdown_{{ $index }}">
                            <div class="dropdown-selected" onclick="toggleCustomDropdown(this)">
                                <div class="selected-tags" id="product_categories_tags_{{ $index }}"></div>
                                <span class="selected-text" data-placeholder="Select product categories...">Select product categories...</span>
                                <i class="dropdown-arrow">▼</i>
                            </div>
                            <div class="dropdown-options" id="product_categories_options_{{ $index }}">
                                @foreach ($productCategories as $opt)
                                <label class="dropdown-option">
                                    <input type="checkbox" name="attendees[{{ $index }}][products][]"
                                        value="{{ $opt }}"
                                        data-label="{{ $opt }}"
                                        {{ !empty($data['products']) && in_array($opt, $data['products']) ? 'checked' : '' }}
                                        onchange="updateCustomDropdownText(this)">
                                    <span>{{ $opt }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>






                {{-- Inaugural Function Participation --}}
                @php 
                $hide = true;
                @endphp
                @if($hide == false)
                <div class="col-md-12">
                    <label class="">Participate (In-person) in SEMICON Inaugural event on 2nd Sept <span
                            class="important">*</span><br>
                    </label>
                    <div class="d-flex gap-3">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input pm-inaugural-radio" type="radio"
                                name="attendees[{{ $index }}][pm_inaugural]"
                                id="pm_inaugural_yes_{{ $index }}" value="1" required
                                {{ ($data['pm_inaugural'] ?? '') === '1' ? 'checked' : '' }}
                                onchange="toggleIDFields({{ $index }})">
                            <label class="form-check-label" for="pm_inaugural_yes_{{ $index }}">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input pm-inaugural-radio" type="radio"
                                name="attendees[{{ $index }}][pm_inaugural]"
                                id="pm_inaugural_no_{{ $index }}" value="0" required
                                {{ ($data['pm_inaugural'] ?? '') === '0' ? 'checked' : '' }}
                                onchange="toggleIDFields({{ $index }})">
                            <label class="form-check-label" for="pm_inaugural_no_{{ $index }}">No</label>
                        </div>
                    </div>

                    <small>(Kindly note that participation (in-person) in the Inaugural event is subject to final confirmation based on availability and will be informed separately.)</small>
                </div>

                {{-- ID Card Type and Number (conditional) --}}
                <div class="col-md-6 id-fields id-fields-{{ $index }}" style="display: none;">
                    <label>ID Card Type <span class="text-danger">*</span></label>
                    <select name="attendees[{{ $index }}][id_card_type]"
                        class="form-select id-card-type-select" data-index="{{ $index }}" required>
                        <option value="">Select ID Card Type</option>
                        @foreach ($idTypes as $type)
                        <option value="{{ $type }}"
                            {{ ($data['id_card_type'] ?? '') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 id-fields id-fields-{{ $index }}" style="display: none;">
                    <label id="id-card-number-label-{{ $index }}">
                        ID Card Number <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="attendees[{{ $index }}][id_card_number]"
                        class="form-control id-card-number-input" data-index="{{ $index }}"
                        value="{{ $data['id_card_number'] ?? '' }}">
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        function updateIdCardNumberLabel(idx) {
                            const select = document.querySelector('.id-card-type-select[data-index="' + idx + '"]');
                            const label = document.getElementById('id-card-number-label-' + idx);
                            if (select && label) {
                                if (select.value === 'Aadhaar Card') {
                                    label.innerHTML = 'ID Card Number (Last 4 digits) <span class="text-danger">*</span>';
                                } else {
                                    label.innerHTML = 'ID Card Number <span class="text-danger">*</span>';
                                }
                            }
                        }
                        document.querySelectorAll('.id-card-type-select').forEach(function(select) {
                            const idx = select.getAttribute('data-index');
                            select.addEventListener('change', function() {
                                updateIdCardNumberLabel(idx);
                            });
                            // Initial state
                            updateIdCardNumberLabel(idx);
                        });
                    });
                </script>

                {{-- Profile Picture --}}
                <div class="col-md-12  id-fields id-fields-{{ $index }}" style="display: none;">
                    <label>Upload Profile Picture <span class="important">*</span></label>
                    <input type="file" name="attendees[{{ $index }}][profile_picture]"
                        class="form-control profile-upload" required accept="image/*"
                        onchange="validateProfilePicture(this)">
                    <small class="form-text text-muted">Max size: 1MB. Allowed formats: jpg, jpeg, png.</small>
                </div>


                <script>
                    function toggleIDFields(index) {
                        const yesRadio = document.getElementById(`pm_inaugural_yes_${index}`);
                        const showFields = yesRadio && yesRadio.checked;

                        // Target profile picture field for the specific index
                        const profilePictureField = document.querySelector(`[name="attendees[${index}][profile_picture]"]`);
                        //console.log('Toggling ID fields for index:', index, 'Show fields:', showFields);

                        // Toggle ID fields
                        document.querySelectorAll(`.id-fields-${index}`).forEach(field => {
                            field.style.display = showFields ? 'block' : 'none';

                            // Set required attribute for all inputs/selects inside the field
                            field.querySelectorAll('input, select').forEach(el => {
                                el.required = showFields;
                            });
                        });

                        // Toggle profile picture field visibility and required status
                        if (profilePictureField) {
                            console.log('Profile picture field found:', profilePictureField);
                            const parentDiv = profilePictureField.closest('.col-md-12');
                            if (parentDiv) {
                                parentDiv.style.display = showFields ? 'block' : 'none';
                            }
                            profilePictureField.required = showFields;
                        }
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        toggleIDFields({
                            {
                                $index
                            }
                        });
                        document.querySelectorAll('.pm-inaugural-radio').forEach(function(radio) {
                            radio.addEventListener('change', function() {
                                const idx = this.id.split('_').pop();
                                toggleIDFields(idx);
                            });
                        });
                    });
                </script>
                {{-- Registration Type --}}
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    function validateProfilePicture(input) {
                        if (input.files && input.files[0]) {
                            if (input.files[0].size > 1024 * 1024) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'File Too Large',
                                    text: 'File size must be less than or equal to 1MB.',
                                });
                                input.value = '';
                            }
                        }
                    }
                </script>

                @endif

                {{-- Consent Checkboxes --}}
                <div class="col-md-12">
                    <div class="form-check">
                        <input type="checkbox" name="attendees[{{ $index }}][consent]"
                            class="form-check-input" required
                            {{ ($data['consent'] ?? '') === 'on' ? 'checked' : '' }}>
                        <label class="form-check-label">I acknowledge the accuracy and authenticity of the above data
                            and its best as per my knowledge. All data is
                            protected and secured as outlined in our <a href="https://www.semi.org/en/privacy-policy"
                                target="_blank" rel="noopener">privacy policy</a>. <span
                                style="color: red;">*</span></label>
                    </div>
                </div>
                <div class="col-md-12 mb-5">
                    <div class="form-check">
                        <input type="checkbox" name="attendees[{{ $index }}][email_consent]"
                            class="form-check-input" {{ ($data['email_consent'] ?? '') === 'on' ? 'checked' : '' }}>
                        <label class="form-check-label">I agree to receive email communications from SEMICON
                            India.</label>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" name="captcha" id="captcha" class="form-control" maxlength="6"
                            placeholder="Enter Captcha" required>
                    </div>
                    <div class="col-md-6">
                        <div id="captcha-img" class="captcha-img">{!! $captchaSvg !!}</div>
                    </div>
                </div>


            </div>
            <div class="mt-4 d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-secondary attendee-prev-btn"
                    data-index="{{ $index }}">Previous</button>
                <button type="submit" class="btn btn-primary" data-index="{{ $index }}">Submit</button>
            </div>
        </div>
    </div>



    <script>
        function toggleIDFields(index) {
            const yesRadio = document.getElementById(`pm_inaugural_yes_${index}`);
            const showFields = yesRadio && yesRadio.checked;

            document.querySelectorAll(`.id-fields-${index}`).forEach(field => {
                field.style.display = showFields ? 'block' : 'none';
                // Remove required from all inputs/selects inside the field
                field.querySelectorAll('input, select').forEach(el => {
                    el.required = showFields;
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {


            const allIndexes = Array.from(document.querySelectorAll('[id^="pm_inaugural_yes_"]'))
                .map(el => el.id.split('_').pop());
            allIndexes.forEach(idx => toggleIDFields(idx));
        });
    </script>

    <script>
        class AttendeeFormValidator {
            constructor(index) {
                this.index = index;
                this.init();
            }

            init() {
                this.setupEventListeners();
                this.initializeValidations();
            }

            setupEventListeners() {
                // Category change events
                document.querySelector(`.category-select[data-index="${this.index}"]`)?.addEventListener('change',
                    () => {
                        this.updateCategoryDependentFields();
                    });

                // ID fields toggle
                document.querySelectorAll(`.pm-inaugural-radio[data-index="${this.index}"]`).forEach(radio => {
                    radio.addEventListener('change', () => this.toggleIdFields());
                });

                // Profile picture validation
                document.querySelector(`.profile-upload[data-index="${this.index}"]`)?.addEventListener('change', (e) =>
                    this.validateProfilePicture(e.target));

                // Event days validation
                document.querySelectorAll(`[name="attendees[${this.index}][event_days][]"]`).forEach(checkbox => {
                    checkbox.addEventListener('change', () => this.validateEventDays());
                });

                // Business nature validation
                //this.setupBusinessNatureValidation();
            }

            updateCategoryDependentFields() {
                const category = document.querySelector(`.category-select[data-index="${this.index}"]`).value;
                const isBusinessRequired = ['Exhibitor', 'Industry'].includes(category);

                // Update required fields based on category
                this.toggleFieldRequired('business_nature', isBusinessRequired);
                this.toggleFieldRequired('products', isBusinessRequired);
                this.updateRequiredLabels(isBusinessRequired);
                this.toggleOthersInput();
            }

            toggleFieldRequired(fieldName, required) {
                const elements = document.querySelectorAll(`[name="attendees[${this.index}][${fieldName}][]"]`);
                elements.forEach(el => {
                    el.required = required;
                    if (required) {
                        this.addValidationHandler(el,
                            `Please select at least one ${fieldName.replace('_', ' ')}`);
                    }
                });
            }

            setupBusinessNatureValidation() {
                const checkboxes = document.querySelectorAll(`[name="attendees[${this.index}][business_nature][]"]`);
                const form = checkboxes.length ? checkboxes[0].closest('form') : null;

                if (form) {
                    form.addEventListener('submit', (e) => {
                        const category = document.querySelector(`.category-select[data-index="${this.index}"]`)
                            .value;
                        if (['Exhibitor', 'Industry'].includes(category)) {
                            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                            if (!anyChecked) {
                                e.preventDefault();
                                this.showError('Please select at least one option for Nature of your Business');
                            }
                        }
                    });
                }
            }

            toggleIdFields() {
                const showFields = document.getElementById(`pm_inaugural_yes_${this.index}`).checked;
                document.querySelectorAll(`.id-fields-${this.index}`).forEach(field => {
                    field.style.display = showFields ? 'block' : 'none';
                    field.querySelectorAll('input, select').forEach(el => {
                        el.required = showFields;
                    });
                });
            }

            validateProfilePicture(input) {
                const maxSize = 1024 * 1024; // 1MB
                if (input.files && input.files[0] && input.files[0].size > maxSize) {
                    this.showError('File size must be less than or equal to 1MB');
                    input.value = '';
                    return false;
                }
                return true;
            }

            validateEventDays() {
                const checkboxes = document.querySelectorAll(`[name="attendees[${this.index}][event_days][]"]`);
                const isValid = Array.from(checkboxes).some(cb => cb.checked);

                // Find the Event Days dropdown and error message
                const dropdown = document.querySelector(`#event_days_dropdown_${this.index}`);
                const errorMsg = document.querySelector(`#event_days_error_${this.index}`);

                if (!isValid) {
                    // Mark as invalid and show error
                    if (dropdown) {
                        dropdown.classList.add('invalid');
                    }
                    if (errorMsg) {
                        errorMsg.style.display = 'block';
                    }
                } else {
                    // Mark as valid and hide error
                    if (dropdown) {
                        dropdown.classList.remove('invalid');
                    }
                    if (errorMsg) {
                        errorMsg.style.display = 'none';
                    }
                }

                return isValid;
            }

            toggleOthersInput() {
                const categoryInput = document.getElementById(`others-category-input-${this.index}`);
                const isOthersSelected = this.isOthersCategory();

                if (categoryInput) {
                    categoryInput.style.display = isOthersSelected ? 'block' : 'none';
                    categoryInput.querySelector('input').required = isOthersSelected;
                }
            }

            isOthersCategory() {
                const catSelect = document.querySelector(`.category-select[data-index="${this.index}"]`);
                const subcatSelect = document.querySelector(`.subcategory-select[data-index="${this.index}"]`);
                return catSelect?.value === 'Others' || subcatSelect?.value === 'Others';
            }

            updateCategoryDependentFields() {
                const categorySelect = document.querySelector(.category - select[data - index = "${this.index}"]);
                const category = categorySelect ? categorySelect.value : '';
                const isBusinessRequired = ['Exhibitor', 'Industry'].includes(category);

                // Nature of Business required logic
                const businessNature = document.getElementById(business_nature_$ {
                    this.index
                });
                if (businessNature) {
                    businessNature.required = isBusinessRequired;
                }

                // Product Categories required logic
                const products = document.getElementById(product_categories_$ {
                    this.index
                });
                if (products) {
                    products.required = isBusinessRequired;
                }

                // Update required labels
                this.updateRequiredLabels(isBusinessRequired);

                // Toggle "Others" input
                this.toggleOthersInput();
            }

            showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: message
                });
            }

            initializeValidations() {
                this.toggleIdFields();
                this.updateCategoryDependentFields();
            }
        }

        // Initialize form validation
        document.addEventListener('DOMContentLoaded', () => {
            const validator = new AttendeeFormValidator({
                {
                    $index
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function validateIndustryFields(index) {
                const categorySelect = document.querySelector(`.category-select[data-index="${index}"]`);
                const form = categorySelect?.closest('form');
                if (!form || !categorySelect) return;

                // Style for invalid fields
                const style = document.createElement('style');
                style.textContent = `
            .field-invalid {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            }
            .field-invalid + .ts-wrapper .ts-control {
                border-color: #dc3545 !important;
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            }
        `;
                document.head.appendChild(style);

                function validateField(field, fieldName) {
                    if (!field) return true;
                    const isValid = field.value && (!field.multiple || field.selectedOptions.length > 0);
                    field.classList.toggle('field-invalid', !isValid);
                    if (!isValid) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Required Field',
                            text: `Please select at least one option for ${fieldName}`
                        });
                        field.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        return false;
                    }
                    return true;
                }

                form.addEventListener('submit', function(e) {
                    let isValid = true;

                    // Always validate Event Days (mandatory for all attendees)
                    const eventDaysCheckboxes = document.querySelectorAll(`[name="attendees[${index}][event_days][]"]:checked`);
                    if (eventDaysCheckboxes.length === 0) {
                        const eventDaysDropdown = document.getElementById(`event_days_dropdown_${index}`);
                        const eventDaysError = document.getElementById(`event_days_error_${index}`);

                        if (eventDaysDropdown) {
                            eventDaysDropdown.classList.add('invalid');
                        }
                        if (eventDaysError) {
                            eventDaysError.style.display = 'block';
                        }
                        isValid = false;
                    }

                    // Validate Industry-specific fields
                    if (categorySelect.value === 'Industry') {
                        const businessNature = document.getElementById(`business_nature_${index}`);
                        const productCategories = document.getElementById(`product_categories_${index}`);
                        const purposeVisit = document.getElementById(`purpose_${index}_visit`);

                        if (!validateField(businessNature, 'Nature of your Business') ||
                            !validateField(productCategories, 'Product Categories of your interest') ||
                            !validateField(purposeVisit, 'Purpose of your visits')) {
                            isValid = false;
                        }
                    }

                    if (!isValid) {
                        e.preventDefault();
                    }
                });

                // Remove invalid styling on change
                [
                    `business_nature_${index}`,
                    `product_categories_${index}`,
                    `purpose_${index}_visit`
                ].forEach(id => {
                    const field = document.getElementById(id);
                    if (field) {
                        field.addEventListener('change', function() {
                            if (this.value || (this.selectedOptions && this.selectedOptions.length > 0)) {
                                this.classList.remove('field-invalid');
                            }
                        });
                    }
                });

                // Add Event Days validation clearing
                const eventDaysCheckboxes = document.querySelectorAll(`[name="attendees[${index}][event_days][]"]`);
                eventDaysCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        const checkedBoxes = document.querySelectorAll(`[name="attendees[${index}][event_days][]"]:checked`);
                        if (checkedBoxes.length > 0) {
                            const eventDaysDropdown = document.getElementById(`event_days_dropdown_${index}`);
                            const eventDaysError = document.getElementById(`event_days_error_${index}`);

                            if (eventDaysDropdown) {
                                eventDaysDropdown.classList.remove('invalid');
                            }
                            if (eventDaysError) {
                                eventDaysError.style.display = 'none';
                            }
                        }
                    });
                });
            }

            // Initialize validation for each form
            document.querySelectorAll('.category-select').forEach(select => {
                const index = select.getAttribute('data-index');
                validateIndustryFields(index);
            });
        });
    </script>
    <script>
        // Custom Dropdown Functions - Generic approach
        function toggleCustomDropdown(element) {
            const dropdown = element.closest('.custom-dropdown');
            if (dropdown) {
                dropdown.classList.toggle('open');

                // Close other dropdowns
                document.querySelectorAll('.custom-dropdown.open').forEach(otherDropdown => {
                    if (otherDropdown !== dropdown) {
                        otherDropdown.classList.remove('open');
                    }
                });
            }
        }

        function updateCustomDropdownText(element) {
            const dropdown = element.closest('.custom-dropdown');
            if (!dropdown) return;

            const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]:checked');
            const selectedText = dropdown.querySelector('.selected-text');
            const tagsContainer = dropdown.querySelector('.selected-tags');

            if (!selectedText || !tagsContainer) return;

            // Clear existing tags
            tagsContainer.innerHTML = '';

            // Check if this is the Event Days dropdown
            const isEventDays = dropdown.id && dropdown.id.includes('event_days_dropdown');

            if (checkboxes.length === 0) {
                selectedText.style.display = 'block';
                selectedText.classList.remove('has-selection');

                // Set appropriate placeholder text
                if (isEventDays) {
                    selectedText.textContent = 'Select event days';
                    // Mark as invalid for validation
                    dropdown.classList.add('invalid');
                } else {
                    // For other dropdowns, keep original placeholder
                    const originalPlaceholder = selectedText.getAttribute('data-placeholder');
                    selectedText.textContent = originalPlaceholder || selectedText.textContent;
                    dropdown.classList.remove('invalid');
                }
            } else {
                // Instead of showing tags, show comma-separated text in the selected-text span
                selectedText.style.display = 'block';
                selectedText.classList.add('has-selection');
                dropdown.classList.remove('invalid');

                // Get all selected item names
                const selectedItems = Array.from(checkboxes).map(checkbox => {
                    return checkbox.getAttribute('data-label') || checkbox.nextElementSibling.textContent;
                });

                // Show comma-separated list
                selectedText.textContent = selectedItems.join(', ');
            }
        }

        function createTag(text, checkbox) {
            const tag = document.createElement('div');
            tag.className = 'selected-tag';

            const span = document.createElement('span');
            span.textContent = text;

            const removeBtn = document.createElement('div');
            removeBtn.className = 'remove-tag';
            removeBtn.innerHTML = '×';
            removeBtn.onclick = function(e) {
                e.stopPropagation();
                checkbox.checked = false;
                updateCustomDropdownText(checkbox);
            };

            tag.appendChild(span);
            tag.appendChild(removeBtn);

            return tag;
        }

        // Initialize dropdown text on page load for index {{ $index }}
        document.addEventListener('DOMContentLoaded', function() {
            // Update text for all dropdowns with pre-selected values for this specific index
            const dropdowns = document.querySelectorAll('#event_days_dropdown_{{ $index }}, #business_nature_dropdown_{{ $index }}, #purpose_dropdown_{{ $index }}, #product_categories_dropdown_{{ $index }}');

            dropdowns.forEach(dropdown => {
                const firstCheckbox = dropdown.querySelector('input[type="checkbox"]');
                if (firstCheckbox) {
                    updateCustomDropdownText(firstCheckbox);
                }
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.custom-dropdown')) {
                document.querySelectorAll('.custom-dropdown.open').forEach(dropdown => {
                    dropdown.classList.remove('open');
                });
            }
        });
    </script>