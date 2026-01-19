@extends('delegate.layouts.app')
@section('title', 'Upgrade History')

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
    
    .history-table {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }
    
    .history-table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem 1.25rem;
        border: none;
    }
    
    .history-table tbody td {
        padding: 1rem 1.25rem;
        border-top: 1px solid #e3e6f0;
        vertical-align: middle;
        color: #2d3748;
    }
    
    .history-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .history-table tbody tr:hover {
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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1"><i class="fas fa-history text-primary me-2"></i>Upgrade History</h2>
            <p class="text-muted mb-0">View all your ticket upgrade requests and their status</p>
        </div>
        <a href="{{ route('delegate.upgrades.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Upgrades
        </a>
    </div>

    <div class="content-card">
        <div class="card-body p-0">
            @if($upgrades->count() > 0)
                <div class="table-responsive">
                    <table class="history-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>Request ID</th>
                                <th><i class="fas fa-tag me-1"></i>Type</th>
                                <th><i class="fas fa-money-bill me-1"></i>Amount</th>
                                <th><i class="fas fa-info-circle me-1"></i>Status</th>
                                <th><i class="fas fa-calendar me-1"></i>Date</th>
                                <th><i class="fas fa-cog me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($upgrades as $upgrade)
                                <tr>
                                    <td><strong>#{{ $upgrade->id }}</strong></td>
                                    <td>
                                        <span class="badge-modern bg-info text-white">
                                            {{ ucfirst($upgrade->request_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($upgrade->total_amount, 2) }} {{ $upgrade->registration->nationality === 'International' ? 'USD' : 'INR' }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge-modern bg-{{ $upgrade->status === 'paid' ? 'success' : 'warning' }} text-white">
                                            <i class="fas fa-{{ $upgrade->status === 'paid' ? 'check-circle' : 'clock' }} me-1"></i>
                                            {{ ucfirst($upgrade->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                        {{ $upgrade->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $upgrade->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <a href="{{ route('delegate.upgrades.receipt', $upgrade->id) }}" class="btn btn-action btn-primary">
                                            <i class="fas fa-eye me-1"></i>View Receipt
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($upgrades->hasPages())
                    <div class="card-footer bg-transparent border-top py-3">
                        <div class="d-flex justify-content-center">
                            {{ $upgrades->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <h5>No Upgrade History</h5>
                    <p>You haven't made any upgrade requests yet.</p>
                    <a href="{{ route('delegate.upgrades.index') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-arrow-up me-2"></i>Upgrade Tickets
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
