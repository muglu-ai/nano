@props(['index', 'data', 'productCategories' => [], 'sectors' => []])

@php
    $titles = ['mr' => 'Mr.', 'mrs' => 'Mrs.', 'ms' => 'Ms.', 'dr' => 'Dr.', 'prof' => 'Prof.'];
    $jobCategories = ['Industry', 'Government', 'Exhibitor', 'Academic', 'Media', 'Others'];
    $purposes = [
        'Purchase new products and services',
        'Source new vendors for an ongoing project',
        'Join the buyer-seller program & meet potential suppliers',
        'To connect & engage with existing suppliers',
        'Stay up to date with the latest innovations',
        'Compare and Benchmark technologies / solutions',
    ];
@endphp

<div class="attendee-step attendee-step-2" id="attendee-step-2-{{ $index }}" style="display:none;">
    <div class="row g-3">
        {{-- Category & Subcategory --}}
        <div class="col-md-6">
            <label>Category *</label>
            <select class="form-select category-select" name="attendees[{{ $index }}][job_category]" data-index="{{ $index }}" required>
                <option value="">Select Category</option>
                @foreach ($jobCategories as $category)
                    <option value="{{ $category }}" {{ ($data['job_category'] ?? '') === $category ? 'selected' : '' }}>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label>Subcategory *</label>
            <select class="form-select subcategory-select" name="attendees[{{ $index }}][job_subcategory]" data-index="{{ $index }}" data-selected="{{ $data['job_subcategory'] ?? '' }}" required>
                <option value="">Select Subcategory</option>
            </select>
        </div>
        <div class="col-md-12 others-category-input" id="others-category-input-{{ $index }}" style="display: none;">
            <label>Please specify your Category/Subcategory <span class="important">*</span></label>
            <input type="text" class="form-control" name="attendees[{{ $index }}][other_job_category]" value="{{ $data['other_job_category'] ?? '' }}" placeholder="Enter your category/subcategory">
        </div>

        {{-- Purpose of visit --}}
        <div class="col-md-12">
            <label class="form-label d-block">The purpose of your visits: <span class="text-danger">*</span></label>
            <div class="row">
                @foreach ($purposes as $i => $label)
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input purpose-checkbox" type="checkbox" id="purpose{{ $i + 1 }}_{{ $index }}" name="attendees[{{ $index }}][purpose][]" value="{{ $label }}" {{ in_array($label, $data['purpose'] ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label" for="purpose{{ $i + 1 }}_{{ $index }}">{{ $label }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Product Categories of Interest --}}
        <div class="col-md-12 mt-3 d-flex flex-column flex-md-row align-items-start">
            <label for="products_{{ $index }}" class="me-md-3 mb-2 mb-md-0 pt-1" style="white-space: nowrap; min-width: 240px;">
                Product Categories of your interest: <span class="important">*</span>
            </label>
            <select id="multiple-checkboxes" multiple name="attendees[{{ $index }}][products][]" required class="form-control" style="max-height: 200px; min-width: 250px;">
                @foreach ($productCategories as $opt)
                    <option value="{{ $opt }}" {{ !empty($data['products']) && in_array($opt, $data['products']) ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>

        {{-- Nature of Business --}}
        <div class="col-md-12 mt-3">
            <label>Nature of your Business: <span class="important">*</span></label>
            <select class="form-select" name="attendees[{{ $index }}][business_nature]" required>
                <option value="">Select Business Nature</option>
                @foreach ($sectors as $sector)
                    @if (!empty($sector['name']))
                        <option value="{{ $sector['name'] }}" {{ ($data['business_nature'] ?? '') === $sector['name'] ? 'selected' : '' }}>{{ $sector['name'] }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        {{-- Inaugural Function Participation --}}
        <div class="col-md-12">
            <label class="form-label d-block">Participate in SEMICON Inaugural session on 2nd Sept <span class="important">*</span><br>
                <small>(Participation is subject to confirmation based on availability.)</small></label>
            <div class="form-check form-check-inline">
                <input class="form-check-input pm-inaugural-radio" type="radio" name="attendees[{{ $index }}][pm_inaugural]" id="pm_inaugural_yes_{{ $index }}" value="1" required {{ ($data['pm_inaugural'] ?? '') === '1' ? 'checked' : '' }} onchange="toggleIDFields({{ $index }})">
                <label class="form-check-label" for="pm_inaugural_yes_{{ $index }}">Yes</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input pm-inaugural-radio" type="radio" name="attendees[{{ $index }}][pm_inaugural]" id="pm_inaugural_no_{{ $index }}" value="0" required {{ ($data['pm_inaugural'] ?? '') === '0' ? 'checked' : '' }} onchange="toggleIDFields({{ $index }})">
                <label class="form-check-label" for="pm_inaugural_no_{{ $index }}">No</label>
            </div>
        </div>

        {{-- ID Card Type and Number (conditional) --}}
        <div class="col-md-6 id-fields id-fields-{{ $index }}" style="display: none;">
            <label>ID Card Type *</label>
            <input type="text" name="attendees[{{ $index }}][id_card_type]" class="form-control" value="{{ $data['id_card_type'] ?? '' }}">
        </div>
        <div class="col-md-6 id-fields id-fields-{{ $index }}" style="display: none;">
            <label>ID Card Number *</label>
            <input type="text" name="attendees[{{ $index }}][id_card_number]" class="form-control" value="{{ $data['id_card_number'] ?? '' }}">
        </div>

        {{-- Registration Type --}}
        <div class="col-md-6">
            <label>Registration Type <span class="important">*</span></label>
            <select name="attendees[{{ $index }}][registration_type]" class="form-select" required>
                <option value="">Select Registration Type</option>
                <option value="Online" {{ ($data['registration_type'] ?? '') === 'Online' ? 'selected' : '' }}>Online</option>
                <option value="In-Person" {{ ($data['registration_type'] ?? '') === 'In-Person' ? 'selected' : '' }}>In-Person</option>
            </select>
        </div>

        {{-- Event Days --}}
        <div class="col-md-6">
            <label>Event Days <span class="important">*</span></label>
            <select name="attendees[{{ $index }}][event_days][]" class="form-select event-days-select" multiple required data-index="{{ $index }}" style="display: none;">
                <option value="Day 1" {{ !empty($data['event_days']) && in_array('Day 1', $data['event_days']) ? 'selected' : '' }}>Day 1 - 2nd September, 2025</option>
                <option value="Day 2" {{ !empty($data['event_days']) && in_array('Day 2', $data['event_days']) ? 'selected' : '' }}>Day 2 - 3rd September, 2025</option>
                <option value="Day 3" {{ !empty($data['event_days']) && in_array('Day 3', $data['event_days']) ? 'selected' : '' }}>Day 3 - 4th September, 2025</option>
                <option value="All Day" {{ !empty($data['event_days']) && in_array('All Day', $data['event_days']) ? 'selected' : '' }}>All Day - 2nd|3rd|4th September, 2025</option>
            </select>
            <input type="text" class="form-control event-days-dropdown" readonly placeholder="Select event days" data-index="{{ $index }}" style="background-color: #fff; cursor: pointer;">
            <div class="dropdown-menu event-days-menu" style="width:100%; max-width:350px;" data-index="{{ $index }}">
                <label class="dropdown-item"><input type="checkbox" value="Day 1" class="event-day-checkbox" data-index="{{ $index }}" {{ !empty($data['event_days']) && in_array('Day 1', $data['event_days']) ? 'checked' : '' }}> Day 1 - 2nd September</label>
                <label class="dropdown-item"><input type="checkbox" value="Day 2" class="event-day-checkbox" data-index="{{ $index }}" {{ !empty($data['event_days']) && in_array('Day 2', $data['event_days']) ? 'checked' : '' }}> Day 2 - 3rd September</label>
                <label class="dropdown-item"><input type="checkbox" value="Day 3" class="event-day-checkbox" data-index="{{ $index }}" {{ !empty($data['event_days']) && in_array('Day 3', $data['event_days']) ? 'checked' : '' }}> Day 3 - 4th September</label>
                <label class="dropdown-item"><input type="checkbox" value="All Day" class="event-day-checkbox" data-index="{{ $index }}" {{ !empty($data['event_days']) && in_array('All Day', $data['event_days']) ? 'checked' : '' }}> All Day - 2nd–4th Sept</label>
            </div>
            <small class="form-text text-muted">Select up to 2 days, or choose "All Day".</small>
        </div>

        {{-- Profile Picture --}}
        <div class="col-md-12">
            <label>Upload Profile Picture <span class="important">*</span></label>
            <input type="file" name="attendees[{{ $index }}][profile_picture]" class="form-control profile-upload" required accept="image/*">
            <small class="form-text text-muted">Max size: 1MB. Allowed formats: jpg, jpeg, png.</small>
        </div>

        {{-- Consent Checkboxes --}}
        <div class="col-md-12">
            <div class="form-check">
                <input type="checkbox" name="attendees[{{ $index }}][consent]" class="form-check-input" required {{ ($data['consent'] ?? '') === 'on' ? 'checked' : '' }}>
                <label class="form-check-label">I acknowledge the accuracy and authenticity of the above data and its best as per my knowledge. <span style="color: red;">*</span></label>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-check">
                <input type="checkbox" name="attendees[{{ $index }}][email_consent]" class="form-check-input" {{ ($data['email_consent'] ?? '') === 'on' ? 'checked' : '' }}>
                <label class="form-check-label">I agree to receive email communications from SEMI. All data is protected and secured as outlined in our <a href="https://www.semi.org/en/privacy-policy" target="_blank" rel="noopener">privacy policy</a>.</label>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleIDFields(index) {
        const yesSelected = document.getElementById(`pm_inaugural_yes_${index}`).checked;
        document.querySelectorAll(`.id-fields-${index}`).forEach(field => {
            field.style.display = yesSelected ? 'block' : 'none';
            const input = field.querySelector('input');
            if (input) input.required = yesSelected;
        });
    }
    document.addEventListener('DOMContentLoaded', () => toggleIDFields({{ $index }}));
</script>