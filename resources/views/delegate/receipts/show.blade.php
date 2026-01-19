@extends('delegate.layouts.app')
@section('title', 'Receipt Details')

@push('styles')
<style>
    :root {
        --primary-color: #004aad;
        --text-primary: #333333;
        --text-secondary: #666666;
        --border-color: #e0e0e0;
        --bg-light: #f8f9fa;
    }

    .form-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        padding: 0;
        margin-bottom: 2rem;
        overflow: hidden;
    }

    .form-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #0066cc 100%);
        color: white;
        padding: 1.5rem 2rem;
        text-align: center;
    }

    .form-header h2 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .form-header p {
        margin: 0.5rem 0 0 0;
        opacity: 0.9;
        font-size: 0.95rem;
    }

    .form-body {
        padding: 2rem;
    }

    .receipt-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background: var(--bg-light);
        border-bottom: 2px solid var(--border-color);
        margin: -2rem -2rem 1.5rem -2rem;
    }

    .receipt-type {
        background: #ffffff;
        color: var(--text-primary);
        padding: 0.5rem 1rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .receipt-date {
        font-size: 0.9rem;
        color: var(--text-secondary);
    }

    .order-info-box {
        background: #f0f7ff;
        border: 2px solid var(--primary-color);
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .order-info-box strong {
        font-size: 1.1rem;
        color: var(--primary-color);
    }

    .alert-box {
        background: #fff3cd;
        border: 1px solid #ffc107;
        border-left: 4px solid #ffc107;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }

    .alert-box.success {
        background: #d4edda;
        border-color: #28a745;
        border-left-color: #28a745;
    }

    .details-section {
        margin-bottom: 2rem;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-primary);
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary-color);
        font-size: 1rem;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table td {
        padding: 0.75rem;
        border: 1px solid var(--border-color);
        font-size: 0.9rem;
        vertical-align: middle;
    }

    .info-table .label-cell {
        background: var(--bg-light);
        font-weight: 600;
        color: var(--text-primary);
        width: 40%;
    }

    .info-table .value-cell {
        color: var(--text-secondary);
        width: 60%;
    }

    .status-row {
        background: #d4edda;
    }

    .status-row.pending {
        background: #fff3cd;
    }

    .payment-status-badge {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .payment-status-badge.paid {
        background: #28a745;
        color: white;
    }

    .payment-status-badge.pending {
        background: #ffc107;
        color: #856404;
    }

    .price-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.5rem;
    }

    .price-table td {
        padding: 0.6rem 0.75rem;
        border: 1px solid var(--border-color);
        font-size: 0.9rem;
    }

    .price-table .label-cell {
        background: var(--bg-light);
        font-weight: 600;
        text-align: right;
    }

    .price-table .value-cell {
        text-align: right;
        font-weight: 600;
    }

    .price-table .total-row {
        background: var(--primary-color);
        color: white;
    }

    .price-table .total-row td {
        border-color: var(--primary-color);
        font-weight: 700;
        font-size: 1rem;
    }

    .btn-pay-now {
        display: inline-block;
        background: var(--primary-color);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.3s;
    }

    .btn-pay-now:hover {
        background: #0066cc;
        color: white;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-receipt me-2"></i>Receipt Details</h2>
        <p>{{ $receipt->order->registration->event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $receipt->order->registration->event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
        @if(session('error'))
        <div class="alert alert-warning mb-3" style="background: #fff3cd; border: 1px solid #ffc107; border-left: 4px solid #ffc107; border-radius: 8px; padding: 1rem;">
            <i class="fas fa-exclamation-triangle me-2" style="color: #856404;"></i>
            <span style="color: #856404;">{{ session('error') }}</span>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success mb-3" style="background: #d4edda; border: 1px solid #28a745; border-left: 4px solid #28a745; border-radius: 8px; padding: 1rem;">
            <i class="fas fa-check-circle me-2" style="color: #155724;"></i>
            <span style="color: #155724;">{{ session('success') }}</span>
        </div>
        @endif

        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="receipt-type">
                @if($receipt->order->status === 'paid')
                    ✓ CONFIRMATION RECEIPT
                @else
                    ⏳ PROVISIONAL RECEIPT
                @endif
            </div>
            <div class="receipt-date">
                <strong>Date:</strong> {{ $receipt->created_at->format('d-m-Y') }}
            </div>
        </div>

        <!-- Order Info -->
        <div class="order-info-box">
            <strong><i class="fas fa-ticket-alt me-2"></i>Order No.: {{ $receipt->order->order_no }}</strong>
            @if($receipt->receipt_no)
                <p style="margin-top: 0.5rem;"><strong>Receipt No.:</strong> <span style="color: #0066cc; font-weight: 700;">{{ $receipt->receipt_no }}</span></p>
            @endif
        </div>

        <!-- Alert -->
        @if($receipt->order->status !== 'paid')
        <div class="alert-box">
            <p><strong>⚠️ Action Required:</strong> Your order is pending payment. Please complete the payment to confirm your upgrade.</p>
        </div>
        @else
        <div class="alert-box success">
            <p><strong>✓ Payment Confirmed:</strong> Your upgrade has been confirmed. Thank you for your payment!</p>
        </div>
        @endif

        <!-- Registration Information -->
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-clipboard-list"></i>
                Registration Information
            </h4>
            @php
                $isInternational = ($receipt->order->registration->nationality === 'International' || $receipt->order->registration->nationality === 'international');
                $currencySymbol = $isInternational ? '$' : '₹';
                $priceFormat = $isInternational ? 2 : 0;
            @endphp
            <table class="info-table">
                <tr class="{{ $receipt->order->status === 'paid' ? 'status-row' : 'status-row pending' }}">
                    <td class="label-cell" style="color: {{ $receipt->order->status === 'paid' ? '#155724' : '#856404' }};">Payment Status</td>
                    <td class="value-cell">
                        <span class="payment-status-badge {{ $receipt->order->status === 'paid' ? 'paid' : 'pending' }}">
                            {{ $receipt->order->status === 'paid' ? '✓ PAID' : '⏳ PENDING' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">Company Name</td>
                    <td class="value-cell"><strong>{{ $receipt->order->registration->company_name ?? 'N/A' }}</strong></td>
                </tr>
                <tr>
                    <td class="label-cell">Contact Name</td>
                    <td class="value-cell">{{ $receipt->order->registration->contact->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Email</td>
                    <td class="value-cell">{{ $receipt->order->registration->contact->email ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Phone</td>
                    <td class="value-cell">{{ $receipt->order->registration->contact->phone ?? $receipt->order->registration->company_phone ?? 'N/A' }}</td>
                </tr>
            </table>
        </div>

        <!-- Upgrade Details -->
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-arrow-up"></i>
                Upgrade Details
            </h4>
            <table class="info-table">
                <thead>
                    <tr>
                        <th class="label-cell">Ticket</th>
                        <th class="value-cell">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($receipt->order->items ?? [] as $item)
                        <tr>
                            <td class="label-cell">Ticket Type</td>
                            <td class="value-cell"><strong>{{ $item->ticketType->name ?? 'N/A' }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label-cell">Quantity</td>
                            <td class="value-cell">{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Price Breakdown -->
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-calculator"></i>
                Price Breakdown
            </h4>
            <table class="price-table">
                <tr>
                    <td class="label-cell">Subtotal:</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($receipt->order->subtotal ?? 0, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">GST ({{ config('constants.GST_RATE', 18) }}%):</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($receipt->order->gst_total ?? 0, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Processing Charge:</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($receipt->order->processing_charge_total ?? 0, $priceFormat) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label-cell">Total Amount:</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($receipt->order->total ?? 0, $priceFormat) }}</td>
                </tr>
            </table>
        </div>

        <!-- Payment Transaction Details (shown only when paid) -->
        @if($receipt->order->status === 'paid')
        @php
            $payment = \App\Models\Ticket\TicketPayment::whereJsonContains('order_ids_json', $receipt->order->id)
                ->where('status', 'completed')
                ->latest()
                ->first();
        @endphp
        @if($payment)
        <div class="details-section">
            <h4 class="section-title">
                <i class="fas fa-receipt"></i>
                Payment Transaction Details
            </h4>
            <table class="info-table">
                <tr>
                    <td class="label-cell">Payment Method</td>
                    <td class="value-cell">{{ ucfirst($payment->method ?? 'Online') }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Transaction ID</td>
                    <td class="value-cell" style="font-weight: 700; color: var(--primary-color);">{{ $payment->gateway_txn_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Amount Paid</td>
                    <td class="value-cell" style="font-weight: 700; color: #155724;">{{ $currencySymbol }}{{ number_format($payment->amount ?? $receipt->order->total, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Payment Date</td>
                    <td class="value-cell">{{ $payment->paid_at ? $payment->paid_at->format('d M Y, h:i A') : 'N/A' }}</td>
                </tr>
                <tr class="status-row">
                    <td class="label-cell" style="color: #155724;">Payment Status</td>
                    <td class="value-cell">
                        <span class="payment-status-badge paid">✓ CONFIRMED</span>
                    </td>
                </tr>
            </table>
        </div>
        @endif
        @endif

        <!-- Pay Now Button (only if unpaid) -->
        @if($receipt->order->status !== 'paid')
        @php
            $upgradeRequest = \App\Models\Ticket\TicketUpgradeRequest::where('upgrade_order_id', $receipt->order->id)->first();
        @endphp
        @if($upgradeRequest)
        <div class="text-center mt-4">
            <a href="{{ route('delegate.upgrades.payment.initiate', $upgradeRequest->id) }}" class="btn-pay-now">
                💳 Pay Now - {{ $currencySymbol }}{{ number_format($receipt->order->total, $priceFormat) }}
            </a>
        </div>
        @endif
        @endif

        <!-- Action Buttons -->
        <div class="mt-4 d-flex gap-2">
            <a href="{{ route('delegate.receipts.download', $receipt->id) }}" class="btn btn-primary">
                <i class="fas fa-download me-2"></i>Download PDF
            </a>
            <a href="{{ route('delegate.receipts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Receipts
            </a>
        </div>
    </div>
</div>
@endsection
