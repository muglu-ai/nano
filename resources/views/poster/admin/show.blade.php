@extends('layouts.dashboard')
@section('title', 'Poster Registration Details - ' . $registration->tin_no)
@section('content')

    <style>
        .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #5a5c69;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .detail-value {
            font-size: 1rem;
            color: #2d3436;
        }
        
        .badge-status-paid {
            background-color: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .badge-status-pending {
            background-color: #ffc107;
            color: #000;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .badge-status-failed {
            background-color: #dc3545;
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
        
        .author-card {
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #fafbfc;
        }
        
        .author-card.lead-author {
            border-color: #28a745;
            background: #f0fff4;
        }
        
        .author-card.presenter {
            border-color: #007bff;
            background: #f0f7ff;
        }
        
        .abstract-box {
            background: #f8f9fc;
            border: 1px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <!-- Header Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">Poster Registration Details</h5>
                                <p class="text-sm mb-0">TIN: <strong>{{ $registration->tin_no }}</strong></p>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('admin.posters.list') }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-arrow-left me-1"></i> Back to List
                                </a>
                                
                                <!-- Resend Email Button -->
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#resendEmailModal">
                                    <i class="fas fa-envelope me-1"></i> Resend Email
                                </button>
                                
                                @if($registration->payment_status === 'paid')
                                    <span class="badge-status badge-status-paid ms-2">
                                        <i class="fas fa-check-circle me-1"></i> Paid
                                    </span>
                                @elseif($registration->payment_status === 'pending')
                                    <span class="badge-status badge-status-pending ms-2">
                                        <i class="fas fa-clock me-1"></i> Pending
                                    </span>
                                @else
                                    <span class="badge-status badge-status-failed ms-2">
                                        <i class="fas fa-times-circle me-1"></i> Failed
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="row">
                    <!-- Registration Details -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Registration Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="detail-label mb-1">TIN (Transaction ID)</p>
                                        <p class="detail-value">{{ $registration->tin_no }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="detail-label mb-1">PIN</p>
                                        <p class="detail-value">{{ $registration->pin_no ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Sector</p>
                                        <p class="detail-value">
                                            <span class="badge bg-info">{{ $registration->sector }}</span>
                                        </p>
                                    </div>
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Poster Category</p>
                                        <p class="detail-value">{{ $registration->poster_category ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Presentation Mode</p>
                                        <p class="detail-value">
                                            <span class="badge bg-secondary">{{ $registration->presentation_mode }}</span>
                                        </p>
                                    </div>
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Currency</p>
                                        <p class="detail-value">
                                            @if($registration->currency === 'INR')
                                                <span class="badge bg-warning text-dark">Indian (INR)</span>
                                            @else
                                                <span class="badge bg-primary">International (USD)</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Attendees Count</p>
                                        <p class="detail-value">{{ $registration->attendee_count }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Registration Date</p>
                                        <p class="detail-value">{{ $registration->created_at->format('M d, Y h:i A') }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Publication Permission</p>
                                        <p class="detail-value">
                                            @if($registration->publication_permission)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Authors Approval</p>
                                        <p class="detail-value">
                                            @if($registration->authors_approval)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Information</h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $currencySymbol = $registration->currency === 'USD' ? '$' : '₹';
                                @endphp
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Base Amount</p>
                                        <p class="detail-value">{{ $currencySymbol }}{{ number_format($registration->base_amount, 2) }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="detail-label mb-1">GST Amount</p>
                                        <p class="detail-value">{{ $currencySymbol }}{{ number_format($registration->gst_amount, 2) }}</p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Processing Fee</p>
                                        <p class="detail-value">{{ $currencySymbol }}{{ number_format($registration->processing_fee, 2) }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Total Amount</p>
                                        <p class="detail-value"><strong class="text-success fs-5">{{ $currencySymbol }}{{ number_format($registration->total_amount, 2) }}</strong></p>
                                    </div>
                                </div>
                                <hr>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Payment Status</p>
                                        <p class="detail-value">
                                            @if($registration->payment_status === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($registration->payment_status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span class="badge bg-danger">Failed</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Payment Method</p>
                                        <p class="detail-value">{{ $registration->payment_method ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Transaction ID</p>
                                        <p class="detail-value">{{ $registration->payment_transaction_id ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="detail-label mb-1">Payment Date</p>
                                        <p class="detail-value">{{ $registration->payment_date ? $registration->payment_date->format('M d, Y h:i A') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Abstract Details -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Abstract Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-12">
                                <p class="detail-label mb-1">Abstract Title</p>
                                <p class="detail-value fs-5"><strong>{{ $registration->abstract_title }}</strong></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <p class="detail-label mb-1">Abstract</p>
                                <div class="abstract-box">{{ $registration->abstract }}</div>
                            </div>
                        </div>
                        @if($registration->extended_abstract_path)
                        <div class="row">
                            <div class="col-12">
                                <p class="detail-label mb-1">Extended Abstract File</p>
                                <p class="detail-value">
                                    <a href="{{ Storage::url($registration->extended_abstract_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download me-1"></i>
                                        {{ $registration->extended_abstract_original_name ?? 'Download File' }}
                                    </a>
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Authors -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-users me-2"></i>Authors ({{ $registration->posterAuthors->count() }})</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @forelse($registration->posterAuthors->sortBy('author_index') as $author)
                                @php
                                    $cardClass = '';
                                    if ($author->is_lead_author) $cardClass = 'lead-author';
                                    elseif ($author->is_presenter) $cardClass = 'presenter';
                                @endphp
                                <div class="col-md-6">
                                    <div class="author-card {{ $cardClass }}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0">
                                                {{ $author->title }} {{ $author->first_name }} {{ $author->last_name }}
                                            </h6>
                                            <div>
                                                @if($author->is_lead_author)
                                                    <span class="badge bg-success">Lead Author</span>
                                                @endif
                                                @if($author->is_presenter)
                                                    <span class="badge bg-primary">Presenter</span>
                                                @endif
                                                @if($author->will_attend)
                                                    <span class="badge bg-info">Attending</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1"><small class="text-muted">Email:</small></p>
                                                <p class="mb-2">{{ $author->email }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-1"><small class="text-muted">Mobile:</small></p>
                                                <p class="mb-2">{{ $author->mobile ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1"><small class="text-muted">Designation:</small></p>
                                                <p class="mb-2">{{ $author->designation ?? 'N/A' }}</p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-1"><small class="text-muted">Institution:</small></p>
                                                <p class="mb-2">{{ $author->institution ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                        
                                        @if($author->country || $author->state || $author->city)
                                        <div class="row">
                                            <div class="col-12">
                                                <p class="mb-1"><small class="text-muted">Location:</small></p>
                                                <p class="mb-2">
                                                    {{ $author->city ?? '' }}
                                                    @if($author->state) {{ $author->state->name ?? '' }} @endif
                                                    @if($author->country), {{ $author->country->name ?? '' }} @endif
                                                </p>
                                            </div>
                                        </div>
                                        @endif
                                        
                                        @if($author->cv_path)
                                        <div class="row">
                                            <div class="col-12">
                                                <a href="{{ Storage::url($author->cv_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-file-pdf me-1"></i> Download CV
                                                </a>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted text-center py-4">No authors found for this registration.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Lead Author Quick Info -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-user-tie me-2"></i>Lead Author (Quick Reference)</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p class="detail-label mb-1">Name</p>
                                <p class="detail-value">{{ $registration->lead_author_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="detail-label mb-1">Email</p>
                                <p class="detail-value">{{ $registration->lead_author_email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-4">
                                <p class="detail-label mb-1">Mobile</p>
                                <p class="detail-value">{{ $registration->lead_author_mobile ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resend Email Modal -->
    <div class="modal fade" id="resendEmailModal" tabindex="-1" aria-labelledby="resendEmailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.posters.resend-email', $registration->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="resendEmailModalLabel">Resend Email</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to resend the confirmation email?</p>
                        <p class="mb-0"><strong>Recipient:</strong> {{ $registration->lead_author_email }}</p>
                        <p class="text-muted small">
                            @if($registration->payment_status === 'paid')
                                A payment confirmation email will be sent.
                            @else
                                A registration confirmation email will be sent.
                            @endif
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i> Send Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
