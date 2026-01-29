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
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Contact Details</th>
                    <th>Organisation</th>
                    <th>Location</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enquiries as $index => $enquiry)
                            <tr>
                                <td>{{ ($enquiries->currentPage() - 1) * $enquiries->perPage() + $index + 1 }}</td>
                        <td>{{ $enquiry->name }}</td>
                        <td>
                            <div><strong>Email:</strong> {{ $enquiry->email }}</div>
                            <div><strong>Phone:</strong> {{ $enquiry->fone }}</div>
                        </td>
                        <td>{{ $enquiry->org }}</td>
                        <td>
                            <div>{{ $enquiry->city }}</div>
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
