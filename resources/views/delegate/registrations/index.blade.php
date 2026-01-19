@extends('delegate.layouts.app')
@section('title', 'Registrations')

@push('styles')
<style>
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
    
    .content-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        overflow: hidden;
    }
    
    .table-modern {
        margin: 0;
    }
    
    .table-modern thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem 1.25rem;
        border: none;
        white-space: nowrap;
    }
    
    .table-modern tbody td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        border-top: 1px solid #e3e6f0;
        color: #2d3748;
    }
    
    .table-modern tbody tr {
        transition: all 0.2s ease;
    }
    
    .table-modern tbody tr:hover {
        background-color: #f8f9fc;
        transform: scale(1.01);
    }
    
    .badge-modern {
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .btn-action {
        padding: 0.4rem 1rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    
    .empty-state p {
        color: #4a5568; /* Darker gray */
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <h2><i class="fas fa-list"></i>My Registrations</h2>
        <p class="text-muted mb-0">View all your ticket registrations and their details</p>
    </div>

    <div class="content-card">
        <div class="card-body p-0">
            @if($registrations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-building me-1"></i>Company Name</th>
                                <th><i class="fas fa-calendar me-1"></i>Event</th>
                                <th><i class="fas fa-users me-1"></i>Delegates</th>
                                <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                <th><i class="fas fa-clock me-1"></i>Date</th>
                                <th><i class="fas fa-cog me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $registration)
                                <tr>
                                    <td>
                                        <strong>{{ $registration->company_name }}</strong>
                                    </td>
                                    <td>
                                        {{ $registration->event->event_name ?? 'N/A' }} 
                                        <span class="text-muted">{{ $registration->event->event_year ?? '' }}</span>
                                    </td>
                                    <td>
                                        <span class="badge-modern bg-info text-white">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $registration->delegates->count() }} delegate(s)
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $status = $registration->order && $registration->order->status === 'paid' ? 'paid' : 'pending';
                                            $statusClass = $status === 'paid' ? 'success' : 'warning';
                                            $statusText = $registration->order ? ucfirst($registration->order->status) : 'Pending';
                                        @endphp
                                        <span class="badge-modern bg-{{ $statusClass }} text-white">
                                            <i class="fas fa-{{ $status === 'paid' ? 'check-circle' : 'clock' }} me-1"></i>
                                            {{ $statusText }}
                                        </span>
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                        {{ $registration->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('delegate.registrations.show', $registration->id) }}" class="btn btn-action btn-primary">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($registrations->hasPages())
                    <div class="card-footer bg-transparent border-top py-3">
                        <div class="d-flex justify-content-center">
                            {{ $registrations->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h5>No Registrations Found</h5>
                    <p>You don't have any registrations yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
