@extends('layouts.dashboard')

@section('title', 'Promocodes - ' . $event->event_name)

@section('content')
<style>
    .promo-codes-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .promo-codes-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
        margin-bottom: 2rem;
    }
    
    .promo-codes-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.75rem 2rem;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .promo-codes-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .promo-codes-card-body {
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
        margin-bottom: 1rem;
    }
    
    .btn-back:hover {
        background: #cbd5e0;
        color: #2d3748;
        transform: translateY(-2px);
    }
    
    .filter-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e9ecef;
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
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge-active {
        background: #d4edda;
        color: #155724;
    }
    
    .badge-inactive {
        background: #f8d7da;
        color: #721c24;
    }
    
    .badge-percentage {
        background: #cfe2ff;
        color: #084298;
    }
    
    .badge-fixed {
        background: #fff3cd;
        color: #856404;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 6px;
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-edit {
        background: #667eea;
        color: white;
    }
    
    .btn-edit:hover {
        background: #5568d3;
        color: white;
    }
    
    .btn-delete {
        background: #dc3545;
        color: white;
    }
    
    .btn-delete:hover {
        background: #c82333;
        color: white;
    }
    
    .btn-toggle {
        background: #28a745;
        color: white;
    }
    
    .btn-toggle:hover {
        background: #218838;
        color: white;
    }
    
    .btn-analytics {
        background: #17a2b8;
        color: white;
    }
    
    .btn-analytics:hover {
        background: #138496;
        color: white;
    }
</style>

<div class="promo-codes-container">
    <a href="{{ route('admin.tickets.events.setup', $event->id) }}" class="btn-back">
        ← Back to Event Setup
    </a>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="promo-codes-card">
        <div class="promo-codes-card-header">
            <h4>
                <i class="fas fa-ticket-alt"></i>
                Promocodes Management
            </h4>
            <a href="{{ route('admin.tickets.events.promo-codes.create', $event->id) }}" class="btn-create">
                <i class="fas fa-plus"></i> Create Promocode
            </a>
        </div>
        
        <div class="promo-codes-card-body">
            <!-- Filters -->
            <div class="filter-section">
                <form method="GET" action="{{ route('admin.tickets.events.promo-codes', $event->id) }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Organization</label>
                        <select name="organization" class="form-select">
                            <option value="">All Organizations</option>
                            @foreach($organizations as $org)
                                <option value="{{ $org }}" {{ request('organization') == $org ? 'selected' : '' }}>
                                    {{ $org }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">Filter</button>
                        <a href="{{ route('admin.tickets.events.promo-codes', $event->id) }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>

            <!-- Promocodes Table -->
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Organization</th>
                            <th>Type</th>
                            <th>Value</th>
                            <th>Validity</th>
                            <th>Usage</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promoCodes as $promoCode)
                            <tr>
                                <td><strong>{{ $promoCode->code }}</strong></td>
                                <td>{{ $promoCode->organization_name ?? '-' }}</td>
                                <td>
                                    <span class="badge {{ $promoCode->type === 'percentage' ? 'badge-percentage' : 'badge-fixed' }}">
                                        {{ ucfirst($promoCode->type) }}
                                    </span>
                                </td>
                                <td>
                                    @if($promoCode->type === 'percentage')
                                        {{ number_format($promoCode->value, 0) }}%
                                    @else
                                        ₹{{ number_format($promoCode->value, 2) }}
                                    @endif
                                </td>
                                <td>
                                    @if($promoCode->valid_from && $promoCode->valid_to)
                                        {{ \Carbon\Carbon::parse($promoCode->valid_from)->format('M d, Y') }} - 
                                        {{ \Carbon\Carbon::parse($promoCode->valid_to)->format('M d, Y') }}
                                    @elseif($promoCode->valid_from)
                                        From {{ \Carbon\Carbon::parse($promoCode->valid_from)->format('M d, Y') }}
                                    @elseif($promoCode->valid_to)
                                        Until {{ \Carbon\Carbon::parse($promoCode->valid_to)->format('M d, Y') }}
                                    @else
                                        No expiry
                                    @endif
                                </td>
                                <td>
                                    {{ $promoCode->getUsedCount() }}
                                    @if($promoCode->max_uses)
                                        / {{ $promoCode->max_uses }}
                                    @else
                                        / ∞
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $promoCode->is_active ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $promoCode->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.tickets.events.promo-codes.analytics', [$event->id, $promoCode->id]) }}" 
                                           class="btn-sm btn-analytics" title="Analytics">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <a href="{{ route('admin.tickets.events.promo-codes.edit', [$event->id, $promoCode->id]) }}" 
                                           class="btn-sm btn-edit" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.tickets.events.promo-codes.toggle-status', [$event->id, $promoCode->id]) }}" 
                                              method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn-sm btn-toggle" title="{{ $promoCode->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $promoCode->is_active ? 'toggle-on' : 'toggle-off' }}"></i>
                                            </button>
                                        </form>
                                        @if($promoCode->getUsedCount() == 0)
                                            <form action="{{ route('admin.tickets.events.promo-codes.delete', [$event->id, $promoCode->id]) }}" 
                                                  method="POST" style="display: inline;"
                                                  onsubmit="return confirm('Are you sure you want to delete this promocode?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-sm btn-delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <p class="text-muted mb-0">No promocodes found. <a href="{{ route('admin.tickets.events.promo-codes.create', $event->id) }}">Create one</a></p>
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
