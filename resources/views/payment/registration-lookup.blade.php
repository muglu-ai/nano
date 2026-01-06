@extends('layouts.app')

@section('title', 'Lookup Your Order - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-search"></i> Lookup Your Order</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                        </div>
                    @endif

                    <p class="mb-4">
                        Enter your <strong>TIN Number</strong> and <strong>Email Address</strong> to find your order and make payment.
                    </p>

                    <form method="POST" action="{{ route('registration.payment.lookup.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="tin_no" class="form-label">
                                TIN Number <span class="text-danger">*</span>
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

                        <div class="mb-4">
                            <label for="email" class="form-label">
                                Email Address <span class="text-danger">*</span>
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

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ url('/') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Home
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Lookup Order
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="alert alert-info mb-0">
                        <h6 class="alert-heading"><i class="fas fa-question-circle"></i> Need Help?</h6>
                        <p class="mb-0 small">
                            If you cannot find your order, please check:
                            <ul class="mb-0 small">
                                <li>Your TIN number is correct (check your registration confirmation email)</li>
                                <li>You're using the same email address used during registration</li>
                                <li>Your payment is still pending (already paid orders won't appear)</li>
                            </ul>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        border-radius: 10px;
    }
    .card-header {
        border-radius: 10px 10px 0 0 !important;
    }
    .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
</style>
@endsection

