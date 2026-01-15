@extends('layouts.dashboard')

@section('title', 'Ticket Rules - ' . $event->event_name)

@section('content')
<style>
    .rules-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .rules-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
        margin-bottom: 2rem;
    }
    
    .rules-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.75rem 2rem;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .rules-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .rules-card-body {
        padding: 2rem;
    }
    
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
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
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        color: white;
    }
    
    .btn-back {
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
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
    
    .table-wrapper {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead {
        background: #f8f9fa;
    }
    
    .table thead th {
        font-weight: 600;
        color: #4a5568;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .badge-reg-category {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-ticket-type {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-subcategory {
        background: #e2e8f0;
        color: #4a5568;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-days {
        background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .btn-delete {
        background: #e53e3e;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .btn-delete:hover {
        background: #c53030;
        color: white;
        transform: translateY(-2px);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #718096;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .text-danger {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }
    
    .day-checkboxes {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .day-checkbox-item {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
</style>

<div class="rules-container">
    <div class="rules-card">
        <div class="rules-card-header">
            <h4>
                <i class="fas fa-link"></i>
                Ticket Rules - {{ $event->event_name }}
            </h4>
            <a href="{{ route('admin.tickets.events.setup', $event->id) }}" class="btn-back" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-arrow-left"></i>
                Back to Setup
            </a>
        </div>
        <div class="rules-card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

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

            <!-- Add New Rule Form -->
            <div class="form-section">
                <h5 class="form-section-title">
                    <i class="fas fa-plus-circle"></i>
                    Add New Ticket Rule
                </h5>
                <p class="text-muted mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    Rules define which ticket types are available for each registration category. You can optionally restrict access to specific days.
                </p>
                <form action="{{ route('admin.tickets.events.rules.store', $event->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Registration Category <span class="text-danger">*</span></label>
                                <select name="registration_category_id" class="form-select" required id="reg_category_id">
                                    <option value="">Select Registration Category</option>
                                    @foreach($registrationCategories as $regCategory)
                                        <option value="{{ $regCategory->id }}" {{ old('registration_category_id') == $regCategory->id ? 'selected' : '' }}>
                                            {{ $regCategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('registration_category_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Ticket Type <span class="text-danger">*</span></label>
                                <select name="ticket_type_id" class="form-select" required id="ticket_type_id">
                                    <option value="">Select Ticket Type</option>
                                    @foreach($ticketTypes as $ticketType)
                                        <option value="{{ $ticketType->id }}" 
                                                data-category="{{ $ticketType->category_id }}"
                                                data-subcategory="{{ $ticketType->subcategory_id }}"
                                                {{ old('ticket_type_id') == $ticketType->id ? 'selected' : '' }}>
                                            {{ $ticketType->name }} ({{ $ticketType->category->name }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('ticket_type_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Subcategory (Optional)</label>
                                <select name="subcategory_id" class="form-select" id="subcategory_id">
                                    <option value="">None</option>
                                </select>
                                @error('subcategory_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    @if($eventDays->count() > 0)
                        <div class="row">
                            <div class="col-md-12">
                                <label class="form-label">Allowed Days (Optional - Leave empty for all days)</label>
                                <div class="day-checkboxes">
                                    @foreach($eventDays as $day)
                                        <div class="day-checkbox-item">
                                            <input type="checkbox" name="allowed_days_json[]" 
                                                   value="{{ $day->id }}" 
                                                   id="day_{{ $day->id }}"
                                                   {{ in_array($day->id, old('allowed_days_json', [])) ? 'checked' : '' }}>
                                            <label for="day_{{ $day->id }}" class="form-check-label">
                                                {{ $day->label }} ({{ \Carbon\Carbon::parse($day->date)->format('M d') }})
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="text-muted">If no days are selected, the ticket will be available for all days.</small>
                                @error('allowed_days_json')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    @endif
                    
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Create Rule
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Existing Rules List -->
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Registration Category</th>
                            <th>Ticket Type</th>
                            <th>Subcategory</th>
                            <th>Allowed Days</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rules as $rule)
                            <tr>
                                <td>
                                    <span class="badge-reg-category">
                                        {{ $rule->registrationCategory->name }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge-ticket-type">
                                        {{ $rule->ticketType->name }}
                                    </span>
                                    <br>
                                    <small class="text-muted">{{ $rule->ticketType->category->name }}</small>
                                </td>
                                <td>
                                    @if($rule->subcategory)
                                        <span class="badge-subcategory">{{ $rule->subcategory->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $allowedDays = $rule->getAllowedDays();
                                    @endphp
                                    @if(empty($allowedDays))
                                        <span class="badge-days">All 3 Days</span>
                                    @else
                                        @php
                                            $dayLabels = $eventDays->whereIn('id', $allowedDays)->pluck('label')->toArray();
                                        @endphp
                                        @if(count($dayLabels) > 0)
                                            <span class="badge-days">{{ implode(', ', $dayLabels) }}</span>
                                        @else
                                            <span class="text-muted">No days</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.tickets.events.rules.delete', [$event->id, $rule->id]) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this rule?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="fas fa-link"></i>
                                    <h5>No Ticket Rules</h5>
                                    <p>Create rules to define which ticket types are available for each registration category.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    const ticketTypesData = @json($ticketTypes->mapWithKeys(function($type) {
        return [$type->id => [
            'subcategory_id' => $type->subcategory_id,
            'subcategory' => $type->subcategory ? [
                'id' => $type->subcategory->id,
                'name' => $type->subcategory->name
            ] : null
        ]];
    }));
    
    // Update subcategory options when ticket type changes
    document.getElementById('ticket_type_id')?.addEventListener('change', function() {
        const ticketTypeId = this.value;
        const subcategorySelect = document.getElementById('subcategory_id');
        
        // Clear existing options
        subcategorySelect.innerHTML = '<option value="">None</option>';
        
        if (ticketTypeId && ticketTypesData[ticketTypeId]) {
            const ticketTypeData = ticketTypesData[ticketTypeId];
            if (ticketTypeData.subcategory) {
                const option = document.createElement('option');
                option.value = ticketTypeData.subcategory.id;
                option.textContent = ticketTypeData.subcategory.name;
                subcategorySelect.appendChild(option);
            }
        }
    });
    
    // Trigger change on page load if there's a selected value
    document.addEventListener('DOMContentLoaded', function() {
        const ticketTypeSelect = document.getElementById('ticket_type_id');
        if (ticketTypeSelect && ticketTypeSelect.value) {
            ticketTypeSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection

