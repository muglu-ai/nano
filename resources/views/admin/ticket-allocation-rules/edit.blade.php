@extends('layouts.dashboard')
@section('title', 'Edit Ticket Allocation Rule')
@section('content')

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="h4 font-weight-bold text-dark">Edit Ticket Allocation Rule</h3>
        <a href="{{ route('admin.ticket-allocation-rules.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.ticket-allocation-rules.update', $rule->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="event_id" class="form-label">Event <small class="text-muted">(Optional)</small></label>
                        <select name="event_id" id="event_id" class="form-select @error('event_id') is-invalid @enderror">
                            <option value="">All Events</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ old('event_id', $rule->event_id) == $event->id ? 'selected' : '' }}>
                                    {{ $event->event_name }} ({{ $event->event_year }})
                                </option>
                            @endforeach
                        </select>
                        @error('event_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="application_type" class="form-label">Application Type <small class="text-muted">(Optional)</small></label>
                        <select name="application_type" id="application_type" class="form-select @error('application_type') is-invalid @enderror">
                            <option value="">All Application Types</option>
                            @foreach($applicationTypes as $type)
                                <option value="{{ $type }}" {{ old('application_type', $rule->application_type) == $type ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('-', ' ', $type)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('application_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="booth_area_min" class="form-label">Minimum Booth Area (sqm) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('booth_area_min') is-invalid @enderror" 
                               id="booth_area_min" name="booth_area_min" 
                               value="{{ old('booth_area_min', $rule->booth_area_min) }}" min="0" required>
                        @error('booth_area_min')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="booth_area_max" class="form-label">Maximum Booth Area (sqm) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('booth_area_max') is-invalid @enderror" 
                               id="booth_area_max" name="booth_area_max" 
                               value="{{ old('booth_area_max', $rule->booth_area_max) }}" min="0" required>
                        @error('booth_area_max')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Ticket Allocations <span class="text-danger">*</span></label>
                    <small class="text-muted d-block mb-2">Select ticket types and specify count for each</small>
                    
                    @php
                        $currentAllocations = is_array($rule->ticket_allocations) ? $rule->ticket_allocations : json_decode($rule->ticket_allocations, true) ?? [];
                    @endphp
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Ticket Type</th>
                                    <th>Category</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ticketTypes as $ticketType)
                                    <tr>
                                        <td>
                                            <label class="form-check-label">
                                                {{ $ticketType->name }}
                                            </label>
                                        </td>
                                        <td>{{ $ticketType->category->name ?? 'N/A' }}</td>
                                        <td>
                                            <input type="number" 
                                                   class="form-control form-control-sm" 
                                                   name="ticket_allocations[{{ $ticketType->id }}]" 
                                                   value="{{ old("ticket_allocations.{$ticketType->id}", $currentAllocations[$ticketType->id] ?? 0) }}" 
                                                   min="0" 
                                                   style="width: 100px;">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @error('ticket_allocations')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                               id="sort_order" name="sort_order" 
                               value="{{ old('sort_order', $rule->sort_order) }}" min="0">
                        <small class="text-muted">Lower numbers appear first</small>
                        @error('sort_order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', $rule->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Rule
                    </button>
                    <a href="{{ route('admin.ticket-allocation-rules.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
