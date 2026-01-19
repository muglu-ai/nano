@extends('delegate.layouts.app')
@section('title', 'Registrations')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-list me-2"></i>My Registrations</h2>
</div>

<div class="card">
    <div class="card-body">
        @if($registrations->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Event</th>
                            <th>Delegates</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrations as $registration)
                            <tr>
                                <td>{{ $registration->company_name }}</td>
                                <td>{{ $registration->event->event_name ?? 'N/A' }} {{ $registration->event->event_year ?? '' }}</td>
                                <td><span class="badge bg-info">{{ $registration->delegates->count() }} delegate(s)</span></td>
                                <td>
                                    <span class="badge bg-{{ $registration->order && $registration->order->status === 'paid' ? 'success' : 'warning' }}">
                                        {{ $registration->order ? ucfirst($registration->order->status) : 'Pending' }}
                                    </span>
                                </td>
                                <td>{{ $registration->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('delegate.registrations.show', $registration->id) }}" class="btn btn-sm btn-primary">View Details</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $registrations->links() }}
        @else
            <p class="text-muted text-center py-4">No registrations found.</p>
        @endif
    </div>
</div>
@endsection
