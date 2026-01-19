@extends('delegate.layouts.app')
@section('title', 'Registration Details')

@push('styles')
<style>
    .detail-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        margin-bottom: 1.5rem;
    }
    
    .detail-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        border: none;
        border-radius: 12px 12px 0 0;
    }
    
    .detail-card .card-header h4 {
        color: white;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .nav-tabs {
        border-bottom: 2px solid #e3e6f0;
        padding: 0 1.5rem;
    }
    
    .nav-tabs .nav-link {
        border: none;
        color: #718096;
        font-weight: 500;
        padding: 1rem 1.5rem;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .nav-tabs .nav-link:hover {
        color: #667eea;
        border-bottom-color: rgba(102, 126, 234, 0.3);
    }
    
    .nav-tabs .nav-link.active {
        color: #667eea;
        border-bottom-color: #667eea;
        background: transparent;
    }
    
    .info-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .info-table th {
        background: #f8f9fc;
        color: #2d3748;
        font-weight: 600;
        padding: 0.75rem 1rem;
        text-align: left;
        border: 1px solid #e3e6f0;
        width: 40%;
    }
    
    .info-table td {
        padding: 0.75rem 1rem;
        border: 1px solid #e3e6f0;
        color: #4a5568;
    }
    
    .info-table tr:hover {
        background-color: #f8f9fc;
    }
    
    .delegates-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .delegates-table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border: none;
    }
    
    .delegates-table tbody td {
        padding: 1rem;
        border-top: 1px solid #e3e6f0;
        vertical-align: middle;
    }
    
    .delegates-table tbody tr:hover {
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
    
    .section-title {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e3e6f0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .section-title i {
        color: #667eea;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="detail-card">
        <div class="card-header">
            <h4><i class="fas fa-list"></i>Registration Details</h4>
        </div>
        <div class="card-body p-0">
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-0" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#details-tab" type="button">
                        <i class="fas fa-info-circle me-1"></i>Details
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#badge-tab" type="button">
                        <i class="fas fa-id-badge me-1"></i>Badge
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#receipt-tab" type="button">
                        <i class="fas fa-receipt me-1"></i>Receipt
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#upgrades-tab" type="button">
                        <i class="fas fa-arrow-up me-1"></i>Upgrades
                    </button>
                </li>
            </ul>

            <div class="tab-content p-4">
                <!-- Details Tab -->
                <div class="tab-pane fade show active" id="details-tab">
                    <div class="row mb-4">
                        <div class="col-md-6 mb-4">
                            <h5 class="section-title"><i class="fas fa-building"></i>Company Information</h5>
                            <table class="info-table">
                                <tr>
                                    <th>Company Name:</th>
                                    <td><strong>{{ $registration->company_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Country:</th>
                                    <td>{{ $registration->company_country ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>State:</th>
                                    <td>{{ $registration->company_state ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>City:</th>
                                    <td>{{ $registration->company_city ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5 class="section-title"><i class="fas fa-user"></i>Contact Information</h5>
                            <table class="info-table">
                                <tr>
                                    <th>Name:</th>
                                    <td><strong>{{ $registration->contact->name ?? 'N/A' }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $registration->contact->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone:</th>
                                    <td>{{ $registration->contact->phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h5 class="section-title"><i class="fas fa-users"></i>Delegates</h5>
                    <div class="table-responsive">
                        <table class="delegates-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Ticket Type</th>
                                    <th>Status</th>
                                    <th>Badge</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registration->delegates as $delegate)
                                    <tr>
                                        <td><strong>{{ $delegate->full_name }}</strong></td>
                                        <td>{{ $delegate->email }}</td>
                                        <td>{{ $delegate->phone ?? 'N/A' }}</td>
                                        <td>{{ $delegate->ticket->ticketType->name ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge-modern bg-{{ $delegate->ticket && $delegate->ticket->status === 'issued' ? 'success' : 'warning' }} text-white">
                                                {{ $delegate->ticket ? ucfirst($delegate->ticket->status) : 'Pending' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($delegate->id)
                                                <a href="{{ route('delegate.badges.show', $delegate->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye me-1"></i>View Badge
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Badge Tab -->
                <div class="tab-pane fade" id="badge-tab">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-id-badge fa-5x text-primary opacity-50"></i>
                        </div>
                        <h4 class="mb-2">Badge Management</h4>
                        <p class="text-muted">View and download badges for all delegates in this registration.</p>
                        <div class="mt-4">
                            @foreach($registration->delegates as $delegate)
                                @if($delegate->id)
                                    <a href="{{ route('delegate.badges.show', $delegate->id) }}" class="btn btn-outline-primary me-2 mb-2">
                                        <i class="fas fa-id-badge me-1"></i>{{ $delegate->full_name }}'s Badge
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Receipt Tab -->
                <div class="tab-pane fade" id="receipt-tab">
                    @if($registration->order)
                        <h5 class="section-title"><i class="fas fa-receipt"></i>Order Information</h5>
                        <table class="info-table mb-4">
                            <tr>
                                <th>Order No:</th>
                                <td><strong>{{ $registration->order->order_no }}</strong></td>
                            </tr>
                            <tr>
                                <th>Total Amount:</th>
                                <td>
                                    <strong class="text-primary">
                                        {{ number_format($registration->order->total, 2) }} {{ $registration->nationality === 'International' ? 'USD' : 'INR' }}
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
                                    <span class="badge-modern bg-{{ $registration->order->status === 'paid' ? 'success' : 'warning' }} text-white">
                                        {{ ucfirst($registration->order->status) }}
                                    </span>
                                </td>
                            </tr>
                        </table>
                        @if($registration->order->receipt)
                            <a href="{{ route('delegate.receipts.show', $registration->order->receipt->id) }}" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>View Receipt
                            </a>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-receipt fa-3x text-muted mb-3 opacity-50"></i>
                            <p class="text-muted">No order found for this registration.</p>
                        </div>
                    @endif
                </div>

                <!-- Upgrades Tab -->
                <div class="tab-pane fade" id="upgrades-tab">
                    @if($upgradeRequests->count() > 0)
                        <h5 class="section-title"><i class="fas fa-arrow-up"></i>Upgrade History</h5>
                        <div class="table-responsive">
                            <table class="delegates-table">
                                <thead>
                                    <tr>
                                        <th>Request ID</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upgradeRequests as $upgrade)
                                        <tr>
                                            <td><strong>#{{ $upgrade->id }}</strong></td>
                                            <td>
                                                <strong>{{ number_format($upgrade->total_amount, 2) }} {{ $registration->nationality === 'International' ? 'USD' : 'INR' }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge-modern bg-{{ $upgrade->status === 'paid' ? 'success' : 'warning' }} text-white">
                                                    {{ ucfirst($upgrade->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $upgrade->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('delegate.upgrades.receipt', $upgrade->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-arrow-up fa-3x text-muted mb-3 opacity-50"></i>
                            <p class="text-muted mb-4">No upgrades for this registration.</p>
                        </div>
                    @endif
                    <div class="mt-4">
                        <a href="{{ route('delegate.upgrades.group.form', $registration->id) }}" class="btn btn-primary">
                            <i class="fas fa-arrow-up me-2"></i>Upgrade Tickets
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer bg-transparent border-top">
            <a href="{{ route('delegate.registrations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Registrations
            </a>
        </div>
    </div>
</div>
@endsection
