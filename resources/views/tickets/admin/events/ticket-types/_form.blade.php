{{-- Ticket Type Form Partial --}}
@php
    $isEdit = isset($ticketType);
    $selectedDays = $isEdit ? $ticketType->eventDays->pluck('id')->toArray() : [];
@endphp

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label">Category <span class="text-danger">*</span></label>
            <select name="category_id" class="form-select" required id="category_id">
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" 
                            {{ ($isEdit && $ticketType->category_id == $category->id) || old('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label">Subcategory</label>
            <select name="subcategory_id" class="form-select" id="subcategory_id">
                <option value="">None</option>
                @php
                    $selectedCategoryId = $isEdit ? $ticketType->category_id : (old('category_id') ?? null);
                    $selectedCategory = $categories->firstWhere('id', $selectedCategoryId);
                @endphp
                @if($selectedCategory && $selectedCategory->subcategories)
                    @foreach($selectedCategory->subcategories as $subcategory)
                        <option value="{{ $subcategory->id }}" 
                                {{ ($isEdit && $ticketType->subcategory_id == $subcategory->id) || old('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
                            {{ $subcategory->name }}
                        </option>
                    @endforeach
                @endif
            </select>
            <small class="text-muted">On public registration, users pick Category then Subcategory; pricing is driven by the ticket type that matches this category+subcategory. Create one ticket type per (Category, Subcategory) combination with its price.</small>
            @error('subcategory_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>
@php
    $subcategoriesByCategory = $categories->keyBy('id')->map(function ($c) {
        return $c->subcategories->map(fn ($s) => ['id' => $s->id, 'name' => $s->name])->values()->toArray();
    })->toArray();
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="form-group mb-3">
            <label class="form-label">Ticket Name <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" 
                   value="{{ $isEdit ? $ticketType->name : old('name') }}" 
                   placeholder="e.g., Full Conference Pass" required>
            @error('name')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label">Available for</label>
            <select name="available_for" class="form-select" id="available_for">
                <option value="both" {{ ($isEdit ? ($ticketType->available_for ?? 'both') : old('available_for', 'both')) === 'both' ? 'selected' : '' }}>Both (Indian & International)</option>
                <option value="indian_only" {{ ($isEdit ? ($ticketType->available_for ?? '') : old('available_for')) === 'indian_only' ? 'selected' : '' }}>Indian only</option>
                <option value="international_only" {{ ($isEdit ? ($ticketType->available_for ?? '') : old('available_for')) === 'international_only' ? 'selected' : '' }}>International only</option>
            </select>
            <small class="text-muted">Restrict who can purchase this ticket (e.g. Indian-only or International-only)</small>
            @error('available_for')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" 
                      placeholder="Describe this ticket type...">{{ $isEdit ? $ticketType->description : old('description') }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<!-- Pricing Section -->
<div class="form-section mb-4" style="background: linear-gradient(135deg, #fff5e6 0%, #fff9f0 100%); border: 2px solid #ff9800; border-radius: 12px; padding: 1.5rem;">
    <h5 class="form-section-title" style="border-bottom-color: #ff9800; margin-bottom: 1.5rem;">
        <i class="fas fa-dollar-sign"></i>
        Pricing Configuration
    </h5>

    <!-- Early Bird Pricing -->
    <div class="mb-4">
        <h6 style="color: #ff9800; font-weight: 600; margin-bottom: 1rem;">
            <i class="fas fa-clock"></i> Early Bird Pricing
        </h6>
        <div class="row">
            <div class="col-md-6 national-price-field">
                <div class="form-group mb-3">
                    <label class="form-label">
                        <i class="fas fa-flag"></i> National Price (INR)
                    </label>
                    <input type="number" name="early_bird_price_national" class="form-control" 
                           value="{{ $isEdit ? $ticketType->early_bird_price_national : old('early_bird_price_national') }}" 
                           step="0.01" min="0" placeholder="0.00">
                    <small class="text-muted">Early bird price for Indian nationals</small>
                    @error('early_bird_price_national')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6 international-price-field">
                <div class="form-group mb-3">
                    <label class="form-label">
                        <i class="fas fa-globe"></i> International Price (USD)
                    </label>
                    <input type="number" name="early_bird_price_international" class="form-control" 
                           value="{{ $isEdit ? $ticketType->early_bird_price_international : old('early_bird_price_international') }}" 
                           step="0.01" min="0" placeholder="0.00">
                    <small class="text-muted">Early bird price for international users</small>
                    @error('early_bird_price_international')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Regular Pricing -->
    <div class="mb-3">
        <h6 style="color: #ff9800; font-weight: 600; margin-bottom: 1rem;">
            <i class="fas fa-calendar-alt"></i> Regular Pricing
        </h6>
        <div class="row">
            <div class="col-md-6 national-price-field">
                <div class="form-group mb-3">
                    <label class="form-label">
                        <i class="fas fa-flag"></i> National Price (INR) <span class="text-danger national-required-star">*</span>
                    </label>
                    <input type="number" name="regular_price_national" class="form-control national-price-input" 
                           value="{{ $isEdit ? $ticketType->regular_price_national : old('regular_price_national') }}" 
                           step="0.01" min="0" required placeholder="0.00">
                    <small class="text-muted">Regular price for Indian nationals</small>
                    @error('regular_price_national')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6 international-price-field">
                <div class="form-group mb-3">
                    <label class="form-label">
                        <i class="fas fa-globe"></i> International Price (USD) <span class="text-danger international-required-star">*</span>
                    </label>
                    <input type="number" name="regular_price_international" class="form-control international-price-input" 
                           value="{{ $isEdit ? $ticketType->regular_price_international : old('regular_price_international') }}" 
                           step="0.01" min="0" required placeholder="0.00">
                    <small class="text-muted">Regular price for international users</small>
                    @error('regular_price_international')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Early Bird End Date -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label">Early Bird End Date</label>
                <input type="date" name="early_bird_end_date" class="form-control" 
                       value="{{ $isEdit && $ticketType->early_bird_end_date ? $ticketType->early_bird_end_date->format('Y-m-d') : old('early_bird_end_date') }}">
                <small class="text-muted">Date when early bird pricing ends</small>
                @error('early_bird_end_date')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <!-- Per-Day Pricing -->
    <div class="mb-3">
        <h6 style="color: #ff9800; font-weight: 600; margin-bottom: 1rem;">
            <i class="fas fa-calendar-day"></i> Per-Day Pricing (Optional)
        </h6>
        <p class="text-muted mb-3" style="font-size: 0.875rem;">
            Set per-day pricing if users can select individual days. Price will be calculated per selected day.
        </p>
        <div class="row">
            <div class="col-md-6 national-price-field">
                <div class="form-group mb-3">
                    <label class="form-label">
                        <i class="fas fa-flag"></i> Per-Day National Price (INR)
                    </label>
                    <input type="number" name="per_day_price_national" class="form-control" 
                           value="{{ $isEdit ? $ticketType->per_day_price_national : old('per_day_price_national') }}" 
                           step="0.01" min="0" placeholder="0.00">
                    <small class="text-muted">Per-day price for Indian nationals</small>
                    @error('per_day_price_national')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6 international-price-field">
                <div class="form-group mb-3">
                    <label class="form-label">
                        <i class="fas fa-globe"></i> Per-Day International Price (USD)
                    </label>
                    <input type="number" name="per_day_price_international" class="form-control" 
                           value="{{ $isEdit ? $ticketType->per_day_price_international : old('per_day_price_international') }}" 
                           step="0.01" min="0" placeholder="0.00">
                    <small class="text-muted">Per-day price for international users</small>
                    @error('per_day_price_international')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="form-label">Capacity</label>
            <input type="number" name="capacity" class="form-control" 
                   value="{{ $isEdit ? $ticketType->capacity : old('capacity') }}" 
                   min="1" placeholder="Leave empty for unlimited">
            <small class="text-muted">Leave empty for unlimited tickets</small>
            @error('capacity')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="form-label">Sale Start Date</label>
            <input type="datetime-local" name="sale_start_at" class="form-control" 
                   value="{{ $isEdit && $ticketType->sale_start_at ? $ticketType->sale_start_at->format('Y-m-d\TH:i') : old('sale_start_at') }}">
            @error('sale_start_at')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="form-label">Sale End Date</label>
            <input type="datetime-local" name="sale_end_at" class="form-control" 
                   value="{{ $isEdit && $ticketType->sale_end_at ? $ticketType->sale_end_at->format('Y-m-d\TH:i') : old('sale_end_at') }}">
            @error('sale_end_at')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<!-- Day Access Configuration Section -->
<div class="form-section mb-4" style="background: linear-gradient(135deg, #e6f3ff 0%, #f0f8ff 100%); border: 2px solid #667eea; border-radius: 12px; padding: 1.5rem;">
    <h5 class="form-section-title" style="border-bottom-color: #667eea; margin-bottom: 1rem;">
        <i class="fas fa-calendar-check"></i>
        Day Access Configuration
    </h5>
    
    {{-- Enable Day Selection Toggle --}}
    <div class="switch-container mb-3" style="background: white; padding: 1.25rem; border-radius: 8px; border: 2px solid #667eea;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <label class="switch-label" for="enable_day_selection" style="font-weight: 600; font-size: 1rem; color: #2d3748; margin: 0;">
                    <i class="fas fa-calendar-day text-primary me-2"></i>
                    Enable Day Selection for Users
                </label>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.875rem;">
                    When enabled, users will see a dropdown to choose which day(s) they want to attend.
                    Options include "All 3 Days" and individual days.
                </p>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="enable_day_selection" id="enable_day_selection" value="1"
                       {{ ($isEdit && $ticketType->enable_day_selection) || old('enable_day_selection') == '1' ? 'checked' : '' }}
                       onchange="toggleDaySelectionConfig()">
            </div>
        </div>
    </div>

    {{-- Day Selection Configuration (shown when enable_day_selection is ON) --}}
    <div id="day-selection-config" style="display: {{ ($isEdit && $ticketType->enable_day_selection) || old('enable_day_selection') == '1' ? 'block' : 'none' }};">
        
        {{-- All Days Access Toggle --}}
        <div class="switch-container mb-3" style="background: #f8f9fa; padding: 1rem; border-radius: 8px; border: 1px solid #dee2e6;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <label class="switch-label" for="all_days_access" style="font-weight: 600; font-size: 0.95rem; color: #2d3748; margin: 0;">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Include "All 3 Days" Option
                    </label>
                    <p class="text-muted mb-0 mt-1" style="font-size: 0.8rem;">
                        Adds an "All 3 Days" option in the dropdown (uses regular price)
                    </p>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="all_days_access" id="all_days_access" value="1"
                           {{ ($isEdit && $ticketType->all_days_access) || old('all_days_access', '1') == '1' ? 'checked' : '' }}>
                </div>
            </div>
        </div>

        {{-- Available Days Selection --}}
        <div class="mb-3">
            <label class="form-label mb-2" style="font-weight: 600;">
                <i class="fas fa-list me-1"></i>
                Available Individual Days
            </label>
            <p class="text-muted mb-2" style="font-size: 0.85rem;">Select which individual days will appear in the dropdown (uses per-day price if set)</p>
            <div class="row" style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
            @forelse($eventDays as $day)
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="event_day_ids[]" 
                               value="{{ $day->id }}" id="day_{{ $day->id }}"
                               {{ in_array($day->id, old('event_day_ids', $selectedDays)) ? 'checked' : '' }}>
                        <label class="form-check-label" for="day_{{ $day->id }}">
                            <strong>{{ $day->label }}</strong>
                                <small class="text-muted d-block">
                                    {{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}
                            </small>
                        </label>
                    </div>
                </div>
            @empty
                <div class="col-12">
                        <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No event days found. Please create event days first.
                    </div>
                </div>
            @endforelse
            </div>
        </div>
    </div>

    {{-- Info when Day Selection is disabled --}}
    <div id="day-selection-disabled-info" style="display: {{ ($isEdit && $ticketType->enable_day_selection) || old('enable_day_selection') == '1' ? 'none' : 'block' }};">
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            Day selection is disabled. Users will get access to all event days automatically.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group mb-3">
            <label class="form-label">Sort Order</label>
            <input type="number" name="sort_order" class="form-control" 
                   value="{{ $isEdit ? $ticketType->sort_order : (old('sort_order') ?? 0) }}" 
                   min="0">
            @error('sort_order')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="switch-container mb-3">
            <label class="switch-label" for="is_active">
                Active (Available for purchase)
            </label>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                       {{ ($isEdit && $ticketType->is_active) || (!isset($ticketType) && old('is_active', '1') == '1') ? 'checked' : '' }}>
            </div>
        </div>
    </div>
</div>

<script>
function toggleDaySelectionConfig() {
    const enableDaySelection = document.getElementById('enable_day_selection').checked;
    const daySelectionConfig = document.getElementById('day-selection-config');
    const daySelectionDisabledInfo = document.getElementById('day-selection-disabled-info');
    
    if (enableDaySelection) {
        daySelectionConfig.style.display = 'block';
        daySelectionDisabledInfo.style.display = 'none';
    } else {
        daySelectionConfig.style.display = 'none';
        daySelectionDisabledInfo.style.display = 'block';
    }
}

// Toggle price fields based on "Available for" selection
function togglePriceFields() {
    const availableFor = document.getElementById('available_for').value;
    const nationalFields = document.querySelectorAll('.national-price-field');
    const internationalFields = document.querySelectorAll('.international-price-field');
    const nationalInputs = document.querySelectorAll('.national-price-input');
    const internationalInputs = document.querySelectorAll('.international-price-input');
    const nationalStars = document.querySelectorAll('.national-required-star');
    const internationalStars = document.querySelectorAll('.international-required-star');
    
    if (availableFor === 'indian_only') {
        // Show national, hide international
        nationalFields.forEach(el => el.style.display = 'block');
        internationalFields.forEach(el => el.style.display = 'none');
        nationalInputs.forEach(el => el.required = true);
        internationalInputs.forEach(el => el.required = false);
        nationalStars.forEach(el => el.style.display = 'inline');
        internationalStars.forEach(el => el.style.display = 'none');
    } else if (availableFor === 'international_only') {
        // Show international, hide national
        nationalFields.forEach(el => el.style.display = 'none');
        internationalFields.forEach(el => el.style.display = 'block');
        nationalInputs.forEach(el => el.required = false);
        internationalInputs.forEach(el => el.required = true);
        nationalStars.forEach(el => el.style.display = 'none');
        internationalStars.forEach(el => el.style.display = 'inline');
    } else {
        // Show both
        nationalFields.forEach(el => el.style.display = 'block');
        internationalFields.forEach(el => el.style.display = 'block');
        nationalInputs.forEach(el => el.required = true);
        internationalInputs.forEach(el => el.required = true);
        nationalStars.forEach(el => el.style.display = 'inline');
        internationalStars.forEach(el => el.style.display = 'inline');
    }
}

// Listen to "Available for" dropdown change
document.getElementById('available_for')?.addEventListener('change', togglePriceFields);

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePriceFields();
});

// Subcategories per category (from server)
window.categoriesWithSubcategories = @json($subcategoriesByCategory);

function populateSubcategories(categoryId, selectedSubcategoryId) {
    const subcategorySelect = document.getElementById('subcategory_id');
    subcategorySelect.innerHTML = '<option value="">None</option>';
    if (!categoryId) return;
    const list = window.categoriesWithSubcategories[categoryId];
    if (list && list.length) {
        list.forEach(function (s) {
            const opt = document.createElement('option');
            opt.value = s.id;
            opt.textContent = s.name;
            if (selectedSubcategoryId && String(s.id) === String(selectedSubcategoryId)) {
                opt.selected = true;
            }
            subcategorySelect.appendChild(opt);
        });
    }
}

document.getElementById('category_id')?.addEventListener('change', function () {
    const categoryId = this.value;
    populateSubcategories(categoryId, null);
});

// On page load, ensure subcategories are populated for the currently selected category (e.g. after validation error)
(function () {
    const categorySelect = document.getElementById('category_id');
    const subcategorySelect = document.getElementById('subcategory_id');
    if (categorySelect && subcategorySelect && categorySelect.value) {
        const selectedSub = subcategorySelect.value;
        populateSubcategories(categorySelect.value, selectedSub || null);
    }
})();
</script>

