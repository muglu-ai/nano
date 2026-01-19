@extends('delegate.layouts.app')
@section('title', 'Notifications')

@push('styles')
<style>
    .notification-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .notification-card.unread {
        border-left-color: #667eea;
        background: linear-gradient(to right, rgba(102, 126, 234, 0.05) 0%, white 4%);
    }
    
    .notification-card:hover {
        transform: translateX(5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25);
    }
    
    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .notification-body {
        padding: 1.25rem 1.5rem;
    }
    
    .notification-title {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 1.1rem;
    }
    
    .notification-message {
        color: #4a5568;
        line-height: 1.6;
        margin-bottom: 0.75rem;
    }
    
    .notification-meta {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: #4a5568; /* Darker for better visibility */
        font-size: 0.875rem;
    }
    
    .badge-type {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .filter-buttons {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    
    .btn-filter {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 2px solid;
    }
    
    .btn-filter.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #4a5568; /* Darker for better visibility */
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.6; /* More visible */
        color: #667eea; /* Use primary color */
    }
    
    .empty-state h5 {
        color: #2d3748; /* Dark text */
        font-weight: 600;
    }
    
    .empty-state p {
        color: #4a5568; /* Darker gray */
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-bell text-primary me-2"></i>Notifications</h2>
            <p class="text-muted mb-0">Stay updated with important announcements</p>
        </div>
        <div class="filter-buttons">
            <a href="?filter=unread" class="btn btn-filter {{ request('filter') === 'unread' ? 'active' : 'btn-outline-primary' }}">
                <i class="fas fa-envelope me-1"></i>Unread
            </a>
            <a href="?filter=read" class="btn btn-filter {{ request('filter') === 'read' ? 'active' : 'btn-outline-secondary' }}">
                <i class="fas fa-envelope-open me-1"></i>Read
            </a>
            <a href="{{ route('delegate.notifications.index') }}" class="btn btn-filter {{ !request('filter') ? 'active' : 'btn-outline-info' }}">
                <i class="fas fa-list me-1"></i>All
            </a>
            <button onclick="markAllAsRead()" class="btn btn-success">
                <i class="fas fa-check-double me-1"></i>Mark All Read
            </button>
        </div>
    </div>

    @if($notifications->count() > 0)
        <div>
            @foreach($notifications as $notification)
                <div class="notification-card {{ !$notification->is_read ? 'unread' : '' }}">
                    <div class="notification-header">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <h5 class="notification-title mb-0">{{ $notification->title }}</h5>
                                <span class="badge-type bg-{{ $notification->type === 'important' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'info') }} text-white">
                                    {{ ucfirst($notification->type) }}
                                </span>
                                @if(!$notification->is_read)
                                    <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">New</span>
                                @endif
                            </div>
                        </div>
                        @if(!$notification->is_read)
                            <button onclick="markAsRead({{ $notification->id }})" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check me-1"></i>Mark Read
                            </button>
                        @endif
                    </div>
                    <div class="notification-body">
                        <p class="notification-message">{{ $notification->message }}</p>
                        <div class="notification-meta">
                            <span><i class="fas fa-clock me-1"></i>{{ $notification->created_at->format('M d, Y h:i A') }}</span>
                            <span><i class="fas fa-history me-1"></i>{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($notifications->hasPages())
            <div class="mt-4 d-flex justify-content-center">
                {{ $notifications->links() }}
            </div>
        @endif
    @else
        <div class="empty-state">
            <i class="fas fa-bell-slash"></i>
            <h5>No Notifications</h5>
            <p>You're all caught up! No notifications to display.</p>
        </div>
    @endif
</div>

<script>
function markAsRead(id) {
    fetch(`/delegate/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('Failed to mark notification as read.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function markAllAsRead() {
    if(!confirm('Mark all notifications as read?')) return;
    
    fetch('{{ route("delegate.notifications.read-all") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            location.reload();
        } else {
            alert('Failed to mark all as read.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>
@endsection
