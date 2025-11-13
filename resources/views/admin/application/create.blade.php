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
    
    /* Multiple select styling */
    select[multiple] {
        min-height: 120px;
        padding: 0.5rem;
    }
    
    select[multiple] option {
        padding: 0.5rem;
        margin: 2px 0;
        border-radius: 0.25rem;
    }
    
    select[multiple] option:checked {
        background-color: #007bff;
        color: white;
    }
    
    /* Form text styling */
    .form-text {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    /* Conditional field styling */
    #stall_size_field, #stall_category_field {
        transition: all 0.3s ease-in-out;
        overflow: hidden;
    }
    
    #stall_size_field.show, #stall_category_field.show {
        opacity: 1;
        max-height: 200px;
    }
    
    #stall_size_field.hide, #stall_category_field.hide {
        opacity: 0;
        max-height: 0;
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
                                        <option value="exhibitor+sponsor" {{ old('application_type') == 'exhibitor+sponsor' ? 'selected' : '' }}>Exhibitor + Sponsorship</option>
                                        <option value="co-exhibitor" {{ old('application_type') == 'co-exhibitor' ? 'selected' : '' }}>Co-Exhibitor</option>
                                    </select>
                                    @error('application_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6" id="stall_size_field" style="display: none;">
                                    <label for="stall_size" class="form-label">Stall Size (SQM) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('stall_size') is-invalid @enderror" 
                                           id="stall_size" name="stall_size" value="{{ old('stall_size') }}" 
                                           placeholder="Enter stall size in square meters" min="1" step="0.1">
                                    <small class="form-text text-muted">Enter the desired stall size in square meters</small>
                                    @error('stall_size')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3" id="stall_category_field" style="display: none;">
                                <div class="col-md-6">
                                    <label for="stall_category" class="form-label">Stall Category <span class="text-danger">*</span></label>
                                    <select class="form-select @error('stall_category') is-invalid @enderror" id="stall_category" name="stall_category">
                                        <option value="">Select Stall Category</option>
                                        <option value="Startup Booth" {{ old('stall_category') == 'Startup Booth' ? 'selected' : '' }}>Startup Booth</option>
                                        <option value="Shell Scheme" {{ old('stall_category') == 'Shell Scheme' ? 'selected' : '' }}>Shell Scheme</option>
                                        <option value="Bare Space" {{ old('stall_category') == 'Bare Space' ? 'selected' : '' }}>Bare Space</option>
                                    </select>
                                    <small class="form-text text-muted">Select the type of stall category</small>
                                    @error('stall_category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6" id="stall_number_field" style="display: none;">
                                    <label for="stall_number" class="form-label">Stall Number</label>
                                    <input type="text" class="form-control @error('stall_number') is-invalid @enderror" 
                                           id="stall_number" name="stall_number" value="{{ old('stall_number') }}" 
                                           placeholder="Enter stall number">
                                    <small class="form-text text-muted">Enter the stall number</small>
                                    @error('stall_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3" id="pavilion_name_field" style="display: none;">
                                <div class="col-md-6">
                                    <label for="pavilionName" class="form-label">Pavilion Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('pavilionName') is-invalid @enderror"
                                           id="pavilionName" name="pavilionName" value="{{ old('pavilionName') }}"
                                           placeholder="Enter Pavilion Name">
                                    <small class="form-text text-muted">Required if Application Type is Co-Exhibitor</small>
                                    @error('pavilionName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="form-section">
                            <h6>Contact Information <small class="text-muted">(Optional)</small></h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="sectors" class="form-label">Sector</label>
                                    <select class="form-select @error('sectors') is-invalid @enderror" id="sectors" name="sectors">
                                        <option value="">Select Sector</option>
                                        @foreach($sectors ?? [] as $sector)
                                            <option value="{{ $sector->id }}" {{ old('sectors') == $sector->id ? 'selected' : '' }}>
                                                {{ $sector->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sectors')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="contact_person" class="form-label">Contact Person Full Name</label>
                                    <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                           id="contact_person" name="contact_person" value="{{ old('contact_person') }}" 
                                           placeholder="Enter full name">
                                    @error('contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="country_code" class="form-label">Country Code</label>
                                    <select class="form-select @error('country_code') is-invalid @enderror" id="country_code" name="country_code">
                                        <option value="">Select Country Code</option>
                                        @foreach($countries ?? [] as $country)
                                            <option value="{{ $country->code }}" {{ old('country_code') == $country->code ? 'selected' : '' }}>
                                                {{ $country->code }} - {{ $country->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('country_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="mobile_number" class="form-label">Mobile Number</label>
                                    <input type="tel" class="form-control @error('mobile_number') is-invalid @enderror" 
                                           id="mobile_number" name="mobile_number" value="{{ old('mobile_number') }}" 
                                           placeholder="Enter mobile number">
                                    @error('mobile_number')
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
    const applicationTypeSelect = document.getElementById('application_type');
    const stallSizeField = document.getElementById('stall_size_field');
    const stallSizeInput = document.getElementById('stall_size');
    const stallCategoryField = document.getElementById('stall_category_field');
    const stallCategorySelect = document.getElementById('stall_category');
    const stallNumberField = document.getElementById('stall_number_field');
    const stallNumberInput = document.getElementById('stall_number');
    const pavilionNameField = document.getElementById('pavilion_name_field');
    const pavilionNameInput = document.getElementById('pavilionName');
    let ticketRowCount = 1;

    // ===== Dynamic States by Country =====
    const countrySelect = document.getElementById('country_id');
    const stateSelect = document.getElementById('state_id');
    const getStatesUrl = "{{ route('get.states') }}";
    const csrfToken = "{{ csrf_token() }}";

    function setStateOptions(states, selectedStateId) {
        // Reset options
        stateSelect.innerHTML = '';
        const defaultOpt = document.createElement('option');
        defaultOpt.value = '';
        defaultOpt.textContent = 'Select State';
        stateSelect.appendChild(defaultOpt);

        // Populate
        states.forEach(function(state) {
            const opt = document.createElement('option');
            opt.value = state.id;
            opt.textContent = state.name;
            if (selectedStateId && String(selectedStateId) === String(state.id)) {
                opt.selected = true;
            }
            stateSelect.appendChild(opt);
        });
    }

    async function loadStatesForCountry(countryId, selectedStateId) {
        if (!countryId) {
            setStateOptions([], null);
            return;
        }

        // Show loading indicator
        stateSelect.innerHTML = '';
        const loadingOpt = document.createElement('option');
        loadingOpt.value = '';
        loadingOpt.textContent = 'Loading...';
        stateSelect.appendChild(loadingOpt);

        try {
            // Use FormData to send form data (not JSON) - matches controller expectations
            const formData = new FormData();
            formData.append('country_id', countryId);
            formData.append('_token', csrfToken);

            const response = await fetch(getStatesUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            if (!response.ok) {
                throw new Error('Failed to fetch states');
            }
            
            const states = await response.json();
            setStateOptions(Array.isArray(states) ? states : [], selectedStateId);
        } catch (e) {
            console.error('Error loading states:', e);
            setStateOptions([], null);
            alert('Error fetching states. Please try again.');
        }
    }

    // Bind change event
    if (countrySelect && stateSelect) {
        countrySelect.addEventListener('change', function() {
            loadStatesForCountry(this.value, null);
        });

        // On initial load, if a country is preselected, load its states and keep old state selected
        const initialCountry = countrySelect.value;
        const initialState = "{{ old('state_id') }}";
        if (initialCountry) {
            loadStatesForCountry(initialCountry, initialState);
        }
    }

    // Handle application type change
    function handleApplicationTypeChange() {
        const appType = applicationTypeSelect.value;
        
        // Co-Exhibitor => show Pavilion Name; hide stall fields
        if (appType === 'co-exhibitor') {
            // Hide stall-related fields
            stallSizeField.classList.remove('show');
            stallSizeField.classList.add('hide');
            setTimeout(() => { stallSizeField.style.display = 'none'; }, 300);
            stallSizeInput.required = false;
            stallSizeInput.value = '';

            stallCategoryField.classList.remove('show');
            stallCategoryField.classList.add('hide');
            setTimeout(() => { stallCategoryField.style.display = 'none'; }, 300);
            stallCategorySelect.required = false;
            stallCategorySelect.value = '';

            stallNumberField.classList.remove('show');
            stallNumberField.classList.add('hide');
            setTimeout(() => { stallNumberField.style.display = 'none'; }, 300);
            stallNumberInput.value = '';

            // Show pavilion name
            pavilionNameField.style.display = 'block';
            setTimeout(() => {
                pavilionNameField.classList.remove('hide');
                pavilionNameField.classList.add('show');
            }, 10);
            pavilionNameInput.required = true;
        } else if (appType === 'exhibitor' || appType === 'exhibitor+sponsor') {
            // Show stall size field for both exhibitor and exhibitor+sponsor
            stallSizeField.style.display = 'block';
            setTimeout(() => {
                stallSizeField.classList.remove('hide');
                stallSizeField.classList.add('show');
            }, 10);
            stallSizeInput.required = true;
            
            // Show stall category field for both exhibitor and exhibitor+sponsor
            stallCategoryField.style.display = 'block';
            setTimeout(() => {
                stallCategoryField.classList.remove('hide');
                stallCategoryField.classList.add('show');
            }, 10);
            stallCategorySelect.required = true;
            
            // Show stall number field for both exhibitor and exhibitor+sponsor
            stallNumberField.style.display = 'block';
            setTimeout(() => {
                stallNumberField.classList.remove('hide');
                stallNumberField.classList.add('show');
            }, 10);

            // Hide pavilion name
            pavilionNameField.classList.remove('show');
            pavilionNameField.classList.add('hide');
            setTimeout(() => { pavilionNameField.style.display = 'none'; }, 300);
            pavilionNameInput.required = false;
            pavilionNameInput.value = '';
        } else {
            // Hide all stall fields for sponsor or empty
            stallSizeField.classList.remove('show');
            stallSizeField.classList.add('hide');
            setTimeout(() => {
                stallSizeField.style.display = 'none';
            }, 300);
            stallSizeInput.required = false;
            stallSizeInput.value = '';
            
            stallCategoryField.classList.remove('show');
            stallCategoryField.classList.add('hide');
            setTimeout(() => {
                stallCategoryField.style.display = 'none';
            }, 300);
            stallCategorySelect.required = false;
            stallCategorySelect.value = '';
            
            stallNumberField.classList.remove('show');
            stallNumberField.classList.add('hide');
            setTimeout(() => {
                stallNumberField.style.display = 'none';
            }, 300);
            stallNumberInput.value = '';

            // Hide pavilion name
            pavilionNameField.classList.remove('show');
            pavilionNameField.classList.add('hide');
            setTimeout(() => { pavilionNameField.style.display = 'none'; }, 300);
            pavilionNameInput.required = false;
            pavilionNameInput.value = '';
        }
    }

    applicationTypeSelect.addEventListener('change', handleApplicationTypeChange);

    // Check initial state on page load
    const initialAppType = applicationTypeSelect.value;
    if (initialAppType === 'exhibitor' || initialAppType === 'exhibitor+sponsor') {
        handleApplicationTypeChange();
    }

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
