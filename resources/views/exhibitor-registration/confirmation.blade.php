@extends('layouts.exhibitor-registration')

@section('title', 'Registration Confirmation - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/custom.css') }}">
<style>
    .form-container {padding: 1rem 0px;}
    .preview-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
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

    /* Success Alert */
    .success-alert {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border: 2px solid #28a745;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .success-alert i {
        color: #28a745;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .success-alert h3 {
        color: #155724;
        margin: 0.5rem 0;
        font-weight: 700;
    }

    .success-alert p {
        color: #155724;
        margin: 0.25rem 0 0;
    }

    .payment-status-badge {
        display: inline-block;
        padding: 0.35rem 0.75rem;
        border-radius: 0.375rem;
        font-weight: 600;
        font-size: 0.875rem;
    }

    .payment-status-badge.paid {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #28a745;
    }

    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .info-table {
            font-size: 0.75rem;
        }

        .info-table td {
            padding: 0.5rem 0.4rem;
        }

        .price-table {
            font-size: 0.8rem;
        }

        .price-table td {
            padding: 0.5rem 0.4rem;
        }
    }
</style>
@endpush

@section('content')
<div class="preview-container">
    {{-- Success Message --}}
    <div class="success-alert">
        <i class="fas fa-check-circle"></i>
        <h3>Registration Successful!</h3>
        <p>Thank you for registering for the Exhibitor Registration at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}!</p>
        <p class="mb-0">
            <strong>TIN Number:</strong> {{ $application->application_id }}<br>
            <strong>Status:</strong> 
            @if($invoice->payment_status === 'paid')
                <span class="payment-status-badge paid">✓ Payment Completed</span>
            @else
                <span class="badge bg-warning">Payment Pending</span>
            @endif
        </p>
    </div>

    @php
        // Extract data from application
        $boothSpace = $application->stall_category ?? '';
        $boothSize = $application->interested_sqm ?? '';
        $sector = $application->sector_id ?? '';
        $subsector = $application->subSector ?? '';
        $otherSector = $application->type_of_business ?? null;
        $category = $application->exhibitorType ?? '';
        $salesExecutiveName = $application->salesPerson ?? '';
        $gstStatus = $application->gst_compliance ? 'Registered' : 'Unregistered';
        $gstNo = $application->gst_no ?? null;
        $panNo = $application->pan_no ?? '';
        $tanNo = $application->tan_no ?? null;
        $tanStatus = $application->tan_compliance ? 'Registered' : 'Unregistered';
        
        // Billing data
        $billingCompany = $billingDetail->billing_company ?? $application->company_name ?? '';
        $billingEmail = $billingDetail->email ?? $application->company_email ?? '';
        $billingAddress = $billingDetail->address ?? $application->address ?? '';
        $billingCity = 'N/A';
        if ($billingDetail && $billingDetail->city_id) {
            if (is_numeric($billingDetail->city_id)) {
                $city = \App\Models\City::find($billingDetail->city_id);
                $billingCity = $city ? $city->name : $billingDetail->city_id;
            } else {
                $billingCity = $billingDetail->city_id;
            }
        } elseif ($application->city_id) {
            if (is_numeric($application->city_id)) {
                $city = \App\Models\City::find($application->city_id);
                $billingCity = $city ? $city->name : $application->city_id;
            } else {
                $billingCity = $application->city_id;
            }
        }
        $billingStateId = $billingDetail->state_id ?? $application->state_id ?? null;
        $billingState = $billingStateId ? (\App\Models\State::find($billingStateId)->name ?? 'N/A') : 'N/A';
        $billingCountryId = $billingDetail->country_id ?? $application->country_id ?? null;
        $billingCountry = $billingCountryId ? (\App\Models\Country::find($billingCountryId)->name ?? 'N/A') : 'N/A';
        $billingPostal = $billingDetail->postal_code ?? $application->postal_code ?? '';
        $billingPhone = $billingDetail->phone ?? $application->landline ?? '';
        $billingWebsite = $application->website ?? '';
        
        // Exhibitor data (same as billing for exhibitor-registration)
        $exhibitorName = $application->company_name ?? '';
        $exhibitorAddress = $application->address ?? '';
        $exhibitorCity = $billingCity;
        $exhibitorState = $billingState;
        $exhibitorCountry = $billingCountry;
        $exhibitorPostal = $application->postal_code ?? '';
        $exhibitorPhone = $application->landline ?? '';
        $exhibitorWebsite = $application->website ?? '';
        $exhibitorEmail = $application->company_email ?? '';
        
        // Contact data
        $contactTitle = $contact->salutation ?? '';
        $contactFirstName = $contact->first_name ?? '';
        $contactLastName = $contact->last_name ?? '';
        $contactDesignation = $contact->designation ?? '';
        $contactEmail = $contact->email ?? '';
        $contactMobile = $contact->contact_number ?? '';
        
        // Currency and pricing
        $currency = $invoice->currency ?? 'INR';
        $currencySymbol = $currency === 'USD' ? '$' : '₹';
        $priceFormat = 2;
    @endphp

    {{-- Booth & Exhibition Details --}}
    <div class="preview-section">
        <h4 class="section-title">
            <i class="fas fa-cube"></i>
            Booth & Exhibition Details
        </h4>
        <table class="info-table">
            <tr>
                <td class="label-cell">Booth Space</td>
                <td class="value-cell"><strong>{{ $boothSpace ?: 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td class="label-cell">Booth Size</td>
                <td class="value-cell">{{ $boothSize ?: 'N/A' }}@if($boothSize) sqm @endif</td>
            </tr>
            <tr>
                <td class="label-cell">Sector</td>
                <td class="value-cell">{{ $sector ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Subsector</td>
                <td class="value-cell">{{ $subsector ?: 'N/A' }}</td>
            </tr>
            @if($otherSector)
            <tr>
                <td class="label-cell">Other Sector Name</td>
                <td class="value-cell">{{ $otherSector }}</td>
            </tr>
            @endif
            <tr>
                <td class="label-cell">Category</td>
                <td class="value-cell">{{ $category ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Sales Executive Name</td>
                <td class="value-cell">{{ $salesExecutiveName ?: 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- Tax & Compliance Details --}}
    <div class="preview-section">
        <h4 class="section-title">
            <i class="fas fa-file-invoice-dollar"></i>
            Tax & Compliance Details
        </h4>
        <table class="info-table">
            <tr>
                <td class="label-cell">GST Status</td>
                <td class="value-cell"><strong>{{ $gstStatus }}</strong></td>
            </tr>
            @if($gstStatus === 'Registered' && $gstNo)
            <tr>
                <td class="label-cell">GST Number</td>
                <td class="value-cell">{{ $gstNo }}</td>
            </tr>
            @endif
            <tr>
                <td class="label-cell">TAN Status</td>
                <td class="value-cell"><strong>{{ $tanStatus }}</strong></td>
            </tr>
            @if($tanStatus === 'Registered' && $tanNo)
            <tr>
                <td class="label-cell">TAN Number</td>
                <td class="value-cell">{{ $tanNo }}</td>
            </tr>
            @endif
            <tr>
                <td class="label-cell">PAN Number</td>
                <td class="value-cell">{{ $panNo ?: 'N/A' }}</td>
            </tr>
            @if($billingDetail && $billingDetail->tax_no)
            <tr>
                <td class="label-cell">Tax Number</td>
                <td class="value-cell">{{ $billingDetail->tax_no }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Billing Information --}}
    <div class="preview-section">
        <h4 class="section-title">
            <i class="fas fa-building"></i>
            Billing Information
        </h4>
        <table class="info-table">
            <tr>
                <td class="label-cell">Company Name</td>
                <td class="value-cell"><strong>{{ $billingCompany ?: 'N/A' }}</strong></td>
            </tr>
            <tr>
                <td class="label-cell">Email</td>
                <td class="value-cell">{{ $billingEmail ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Address</td>
                <td class="value-cell">{{ $billingAddress ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">City</td>
                <td class="value-cell">{{ $billingCity }}</td>
            </tr>
            <tr>
                <td class="label-cell">State</td>
                <td class="value-cell">{{ $billingState }}</td>
            </tr>
            <tr>
                <td class="label-cell">Postal Code</td>
                <td class="value-cell">{{ $billingPostal ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Country</td>
                <td class="value-cell">{{ $billingCountry }}</td>
            </tr>
            <tr>
                <td class="label-cell">Telephone</td>
                <td class="value-cell">{{ $billingPhone ?: 'N/A' }}</td>
            </tr>
            @if($billingWebsite)
            <tr>
                <td class="label-cell">Website</td>
                <td class="value-cell"><a href="{{ $billingWebsite }}" target="_blank">{{ $billingWebsite }}</a></td>
            </tr>
            @endif
        </table>
    </div>

    {{-- Exhibitor Information --}}
    @if($exhibitorName && ($exhibitorName !== $billingCompany || $exhibitorAddress !== $billingAddress))
    <div class="preview-section">
        <h4 class="section-title">
            <i class="fas fa-building"></i>
            Exhibitor Information
        </h4>
        <table class="info-table">
            <tr>
                <td class="label-cell">Name of Exhibitor</td>
                <td class="value-cell"><strong>{{ $exhibitorName }}</strong></td>
            </tr>
            <tr>
                <td class="label-cell">Address</td>
                <td class="value-cell">{{ $exhibitorAddress ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">City</td>
                <td class="value-cell">{{ $exhibitorCity ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">State</td>
                <td class="value-cell">{{ $exhibitorState ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Postal Code</td>
                <td class="value-cell">{{ $exhibitorPostal ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Country</td>
                <td class="value-cell">{{ $exhibitorCountry ?? 'N/A' }}</td>
            </tr>
            @if($exhibitorPhone)
            <tr>
                <td class="label-cell">Telephone</td>
                <td class="value-cell">{{ $exhibitorPhone }}</td>
            </tr>
            @endif
            @if($exhibitorWebsite)
            <tr>
                <td class="label-cell">Website</td>
                <td class="value-cell"><a href="{{ $exhibitorWebsite }}" target="_blank">{{ $exhibitorWebsite }}</a></td>
            </tr>
            @endif
            @if($exhibitorEmail)
            <tr>
                <td class="label-cell">Email</td>
                <td class="value-cell">{{ $exhibitorEmail }}</td>
            </tr>
            @endif
        </table>
    </div>
    @endif

    {{-- Contact Person Details --}}
    @if($contact)
    <div class="preview-section">
        <h4 class="section-title">
            <i class="fas fa-user"></i>
            Contact Person Details
        </h4>
        <table class="info-table">
            <tr>
                <td class="label-cell">Name</td>
                <td class="value-cell"><strong>{{ $contactTitle }} {{ $contactFirstName }} {{ $contactLastName }}</strong></td>
            </tr>
            <tr>
                <td class="label-cell">Designation</td>
                <td class="value-cell">{{ $contactDesignation ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Email</td>
                <td class="value-cell">{{ $contactEmail ?: 'N/A' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Mobile</td>
                <td class="value-cell">{{ $contactMobile ?: 'N/A' }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Pricing Summary --}}
    <div class="price-section">
        <h4 class="section-title">
            <i class="fas fa-calculator"></i>
            Pricing Summary
        </h4>
        <table class="price-table">
            <tr>
                <td class="label-cell">Base Price</td>
                <td class="value-cell">{{ $currencySymbol }}{{ number_format($invoice->price ?? $invoice->amount, $priceFormat) }}</td>
            </tr>
            @if(isset($invoice->cgst_amount) && $invoice->cgst_amount)
            <tr>
                <td class="label-cell">CGST ({{ $invoice->cgst_rate ?? 0 }}%)</td>
                <td class="value-cell">{{ $currencySymbol }}{{ number_format($invoice->cgst_amount, $priceFormat) }}</td>
            </tr>
            @endif
            @if(isset($invoice->sgst_amount) && $invoice->sgst_amount)
            <tr>
                <td class="label-cell">SGST ({{ $invoice->sgst_rate ?? 0 }}%)</td>
                <td class="value-cell">{{ $currencySymbol }}{{ number_format($invoice->sgst_amount, $priceFormat) }}</td>
            </tr>
            @endif
            @if(isset($invoice->igst_amount) && $invoice->igst_amount)
            <tr>
                <td class="label-cell">IGST ({{ $invoice->igst_rate ?? 0 }}%)</td>
                <td class="value-cell">{{ $currencySymbol }}{{ number_format($invoice->igst_amount, $priceFormat) }}</td>
            </tr>
            @endif
            @if($invoice->processing_charges)
            <tr>
                <td class="label-cell">Processing Charges ({{ $invoice->processing_chargesRate ?? 3 }}%)</td>
                <td class="value-cell">{{ $currencySymbol }}{{ number_format($invoice->processing_charges, $priceFormat) }}</td>
            </tr>
            @endif
            <tr class="total-row">
                <td class="label-cell" style="background: var(--primary-color); color: white;">Total Amount</td>
                <td class="value-cell" style="background: var(--primary-color); color: white;">{{ $currencySymbol }}{{ number_format($invoice->total_final_price ?? $invoice->amount, $priceFormat) }}</td>
            </tr>
        </table>
    </div>

    {{-- Payment Transaction Details --}}
    @if($invoice->payment_status === 'paid')
    <div class="preview-section">
        <h4 class="section-title">
            <i class="fas fa-credit-card"></i>
            Payment Transaction Details
        </h4>
        
        <div class="success-alert mb-3" style="text-align: left; padding: 1rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-check-circle" style="color: #28a745; font-size: 1.2rem;"></i>
                <div style="flex: 1;">
                    <strong style="color: #155724;">Payment Successful!</strong>
                    <p style="color: #155724; margin: 0.25rem 0 0; font-size: 0.9rem;">Your payment has been processed successfully.</p>
                </div>
            </div>
        </div>

        <table class="info-table">
            <tr>
                <td class="label-cell">Payment Status</td>
                <td class="value-cell">
                    <span class="payment-status-badge paid">✓ PAID</span>
                </td>
            </tr>
            @if($invoice->pin_no)
            <tr>
                <td class="label-cell">PIN Number</td>
                <td class="value-cell"><strong style="color: var(--primary-color);">{{ $invoice->pin_no }}</strong></td>
            </tr>
            @endif
            @if($payment)
            <tr>
                <td class="label-cell">Payment Method</td>
                <td class="value-cell"><strong>{{ ucfirst($payment->payment_method ?? 'CCAvenue') }}</strong></td>
            </tr>
            @if($payment->transaction_id || $payment->track_id)
            <tr>
                <td class="label-cell">Transaction ID</td>
                <td class="value-cell"><strong style="color: var(--primary-color);">{{ $payment->transaction_id ?? $payment->track_id ?? 'N/A' }}</strong></td>
            </tr>
            @endif
            @if($payment->payment_date)
            <tr>
                <td class="label-cell">Payment Date & Time</td>
                <td class="value-cell">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y, h:i A') }}</td>
            </tr>
            @endif
            @if($payment->pg_response_json)
            @php
                $responseData = is_string($payment->pg_response_json) ? json_decode($payment->pg_response_json, true) : $payment->pg_response_json;
                $paymentMode = $responseData['payment_mode'] ?? $responseData['payment_method'] ?? null;
                $bankRefNo = $responseData['bank_ref_no'] ?? null;
            @endphp
            @if($paymentMode)
            <tr>
                <td class="label-cell">Payment Mode</td>
                <td class="value-cell"><strong>{{ strtoupper($paymentMode) }}</strong></td>
            </tr>
            @endif
            @if($bankRefNo)
            <tr>
                <td class="label-cell">Bank Reference Number</td>
                <td class="value-cell"><strong>{{ $bankRefNo }}</strong></td>
            </tr>
            @endif
            @endif
            @elseif(session('payment_response'))
            @php
                $paymentResponse = session('payment_response');
            @endphp
            <tr>
                <td class="label-cell">Payment Method</td>
                <td class="value-cell"><strong>{{ ucfirst($paymentResponse['payment_mode'] ?? 'CCAvenue') }}</strong></td>
            </tr>
            @if(isset($paymentResponse['tracking_id']))
            <tr>
                <td class="label-cell">Transaction ID</td>
                <td class="value-cell"><strong style="color: var(--primary-color);">{{ $paymentResponse['tracking_id'] }}</strong></td>
            </tr>
            @endif
            @if(isset($paymentResponse['bank_ref_no']))
            <tr>
                <td class="label-cell">Bank Reference Number</td>
                <td class="value-cell"><strong>{{ $paymentResponse['bank_ref_no'] }}</strong></td>
            </tr>
            @endif
            @endif
            <tr>
                <td class="label-cell">Amount Paid</td>
                <td class="value-cell"><strong style="color: #155724; font-size: 1.1rem;">{{ $currencySymbol }}{{ number_format($invoice->amount_paid ?? $invoice->total_final_price ?? $invoice->amount, $priceFormat) }}</strong></td>
            </tr>
            <tr>
                <td class="label-cell">Currency</td>
                <td class="value-cell"><strong>{{ $currency }}</strong></td>
            </tr>
        </table>
    </div>
    @endif

    {{-- Next Steps --}}
    <div class="preview-section">
        <h4 class="section-title">
            <i class="fas fa-info-circle"></i>
            Next Steps
        </h4>
        @if($invoice->payment_status === 'paid')
        <div class="alert alert-success">
            <strong><i class="fas fa-check-circle"></i> Payment Completed!</strong> Your registration is confirmed. You will receive a confirmation email shortly.
        </div>
        <ul style="margin: 1rem 0; padding-left: 1.5rem;">
            <li>Check your email for the confirmation and invoice</li>
            <li>You will receive further instructions via email</li>
            <li>For any queries, please contact the event organizers</li>
        </ul>
        @else
        <div class="alert alert-warning">
            <strong><i class="fas fa-exclamation-triangle"></i> Payment Pending:</strong> Please complete the payment to confirm your registration.
        </div>
        <div class="text-center mt-3">
            <a href="{{ route('exhibitor-registration.payment', $application->application_id) }}" class="btn btn-success btn-lg">
                <i class="fas fa-credit-card"></i> Complete Payment
            </a>
        </div>
        @endif
    </div>

    {{-- Action Buttons --}}
    <div class="text-center mt-4">
        <a href="{{ route('exhibitor-registration.register') }}" class="btn btn-secondary">
            <i class="fas fa-home"></i> Back to Home
        </a>
        @if($invoice->payment_status !== 'paid')
        <a href="{{ route('exhibitor-registration.payment', $application->application_id) }}" class="btn btn-success">
            <i class="fas fa-credit-card"></i> Make Payment
        </a>
        @endif
    </div>
</div>
@endsection
