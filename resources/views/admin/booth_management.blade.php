@extends('layouts.dashboard')
@section('title', 'Booth Management')
@section('content')

{{-- Add csrf token --}}
<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
    /* Clean and simple design */
    .card {
        border: 1px solid #e3e6f0;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
    }
    
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        color: #5a5c69;
    }
    
    .card-header h5 {
        color: #5a5c69;
        font-weight: 600;
    }
    
    /* Search section */
    .search-section {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        padding: 1.5rem;
    }
    
    .search-input {
        border: 2px solid #d1d3e2;
        border-radius: 0.35rem;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .search-input:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .search-btn {
        background-color: #4e73df;
        border-color: #4e73df;
        color: white;
        border-radius: 0.35rem;
        padding: 0.75rem 1.5rem;
    }
    
    .search-btn:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
        color: white;
    }
    
    .clear-btn {
        background-color: #e74a3b;
        border-color: #e74a3b;
        color: white;
        border-radius: 0.35rem;
        padding: 0.75rem 1.5rem;
    }
    
    .clear-btn:hover {
        background-color: #c0392b;
        border-color: #c0392b;
        color: white;
    }
    
    /* Table styling */
    .table th {
        background-color: #5a5c69;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
        border: none;
    }
    
    .table td {
        padding: 0.75rem;
        vertical-align: middle;
        border-top: 1px solid #e3e6f0;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
    
    /* Edit input styling */
    .edit-input {
        border: 2px solid #d1d3e2;
        border-radius: 0.35rem;
        padding: 0.5rem;
        font-size: 0.9rem;
        width: 100%;
    }
    
    .edit-input:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        outline: none;
    }
    
    /* Bulk action buttons */
    .bulk-actions {
        background-color: #f8f9fc;
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }
    
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    .btn-danger {
        background-color: #e74a3b;
        border-color: #e74a3b;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
    
    /* Checkbox styling */
    .form-check-input {
        cursor: pointer;
    }
    
    /* Pagination */
    .pagination {
        margin: 0;
    }
    
    .page-link {
        color: #4e73df;
        border: 1px solid #d1d3e2;
        padding: 0.5rem 0.75rem;
        margin: 0 2px;
        border-radius: 0.35rem;
    }
    
    .page-item.active .page-link {
        background-color: #4e73df;
        border-color: #4e73df;
        color: white;
    }
    
    .page-link:hover {
        color: #2e59d9;
        background-color: #f8f9fc;
        border-color: #d1d3e2;
    }
    
    /* Alert messages */
    .alert {
        border-radius: 0.35rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .selected-count {
        background-color: #4e73df;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
    }
</style>

<div class="container-fluid py-2">
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <!-- Card header -->
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">@yield('title')</h5>
                            <p class="text-sm mb-0">
                                Manage booth numbers and zones for all applications.
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="d-flex gap-2 align-items-center">
                                
                                <span class="badge bg-primary">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $applications->total() }} Applications
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Search Section -->
                <div class="search-section">
                    <form method="GET" action="{{ route('booth.management') }}" id="searchForm">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control search-input" 
                                           name="search" 
                                           placeholder="Search by company name, booth number, or application ID..." 
                                           value="{{ request('search') }}">
                                    <button class="btn search-btn" type="submit">
                                        <i class="fas fa-search me-1"></i> Search
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="zone" class="form-control search-input" onchange="this.form.submit()">
                                    <option value="">All Zones</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone }}" {{ request('zone') == $zone ? 'selected' : '' }}>
                                            {{ $zone }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="per_page" class="form-control search-input" onchange="this.form.submit()">
                                    <option value="25" {{ request('per_page') == 25 || !request('per_page') ? 'selected' : '' }}>25 per page</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                                </select>
                            </div>
                            <div class="col-md-2 text-end">
                                @if(request('search') || request('zone'))
                                    <a href="{{ route('booth.management') }}" class="btn clear-btn">
                                        <i class="fas fa-times me-1"></i> Clear
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Bulk Actions -->
                <div class="bulk-actions">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="selected-count" id="selectedCount">0 Selected</span>
                        </div>
                        <div>
                            <button class="btn btn-success btn-sm me-2" onclick="saveBulkChanges()">
                                <i class="fas fa-save me-1"></i> Save Bulk Changes
                            </button>
                            <button class="btn btn-secondary btn-sm" onclick="clearSelection()">
                                <i class="fas fa-times me-1"></i> Clear Selection
                            </button>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                            </th>
                            <th>Company Name</th>
                            <th>Application ID</th>
                            <th>Booth Number</th>
                            <th>Zone</th>
                            <th>Hall No</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($applications as $app)
                            <tr data-id="{{ $app->id }}">
                                <td>
                                    <input type="checkbox" class="form-check-input row-checkbox" 
                                           data-id="{{ $app->id }}"
                                           onchange="updateSelectedCount()">
                                </td>
                                <td>{{ $app->company_name }}</td>
                                <td>{{ $app->application_id }}</td>
                                <td>
                                    <input type="text" 
                                           class="edit-input" 
                                           name="stallNumber_{{ $app->id }}" 
                                           value="{{ $app->stallNumber ?? '' }}"
                                           data-field="stallNumber"
                                           data-id="{{ $app->id }}"
                                           placeholder="Enter booth number">
                                </td>
                                <td>
                                    <input type="text" 
                                           class="edit-input" 
                                           name="zone_{{ $app->id }}" 
                                           value="{{ $app->zone ?? '' }}"
                                           data-field="zone"
                                           data-id="{{ $app->id }}"
                                           placeholder="Enter zone">
                                </td>
                                <td>
                                    <input type="text" 
                                           class="edit-input" 
                                           name="hallNo_{{ $app->id }}" 
                                           value="{{ $app->hallNo ?? '' }}"
                                           data-field="hallNo"
                                           data-id="{{ $app->id }}"
                                           placeholder="Enter hall no">
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-sm" 
                                            onclick="saveRowChanges({{ $app->id }})"
                                            title="Save changes">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No applications found</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="results-info">
                            Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} 
                            of {{ $applications->total() }} results
                        </div>
                        <div>
                            {{ $applications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Track changes for bulk update
let rowChanges = {};

// Select All checkbox
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

// Update selected count
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    const count = checkboxes.length;
    document.getElementById('selectedCount').textContent = count + ' Selected';
}

// Save single row changes
function saveRowChanges(id) {
    const row = document.querySelector(`tr[data-id="${id}"]`);
    const inputs = row.querySelectorAll('.edit-input');
    
    const data = {
        id: id,
    };
    
    inputs.forEach(input => {
        const field = input.getAttribute('data-field');
        const value = input.value.trim();
        data[field] = value;
    });
    
    // Send AJAX request
    fetch(`{{ route('booth.update', ':id') }}`.replace(':id', id), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            // Remove from changes tracking
            delete rowChanges[id];
        } else {
            showAlert('danger', data.message || 'Failed to update');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while saving');
    });
}

// Save bulk changes
function saveBulkChanges() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    
    if (checkboxes.length === 0) {
        showAlert('warning', 'Please select at least one row');
        return;
    }
    
    const updates = [];
    
    checkboxes.forEach(checkbox => {
        const id = checkbox.getAttribute('data-id');
        const row = document.querySelector(`tr[data-id="${id}"]`);
        const inputs = row.querySelectorAll('.edit-input');
        
        const data = {
            id: parseInt(id)
        };
        
        inputs.forEach(input => {
            const field = input.getAttribute('data-field');
            const value = input.value.trim();
            data[field] = value;
        });
        
        updates.push(data);
    });
    
    // Send bulk update request
    fetch('{{ route("booth.bulkUpdate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ updates: updates })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            // Clear all selections
            clearSelection();
            // Reload page after 1 second
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showAlert('danger', data.message || 'Failed to update');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'An error occurred while saving');
    });
}

// Clear selection
function clearSelection() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAll').checked = false;
    updateSelectedCount();
}

// Show alert
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert before the card
    const card = document.querySelector('.card');
    card.insertAdjacentElement('beforebegin', alertDiv);
    
    // Remove after 3 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>

@endsection

