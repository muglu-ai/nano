@extends('layouts.dashboard')
@section('title', 'Ticket Allocation Rules')
@section('content')

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h4 font-weight-bold text-dark">Ticket Allocation Rules</h3>
        <a href="{{ route('admin.ticket-allocation-rules.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Rule
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.ticket-allocation-rules.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="event_id" class="form-label">Event</label>
                    <select name="event_id" id="event_id" class="form-select">
                        <option value="">All Events</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->event_name }} ({{ $event->event_year }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="application_type" class="form-label">Application Type</label>
                    <select name="application_type" id="application_type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($applicationTypes as $type)
                            <option value="{{ $type }}" {{ request('application_type') == $type ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('-', ' ', $type)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="is_active" class="form-label">Status</label>
                    <select name="is_active" id="is_active" class="form-select">
                        <option value="1" {{ request('is_active', '1') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        <option value="" {{ request('is_active') === '' ? 'selected' : '' }}>All</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                    <a href="{{ route('admin.ticket-allocation-rules.index') }}" class="btn btn-secondary">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Rules Table -->
    <div class="card">
        <div class="card-body">
            @if($rules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Event</th>
                                <th>Application Type</th>
                                <th>Booth Type / Range</th>
                                <th>Ticket Allocations</th>
                                <th>Status</th>
                                <th>Sort Order</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rules as $rule)
                                <tr>
                                    <td>{{ $rule->id }}</td>
                                    <td>{{ $rule->event ? $rule->event->event_name : 'All Events' }}</td>
                                    <td>{{ $rule->application_type ? ucfirst(str_replace('-', ' ', $rule->application_type)) : 'All Types' }}</td>
                                    <td>
                                        @if($rule->booth_type)
                                            <span class="badge bg-info">{{ $rule->booth_type }}</span>
                                        @else
                                            {{ $rule->booth_area_min }} - {{ $rule->booth_area_max }} sqm
                                        @endif
                                    </td>
                                    <td>
                                        @if($rule->ticket_allocations)
                                            @php
                                                $allocations = is_array($rule->ticket_allocations) ? $rule->ticket_allocations : json_decode($rule->ticket_allocations, true);
                                            @endphp
                                            @if($allocations)
                                                @foreach($allocations as $ticketTypeId => $count)
                                                    @php
                                                        $ticketType = \App\Models\Ticket\TicketType::find($ticketTypeId);
                                                    @endphp
                                                    <span class="badge bg-info me-1">
                                                        {{ $ticketType ? $ticketType->name : "ID: {$ticketTypeId}" }}: {{ $count }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">Not configured</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No allocations</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rule->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $rule->sort_order }}</td>
                                    <td>
                                        <a href="{{ route('admin.ticket-allocation-rules.edit', $rule->id) }}" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.ticket-allocation-rules.destroy', $rule->id) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this rule?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $rules->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    No allocation rules found. <a href="{{ route('admin.ticket-allocation-rules.create') }}">Create one now</a>.
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
