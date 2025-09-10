@extends('meeting-rooms.layout')

@section('content')
    <style>
        .event-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .event-subtitle {
            font-size: 0.95rem;
            opacity: 0.9;
            margin: 0.25rem 0 0 0;
            font-weight: 400;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(125, 211, 192, 0.15);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }

        .content-section {
            padding: 2.5rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .section-title i {
            color: var(--semicon-teal);
        }

        .nav-breadcrumb {
            background: var(--light-gray);
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
        }

        .breadcrumb {
            margin: 0;
            background: transparent;
        }

        .breadcrumb-item a {
            color: var(--semicon-teal);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--semicon-teal);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #6b7280;
            font-weight: 500;
        }

        .filters-section {
            background: var(--light-gray);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .filter-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        .form-control,
        .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--semicon-teal);
            box-shadow: 0 0 0 3px rgba(125, 211, 192, 0.1);
        }

        .meeting-card {
            background: white;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .meeting-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .meeting-header {
            display: flex;
            justify-content: between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .meeting-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .meeting-id {
            font-size: 0.85rem;
            color: #6b7280;
            font-family: 'Courier New', monospace;
        }

        .meeting-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .detail-item {
            display: row;
            align-items: center;
            gap: 0.5rem;
            color: #374151;
        }

        .detail-item i {
            color: var(--semicon-teal);
            width: 1.2rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-confirmed {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-pending {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-cancelled {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .meeting-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary-action {
            background: var(--semicon-teal);
            color: white;
        }

        .btn-primary-action:hover {
            background: var(--semicon-teal-dark);
            transform: translateY(-1px);
        }

        .btn-secondary-action {
            background: #6b7280;
            color: white;
        }

        .btn-secondary-action:hover {
            background: #4b5563;
            transform: translateY(-1px);
        }

        .btn-danger-action {
            background: var(--danger-color);
            color: white;
        }

        .btn-danger-action:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 4rem;
            color: var(--semicon-teal);
            margin-bottom: 1rem;
        }

        .footer {
            background: var(--light-gray);
            padding: 1.5rem 2rem;
            text-align: center;
            color: #6b7280;
            font-size: 0.9rem;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .main-container {
                margin: 1rem;
                border-radius: 12px;
            }

            .content-section {
                padding: 1.5rem;
            }

            .header {
                padding: 1rem 1.5rem;
            }

            .event-title {
                font-size: 1.5rem;
            }

            .meeting-details {
                grid-template-columns: 1fr;
            }

            .filter-controls {
                flex-direction: column;
                align-items: stretch;
            }

            .meeting-actions {
                justify-content: center;
            }
        }
    </style>

    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="fw-bold mb-0">Meeting Room Management</h2>
            <a href="{{ route('meeting_rooms.index') }}" class="btn btn-primary" style="background: var(--semicon-teal); border: none;">
            <i class="fas fa-plus me-2"></i>Book New Meeting Room
            </a>
        </div>
        <h5 class="text-muted">Welcome, {{ Auth::user()->name }}</h5>

        <!-- Stats -->
        <div class="content-section">
            <h2 class="section-title">
                <i class="fas fa-calendar-check"></i>
                My Booked Meetings
            </h2>
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number">{{ $bookings->count() }}</div>
                    <div class="stat-label">Total Bookings</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $bookings->where('confirmation_status', 'confirmed')->count() }}</div>
                    <div class="stat-label">Confirmed</div>
                </div>
                {{-- <div class="stat-card">
                    <div class="stat-number">{{ $upcomingCount }}</div>
                    <div class="stat-label">Upcoming</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">{{ $totalHours }}</div>
                    <div class="stat-label">Total Hours</div>
                </div> --}}
            </div>


            <div class="meetings-list">
                <!-- Meeting Cards -->
                @php
                    $i = 1;
                @endphp
                @foreach ($bookings as $booking)
                    <div class="meeting-card">
                        <div class="meeting-header">
                            <div class="flex-grow-1">
                                <div class="meeting-title">
                                    {{ $booking->title ? $booking->title : 'Meeting #' . $i++ }}
                                </div>
                                <div class="meeting-id"> {{ $booking->booking_id }}</div>
                            </div>
                            <span
                                class="status-badge 
                                        @if ($booking->confirmation_status == 'confirmed') status-confirmed
                                        @elseif($booking->confirmation_status == 'pending') status-pending
                                        @elseif($booking->confirmation_status == 'canceled' || $booking->confirmation_status == 'rejected') status-cancelled @endif">
                                {{ ucfirst($booking->confirmation_status) }}
                            </span>

                        </div>

                        <div class="meeting-details-row d-flex flex-wrap align-items-center gap-4 mb-3">
                            <div class="detail-item d-flex align-items-center gap-2">
                                <i class="fas fa-calendar-alt"></i>
                                <strong>Date:</strong>
                                <span>{{ \Carbon\Carbon::parse($booking->booking_date)->format('l, F jS, Y') }}</span>
                            </div>
                            <div class="detail-item d-flex align-items-center gap-2">
                                <i class="fas fa-clock"></i>
                                <strong>Time:</strong>
                                <span>
                                    {{ $booking->slot->start_time }} - {{ $booking->slot->end_time }}
                                    ({{ \Carbon\Carbon::parse($booking->slot->start_time)->diffInHours(\Carbon\Carbon::parse($booking->slot->end_time)) }}
                                    hours)
                                </span>
                            </div>
                            <div class="detail-item d-flex align-items-center gap-2">
                                <i class="fas fa-door-open"></i>
                                <strong>Location:</strong>
                                <span>{{ $booking->roomType->name }} ({{ $booking->roomType->location }})</span>
                            </div>
                            <div class="detail-item d-flex align-items-center gap-2">
                                <i class="fas fa-users"></i>
                                <strong>Expected Attendees:</strong>
                                <span>{{ $booking->roomType->capacity }}</span>
                            </div>
                            <div class="ms-auto">
                                <a href="{{ url('meeting-room-invoice/' . $booking->booking_id) }}" target="_blank" class="btn btn-primary-action btn-action">
                                    <i class="fas fa-file-invoice"></i> View Invoice
                                </a>
                            </div>
                        </div>
                        <style>
                            .meeting-details-row {
                                display: flex;
                                flex-wrap: wrap;
                                gap: 2rem;
                                margin-bottom: 1rem;
                            }

                            .meeting-details-row .detail-item {
                                display: flex;
                                align-items: center;
                                gap: 0.5rem;
                                color: #374151;
                                min-width: 220px;
                            }

                            @media (max-width: 768px) {
                                .meeting-details-row {
                                    flex-direction: column;
                                    gap: 1rem;
                                }

                                .meeting-details-row .detail-item {
                                    min-width: unset;
                                }
                            }
                        </style>


                    </div>
                @endforeach
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('meeting_rooms.index') }}" class="btn btn-lg"
                    style="background: var(--semicon-teal); color: white; padding: 0.75rem 2rem; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-plus me-2"></i>Book New Meeting Room
                </a>
            </div>
        </div>
    @endsection
