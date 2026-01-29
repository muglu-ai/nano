@extends('enquiry.layout')

@section('title', 'Thank You - VISA Clearance Registration')

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
        box-shadow: 0 4px 12px rgba(32, 178, 170, 0.3);
    }

    .thankyou-container h1 {
        color: var(--text-primary);
        margin-bottom: 1rem;
        font-size: 2rem;
    }

    .thankyou-container p {
        color: var(--text-secondary);
        font-size: 1.1rem;
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
    <h1>Thank You!</h1>
    <p>Your VISA clearance registration has been submitted successfully.</p>
    <p>Our team will review your details and get back to you with further instructions.</p>
</div>
@endsection


