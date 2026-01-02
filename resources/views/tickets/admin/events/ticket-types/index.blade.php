@extends('layouts.dashboard')

@section('title', 'Ticket Types - ' . $event->event_name)

@section('content')
<style>
    .ticket-types-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .ticket-types-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
        margin-bottom: 2rem;
    }
    
    .ticket-types-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.75rem 2rem;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .ticket-types-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .ticket-types-card-body {
        padding: 2rem;
    }
    
    .btn-create {
        background: white;
        color: #667eea;
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-create:hover {
        background: rgba(255,255,255,0.2);
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-back {
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-back:hover {
        background: #cbd5e0;
        color: #2d3748;
        transform: translateY(-2px);
    }
    
    .table-wrapper {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e9ecef;
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
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .badge-active {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-inactive {
        background: #e2e8f0;
        color: #718096;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-all-days {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .btn-edit {
        background: #667eea;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-edit:hover {
        background: #5568d3;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-delete {
        background: #e53e3e;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .btn-delete:hover {
        background: #c53030;
        color: white;
        transform: translateY(-2px);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #718096;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .price-badge {
        background: linear-gradient(135deg, #f6ad55 0%, #ed8936 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
    }
</style>

<div class="ticket-types-container">
    <div class="ticket-types-card">
        <div class="ticket-types-card-header">
            <h4>
                <i class="fas fa-ticket-alt"></i>
                Ticket Types - {{ $event->event_name }}
            </h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.tickets.events.ticket-types.create', $event->id) }}" class="btn-create">
                    <i class="fas fa-plus"></i>
                    Create New
                </a>
                <a href="{{ route('admin.tickets.events.setup', $event->id) }}" class="btn-back" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                    <i class="fas fa-arrow-left"></i>
                    Back to Setup
                </a>
            </div>
        </div>
        <div class="ticket-types-card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Ticket Types List -->
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Subcategory</th>
                            <th>Price</th>
                            <th>Days Access</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ticketTypes as $ticketType)
                            <tr>
                                <td>
                                    <strong>{{ $ticketType->name }}</strong>
                                    @if($ticketType->description)
                                        <br><small class="text-muted">{{ Str::limit($ticketType->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $ticketType->category->name }}</span>
                                </td>
                                <td>
                                    @if($ticketType->subcategory)
                                        <span class="badge bg-secondary">{{ $ticketType->subcategory->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $currentPrice = $ticketType->getCurrentPrice();
                                        $isEarlyBird = $ticketType->isEarlyBirdActive();
                                    @endphp
                                    <span class="price-badge">
                                        ₹{{ number_format($currentPrice, 2) }}
                                    </span>
                                    @if($isEarlyBird && $ticketType->early_bird_price)
                                        <br><small class="text-muted">Early Bird: ₹{{ number_format($ticketType->early_bird_price, 2) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @if($ticketType->all_days_access)
                                        <span class="badge-all-days">
                                            <i class="fas fa-check-circle me-1"></i>All Days
                                        </span>
                                    @else
                                        @php
                                            $dayCount = $ticketType->eventDays->count();
                                        @endphp
                                        @if($dayCount > 0)
                                            <span class="badge bg-primary">{{ $dayCount }} Day{{ $dayCount > 1 ? 's' : '' }}</span>
                                        @else
                                            <span class="text-muted">No days</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($ticketType->is_active)
                                        <span class="badge-active">Active</span>
                                    @else
                                        <span class="badge-inactive">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.tickets.events.ticket-types.edit', [$event->id, $ticketType->id]) }}" 
                                           class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.tickets.events.ticket-types.delete', [$event->id, $ticketType->id]) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this ticket type?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <i class="fas fa-ticket-alt"></i>
                                    <h5>No Ticket Types</h5>
                                    <p>Create ticket types to start selling tickets.</p>
                                    <a href="{{ route('admin.tickets.events.ticket-types.create', $event->id) }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus me-2"></i>Create First Ticket Type
                                    </a>
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

