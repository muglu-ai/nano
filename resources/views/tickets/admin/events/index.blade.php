@extends('layouts.dashboard')

@section('title', 'Ticket Events - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                        <h6 class="text-white text-capitalize ps-3">Select Event for Ticket Configuration</h6>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show mx-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Event</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Year</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Location</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dates</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $event->event_name }}</h6>
                                                    @if($event->slug)
                                                        <p class="text-xs text-secondary mb-0">/{{ $event->slug }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">{{ $event->event_year }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $event->event_location }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">
                                                @if($event->start_date)
                                                    {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                                                @else
                                                    {{ $event->event_date }}
                                                @endif
                                                @if($event->end_date)
                                                    - {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @php
                                                $config = \App\Models\Ticket\TicketEventConfig::where('event_id', $event->id)->first();
                                            @endphp
                                            @if($config && $config->is_active)
                                                <span class="badge badge-sm bg-gradient-success">Active</span>
                                            @elseif($config)
                                                <span class="badge badge-sm bg-gradient-warning">Configured</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Not Configured</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <a href="{{ route('admin.tickets.events.setup', $event->id) }}" 
                                               class="text-secondary font-weight-bold text-xs" 
                                               data-toggle="tooltip" 
                                               data-original-title="Configure Tickets">
                                                <i class="fas fa-cog me-2"></i>Configure
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <p class="text-muted">No events found. Please create an event first.</p>
                                            <a href="{{ route('super-admin.events.create') }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-plus me-1"></i>Create Event
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

