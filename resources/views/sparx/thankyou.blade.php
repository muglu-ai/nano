@extends('sparx.layout')

@section('title', 'Application Submitted: NANO SparX')

@push('styles')
<style>
    .thankyou-container {
        max-width: 600px;
        margin: 0 auto;
        background: var(--bg-secondary);
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        padding: 3rem;
        text-align: center;
    }
    .success-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-dark) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        color: white;
        font-size: 3rem;
        box-shadow: 0 4px 12px rgba(11, 94, 215, 0.3);
    }
    .reference-box {
        background: #f0f9ff;
        border: 2px dashed var(--primary-color);
        border-radius: 12px;
        padding: 1.5rem;
        margin: 2rem 0;
        font-family: monospace;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--primary-color-dark);
    }
    .next-steps {
        background: var(--progress-bg, #f0f0f0);
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
        font-size: 2rem;
        color: var(--text-primary);
        margin-bottom: 1rem;
    }
    .thankyou-container p {
        font-size: 1.1rem;
        color: var(--text-secondary);
        line-height: 1.6;
        margin-bottom: 1rem;
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
        Keep an eye on your inbox (and spam folder). We're excited to learn more about your idea!
    </p>

    <a href="/" class="btn btn-outline-primary mt-4 px-4">
        Back to Homepage
    </a>
</div>
@endsection
