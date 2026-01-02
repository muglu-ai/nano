@extends('tickets.public.layout')

@section('title', 'Register for Tickets')

@push('styles')
<style>
    .registration-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .registration-card {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 2.5rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .form-section {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #fff;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid rgba(102, 126, 234, 0.5);
    }

    .form-label {
        font-weight: 600;
        color: #fff;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-control, .form-select {
        background: rgba(255, 255, 255, 0.1);
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        color: #fff;
        transition: all 0.3s;
    }

    .form-control:focus, .form-select:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        color: #fff;
        outline: none;
    }

    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .form-control option {
        background: #1a1a2e;
        color: #fff;
    }

    .btn-primary {
        background: var(--primary-gradient);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        color: white;
    }

    .ticket-summary {
        background: var(--primary-gradient);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .ticket-summary h5 {
        color: #fff;
        font-weight: 700;
        margin-bottom: 1rem;
    }

    .ticket-summary p {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 0.5rem;
    }

    .text-danger {
        color: #f5576c;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .required-field::after {
        content: " *";
        color: #f5576c;
    }
</style>
@endpush

@section('content')
<div class="registration-container">
    <div class="registration-card">
        <h2 class="text-center mb-4" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
            Register for {{ $event->event_name }}
        </h2>

        @if($selectedTicketType)
            <div class="ticket-summary">
                <h5><i class="fas fa-ticket-alt me-2"></i>Selected Ticket</h5>
                <p><strong>{{ $selectedTicketType->name }}</strong></p>
                <p>Price: ₹{{ number_format($selectedTicketType->getCurrentPrice(), 2) }}</p>
                @if($selectedTicketType->description)
                    <p class="mb-0">{{ $selectedTicketType->description }}</p>
                @endif
            </div>
        @endif

        <form action="{{ route('tickets.store', $event->slug ?? $event->id) }}" method="POST" id="registrationForm">
            @csrf
            <input type="hidden" name="ticket_type_id" value="{{ $selectedTicketType->id ?? '' }}">

            <!-- Registration Information Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Registration Information
                </h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Registration Category</label>
                        <select name="registration_category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($registrationCategories as $category)
                                <option value="{{ $category->id }}" {{ old('registration_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('registration_category_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Ticket Type</label>
                        <select name="ticket_type_id" class="form-select" required id="ticket_type_id">
                            <option value="">Select Ticket Type</option>
                            @foreach($ticketTypes as $ticketType)
                                <option value="{{ $ticketType->id }}" 
                                        {{ ($selectedTicketType && $selectedTicketType->id == $ticketType->id) || old('ticket_type_id') == $ticketType->id ? 'selected' : '' }}
                                        data-price="{{ $ticketType->getCurrentPrice() }}">
                                    {{ $ticketType->name }} - ₹{{ number_format($ticketType->getCurrentPrice(), 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('ticket_type_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Number of Delegates</label>
                        <input type="number" name="delegate_count" class="form-control" 
                               value="{{ old('delegate_count', 1) }}" 
                               min="1" max="100" required id="delegate_count">
                        <small class="form-text text-muted">Enter the number of delegates attending</small>
                        @error('delegate_count')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Nationality</label>
                        <select name="nationality" class="form-select" required>
                            <option value="">Select Nationality</option>
                            <option value="Indian" {{ old('nationality') == 'Indian' ? 'selected' : '' }}>Indian</option>
                            <option value="International" {{ old('nationality') == 'International' ? 'selected' : '' }}>International</option>
                        </select>
                        @error('nationality')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Delegates Information Section -->
            <div class="form-section" id="delegates_section" style="display: none;">
                <h4 class="section-title">
                    <i class="fas fa-users me-2"></i>
                    Delegates Information
                </h4>
                <p class="text-muted mb-3">Please provide details for each delegate attending the event.</p>
                <div id="delegates_container">
                    <!-- Delegates will be dynamically added here -->
                </div>
            </div>

            <!-- Organisation Information Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-building me-2"></i>
                    Organisation Information
                </h4>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label required-field">Organisation Name</label>
                        <input type="text" name="organisation_name" class="form-control" 
                               value="{{ old('organisation_name') }}" 
                               placeholder="Enter organisation name" required>
                        @error('organisation_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Industry Sector</label>
                        <select name="industry_sector" class="form-select" required>
                            <option value="">Select Industry Sector</option>
                            @foreach(config('constants.sectors') as $sector)
                                <option value="{{ $sector }}" {{ old('industry_sector') == $sector ? 'selected' : '' }}>
                                    {{ $sector }}
                                </option>
                            @endforeach
                        </select>
                        @error('industry_sector')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Organisation Type</label>
                        <select name="organisation_type" class="form-select" required>
                            <option value="">Select Organisation Type</option>
                            @foreach(config('constants.organization_types') as $orgType)
                                <option value="{{ $orgType }}" {{ old('organisation_type') == $orgType ? 'selected' : '' }}>
                                    {{ $orgType }}
                                </option>
                            @endforeach
                        </select>
                        @error('organisation_type')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label required-field">Country</label>
                        <select name="country" class="form-select" required id="country">
                            <option value="">Select Country</option>
                            <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India</option>
                            <!-- Add more countries as needed -->
                        </select>
                        @error('country')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control" 
                               value="{{ old('state') }}" 
                               placeholder="Enter state">
                        @error('state')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" 
                               value="{{ old('city') }}" 
                               placeholder="Enter city">
                        @error('city')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" 
                               value="{{ old('phone') }}" 
                               placeholder="Enter phone number" required>
                        @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email') }}" 
                               placeholder="Enter email address">
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- GST Information Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-file-invoice-dollar me-2"></i>
                    GST Information
                </h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">GST Required</label>
                        <select name="gst_required" class="form-select" id="gst_required" required>
                            <option value="0" {{ old('gst_required', '0') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('gst_required') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('gst_required')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="gst_fields" style="display: {{ old('gst_required') == '1' ? 'block' : 'none' }};">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GSTIN Number</label>
                            <div class="input-group">
                                <input type="text" name="gstin" class="form-control" 
                                       value="{{ old('gstin') }}" 
                                       pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}"
                                       placeholder="Enter 15-digit GSTIN" 
                                       id="gstin_input">
                                <button type="button" class="btn btn-outline-primary" id="validateGstBtn">
                                    <i class="fas fa-search"></i> Validate
                                </button>
                            </div>
                            <div id="gst_loading" class="d-none mt-1">
                                <small class="text-info"><i class="fas fa-spinner fa-spin"></i> Fetching details...</small>
                            </div>
                            <div id="gst_feedback" class="mt-1"></div>
                            @error('gstin')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Click "Validate" to auto-fill company details from GST database</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">GST Legal Name</label>
                            <input type="text" name="gst_legal_name" class="form-control" 
                                   value="{{ old('gst_legal_name') }}" 
                                   placeholder="Legal name as per GST certificate" 
                                   id="gst_legal_name">
                            @error('gst_legal_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">GST Address</label>
                            <textarea name="gst_address" class="form-control" rows="3" 
                                      placeholder="Address as per GST certificate" 
                                      id="gst_address">{{ old('gst_address') }}</textarea>
                            @error('gst_address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">GST State</label>
                            <input type="text" name="gst_state" class="form-control" 
                                   value="{{ old('gst_state') }}" 
                                   placeholder="State as per GST certificate" 
                                   id="gst_state">
                            @error('gst_state')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Primary Contact Information -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-user me-2"></i>
                    Primary Contact Information
                </h4>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Full Name</label>
                        <input type="text" name="contact_name" class="form-control" 
                               value="{{ old('contact_name') }}" 
                               placeholder="Enter full name" required>
                        @error('contact_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Email Address</label>
                        <input type="email" name="contact_email" class="form-control" 
                               value="{{ old('contact_email') }}" 
                               placeholder="Enter email address" required>
                        @error('contact_email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Phone Number</label>
                        <input type="tel" name="contact_phone" class="form-control" 
                               value="{{ old('contact_phone') }}" 
                               placeholder="Enter phone number" required>
                        @error('contact_phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-arrow-right me-2"></i>
                    Continue to Payment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Update ticket price display when ticket type changes
    document.getElementById('ticket_type_id')?.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price');
        // You can update a price display element here if needed
    });

    // Handle delegate count change - dynamically show/hide delegate fields
    document.getElementById('delegate_count')?.addEventListener('change', function() {
        const count = parseInt(this.value) || 1;
        const delegatesSection = document.getElementById('delegates_section');
        const delegatesContainer = document.getElementById('delegates_container');
        
        if (count > 1) {
            delegatesSection.style.display = 'block';
            delegatesContainer.innerHTML = '';
            
            // Generate delegate forms
            for (let i = 1; i <= count; i++) {
                const delegateHtml = `
                    <div class="delegate-form mb-4 p-3" style="background: rgba(255, 255, 255, 0.02); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1);">
                        <h6 class="mb-3" style="color: #667eea;">
                            <i class="fas fa-user me-2"></i>Delegate ${i}
                        </h6>
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Salutation</label>
                                <select name="delegates[${i}][salutation]" class="form-select">
                                    <option value="">Select</option>
                                    <option value="Mr">Mr</option>
                                    <option value="Mrs">Mrs</option>
                                    <option value="Ms">Ms</option>
                                    <option value="Dr">Dr</option>
                                    <option value="Prof">Prof</option>
                                </select>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label required-field">First Name</label>
                                <input type="text" name="delegates[${i}][first_name]" class="form-control" 
                                       placeholder="Enter first name" required>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="form-label required-field">Last Name</label>
                                <input type="text" name="delegates[${i}][last_name]" class="form-control" 
                                       placeholder="Enter last name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label required-field">Email</label>
                                <input type="email" name="delegates[${i}][email]" class="form-control" 
                                       placeholder="Enter email address" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" name="delegates[${i}][phone]" class="form-control" 
                                       placeholder="Enter phone number">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">Job Title</label>
                                <input type="text" name="delegates[${i}][job_title]" class="form-control" 
                                       placeholder="Enter job title">
                            </div>
                        </div>
                    </div>
                `;
                delegatesContainer.innerHTML += delegateHtml;
            }
        } else {
            delegatesSection.style.display = 'none';
            delegatesContainer.innerHTML = '';
        }
    });

    // Trigger on page load if delegate_count has a value > 1
    document.addEventListener('DOMContentLoaded', function() {
        const delegateCount = document.getElementById('delegate_count');
        if (delegateCount && parseInt(delegateCount.value) > 1) {
            delegateCount.dispatchEvent(new Event('change'));
        }
    });

    // Toggle GST fields based on GST Required selection
    document.getElementById('gst_required')?.addEventListener('change', function() {
        const gstFields = document.getElementById('gst_fields');
        if (this.value == '1') {
            gstFields.style.display = 'block';
        } else {
            gstFields.style.display = 'none';
            // Clear GST fields when hidden
            document.getElementById('gstin_input').value = '';
            document.getElementById('gst_legal_name').value = '';
            document.getElementById('gst_address').value = '';
            document.getElementById('gst_state').value = '';
            document.getElementById('gst_feedback').innerHTML = '';
        }
    });

    // GST Validation
    document.getElementById('validateGstBtn')?.addEventListener('click', function() {
        const gstin = document.getElementById('gstin_input').value.trim();
        const loadingDiv = document.getElementById('gst_loading');
        const feedbackDiv = document.getElementById('gst_feedback');
        const btn = this;

        // Validate format
        const gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
        if (!gstPattern.test(gstin)) {
            feedbackDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times-circle"></i> Invalid GSTIN format. Please enter a valid 15-digit GSTIN.</small>';
            return;
        }

        // Show loading
        loadingDiv.classList.remove('d-none');
        feedbackDiv.innerHTML = '';
        btn.disabled = true;

        // Make API call
        fetch('{{ route("tickets.validate-gst") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ gstin: gstin })
        })
        .then(response => response.json())
        .then(data => {
            loadingDiv.classList.add('d-none');
            btn.disabled = false;

            if (data.success && data.gst) {
                // Auto-fill fields
                document.getElementById('gst_legal_name').value = data.gst.company_name || '';
                document.getElementById('gst_address').value = data.gst.billing_address || '';
                document.getElementById('gst_state').value = data.gst.state_name || '';
                
                feedbackDiv.innerHTML = '<small class="text-success"><i class="fas fa-check-circle"></i> GST details fetched successfully!</small>';
            } else {
                feedbackDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times-circle"></i> ' + (data.message || 'Could not fetch GST details. Please enter manually.') + '</small>';
            }
        })
        .catch(error => {
            loadingDiv.classList.add('d-none');
            btn.disabled = false;
            feedbackDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times-circle"></i> Error validating GST. Please try again or enter manually.</small>';
            console.error('GST validation error:', error);
        });
    });

    // Handle validation errors and success messages
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors->any())
            // Clear previous validation errors
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.remove();
            });

            // Collect error messages
            let errorMessages = [];
            const errors = @json($errors->getMessages());

            // Mark invalid fields and collect messages
            Object.keys(errors).forEach(field => {
                const errorMsg = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
                errorMessages.push(errorMsg);

                // Find the field element
                let fieldElement = document.querySelector('[name="' + field + '"]');
                if (fieldElement) {
                    fieldElement.classList.add('is-invalid');
                    
                    // Add invalid feedback
                    let feedback = fieldElement.nextElementSibling;
                    if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                        feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.style.color = '#f5576c';
                        feedback.style.fontSize = '0.875rem';
                        feedback.style.marginTop = '0.25rem';
                        fieldElement.parentNode.insertBefore(feedback, fieldElement.nextSibling);
                    }
                    feedback.textContent = errorMsg;
                }
            });

            // Show SweetAlert with all errors
            if (errorMessages.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: '<div style="text-align: left; max-height: 400px; overflow-y: auto;"><ul style="margin: 0; padding-left: 20px; color: #333;"><li>' + errorMessages.join('</li><li>') + '</li></ul></div>',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#667eea',
                    width: '600px',
                    customClass: {
                        popup: 'swal2-popup-dark'
                    }
                }).then(() => {
                    // Scroll to first error field
                    const firstError = document.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    }
                });
            }
        @endif

        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: '{{ session('success') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#667eea'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                confirmButtonText: 'OK',
                confirmButtonColor: '#667eea'
            });
        @endif
    });

    // Handle form submission with AJAX for better error handling
    document.getElementById('registrationForm')?.addEventListener('submit', function(e) {
        // Clear previous validation
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.remove();
        });
    });
</script>

<style>
    .is-invalid {
        border-color: #f5576c !important;
        background: rgba(245, 87, 108, 0.1) !important;
    }

    .invalid-feedback {
        display: block;
        color: #f5576c;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .swal2-popup {
        background: #1a1a2e !important;
        color: #fff !important;
    }

    .swal2-title {
        color: #fff !important;
    }

    .swal2-content {
        color: #fff !important;
    }
</style>
@endpush

