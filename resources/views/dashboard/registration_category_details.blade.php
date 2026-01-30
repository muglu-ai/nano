@extends('layouts.dashboard')
@section('title', 'Registration Category Details')
@section('content')
<div class="card">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ $category }} Registration Details</h5>
            <a href="{{ route('admin.event.analytics') }}" class="btn btn-secondary btn-sm">Back to Analytics</a>
        </div>
    </div>
    <div class="card-body p-0">
        @if($delegates->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Registration Date</th>
                        <th>Industry Sector</th>
                        <th>Organisation Type</th>
                        <th>TIN Number</th>
                        <th>Organisation Name</th>
                        <th>No of Deleg.</th>
                       
                        <th>Registration Category</th>
                        <th>Registration Type</th>
                       
                        <th>Payment Status</th>
                        <th>Amount_inc_all service taxes</th>
                        <th>Invoice</th>
                        <th>GST Number</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($delegates as $delegate)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($delegate->registration_date)->format('Y-m-d H:i:s') }}</td>
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
                        <td>{{ $delegate->invoice }}</td>
                        <td>{{ $delegate->gst_number ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center p-4">
            <p class="text-muted">No registrations found for {{ $category }}.</p>
        </div>
        @endif
    </div>
    @if($delegates->count() > 0)
    <div class="card-footer bg-light">
        <small class="text-muted">Total Records: {{ $delegates->count() }}</small>
    </div>
    @endif
</div>
@endsection