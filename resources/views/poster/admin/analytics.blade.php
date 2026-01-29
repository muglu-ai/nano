@extends('layouts.dashboard')
@section('title', 'Poster Registration Analytics')
@section('content')

    <style>
        .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-card.danger {
            background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
            margin: 0;
        }
        
        .clickable-badge {
            transition: all 0.2s ease;
        }
        
        .clickable-badge:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .clickable-number {
            transition: all 0.2s ease;
        }
        
        .clickable-number:hover {
            color: #6A1B9A !important;
            text-decoration: underline !important;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Poster Registration Analytics</h5>
                            <a href="{{ route('admin.posters.list') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-list me-1"></i> View All Registrations
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.posters.analytics') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Date From</label>
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date To</label>
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        @if(request()->anyFilled(['date_from', 'date_to']))
                                            <a href="{{ route('admin.posters.analytics') }}" class="btn btn-secondary">Clear Filters</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-card">
                            <p class="stat-value">{{ number_format($totalRegistrations) }}</p>
                            <p class="stat-label">Total Registrations</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card success">
                            <p class="stat-value">{{ number_format($paidRegistrations) }}</p>
                            <p class="stat-label">Paid Registrations</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card warning">
                            <p class="stat-value">{{ number_format($pendingRegistrations) }}</p>
                            <p class="stat-label">Pending Payments</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card danger">
                            <p class="stat-value">{{ number_format($failedRegistrations) }}</p>
                            <p class="stat-label">Failed Payments</p>
                        </div>
                    </div>
                </div>

                <!-- Revenue Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card info">
                            <p class="stat-value">₹{{ number_format($revenueINR, 2) }}</p>
                            <p class="stat-label">Revenue (INR)</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card info">
                            <p class="stat-value">${{ number_format($revenueUSD, 2) }}</p>
                            <p class="stat-label">Revenue (USD)</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                            <p class="stat-value">{{ number_format($totalAuthors) }}</p>
                            <p class="stat-label">Total Authors ({{ $attendingAuthors }} Attending)</p>
                        </div>
                    </div>
                </div>

                <!-- Sector-wise Breakdown -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Sector-wise Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Sector</th>
                                        <th class="text-end">Paid</th>
                                        <th class="text-end">Pending</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Revenue (INR)</th>
                                        <th class="text-end">Revenue (USD)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sectorWiseData as $data)
                                        <tr>
                                            <td><strong>{{ $data->sector ?? 'N/A' }}</strong></td>
                                            <td class="text-end">
                                                @if($data->paid_count > 0)
                                                    <a href="{{ route('admin.posters.list', ['sector' => $data->sector, 'payment_status' => 'paid']) }}" 
                                                       class="badge bg-success text-decoration-none clickable-badge">
                                                        {{ number_format($data->paid_count) }}
                                                    </a>
                                                @else
                                                    <span class="badge bg-success">0</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($data->pending_count > 0)
                                                    <a href="{{ route('admin.posters.list', ['sector' => $data->sector, 'payment_status' => 'pending']) }}" 
                                                       class="badge bg-warning text-dark text-decoration-none clickable-badge">
                                                        {{ number_format($data->pending_count) }}
                                                    </a>
                                                @else
                                                    <span class="badge bg-warning text-dark">0</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('admin.posters.list', ['sector' => $data->sector]) }}" 
                                                   class="text-decoration-none text-dark fw-bold clickable-number">
                                                    {{ number_format($data->total_count) }}
                                                </a>
                                            </td>
                                            <td class="text-end"><strong>₹{{ number_format($data->revenue_inr, 2) }}</strong></td>
                                            <td class="text-end"><strong>${{ number_format($data->revenue_usd, 2) }}</strong></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Currency Breakdown (Indian vs International) -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Currency Breakdown (Indian vs International)</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Currency</th>
                                            <th class="text-end">Paid</th>
                                            <th class="text-end">Pending</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($currencyBreakdown as $data)
                                            <tr>
                                                <td>
                                                    @if($data->currency === 'INR')
                                                        <span class="badge bg-warning text-dark">Indian (INR)</span>
                                                    @else
                                                        <span class="badge bg-primary">International (USD)</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge bg-success">{{ number_format($data->paid_count) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge bg-warning text-dark">{{ number_format($data->pending_count) }}</span>
                                                </td>
                                                <td class="text-end"><strong>{{ number_format($data->total_count) }}</strong></td>
                                                <td class="text-end">
                                                    <strong>{{ $data->currency === 'INR' ? '₹' : '$' }}{{ number_format($data->revenue, 2) }}</strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Presentation Mode Breakdown -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Presentation Mode Breakdown</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Mode</th>
                                            <th class="text-end">Paid</th>
                                            <th class="text-end">Pending</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($presentationModeData as $data)
                                            <tr>
                                                <td><strong>{{ $data->presentation_mode ?? 'N/A' }}</strong></td>
                                                <td class="text-end">
                                                    <span class="badge bg-success">{{ number_format($data->paid_count) }}</span>
                                                </td>
                                                <td class="text-end">
                                                    <span class="badge bg-warning text-dark">{{ number_format($data->pending_count) }}</span>
                                                </td>
                                                <td class="text-end"><strong>{{ number_format($data->total_count) }}</strong></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">No data available</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Gateway Breakdown -->
                @if(count($gatewayBreakdown) > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Gateway Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Gateway</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gatewayBreakdown as $data)
                                    <tr>
                                        <td><strong>{{ ucfirst($data->payment_method ?? 'Unknown') }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($data->count) }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($data->revenue, 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Daily Registration Trends -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Daily Registration Trends (Last 30 Days)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Paid</th>
                                        <th class="text-end">Pending</th>
                                        <th class="text-end">Revenue (INR)</th>
                                        <th class="text-end">Revenue (USD)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dailyTrends as $trend)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                            <td class="text-end"><strong>{{ number_format($trend->total_count) }}</strong></td>
                                            <td class="text-end">
                                                <span class="badge bg-success">{{ number_format($trend->paid_count) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-warning text-dark">{{ number_format($trend->pending_count) }}</span>
                                            </td>
                                            <td class="text-end">
                                                @if($trend->revenue_inr > 0)
                                                    <strong>₹{{ number_format($trend->revenue_inr, 2) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($trend->revenue_usd > 0)
                                                    <strong>${{ number_format($trend->revenue_usd, 2) }}</strong>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
