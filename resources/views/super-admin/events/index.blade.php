@extends('layouts.app')

@section('title', 'Manage Events - Super Admin')

@section('content')
<style>
    .events-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .events-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
    }
    
    .events-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .events-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .btn-create {
        background: white;
        color: #667eea;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        color: #667eea;
    }
    
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
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
    }
    
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .event-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .btn-action {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.875rem;
        margin-right: 0.25rem;
        transition: all 0.2s;
    }
    
    .btn-edit {
        background: #667eea;
        color: white;
        border: none;
    }
    
    .btn-edit:hover {
        background: #5568d3;
        color: white;
        transform: translateY(-1px);
    }
    
    .btn-delete {
        background: #e53e3e;
        color: white;
        border: none;
    }
    
    .btn-delete:hover {
        background: #c53030;
        color: white;
        transform: translateY(-1px);
    }
    
    .badge-year {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 600;
    }
    
    .badge-status {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: capitalize;
    }
    
    .badge-upcoming {
        background: #48bb78;
        color: white;
    }
    
    .badge-ongoing {
        background: #ed8936;
        color: white;
    }
    
    .badge-over {
        background: #a0aec0;
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #a0aec0;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
</style>

<div class="events-container">
    <div class="events-card">
        <div class="events-card-header">
            <div class="d-flex align-items-center gap-3">
                <h4>
                    <i class="fas fa-calendar-alt"></i>
                    Manage Events
                </h4>
                <a href="{{ route('super-admin.event-config') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-cog me-2"></i>Event Config
                </a>
            </div>
            <a href="{{ route('super-admin.events.create') }}" class="btn-create">
                <i class="fas fa-plus me-2"></i>Create New Event
            </a>
        </div>
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Event Name</th>
                            <th>Year</th>
                            <th>Location</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            <tr>
                                <td>
                                    @if($event->event_image && file_exists(public_path($event->event_image)))
                                        <img src="{{ asset($event->event_image) }}" alt="{{ $event->event_name }}" class="event-image">
                                    @else
                                        <div class="event-image bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $event->event_name }}</strong>
                                    @if($event->slug)
                                        <br><small class="text-muted">/{{ $event->slug }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-year">{{ $event->event_year }}</span>
                                </td>
                                <td>{{ $event->event_location }}</td>
                                <td>
                                    @if($event->start_date)
                                        {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($event->end_date)
                                        {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $status = $event->status ?? 'upcoming';
                                        $statusClass = 'badge-' . $status;
                                    @endphp
                                    <span class="badge-status {{ $statusClass }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('super-admin.events.edit', $event->id) }}" class="btn btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('super-admin.events.delete', $event->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this event? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times"></i>
                                        <h5>No events found</h5>
                                        <p>Get started by creating your first event.</p>
                                        <a href="{{ route('super-admin.events.create') }}" class="btn btn-create mt-3">
                                            <i class="fas fa-plus me-2"></i>Create New Event
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
