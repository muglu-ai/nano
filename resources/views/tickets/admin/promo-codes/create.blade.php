@extends('layouts.dashboard')

@section('title', 'Create Promocode - ' . $event->event_name)

@section('content')
<style>
    .promo-form-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .promo-form-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
    }
    
    .promo-form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.75rem 2rem;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .promo-form-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .promo-form-body {
        padding: 2rem;
    }
    
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e9ecef;
    }
    
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #667eea;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        display: block;
    }
    
    .form-control, .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .form-check {
        margin-bottom: 0.75rem;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        color: white;
    }
    
    .btn-back {
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-back:hover {
        background: #cbd5e0;
        color: #2d3748;
        transform: translateY(-2px);
    }
    
    .help-text {
        font-size: 0.875rem;
        color: #718096;
        margin-top: 0.25rem;
    }
</style>

<div class="promo-form-container">
    <div class="promo-form-card">
        <div class="promo-form-header">
            <h4>
                <i class="fas fa-ticket-alt"></i>
                Create New Promocode - {{ $event->event_name }}
            </h4>
            <a href="{{ route('admin.tickets.events.promo-codes', $event->id) }}" class="btn-back" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>
        <div class="promo-form-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.tickets.events.promo-codes.store', $event->id) }}" method="POST">
                @csrf
                
                <!-- Basic Information -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i>
                        Basic Information
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Promocode *</label>
                            <input type="text" name="code" class="form-control" value="{{ old('code') }}" required 
                                   placeholder="e.g., SUMMER2024" maxlength="100">
                            <div class="help-text">Enter a unique promocode (will be converted to uppercase)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Organization Name</label>
                            <input type="text" name="organization_name" class="form-control" value="{{ old('organization_name') }}" 
                                   placeholder="e.g., Tech Corp Inc" maxlength="255">
                            <div class="help-text">Optional: Bind this promocode to an organization for tracking</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Discount Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                                <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Discount Value *</label>
                            <input type="number" name="value" class="form-control" value="{{ old('value') }}" 
                                   step="0.01" min="0" required placeholder="e.g., 10 or 500">
                            <div class="help-text" id="value-help">
                                @if(old('type') == 'percentage')
                                    Enter percentage (0-100)
                                @else
                                    Enter fixed amount
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Validity Period -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Validity Period
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Valid From</label>
                            <input type="datetime-local" name="valid_from" class="form-control" value="{{ old('valid_from') }}">
                            <div class="help-text">Leave empty for no start date restriction</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Valid To</label>
                            <input type="datetime-local" name="valid_to" class="form-control" value="{{ old('valid_to') }}">
                            <div class="help-text">Leave empty for no expiry date</div>
                        </div>
                    </div>
                </div>

                <!-- Usage Limits -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-users"></i>
                        Usage Limits
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Max Uses (Global)</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="unlimited_uses" id="unlimited_uses" 
                                       {{ old('unlimited_uses') ? 'checked' : '' }}>
                                <label class="form-check-label" for="unlimited_uses">Unlimited</label>
                            </div>
                            <input type="number" name="max_uses" class="form-control" value="{{ old('max_uses') }}" 
                                   min="1" id="max_uses_input" placeholder="e.g., 100">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Max Uses Per Contact</label>
                            <input type="number" name="max_uses_per_contact" class="form-control" value="{{ old('max_uses_per_contact') }}" 
                                   min="1" placeholder="e.g., 1">
                            <div class="help-text">Leave empty for unlimited uses per contact</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Min Order Amount</label>
                            <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount') }}" 
                                   step="0.01" min="0" placeholder="e.g., 1000">
                            <div class="help-text">Minimum base amount required to apply this promocode</div>
                        </div>
                    </div>
                </div>

                <!-- Restrictions -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-filter"></i>
                        Restrictions (Leave empty for "All")
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Registration Categories</label>
                            <select name="applicable_registration_category_ids[]" class="form-select" multiple size="4">
                                @foreach($registrationCategories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ in_array($category->id, old('applicable_registration_category_ids', [])) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="help-text">Hold Ctrl/Cmd to select multiple. Leave empty for all categories.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ticket Categories</label>
                            <select name="applicable_ticket_category_ids[]" class="form-select" multiple size="4">
                                @foreach($ticketCategories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ in_array($category->id, old('applicable_ticket_category_ids', [])) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="help-text">Hold Ctrl/Cmd to select multiple. Leave empty for all categories.</div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Event Days</label>
                            <select name="applicable_event_day_ids[]" class="form-select" multiple size="4">
                                @foreach($eventDays as $day)
                                    <option value="{{ $day->id }}" 
                                            {{ in_array($day->id, old('applicable_event_day_ids', [])) ? 'selected' : '' }}>
                                        {{ $day->label }} ({{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="help-text">Hold Ctrl/Cmd to select multiple. Leave empty for all days.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Delegate Limits</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" name="min_delegates" class="form-control" value="{{ old('min_delegates', 1) }}" 
                                           min="1" placeholder="Min">
                                </div>
                                <div class="col-6">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="unlimited_delegates" id="unlimited_delegates" 
                                               {{ old('unlimited_delegates') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="unlimited_delegates">Unlimited Max</label>
                                    </div>
                                    <input type="number" name="max_delegates" class="form-control" value="{{ old('max_delegates') }}" 
                                           min="1" id="max_delegates_input" placeholder="Max">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-sticky-note"></i>
                        Additional Information
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" 
                                  placeholder="Optional notes about this promocode...">{{ old('description') }}</textarea>
                    </div>
                </div>

                <div class="form-actions mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save me-2"></i>Create Promocode
                    </button>
                    <a href="{{ route('admin.tickets.events.promo-codes', $event->id) }}" class="btn btn-back">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Handle unlimited uses checkbox
    document.getElementById('unlimited_uses')?.addEventListener('change', function() {
        document.getElementById('max_uses_input').disabled = this.checked;
        if (this.checked) {
            document.getElementById('max_uses_input').value = '';
        }
    });
    
    // Handle unlimited delegates checkbox
    document.getElementById('unlimited_delegates')?.addEventListener('change', function() {
        document.getElementById('max_delegates_input').disabled = this.checked;
        if (this.checked) {
            document.getElementById('max_delegates_input').value = '';
        }
    });
    
    // Update help text based on discount type
    document.querySelector('select[name="type"]')?.addEventListener('change', function() {
        const helpText = document.getElementById('value-help');
        if (this.value === 'percentage') {
            helpText.textContent = 'Enter percentage (0-100)';
        } else {
            helpText.textContent = 'Enter fixed amount';
        }
    });
</script>
@endsection
