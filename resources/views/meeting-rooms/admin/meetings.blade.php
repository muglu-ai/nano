@extends('layouts.dashboard')
@section('title', 'Meeting Room Bookings')

@section('content')
<div class="card shadow-sm">
    <div class="card-header bg-light">
        <h5 class="mb-0">Meeting Room Bookings</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Booking ID</th>
                        <th scope="col">Room Type</th>
                        <th scope="col">Date</th>
                        <th scope="col">Time</th>
                        <th scope="col">Company</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $booking->booking_id }}</td>
                            <td>
                                <span class="fw-bold">{{ $booking->roomType->room_type }}</span>
                                <small class="text-muted">({{ $booking->roomType->location }})</small>
                            </td>
                            <td>{{ $booking->formatted_date }}</td>
                            <td>{{ $booking->booking_time }}</td>
                            <td>{{ $booking->application->company_name }}</td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $booking->payment_status === 'paid' ? 'success' : 'warning' }}">
                                    {{ $booking->status_label }}
                                </span>
                            </td>
                            <td>
                                @if($booking->payment_status !== 'paid')
                                    <form action="{{ route('meeting_rooms.admin.mark_paid') }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="booking_id" value="{{ $booking->booking_id }}">
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check me-1"></i>Mark as Paid
                                        </button>
                                    </form>
                                @else
                                    <span class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>Paid
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <div class="text-muted">No bookings found</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection