@extends('elevate-registration.layout')

@section('title', 'Preview - ELEVATE Registration')

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-eye me-2"></i>Review Your Registration</h2>
        <p>Please review your information before submitting</p>
    </div>

    <div class="form-body">
        <!-- Company Information -->
        <div class="form-section">
            <div class="section-header">
                <h5>Company Information</h5>
            </div>

            <table class="table table-bordered">
                <tr>
                    <th style="width: 40%;">Company Name:</th>
                    <td>{{ $formData['company_name'] }}</td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td>{{ $formData['address'] }}</td>
                </tr>
                <tr>
                    <th>City:</th>
                    <td>{{ $formData['city'] }}</td>
                </tr>
                <tr>
                    <th>Postal Code:</th>
                    <td>{{ $formData['postal_code'] }}</td>
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
                        @foreach($formData['elevate_application_call_names'] as $callName)
                            • {{ $callName }}<br>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th>ELEVATE 2025 ID:</th>
                    <td>{{ $formData['elevate_2025_id'] }}</td>
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
                        <strong style="color: {{ $formData['attendance'] == 'yes' ? '#28a745' : '#dc3545' }};">
                            {{ strtoupper($formData['attendance']) }}
                        </strong>
                    </td>
                </tr>
                @if($formData['attendance'] == 'no' && !empty($formData['attendance_reason']))
                <tr>
                    <th>Reason:</th>
                    <td>{{ $formData['attendance_reason'] }}</td>
                </tr>
                @endif
            </table>
        </div>

        @if(!empty($formData['attendees']))
        <!-- Attendees/Contact Information -->
        <div class="form-section">
            <div class="section-header">
                <h5>{{ $formData['attendance'] == 'yes' ? 'Attendees Information' : 'Contact Information' }}</h5>
            </div>

            @foreach($formData['attendees'] as $index => $attendee)
            <div class="attendee-block" style="margin-bottom: 1.5rem;">
                <h6 style="color: var(--primary-color); margin-bottom: 1rem;">
                    {{ $formData['attendance'] == 'yes' ? 'Attendee' : 'Contact' }} {{ $index + 1 }}
                </h6>
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 40%;">Name:</th>
                        <td>{{ $attendee['salutation'] }} {{ $attendee['first_name'] }} {{ $attendee['last_name'] }}</td>
                    </tr>
                    <tr>
                        <th>Designation:</th>
                        <td>{{ $attendee['job_title'] ?? '-' }}</td>
                    </tr>
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

        <!-- Action Buttons -->
        <div class="form-section mt-4">
            <form action="{{ route('elevate-registration.submit') }}" method="POST" id="finalSubmitForm">
                @csrf
                <input type="hidden" name="session_id" value="{{ $session->session_id }}">
                
                <div class="d-flex gap-3 justify-content-between">
                    <a href="{{ route('elevate-registration.form') }}" class="btn btn-secondary" style="flex: 1; padding: 1rem;">
                        <i class="fas fa-edit me-2"></i>Edit Registration
                    </a>
                    <button type="submit" class="btn-submit" style="flex: 1;">
                        <i class="fas fa-check me-2"></i>Confirm & Submit
                    </button>
                </div>
            </form>
        </div>
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
    
    .btn-secondary {
        background: #6c757d;
        color: white;
        border: none;
        padding: 1rem 3rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        color: white;
        text-decoration: none;
    }
    
    .d-flex {
        display: flex;
    }
    
    .gap-3 {
        gap: 1rem;
    }
    
    .justify-content-between {
        justify-content: space-between;
    }
</style>
@endpush

@push('scripts')
<script>
    document.getElementById('finalSubmitForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const form = this;
        const submitBtn = this.querySelector('button[type="submit"]');
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        
        @if(config('constants.RECAPTCHA_ENABLED', false))
        // Execute reCAPTCHA
        if (typeof grecaptcha !== 'undefined' && grecaptcha.enterprise) {
            grecaptcha.enterprise.ready(function() {
                grecaptcha.enterprise.execute('{{ config('services.recaptcha.site_key') }}', { action: 'submit' })
                    .then(function(token) {
                        // Add token to form
                        const tokenInput = document.createElement('input');
                        tokenInput.type = 'hidden';
                        tokenInput.name = 'g-recaptcha-response';
                        tokenInput.value = token;
                        form.appendChild(tokenInput);
                        
                        // Submit form
                        form.submit();
                    })
                    .catch(function(err) {
                        console.error('reCAPTCHA error:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'reCAPTCHA Error',
                            text: 'reCAPTCHA verification failed. Please try again.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#6A1B9A'
                        });
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Confirm & Submit';
                    });
            });
        } else {
            form.submit();
        }
        @else
        form.submit();
        @endif
    });
</script>
@endpush
@endsection
