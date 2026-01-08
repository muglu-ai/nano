@extends('elevate-registration.layout')

@section('title', 'Thank You - ELEVATE Registration')

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-check-circle me-2"></i>Thank You!</h2>
        <p>Your registration has been submitted successfully</p>
    </div>

    <div class="form-body">
        <div class="text-center mb-4">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: #28a745;"></i>
            <h3 class="mb-3 mt-3">Registration Submitted Successfully</h3>
            <p class="mb-4" style="color: var(--text-secondary);">
                Thank you for registering for the Felicitation Ceremony for ELEVATE 2025, ELEVATE Unnati 2025 & ELEVATE Minorities 2025 Winners.
            </p>
            <p class="mb-4" style="color: var(--text-secondary);">
                We have received your registration details and will contact you shortly with further information.
            </p>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        @if($registration || $formData)
            @php
                $data = $registration ? [
                    'company_name' => $registration->company_name,
                    'address' => $registration->address,
                    'country' => $registration->country,
                    'state' => $registration->state,
                    'city' => $registration->city,
                    'postal_code' => $registration->postal_code,
                    'elevate_application_call_names' => $registration->elevate_application_call_names ?? [],
                    'elevate_2025_id' => $registration->elevate_2025_id,
                    'attendance' => $registration->attendance,
                    'attendance_reason' => $registration->attendance_reason,
                    'attendees' => $registration->attendees->map(function($a) {
                        return [
                            'salutation' => $a->salutation,
                            'first_name' => $a->first_name,
                            'last_name' => $a->last_name,
                            'job_title' => $a->job_title,
                            'email' => $a->email,
                            'phone_number' => $a->phone_number,
                        ];
                    })->toArray(),
                ] : $formData;
            @endphp

            <!-- Company Information -->
            <div class="form-section">
                <div class="section-header">
                    <h5>Company Information</h5>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th style="width: 40%;">Company Name:</th>
                        <td>{{ $data['company_name'] }}</td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td>{{ $data['address'] }}</td>
                    </tr>
                    <tr>
                        <th>Country:</th>
                        <td>{{ $data['country'] }}</td>
                    </tr>
                    <tr>
                        <th>State:</th>
                        <td>{{ $data['state'] }}</td>
                    </tr>
                    <tr>
                        <th>City:</th>
                        <td>{{ $data['city'] }}</td>
                    </tr>
                    <tr>
                        <th>Postal Code:</th>
                        <td>{{ $data['postal_code'] }}</td>
                    </tr>
                </table>
            </div>

            <!-- Elevate Application Information -->
            <div class="form-section">
                <div class="section-header">
                    <h5>Elevate Application Information</h5>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th style="width: 40%;">Elevate Application Call Name:</th>
                        <td>
                            @foreach($data['elevate_application_call_names'] as $callName)
                                • {{ $callName }}<br>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>ELEVATE 2025 ID:</th>
                        <td>{{ $data['elevate_2025_id'] }}</td>
                    </tr>
                </table>
            </div>

            <!-- Attendance Information -->
            <div class="form-section">
                <div class="section-header">
                    <h5>Attendance Information</h5>
                </div>

                <table class="table table-bordered">
                    <tr>
                        <th style="width: 40%;">Attendance:</th>
                        <td>
                            <strong style="color: {{ $data['attendance'] == 'yes' ? '#28a745' : '#dc3545' }};">
                                {{ strtoupper($data['attendance']) }}
                            </strong>
                        </td>
                    </tr>
                    @if($data['attendance'] == 'no' && !empty($data['attendance_reason']))
                    <tr>
                        <th>Reason:</th>
                        <td>{{ $data['attendance_reason'] }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            @if(!empty($data['attendees']))
            <!-- Attendees/Contact Information -->
            <div class="form-section">
                <div class="section-header">
                    <h5>{{ $data['attendance'] == 'yes' ? 'Attendees Information' : 'Contact Information' }}</h5>
                </div>

                @foreach($data['attendees'] as $index => $attendee)
                <div class="attendee-block" style="margin-bottom: 1.5rem;">
                    <h6 style="color: var(--primary-color); margin-bottom: 1rem;">
                        {{ $data['attendance'] == 'yes' ? 'Attendee' : 'Contact' }} {{ $index + 1 }}
                    </h6>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%;">Name:</th>
                            <td>{{ $attendee['salutation'] }} {{ $attendee['first_name'] }} {{ $attendee['last_name'] }}</td>
                        </tr>
                        @if(!empty($attendee['job_title']))
                        <tr>
                            <th>Designation:</th>
                            <td>{{ $attendee['job_title'] }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Email:</th>
                            <td>{{ $attendee['email'] }}</td>
                        </tr>
                        <tr>
                            <th>Phone Number:</th>
                            <td>{{ $attendee['phone_number'] }}</td>
                        </tr>
                    </table>
                </div>
                @endforeach
            </div>
            @endif
        @endif

        {{-- <div class="form-section mt-4 text-center">
            <a href="{{ route('elevate-registration.form') }}" class="btn-submit" style="display: inline-block; width: auto; padding: 0.75rem 2rem;">
                Submit Another Registration
            </a>
        </div> --}}
    </div>
</div>

@push('styles')
<style>
    .table {
        width: 100%;
        margin-bottom: 0;
    }
    
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
        padding: 0.75rem;
        border: 1px solid #dee2e6;
    }
    
    .table td {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
    }
    
    .alert-success {
        background-color: #d4edda;
        border-color: #c3e6cb;
        color: #155724;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1rem;
    }
</style>
@endpush
@endsection
