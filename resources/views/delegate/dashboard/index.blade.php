@extends('delegate.layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stat-card.primary::before {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stat-card.info::before {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .stat-card.warning::before {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .stat-icon.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .stat-icon.info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }
    
    .stat-icon.warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
        line-height: 1;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #4a5568; /* Darker for better visibility */
        margin-top: 0.5rem;
        font-weight: 500;
    }
    
    .content-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        margin-bottom: 1.5rem;
    }
    
    .content-card .card-header {
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        border-bottom: 2px solid #e3e6f0;
        padding: 1.25rem 1.5rem;
        border-radius: 12px 12px 0 0;
    }
    
    .content-card .card-header h5 {
        color: #2d3748;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .content-card .card-header h5 i {
        color: #667eea;
    }
    
    .ticket-item, .notification-item {
        padding: 1rem;
        border-bottom: 1px solid #e3e6f0;
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }
    
    .ticket-item:hover, .notification-item:hover {
        background-color: #f8f9fc;
        border-left-color: #667eea;
        padding-left: 1.25rem;
    }
    
    .ticket-item:last-child, .notification-item:last-child {
        border-bottom: none;
    }
    
    .status-badge {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #4a5568; /* Darker for better visibility */
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.6; /* Slightly more visible */
        color: #667eea; /* Use primary color */
    }
    
    .empty-state p {
        color: #4a5568; /* Darker text */
        font-weight: 500;
    }
    
    .page-header {
        margin-bottom: 2rem;
    }
    
    .page-header h2 {
        color: #2d3748;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .page-header h2 i {
        color: #667eea;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <h2><i class="fas fa-home"></i>Dashboard</h2>
        <p class="text-muted mb-0">Welcome back! Here's an overview of your account.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card primary">
                <div class="stat-icon primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-value text-primary">{{ $tickets->count() }}</div>
                <div class="stat-label">My Tickets</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card info">
                <div class="stat-icon info">
                    <i class="fas fa-list"></i>
                </div>
                <div class="stat-value text-info">{{ $registrations->count() }}</div>
                <div class="stat-label">Registrations</div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="stat-card warning">
                <div class="stat-icon warning">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="stat-value text-warning">{{ $unreadNotificationsCount }}</div>
                <div class="stat-label">Unread Notifications</div>
            </div>
        </div>
    </div>

    <!-- Content Cards -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <h5><i class="fas fa-ticket-alt"></i>Recent Tickets</h5>
                </div>
                <div class="card-body p-0">
                    @if($tickets->count() > 0)
                        <div>
                            @foreach($tickets->take(5) as $ticket)
                                <div class="ticket-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold">{{ $ticket->ticketType->name ?? 'N/A' }}</h6>
                                            <p class="text-muted mb-1 small">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                {{ $ticket->event->event_name ?? 'Event' }} {{ $ticket->event->event_year ?? '' }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $ticket->created_at->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <span class="status-badge bg-{{ $ticket->status === 'issued' ? 'success' : 'warning' }} text-white">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($tickets->count() > 5)
                            <div class="card-footer bg-transparent border-top text-center">
                                <a href="{{ route('delegate.registrations.index') }}" class="btn btn-sm btn-outline-primary">
                                    View All Tickets <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="empty-state">
                            <i class="fas fa-ticket-alt"></i>
                            <p>No tickets found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="content-card">
                <div class="card-header">
                    <h5><i class="fas fa-bell"></i>Recent Notifications</h5>
                </div>
                <div class="card-body p-0">
                    @if($recentNotifications->count() > 0)
                        <div>
                            @foreach($recentNotifications as $notification)
                                <div class="notification-item {{ !$notification->is_read ? 'bg-light' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <h6 class="mb-0 fw-bold">{{ $notification->title }}</h6>
                                                @if(!$notification->is_read)
                                                    <span class="badge bg-danger rounded-pill" style="font-size: 0.65rem;">New</span>
                                                @endif
                                                <span class="badge bg-{{ $notification->type === 'important' ? 'danger' : ($notification->type === 'warning' ? 'warning' : 'info') }} rounded-pill" style="font-size: 0.65rem;">
                                                    {{ ucfirst($notification->type) }}
                                                </span>
                                            </div>
                                            <p class="text-muted mb-2 small">{{ Str::limit($notification->message, 80) }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer bg-transparent border-top text-center">
                            <a href="{{ route('delegate.notifications.index') }}" class="btn btn-sm btn-outline-primary">
                                View All Notifications <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-bell-slash"></i>
                            <p>No notifications.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="content-card">
                <div class="card-header">
                    <h5><i class="fas fa-bolt"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('delegate.upgrades.index') }}" class="btn btn-outline-primary w-100 d-flex flex-column align-items-center p-3" style="min-height: 100px;">
                                <i class="fas fa-arrow-up fa-2x mb-2"></i>
                                <span>Upgrade Ticket</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('delegate.receipts.index') }}" class="btn btn-outline-info w-100 d-flex flex-column align-items-center p-3" style="min-height: 100px;">
                                <i class="fas fa-receipt fa-2x mb-2"></i>
                                <span>View Receipts</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('delegate.registrations.index') }}" class="btn btn-outline-success w-100 d-flex flex-column align-items-center p-3" style="min-height: 100px;">
                                <i class="fas fa-list fa-2x mb-2"></i>
                                <span>My Registrations</span>
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <a href="{{ route('delegate.notifications.index') }}" class="btn btn-outline-warning w-100 d-flex flex-column align-items-center p-3" style="min-height: 100px;">
                                <i class="fas fa-bell fa-2x mb-2"></i>
                                <span>Notifications</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
