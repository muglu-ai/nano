@extends('enquiry.layout')

@section('title', 'Lookup Your Order')

@push('styles')
<style>
    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--progress-inactive);
    }

    .section-title i {
        color: var(--primary-color);
    }

    .help-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 2rem;
    }

    .help-section h6 {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .help-section ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }

    .help-section li {
        margin-bottom: 0.5rem;
        color: var(--text-secondary);
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-search me-2"></i>Lookup Your Order</h2>
        <p>{{ config('constants.EVENT_NAME', 'Event') }} {{ config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        <p class="mb-4" style="color: var(--text-secondary);">
            Enter your <strong>TIN Number</strong> and <strong>Email Address</strong> to find your order and make payment.
        </p>

        <form method="POST" action="{{ route('registration.payment.lookup.submit') }}" id="lookupForm">
            @csrf

            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-file-invoice"></i>
                    <span>Order Information</span>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="tin_no" class="form-label">
                            TIN Number <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            name="tin_no"
                            id="tin_no"
                            class="form-control @error('tin_no') is-invalid @enderror"
                            value="{{ old('tin_no') }}"
                            placeholder="e.g. BTS-2026-EXH-123456"
                            required
                            autofocus
                        >
                        @error('tin_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Your Tax Identification Number (TIN) from your registration confirmation
                        </small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="email" class="form-label">
                            Email Address <span class="required">*</span>
                        </label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            placeholder="your.email@example.com"
                            required
                        >
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> The email address used during registration
                        </small>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ url('/') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Home
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Lookup Order
                    </button>
                </div>
            </div>
        </form>

        <div class="help-section">
            <h6><i class="fas fa-question-circle me-2"></i>Need Help?</h6>
            <p class="mb-2" style="color: var(--text-secondary);">
                If you cannot find your order, please check:
            </p>
            <ul>
                <li>Your TIN number is correct (check your registration confirmation email)</li>
                <li>You're using the same email address used during registration</li>
                <li>Your payment is still pending (already paid orders won't appear)</li>
            </ul>
        </div>
    </div>
</div>
@endsection

