@extends('layouts.dashboard')
@section('title', 'ELEVATE Registration Details')
@section('content')

    <style>
        .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #6A1B9A;
            color: white;
            border-bottom: none;
        }
        
        .card-header h5 {
            color: white;
            font-weight: 600;
            margin: 0;
        }
        
        .table th {
            background-color: #f8f9fc;
            font-weight: 600;
            width: 40%;
        }
        
        .badge-attendance-yes {
            background-color: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
        }
        
        .badge-attendance-no {
            background-color: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
        }
        
        .back-btn {
            background-color: #6A1B9A;
            border-color: #6A1B9A;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.35rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .back-btn:hover {
            background-color: #4A0072;
            border-color: #4A0072;
            color: white;
            text-decoration: none;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <a href="{{ route('admin.elevate-registrations.index') }}" class="back-btn mb-3">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
                
                <!-- Company Information -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-building me-2"></i>Company Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Company Name:</th>
                                <td>{{ $registration->company_name }}</td>
                            </tr>
                            <tr>
                                <th>Sector:</th>
                                <td>{{ $registration->sector ?? '-' }}</td>
                            </tr>
                            @if(!empty($registration->address))
                            <tr>
                                <th>Address:</th>
                                <td>{{ $registration->address }}</td>
                            </tr>
                            @endif
                            <tr>
                                <th>City:</th>
                                <td>{{ $registration->city ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Postal Code:</th>
                                <td>{{ $registration->postal_code }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Elevate Application Information -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-star me-2"></i>Elevate Application Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Elevate Application Call Name:</th>
                                <td>
                                    @if($registration->elevate_application_call_names)
                                        @foreach($registration->elevate_application_call_names as $callName)
                                            <span class="badge bg-secondary me-1">{{ $callName }}</span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>ELEVATE 2025 ID:</th>
                                <td>{{ $registration->elevate_2025_id }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Attendance Information -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-calendar-check me-2"></i>Attendance Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Attendance:</th>
                                <td>
                                    @if($registration->attendance == 'yes')
                                        <span class="badge-attendance-yes">YES</span>
                                    @else
                                        <span class="badge-attendance-no">NO</span>
                                    @endif
                                </td>
                            </tr>
                            @if($registration->attendance == 'no' && $registration->attendance_reason)
                            <tr>
                                <th>Reason:</th>
                                <td>{{ $registration->attendance_reason }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Attendees/Contact Information -->
                @if($registration->attendees->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-users me-2"></i>{{ $registration->attendance == 'yes' ? 'Attendees Information' : 'Contact Information' }}</h5>
                    </div>
                    <div class="card-body">
                        @foreach($registration->attendees as $index => $attendee)
                        <div class="mb-4 {{ !$loop->last ? 'border-bottom pb-4' : '' }}">
                            <h6 class="text-primary mb-3">{{ $registration->attendance == 'yes' ? 'Attendee' : 'Contact' }} {{ $index + 1 }}</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Name:</th>
                                    <td>{{ $attendee->salutation }} {{ $attendee->first_name }} {{ $attendee->last_name }}</td>
                                </tr>
                                <tr>
                                    <th>Designation:</th>
                                    <td>{{ $attendee->job_title ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $attendee->email }}</td>
                                </tr>
                                <tr>
                                    <th>Mobile Number:</th>
                                    <td>{{ $attendee->phone_number }}</td>
                                </tr>
                            </table>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Registration Metadata -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-info-circle me-2"></i>Registration Details</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Registered On:</th>
                                <td>{{ $registration->created_at->format('M d, Y h:i A') }}</td>
                            </tr>
                            <tr>
                                <th>Last Updated:</th>
                                <td>{{ $registration->updated_at->format('M d, Y h:i A') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
