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
   
    {{-- Important Information --}}
    <div class="alert alert-info mb-4">
        <h5><i class="fas fa-info-circle"></i> Important Information</h5>
        <ul class="mb-0">
            <li>A confirmation email has been sent to your registered email address.</li>
            <li>Please keep your TIN number safe for future reference.</li>
          
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
    @if(isset($authors) && $authors->count() > 0)
        @php
            $leadAuthor = $authors->firstWhere('is_lead_author', true);
            $attendingAuthors = $authors->where('will_attend', true);
        @endphp
        
        @if($leadAuthor)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-user"></i>
                Lead Author
            </h4>
            
            <div class="info-row">
                <div class="info-label">Name</div>
                <div class="info-value"><strong>{{ $leadAuthor->title ?? '' }} {{ $leadAuthor->first_name ?? '' }} {{ $leadAuthor->last_name ?? '' }}</strong></div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Designation</div>
                <div class="info-value">{{ $leadAuthor->designation ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Email</div>
                <div class="info-value">{{ $leadAuthor->email ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Mobile</div>
                <div class="info-value">{{ $leadAuthor->mobile ?? 'N/A' }}</div>
            </div>
            
            <div class="info-row">
                <div class="info-label">Institution</div>
                <div class="info-value">{{ $leadAuthor->institution ?? 'N/A' }}</div>
            </div>
        </div>
        @endif
        
        {{-- All Authors --}}
        @if($authors->count() > 1)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-users"></i>
                All Authors ({{ $authors->count() }})
            </h4>
            
            @foreach($authors as $index => $author)
            <div class="info-row">
                <div class="info-label">Author {{ $index + 1 }}</div>
                <div class="info-value">
                    <strong>{{ $author->title ?? '' }} {{ $author->first_name ?? '' }} {{ $author->last_name ?? '' }}</strong>
                    @if($author->is_lead_author)
                        <span class="badge bg-primary">Lead Author</span>
                    @endif
                    @if($author->is_presenter)
                        <span class="badge bg-info">Presenter</span>
                    @endif
                    <br>
                    <small class="text-muted">{{ $author->email ?? 'N/A' }}</small>
                    @if($author->institution)
                        <br><small class="text-muted">{{ $author->institution }}</small>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
        
        {{-- Attendees --}}
        @if($attendingAuthors->count() > 0)
        <div class="info-section">
            <h4 class="section-title">
                <i class="fas fa-user-check"></i>
                Attendees ({{ $attendingAuthors->count() }})
            </h4>
            
            @foreach($attendingAuthors as $index => $attendee)
            <div class="info-row">
                <div class="info-label">Attendee {{ $loop->iteration }}</div>
                <div class="info-value">
                    <strong>{{ $attendee->title ?? '' }} {{ $attendee->first_name ?? '' }} {{ $attendee->last_name ?? '' }}</strong>
                    <br>
                    <small class="text-muted">{{ $attendee->email ?? 'N/A' }}</small>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    @elseif(isset($poster->authors) && is_array($poster->authors))
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
            <div class="info-label">Invoice Date</div>
            <div class="info-value">{{ $invoice->created_at ? $invoice->created_at->format('d M Y, h:i A') : 'N/A' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">Base Amount</div>
            <div class="info-value">
                {{ $poster->currency === 'USD' ? '$' : '₹' }} 
                {{ number_format($invoice->price ?? 0, 2) }}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">GST ({{ config('constants.GST_RATE', 18) }}%)</div>
            <div class="info-value">
                {{ $poster->currency === 'USD' ? '$' : '₹' }} 
                {{ number_format($invoice->gst ?? 0, 2) }}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Processing Charges ({{ $invoice->processing_chargesRate ?? 0 }}%)</div>
            <div class="info-value">
                {{ $poster->currency === 'USD' ? '$' : '₹' }} 
                {{ number_format($invoice->processing_charges ?? 0, 2) }}
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Amount</div>
            <div class="info-value">
                <strong>
                    {{ $poster->currency === 'USD' ? '$' : '₹' }} 
                    {{ number_format($invoice->total_final_price ?? 0, 2) }}
                </strong>
            </div>
        </div>
        <div class="info-row">
            <div class="info-label">Amount Paid</div>
            <div class="info-value">
                {{ $poster->currency === 'USD' ? '$' : '₹' }} 
                {{ number_format($invoice->amount_paid ?? 0, 2) }}
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
        <a href="{{ route('poster.register.payment', ['tin_no' => $poster->tin_no]) }}" 
           class="btn btn-download">
            <i class="fas fa-credit-card"></i> Complete Payment
        </a>
        @endif
        
        <button onclick="window.print()" class="btn btn-download">
            <i class="fas fa-print"></i> Print Confirmation
        </button>
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
