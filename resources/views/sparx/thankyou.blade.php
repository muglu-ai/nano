@extends('sparx.layout')  <!-- or 'layouts.registration' — use the same as form -->

@section('title', 'Application Submitted: NANO SparX')

@push('styles')
<style>
    .thankyou-container {
        max-width: 700px;
        margin: 4rem auto;
        background: white;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        padding: 3.5rem 2.5rem;
        text-align: center;
    }

    .success-icon {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #10b981, #059669);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        color: white;
        font-size: 3.8rem;
        box-shadow: 0 8px 25px rgba(16,185,129,0.3);
    }

    .reference-box {
        background: #f0fdf4;
        border: 2px dashed #10b981;
        border-radius: 12px;
        padding: 1.5rem;
        margin: 2rem 0;
        font-family: monospace;
        font-size: 1.4rem;
        font-weight: 700;
        color: #065f46;
    }

    .next-steps {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1.8rem;
        margin: 2rem 0;
        text-align: left;
    }

    .next-steps ul {
        padding-left: 1.5rem;
        margin-bottom: 0;
    }

    .thankyou-container h1 {
        font-size: 2.4rem;
        color: #1e293b;
        margin-bottom: 1rem;
    }

    .thankyou-container p {
        font-size: 1.15rem;
        color: #475569;
        line-height: 1.7;
    }
</style>
@endpush

@section('content')
<div class="thankyou-container">
    <div class="success-icon">
        <i class="fas fa-check"></i>
    </div>

    <h1>Application Submitted Successfully!</h1>

    <p class="lead mb-4">
        Thank you for applying to NanoSparX. Your application has been received.
    </p>

    <div class="reference-box">
        Your Application Reference: <br>
        <span style="font-size: 1.6rem;">{{ session('reference_number') ?? 'N/A' }}</span>
    </div>

    <div class="next-steps">
        <h5 class="mb-3">What happens next?</h5>
        <ul>
            <li>You will receive a confirmation email shortly with your application details.</li>
            <li>Our team will review all applications over the next 4–6 weeks.</li>
            <li>Shortlisted applicants will be contacted for the next round (interviews / pitch sessions).</li>
            <li>You can check your application status anytime using your reference number.</li>
        </ul>
    </div>

    @if(session('success'))
        <div class="alert alert-success mt-4">
            {{ session('success') }}
        </div>
    @endif

    <p class="mt-4 text-muted">
        Keep an eye on your inbox (and spam folder). We’re excited to learn more about your idea!
    </p>

    <a href="/" class="btn btn-outline-primary mt-4 px-4">
        Back to Homepage
    </a>
</div>
@endsection