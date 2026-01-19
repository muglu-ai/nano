@extends('delegate.layouts.app')
@section('title', 'Ticket Upgrades')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-arrow-up me-2"></i>Ticket Upgrades</h2>
    <a href="{{ route('delegate.upgrades.history') }}" class="btn btn-outline-secondary">
        <i class="fas fa-history me-2"></i>Upgrade History
    </a>
</div>

@if($pendingUpgrades->count() > 0)
<div class="alert alert-warning">
    <h5><i class="fas fa-exclamation-triangle me-2"></i>Pending Upgrades</h5>
    <p>You have {{ $pendingUpgrades->count() }} pending upgrade request(s).</p>
    <ul>
        @foreach($pendingUpgrades as $upgrade)
            <li>
                <a href="{{ route('delegate.upgrades.receipt', $upgrade->id) }}">
                    Upgrade Request #{{ $upgrade->id }} - {{ $upgrade->total_amount }} {{ $upgrade->registration->nationality === 'International' ? 'USD' : 'INR' }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endif

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-ticket-alt me-2"></i>Individual Ticket Upgrades</h5>
            </div>
            <div class="card-body">
                @if($tickets->count() > 0)
                    <div class="list-group">
                        @foreach($tickets as $ticket)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $ticket->ticketType->name ?? 'N/A' }}</strong><br>
                                        <small class="text-muted">{{ $ticket->ticketType->category->name ?? 'Category' }}</small>
                                    </div>
                                    <a href="{{ route('delegate.upgrades.individual.form', $ticket->id) }}" class="btn btn-sm btn-primary">
                                        Upgrade
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No tickets available for upgrade.</p>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users me-2"></i>Group Registration Upgrades</h5>
            </div>
            <div class="card-body">
                @if($registrations->count() > 0)
                    <div class="list-group">
                        @foreach($registrations as $registration)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $registration->company_name }}</strong><br>
                                        <small class="text-muted">{{ $registration->delegates->count() }} delegate(s)</small>
                                    </div>
                                    <a href="{{ route('delegate.upgrades.group.form', $registration->id) }}" class="btn btn-sm btn-primary">
                                        Upgrade
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No group registrations available for upgrade.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
