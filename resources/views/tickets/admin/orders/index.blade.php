@extends('layouts.dashboard')
@section('title', 'Ticket Orders')
@section('content')

    <style>
        .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            color: #5a5c69;
        }
        
        .search-section {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.5rem;
        }
        
        .search-input {
            border: 2px solid #d1d3e2;
            border-radius: 0.35rem;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .table th {
            background-color: #6A1B9A;
            color: white;
            font-weight: 600;
        }
        
        .badge-status {
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Ticket Orders</h5>
                    </div>
                    
                    <div class="search-section">
                        <form method="GET" action="{{ route('admin.tickets.orders') }}">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <input type="text" class="form-control search-input" name="search" 
                                           placeholder="Search by order number, company, email..." 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <select name="status" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="event_id" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Events</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                                {{ $event->event_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                    @if(request()->anyFilled(['search', 'status', 'event_id']))
                                        <a href="{{ route('admin.tickets.orders') }}" class="btn btn-secondary">Clear</a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order No</th>
                                    <th>Company</th>
                                    <th>Contact</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    @php
                                        $registration = $order->registration;
                                        $currency = $registration && $registration->nationality === 'International' ? 'USD' : 'INR';
                                        $currencySymbol = $currency === 'USD' ? '$' : '₹';
                                    @endphp
                                    <tr>
                                        <td><strong>{{ $order->order_no }}</strong></td>
                                        <td>{{ $registration ? $registration->company_name : '-' }}</td>
                                        <td>{{ $registration && $registration->contact ? $registration->contact->email : '-' }}</td>
                                        <td><strong>{{ $currencySymbol }}{{ number_format($order->total, 2) }}</strong></td>
                                        <td>
                                            @if($order->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @elseif($order->status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($order->status === 'cancelled')
                                                <span class="badge bg-danger">Cancelled</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.tickets.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">No orders found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($orders->hasPages())
                        <div class="card-footer">
                            {{ $orders->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
