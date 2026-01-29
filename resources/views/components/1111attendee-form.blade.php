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
                    <label>Title <span class="text-danger">*</span></label>
                    <select name="attendees[{{ $index }}][title]" class="form-control" required>
                        <option value="" disabled {{ empty($data['title']) ? 'selected' : '' }}>--- Title ---
                        </option>
                        @foreach ($titles as $val => $label)
                            <option value="{{ $val }}" {{ ($data['title'] ?? '') === $val ? 'selected' : '' }}>
                                {{ $label }}</option>
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
                    <label>Postal Code <span class="text-danger">*</span></label>
                    <input type="number" name="attendees[{{ $index }}][postal_code]" class="form-control"
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
                {{-- <div class="col-md-6 mt-3">
                    <label class="form-label d-block">Event Days <span class="important">*</span></label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @php
                            $eventOptions = [
                                'All' => 'All Days',
                                'Day 1' => 'Day 1 - 2nd September',
                                'Day 2' => 'Day 2 - 3rd September',
                                'Day 3' => 'Day 3 - 4th September',
                            ];
                        @endphp
                        @foreach ($eventOptions as $val => $label)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                    id="event_day_{{ $val }}_{{ $index }}"
                                    name="attendees[{{ $index }}][event_days][]" value="{{ $val }}"
                                    {{ !empty($data['event_days']) && in_array($val, $data['event_days']) ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="event_day_{{ $val }}_{{ $index }}">{{ $label }}</label>
                            </div>
                        @endforeach
                    </div>
                </div> --}}
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
                    <select id="event_days_{{ $index }}" name="attendees[{{ $index }}][event_days][]"
                        multiple="multiple" class="form-control multiselect-dropdown" required>
                        @foreach ($eventOptions as $val => $label)
                            <option value="{{ $val }}"
                                {{ !empty($data['event_days']) && in_array($val, $data['event_days']) ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label>Category <span class="text-danger">*</span></label>
                    <select class="form-select category-select" name="attendees[{{ $index }}][job_category]"
                        data-index="{{ $index }}" required>
                        <option value="">--- Select ---</option>
                        @foreach ($jobCategories as $category)
                            <option value="{{ $category }}"
                                {{ ($data['job_category'] ?? '') === $category ? 'selected' : '' }}>
                                {{ $category }}</option>
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
                            // Purpose checkboxes
                            // validatePurposeRequired(idx, selected);
                            // validateEventDaysRequired(idx);
                            /*
                                                        // Product Categories
                                                        const productsSelect = document.querySelector(`[name="attendees[${idx}][products][]"]`);
                                                        if (productsSelect) {
                                                            // Instead of making the select required, require at least one checkbox to be checked
                                                            const productCheckboxes = document.querySelectorAll(`[name="attendees[${idx}][products][]"]`);
                                                            const form = productsSelect.closest('form');

                                                            if (form && (selected === 'Exhibitor' || selected === 'Industry')) {
                                                                // Remove any existing handler
                                                                if (form._productsHandlerFn) {
                                                                    form.removeEventListener('submit', form._productsHandlerFn);
                                                                }

                                                                // Create new handler
                                                                const handler = function(e) {
                                                                    const anyChecked = Array.from(productCheckboxes).some(cb => cb.checked);
                                                                    if (!anyChecked) {
                                                                        e.preventDefault();
                                                                        Swal.fire({
                                                                            icon: 'warning',
                                                                            title: 'Required Field',
                                                                            text: 'Please select at least one product category'
                                                                        });
                                                                    }
                                                                };

                                                                // Store handler reference for later removal
                                                                form._productsHandlerFn = handler;
                                                                form.addEventListener('submit', handler);

                                                                // Optional: Clear validation message when user checks any box
                                                                productCheckboxes.forEach(cb => {
                                                                    cb.addEventListener('change', () => {
                                                                        const anyChecked = Array.from(productCheckboxes).some(cb => cb
                                                                            .checked);
                                                                        if (anyChecked) {
                                                                            // Clear any previous validation messages if needed
                                                                            cb.setCustomValidity('');
                                                                        }
                                                                    });
                                                                });
                                                            }
                                                        } */
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
                            });
                            // Initial state
                            updateMandatoryFields(idx);
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


                {{-- Nature of Business --}} <div class="col-md-12 mt-3">
                    <label>Nature of your Business: <span class="important">*</span></label>
                    <select name="attendees[{{ $index }}][business_nature][]" multiple
                        id="business_nature_{{ $index }}">
                        <option value="">Select business nature</option>
                        @foreach ($sectors as $i => $sector)
                            @if (!empty($sector['name']))
                                <option value="{{ $sector['name'] }}"
                                    {{ !empty($data['business_nature']) && in_array($sector['name'], (array) $data['business_nature']) ? 'selected' : '' }}>
                                    {{ $sector['name'] }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
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
                        const idx = {{ $index }};
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
                    <div class="row">
                        <div class="col-md-12">
                            <select id="purpose_{{ $index }}_visit"
                                name="attendees[{{ $index }}][purpose][]" multiple>
                                @foreach ($purposes as $i => $label)
                                    <option value="{{ $label }}"
                                        {{ in_array($label, $data['purpose'] ?? []) ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Product Categories of Interest --}}
                <div class="col-md-12 mt-3">
                    <label for="product_categories_{{ $index }}">
                        Product Categories of your interest: <span class="important">*</span>
                    </label>
                    <select id="product_categories_{{ $index }}"
                        name="attendees[{{ $index }}][products][]" multiple>
                        @foreach ($productCategories as $opt)
                            <option value="{{ $opt }}"
                                {{ !empty($data['products']) && in_array($opt, $data['products']) ? 'selected' : '' }}>
                                {{ $opt }}
                            </option>
                        @endforeach
                    </select>
                </div>






                {{-- Inaugural Function Participation --}}
                <div class="col-md-12">
                    <label class="">Participate in SEMICON Inaugural session on 2nd Sept <span
                            class="important">*</span><br>
                        <small>(Participation is subject to confirmation based on availability.)</small></label>
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
                        toggleIDFields({{ $index }});
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
                <button type="submit" class="btn btn-primary" data-index="{{ $index }}" onclick="validateAndSubmitForm();">Submit</button>
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
                return Array.from(checkboxes).some(cb => cb.checked);
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

            updateRequiredLabels(isRequired) {
                const labelSelectors = [{
                        selector: '#purpose_0_visit',
                        text: 'The purpose of your visits:'
                    },
                    {
                        selector: '[name^="attendees"][name$="[products][]"]',
                        text: 'Product Categories of your interest:'
                    },
                    {
                        selector: '[name^="attendees"][name$="[business_nature]"]',
                        text: 'Nature of your Business:'
                    }
                ];

                labelSelectors.forEach(({
                    selector,
                    text
                }) => {
                    var label = document.querySelector(`label[for^="${selector}"]`);
                    if (label) {
                        label.innerHTML = `${text} ${isRequired ? '<span class="important">*</span>' : ''}`;
                    }
                });
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
            const validator = new AttendeeFormValidator({{ $index }});
        });
    </script>
    <script>
        /*
        class FormValidator {
            constructor(index) {
                this.index = index;
                this.formSubmitted = false;
                this.init();
            }

            init() {
                this.form = document.querySelector(`form`);
                this.setupMultiSelectValidation();
                this.setupFormSubmitHandler();
            }

            setupMultiSelectValidation() {
                // Validate business nature
                const businessNatureCheckboxes = document.querySelectorAll(`[name="attendees[${this.index}][business_nature][]"]`);
                const productCheckboxes = document.querySelectorAll(`[name="attendees[${this.index}][products][]"]`);
                const purposeCheckboxes = document.querySelectorAll(`[name="attendees[${this.index}][purpose][]"]`);

                this.setupCheckboxGroupValidation(businessNatureCheckboxes, 'Business Nature');
                this.setupCheckboxGroupValidation(productCheckboxes, 'Product Categories');
                this.setupCheckboxGroupValidation(purposeCheckboxes, 'Purpose');
            }

            setupCheckboxGroupValidation(checkboxes, fieldName) {
                if (!checkboxes.length) return;

                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', () => {
                        if (this.formSubmitted) {
                            this.validateCheckboxGroup(checkboxes, fieldName);
                        }
                    });
                });
            }

            validateCheckboxGroup(checkboxes, fieldName) {
                const category = document.querySelector(`.category-select[data-index="${this.index}"]`)?.value;
                const isRequired = ['Exhibitor', 'Industry'].includes(category);

                if (isRequired) {
                    const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                    if (!anyChecked) {
                        this.showError(`Please select at least one ${fieldName}`);
                        return false;
                    }
                }
                return true;
            }

            setupFormSubmitHandler() {
                this.form.addEventListener('submit', (e) => {
                    this.formSubmitted = true;
                    
                    const category = document.querySelector(`.category-select[data-index="${this.index}"]`)?.value;
                    if (['Exhibitor', 'Industry'].includes(category)) {
                        const businessNatureCheckboxes = document.querySelectorAll(`[name="attendees[${this.index}][business_nature][]"]`);
                        const productCheckboxes = document.querySelectorAll(`[name="attendees[${this.index}][products][]"]`);
                        
                        const isBusinessNatureValid = this.validateCheckboxGroup(businessNatureCheckboxes, 'Business Nature');
                        const isProductsValid = this.validateCheckboxGroup(productCheckboxes, 'Product Categories');

                        if (!isBusinessNatureValid || !isProductsValid) {
                            e.preventDefault();
                        }
                    }
                });
            }

            showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: message
                });
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new FormValidator({{ $index }});
        });
        */
    </script>
