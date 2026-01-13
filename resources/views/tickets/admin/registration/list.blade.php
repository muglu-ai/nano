@extends('layouts.dashboard')
@section('title', 'Registration List')
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
        
        .badge-status-not-paid {
            background-color: #ffc107;
            color: #000;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">Registration List</h5>
                                <p class="text-sm mb-0">
                                    Manage all ticket registrations with advanced filters.
                                </p>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('admin.tickets.registrations.export', request()->query()) }}" class="btn btn-success">
                                    <i class="fas fa-download me-1"></i> Export
                                </a>
                                <span class="badge bg-primary ms-2">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $registrations->total() }} Registrations
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="search-section">
                        <form method="GET" action="{{ route('admin.tickets.registration.list') }}">
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control search-input" 
                                               name="search" 
                                               placeholder="Search by TIN, company, email, phone..." 
                                               value="{{ request('search') }}">
                                        <button class="btn search-btn" type="submit">
                                            <i class="fas fa-search me-1"></i> Search
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select name="event_id" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Events</option>
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                                {{ $event->event_name }} {{ $event->event_year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="payment_status" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Payment Status</option>
                                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                        <option value="not_paid" {{ request('payment_status') == 'not_paid' ? 'selected' : '' }}>Not Paid</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="nationality" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Nationality</option>
                                        <option value="Indian" {{ request('nationality') == 'Indian' ? 'selected' : '' }}>Indian</option>
                                        <option value="International" {{ request('nationality') == 'International' ? 'selected' : '' }}>International</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="category_id" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <label class="form-label small">Date From</label>
                                    <input type="date" name="date_from" class="form-control search-input" value="{{ request('date_from') }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Date To</label>
                                    <input type="date" name="date_to" class="form-control search-input" value="{{ request('date_to') }}" onchange="this.form.submit()">
                                </div>
                                <div class="col-md-2">
                                    <select name="per_page" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page</option>
                                        <option value="25" {{ request('per_page') == 25 || !request('per_page') ? 'selected' : '' }}>25 per page</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                                    </select>
                                </div>
                                <div class="col-md-4 text-end">
                                    @if(request()->anyFilled(['search', 'event_id', 'payment_status', 'nationality', 'category_id', 'date_from', 'date_to']))
                                        <a href="{{ route('admin.tickets.registration.list') }}" class="btn btn-danger">
                                            <i class="fas fa-times me-1"></i> Clear Filters
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="sortable" onclick="sortTable('created_at')">
                                    Date
                                    @if(request('sort') == 'created_at')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th>Order No (TIN)</th>
                                <th>Company</th>
                                <th>Contact</th>
                                <th>Category</th>
                                <th>Nationality</th>
                                <th>Delegates</th>
                                <th>Total Amount</th>
                                <th>Payment Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($registrations as $registration)
                                    @php
                                        $order = $registration->order;
                                        $currency = $registration->nationality === 'International' ? 'USD' : 'INR';
                                        $currencySymbol = $currency === 'USD' ? '$' : '₹';
                                        $isPaid = $order && $order->status === 'paid';
                                    @endphp
                                    <tr>
                                        <td>{{ $registration->created_at->format('M d, Y') }}</td>
                                        <td>
                                            @if($order)
                                                <strong>{{ $order->order_no }}</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $registration->company_name }}</strong></td>
                                        <td>
                                            @if($registration->contact)
                                                <div>{{ $registration->contact->name }}</div>
                                                <small class="text-muted">{{ $registration->contact->email }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($registration->registrationCategory)
                                                <span class="badge bg-info">{{ $registration->registrationCategory->name }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($registration->nationality === 'Indian')
                                                <span class="badge bg-warning text-dark">Indian</span>
                                            @else
                                                <span class="badge bg-primary">International</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $registration->delegates->count() }} Delegate(s)</span>
                                        </td>
                                        <td>
                                            @if($order)
                                                <strong>{{ $currencySymbol }}{{ number_format($order->total, 2) }}</strong>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isPaid)
                                                <span class="badge-status badge-status-paid">Paid</span>
                                            @else
                                                <span class="badge-status badge-status-not-paid">Not Paid</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.tickets.registrations.show', $registration->id) }}" class="btn btn-sm btn-primary" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.tickets.registrations.edit', $registration->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            @if(request()->anyFilled(['search', 'event_id', 'payment_status', 'nationality', 'category_id', 'date_from', 'date_to']))
                                                <i class="fas fa-search me-2"></i>
                                                No registrations found matching your search criteria.
                                            @else
                                                <i class="fas fa-users me-2"></i>
                                                No registrations found.
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

    <script>
        function sortTable(field) {
            const url = new URL(window.location.href);
            const currentSort = url.searchParams.get('sort');
            const currentDirection = url.searchParams.get('direction');
            
            let direction = 'asc';
            if (currentSort === field && currentDirection === 'asc') {
                direction = 'desc';
            }
            
            url.searchParams.set('sort', field);
            url.searchParams.set('direction', direction);
            window.location.href = url.toString();
        }
    </script>
@endsection
