@extends('delegate.layouts.app')
@section('title', 'Registration Details')

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-list me-2"></i>Registration Details</h4>
    </div>
    <div class="card-body">
        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#details-tab" type="button">Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#badge-tab" type="button">Badge</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#receipt-tab" type="button">Receipt</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#upgrades-tab" type="button">Upgrades</button>
            </li>
        </ul>

        <div class="tab-content">
            <!-- Details Tab -->
            <div class="tab-pane fade show active" id="details-tab">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Company Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Company Name:</th>
                                <td>{{ $registration->company_name }}</td>
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
                    <div class="col-md-6">
                        <h5>Contact Information</h5>
                        <table class="table table-bordered">
                            <tr>
                                <th>Name:</th>
                                <td>{{ $registration->contact->name ?? 'N/A' }}</td>
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

                <h5>Delegates</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                                    <td>{{ $delegate->full_name }}</td>
                                    <td>{{ $delegate->email }}</td>
                                    <td>{{ $delegate->phone ?? 'N/A' }}</td>
                                    <td>{{ $delegate->ticket->ticketType->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $delegate->ticket && $delegate->ticket->status === 'issued' ? 'success' : 'warning' }}">
                                            {{ $delegate->ticket ? ucfirst($delegate->ticket->status) : 'Pending' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($delegate->id)
                                            <a href="{{ route('delegate.badges.show', $delegate->id) }}" class="btn btn-sm btn-outline-primary">View Badge</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Badge Tab (Coming Soon) -->
            <div class="tab-pane fade" id="badge-tab">
                <div class="text-center py-5">
                    <i class="fas fa-id-badge fa-5x text-muted mb-4"></i>
                    <h3>Badge Management</h3>
                    <p class="text-muted">This feature is coming soon.</p>
                </div>
            </div>

            <!-- Receipt Tab -->
            <div class="tab-pane fade" id="receipt-tab">
                @if($registration->order)
                    <h5>Order Information</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th>Order No:</th>
                            <td>{{ $registration->order->order_no }}</td>
                        </tr>
                        <tr>
                            <th>Total Amount:</th>
                            <td>{{ number_format($registration->order->total, 2) }} {{ $registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td><span class="badge bg-{{ $registration->order->status === 'paid' ? 'success' : 'warning' }}">{{ ucfirst($registration->order->status) }}</span></td>
                        </tr>
                    </table>
                    @if($registration->order->receipt)
                        <a href="{{ route('delegate.receipts.show', $registration->order->receipt->id) }}" class="btn btn-primary">View Receipt</a>
                    @endif
                @else
                    <p class="text-muted">No order found for this registration.</p>
                @endif
            </div>

            <!-- Upgrades Tab -->
            <div class="tab-pane fade" id="upgrades-tab">
                @if($upgradeRequests->count() > 0)
                    <h5>Upgrade History</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
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
                                        <td>#{{ $upgrade->id }}</td>
                                        <td>{{ number_format($upgrade->total_amount, 2) }} {{ $registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
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
                @else
                    <p class="text-muted">No upgrades for this registration.</p>
                @endif
                <div class="mt-3">
                    <a href="{{ route('delegate.upgrades.group.form', $registration->id) }}" class="btn btn-primary">Upgrade Tickets</a>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('delegate.registrations.index') }}" class="btn btn-secondary">Back to Registrations</a>
        </div>
    </div>
</div>
@endsection
