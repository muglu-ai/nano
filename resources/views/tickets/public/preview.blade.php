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
        background: #ffffff;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e0e0e0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: var(--text-primary);
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary-color);
        font-size: 1rem;
    }

    /* Tabular Info Styles */
    .info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .info-table td {
        padding: 0.6rem 0.75rem;
        border: 1px solid #e9ecef;
        font-size: 0.875rem;
        vertical-align: middle;
    }

    .info-table .label-cell {
        background: #f8f9fa;
        font-weight: 600;
        color: #495057;
        width: 40%;
    }

    .info-table .value-cell {
        color: #212529;
        width: 60%;
    }

    .delegates-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 0.5rem;
    }

    .delegates-table th {
        background: var(--primary-color);
        color: white;
        padding: 0.75rem;
        text-align: left;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .delegates-table td {
        padding: 0.65rem 0.75rem;
        border: 1px solid #e9ecef;
        font-size: 0.85rem;
        color: #495057;
    }

    .delegates-table tr:nth-child(even) {
        background: #f8f9fa;
    }

    .delegates-table tr:hover {
        background: #e9ecef;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .delegates-table {
            font-size: 0.75rem;
            overflow-x: auto;
            display: block;
            white-space: nowrap;
        }

        .delegates-table th,
        .delegates-table td {
            padding: 0.5rem 0.4rem;
            min-width: 100px;
            word-wrap: break-word;
            white-space: normal;
        }

        .delegates-table th {
            font-size: 0.7rem;
        }

        .btn-edit,
        .btn-primary {
            padding: 0.75rem 1.25rem !important;
            font-size: 0.9rem !important;
            margin-bottom: 0.5rem;
        }

        .btn-lg {
            padding: 0.75rem 1.25rem !important;
            font-size: 0.9rem !important;
        }
    }

    /* Price Table Styles */
    .price-section {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 10px;
        padding: 1.25rem;
        margin-top: 1.5rem;
        border: 1px solid #dee2e6;
    }

    .price-table {
        width: 100%;
        border-collapse: collapse;
    }

    .price-table td {
        padding: 0.65rem 0.85rem;
        border: 1px solid #e9ecef;
        font-size: 0.9rem;
    }

    .price-table .label-cell {
        background: #ffffff;
        font-weight: 500;
        color: #495057;
        width: 65%;
    }

    .price-table .value-cell {
        background: #ffffff;
        text-align: right;
        font-weight: 600;
        color: #212529;
        width: 35%;
    }

    .price-table .total-row td {
        background: var(--primary-color);
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 700;
        padding: 0.85rem;
    }

    /* Badge styles */
    .day-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-right: 0.25rem;
    }

    .day-badge.primary {
        background: var(--primary-color);
        color: white;
    }

    .day-badge.success {
        background: #28a745;
        color: white;
    }

    .btn-edit {
        background: #ffffff;
        border: 2px solid var(--primary-color);
        color: var(--primary-color);
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-edit:hover {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(11, 94, 215, 0.3);
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
                <i class="fas fa-clipboard-list"></i>
                Registration Information
            </h4>
            <table class="info-table">
                <tr>
                    <td class="label-cell">Ticket Type</td>
                    <td class="value-cell"><strong>{{ $ticketType->name }}</strong></td>
                </tr>
                <tr>
                    <td class="label-cell">Day Access</td>
                    <td class="value-cell">
                        @php
                            $selectedDayId = $registrationData['selected_event_day_id'] ?? null;
                            $selectedDay = null;
                            if ($selectedDayId) {
                                $selectedDay = \App\Models\Ticket\EventDay::find($selectedDayId);
                            }
                        @endphp
                        @if($selectedDay)
                            <span class="day-badge primary">{{ $selectedDay->label }}</span>
                            <small class="text-muted">({{ \Carbon\Carbon::parse($selectedDay->date)->format('M d, Y') }})</small>
                        @elseif($ticketType->all_days_access || ($ticketType->enable_day_selection && $ticketType->include_all_days_option))
                            <span class="day-badge success">All Days</span>
                        @else
                            @php
                                $accessibleDays = $ticketType->getAllAccessibleDays();
                            @endphp
                            @if($accessibleDays->count() > 0)
                                @foreach($accessibleDays as $day)
                                    <span class="day-badge primary">{{ $day->label }}</span>
                                @endforeach
                            @else
                                <span class="day-badge success">All Days</span>
                            @endif
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">Number of Delegates</td>
                    <td class="value-cell">{{ $quantity }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Currency</td>
                    <td class="value-cell">{{ ($registrationData['nationality'] === 'international' || $registrationData['nationality'] === 'International') ? 'USD ($)' : 'INR (₹)' }}</td>
                </tr>
            </table>
        </div>

        <!-- Delegate Details -->
        @if(isset($registrationData['delegates']) && count($registrationData['delegates']) > 0)
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-users"></i>
                Delegate Details
            </h4>
                <table class="delegates-table">
                    <thead>
                        <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 28%;">Delegate Name</th>
                        <th style="width: 28%;">Email</th>
                        <th style="width: 15%;">Phone</th>
                        <th style="width: 24%;">Ticket Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($registrationData['delegates'] as $index => $delegate)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $delegate['salutation'] }} {{ $delegate['first_name'] }} {{ $delegate['last_name'] }}</strong></td>
                                <td>{{ $delegate['email'] }}</td>
                                <td>{{ $delegate['phone'] ?? '-' }}</td>
                            <td>{{ $ticketType->name ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
        </div>
        @endif

        <!-- Organisation Information -->
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-building"></i>
                Organisation Information
            </h4>
            <table class="info-table">
                <tr>
                    <td class="label-cell">Organisation Name</td>
                    <td class="value-cell"><strong>{{ $registrationData['organisation_name'] }}</strong></td>
                </tr>
                <tr>
                    <td class="label-cell">Industry Sector</td>
                    <td class="value-cell">{{ $registrationData['industry_sector'] }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Organisation Type</td>
                    <td class="value-cell">{{ $registrationData['organisation_type'] }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Country</td>
                    <td class="value-cell">{{ $registrationData['company_country'] ?? $registrationData['country'] ?? 'N/A' }}</td>
                </tr>
                @if(!empty($registrationData['company_state'] ?? $registrationData['state'] ?? null))
                <tr>
                    <td class="label-cell">State</td>
                    <td class="value-cell">{{ $registrationData['company_state'] ?? $registrationData['state'] }}</td>
                </tr>
            @endif
                @if(!empty($registrationData['company_city'] ?? $registrationData['city'] ?? null))
                <tr>
                    <td class="label-cell">City</td>
                    <td class="value-cell">{{ $registrationData['company_city'] ?? $registrationData['city'] }}</td>
                </tr>
            @endif
                <tr>
                    <td class="label-cell">Phone</td>
                    <td class="value-cell">{{ $registrationData['phone'] }}</td>
                </tr>
            @if(!empty($registrationData['email']))
                <tr>
                    <td class="label-cell">Email</td>
                    <td class="value-cell">{{ $registrationData['email'] }}</td>
                </tr>
            @endif
            </table>
        </div>

        <!-- GST Information -->
        @if($registrationData['gst_required'] == '1')
        <div class="preview-section">
            <h4 class="section-title">
                <i class="fas fa-file-invoice-dollar"></i>
                GST / Invoice Details
            </h4>
            <table class="info-table">
                <tr>
                    <td class="label-cell">GSTIN</td>
                    <td class="value-cell"><strong>{{ $registrationData['gstin'] }}</strong></td>
                </tr>
                <tr>
                    <td class="label-cell">Legal Name (For Invoice)</td>
                    <td class="value-cell">{{ $registrationData['gst_legal_name'] }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Invoice Address</td>
                    <td class="value-cell">{{ $registrationData['gst_address'] }}</td>
                </tr>
                <tr>
                    <td class="label-cell">State</td>
                    <td class="value-cell">{{ $registrationData['gst_state'] }}</td>
                </tr>
                @if(!empty($registrationData['contact_name']))
                <tr>
                    <td class="label-cell">Contact Person</td>
                    <td class="value-cell">{{ $registrationData['contact_name'] }}</td>
                </tr>
                @endif
                @if(!empty($registrationData['contact_email']))
                <tr>
                    <td class="label-cell">Contact Email</td>
                    <td class="value-cell">{{ $registrationData['contact_email'] }}</td>
                </tr>
                @endif
                @if(!empty($registrationData['contact_phone']))
                <tr>
                    <td class="label-cell">Contact Phone</td>
                    <td class="value-cell">{{ $registrationData['contact_phone'] }}</td>
                </tr>
        @endif
            </table>
        </div>
        @endif

        <!-- Price Breakdown -->
        <div class="price-section">
            <h4 class="section-title">
                <i class="fas fa-calculator"></i>
                Price Breakdown
            </h4>
            @php
                $currencySymbol = ($currency ?? 'INR') === 'USD' ? '$' : '₹';
                $priceFormat = 2;
            @endphp
            <table class="price-table">
                <tr>
                    <td class="label-cell">Ticket Price ({{ $quantity }} × {{ $currencySymbol }}{{ number_format($unitPrice, $priceFormat) }})</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($subtotal, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">GST ({{ $gstRate }}%)</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($gstAmount, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td class="label-cell">Processing Charge ({{ $processingChargeRate }}%)</td>
                    <td class="value-cell">{{ $currencySymbol }}{{ number_format($processingChargeAmount, $priceFormat) }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label-cell" style="background: var(--primary-color); color: white;">Total Amount</td>
                    <td class="value-cell" style="background: var(--primary-color); color: white;">{{ $currencySymbol }}{{ number_format($total, $priceFormat) }}</td>
                </tr>
            </table>
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
