@extends('layouts.dashboard')

@section('title', 'Promocode Analytics - ' . $promoCode->code)

@section('content')
<style>
    .analytics-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .analytics-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
        margin-bottom: 2rem;
    }
    
    .analytics-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.75rem 2rem;
        border: none;
    }
    
    .analytics-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
    }
    
    .analytics-card-body {
        padding: 2rem;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #e9ecef;
    }
    
    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-wrapper {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead {
        background: #f8f9fa;
    }
    
    .table thead th {
        font-weight: 600;
        color: #4a5568;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    .btn-back {
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    
    .btn-back:hover {
        background: #cbd5e0;
        color: #2d3748;
        transform: translateY(-2px);
    }
</style>

<div class="analytics-container">
    <a href="{{ route('admin.tickets.events.promo-codes', $event->id) }}" class="btn-back">
        ← Back to Promocodes
    </a>

    <div class="analytics-card">
        <div class="analytics-card-header">
            <h4>
                <i class="fas fa-chart-bar"></i>
                Promocode Analytics - {{ $promoCode->code }}
            </h4>
        </div>
        
        <div class="analytics-card-body">
            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['total_redemptions'] }}</div>
                    <div class="stat-label">Total Redemptions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">
                        @if($stats['remaining_uses'] === null)
                            ∞
                        @else
                            {{ $stats['remaining_uses'] }}
                        @endif
                    </div>
                    <div class="stat-label">Remaining Uses</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $currencySymbol }}{{ number_format($stats['total_discount_given'], 2) }}</div>
                    <div class="stat-label">Total Discount Given</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">{{ $stats['total_orders'] }}</div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>

            <!-- Promocode Details -->
            <div class="mb-4">
                <h5>Promocode Details</h5>
                <table class="table">
                    <tr>
                        <td style="width: 30%; font-weight: 600;">Code</td>
                        <td><strong>{{ $promoCode->code }}</strong></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;">Type</td>
                        <td>{{ ucfirst($promoCode->type) }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 600;">Value</td>
                        <td>
                            @if($promoCode->type === 'percentage')
                                {{ number_format($promoCode->value, 0) }}%
                            @else
                                {{ $currencySymbol }}{{ number_format($promoCode->value, 2) }}
                            @endif
                        </td>
                    </tr>
                    @if($promoCode->organization_name)
                    <tr>
                        <td style="font-weight: 600;">Organization</td>
                        <td>{{ $promoCode->organization_name }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="font-weight: 600;">Status</td>
                        <td>
                            <span class="badge {{ $promoCode->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $promoCode->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Redemptions List -->
            <div>
                <h5>Redemption History</h5>
                <div class="table-wrapper">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>Contact</th>
                                <th>Discount Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($redemptions as $redemption)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.tickets.orders.show', $redemption->order_id) }}" target="_blank">
                                            {{ $redemption->order->order_no ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        {{ $redemption->contact->name ?? 'N/A' }}<br>
                                        <small class="text-muted">{{ $redemption->contact->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        <strong style="color: #155724;">
                                            {{ $currencySymbol }}{{ number_format($redemption->discount_amount, 2) }}
                                        </strong>
                                    </td>
                                    <td>
                                        {{ $redemption->created_at->format('M d, Y h:i A') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <p class="text-muted mb-0">No redemptions yet.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $currencySymbol = '₹'; // Default to INR, can be made dynamic if needed
@endphp

@endsection
