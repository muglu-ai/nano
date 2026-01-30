@extends('layouts.dashboard')
@section('title', 'Conference Delegate(s) Data')
@section('content')
<div class="container-fluid">
    <!-- Header Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><i class="fas fa-users me-2"></i>Conference Delegate(s) Data</h4>
                    <small class="opacity-75">Registration ID: {{ $registration->id ?? 'N/A' }}</small>
                </div>
                <a href="javascript:history.back()" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Basic Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Basic Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr style="background-color: #f8f9fa;">
                                    <td class="text-muted" style="width: 25%;">Industry Sector:</td>
                                    <td class="fw-bold" style="width: 25%;">{{ $registration->industry_sector ?? 'N/A' }}</td>
                                    
                                </tr>
                                <tr >
                                    <td class="text-muted">Organisation Type:</td>
                                    <td class="fw-bold">{{ $registration->organisation_type ?? 'N/A' }}</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td class="text-muted">TIN Number:</td>
                                    <td colspan="3">
                                        <span class="badge bg-primary fs-6 px-3 py-2">{{ $registration->tin_number ?? 'N/A' }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Registration Type</td>
                                    <td class="fw-semibold">{{ $registration->registration_type ?? 'N/A' }}</td>
                                   
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td class="text-muted">Registration Category</td>
                                    <td class="fw-bold">{{ $registration->registration_category ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Total Delegates</td>
                                    <td class="fw-bold text-danger fs-5">{{ $delegates->count() }}</td>
                                    
                                </tr>
                                <tr>
                                    <td class="text-muted">Organisation Name</td>
                                    <td class="fw-bold" colspan="3">{{ $registration->company_name ?? 'N/A' }}</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td class="text-muted">GST Number</td>
                                    <td class="fw-bold">{{ $registration->gst_number ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Details Table -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-address-book me-2 text-success"></i>Contact Details</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr style="background-color: #f8f9fa;">
                                    <td class="text-muted" style="width: 25%;">Address:</td>
                                    <td class="fw-bold" style="width: 75%;">{{ $registration->company_address ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">City:</td>
                                    <td class="fw-bold">{{ $registration->company_city ?? 'N/A' }}</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td class="text-muted">State:</td>
                                    <td class="fw-bold">{{ $registration->company_state ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Country:</td>
                                    <td class="fw-bold">{{ $registration->company_country ?? 'N/A' }}</td>
                                </tr>
                                <tr style="background-color: #f8f9fa;">
                                    <td class="text-muted">Zip Code:</td>
                                    <td class="fw-bold">{{ $registration->postal_code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Phone:</td>
                                    <td class="fw-bold">{{ $registration->company_phone ?? 'N/A' }}</td>
                                </tr>
                                {{-- <tr style="background-color: #f8f9fa;">
                                    <td class="text-muted">Fax:</td>
                                    <td class="fw-bold">N/A</td>
                                </tr> --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-credit-card me-2 text-warning"></i>Payment Information</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted" style="width: 20%;">Payment Status</td>
                                    <td style="width: 30%;">
                                        <span class="badge fs-6 px-3 py-2 {{ $registration->payment_status == 'Paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                            <i class="fas {{ $registration->payment_status == 'Paid' ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-1"></i>
                                            {{ $registration->payment_status ?? 'Not Paid' }}
                                        </span>
                                    </td>
                                    <td class="text-muted" style="width: 20%;">Payment Mode</td>
                                    <td style="width: 30%;">{{ $registration->payment_method ?? 'Not Specified' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Selection Amount</td>
                                    <td class="fw-bold text-primary">₹{{ number_format($registration->total_amount ?? 0, 0) }}</td>
                                    @if((isset($registration->igst_total) && $registration->igst_total > 0))
                                        <td class="text-muted">IGST ({{ $registration->igst_rate ?? 0 }}%)</td>
                                        <td class="fw-bold text-success">₹{{ number_format($registration->igst_total, 2) }}</td>
                                    @elseif((isset($registration->cgst_total) && $registration->cgst_total > 0) && (isset($registration->sgst_total) && $registration->sgst_total > 0))
                                        <td class="text-muted">CGST ({{ $registration->cgst_rate ?? 0 }}%)</td>
                                        <td class="fw-bold text-success">₹{{ number_format($registration->cgst_total, 2) }}</td>
                                    @else
                                        <td class="text-muted">Tax</td>
                                        <td class="fw-bold text-success">₹0</td>
                                    @endif
                                </tr>
                                @if((isset($registration->cgst_total) && $registration->cgst_total > 0) && (isset($registration->sgst_total) && $registration->sgst_total > 0))
                                <tr>
                                    <td class="text-muted">SGST ({{ $registration->sgst_rate ?? 0 }}%)</td>
                                    <td class="fw-bold text-success">₹{{ number_format($registration->sgst_total, 2) }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="text-muted">Total Amount</td>
                                    <td colspan="3">
                                        <span class="fs-4 fw-bold text-white px-4 py-2" style="background:#e91e63; border-radius:6px; display:inline-block;">₹{{ number_format($registration->total_amount ?? 0, 0) }}</span>
                                    </td>
                                </tr>
                                <tr style="background:#f1f1f1;">
                                    <td class="text-muted">Membership Discount</td>
                                    <td class="fw-bold text-success">₹0</td>
                                    <td class="text-muted">Group Discount</td>
                                    <td class="fw-bold text-success">₹0</td>
                                </tr>
                                <tr style="background:#f1f1f1;">
                                    <td class="text-muted">Admin Discount</td>
                                    <td class="fw-bold text-success">₹0</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Delegates Information -->
    @if($delegates->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-user-friends me-2 text-info"></i>
                            Delegate Details ({{ $delegates->count() }} {{ $delegates->count() == 1 ? 'Delegate' : 'Delegates' }})
                        </h6>
                        {{-- <button class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Resend Receipt
                         </button> --}}
                        <span class="badge bg-info">Total: {{ $delegates->count() }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-user me-1"></i>Delegate's Name</th>
                                    <th><i class="fas fa-briefcase me-1"></i>Job Title</th>
                                    <th><i class="fas fa-id-badge me-1"></i>Badge Name</th>
                                    <th><i class="fas fa-envelope me-1"></i>Email Address</th>
                                    <th><i class="fas fa-tag me-1"></i>Category</th>
                                    <th><i class="fas fa-phone me-1"></i>Mobile No.</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($delegates as $index => $delegate)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                           
                                            <div>
                                                <div class="fw-bold">
                                                    {{ trim(($delegate->salutation ?? '') . ' ' . ($delegate->first_name ?? '') . ' ' . ($delegate->last_name ?? '')) ?: 'N/A' }}
                                                </div>
                                               
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $delegate->job_title ?? 'N/A' }}</span>
                                    </td>
                                    <td class="fw-semibold">
                                        {{ trim(($delegate->salutation ?? '') . ' ' . ($delegate->first_name ?? '') . ' ' . ($delegate->last_name ?? '')) ?: 'N/A' }}
                                    </td>
                                    <td>
                                        @if($delegate->email)
                                            <a href="mailto:{{ $delegate->email }}" class="text-decoration-none">
                                                <i class="fas fa-envelope text-primary me-1"></i>{{ $delegate->email }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $delegate->registration_category ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($delegate->phone)
                                            <a href="tel:{{ $delegate->phone }}" class="text-decoration-none">
                                            {{ $delegate->phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">
                                            ₹{{ number_format(($registration->total_amount ?? 0) / $delegates->count(), 0) }}
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Total Amount: ₹{{ number_format($registration->total_amount ?? 0, 0) }} 
                       
                    </small>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-slash text-muted" style="font-size: 4rem;"></i>
                    <h5 class="text-muted mt-3">No Delegates Found</h5>
                    <p class="text-muted">No delegates are registered for this conference registration.</p>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection