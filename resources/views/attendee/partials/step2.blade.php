@php
    $isFirst = $index === 0;
@endphp

<div class="attendee-step attendee-step-2" id="attendee-step-2-{{ $index }}" data-attendee-step style="display: none;">
    <div class="row g-3">
        <div class="col-md-4">
            <label>Job Category <span class="text-danger">*</span></label>
            <select name="attendees[{{ $index }}][job_category]" class="form-select job-category-select" data-index="{{ $index }}" required>
                <option value="">--- Select ---</option>
                @foreach($jobCategories as $cat)
                    <option value="{{ $cat }}" {{ ($data['job_category'] ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label>Job Subcategory</label>
            <select name="attendees[{{ $index }}][job_subcategory]" class="form-select">
                <option value="">--- Select ---</option>
                <option value="Startup">Startup</option>
                <option value="SME">SME</option>
                <option value="Large Company">Large Company</option>
            </select>
        </div>

        <div class="col-md-4 startup-section" id="startup-section-{{ $index }}" style="display: none;">
            <label>Are you a Startup? <span class="text-danger">*</span></label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="attendees[{{ $index }}][startup]" value="Yes">
                <label class="form-check-label">Yes</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="attendees[{{ $index }}][startup]" value="No">
                <label class="form-check-label">No</label>
            </div>
        </div>

        @foreach ([
            'business_nature' => 'Nature of your Business',
            'purpose' => 'Purpose of Visit',
            'product_category' => 'Product Categories',
            'event_days' => 'Event Days'
        ] as $field => $label)
        <div class="col-md-6">
            <label>{{ $label }} <span class="text-danger">*</span></label>
            <div class="checkbox-dropdown" data-field="{{ $field }}" data-index="{{ $index }}">
                <input type="text" class="form-control dropdown-toggle" placeholder="Select" readonly>
                <div class="dropdown-menu">
                    @php
                        $optionss = [];
                        if ($field === 'business_nature') $options = $sectors;
                        elseif ($field === 'purpose') $options = $purposes;
                        elseif ($field === 'product_category') $options = $productCategories;
                        elseif ($field === 'event_days') $options = $eventOptions;
                    @endphp
                    @foreach ($optionss as $options)
                        <label class="dropdown-item">
                            <input type="checkbox" name="attendees[{{ $index }}][{{ $field }}][]" value="{{ $option }}"
                            {{ !empty($data[$field]) && in_array($option, (array) $data[$field]) ? 'checked' : '' }}>
                            {{ $option }}
                        </label>
                    @endforeach
                </div>
                <select name="attendees[{{ $index }}][{{ $field }}][]" multiple class="d-none"></select>
            </div>
        </div>
        @endforeach

        <div class="col-md-6">
            <label>ID Type</label>
            <select name="attendees[{{ $index }}][id_type]" class="form-select">
                <option value="">--- Select ---</option>
                @foreach ($idTypes as $id)
                    <option value="{{ $id }}" {{ ($data['id_type'] ?? '') == $id ? 'selected' : '' }}>{{ $id }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label>ID Upload (PDF/JPG/PNG)</label>
            <input type="file" name="attendees[{{ $index }}][id_upload]" accept=".pdf,.jpg,.jpeg,.png" class="form-control">
        </div>

        <div class="col-md-6">
            <label>Captcha <span class="text-danger">*</span></label>
            <input type="text" name="attendees[{{ $index }}][captcha]" class="form-control" maxlength="6" required>
            <div class="mt-2">{!! $captchaSvg !!}</div>
            <a href="" class="d-block mt-2 text-primary" onclick="location.reload(); return false;">Reload Captcha</a>
        </div>
    </div>
    <div class="mt-4 d-flex justify-content-between">
        <button type="button" class="btn btn-secondary attendee-prev-btn" data-index="{{ $index }}">Back</button>
        <button type="submit" class="btn btn-success">Submit</button>
    </div>
</div>
