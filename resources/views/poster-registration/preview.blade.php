@extends('layouts.poster-registration')

@section('title', 'Preview Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/custom.css') }}">
<style>
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

    .author-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .author-card.lead-author {
        border-color: #0B5ED7;
        background: #f0f5ff;
    }

    .author-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }

    .author-name {
        font-weight: 700;
        font-size: 1rem;
        color: #212529;
    }

    .role-badges {
        display: flex;
        gap: 0.5rem;
    }

    .role-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-lead {
        background: #0B5ED7;
        color: white;
    }

    .badge-presenter {
        background: #20C997;
        color: white;
    }

    .badge-attending {
        background: #28a745;
        color: white;
    }

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

    .form-container {padding: 1rem 0px;}
</style>
@endpush

@section('poster-content')
<div class="container py-3">
    {{-- Step Indicator --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="step-indicator">
                <div class="step-item completed">
                    <div class="step-number">1</div>
                    <div class="step-label">Registration Details</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item active">
                    <div class="step-number">2</div>
                    <div class="step-label">Preview Details</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-label">Payment</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">Preview Your Registration</h2>
            
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Registration Details --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Registration Details
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Sector</td>
                        <td class="value-cell">{{ $draft->sector ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Currency</td>
                        <td class="value-cell">{{ $draft->currency ?? 'INR' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Abstract/Poster Details --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-file-alt"></i>
                    Abstract / Poster Details
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Poster Category</td>
                        <td class="value-cell">{{ $draft->poster_category ?? 'Breaking Boundaries' }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Abstract Title</td>
                        <td class="value-cell"><strong>{{ $draft->abstract_title ?? 'N/A' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label-cell">Abstract</td>
                        <td class="value-cell">{{ $draft->abstract ?? 'N/A' }}</td>
                    </tr>
                    @if(isset($draft->extended_abstract_path) && $draft->extended_abstract_path)
                    <tr>
                        <td class="label-cell">Extended Abstract</td>
                        <td class="value-cell">
                            <a href="{{ route('poster.downloadFile', ['type' => 'extended_abstract', 'token' => $draft->token]) }}" 
                               class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                    @endif
                </table>
            </div>

            {{-- Authors --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-users"></i>
                    Authors ({{ count($draft->authors ?? []) }})
                </h4>
                
                @if(isset($draft->authors) && count($draft->authors) > 0)
                    @foreach($draft->authors as $index => $author)
                        <div class="author-card {{ isset($author['is_lead']) && $author['is_lead'] ? 'lead-author' : '' }}">
                            <div class="author-header">
                                <div>
                                    <div class="author-name">
                                        {{ $author['first_name'] ?? '' }} {{ $author['last_name'] ?? '' }}
                                    </div>
                                    <small class="text-muted">Author {{ $index + 1 }}</small>
                                </div>
                                <div class="role-badges">
                                    @if(isset($author['is_lead']) && $author['is_lead'])
                                        <span class="role-badge badge-lead">Lead Author</span>
                                    @endif
                                    @if(isset($author['is_presenter']) && $author['is_presenter'])
                                        <span class="role-badge badge-presenter">Presenter</span>
                                    @endif
                                    @if(isset($author['will_attend']) && $author['will_attend'])
                                        <span class="role-badge badge-attending">Attending</span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <small class="text-muted">Email:</small>
                                    <p class="mb-2">{{ $author['email'] ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted">Mobile:</small>
                                    <p class="mb-2">{{ $author['mobile'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <small class="text-muted">Affiliation Address:</small>
                                    <p class="mb-2">{{ $author['city'] ?? '' }}, {{ $author['state'] ?? '' }}, {{ $author['country'] ?? '' }} - {{ $author['postal_code'] ?? '' }}</p>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <small class="text-muted">Affiliation:</small>
                                    <p class="mb-0">{{ $author['institution'] ?? 'N/A' }}, {{ $author['affiliation_city'] ?? '' }}, {{ $author['affiliation_country'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No authors added.</p>
                @endif
            </div>

            {{-- Presentation Preference --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-presentation"></i>
                    Presentation Preference
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Preferred Mode</td>
                        <td class="value-cell">{{ $draft->presentation_mode ?? 'Poster only' }}</td>
                    </tr>
                </table>
            </div>

            {{-- Attendance & Pricing --}}
            <div class="price-section">
                <h4 class="section-title">
                    <i class="fas fa-calculator"></i>
                    Attendance Summary & Registration Fee
                </h4>
                
                @php
                    $attendeeCount = 0;
                    $attendees = [];
                    if(isset($draft->authors)) {
                        foreach($draft->authors as $author) {
                            if(isset($author['will_attend']) && $author['will_attend']) {
                                $attendeeCount++;
                                $attendees[] = ($author['first_name'] ?? '') . ' ' . ($author['last_name'] ?? '');
                            }
                        }
                    }
                    
                    $currency = $draft->currency ?? 'INR';
                    $pricePerAttendee = $currency === 'INR' ? 2000 : 25;
                    $totalFee = $attendeeCount * $pricePerAttendee;
                    $currencySymbol = $currency === 'INR' ? '₹' : '$';
                @endphp
                
                <div class="mb-3">
                    <strong>Attendees ({{ $attendeeCount }}):</strong>
                    @if(count($attendees) > 0)
                        <ul class="mb-0 mt-2">
                            @foreach($attendees as $attendee)
                                <li>{{ $attendee }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">No attendees marked.</p>
                    @endif
                </div>
                
                <table class="price-table">
                    <tr>
                        <td class="label-cell">Number of Attendees</td>
                        <td class="value-cell">{{ $attendeeCount }}</td>
                    </tr>
                    <tr>
                        <td class="label-cell">Per Attendee Fee</td>
                        <td class="value-cell">{{ $currencySymbol }} {{ number_format($pricePerAttendee) }}</td>
                    </tr>
                    <tr class="total-row">
                        <td>Total Registration Fee</td>
                        <td>{{ $currencySymbol }} {{ number_format($totalFee) }}</td>
                    </tr>
                </table>
            </div>

            {{-- Permissions & Approvals --}}
            <div class="preview-section">
                <h4 class="section-title">
                    <i class="fas fa-check-circle"></i>
                    Permissions & Approvals
                </h4>
                <table class="info-table">
                    <tr>
                        <td class="label-cell">Publication Permission</td>
                        <td class="value-cell">
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> Granted
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="label-cell">Author(s) Approval</td>
                        <td class="value-cell">
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> Approved
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between flex-wrap gap-2 mt-4">
                <a href="{{ route('poster.register.edit', ['token' => $draft->token]) }}" 
                   class="btn btn-edit btn-lg">
                    <i class="fas fa-edit"></i> Edit Registration
                </a>
                
                <form action="{{ route('poster.submit', ['token' => $draft->token]) }}" method="POST" id="submitForm">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-lg" id="proceedBtn">
                        <i class="fas fa-arrow-right"></i> Proceed to Payment
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('submitForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('proceedBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
});
</script>
@endpush
@endsection
