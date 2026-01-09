@extends('enquiry.layout')

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

    .preview-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e0e0e0;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: var(--text-primary);
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--progress-inactive);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary-color);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e0e0e0;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label {
        font-weight: 600;
        color: var(--text-secondary);
        flex: 1;
    }

    .info-value {
        color: var(--text-primary);
        flex: 1;
        text-align: right;
    }

    .price-breakdown {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        border: 1px solid #e0e0e0;
    }

    .delegates-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.5rem;
    }

    .delegates-table th,
    .delegates-table td {
        padding: 0.6rem 0.75rem;
        border-bottom: 1px solid #e0e0e0;
        color: var(--text-primary);
        font-size: 0.95rem;
    }

    .delegates-table th {
        background: var(--primary-color);
        color: white;
        font-weight: 600;
    }

    .delegates-table tr:last-child td {
        border-bottom: none;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        font-size: 1rem;
        color: var(--text-secondary);
    }

    .price-row.total {
        font-size: 1.5rem;
        font-weight: 700;
        padding-top: 1rem;
        margin-top: 1rem;
        border-top: 2px solid #e0e0e0;
        color: var(--text-primary);
    }

    .price-label {
        color: var(--text-secondary);
    }

    .price-value {
        color: var(--text-primary);
        font-weight: 600;
    }

    .price-row.total .price-label {
        color: var(--text-primary);
        font-weight: 700;
    }

    .price-row.total .price-value {
        color: var(--text-primary);
        font-weight: 700;
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
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-eye me-2"></i>Review Your Registration</h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
        <!-- Progress Bar -->
        @include('tickets.public.partials.progress-bar', ['currentStep' => 2])

        <!-- Registration Information -->
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-info-circle me-2"></i>
                Registration Information
            </h4>
            <div class="info-row">
                <span class="info-label">Registration Category:</span>
                <span class="info-value">{{ $registrationCategory ? $registrationCategory->name : 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Ticket Type:</span>
                <span class="info-value">{{ $ticketType->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Nationality:</span>
                <span class="info-value">{{ $registrationData['nationality'] }}</span>
            </div>
        </div>

        <!-- Delegate Details -->
        @if(isset($registrationData['delegates']) && count($registrationData['delegates']) > 0)
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-users me-2"></i>
                Delegate Details
            </h4>
            <div class="info-row">
                <span class="info-label">Number of Delegates:</span>
                <span class="info-value">{{ $quantity }}</span>
            </div>

            <div class="table-responsive mt-3">
                <table class="delegates-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Delegate Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Job Title</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrationData['delegates'] as $index => $delegate)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $delegate['salutation'] }} {{ $delegate['first_name'] }} {{ $delegate['last_name'] }}</td>
                                <td>{{ $delegate['email'] }}</td>
                                <td>{{ $delegate['phone'] ?? '-' }}</td>
                                <td>{{ $delegate['job_title'] ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

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
                <span class="info-value">{{ $registrationData['company_country'] ?? $registrationData['country'] ?? 'N/A' }}</span>
            </div>
            @if(!empty($registrationData['company_state'] ?? $registrationData['state'] ?? null))
            <div class="info-row">
                <span class="info-label">State:</span>
                <span class="info-value">{{ $registrationData['company_state'] ?? $registrationData['state'] }}</span>
            </div>
            @endif
            @if(!empty($registrationData['company_city'] ?? $registrationData['city'] ?? null))
            <div class="info-row">
                <span class="info-label">City:</span>
                <span class="info-value">{{ $registrationData['company_city'] ?? $registrationData['city'] }}</span>
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
            <form action="{{ route('tickets.payment.initiate', $event->slug ?? $event->id) }}" method="POST" id="proceedToPaymentForm">
                @csrf
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-right me-2"></i>
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
    document.getElementById('proceedToPaymentForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Creating Order...',
            text: 'Please wait while we create your order.',
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

