@extends('tickets.public.layout')

@section('title', 'Review Registration')

@push('styles')
<style>
    .preview-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .preview-container .registration-progress {
        margin-bottom: 2rem;
    }

    .preview-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 2.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        margin-bottom: 2rem;
    }

    .preview-section {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #fff;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(102, 126, 234, 0.5);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: rgba(255, 255, 255, 0.7);
        flex: 1;
    }

    .info-value {
        color: #fff;
        flex: 1;
        text-align: right;
    }

    .price-breakdown {
        background: rgba(102, 126, 234, 0.1);
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        font-size: 1rem;
    }

    .price-row.total {
        font-size: 1.5rem;
        font-weight: 700;
        padding-top: 1rem;
        margin-top: 1rem;
        border-top: 2px solid rgba(255, 255, 255, 0.2);
        color: #fff;
    }

    .price-label {
        color: rgba(255, 255, 255, 0.8);
    }

    .price-value {
        color: #fff;
        font-weight: 600;
    }

    .btn-edit {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: #fff;
    }

    .btn-edit:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="preview-container">
    <!-- Progress Bar -->
    @include('tickets.public.partials.progress-bar', ['currentStep' => 2])
    
    <div class="preview-card">
        <h2 class="text-center mb-4" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            Review Your Registration
        </h2>

        <!-- Registration Information -->
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-info-circle me-2"></i>
                Registration Information
            </h4>
            <div class="info-row">
                <span class="info-label">Registration Category:</span>
                <span class="info-value">{{ $registrationCategory->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ticket Type:</span>
                <span class="info-value">{{ $ticketType->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Number of Delegates:</span>
                <span class="info-value">{{ $quantity }}</span>
            </div>
            @if(isset($registrationData['delegates']) && count($registrationData['delegates']) > 0)
            <div class="info-row">
                <span class="info-label">Delegates:</span>
                <span class="info-value">
                    <ul class="list-unstyled mb-0" style="text-align: right;">
                        @foreach($registrationData['delegates'] as $delegate)
                            <li>{{ ($delegate['salutation'] ?? '') }} {{ $delegate['first_name'] }} {{ $delegate['last_name'] }} ({{ $delegate['email'] }})</li>
                        @endforeach
                    </ul>
                </span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Nationality:</span>
                <span class="info-value">{{ $registrationData['nationality'] }}</span>
            </div>
        </div>

        <!-- Organisation Information -->
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-building me-2"></i>
                Organisation Information
            </h4>
            <div class="info-row">
                <span class="info-label">Organisation Name:</span>
                <span class="info-value">{{ $registrationData['organisation_name'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Industry Sector:</span>
                <span class="info-value">{{ $registrationData['industry_sector'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Organisation Type:</span>
                <span class="info-value">{{ $registrationData['organisation_type'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Country:</span>
                <span class="info-value">{{ $registrationData['country'] }}</span>
            </div>
            @if(!empty($registrationData['state']))
            <div class="info-row">
                <span class="info-label">State:</span>
                <span class="info-value">{{ $registrationData['state'] }}</span>
            </div>
            @endif
            @if(!empty($registrationData['city']))
            <div class="info-row">
                <span class="info-label">City:</span>
                <span class="info-value">{{ $registrationData['city'] }}</span>
            </div>
            @endif
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $registrationData['phone'] }}</span>
            </div>
            @if(!empty($registrationData['email']))
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $registrationData['email'] }}</span>
            </div>
            @endif
        </div>

        <!-- GST Information -->
        @if($registrationData['gst_required'] == '1')
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-file-invoice-dollar me-2"></i>
                GST Information
            </h4>
            <div class="info-row">
                <span class="info-label">GSTIN:</span>
                <span class="info-value">{{ $registrationData['gstin'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">GST Legal Name:</span>
                <span class="info-value">{{ $registrationData['gst_legal_name'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">GST Address:</span>
                <span class="info-value">{{ $registrationData['gst_address'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">GST State:</span>
                <span class="info-value">{{ $registrationData['gst_state'] }}</span>
            </div>
        </div>
        @endif

        <!-- Contact Information (Only shown if GST is required) -->
        @if($registrationData['gst_required'] == '1' && !empty($registrationData['contact_name']))
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-user me-2"></i>
                Primary Contact Information (For GST Invoice)
            </h4>
            <div class="info-row">
                <span class="info-label">Name:</span>
                <span class="info-value">{{ $registrationData['contact_name'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ $registrationData['contact_email'] }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Phone:</span>
                <span class="info-value">{{ $registrationData['contact_phone'] }}</span>
            </div>
        </div>
        @endif

        <!-- Price Breakdown -->
        <div class="price-breakdown">
            <h4 class="section-title mb-3">
                <i class="fas fa-calculator me-2"></i>
                Price Breakdown
            </h4>
            <div class="price-row">
                <span class="price-label">Ticket Price ({{ $quantity }} × ₹{{ number_format($unitPrice, 2) }}):</span>
                <span class="price-value">₹{{ number_format($subtotal, 2) }}</span>
            </div>
            <div class="price-row">
                <span class="price-label">GST ({{ $gstRate }}%):</span>
                <span class="price-value">₹{{ number_format($gstAmount, 2) }}</span>
            </div>
            <div class="price-row">
                <span class="price-label">Processing Charge ({{ $processingChargeRate }}%):</span>
                <span class="price-value">₹{{ number_format($processingChargeAmount, 2) }}</span>
            </div>
            <div class="price-row total">
                <span class="price-label">Total Amount:</span>
                <span class="price-value">₹{{ number_format($total, 2) }}</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('tickets.register', $event->slug ?? $event->id) }}" class="btn btn-edit btn-lg">
                <i class="fas fa-arrow-left me-2"></i>
                Edit Registration
            </a>
            <form action="{{ route('tickets.payment.initiate', $event->slug ?? $event->id) }}" method="POST" id="paymentForm">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-credit-card me-2"></i>
                    Proceed to Payment
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we prepare your payment.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Submit the form
        this.submit();
    });
</script>
@endpush

