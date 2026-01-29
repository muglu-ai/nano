@extends('layouts.startup-zone')

@section('title', 'Find Your Payment - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Find Your Payment</h5>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @elseif(session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif

                    <p class="mb-3">
                        Please enter your <strong>Application ID</strong>, <strong>TIN No</strong>, or <strong>Invoice Number</strong>.
                        We will redirect you back to the correct payment page.
                    </p>

                    <form method="POST" action="{{ route('payment.lookup.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="application_id" class="form-label">Application ID</label>
                            <input
                                type="text"
                                name="application_id"
                                id="application_id"
                                class="form-control @error('application_id') is-invalid @enderror"
                                value="{{ old('application_id') }}"
                                placeholder="e.g. BTS-2026-EXH-123456"
                            >
                            @error('application_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Your unique application identifier</small>
                        </div>

                        <div class="mb-3">
                            <label for="tin_no" class="form-label">TIN No</label>
                            <input
                                type="text"
                                name="tin_no"
                                id="tin_no"
                                class="form-control @error('tin_no') is-invalid @enderror"
                                value="{{ old('tin_no') }}"
                                placeholder="e.g. BTS-2026-EXH-123456"
                            >
                            @error('tin_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Tax Identification Number</small>
                        </div>

                        <div class="mb-3">
                            <label for="invoice_no" class="form-label">Invoice Number</label>
                            <input
                                type="text"
                                name="invoice_no"
                                id="invoice_no"
                                class="form-control @error('invoice_no') is-invalid @enderror"
                                value="{{ old('invoice_no', session('invoice_hint')) }}"
                                placeholder="Enter your invoice number"
                            >
                            @error('invoice_no')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Your invoice reference number</small>
                        </div>

                        <div class="d-flex justify-content-between">
                        {{--
                            <a href="{{ url('/') }}" class="btn btn-secondary">
                                Back to Home
                            </a>
                            --}}
                            <button type="submit" class="btn btn-primary">
                                Continue to Payment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

