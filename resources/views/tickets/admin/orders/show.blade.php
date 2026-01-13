@extends('layouts.dashboard')
@section('title', 'Order Details')
@section('content')

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('admin.tickets.orders') }}" class="btn btn-secondary mb-3">
                    <i class="fas fa-arrow-left me-2"></i>Back to Orders
                </a>

                @php
                    $registration = $order->registration;
                    $currency = $registration && $registration->nationality === 'International' ? 'USD' : 'INR';
                    $currencySymbol = $currency === 'USD' ? '$' : '₹';
                @endphp

                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order: {{ $order->order_no }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Status:</strong> 
                                    @if($order->status === 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($order->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="badge bg-danger">Cancelled</span>
                                    @else
                                        <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
                                    @endif
                                </p>
                                <p><strong>Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Subtotal:</strong> {{ $currencySymbol }}{{ number_format($order->subtotal, 2) }}</p>
                                <p><strong>GST:</strong> {{ $currencySymbol }}{{ number_format($order->gst_total, 2) }}</p>
                                <p><strong>Processing Charge:</strong> {{ $currencySymbol }}{{ number_format($order->processing_charge_total, 2) }}</p>
                                <p><strong>Total:</strong> <strong>{{ $currencySymbol }}{{ number_format($order->total, 2) }}</strong></p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($registration)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Registration Details</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Company:</strong> {{ $registration->company_name }}</p>
                        <p><strong>Contact:</strong> {{ $registration->contact ? $registration->contact->email : '-' }}</p>
                        <a href="{{ route('admin.tickets.registrations.show', $registration->id) }}" class="btn btn-sm btn-primary">
                            View Full Registration
                        </a>
                    </div>
                </div>
                @endif

                @if($order->items->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Items</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Ticket Type</th>
                                    <th>Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->ticketType ? $item->ticketType->name : 'N/A' }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($payment)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Gateway:</strong> {{ ucfirst($payment->gateway_name) }}</p>
                        <p><strong>Transaction ID:</strong> {{ $payment->gateway_txn_id }}</p>
                        <p><strong>Amount:</strong> {{ $currencySymbol }}{{ number_format($payment->amount, 2) }}</p>
                        <p><strong>Paid At:</strong> {{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : '-' }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
