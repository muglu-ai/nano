@extends('delegate.layouts.app')
@section('title', 'Upgrade Receipt')

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-receipt me-2"></i>Upgrade Receipt</h4>
    </div>
    <div class="card-body">
        <div class="alert alert-{{ $upgradeRequest->status === 'paid' ? 'success' : 'warning' }}">
            <strong>Status: {{ strtoupper($upgradeRequest->status) }}</strong>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Upgrade Details</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Request ID:</th>
                        <td>#{{ $upgradeRequest->id }}</td>
                    </tr>
                    <tr>
                        <th>Type:</th>
                        <td>{{ ucfirst($upgradeRequest->request_type) }}</td>
                    </tr>
                    <tr>
                        <th>Order No:</th>
                        <td>{{ $upgradeRequest->upgradeOrder->order_no ?? 'Pending' }}</td>
                    </tr>
                    <tr>
                        <th>Created:</th>
                        <td>{{ $upgradeRequest->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h5>Price Breakdown</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Price Difference:</th>
                        <td>{{ number_format($upgradeRequest->price_difference, 2) }} {{ $upgradeRequest->registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                    </tr>
                    <tr>
                        <th>GST:</th>
                        <td>{{ number_format($upgradeRequest->gst_amount, 2) }} {{ $upgradeRequest->registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                    </tr>
                    <tr>
                        <th>Processing Charge:</th>
                        <td>{{ number_format($upgradeRequest->processing_charge_amount, 2) }} {{ $upgradeRequest->registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                    </tr>
                    <tr>
                        <th><strong>Total:</strong></th>
                        <td><strong>{{ number_format($upgradeRequest->total_amount, 2) }} {{ $upgradeRequest->registration->nationality === 'International' ? 'USD' : 'INR' }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <h5>Ticket Upgrades</h5>
        <div class="table-responsive">
            <table class="table table-bordered">
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
                            <td>Ticket #{{ $ticketData['ticket_id'] ?? 'N/A' }}</td>
                            <td>{{ $ticketData['old_ticket_type_name'] ?? 'N/A' }}</td>
                            <td>{{ $ticketData['new_ticket_type_name'] ?? 'N/A' }}</td>
                            <td>{{ number_format($ticketData['price_difference'] ?? 0, 2) }} {{ $upgradeRequest->registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($upgradeRequest->status === 'pending')
            <div class="mt-4">
                <form method="POST" action="{{ route('delegate.upgrades.confirm', $upgradeRequest->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-credit-card me-2"></i>Proceed to Payment
                    </button>
                </form>
            </div>
        @endif

        <div class="mt-3">
            <a href="{{ route('delegate.upgrades.index') }}" class="btn btn-secondary">Back to Upgrades</a>
        </div>
    </div>
</div>
@endsection
