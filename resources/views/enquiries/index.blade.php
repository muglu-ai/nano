@extends('layouts.dashboard')
@section('title', 'Enquiry Management')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-envelope me-2"></i>Enquiry Management</h2>
        <div>
            <span class="badge bg-primary">{{ $enquiries->total() }} Total Enquiries</span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('enquiries.index') }}" id="filterForm">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_to" class="form-select">
                            <option value="">All Users</option>
                            <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('assigned_to') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Interest Type</label>
                        <select name="interest_type" class="form-select">
                            <option value="">All Interests</option>
                            @foreach($interestTypes as $key => $label)
                                <option value="{{ $key }}" {{ request('interest_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-2"></i>Apply Filters</button>
                        <a href="{{ route('enquiries.index') }}" class="btn btn-secondary"><i class="fas fa-redo me-2"></i>Reset</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Enquiries Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Organisation</th>
                            <th>Contact</th>
                            <th>Location</th>
                            <th>Interests</th>
                            <th>Status</th>
                            <th>Assigned To</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enquiries as $index => $enquiry)
                            <tr>
                                <td>{{ ($enquiries->currentPage() - 1) * $enquiries->perPage() + $index + 1 }}</td>
                                <td>
                                    <strong>{{ $enquiry->full_name }}</strong><br>
                                    <small class="text-muted">{{ $enquiry->designation }}</small>
                                </td>
                                <td>{{ $enquiry->organisation }}</td>
                                <td>
                                    <div><i class="fas fa-envelope me-1"></i>{{ $enquiry->email }}</div>
                                    <div><i class="fas fa-phone me-1"></i>{{ $enquiry->phone_full ?? $enquiry->phone_number }}</div>
                                </td>
                                <td>
                                    {{ $enquiry->city }}, {{ $enquiry->country }}
                                </td>
                                <td>
                                    @if($enquiry->interests->count() > 0)
                                        @foreach($enquiry->interests->take(2) as $interest)
                                            <span class="badge bg-info me-1">{{ $interestTypes[$interest->interest_type] ?? $interest->interest_type }}</span>
                                        @endforeach
                                        @if($enquiry->interests->count() > 2)
                                            <span class="badge bg-secondary">+{{ $enquiry->interests->count() - 2 }} more</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'new' => 'secondary',
                                            'contacted' => 'info',
                                            'qualified' => 'warning',
                                            'converted' => 'success',
                                            'closed' => 'dark'
                                        ];
                                        $color = $statusColors[$enquiry->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $color }}">{{ ucfirst($enquiry->status) }}</span>
                                    @if($enquiry->prospect_level)
                                        <br><small class="text-muted">{{ ucfirst($enquiry->prospect_level) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($enquiry->assignedTo)
                                        {{ $enquiry->assignedTo->name }}
                                    @else
                                        <span class="text-muted">Unassigned</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $enquiry->created_at->format('M d, Y') }}<br>
                                    <small class="text-muted">{{ $enquiry->created_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('enquiries.show', $enquiry->id) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-info" 
                                                onclick="quickAssign({{ $enquiry->id }})"
                                                title="Quick Assign">
                                            <i class="fas fa-user-plus"></i>
                                        </button>
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                onclick="quickStatus({{ $enquiry->id }}, '{{ $enquiry->status }}')"
                                                title="Change Status">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No enquiries found.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $enquiries->firstItem() }} to {{ $enquiries->lastItem() }} of {{ $enquiries->total() }} enquiries
                </div>
                <div>
                    {{ $enquiries->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Assign Modal -->
<div class="modal fade" id="quickAssignModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Assign Enquiry</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickAssignForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Assign To</label>
                        <select name="assigned_to_user_id" class="form-select">
                            <option value="">Unassign</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Status Modal -->
<div class="modal fade" id="quickStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickStatusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prospect Level</label>
                        <select name="prospect_level" class="form-select">
                            <option value="">None</option>
                            <option value="hot">Hot</option>
                            <option value="warm">Warm</option>
                            <option value="cold">Cold</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea name="status_comment" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function quickAssign(enquiryId) {
        const form = document.getElementById('quickAssignForm');
        form.action = `/admin/enquiries/${enquiryId}/assign`;
        const modal = new bootstrap.Modal(document.getElementById('quickAssignModal'));
        modal.show();
    }

    function quickStatus(enquiryId, currentStatus) {
        const form = document.getElementById('quickStatusForm');
        form.action = `/admin/enquiries/${enquiryId}/status`;
        form.querySelector('select[name="status"]').value = currentStatus;
        const modal = new bootstrap.Modal(document.getElementById('quickStatusModal'));
        modal.show();
    }

    // Handle form submissions
    document.getElementById('quickAssignForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || response.ok) {
                Swal.fire('Success', 'Enquiry assigned successfully!', 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to assign enquiry', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'An error occurred', 'error');
        });
    });

    document.getElementById('quickStatusForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success || response.ok) {
                Swal.fire('Success', 'Status updated successfully!', 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', data.message || 'Failed to update status', 'error');
            }
        })
        .catch(error => {
            Swal.fire('Error', 'An error occurred', 'error');
        });
    });
</script>
@endpush
@endsection
