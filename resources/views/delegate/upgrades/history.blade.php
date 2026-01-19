@extends('delegate.layouts.app')
@section('title', 'Upgrade History')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-history me-2"></i>Upgrade History</h2>
    <a href="{{ route('delegate.upgrades.index') }}" class="btn btn-secondary">Back to Upgrades</a>
</div>

<div class="card">
    <div class="card-body">
        @if($upgrades->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upgrades as $upgrade)
                            <tr>
                                <td>#{{ $upgrade->id }}</td>
                                <td><span class="badge bg-info">{{ ucfirst($upgrade->request_type) }}</span></td>
                                <td>{{ number_format($upgrade->total_amount, 2) }} {{ $upgrade->registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                                <td><span class="badge bg-{{ $upgrade->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($upgrade->status) }}</span></td>
                                <td>{{ $upgrade->created_at->format('M d, Y h:i A') }}</td>
                                <td>
                                    <a href="{{ route('delegate.upgrades.receipt', $upgrade->id) }}" class="btn btn-sm btn-primary">View Receipt</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $upgrades->links() }}
        @else
            <p class="text-muted text-center py-4">No upgrade history found.</p>
        @endif
    </div>
</div>
@endsection
