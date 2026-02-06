@extends('sparx.layout')  <!-- create a sparx.layout if different from enquiry -->

@section('title', 'NANO SparX Application Form')

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css">
    <style>
        .form-section { margin-bottom: 2rem; }
        .section-title { font-size: 1.4rem; font-weight: 600; margin-bottom: 1rem; color: #2c3e50; }
        .word-counter { font-size: 0.9rem; color: #666; margin-top: 0.5rem; }
        .word-counter.warning { color: #f39c12; }
        .word-counter.danger { color: #e74c3c; }
        .conditional { display: none; }
        .required::after { content: " *"; color: #e74c3c; }
    </style>
@endpush

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white text-center py-4">
            <h2 class="mb-0">NanoSparX Application Form</h2>
            <p class="mb-0 mt-2">{{ $event->event_name ?? 'NanoSparX Program' }} {{ $event->event_year ?? date('Y') }}</p>
        </div>

        <div class="card-body p-4 p-md-5">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Please correct the following errors:</strong>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('sparx.store') }}" method="POST" id="sparxForm">
                @csrf

                <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">
                <input type="hidden" name="event_year" value="{{ $event->event_year ?? date('Y') }}">

                <!-- SECTION 1: Personal Information -->
                <div class="form-section">
                    <h3 class="section-title">1. Personal Information</h3>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $application?->name ?? '') }}" required>
                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Designation</label>
                            <input type="text" name="designation" class="form-control" value="{{ old('designation', $application?->designation ?? '') }}" required>
                            @error('designation') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label required">Organization</label>
                            <input type="text" name="organization" class="form-control" value="{{ old('organization', $application?->organization ?? '') }}" required>
                            @error('organization') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: Contact Information -->
                <div class="form-section">
                    <h3 class="section-title">2. Contact Information</h3>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $application?->email ?? '') }}" required>
                            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label required">Contact Number</label>
                            <input type="tel" name="phone_number" id="phone_number" class="form-control" value="{{ old('phone_number', $application?->phone_number ?? '') }}" required>
                            <input type="hidden" name="phone_country_code" id="phone_country_code" value="{{ old('phone_country_code', $application?->phone_country_code ?? '') }}">
                            @error('phone_number') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $application?->address ?? '') }}</textarea>
                            @error('address') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', $application?->city ?? '') }}">
                            @error('city') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">State</label>
                            <input type="text" name="state" class="form-control" value="{{ old('state', $application?->state ?? '') }}">
                            @error('state') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label required">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', $application?->country ?? 'India') }}" required>
                            @error('country') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Postal Code</label>
                            <input type="text" name="postal_code" class="form-control" value="{{ old('postal_code', $application?->postal_code ?? '') }}">
                            @error('postal_code') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: Startup / Idea Details -->
                <div class="form-section">
                    <h3 class="section-title">3. Startup / Idea Details</h3>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label required">Startup / Idea Name</label>
                            <input type="text" name="startup_idea_name" class="form-control" value="{{ old('startup_idea_name', $application?->startup_idea_name ?? '') }}" required maxlength="120">
                            @error('startup_idea_name') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Website (if any)</label>
                            <input type="url" name="website" class="form-control" value="{{ old('website', $application?->website ?? '') }}" placeholder="https://example.com">
                            @error('website') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Sector</label>
                            <select name="sector" class="form-select">
                                <option value="">-- Select Sector --</option>
                                @foreach(['Medicine', 'Electronics', 'Agriculture', 'Healthcare', 'Manufacturing', 'Environment/Energy', 'Others'] as $sector)
                                    <option value="{{ $sector }}" {{ old('sector', $application?->sector ?? '') == $sector ? 'selected' : '' }}>{{ $sector }}</option>
                                @endforeach
                            </select>
                            @error('sector') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: Idea Description & Details -->
                <div class="form-section">
                    <h3 class="section-title">4. Idea Description & Details</h3>

                    <div class="mb-4">
                        <label class="form-label required">Idea Description (max 500 words)</label>
                        <textarea name="idea_description" class="form-control" rows="8" maxlength="3000" required>{{ old('idea_description', $application?->idea_description ?? '') }}</textarea>
                        <div class="word-counter" id="descCounter">500 words remaining</div>
                        @error('idea_description') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label required">Any Products</label>
                        <textarea name="products" class="form-control" rows="5" required>{{ old('products', $application?->products ?? '') }}</textarea>
                        @error('products') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label required">Key Successes so far</label>
                        <textarea name="key_successes" class="form-control" rows="5" required>{{ old('key_successes', $application?->key_successes ?? '') }}</textarea>
                        @error('key_successes') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label required">Potential Market Size</label>
                        <input type="text" name="potential_market_size" class="form-control" value="{{ old('potential_market_size', $application?->potential_market_size ?? '') }}" required>
                        @error('potential_market_size') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label required">Company Size (No. of Employees)</label>
                        <input type="number" name="company_size_employees" class="form-control" min="0" value="{{ old('company_size_employees', $application?->company_size_employees ?? 0) }}" required>
                        @error('company_size_employees') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- SECTION 5: Registration Status -->
                <div class="form-section">
                    <h3 class="section-title">5. Registration Status</h3>

                    <div class="mb-4">
                        <label class="form-label required">Is your start-up registered?</label>
                        <select name="is_registered" id="is_registered" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="1" {{ old('is_registered', $application?->is_registered ? 1 : 0) == 1 ? 'selected' : '' }}>Yes</option>
                            <option value="0" {{ old('is_registered', $application?->is_registered ? 1 : 0) == 0 ? 'selected' : '' }}>No</option>
                        </select>
                        @error('is_registered') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4 conditional" id="registrationDateField">
                        <label class="form-label">Date of Start-up Registration</label>
                        <input type="date" name="registration_date" class="form-control" value="{{ old('registration_date', $application?->registration_date?->format('Y-m-d') ?? '') }}">
                        @error('registration_date') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Consent -->
                <div class="form-section">
                    <div class="form-check">
                        <input type="checkbox" name="consent_given" id="consent" class="form-check-input" value="1" checked required>
                        <label class="form-check-label" for="consent">
                            I consent to receive emails from the organizer regarding this application.
                        </label>
                        @error('consent_given') <div class="text-danger">{{ $message }}</div> @enderror
                    </div>
                </div>

                <!-- Submit -->
                <div class="text-center mt-5">
                    <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                        Submit Application <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
    <script>
        // Phone input
        const phoneInput = document.getElementById('phone_number');
        if (phoneInput) {
            const iti = window.intlTelInput(phoneInput, {
                initialCountry: 'in',
                preferredCountries: ['in'],
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            });
            const countryCodeInput = document.getElementById('phone_country_code');
            countryCodeInput.value = iti.getSelectedCountryData().dialCode;
            phoneInput.addEventListener('countrychange', () => {
                countryCodeInput.value = iti.getSelectedCountryData().dialCode;
            });
        }
        // Conditional registration date field
        const isRegistered = document.getElementById('is_registered');
        const regDateField = document.getElementById('registrationDateField');
        if (isRegistered && regDateField) {
            function toggleRegDate() {
                regDateField.style.display = isRegistered.value === '1' ? 'block' : 'none';
            }
            isRegistered.addEventListener('change', toggleRegDate);
            toggleRegDate(); // initial state
        }
        // Word counter for idea_description (500 words)
        const descTextarea = document.querySelector('textarea[name="idea_description"]');
        const descCounter = document.getElementById('descCounter');
        if (descTextarea && descCounter) {
            descTextarea.addEventListener('input', () => {
                const words = descTextarea.value.trim().split(/\s+/).filter(Boolean).length;
                const remaining = 500 - words;
                descCounter.textContent = remaining + ' words remaining';
                descCounter.className = 'word-counter';
                if (remaining < 50) descCounter.classList.add('danger');
                else if (remaining < 100) descCounter.classList.add('warning');
            });
        }
        // Basic form progress (optional)
        // You can expand this like in enquiry
    </script>
@endpush