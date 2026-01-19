@extends('delegate.layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-home me-2"></i>Dashboard</h2>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-ticket-alt me-2"></i>My Tickets</h5>
                <h3 class="text-primary">{{ $tickets->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-list me-2"></i>Registrations</h5>
                <h3 class="text-info">{{ $registrations->count() }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-bell me-2"></i>Notifications</h5>
                <h3 class="text-warning">{{ $unreadNotificationsCount }}</h3>
                <small class="text-muted">Unread</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-ticket-alt me-2"></i>Recent Tickets</h5>
            </div>
            <div class="card-body">
                @if($tickets->count() > 0)
                    <div class="list-group">
                        @foreach($tickets->take(5) as $ticket)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $ticket->ticketType->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $ticket->event->event_name ?? 'Event' }}</small>
                                    </div>
                                    <span class="badge bg-{{ $ticket->status === 'issued' ? 'success' : 'warning' }}">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No tickets found.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-bell me-2"></i>Recent Notifications</h5>
            </div>
            <div class="card-body">
                @if($recentNotifications->count() > 0)
                    <div class="list-group">
                        @foreach($recentNotifications as $notification)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $notification->title }}</strong><br>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if(!$notification->is_read)
                                        <span class="badge bg-danger">New</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('delegate.notifications.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                @else
                    <p class="text-muted">No notifications.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
