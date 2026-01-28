@extends('layouts.poster-registration')

@section('title', 'Registration Successful - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/custom.css') }}">
<style>
    .success-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .success-card {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 15px;
        padding: 3rem 2rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
    }

    .success-icon {
        font-size: 5rem;
        margin-bottom: 1rem;
        animation: scaleIn 0.5s ease-in-out;
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0);
        }
        50% {
            transform: scale(1.2);
        }
        100% {
            transform: scale(1);
        }
    }

    .tin-number {
        font-size: 2rem;
        font-weight: 700;
        background: white;
        color: #28a745;
        padding: 1rem 2rem;
        border-radius: 10px;
        display: inline-block;
        margin: 1rem 0;
    }

    .info-section {
        background: #ffffff;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #212529;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #28a745;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-row {
        display: flex;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: #666;
        width: 40%;
        min-width: 150px;
    }

    .info-value {
        color: #212529;
        flex: 1;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 2rem;
    }

    .btn-download {
        background: #0B5ED7;
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-download:hover {
        background: #084298;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(11, 94, 215, 0.3);
    }

    .form-container {padding: 1rem 0px;}
</style>
@endpush

@section('poster-content')
@php
    // Ensure $poster variable exists for backward compatibility
    if (isset($posterRegistration) && !isset($poster)) {
        $poster = $posterRegistration;
    }
@endphp
<div class="success-container">
    {{-- Success Header --}}
    <div class="success-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1 class="mb-3">Registration Successful!</h1>
        <p class="lead mb-4">Thank you for registering your poster presentation at<br>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</p>
        
        <div class="tin-number">
            TIN: {{ $poster->tin_no ?? 'N/A' }}
        </div>
        
        <p class="mt-4 mb-0">
            @if(isset($invoice) && $invoice->payment_status === 'paid')
                <span class="badge bg-light text-success" style="font-size: 1.1rem; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-check"></i> Payment Completed
                </span>
            @else
                <span class="badge bg-light text-warning" style="font-size: 1.1rem; padding: 0.5rem 1.5rem;">
                    <i class="fas fa-clock"></i> Payment Pending
                </span>
            @endif
        </p>
    </div>

    {{-- Important Information --}}
    <div class="alert alert-info mb-4">
        <h5><i class="fas fa-info-circle"></i> Important Information</h5>
        <ul class="mb-0">
            <li>A confirmation email has been sent to your registered email address.</li>
            <li>Please keep your TIN number safe for future reference.</li>
            <li>You will receive further instructions about poster submission guidelines via email.</li>
        </ul>
    </div>

    {{-- Registration Summary --}}
    <div class="info-section">
        <h4 class="section-title">
            <i class="fas fa-file-alt"></i>
            Registration Summary
        </h4>
        
        <div class="info-row">
            <div class="info-label">TIN Number</div>
            <div class="info-value"><strong>{{ $poster->tin_no ?? 'N/A' }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">PIN Number</div>
            <div class="info-value"><strong>{{ $poster->pin_no ?? 'N/A' }}</strong></div>
        </div>
        <div class="info-row">
            <div class="info-label">Registration Date</div>
            <div class="info-value">{{ $poster->created_at ? $poster->created_at->format('d M Y, h:i A') : 'N/A' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Sector</div>
            <div class="info-value">{{ $poster->sector ?? 'N/A' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Currency</div>
            <div class="info-value">{{ $poster->currency ?? 'INR' }}</div>
        </div>
    </div>

    {{-- Poster Details --}}
    <div class="info-section">
        <h4 class="section-title">
            <i class="fas fa-clipboard"></i>
            Poster Details
        </h4>
        
        <div class="info-row">
            <div class="info-label">Poster Category</div>
            <div class="info-value">{{ $poster->poster_category ?? 'Breaking Boundaries' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Abstract Title</div>
            <div class="info-value"><strong>{{ $poster->abstract_title ?? 'N/A' }}</strong></div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Presentation Mode</div>
            <div class="info-value">{{ $poster->presentation_mode ?? 'Poster only' }}</div>
        </div>
    </div>

    {{-- Lead Author Information --}}
    @if(isset($poster->authors) && is_array($poster->authors))
        @php
            $leadAuthor = collect($poster->authors)->firstWhere('is_lead', true);
        @endphp
        
        @if($leadAuthor)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-user"></i>
                Lead Author
            </h4>
            
            <div class="info-row">
                <div class="info-label">Name</div>
                <div class="info-value"><strong>{{ $leadAuthor['first_name'] ?? '' }} {{ $leadAuthor['last_name'] ?? '' }}</strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $leadAuthor['email'] ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Mobile</div>
                <div class="info-value">{{ $leadAuthor['mobile'] ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Institution</div>
                <div class="info-value">{{ $leadAuthor['institution'] ?? 'N/A' }}</div>
            </div>
        </div>
        @endif
    @endif

    {{-- Payment Information --}}
    @if(isset($invoice))
    <div class="info-section">
        <h4 class="section-title">
            <i class="fas fa-credit-card"></i>
            Payment Information
        </h4>
        
        <div class="info-row">
            <div class="info-label">Invoice Number</div>
            <div class="info-value">{{ $invoice->invoice_no ?? 'N/A' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Amount</div>
            <div class="info-value">
                <strong>
                    {{ $poster->currency === 'USD' ? '$' : '₹' }} 
                    {{ number_format($invoice->final_amount ?? 0, 2) }}
                </strong>
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Payment Status</div>
            <div class="info-value">
                @if($invoice->payment_status === 'paid')
                    <span class="badge bg-success">Paid</span>
                @elseif($invoice->payment_status === 'pending')
                    <span class="badge bg-warning">Pending</span>
                @else
                    <span class="badge bg-secondary">{{ ucfirst($invoice->payment_status ?? 'Unknown') }}</span>
                @endif
            </div>
        </div>
        
        @if($invoice->payment_status === 'paid' && isset($invoice->payment_date))
        <div class="info-row">
            <div class="info-label">Payment Date</div>
            <div class="info-value">{{ \Carbon\Carbon::parse($invoice->payment_date)->format('d M Y, h:i A') }}</div>
        </div>
        @endif
    </div>
    @endif

    {{-- Action Buttons --}}
    <div class="action-buttons">
        @if(isset($invoice) && $invoice->payment_status !== 'paid')
        <a href="{{ route('poster.payment', ['tin_no' => $poster->tin_no]) }}" 
           class="btn btn-download">
            <i class="fas fa-credit-card"></i> Complete Payment
        </a>
        @endif
        
        <button onclick="window.print()" class="btn btn-download">
            <i class="fas fa-print"></i> Print Confirmation
        </button>
        
        <a href="{{ url('/') }}" class="btn btn-outline-primary" style="padding: 0.75rem 2rem; border-radius: 8px; font-weight: 600;">
            <i class="fas fa-home"></i> Go to Home
        </a>
    </div>

    {{-- Next Steps --}}
    <div class="alert alert-success mt-4">
        <h5><i class="fas fa-clipboard-list"></i> Next Steps</h5>
        <ol class="mb-0">
            <li>Check your email for the registration confirmation.</li>
            <li>Complete the payment if not done already.</li>
            <li>Wait for poster submission guidelines via email.</li>
            <li>Prepare your poster according to the provided specifications.</li>
            <li>Submit your poster before the deadline.</li>
        </ol>
    </div>
</div>

@push('scripts')
<script>
// Confetti effect on page load (optional)
document.addEventListener('DOMContentLoaded', function() {
    console.log('Registration successful! TIN: {{ $poster->tin_no ?? 'N/A' }}');
});
</script>
@endpush
@endsection
