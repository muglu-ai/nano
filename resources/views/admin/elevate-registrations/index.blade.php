@extends('layouts.dashboard')
@section('title', 'ELEVATE Registrations')
@section('content')

    <style>
        /* Clean and simple design */
        .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            color: #5a5c69;
        }
        
        .card-header h5 {
            color: #5a5c69;
            font-weight: 600;
        }
        
        /* Search section */
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
        
        .search-btn:hover {
            background-color: #4A0072;
            border-color: #4A0072;
            color: white;
        }
        
        .clear-btn {
            background-color: #e74a3b;
            border-color: #e74a3b;
            border-radius: 0.35rem;
            padding: 0.75rem 1.5rem;
            color: white;
        }
        
        .clear-btn:hover {
            background-color: #c0392b;
            border-color: #c0392b;
            color: white;
        }
        
        /* Table styling */
        .table th {
            background-color: #6A1B9A;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
            border: none;
        }
        
        .table td {
            padding: 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #e3e6f0;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fc;
        }
        
        /* Sortable headers */
        .sortable {
            cursor: pointer;
            position: relative;
        }
        
        .sortable:hover {
            background-color: #4A0072 !important;
        }
        
        .sortable::after {
            content: '↕';
            position: absolute;
            right: 8px;
            opacity: 0.5;
        }
        
        /* Pagination */
        .pagination {
            margin: 0;
        }
        
        .page-link {
            color: #6A1B9A;
            border: 1px solid #d1d3e2;
            padding: 0.5rem 0.75rem;
            margin: 0 2px;
            border-radius: 0.35rem;
        }
        
        .page-item.active .page-link {
            background-color: #6A1B9A;
            border-color: #6A1B9A;
            color: white;
        }
        
        .page-link:hover {
            color: #4A0072;
            background-color: #f8f9fc;
            border-color: #d1d3e2;
        }
        
        .badge-attendance-yes {
            background-color: #28a745;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }
        
        .badge-attendance-no {
            background-color: #dc3545;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }
        
        .filter-badge {
            background-color: #6A1B9A;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }
        
        .view-btn {
            background-color: #6A1B9A;
            border-color: #6A1B9A;
            color: white;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.35rem;
        }
        
        .view-btn:hover {
            background-color: #4A0072;
            border-color: #4A0072;
            color: white;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">ELEVATE Registrations</h5>
                                <p class="text-sm mb-0">
                                    List of all ELEVATE 2025 registrations.
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $registrations->total() }} Registrations
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="search-section">
                        <form method="GET" action="{{ route('admin.elevate-registrations.index') }}">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control search-input" 
                                               name="search" 
                                               placeholder="Search by company, ELEVATE ID, city, email, or name..." 
                                               value="{{ request('search') }}">
                                        <button class="btn search-btn" type="submit">
                                            <i class="fas fa-search me-1"></i> Search
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select name="attendance" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="">All Attendance</option>
                                        <option value="yes" {{ request('attendance') == 'yes' ? 'selected' : '' }}>Attending</option>
                                        <option value="no" {{ request('attendance') == 'no' ? 'selected' : '' }}>Not Attending</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <select name="per_page" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 per page</option>
                                        <option value="25" {{ request('per_page') == 25 || !request('per_page') ? 'selected' : '' }}>25 per page</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                                    </select>
                                </div>
                                <div class="col-md-2 text-end">
                                    @if(request('search') || request('attendance'))
                                        <a href="{{ route('admin.elevate-registrations.index') }}" class="btn clear-btn">
                                            <i class="fas fa-times me-1"></i> Clear
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
                                <th class="sortable" onclick="sortTable('company_name')">
                                    Company Name
                                    @if(request('sort') == 'company_name')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th class="sortable" onclick="sortTable('elevate_2025_id')">
                                    ELEVATE 2025 ID
                                    @if(request('sort') == 'elevate_2025_id')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th>Application Calls</th>
                                <th>City</th>
                                <th>Attendees/Contacts</th>
                                <th class="sortable" onclick="sortTable('attendance')">
                                    Attendance
                                    @if(request('sort') == 'attendance')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th class="sortable" onclick="sortTable('created_at')">
                                    Registered On
                                    @if(request('sort') == 'created_at')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($registrations as $registration)
                                    <tr>
                                        <td><strong>{{ $registration->company_name }}</strong></td>
                                        <td>{{ $registration->elevate_2025_id }}</td>
                                        <td>
                                            @if($registration->elevate_application_call_names)
                                                @foreach($registration->elevate_application_call_names as $callName)
                                                    <span class="badge bg-secondary me-1">{{ $callName }}</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $registration->city ?? '-' }}</td>
                                        <td>
                                            @if($registration->attendees->count() > 0)
                                                <span class="badge bg-info">{{ $registration->attendees->count() }} {{ $registration->attendance == 'yes' ? 'Attendee(s)' : 'Contact(s)' }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($registration->attendance == 'yes')
                                                <span class="badge-attendance-yes">YES</span>
                                            @else
                                                <span class="badge-attendance-no">NO</span>
                                            @endif
                                        </td>
                                        <td>{{ $registration->created_at->format('M d, Y h:i A') }}</td>
                                        <td>
                                            <a href="{{ route('admin.elevate-registrations.show', $registration->id) }}" class="btn view-btn">
                                                <i class="fas fa-eye me-1"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            @if(request('search') || request('attendance'))
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

                    <!-- Pagination -->
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
