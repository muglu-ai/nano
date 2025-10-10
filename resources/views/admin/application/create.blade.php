@extends('layouts.dashboard')
@section('title', 'Add New Application')
@section('content')

<style>
    /* Prevent browser autofill popup overlap */
    .form-control {
        position: relative;
        z-index: 1;
    }
    
    /* Improve form spacing and layout */
    .form-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 0.5rem;
        border: 1px solid #e9ecef;
    }
    
    .form-section h6 {
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #007bff;
        color: #007bff;
        font-weight: 600;
    }
    
    /* Consistent form field styling */
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #495057;
    }
    
    .form-control, .form-select {
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    
    /* Better button styling */
    .btn-submit {
        padding: 0.75rem 2rem;
        font-weight: 600;
        border-radius: 0.5rem;
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">Create New Application & User</h5>
                            <p class="text-sm mb-0 text-dark">
                                Create a new application and user account for an exhibitor.
                            </p>
                        </div>
                        <div class="ms-auto my-auto mt-lg-0 mt-4">
                            <a href="{{ route('application.lists') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Applications
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('application.store') }}">
                        @csrf
                        
                        <!-- Company Information -->
                        <div class="form-section">
                            <h6>Company Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="company_email" class="form-label">Company Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('company_email') is-invalid @enderror" 
                                           id="company_email" name="company_email" value="{{ old('company_email') }}" required>
                                    <small class="form-text text-muted">This email will be used to create the user account</small>
                                    @error('company_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="application_type" class="form-label">Application Type <span class="text-danger">*</span></label>
                                    <select class="form-select @error('application_type') is-invalid @enderror" id="application_type" name="application_type" required>
                                        <option value="">Select Application Type</option>
                                        <option value="exhibitor" {{ old('application_type') == 'exhibitor' ? 'selected' : '' }}>Exhibitor</option>
                                        <option value="sponsor" {{ old('application_type') == 'sponsor' ? 'selected' : '' }}>Sponsor</option>
                                    </select>
                                    @error('application_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Allocation (Admin Only) -->
                        <div class="form-section">
                            <h6>Ticket Allocation</h6>
                            <div id="ticket-allocations">
                                <div class="ticket-allocation-row mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label class="form-label">Ticket Type <span class="text-danger">*</span></label>
                                            <select class="form-select" name="ticket_ids[]" required>
                                                <option value="">Select Ticket Type</option>
                                                @foreach($tickets ?? [] as $ticket)
                                                    <option value="{{ $ticket->id }}">{{ ucfirst(str_replace('_', ' ', $ticket->ticket_type)) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Number of Tickets <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="ticket_counts[]" min="1" required>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-end">
                                            <button type="button" class="btn btn-danger btn-sm remove-ticket" style="display: none;">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <button type="button" class="btn btn-success btn-sm" id="add-ticket">
                                        <i class="fas fa-plus"></i> Add Another Ticket Type
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="form-section">
                            <h6>Address Information <small class="text-muted">(Optional)</small></h6>
                            <div class="row">
                                <div class="col-12">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3">{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-3">
                                    <label for="country_id" class="form-label">Country</label>
                                    <select class="form-select @error('country_id') is-invalid @enderror" id="country_id" name="country_id">
                                        <option value="">Select Country</option>
                                        @foreach($countries ?? [] as $country)
                                            <option value="{{ $country->id ?? '' }}" {{ old('country_id') == ($country->id ?? '') ? 'selected' : '' }}>
                                                {{ $country->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="state_id" class="form-label">State</label>
                                    <select class="form-select @error('state_id') is-invalid @enderror" id="state_id" name="state_id">
                                        <option value="">Select State</option>
                                        @foreach($states ?? [] as $state)
                                            <option value="{{ $state->id ?? '' }}" {{ old('state_id') == ($state->id ?? '') ? 'selected' : '' }}>
                                                {{ $state->name ?? '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('state_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="city_id" class="form-label">City</label>
                                    <input type="text" class="form-control @error('city_id') is-invalid @enderror" 
                                           id="city_id" name="city_id" value="{{ old('city_id') }}">
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                           id="postal_code" name="postal_code" value="{{ old('postal_code') }}">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Business Information -->
                      

                        <!-- Exhibition Details -->
                        

                        <!-- Additional Information -->
                    

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="{{ route('application.lists') }}" class="btn btn-secondary btn-submit">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-submit">
                                        <i class="fas fa-save me-2"></i>Create Application & User
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const addTicketBtn = document.getElementById('add-ticket');
    const ticketAllocations = document.getElementById('ticket-allocations');
    let ticketRowCount = 1;

    // Get ticket types from the first select element
    const firstSelect = document.querySelector('select[name="ticket_ids[]"]');
    const ticketTypesOptions = firstSelect ? firstSelect.innerHTML : '';

    addTicketBtn.addEventListener('click', function() {
        const newRow = document.createElement('div');
        newRow.className = 'ticket-allocation-row mb-3';
        newRow.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Ticket Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="ticket_ids[]" required>
                        ${ticketTypesOptions}
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Number of Tickets <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="ticket_counts[]" min="1" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-danger btn-sm remove-ticket">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        `;
        
        ticketAllocations.appendChild(newRow);
        ticketRowCount++;
        
        // Show remove buttons for all rows if more than 1
        if (ticketRowCount > 1) {
            document.querySelectorAll('.remove-ticket').forEach(btn => {
                btn.style.display = 'block';
            });
        }
    });

    // Handle remove button clicks
    ticketAllocations.addEventListener('click', function(e) {
        if (e.target.closest('.remove-ticket')) {
            e.target.closest('.ticket-allocation-row').remove();
            ticketRowCount--;
            
            // Hide remove buttons if only 1 row left
            if (ticketRowCount === 1) {
                document.querySelectorAll('.remove-ticket').forEach(btn => {
                    btn.style.display = 'none';
                });
            }
        }
    });
});
</script>
