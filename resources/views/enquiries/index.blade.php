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
        {{-- Download in Excel --}}
        <div class="mt-2">
            <a href="{{ route('enquiries.export', ['search' => request('search')]) }}" class="btn btn-success">Download All Enquiries</a>
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
    
    {{-- Pagination Info --}}
    <div class="row mt-3">
        <div class="col-md-6">
            <p class="text-muted">
                Showing {{ $enquiries->firstItem() ?? 0 }} to {{ $enquiries->lastItem() ?? 0 }} of {{ $enquiries->total() }} results
            </p>
        </div>
        <div class="col-md-6">
            <div class="d-flex justify-content-end">
                <form method="GET" action="" class="me-3">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 per page</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                    </select>
                </form>
            </div>
        </div>
    </div>
    
    {{-- Pagination Links --}}
    <div class="d-flex justify-content-center">
        {{-- {{ $enquiries->appends(['search' => request('search')])->links() }} --}}
        {{ $enquiries->appends(request()->query())->links() }}
    </div>
</div>
@endsection
