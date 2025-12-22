@extends('layouts.startup-zone')

@section('title', 'Preview Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@section('content')
<div class="container py-5">
    {{-- Step Indicator --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="step-indicator">
                <div class="step-item completed">
                    <div class="step-number">1</div>
                    <div class="step-label">Exhibitor Details</div>
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
            
            @if(isset($application))
                {{-- Application Preview (After Draft Restoration) --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0"><i class="fas fa-check-circle"></i> Application Created Successfully</h4>
                    </div>
                    <div class="card-body">
                        <p class="alert alert-info">
                            <strong>Application ID:</strong> {{ $application->application_id }}<br>
                            Please review your details below and proceed to payment.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Company Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Company Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Company Name:</strong><br>
                            {{ $application->company_name ?? $draft->company_name ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Company Email:</strong><br>
                            {{ $application->company_email ?? $draft->company_email ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Address:</strong><br>
                            {{ $application->address ?? $draft->address ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>City:</strong><br>
                            {{ $application->city_id ?? $draft->city_id ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>State:</strong><br>
                            @if(isset($application))
                                {{ $application->state->name ?? 'N/A' }}
                            @elseif(isset($draft) && $draft->state_id)
                                {{ \App\Models\State::find($draft->state_id)->name ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Postal Code:</strong><br>
                            {{ $application->postal_code ?? $draft->postal_code ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Country:</strong><br>
                            @if(isset($application))
                                {{ $application->country->name ?? 'N/A' }}
                            @elseif(isset($draft) && $draft->country_id)
                                {{ \App\Models\Country::find($draft->country_id)->name ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Website:</strong><br>
                            <a href="{{ $application->website ?? $draft->website ?? '#' }}" target="_blank">
                                {{ $application->website ?? $draft->website ?? 'N/A' }}
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Telephone:</strong><br>
                            {{ $application->landline ?? $draft->landline ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Company Age:</strong><br>
                            @php
                                $companyAge = $application->companyYears ?? $application->how_old_startup ?? $draft->how_old_startup ?? null;
                            @endphp
                            @if($companyAge)
                                {{ $companyAge }} Year{{ $companyAge > 1 ? 's' : '' }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tax Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Tax Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>GST Status:</strong><br>
                            {{ (($application->gst_compliance ?? $draft->gst_compliance ?? false) ? 'Registered' : 'Unregistered') }}
                        </div>
                        @if(($application->gst_compliance ?? $draft->gst_compliance ?? false))
                        <div class="col-md-6 mb-3">
                            <strong>GST Number:</strong><br>
                            {{ $application->gst_no ?? $draft->gst_no ?? 'N/A' }}
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <strong>PAN Number:</strong><br>
                            {{ $application->pan_no ?? $draft->pan_no ?? 'N/A' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sector Information --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Sector Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Sector:</strong><br>
                            @if(isset($application))
                                {{ \DB::table('sectors')->where('id', $application->sector_id)->value('name') ?? 'N/A' }}
                            @elseif(isset($draft) && $draft->sector_id)
                                {{ \DB::table('sectors')->where('id', $draft->sector_id)->value('name') ?? 'N/A' }}
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Subsector:</strong><br>
                            @if(isset($application) && $application->subSector)
                                {{ $application->subSector }}
                            @elseif(isset($draft) && $draft->subSector)
                                {{ $draft->subSector }}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Person Details --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Contact Person Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if(isset($contact))
                            <div class="col-md-6 mb-3">
                                <strong>Name:</strong><br>
                                {{ $contact->salutation ?? '' }} {{ $contact->first_name }} {{ $contact->last_name }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Designation:</strong><br>
                                {{ $contact->job_title ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong><br>
                                {{ $contact->email }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Mobile:</strong><br>
                                {{ $contact->contact_number }}
                            </div>
                        @elseif(isset($draft) && $draft->contact_data)
                            <div class="col-md-6 mb-3">
                                <strong>Name:</strong><br>
                                {{ ($draft->contact_data['title'] ?? '') }} {{ $draft->contact_data['first_name'] ?? '' }} {{ $draft->contact_data['last_name'] ?? '' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Designation:</strong><br>
                                {{ $draft->contact_data['designation'] ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong><br>
                                {{ $draft->contact_data['email'] ?? 'N/A' }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Mobile:</strong><br>
                                +{{ $draft->contact_data['country_code'] ?? '' }} {{ $draft->contact_data['mobile'] ?? 'N/A' }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Pricing Summary --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Pricing Summary</h4>
                </div>
                <div class="card-body">
                    @if(isset($invoice))
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Base Price:</strong></td>
                                <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->price, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>GST (18%):</strong></td>
                                <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->gst, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Processing Charges ({{ $invoice->processing_chargesRate ?? 3 }}%):</strong></td>
                                <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->processing_charges, 2) }}</td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Total Amount:</strong></td>
                                <td class="text-end"><strong>{{ $invoice->currency }} {{ number_format($invoice->total_final_price, 2) }}</strong></td>
                            </tr>
                        </table>
                    @elseif(isset($pricing))
                        <table class="table table-bordered">
                            <tr>
                                <td><strong>Base Price:</strong></td>
                                <td class="text-end">{{ $pricing['currency'] }} {{ number_format($pricing['base_price'], 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>GST (18%):</strong></td>
                                <td class="text-end">{{ $pricing['currency'] }} {{ number_format($pricing['gst'], 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Processing Charges ({{ $pricing['processing_rate'] }}%):</strong></td>
                                <td class="text-end">{{ $pricing['currency'] }} {{ number_format($pricing['processing_charges'], 2) }}</td>
                            </tr>
                            <tr class="table-success">
                                <td><strong>Total Amount:</strong></td>
                                <td class="text-end"><strong>{{ $pricing['currency'] }} {{ number_format($pricing['total'], 2) }}</strong></td>
                            </tr>
                        </table>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between">
                @if(isset($application))
                    <a href="{{ route('startup-zone.register') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Form
                    </a>
                    <a href="{{ route('startup-zone.payment', $application->application_id) }}" class="btn btn-success btn-lg">
                        Proceed to Payment <i class="fas fa-arrow-right"></i>
                    </a>
                @else
                    <a href="{{ route('startup-zone.register') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Edit Details
                    </a>
                    <button type="button" class="btn btn-success btn-lg" id="confirmAndProceed">
                        Confirm & Proceed to Payment <i class="fas fa-check"></i>
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

@if(!isset($application))
<script>
document.getElementById('confirmAndProceed')?.addEventListener('click', function() {
    // Restore draft to application
    fetch('{{ route("startup-zone.restore-draft") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            alert('Error: ' + (data.message || 'Failed to create application'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>
@endif
@push('styles')
<style>
    .step-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 10px;
    }
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        flex: 1;
    }
    .step-number {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: #e0e0e0;
        color: #666;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
        border: 3px solid #e0e0e0;
    }
    .step-item.active .step-number {
        background: var(--primary-color);
        color: white;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(27, 55, 131, 0.2);
    }
    .step-item.completed .step-number {
        background: #28a745;
        color: white;
        border-color: #28a745;
        font-size: 0;
    }
    .step-item.completed .step-number::before {
        content: '✓';
        font-size: 1.5rem;
        display: block;
    }
    .step-label {
        font-size: 0.9rem;
        color: #666;
        font-weight: 500;
        text-align: center;
    }
    .step-item.active .step-label {
        color: var(--primary-color);
        font-weight: 600;
    }
    .step-item.completed .step-label {
        color: #28a745;
    }
    .step-connector {
        flex: 1;
        height: 3px;
        background: #e0e0e0;
        margin: 0 1rem;
        margin-top: -25px;
        position: relative;
        z-index: 0;
    }
    .step-item.completed ~ .step-connector,
    .step-item.active ~ .step-connector {
        background: var(--primary-color);
    }
    @media (max-width: 768px) {
        .step-indicator {
            padding: 1rem 0.5rem;
        }
        .step-number {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        .step-label {
            font-size: 0.75rem;
        }
        .step-connector {
            margin: 0 0.5rem;
            margin-top: -20px;
        }
    }
</style>
@endpush

@endsection
