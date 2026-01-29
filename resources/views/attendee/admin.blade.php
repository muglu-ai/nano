@extends('layouts.dashboard')
@section('title', ucfirst($slug))
@section('content')

<style>
    thead.custom-header {
        background-color: #000; /* Light gray */
        color: #fff; /* Dark text */
    }
    th {
        text-align: left !important;
        padding-left:8px !important;
    }
    .custom-td {
        text-align: start !important;
        padding-left: 8px !important;
    }
    .table-hover tbody tr:hover {
    background-color: #f8f9fa;
    cursor: pointer;
}


</style>
<div class="container-fluid py-2">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- Card header -->
                <h2 class="mb-4">Attendee List</h2>

    <!-- Search Bar -->
    <form method="GET" action="{{ route('visitor.list') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, company, or ID..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <!-- Attendees Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Unique ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Company</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendees as $index => $attendee)
                        <tr>
                            <td>{{ $attendees->firstItem() + $index }}</td>
                            <td>{{ $attendee->unique_id }}</td>
                            <td>{{ $attendee->first_name }} {{ $attendee->last_name }}</td>
                            <td>{{ $attendee->email }}</td>
                            <td>{{ $attendee->company }}</td>
                            <td>
                                <span class="badge bg-{{ $attendee->status == 'approved' ? 'success' : ($attendee->status == 'pending' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($attendee->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No attendees found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
     <!-- Pagination -->
     <div class="d-flex justify-content-center mt-3">
        {{ $attendees->appends(request()->query())->links() }}
    </div>
            </div>
        </div>
    </div>
</div>

                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                @endsection    