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
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <div class="row">
                                    <div class="col-4 text-muted small">Industry Sector:</div>
                                    <div class="col-8 fw-bold">{{ $registration->industry_sector ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 border rounded">
                                <div class="row">
                                    <div class="col-4 text-muted small">TIN Number:</div>
                                    <div class="col-8">
                                        <span class="badge bg-info text-dark">{{ $registration->tin_number ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Registration Type</div>
                            <div class="fw-semibold">{{ $registration->registration_type ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Registration Category</div>
                            <div class="fw-semibold">
                                <span class="badge bg-secondary">{{ $registration->registration_category ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Total Delegates</div>
                            <div class="fw-bold text-primary fs-5">{{ $delegates->count() }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">GST Number</div>
                            <div class="fw-semibold">{{ $registration->gstin ?? 'N/A' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">Organisation Name</div>
                            <div class="fw-bold">{{ $registration->company_name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-address-book me-2 text-success"></i>Contact Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="text-muted small">Address</div>
                            <div class="fw-semibold">{{ $registration->company_address ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">City</div>
                            <div class="fw-semibold">{{ $registration->company_city ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">State</div>
                            <div class="fw-semibold">{{ $registration->company_state ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Country</div>
                            <div class="fw-semibold">{{ $registration->company_country ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Zip Code</div>
                            <div class="fw-semibold">{{ $registration->postal_code ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small"><i class="fas fa-phone me-1 text-primary"></i>Phone</div>
                            <div class="fw-semibold">{{ $registration->company_phone ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small"><i class="fas fa-fax me-1 text-secondary"></i>Fax</div>
                            <div class="fw-semibold">N/A</div>
                        </div>
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
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <div class="text-muted small">Payment Status</div>
                                <div class="mt-2">
                                    <span class="badge fs-6 px-3 py-2 {{ $registration->payment_status == 'Paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        <i class="fas {{ $registration->payment_status == 'Paid' ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-1"></i>
                                        {{ $registration->payment_status ?? 'Not Paid' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 border rounded">
                                <div class="text-muted small">Payment Mode</div>
                                <div class="fw-semibold mt-2">{{ $registration->payment_method ?? 'Not Specified' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded text-center">
                                        <div class="small text-muted">Selection Amount</div>
                                        <div class="fw-bold text-primary">₹{{ number_format($registration->total_amount ?? 0, 0) }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 bg-light rounded text-center">
                                        <div class="small text-muted">Tax (18%)</div>
                                        <div class="fw-bold text-success">₹{{ number_format(($registration->total_amount ?? 0) * 0.18, 0) }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 bg-primary text-white rounded text-center">
                                        <div class="small opacity-75">Total Amount</div>
                                        <div class="fs-4 fw-bold">₹{{ number_format($registration->total_amount ?? 0, 0) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Discounts Section -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="border rounded p-3 bg-light">
                                <div class="small text-muted mb-2">Discount Details</div>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="small text-muted">Membership Discount</div>
                                        <div class="fw-semibold text-success">₹0</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="small text-muted">Group Discount</div>
                                        <div class="fw-semibold text-success">₹0</div>
                                    </div>
                                    <div class="col-4">
                                        <div class="small text-muted">Admin Discount</div>
                                        <div class="fw-semibold text-success">₹0</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-receipt text-primary fs-1"></i>
                    </div>
                    <h6 class="text-muted">Need to resend receipt?</h6>
                    <button class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>Resend Receipt
                    </button>
                    <div class="mt-2">
                        <small class="text-muted">Receipt will be sent to registered email address</small>
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
                                    <th><i class="fas fa-rupee-sign me-1"></i>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($delegates as $index => $delegate)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 14px; font-weight: bold;">
                                                {{ strtoupper(substr($delegate->first_name ?? 'D', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">
                                                    {{ trim(($delegate->salutation ?? '') . ' ' . ($delegate->first_name ?? '') . ' ' . ($delegate->last_name ?? '')) ?: 'N/A' }}
                                                </div>
                                                <small class="text-muted">Delegate #{{ $index + 1 }}</small>
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
                                        <span class="badge bg-success">Full Delegate</span>
                                    </td>
                                    <td>
                                        @if($delegate->phone)
                                            <a href="tel:{{ $delegate->phone }}" class="text-decoration-none">
                                                <i class="fas fa-phone text-success me-1"></i>{{ $delegate->phone }}
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
                        | Average per delegate: ₹{{ number_format(($registration->total_amount ?? 0) / $delegates->count(), 0) }}
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