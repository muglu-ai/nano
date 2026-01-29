@extends('layouts.dashboard')
@section('title', 'Ticket Analytics')
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
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Analytics Filters</h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('admin.tickets.analytics') }}">
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
                                            <a href="{{ route('admin.tickets.analytics') }}" class="btn btn-secondary">Clear Filters</a>
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
                            <p class="stat-value">{{ number_format($registrationsByStatus['paid'] ?? 0) }}</p>
                            <p class="stat-label">Paid Registrations</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card warning">
                            <p class="stat-value">{{ number_format($registrationsByStatus['pending'] ?? 0) }}</p>
                            <p class="stat-label">Pending Registrations</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-card info">
                            <p class="stat-value">{{ number_format($totalStarted) }}</p>
                            <p class="stat-label">Registrations Started</p>
                        </div>
                    </div>
                </div>

                <!-- Revenue by Nationality -->
                <div class="row">
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
                                                <td>{{ $nationality }}</td>
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
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Revenue by Payment Gateway</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Gateway</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($revenueByGateway as $gateway => $revenue)
                                            <tr>
                                                <td>{{ ucfirst($gateway) }}</td>
                                                <td class="text-end"><strong>{{ number_format($revenue, 2) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Registration Status Distribution -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Registrations by Status</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th class="text-end">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($registrationsByStatus as $status => $count)
                                            <tr>
                                                <td>{{ ucfirst($status) }}</td>
                                                <td class="text-end"><strong>{{ number_format($count) }}</strong></td>
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
                                <h5 class="mb-0">Registrations by Nationality</h5>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Nationality</th>
                                            <th class="text-end">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($registrationsByNationality as $nationality => $count)
                                            <tr>
                                                <td>{{ $nationality }}</td>
                                                <td class="text-end"><strong>{{ number_format($count) }}</strong></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Conversion Funnel -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Conversion Funnel</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Stage</th>
                                    <th>Count</th>
                                    <th>Conversion Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Registrations Started</td>
                                    <td>{{ number_format($totalStarted) }}</td>
                                    <td>100%</td>
                                </tr>
                                <tr>
                                    <td>Registrations Completed</td>
                                    <td>{{ number_format($totalCompleted) }}</td>
                                    <td>{{ $totalStarted > 0 ? number_format(($totalCompleted / $totalStarted) * 100, 2) : 0 }}%</td>
                                </tr>
                                <tr>
                                    <td>Abandoned</td>
                                    <td>{{ number_format($totalAbandoned) }}</td>
                                    <td>{{ $totalStarted > 0 ? number_format(($totalAbandoned / $totalStarted) * 100, 2) : 0 }}%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Daily Trends -->
                @if($dailyTrends->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Daily Registration Trends</h5>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Registrations</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dailyTrends as $trend)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                        <td class="text-end"><strong>{{ number_format($trend->count) }}</strong></td>
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
