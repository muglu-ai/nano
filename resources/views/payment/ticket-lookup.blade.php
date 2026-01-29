@extends('enquiry.layout')

@section('title', 'Lookup Your Ticket Order')

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
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .help-section ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
    }

    .help-section li {
        margin-bottom: 0.5rem;
        color: var(--text-secondary);
    }

    .required {
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-search me-2"></i>Lookup Your Ticket Order</h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
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
            Enter your <strong>Order Number (TIN)</strong> to find your ticket order and make payment.
        </p>

        <form method="POST" action="{{ route('tickets.payment.lookup.submit', $event->slug ?? $event->id) }}" id="lookupForm">
            @csrf

            <div class="form-section">
                <div class="section-title">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Order Information</span>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="tin_no" class="form-label">
                            Order Number (TIN) <span class="required">*</span>
                        </label>
                        <input
                            type="text"
                            name="tin_no"
                            id="tin_no"
                            class="form-control @error('tin_no') is-invalid @enderror"
                            value="{{ $tin ?? session('tin') ?? old('tin_no') }}"
                            placeholder="e.g. TIN-BTS-2026-TKT-123456"
                            required
                            autofocus
                        >
                        @error('tin_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Your Order Number from your ticket registration confirmation email
                        </small>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Lookup Order
                    </button>
                </div>
            </div>
        </form>

        <div class="help-section">
            <h6><i class="fas fa-question-circle"></i>Need Help?</h6>
            <p class="mb-2" style="color: var(--text-secondary);">
                If you cannot find your order, please check:
            </p>
            <ul>
                <li>Your Order Number is correct (check your registration confirmation email)</li>
                <li>You're looking up the correct event</li>
                <li>Your payment is still pending (already paid orders will show payment details)</li>
            </ul>
        </div>
    </div>
</div>
@endsection
