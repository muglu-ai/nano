@extends('layouts.dashboard')
@section('title', 'Enquiry List')
@section('content')
<div class="container">
    <h2 class="mb-4">Enquiries</h2>
    <form method="GET" action="" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search enquiries..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Srno</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Contact No.</th>
                    <th>Enquiry Source</th>
                    <th>Enquiry Type</th>
                    <th>Location</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enquiries as $index => $enquiry)
                            <tr>
                                <td>{{ ($enquiries->currentPage() - 1) * $enquiries->perPage() + $index + 1 }}</td>
                        <td>{{ $enquiry->full_name }}</td>
                        <td>{{ $enquiry->email }}</td>
                        <td>{{ $enquiry->phone_country_code }}-{{ $enquiry->phone_number }}</td>
                        <td>{{ $enquiry->referral_source }}</td>
                        <td>{{ $enquiry->interests && $enquiry->interests->isNotEmpty() ? strtoupper($enquiry->interests->pluck('interest_type')->implode(', ')) : 'N/A' }}</td>
                        <td>
                            <div>{{ ucfirst($enquiry->city) }}</div>
                            <div>{{ $enquiry->state }}</div>
                            <div>{{ $enquiry->country }}</div>
                        </td>
                        <td>{{ $enquiry->comments }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No enquiries found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="d-flex justify-content-center">
        {{ $enquiries->appends(['search' => request('search')])->links() }}
    </div>
</div>
@endsection
