@extends('layouts.dashboard')
@section('title', 'Registration Category Details')
@section('content')
<div class="card">
    <div class="card-header bg-light">
        <div class="row align-items-center g-2">
            <div class="col-md-4 col-12 mb-2 mb-md-0">
                <h5 class="mb-0">{{ $category }} Registration Details</h5>
            </div>
            <div class="col-md-8 col-12">
                <form method="GET" class="row g-2 justify-content-md-end align-items-center">
                    <div class="col-auto">
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Organisation, TIN, GST..." value="{{ request('search') }}" style="min-width:180px;">
                    </div>
                    <div class="col-auto">
                        <select name="payment_status" class="form-select form-select-sm">
                            <option value="">All</option>
                            <option value="Paid" {{ request('payment_status') == 'Paid' ? 'selected' : '' }}>Paid</option>
                            <option value="Not Paid" {{ request('payment_status') == 'Not Paid' ? 'selected' : '' }}>Not Paid</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary px-3">Search</button>
                    </div>
                    <div class="col-auto">
                        <a href="#" class="btn btn-sm btn-success px-3">Download Data in Excel</a>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('admin.event.analytics') }}" class="btn btn-sm btn-secondary px-3">Back to Analytics</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        @php
            $search = request('search');
            $filterStatus = request('payment_status');
            $filteredDelegates = $delegates;
            if ($search) {
                $filteredDelegates = $filteredDelegates->filter(function($item) use ($search) {
                    $search = strtolower($search);
                    return strpos(strtolower($item->company_name ?? ''), $search) !== false
                        || strpos(strtolower($item->tin_number ?? ''), $search) !== false
                        || strpos(strtolower($item->gst_number ?? ''), $search) !== false;
                });
            }
            if ($filterStatus) {
                $filteredDelegates = $filteredDelegates->filter(function($item) use ($filterStatus) {
                    return $item->payment_status === $filterStatus;
                });
            }
        @endphp
        @if($filteredDelegates->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Reg. Date</th>
                        <th>Industry Sector</th>
                        <th>Organisation Type</th>
                        <th>TIN Number</th>
                        <th>Organisation Name</th>
                        <th>No of Deleg.</th>
                       
                        <th>Registration Category</th>
                        <th>Registration Type</th>
                       
                        <th>Payment Status</th>
                        <th>Amount_inc_all service taxes</th>
                        
                        <th>GST Number</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($filteredDelegates as $delegate)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($delegate->registration_date)->format('Y-m-d') }}</td>
                        <td>{{ $delegate->sector ?? 'N/A' }}</td>
                        <td>{{ $delegate->organisation_type ?? 'N/A' }}</td>
                        <td>
                            @if($delegate->tin_number)
                                <a href="{{ route('admin.delegate.details', ['registrationId' => $delegate->registration_id]) }}" class="text-primary text-decoration-underline">
                                    {{ $delegate->tin_number }}
                                </a>
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $delegate->company_name ?? 'N/A' }}</td>
                        <td class="text-center">{{ $delegate->no_of_delegates }}</td>
                       
                        <td>{{ $delegate->registration_category }}</td>
                        <td>{{ $delegate->registration_type }}</td> 
                        <td>
                            <span class="badge {{ $delegate->payment_status == 'Paid' ? 'bg-success' : 'bg-warning' }}">
                                {{ $delegate->payment_status }}
                            </span>
                        </td>
                        <td>{{ $delegate->amount }}</td>
                      
                        <td>{{ $delegate->gst_number ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center p-4">
            <p class="text-muted">No registrations found for the selected criteria.</p>
        </div>
        @endif
    </div>
    @if($filteredDelegates->count() > 0)
    <div class="card-footer bg-light">
        <div class="d-flex justify-content-between align-items-center flex-column flex-md-row">
            <div class="mb-2 mb-md-0">
                @if($filteredDelegates instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Showing {{ $filteredDelegates->firstItem() ?? 0 }} to {{ $filteredDelegates->lastItem() ?? 0 }} of {{ $filteredDelegates->total() }} results
                    </small>
                @else
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Showing {{ $filteredDelegates->count() }} of {{ $delegates->count() }} results
                    </small>
                @endif
            </div>
            <div>
                @if($filteredDelegates instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    {{ $filteredDelegates->appends(request()->query())->links() }}
                @endif
            </div>
        </div>
    </div>
    @endif
    
</div>
@endsection