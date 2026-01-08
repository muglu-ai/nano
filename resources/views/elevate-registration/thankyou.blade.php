@extends('elevate-registration.layout')

@section('title', 'Thank You - ELEVATE Registration')

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-check-circle me-2"></i>Thank You!</h2>
        <p>Your registration has been submitted successfully</p>
    </div>

    <div class="form-body text-center">
        <div class="mb-4">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: #28a745;"></i>
        </div>
        
        <h3 class="mb-3">Registration Submitted Successfully</h3>
        
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

        <div class="mt-4">
            <a href="{{ route('elevate-registration.form') }}" class="btn-submit" style="display: inline-block; width: auto; padding: 0.75rem 2rem;">
                Submit Another Registration
            </a>
        </div>
    </div>
</div>
@endsection
