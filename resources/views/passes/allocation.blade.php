@extends('layouts.dashboard')
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('title', 'Passes Allocation')

@section('content')
<style>
    .search-box {
        /* background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); */
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }
    
    .search-box h5 {
        color: white;
        margin-bottom: 20px;
        font-weight: 600;
    }
    
    .search-input {
        border-radius: 25px;
        border: none;
        padding: 12px 20px;
        font-size: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .search-input:focus {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        outline: none;
    }
    
    .stats-cards {
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .stat-card .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 10px;
    }
    
    .stat-card .stat-label {
        color: #6c757d;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .table-container {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 15px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.9rem;
        letter-spacing: 0.5px;
    }
    
    .table tbody tr {
        transition: background-color 0.3s ease;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .table tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    .pass-count {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        display: inline-block;
        min-width: 60px;
        text-align: center;
    }
    
    .pagination-container {
        margin-top: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }
    
    .pagination-container .btn {
        border-radius: 25px;
        padding: 8px 16px;
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .pagination-container .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .pagination-container .btn-secondary {
        background: #6c757d;
    }
    
    .pagination-container .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }
    
    .per-page-selector {
        margin-left: 20px;
    }
    
    .per-page-selector select {
        border-radius: 20px;
        border: 1px solid #dee2e6;
        padding: 8px 16px;
        font-weight: 600;
    }
    
    /* Ticket Allocations Styling */
    .ticket-allocations {
        max-width: 300px;
    }
    
    /* Consumed Passes Styling */
    .consumed-passes {
        max-width: 300px;
    }
    
    .consumed-passes .badge {
        font-size: 0.8rem;
        padding: 4px 8px;
        margin-bottom: 4px;
    }
    
    .consumed-passes .badge.bg-danger {
        background-color: #dc3545 !important;
    }
    
    .consumed-passes .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    
    .ticket-item {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 3px;
    }
    
    .ticket-item .badge {
        font-size: 0.75rem;
        padding: 4px 8px;
        white-space: nowrap;
    }
    
    .ticket-item .badge.bg-info {
        background-color: #17a2b8 !important;
        color: white;
        min-width: 80px;
        text-align: center;
    }
    
    .ticket-item .badge.bg-secondary {
        background-color: #6c757d !important;
        color: white;
        min-width: 30px;
        text-align: center;
    }
    
    /* Ensure ticket allocation column has enough space */
    .table td:nth-child(5) {
        min-width: 200px;
        max-width: 300px;
    }
    
    /* Sortable Table Headers */
    .sortable-header {
        cursor: pointer;
        user-select: none;
        position: relative;
        transition: all 0.3s ease;
    }
    
    .sortable-header:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%) !important;
        transform: translateY(-1px);
    }
    
    .sortable-header::after {
        content: '↕';
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 14px;
        opacity: 0.7;
        transition: all 0.3s ease;
    }
    
    .sortable-header.sort-asc::after {
        content: '↑';
        opacity: 1;
        color: #fff;
    }
    
    .sortable-header.sort-desc::after {
        content: '↓';
        opacity: 1;
        color: #fff;
    }
    
    .sortable-header:hover::after {
        opacity: 1;
        transform: translateY(-50%) scale(1.1);
    }
    
    /* Loading State */
    .table-loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    .sort-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-left: 8px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Modal Input Styling */
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0;
    }
    
    .modal-body {
        padding: 30px;
    }
    
    .modal-body .form-control {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 16px;
        transition: all 0.3s ease;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .modal-body .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        outline: none;
    }
    
    .modal-body .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .modal-body .form-text {
        color: #6c757d;
        font-size: 12px;
        margin-top: 4px;
    }
    
    .ticket-allocation-row {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        border: 1px solid #e9ecef;
    }
    
    .ticket-allocation-row .form-label {
        color: #495057;
        font-weight: 500;
        margin-bottom: 5px;
    }
    
    .ticket-allocation-row .form-control {
        background-color: white;
        border: 2px solid #dee2e6;
    }
    
    .ticket-allocation-row .form-control:focus {
        border-color: #667eea;
        background-color: white;
    }
    
    .modal-footer {
        background-color: #f8f9fa;
        border-radius: 0 0 15px 15px;
        padding: 20px 30px;
    }
    
    .modal-footer .btn {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .modal-footer .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .modal-footer .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .modal-footer .btn-secondary {
        background-color: #6c757d;
        border: none;
    }
    
    .modal-footer .btn-secondary:hover {
        background-color: #5a6268;
        transform: translateY(-1px);
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                                 <div>
                     <h4 class="mb-1">Passes Allocation - All</h4>
                     <p class="text-muted mb-0">View approved exhibitors who need passes allocated</p>
                 </div>
                {{-- <div>
                    <a href="{{ route('invoice.list') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Invoices
                    </a>
                </div> --}}
            </div>

                         <!-- Search Box -->
             <div class="search-box">
                 <h5 class="text-dark"><i class="fas fa-search me-2"></i>Search Exhibitors</h5>
                <form method="GET" action="{{ route('passes.allocation') }}" class="row g-3">
                    <div class="col-md-8">
                        <input type="text" 
                               name="search" 
                               class="form-control search-input" 
                               placeholder="Search by company name, application ID, user name, or email..."
                               value="{{ $search }}">
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-control search-input">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 per page</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-light w-100">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </form>
            </div>

                         <!-- Statistics Cards -->
             <div class="row stats-cards">
                 <div class="col-md-3">
                     <div class="stat-card">
                         <div class="stat-number">{{ $totalStats['total_exhibitors'] }}</div>
                         <div class="stat-label">Total Exhibitors</div>
                     </div>
                 </div>
                 <div class="col-md-3">
                     <div class="stat-card">
                         <div class="stat-number">{{ $totalStats['total_stall_manning'] }}</div>
                         <div class="stat-label">Exhibitor Passes</div>
                     </div>
                 </div>
                 {{-- <div class="col-md-3">
                     <div class="stat-card">
                         <div class="stat-number">{{ $totalStats['total_complimentary_delegates'] }}</div>
                         <div class="stat-label">Complimentary Passes</div>
                     </div>
                 </div> --}}
                 <div class="col-md-3">
                     <div class="stat-card">
                         <div class="stat-number">{{ $totalStats['total_ticket_allocations'] }}</div>
                         <div class="stat-label">Complimentary Passes</div>
                     </div>
                 </div>
             </div>

            <!-- Results Table -->
            <div class="table-container">
                @if($applications->count() > 0)
                    <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="sortable-header" data-sort="company_name" data-order="{{ request('sort') == 'company_name' ? request('order') : '' }}" style="white-space: normal;">
                                        Exhibitor Name
                                        @if(request('sort') == 'company_name')
                                            <span class="sort-spinner"></span>
                                        @endif
                                    </th>
                                    <th class="sortable-header" data-sort="stall_category" data-order="{{ request('sort') == 'stall_category' ? request('order') : '' }}" style="white-space: normal;">
                                        Stall Category
                                        @if(request('sort') == 'stall_category')
                                            <span class="sort-spinner"></span>
                                        @endif
                                    </th>
                                    {{-- <th class="sortable-header" data-sort="stall_manning_count" data-order="{{ request('sort') == 'stall_manning_count' ? request('order') : '' }}" style="white-space: normal;">
                                        Exhibitor Passes Allocated
                                        @if(request('sort') == 'stall_manning_count')
                                            <span class="sort-spinner"></span>
                                        @endif
                                    </th> --}}
                                    {{-- <th class="sortable-header" data-sort="complimentary_delegate_count" data-order="{{ request('sort') == 'complimentary_delegate_count' ? request('order') : '' }}" style="white-space: normal;">
                                        Complimentary Passes Allocated
                                        @if(request('sort') == 'complimentary_delegate_count')
                                            <span class="sort-spinner"></span>
                                        @endif
                                    </th> --}}
                                    <th style="white-space: normal;">Complimentary Passes</th>
                                    <th style="white-space: normal;">Consumed Passes</th>
                                    <th class="sortable-header" data-sort="total_passes" data-order="{{ request('sort') == 'total_passes' ? request('order') : '' }}" style="white-space: normal;">
                                        Total Passes
                                        @if(request('sort') == 'total_passes')
                                            <span class="sort-spinner"></span>
                                        @endif
                                    </th>
                                    <th style="white-space: normal;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($applications as $application)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-gradient-primary rounded-circle me-3">
                                                    <span class="text-white fw-bold">
                                                        {{ strtoupper(substr($application->company_name ?? 'N/A', 0, 2)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $application->company_name ?? 'N/A' }}</h6>
                                                    <small class="text-muted">{{ $application->user->email ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        {{-- <td>
                                            <span class="badge bg-info">{{ $application->application_id ?? 'N/A' }}</span>
                                        </td> --}}
                                        {{-- <td>
                                            <strong>{{ $application->company_name ?? 'N/A' }}</strong>
                                            @if($application->billingDetail)
                                                <br><small class="text-muted">{{ $application->billingDetail->billing_company ?? 'N/A' }}</small>
                                            @endif
                                        </td> --}}
                                        <td>
                                            <span class="badge bg-secondary">{{ $application->stall_category ?? 'N/A' }}</span>
                                            @if($application->allocated_sqm)
                                                <br><small class="text-muted">{{ $application->allocated_sqm }} sqm</small>
                                            @endif
                                        </td>
                                        {{-- <td>
                                            @if($application->exhibitionParticipant && $application->exhibitionParticipant->stall_manning_count > 0)
                                                <span class="pass-count">{{ $application->exhibitionParticipant->stall_manning_count }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td> --}}
                                        {{-- <td>
                                            @if($application->exhibitionParticipant && $application->exhibitionParticipant->complimentary_delegate_count > 0)
                                                <span class="pass-count">{{ $application->exhibitionParticipant->complimentary_delegate_count }}</span>
                                            @else
                                                <span class="text-muted">0</span>
                                            @endif
                                        </td> --}}
                                        <td>
                                            @if($application->exhibitionParticipant)
                                                @php
                                                    $tickets = $application->exhibitionParticipant->tickets();
                                                @endphp
                                                @if(count($tickets) > 0)
                                                    <div class="ticket-allocations">
                                                        @foreach($tickets as $ticket)
                                                            <div class="ticket-item mb-1">
                                                                <span class="badge bg-info me-1" style="min-width: 80px; display: inline-block;">{{ $ticket['name'] ?? 'Unknown Ticket' }}</span>
                                                                <span class="badge bg-secondary">{{ $ticket['count'] ?? 0 }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">No tickets allocated</span>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($application->exhibitionParticipant)
                                                @php
                                                    $consumedTickets = $application->consumedTickets ?? [];
                                                    $consumedStallManning = $application->consumedStallManning ?? 0;
                                                    $consumedComplimentary = $application->consumedComplimentary ?? 0;
                                                    $ticketTypes = ['VIP Pass', 'Premium', 'Exhibitor', 'Service Pass', 'Business Visitor Pass'];
                                                    $hasConsumed = $consumedStallManning > 0 || $consumedComplimentary > 0 || array_sum($consumedTickets) > 0;
                                                @endphp
                                                @if($hasConsumed)
                                                    <div class="consumed-passes">
                                                        {{-- <div class="mb-1">
                                                            <span class="badge bg-danger me-1">Exhibitor: {{ $consumedStallManning }}</span>
                                                        </div> --}}
                                                        @foreach($ticketTypes as $ticketType)
                                                            @if(isset($consumedTickets[$ticketType]) && $consumedTickets[$ticketType] > 0)
                                                                <div class="mb-1">
                                                                    <span class="badge bg-warning me-1">{{ $ticketType }}: {{ $consumedTickets[$ticketType] }}</span>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $stallManning = $application->exhibitionParticipant->stall_manning_count ?? 0;
                                                
                                                // Calculate complimentary count from ticketAllocation JSON
                                                $complimentary = 0;
                                                if($application->exhibitionParticipant && $application->exhibitionParticipant->ticketAllocation) {
                                                    $ticketAllocation = json_decode($application->exhibitionParticipant->ticketAllocation, true);
                                                    if(is_array($ticketAllocation)) {
                                                        $complimentary = array_sum($ticketAllocation);
                                                    }
                                                }
                                                
                                                $ticketTotal = 0;
                                                if($application->exhibitionParticipant) {
                                                    $tickets = $application->exhibitionParticipant->tickets();
                                                    $ticketTotal = collect($tickets)->sum('count');
                                                }
                                                $total = $stallManning + $complimentary + $ticketTotal;
                                            @endphp
                                            <span class="badge bg-success fs-6">{{ $total }}</span>
                                        </td>
                                        <td>
                                             <div class="btn-group" role="group">
                                                 <button type="button" class="btn btn-sm btn-primary" onclick="openUpdateModal({{ $application->id }}, '{{ $application->company_name }}', {{ $application->exhibitionParticipant->stall_manning_count ?? 0 }}, {{ $complimentary }}, '{{ $application->exhibitionParticipant->ticketAllocation ?? '{}' }}')">
                                                     <i class="fas fa-edit"></i> Update
                                                 </button>
                                                 {{-- <button type="button" class="btn btn-sm btn-success" onclick="autoAllocatePasses({{ $application->id }}, '{{ $application->company_name }}')">
                                                     <i class="fas fa-magic"></i> Auto
                                                 </button> --}}
                                             </div>
                                         </td>
                                     </tr>
                                 @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        @if($applications->hasPages())
                            {{ $applications->appends(request()->query())->links() }}
                        @endif
                        
                        <div class="per-page-selector">
                            <span class="text-muted me-2">
                                <i class="fas fa-info-circle me-1"></i>
                                Showing {{ $applications->firstItem() ?? 0 }} to {{ $applications->lastItem() ?? 0 }} of {{ $applications->total() }} results
                                @if(request('sort'))
                                    <br><small class="text-muted">Sorted by: <strong>{{ ucfirst(str_replace('_', ' ', request('sort'))) }}</strong> 
                                    ({{ request('order') == 'asc' ? 'A-Z' : 'Z-A' }})
                                    @if(in_array(request('sort'), ['stall_manning_count', 'complimentary_delegate_count', 'total_passes']))
                                        <br><em>Note: Pass count sorting is currently limited to company name order</em>
                                    @endif
                                    </small>
                                @endif
                            </span>
                        </div>
                    </div>
                @else
                                         <div class="text-center py-5">
                         <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                         <h5 class="text-success">All Exhibitors Have Passes!</h5>
                         <p class="text-muted">
                             @if($search)
                                 No exhibitors found matching "{{ $search }}". 
                                 <a href="{{ route('passes.allocation') }}" class="text-primary">Clear search</a>
                             @else
                                 Great news! All approved exhibitors have been allocated their required passes.
                             @endif
                         </p>
                     </div>
                @endif
            </div>
        </div>
    </div>
 </div>

 <!-- Update Passes Allocation Modal -->
 <div class="modal fade" id="updatePassesModal" tabindex="-1" aria-labelledby="updatePassesModalLabel" aria-hidden="true">

     <div class="modal-dialog modal-dialog-centered modal-lg">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="updatePassesModalLabel">Update Passes Allocation</h5>
                 <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
             </div>
             <div class="modal-body">
                 <form id="updatePassesForm">
                     @csrf
                     <input type="hidden" id="updateApplicationId" name="application_id">
                     
                     <div class="mb-3">
                         <label class="form-label">Company Name</label>
                         <input type="text" class="form-control" id="updateCompanyName" readonly>
                     </div>
                     
                     <div class="mb-3">
                         <label for="updateStallManning" class="form-label">Exhibitor Passes Allocated</label>
                         <input type="number" class="form-control" id="updateStallManning" name="stall_manning_count" min="0" required>
                         <div class="form-text">Enter the number of exhibitor passes to allocate</div>
                     </div>
                     
                     {{-- <div class="mb-3">
                         <label for="updateComplimentary" class="form-label">Inaugural Passes Allocated</label>
                         <input type="number" class="form-control" id="updateComplimentary" name="complimentary_delegate_count" min="0" required>
                         <div class="form-text">Enter the number of inaugural passes to allocate</div>
                     </div> --}}
                     
                     <div class="mb-3">
                         <label class="form-label">Ticket Allocations</label>
                         <div id="ticketAllocations">
                             @if($availableTickets && count($availableTickets) > 0)
                                 @foreach($availableTickets as $ticket)
                                     <div class="row mb-2 ticket-allocation-row">
                                         <div class="col-md-6">
                                             <label class="form-label">{{ $ticket->ticket_type }}</label>
                                         </div>
                                         <div class="col-md-6">
                                             <input type="number" 
                                                    class="form-control ticket-count" 
                                                    name="ticket_allocations[{{ $ticket->id }}]" 
                                                    id="ticket_{{ $ticket->id }}" 
                                                    min="0" 
                                                    value="0"
                                                    data-ticket-id="{{ $ticket->id }}"
                                                    data-ticket-name="{{ $ticket->ticket_type }}">
                                         </div>
                                     </div>
                                 @endforeach
                             @else
                                 <div class="alert alert-warning">
                                     <i class="fas fa-exclamation-triangle me-2"></i>
                                     No tickets are currently available. Please contact the administrator to add ticket types.
                                 </div>
                             @endif
                         </div>
                         <div class="form-text">Enter the number of tickets to allocate for each category</div>
                     </div>
                     
                     <div class="mb-3">
                         <label class="form-label">Total Passes</label>
                         <input type="text" class="form-control" id="updateTotalPasses" readonly>
                         <div class="form-text">This will be calculated automatically</div>
                     </div>
                 </form>
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                 <button type="button" class="btn btn-primary" onclick="updatePassesAllocation()">Update Passes</button>
             </div>
         </div>
     </div>
 </div>

 <script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when per_page changes
    document.querySelector('select[name="per_page"]').addEventListener('change', function() {
        this.form.submit();
    });
    
    // Add event listeners for ticket count inputs
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('ticket-count') || 
            e.target.id === 'updateStallManning') {
            updateTotalPasses();
        }
    });
    
    // Clear search functionality
    // if (document.querySelector('input[name="search"]').value) {
    //     const clearButton = document.createElement('button');
    //     clearButton.type = 'button';
    //     clearButton.className = 'btn btn-outline-light ms-2';
    //     clearButton.innerHTML = '<i class="fas fa-times me-2"></i>Clear';
    //     clearButton.onclick = function() {
    //         document.querySelector('input[name="search"]').value = '';
    //         document.querySelector('form').submit();
    //     };
    //     document.querySelector('.search-box form').appendChild(clearButton);
    // }
    
    // Table Sorting Functionality
    const sortableHeaders = document.querySelectorAll('.sortable-header');
    
    sortableHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const sortField = this.dataset.sort;
            const currentOrder = this.dataset.order;
            
            // Determine new order
            let newOrder = 'asc';
            if (currentOrder === 'asc') {
                newOrder = 'desc';
            } else if (currentOrder === 'desc') {
                newOrder = 'asc';
            }
            
            // Add loading state
            document.querySelector('.table-container').classList.add('table-loading');
            
            try {
                // Update URL and reload with new sort parameters
                const url = new URL(window.location);
                url.searchParams.set('sort', sortField);
                url.searchParams.set('order', newOrder);
                
                // Redirect to new URL
                window.location.href = url.toString();
            } catch (error) {
                console.error('Error updating URL:', error);
                // Fallback: remove loading state and show error
                document.querySelector('.table-container').classList.remove('table-loading');
                alert('An error occurred while sorting. Please try again.');
            }
        });
    });
    
    // Apply current sort state to headers
    function applySortState() {
        const currentSort = '{{ request("sort") }}';
        const currentOrder = '{{ request("order") }}';
        
        if (currentSort && currentOrder) {
            const activeHeader = document.querySelector(`[data-sort="${currentSort}"]`);
            if (activeHeader) {
                // Remove previous sort classes
                document.querySelectorAll('.sortable-header').forEach(header => {
                    header.classList.remove('sort-asc', 'sort-desc');
                });
                
                // Add current sort class
                activeHeader.classList.add(`sort-${currentOrder}`);
                
                // Add visual indicator
                activeHeader.style.background = 'linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%)';
            }
        }
    }
    
    // Apply sort state on page load
    applySortState();
    
    // Add hover effects for sortable headers
    sortableHeaders.forEach(header => {
        header.addEventListener('mouseenter', function() {
            if (!this.classList.contains('sort-asc') && !this.classList.contains('sort-desc')) {
                this.style.background = 'linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%)';
            }
        });
        
        header.addEventListener('mouseleave', function() {
            if (!this.classList.contains('sort-asc') && !this.classList.contains('sort-desc')) {
                this.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            }
                 });
     });
 });

 // Passes Allocation Functions
 function openUpdateModal(applicationId, companyName, stallManningCount, complimentaryCount, ticketAllocation) {
     document.getElementById('updateApplicationId').value = applicationId;
     document.getElementById('updateCompanyName').value = companyName;
     document.getElementById('updateStallManning').value = stallManningCount;
     
     // Parse and populate ticket allocations
     try {
         const allocations = JSON.parse(ticketAllocation);
         document.querySelectorAll('.ticket-count').forEach(input => {
             const ticketId = input.dataset.ticketId;
             input.value = allocations[ticketId] || 0;
         });
     } catch (e) {
         console.error('Error parsing ticket allocation:', e);
         // Reset all ticket counts to 0
         document.querySelectorAll('.ticket-count').forEach(input => {
             input.value = 0;
         });
     }
     
     updateTotalPasses();
     
     const modal = new bootstrap.Modal(document.getElementById('updatePassesModal'));
     modal.show();
 }

 function updateTotalPasses() {
     const stallManning = parseInt(document.getElementById('updateStallManning').value) || 0;
     
     // Calculate total ticket allocations (this becomes the complimentary count)
     let complimentary = 0;
     document.querySelectorAll('.ticket-count').forEach(input => {
         complimentary += parseInt(input.value) || 0;
     });
     
     const total = stallManning + complimentary;
     document.getElementById('updateTotalPasses').value = total;
 }

 function updatePassesAllocation() {
     const form = document.getElementById('updatePassesForm');
     const formData = new FormData(form);
     
     // Show loading state
     const updateBtn = document.querySelector('#updatePassesModal .btn-primary');
     const originalText = updateBtn.innerHTML;
     updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
     updateBtn.disabled = true;
     
     fetch('{{ route("passes.update-allocation") }}', {
         method: 'POST',
         body: formData,
         headers: {
             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
         }
     })
     .then(response => response.json())
     .then(data => {
         if (data.success) {
             // Show success message
             Swal.fire({
                 icon: 'success',
                 title: 'Success!',
                 text: data.message,
                 confirmButtonText: 'OK'
             }).then(() => {
                 // Reload the page to show updated data
                 window.location.reload();
             });
         } else {
             throw new Error(data.message || 'An error occurred');
         }
     })
     .catch(error => {
         console.error('Error:', error);
         Swal.fire({
             icon: 'error',
             title: 'Error!',
             text: error.message || 'An error occurred while updating passes allocation.',
             confirmButtonText: 'OK'
         });
     })
     .finally(() => {
         // Reset button state
         updateBtn.innerHTML = originalText;
         updateBtn.disabled = false;
     });
 }

 function autoAllocatePasses(applicationId, companyName) {
     // Show confirmation dialog
     Swal.fire({
         title: 'Auto-allocate Passes?',
         text: `This will automatically calculate and allocate passes for ${companyName} based on their stall size. Continue?`,
         icon: 'question',
         showCancelButton: true,
         confirmButtonColor: '#28a745',
         cancelButtonColor: '#6c757d',
         confirmButtonText: 'Yes, Auto-allocate!',
         cancelButtonText: 'Cancel'
     }).then((result) => {
         if (result.isConfirmed) {
             // Show loading state
             Swal.fire({
                 title: 'Auto-allocating Passes...',
                 text: 'Please wait while we calculate the optimal pass allocation.',
                 allowOutsideClick: false,
                 didOpen: () => {
                     Swal.showLoading();
                 }
             });
             
             // Make API call
             fetch('{{ route("passes.auto-allocate") }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                 },
                 body: JSON.stringify({
                     application_id: applicationId
                 })
             })
             .then(response => response.json())
             .then(data => {
                 if (data.success) {
                     Swal.fire({
                         icon: 'success',
                         title: 'Passes Auto-allocated!',
                         text: data.message,
                         confirmButtonText: 'OK'
                     }).then(() => {
                         // Reload the page to show updated data
                         window.location.reload();
                     });
                 } else {
                     throw new Error(data.message || 'An error occurred');
                 }
             })
             .catch(error => {
                 console.error('Error:', error);
                 Swal.fire({
                     icon: 'error',
                     title: 'Error!',
                     text: error.message || 'An error occurred while auto-allocating passes.',
                     confirmButtonText: 'OK'
                 });
             });
         }
     });
 }

 // Add event listeners for real-time total calculation
 document.addEventListener('DOMContentLoaded', function() {
     const updateStallManning = document.getElementById('updateStallManning');
     
     if (updateStallManning) {
         updateStallManning.addEventListener('input', updateTotalPasses);
     }
 });
 </script>
 @endsection
