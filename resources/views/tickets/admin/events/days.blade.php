@extends('layouts.dashboard')

@section('title', 'Event Days - ' . $event->event_name)

@section('content')
<style>
    .days-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .days-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
        margin-bottom: 2rem;
    }
    
    .days-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.75rem 2rem;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .days-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .days-card-body {
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
    
    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control:focus {
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
    
    .badge-date {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .btn-edit {
        background: #667eea;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-edit:hover {
        background: #5568d3;
        color: white;
        transform: translateY(-2px);
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
    
    .edit-form {
        display: none;
    }
    
    .edit-form.active {
        display: block;
    }
    
    .view-mode {
        display: block;
    }
    
    .view-mode.hidden {
        display: none;
    }
</style>

<div class="days-container">
    <div class="days-card">
        <div class="days-card-header">
            <h4>
                <i class="fas fa-calendar"></i>
                Event Days - {{ $event->event_name }}
            </h4>
            <a href="{{ route('admin.tickets.events.setup', $event->id) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Back to Setup
            </a>
        </div>
        <div class="days-card-body">
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    {{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Generate All Days Section -->
            @if($event->start_date && $event->end_date)
                <div class="form-section" style="background: linear-gradient(135deg, #e6f3ff 0%, #f0f8ff 100%); border: 2px solid #667eea;">
                    <h5 class="form-section-title" style="border-bottom-color: #667eea;">
                        <i class="fas fa-magic"></i>
                        Quick Generate All Days
                    </h5>
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <p class="mb-2">
                                <strong>Event Date Range:</strong>
                                <span class="badge bg-primary ms-2">
                                    {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                </span>
                                <span class="mx-2">to</span>
                                <span class="badge bg-primary">
                                    {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                                </span>
                            </p>
                            <p class="text-muted mb-0 small">
                                <i class="fas fa-info-circle me-1"></i>
                                This will automatically create event days from the start date to end date. 
                                Days that already exist will be skipped.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <form action="{{ route('admin.tickets.events.days.generate-all', $event->id) }}" method="POST" 
                                  onsubmit="return confirm('This will generate all days from {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}. Existing days will be skipped. Continue?');">
                                @csrf
                                <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 0.75rem 2rem;">
                                    <i class="fas fa-magic me-2"></i>Generate All Days
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Event dates not set:</strong> Please set the event start date and end date in the event settings to use the "Generate All Days" feature.
                </div>
            @endif

            <!-- Add New Day Form -->
            <div class="form-section">
                <h5 class="form-section-title">
                    <i class="fas fa-plus-circle"></i>
                    Add New Event Day
                </h5>
                <form action="{{ route('admin.tickets.events.days.store', $event->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Day Label</label>
                                <input type="text" name="label" class="form-control" 
                                       value="{{ old('label') }}" 
                                       placeholder="e.g., Day 1, Day 2, VIP Day" required>
                                @error('label')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" 
                                       value="{{ old('date') }}" required>
                                @error('date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" 
                                       value="{{ old('sort_order', 0) }}" 
                                       placeholder="0" min="0">
                                @error('sort_order')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Existing Days List -->
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Label</th>
                            <th>Date</th>
                            <th>Day of Week</th>
                            <th>Sort Order</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($days as $day)
                            <tr id="day-row-{{ $day->id }}">
                                <td>
                                    <strong>{{ $day->label }}</strong>
                                </td>
                                <td>
                                    <span class="badge-date">
                                        {{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}
                                    </span>
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($day->date)->format('l') }}
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $day->sort_order }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn-edit" onclick="editDay({{ $day->id }}, '{{ $day->label }}', '{{ $day->date }}', {{ $day->sort_order }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.tickets.events.days.delete', [$event->id, $day->id]) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this event day?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Edit Form (Hidden by default) -->
                                    <div class="edit-form mt-3" id="edit-form-{{ $day->id }}">
                                        <form action="{{ route('admin.tickets.events.days.update', [$event->id, $day->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input type="text" name="label" class="form-control form-control-sm" 
                                                           id="edit-label-{{ $day->id }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="date" name="date" class="form-control form-control-sm" 
                                                           id="edit-date-{{ $day->id }}" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="number" name="sort_order" class="form-control form-control-sm" 
                                                           id="edit-sort-{{ $day->id }}" min="0">
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <button type="button" class="btn btn-secondary btn-sm" 
                                                        onclick="cancelEdit({{ $day->id }})">
                                                    <i class="fas fa-times"></i> Cancel
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <i class="fas fa-calendar-times"></i>
                                    <h5>No Event Days</h5>
                                    <p>Add event days to organize your event schedule.</p>
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
    function editDay(id, label, date, sortOrder) {
        // Hide all other edit forms
        document.querySelectorAll('.edit-form').forEach(form => {
            form.classList.remove('active');
        });
        
        // Show edit form for this day
        const editForm = document.getElementById('edit-form-' + id);
        editForm.classList.add('active');
        
        // Populate form fields
        document.getElementById('edit-label-' + id).value = label;
        document.getElementById('edit-date-' + id).value = date;
        document.getElementById('edit-sort-' + id).value = sortOrder;
        
        // Scroll to form
        editForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    function cancelEdit(id) {
        document.getElementById('edit-form-' + id).classList.remove('active');
    }
</script>
@endsection

