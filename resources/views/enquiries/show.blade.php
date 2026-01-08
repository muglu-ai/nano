@extends('layouts.dashboard')
@section('title', 'Enquiry Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-envelope me-2"></i>Enquiry Details</h2>
        <div>
            <a href="{{ route('enquiries.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to List
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Main Details -->
        <div class="col-md-8">
            <!-- Enquiry Information -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Enquiry Information</h5>
                    <div>
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
                        <span class="badge bg-{{ $color }} fs-6">{{ ucfirst($enquiry->status) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Name:</strong> {{ $enquiry->full_name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Designation:</strong> {{ $enquiry->designation }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Organisation:</strong> {{ $enquiry->organisation }}
                        </div>
                        <div class="col-md-6">
                            <strong>Sector:</strong> {{ $enquiry->sector ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Email:</strong> <a href="mailto:{{ $enquiry->email }}">{{ $enquiry->email }}</a>
                        </div>
                        <div class="col-md-6">
                            <strong>Phone:</strong> {{ $enquiry->phone_full ?? $enquiry->phone_number }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>City:</strong> {{ $enquiry->city }}
                        </div>
                        <div class="col-md-4">
                            <strong>State:</strong> {{ $enquiry->state ?? 'N/A' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Country:</strong> {{ $enquiry->country }}
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Referral Source:</strong> {{ $enquiry->referral_source ?? 'N/A' }}
                        </div>
                        <div class="col-md-6">
                            <strong>Submitted:</strong> {{ $enquiry->created_at->format('M d, Y h:i A') }}
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Interests:</strong>
                        <div class="mt-2">
                            @if($enquiry->interests->count() > 0)
                                @foreach($enquiry->interests as $interest)
                                    <span class="badge bg-info me-1">
                                        {{ \App\Models\EnquiryInterest::getInterestTypes()[$interest->interest_type] ?? $interest->interest_type }}
                                        @if($interest->interest_type === 'other' && $interest->interest_other_detail)
                                            ({{ $interest->interest_other_detail }})
                                        @endif
                                    </span>
                                @endforeach
                            @else
                                <span class="text-muted">No interests selected</span>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3">
                        <strong>Comments:</strong>
                        <p class="mt-2">{{ $enquiry->comments }}</p>
                    </div>
                </div>
            </div>

            <!-- Followups -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Followups</h5>
                </div>
                <div class="card-body">
                    @if($enquiry->followups->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date/Time</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Comment</th>
                                        <th>Assigned To</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($enquiry->followups->sortByDesc('created_at') as $followup)
                                        <tr>
                                            <td>
                                                @if($followup->followup_date)
                                                    {{ \Carbon\Carbon::parse($followup->followup_date)->format('M d, Y') }}
                                                    @if($followup->followup_time)
                                                        {{ \Carbon\Carbon::parse($followup->followup_time)->format('h:i A') }}
                                                    @endif
                                                @else
                                                    {{ $followup->created_at->format('M d, Y h:i A') }}
                                                @endif
                                            </td>
                                            <td>{{ $followup->followup_type ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $followup->followup_status === 'completed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($followup->followup_status ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td>{{ Str::limit($followup->followup_comment, 50) }}</td>
                                            <td>{{ $followup->assigned_to_name ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No followups yet.</p>
                    @endif

                    <button type="button" class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#addFollowupModal">
                        <i class="fas fa-plus me-2"></i>Add Followup
                    </button>
                </div>
            </div>

            <!-- Notes -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h5>
                </div>
                <div class="card-body">
                    @if($enquiry->notes->count() > 0)
                        <div class="list-group">
                            @foreach($enquiry->notes->sortByDesc('created_at') as $note)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between">
                                        <strong>{{ $note->created_by_name ?? 'System' }}</strong>
                                        <small class="text-muted">{{ $note->created_at->format('M d, Y h:i A') }}</small>
                                    </div>
                                    <p class="mb-0 mt-2">{{ $note->note }}</p>
                                    <small class="text-muted">Type: {{ ucfirst($note->note_type) }}</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No notes yet.</p>
                    @endif

                    <button type="button" class="btn btn-primary btn-sm mt-3" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                        <i class="fas fa-plus me-2"></i>Add Note
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('enquiries.assign', $enquiry->id) }}" method="POST" class="mb-3">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Assign To</label>
                            <select name="assigned_to_user_id" class="form-select">
                                <option value="">Unassign</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $enquiry->assigned_to_user_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Assignment</button>
                    </form>

                    <form action="{{ route('enquiries.status', $enquiry->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ $enquiry->status == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prospect Level</label>
                            <select name="prospect_level" class="form-select">
                                <option value="">None</option>
                                <option value="hot" {{ $enquiry->prospect_level == 'hot' ? 'selected' : '' }}>Hot</option>
                                <option value="warm" {{ $enquiry->prospect_level == 'warm' ? 'selected' : '' }}>Warm</option>
                                <option value="cold" {{ $enquiry->prospect_level == 'cold' ? 'selected' : '' }}>Cold</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status Comment</label>
                            <textarea name="status_comment" class="form-control" rows="3">{{ $enquiry->status_comment }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Update Status</button>
                    </form>
                </div>
            </div>

            <!-- Metadata -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info me-2"></i>Metadata</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Enquiry ID:</strong> #{{ $enquiry->id }}
                    </div>
                    <div class="mb-2">
                        <strong>Event:</strong> {{ $enquiry->event ? $enquiry->event->event_name : 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <strong>IP Address:</strong> {{ $enquiry->ip_address ?? 'N/A' }}
                    </div>
                    <div class="mb-2">
                        <strong>Source URL:</strong> 
                        @if($enquiry->source_url)
                            <a href="{{ $enquiry->source_url }}" target="_blank">View</a>
                        @else
                            N/A
                        @endif
                    </div>
                    <div class="mb-2">
                        <strong>Created:</strong> {{ $enquiry->created_at->format('M d, Y h:i A') }}
                    </div>
                    <div class="mb-2">
                        <strong>Last Updated:</strong> {{ $enquiry->updated_at->format('M d, Y h:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Followup Modal -->
<div class="modal fade" id="addFollowupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Followup</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('enquiries.followup', $enquiry->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Followup Type</label>
                        <select name="followup_type" class="form-select">
                            <option value="">Select Type</option>
                            <option value="call">Call</option>
                            <option value="email">Email</option>
                            <option value="meeting">Meeting</option>
                            <option value="note">Note</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="followup_status" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" name="followup_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Time</label>
                            <input type="time" name="followup_time" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Comment</label>
                        <textarea name="followup_comment" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Assign To</label>
                        <select name="assigned_to_user_id" class="form-select">
                            <option value="">None</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
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
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Followup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('enquiries.note', $enquiry->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Note Type</label>
                        <select name="note_type" class="form-select">
                            <option value="general">General</option>
                            <option value="internal">Internal</option>
                            <option value="customer_response">Customer Response</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Note</label>
                        <textarea name="note" class="form-control" rows="5" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Note</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection




