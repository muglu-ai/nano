@extends('delegate.layouts.app')
@section('title', 'Upgrade Receipt')

@push('styles')
<style>
    .receipt-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        overflow: hidden;
    }
    
    .receipt-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }
    
    .receipt-card .card-header h4 {
        color: white;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-alert {
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 2rem;
        border-left: 4px solid;
    }
    
    .status-alert.success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-left-color: #28a745;
        color: #155724;
    }
    
    .status-alert.warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border-left-color: #ffc107;
        color: #856404;
    }
    
    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
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
    
    .upgrade-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .upgrade-table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border: none;
    }
    
    .upgrade-table tbody td {
        padding: 1rem;
        border-top: 1px solid #e3e6f0;
        vertical-align: middle;
    }
    
    .upgrade-table tbody tr:hover {
        background-color: #f8f9fc;
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
    
    .btn-payment {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }
    
    .btn-payment:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="receipt-card">
        <div class="card-header">
            <h4><i class="fas fa-receipt"></i>Upgrade Receipt</h4>
        </div>
        <div class="card-body p-4">
            <div class="status-alert {{ $upgradeRequest->status === 'paid' ? 'success' : 'warning' }}">
                <div class="d-flex align-items-center">
                    <i class="fas fa-{{ $upgradeRequest->status === 'paid' ? 'check-circle' : 'exclamation-triangle' }} fa-2x me-3"></i>
                    <div>
                        <h5 class="mb-1">Status: {{ strtoupper($upgradeRequest->status) }}</h5>
                        <p class="mb-0">
                            @if($upgradeRequest->status === 'paid')
                                Your upgrade has been successfully processed and paid.
                            @else
                                This upgrade request is pending payment. Please proceed to payment to complete the upgrade.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-4">
                    <h5 class="section-title"><i class="fas fa-info-circle"></i>Upgrade Details</h5>
                    <table class="info-table">
                        <tr>
                            <th>Request ID:</th>
                            <td><strong>#{{ $upgradeRequest->id }}</strong></td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($upgradeRequest->request_type) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Order No:</th>
                            <td>
                                <strong>{{ $upgradeRequest->upgradeOrder->order_no ?? 'Pending' }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>
                                <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                {{ $upgradeRequest->created_at->format('M d, Y h:i A') }}
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6 mb-4">
                    <h5 class="section-title"><i class="fas fa-calculator"></i>Price Breakdown</h5>
                    <table class="info-table">
                        @php
                            $currency = $upgradeRequest->registration->nationality === 'International' ? 'USD' : 'INR';
                        @endphp
                        <tr>
                            <th>Price Difference:</th>
                            <td>{{ number_format($upgradeRequest->price_difference, 2) }} {{ $currency }}</td>
                        </tr>
                        <tr>
                            <th>GST:</th>
                            <td>{{ number_format($upgradeRequest->gst_amount, 2) }} {{ $currency }}</td>
                        </tr>
                        <tr>
                            <th>Processing Charge:</th>
                            <td>{{ number_format($upgradeRequest->processing_charge_amount, 2) }} {{ $currency }}</td>
                        </tr>
                        <tr style="background: #f8f9fc;">
                            <th><strong>Total:</strong></th>
                            <td>
                                <strong class="text-primary" style="font-size: 1.2rem;">
                                    {{ number_format($upgradeRequest->total_amount, 2) }} {{ $currency }}
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <h5 class="section-title"><i class="fas fa-ticket-alt"></i>Ticket Upgrades</h5>
            <div class="table-responsive">
                <table class="upgrade-table">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Old Type</th>
                            <th>New Type</th>
                            <th>Price Difference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upgradeRequest->upgrade_data_json['tickets'] ?? [] as $ticketData)
                            <tr>
                                <td><strong>Ticket #{{ $ticketData['ticket_id'] ?? 'N/A' }}</strong></td>
                                <td>{{ $ticketData['old_ticket_type_name'] ?? 'N/A' }}</td>
                                <td>
                                    <strong class="text-success">{{ $ticketData['new_ticket_type_name'] ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <strong>{{ number_format($ticketData['price_difference'] ?? 0, 2) }} {{ $currency }}</strong>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($upgradeRequest->status === 'pending')
                <div class="mt-4 text-center">
                    <form method="POST" action="{{ route('delegate.upgrades.payment.initiate', $upgradeRequest->id) }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-payment">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                        </button>
                    </form>
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('delegate.upgrades.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Upgrades
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
