@extends('layouts.dashboard')
@section('title', 'Delegate Notifications')

@section('content')
<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 h4 font-weight-bolder">Delegate Notifications</h3>
        <a href="{{ route('admin.delegate-notifications.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create Notification
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="info" {{ request('type') === 'info' ? 'selected' : '' }}>Info</option>
                            <option value="warning" {{ request('type') === 'warning' ? 'selected' : '' }}>Warning</option>
                            <option value="important" {{ request('type') === 'important' ? 'selected' : '' }}>Important</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="is_read" class="form-select">
                            <option value="">All</option>
                            <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Unread</option>
                            <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Read</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="{{ route('admin.delegate-notifications.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </form>

            @if($notifications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Target</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->id }}</td>
                                    <td>{{ $notification->title }}</td>
                                    <td><span class="badge bg-{{ $notification->type === 'important' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'info') }}">{{ ucfirst($notification->type) }}</span></td>
                                    <td>
                                        @if($notification->delegate)
                                            Delegate: {{ $notification->delegate->full_name }}
                                        @elseif($notification->contact)
                                            Contact: {{ $notification->contact->name }}
                                        @else
                                            All
                                        @endif
                                    </td>
                                    <td><span class="badge bg-{{ $notification->is_read ? 'success' : 'warning' }}">{{ $notification->is_read ? 'Read' : 'Unread' }}</span></td>
                                    <td>{{ $notification->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <button onclick="sendEmail({{ $notification->id }})" class="btn btn-sm btn-outline-primary">Send Email</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $notifications->links() }}
            @else
                <p class="text-muted text-center py-4">No notifications found.</p>
            @endif
        </div>
    </div>
</div>

<script>
function sendEmail(id) {
    fetch(`/admin/delegate-notifications/${id}/send`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert('Email sent successfully!');
        } else {
            alert('Failed to send email: ' + data.message);
        }
    });
}
</script>
@endsection
