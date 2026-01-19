@extends('delegate.layouts.app')
@section('title', 'Receipts')

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
        margin-bottom: 2rem;
        overflow: hidden;
    }
    
    .content-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        border: none;
    }
    
    .content-card .card-header h5 {
        color: white;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .table-modern {
        margin: 0;
    }
    
    .table-modern thead th {
        background: #f8f9fc;
        color: #2d3748;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem 1.25rem;
        border-bottom: 2px solid #e3e6f0;
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
        margin: 0 0.25rem;
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
        <h2><i class="fas fa-receipt"></i>Receipts</h2>
        <p class="text-muted mb-0">View and download your payment receipts</p>
    </div>

    <div class="content-card">
        <div class="card-header">
            <h5><i class="fas fa-file-invoice"></i>Regular Receipts</h5>
        </div>
        <div class="card-body p-0">
            @if($receipts->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>Receipt No</th>
                                <th><i class="fas fa-shopping-cart me-1"></i>Order No</th>
                                <th><i class="fas fa-tag me-1"></i>Type</th>
                                <th><i class="fas fa-money-bill me-1"></i>Amount</th>
                                <th><i class="fas fa-calendar me-1"></i>Date</th>
                                <th><i class="fas fa-cog me-1"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receipts as $receipt)
                                <tr>
                                    <td><strong>{{ $receipt->receipt_no ?? 'N/A' }}</strong></td>
                                    <td>{{ $receipt->order->order_no ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge-modern bg-info text-white">
                                            {{ ucfirst(str_replace('_', ' ', $receipt->type)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($receipt->order->total ?? 0, 2) }} {{ $receipt->order->registration->nationality === 'International' ? 'USD' : 'INR' }}</strong>
                                    </td>
                                    <td>
                                        <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                        {{ $receipt->created_at->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('delegate.receipts.show', $receipt->id) }}" class="btn btn-action btn-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="{{ route('delegate.receipts.download', $receipt->id) }}" class="btn btn-action btn-outline-primary">
                                            <i class="fas fa-download me-1"></i>Download
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($receipts->hasPages())
                    <div class="card-footer bg-transparent border-top py-3">
                        <div class="d-flex justify-content-center">
                            {{ $receipts->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-receipt"></i>
                    <h5>No Receipts Found</h5>
                    <p>You don't have any receipts yet.</p>
                </div>
            @endif
        </div>
    </div>

    @if($upgradeRequests->count() > 0)
    <div class="content-card">
        <div class="card-header" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
            <h5><i class="fas fa-arrow-up"></i>Upgrade Receipts</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-modern mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-hashtag me-1"></i>Request ID</th>
                            <th><i class="fas fa-shopping-cart me-1"></i>Order No</th>
                            <th><i class="fas fa-money-bill me-1"></i>Amount</th>
                            <th><i class="fas fa-info-circle me-1"></i>Status</th>
                            <th><i class="fas fa-calendar me-1"></i>Date</th>
                            <th><i class="fas fa-cog me-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upgradeRequests as $upgrade)
                            <tr>
                                <td><strong>#{{ $upgrade->id }}</strong></td>
                                <td>{{ $upgrade->upgradeOrder->order_no ?? 'Pending' }}</td>
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
                                </td>
                                <td>
                                    <a href="{{ route('delegate.upgrades.receipt', $upgrade->id) }}" class="btn btn-action btn-primary">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
