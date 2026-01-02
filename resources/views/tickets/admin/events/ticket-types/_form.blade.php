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
            @error('subcategory_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

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

<div class="row">
    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="form-label">Early Bird Price</label>
            <input type="number" name="early_bird_price" class="form-control" 
                   value="{{ $isEdit ? $ticketType->early_bird_price : old('early_bird_price') }}" 
                   step="0.01" min="0" placeholder="0.00">
            @error('early_bird_price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="form-label">Regular Price <span class="text-danger">*</span></label>
            <input type="number" name="regular_price" class="form-control" 
                   value="{{ $isEdit ? $ticketType->regular_price : old('regular_price') }}" 
                   step="0.01" min="0" required placeholder="0.00">
            @error('regular_price')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group mb-3">
            <label class="form-label">Early Bird End Date</label>
            <input type="date" name="early_bird_end_date" class="form-control" 
                   value="{{ $isEdit && $ticketType->early_bird_end_date ? $ticketType->early_bird_end_date->format('Y-m-d') : old('early_bird_end_date') }}">
            @error('early_bird_end_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
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

<!-- All Days Access Section -->
<div class="form-section mb-4" style="background: linear-gradient(135deg, #e6f3ff 0%, #f0f8ff 100%); border: 2px solid #667eea; border-radius: 12px; padding: 1.5rem;">
    <h5 class="form-section-title" style="border-bottom-color: #667eea; margin-bottom: 1rem;">
        <i class="fas fa-calendar-check"></i>
        Day Access Configuration
    </h5>
    
    <div class="switch-container mb-3" style="background: white; padding: 1.25rem; border-radius: 8px; border: 2px solid #667eea;">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <label class="switch-label" for="all_days_access" style="font-weight: 600; font-size: 1rem; color: #2d3748; margin: 0;">
                    <i class="fas fa-check-circle text-success me-2"></i>
                    Grant Access to ALL Event Days
                </label>
                <p class="text-muted mb-0 mt-2" style="font-size: 0.875rem;">
                    When enabled, this ticket will automatically grant access to all event days. 
                    Users won't need to select individual days.
                </p>
            </div>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="all_days_access" id="all_days_access" value="1"
                       {{ ($isEdit && $ticketType->all_days_access) || old('all_days_access') == '1' ? 'checked' : '' }}
                       onchange="toggleDaySelection()">
            </div>
        </div>
    </div>

    <div id="day-selection-section" style="display: {{ ($isEdit && $ticketType->all_days_access) || old('all_days_access') == '1' ? 'none' : 'block' }};">
        <label class="form-label mb-3">Select Specific Days (if not using "All Days")</label>
        <div class="row">
            @forelse($eventDays as $day)
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="event_day_ids[]" 
                               value="{{ $day->id }}" id="day_{{ $day->id }}"
                               {{ in_array($day->id, old('event_day_ids', $selectedDays)) ? 'checked' : '' }}>
                        <label class="form-check-label" for="day_{{ $day->id }}">
                            <strong>{{ $day->label }}</strong>
                            <br>
                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse($day->date)->format('M d, Y (l)') }}
                            </small>
                        </label>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No event days found. Please create event days first.
                    </div>
                </div>
            @endforelse
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
function toggleDaySelection() {
    const allDaysChecked = document.getElementById('all_days_access').checked;
    const daySelectionSection = document.getElementById('day-selection-section');
    
    if (allDaysChecked) {
        daySelectionSection.style.display = 'none';
        // Uncheck all individual day checkboxes
        document.querySelectorAll('input[name="event_day_ids[]"]').forEach(checkbox => {
            checkbox.checked = false;
        });
    } else {
        daySelectionSection.style.display = 'block';
    }
}

// Load subcategories when category changes
document.getElementById('category_id')?.addEventListener('change', function() {
    const categoryId = this.value;
    const subcategorySelect = document.getElementById('subcategory_id');
    
    // Clear existing options except "None"
    subcategorySelect.innerHTML = '<option value="">None</option>';
    
    if (categoryId) {
        // Fetch subcategories via AJAX or reload page
        // For now, we'll need to handle this in the controller or use AJAX
        // This is a simplified version - you may want to implement AJAX loading
    }
});
</script>

