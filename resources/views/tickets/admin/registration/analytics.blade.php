@extends('layouts.dashboard')
@section('title', 'Registration Analytics')
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
        
        .chart-container {
            position: relative;
            height: 300px;
            margin-top: 1rem;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Analytics Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.tickets.registration.analytics') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="form-label">Event</label>
                                    <select name="event_id" class="form-control" onchange="this.form.submit()">
                                        <option value="">All Events</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                                {{ $event->event_name }} {{ $event->event_year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
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
                                        @if(request()->anyFilled(['event_id', 'date_from', 'date_to']))
                                            <a href="{{ route('admin.tickets.registration.analytics') }}" class="btn btn-secondary">Clear Filters</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <p class="stat-value">{{ number_format($totalRegistrations) }}</p>
                            <p class="stat-label">Total Registrations</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card success">
                            <p class="stat-value">{{ number_format($paidRegistrations) }}</p>
                            <p class="stat-label">Paid Registrations</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card warning">
                            <p class="stat-value">{{ number_format($notPaidRegistrations) }}</p>
                            <p class="stat-label">Not Paid Registrations</p>
                        </div>
                    </div>
                </div>

                <!-- Category-wise Registration with Nationality (Paid & Not Paid) -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Category-wise Registration with Nationality (Paid & Not Paid)</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Category</th>
                                        <th>Nationality</th>
                                        <th class="text-end">Paid</th>
                                        <th class="text-end">Not Paid</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categoryWiseData as $data)
                                        <tr>
                                            <td><strong>{{ $data->category_name ?? 'Uncategorized' }}</strong></td>
                                            <td>
                                                @if($data->nationality === 'Indian')
                                                    <span class="badge bg-warning text-dark">Indian</span>
                                                @elseif($data->nationality === 'International')
                                                    <span class="badge bg-primary">International</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-success">{{ number_format($data->paid_count) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="badge bg-warning text-dark">{{ number_format($data->not_paid_count) }}</span>
                                            </td>
                                            <td class="text-end">
                                                <strong>{{ number_format($data->total_count) }}</strong>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No data available</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Registration Trends for Paid -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Registration Trends for Paid (Daily)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th class="text-end">Total Registrations</th>
                                                <th class="text-end">Paid Count</th>
                                                <th class="text-end">Revenue</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($paidTrends as $trend)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                                    <td class="text-end"><strong>{{ number_format($trend->count) }}</strong></td>
                                                    <td class="text-end">
                                                        <span class="badge bg-success">{{ number_format($trend->paid_count) }}</span>
                                                    </td>
                                                    <td class="text-end">
                                                        @php
                                                            $revenueUsd = $trend->revenue_usd ?? 0;
                                                            $revenueInr = $trend->revenue_inr ?? 0;
                                                        @endphp
                                                        @if($revenueUsd > 0)
                                                            <strong>${{ number_format($revenueUsd, 2) }}</strong>
                                                        @endif
                                                        @if($revenueUsd > 0 && $revenueInr > 0) / @endif
                                                        @if($revenueInr > 0)
                                                            <strong>₹{{ number_format($revenueInr, 2) }}</strong>
                                                        @endif
                                                        @if($revenueUsd == 0 && $revenueInr == 0)
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
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
                </div>

                <!-- Registration Trends for Not Paid -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Registration Trends for Not Paid (Daily)</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th class="text-end">Not Paid Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($notPaidTrends as $trend)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                                    <td class="text-end">
                                                        <span class="badge bg-warning text-dark">{{ number_format($trend->not_paid_count) }}</span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="2" class="text-center text-muted py-4">No data available</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nationality Breakdown -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Registration by Nationality (Paid vs Not Paid)</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nationality</th>
                                            <th class="text-end">Paid</th>
                                            <th class="text-end">Not Paid</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($nationalityBreakdown as $breakdown)
                                            <tr>
                                                <td>
                                                    @if($breakdown->nationality === 'Indian')
                                                        <span class="badge bg-warning text-dark">Indian</span>
                                                    @else
                                                        <span class="badge bg-primary">International</span>
                                                    @endif
                                                </td>
                                                <td class="text-end"><span class="badge bg-success">{{ number_format($breakdown->paid_count) }}</span></td>
                                                <td class="text-end"><span class="badge bg-warning text-dark">{{ number_format($breakdown->not_paid_count) }}</span></td>
                                                <td class="text-end"><strong>{{ number_format($breakdown->total_count) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Revenue by Nationality</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nationality</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($revenueByNationality as $nationality => $revenue)
                                            <tr>
                                                <td>
                                                    @if($nationality === 'Indian')
                                                        <span class="badge bg-warning text-dark">Indian</span>
                                                    @else
                                                        <span class="badge bg-primary">International</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <strong>
                                                        {{ $nationality === 'International' ? '$' : '₹' }}{{ number_format($revenue, 2) }}
                                                    </strong>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Gateway Breakdown -->
                @if(!empty($gatewayBreakdown))
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Gateway Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Gateway</th>
                                    <th class="text-end">Count</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gatewayBreakdown as $gateway => $data)
                                    <tr>
                                        <td><strong>{{ ucfirst($gateway) }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($data['count']) }}</strong></td>
                                        <td class="text-end"><strong>{{ number_format($data['revenue'], 2) }}</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
