@extends('delegate.layouts.app')
@section('title', 'Ticket Upgrades')

@push('styles')
<style>
    .upgrade-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        transition: all 0.3s ease;
        height: 100%;
        margin-bottom: 1.5rem;
    }
    
    .upgrade-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25);
    }
    
    .upgrade-card .card-header {
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        border-bottom: 2px solid #e3e6f0;
        padding: 1.25rem 1.5rem;
        border-radius: 12px 12px 0 0;
    }
    
    .upgrade-card .card-header h5 {
        color: #2d3748;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .upgrade-card .card-header h5 i {
        color: #667eea;
    }
    
    .ticket-list-item {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #e3e6f0;
        transition: all 0.2s ease;
    }
    
    .ticket-list-item:hover {
        background-color: #f8f9fc;
        padding-left: 1.5rem;
    }
    
    .ticket-list-item:last-child {
        border-bottom: none;
    }
    
    .ticket-info h6 {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }
    
    .ticket-info .text-muted {
        font-size: 0.875rem;
        color: #4a5568 !important; /* Darker for better visibility */
    }
    
    .btn-upgrade {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-upgrade:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .alert-pending {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: none;
        border-left: 4px solid #ffc107;
        border-radius: 8px;
        padding: 1.25rem;
    }
    
    .alert-pending h5 {
        color: #856404;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
    
    .pending-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .pending-list li {
        padding: 0.75rem;
        background: white;
        border-radius: 6px;
        margin-bottom: 0.5rem;
        border: 1px solid #e3e6f0;
    }
    
    .pending-list li:last-child {
        margin-bottom: 0;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #4a5568; /* Darker for better visibility */
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.6; /* More visible */
        color: #667eea; /* Use primary color */
    }
    
    .empty-state p {
        color: #4a5568; /* Darker text */
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-arrow-up text-primary me-2"></i>Ticket Upgrades</h2>
            <p class="text-muted mb-0">Upgrade your tickets to higher categories</p>
        </div>
        <a href="{{ route('delegate.upgrades.history') }}" class="btn btn-outline-secondary">
            <i class="fas fa-history me-2"></i>Upgrade History
        </a>
    </div>

    @if($pendingUpgrades->count() > 0)
    <div class="alert-pending mb-4">
        <h5><i class="fas fa-exclamation-triangle me-2"></i>Pending Upgrades</h5>
        <p class="mb-3">You have <strong>{{ $pendingUpgrades->count() }}</strong> pending upgrade request(s) awaiting payment.</p>
        <ul class="pending-list">
            @foreach($pendingUpgrades as $upgrade)
                <li>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>Upgrade Request #{{ $upgrade->id }}</strong>
                            <br>
                            <small class="text-muted">
                                Amount: <strong>{{ number_format($upgrade->total_amount, 2) }} {{ $upgrade->registration->nationality === 'International' ? 'USD' : 'INR' }}</strong>
                            </small>
                        </div>
                        <a href="{{ route('delegate.upgrades.receipt', $upgrade->id) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-eye me-1"></i>View & Pay
                        </a>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="upgrade-card">
                <div class="card-header">
                    <h5><i class="fas fa-ticket-alt"></i>Individual Ticket Upgrades</h5>
                </div>
                <div class="card-body p-0">
                    @if($tickets->count() > 0)
                        <div>
                            @foreach($tickets as $ticket)
                                <div class="ticket-list-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="ticket-info flex-grow-1">
                                            <h6>{{ $ticket->ticketType->name ?? 'N/A' }}</h6>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-tag me-1"></i>
                                                {{ $ticket->ticketType->category->name ?? 'Category' }}
                                            </p>
                                        </div>
                                        <a href="{{ route('delegate.upgrades.individual.form', $ticket->id) }}" class="btn btn-upgrade">
                                            <i class="fas fa-arrow-up me-1"></i>Upgrade
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-ticket-alt"></i>
                            <p>No tickets available for upgrade.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="upgrade-card">
                <div class="card-header">
                    <h5><i class="fas fa-users"></i>Group Registration Upgrades</h5>
                </div>
                <div class="card-body p-0">
                    @if($registrations->count() > 0)
                        <div>
                            @foreach($registrations as $registration)
                                <div class="ticket-list-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="ticket-info flex-grow-1">
                                            <h6>{{ $registration->company_name }}</h6>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $registration->delegates->count() }} delegate(s)
                                            </p>
                                        </div>
                                        <a href="{{ route('delegate.upgrades.group.form', $registration->id) }}" class="btn btn-upgrade">
                                            <i class="fas fa-arrow-up me-1"></i>Upgrade
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>No group registrations available for upgrade.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
