@extends('delegate.layouts.app')
@section('title', 'Receipts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-receipt me-2"></i>Receipts</h2>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Regular Receipts</h5>
        @if($receipts->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Receipt No</th>
                            <th>Order No</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($receipts as $receipt)
                            <tr>
                                <td>{{ $receipt->receipt_no ?? 'N/A' }}</td>
                                <td>{{ $receipt->order->order_no ?? 'N/A' }}</td>
                                <td><span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $receipt->type)) }}</span></td>
                                <td>{{ number_format($receipt->order->total ?? 0, 2) }} {{ $receipt->order->registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                                <td>{{ $receipt->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('delegate.receipts.show', $receipt->id) }}" class="btn btn-sm btn-primary">View</a>
                                    <a href="{{ route('delegate.receipts.download', $receipt->id) }}" class="btn btn-sm btn-outline-primary">Download</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $receipts->links() }}
        @else
            <p class="text-muted">No receipts found.</p>
        @endif
    </div>
</div>

@if($upgradeRequests->count() > 0)
<div class="card mt-4">
    <div class="card-body">
        <h5 class="mb-3">Upgrade Receipts</h5>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>Order No</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upgradeRequests as $upgrade)
                        <tr>
                            <td>#{{ $upgrade->id }}</td>
                            <td>{{ $upgrade->upgradeOrder->order_no ?? 'Pending' }}</td>
                            <td>{{ number_format($upgrade->total_amount, 2) }} {{ $upgrade->registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                            <td><span class="badge bg-{{ $upgrade->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($upgrade->status) }}</span></td>
                            <td>{{ $upgrade->created_at->format('M d, Y') }}</td>
                            <td>
                                <a href="{{ route('delegate.upgrades.receipt', $upgrade->id) }}" class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
