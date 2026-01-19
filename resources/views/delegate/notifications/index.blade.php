@extends('delegate.layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-bell me-2"></i>Notifications</h2>
    <div>
        <a href="?filter=unread" class="btn btn-sm btn-outline-primary me-2">Unread</a>
        <a href="?filter=read" class="btn btn-sm btn-outline-secondary me-2">Read</a>
        <a href="{{ route('delegate.notifications.index') }}" class="btn btn-sm btn-outline-info me-2">All</a>
        <button onclick="markAllAsRead()" class="btn btn-sm btn-success">Mark All Read</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($notifications->count() > 0)
            <div class="list-group">
                @foreach($notifications as $notification)
                    <div class="list-group-item {{ !$notification->is_read ? 'bg-light' : '' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 me-2">{{ $notification->title }}</h6>
                                    <span class="badge bg-{{ $notification->type === 'important' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'info') }}">
                                        {{ ucfirst($notification->type) }}
                                    </span>
                                    @if(!$notification->is_read)
                                        <span class="badge bg-danger ms-2">New</span>
                                    @endif
                                </div>
                                <p class="mb-1">{{ $notification->message }}</p>
                                <small class="text-muted">{{ $notification->created_at->format('M d, Y h:i A') }}</small>
                            </div>
                            @if(!$notification->is_read)
                                <button onclick="markAsRead({{ $notification->id }})" class="btn btn-sm btn-outline-primary">
                                    Mark Read
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-3">
                {{ $notifications->links() }}
            </div>
        @else
            <p class="text-muted text-center py-4">No notifications found.</p>
        @endif
    </div>
</div>

<script>
function markAsRead(id) {
    fetch(`/delegate/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}

function markAllAsRead() {
    fetch('{{ route("delegate.notifications.read-all") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        }
    });
}
</script>
@endsection
