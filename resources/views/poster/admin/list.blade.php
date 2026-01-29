@extends('layouts.dashboard')
@section('title', 'Poster Registration List')
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
        
        .search-input:focus {
            border-color: #6A1B9A;
            box-shadow: 0 0 0 0.2rem rgba(106, 27, 154, 0.25);
        }
        
        .search-btn {
            background-color: #6A1B9A;
            border-color: #6A1B9A;
            border-radius: 0.35rem;
            padding: 0.75rem 1.5rem;
            color: white;
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
        
        .badge-status-paid {
            background-color: #28a745;
            color: white;
        }
        
        .badge-status-pending {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-status-failed {
            background-color: #dc3545;
            color: white;
        }
        
        .abstract-title {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">Poster Registration List</h5>
                                <p class="text-sm mb-0">
                                    Manage all poster registrations with advanced filters.
                                </p>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('admin.posters.analytics') }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-chart-bar me-1"></i> Analytics
                                </a>
                                <a href="{{ route('admin.posters.export', request()->query()) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-download me-1"></i> Export
                                </a>
                                <span class="badge bg-primary ms-2">
                                    <i class="fas fa-file-alt me-1"></i>
                                    {{ $registrations->total() }} Registrations
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="search-section">
                        <form method="GET" action="{{ route('admin.posters.list') }}">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control search-input" 
                                               name="search" 
                                               placeholder="Search by TIN, title, author name, email..." 
                                               value="{{ request('search') }}">
                                        <button class="btn search-btn" type="submit">
                                            <i class="fas fa-search me-1"></i> Search
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select name="payment_status" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Payment Status</option>
                                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="currency" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Currencies</option>
                                        <option value="INR" {{ request('currency') == 'INR' ? 'selected' : '' }}>Indian (INR)</option>
                                        <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>International (USD)</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="sector" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Sectors</option>
                                        @foreach($sectors as $sector)
                                            <option value="{{ $sector }}" {{ request('sector') == $sector ? 'selected' : '' }}>
                                                {{ $sector }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="presentation_mode" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Modes</option>
                                        @foreach($presentationModes as $mode)
                                            <option value="{{ $mode }}" {{ request('presentation_mode') == $mode ? 'selected' : '' }}>
                                                {{ $mode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <label class="form-label small">Date From</label>
                                    <input type="date" name="date_from" class="form-control search-input" value="{{ request('date_from') }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Date To</label>
                                    <input type="date" name="date_to" class="form-control search-input" value="{{ request('date_to') }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">Per Page</label>
                                    <select name="per_page" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page</option>
                                        <option value="25" {{ request('per_page') == 25 || !request('per_page') ? 'selected' : '' }}>25 per page</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                                    </select>
                                </div>
                                <div class="col-md-6 text-end">
                                    <label class="form-label small">&nbsp;</label>
                                    <div>
                                        @if(request()->anyFilled(['search', 'payment_status', 'currency', 'sector', 'presentation_mode', 'date_from', 'date_to']))
                                            <a href="{{ route('admin.posters.list') }}" class="btn btn-danger">
                                                <i class="fas fa-times me-1"></i> Clear Filters
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>TIN</th>
                                <th>Abstract Title</th>
                                <th>Lead Author</th>
                                <th>Sector</th>
                                <th>Mode</th>
                                <th>Authors</th>
                                <th>Currency</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($registrations as $registration)
                                    @php
                                        $currencySymbol = $registration->currency === 'USD' ? '$' : '₹';
                                    @endphp
                                    <tr>
                                        <td>{{ $registration->created_at->format('M d, Y') }}</td>
                                        <td><strong>{{ $registration->tin_no }}</strong></td>
                                        <td class="abstract-title" title="{{ $registration->abstract_title }}">
                                            {{ Str::limit($registration->abstract_title, 40) }}
                                        </td>
                                        <td>
                                            <div>{{ $registration->lead_author_name }}</div>
                                            <small class="text-muted">{{ $registration->lead_author_email }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $registration->sector }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $registration->presentation_mode }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark">{{ $registration->posterAuthors->count() }} Author(s)</span>
                                        </td>
                                        <td>
                                            @if($registration->currency === 'INR')
                                                <span class="badge bg-warning text-dark">INR</span>
                                            @else
                                                <span class="badge bg-primary">USD</span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $currencySymbol }}{{ number_format($registration->total_amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($registration->payment_status === 'paid')
                                                <span class="badge-status badge-status-paid">Paid</span>
                                            @elseif($registration->payment_status === 'pending')
                                                <span class="badge-status badge-status-pending">Pending</span>
                                            @else
                                                <span class="badge-status badge-status-failed">Failed</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.posters.show', $registration->id) }}" class="btn btn-sm btn-primary" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            @if(request()->anyFilled(['search', 'payment_status', 'currency', 'sector', 'presentation_mode', 'date_from', 'date_to']))
                                                <i class="fas fa-search me-2"></i>
                                                No registrations found matching your search criteria.
                                            @else
                                                <i class="fas fa-file-alt me-2"></i>
                                                No poster registrations found.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($registrations->hasPages())
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="results-info">
                                    Showing {{ $registrations->firstItem() }} to {{ $registrations->lastItem() }} of {{ $registrations->total() }} results
                                </div>
                                <div>
                                    {{ $registrations->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
