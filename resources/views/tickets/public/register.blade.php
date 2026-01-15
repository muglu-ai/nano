@extends('enquiry.layout')

@section('title', 'Register for Tickets')

@push('head-links')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css">
@endpush

@push('styles')
<style>
    .registration-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .registration-container .registration-progress {
        margin-bottom: 2rem;
    }

    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e0e0e0;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: var(--text-primary);
        padding-bottom: 0.75rem;
        border-bottom: 2px solid var(--progress-inactive);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-title i {
        color: var(--primary-color);
    }

    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .form-control, .form-select {
        background: #ffffff;
        border: 1px solid #ced4da;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        color: var(--text-primary);
        transition: all 0.3s;
    }

    .form-control:focus, .form-select:focus {
        background: #ffffff;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(106, 27, 154, 0.25);
        color: var(--text-primary);
        outline: none;
    }

    .form-control::placeholder {
        color: var(--text-light);
    }

    .form-control option {
        background: #ffffff;
        color: var(--text-primary);
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-dark) 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .required-field::after {
        content: " *";
        color: #dc3545;
    }

    .delegate-form {
        background: #ffffff;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .text-danger {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .text-muted {
        color: var(--text-secondary);
    }

    .alert {
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
    }

    .alert-danger {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        color: #721c24;
    }

    .alert-success {
        background: #d4edda;
        border-left: 4px solid #28a745;
        color: #155724;
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-dark) 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 1rem 3rem;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="form-card">
    <div class="form-header">
        <h2><i class="fas fa-ticket-alt me-2"></i>Register for Tickets</h2>
        <p>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
    </div>

    <div class="form-body">
        <!-- Progress Bar -->
        @include('tickets.public.partials.progress-bar', ['currentStep' => 1])
        
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tickets.store', $event->slug ?? $event->id) }}" method="POST" id="registrationForm">
            @csrf

            <!-- Registration Information Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-info-circle"></i>
                    Registration Information
                </h4>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label required-field">Ticket Type</label>
                        @if(isset($isTicketTypeLocked) && $isTicketTypeLocked)
                            {{-- Hidden field to submit the value --}}
                            <input type="hidden" name="ticket_type_id" value="{{ $selectedTicketType->slug }}">
                            {{-- Display field (readonly) with data attributes for day selection --}}
                            @php
                                $nationalityForPrice = $selectedNationality ?? 'national';
                                $price = $selectedTicketType->getCurrentPrice($nationalityForPrice);
                                $currency = ($nationalityForPrice === 'international') ? '$' : '₹';
                                $priceFormat = ($nationalityForPrice === 'international') ? number_format($price, 2) : number_format($price, 0);
                            @endphp
                            <input type="text" class="form-control" 
                                   id="locked_ticket_type"
                                   value="{{ $selectedTicketType->name }} - {{ $currency }}{{ $priceFormat }}" 
                                   readonly 
                                   style="background-color: #e9ecef; cursor: not-allowed;"
                                   data-price-national="{{ $selectedTicketType->getCurrentPrice('national') }}"
                                   data-price-international="{{ $selectedTicketType->getCurrentPrice('international') }}"
                                   data-per-day-price-national="{{ $selectedTicketType->getPerDayPrice('national') ?? '' }}"
                                   data-per-day-price-international="{{ $selectedTicketType->getPerDayPrice('international') ?? '' }}"
                                   data-has-per-day-pricing="{{ $selectedTicketType->hasPerDayPricing() ? '1' : '0' }}"
                                   data-enable-day-selection="{{ $selectedTicketType->enable_day_selection ? '1' : '0' }}"
                                   data-all-days-access="{{ $selectedTicketType->all_days_access ? '1' : '0' }}"
                                   data-include-all-days-option="{{ $selectedTicketType->include_all_days_option ? '1' : '0' }}"
                                   data-available-days="{{ json_encode($selectedTicketType->getAllAccessibleDays()->map(function($day) { return ['id' => $day->id, 'label' => $day->label, 'date' => $day->date->format('M d, Y')]; })) }}">
                        @else
                            <select name="ticket_type_id" class="form-select" id="ticket_type_select" required>
                                <option value="">Select Ticket Type</option>
                                @foreach($ticketTypes as $ticketType)
                                    @php
                                        $nationalityForPrice = old('nationality', $selectedNationality ?? 'national');
                                        $perDayPrice = $ticketType->getPerDayPrice($nationalityForPrice);
                                        $price = $perDayPrice ?? $ticketType->getCurrentPrice($nationalityForPrice);
                                        $currency = ($nationalityForPrice === 'international') ? '$' : '₹';
                                        $priceFormat = ($nationalityForPrice === 'international') ? number_format($price, 2) : number_format($price, 0);
                                        $priceLabel = $perDayPrice ? '/day' : '';
                                    @endphp
                                    <option value="{{ $ticketType->slug }}" 
                                            data-price-national="{{ $ticketType->getCurrentPrice('national') }}"
                                            data-price-international="{{ $ticketType->getCurrentPrice('international') }}"
                                            data-per-day-price-national="{{ $ticketType->getPerDayPrice('national') ?? '' }}"
                                            data-per-day-price-international="{{ $ticketType->getPerDayPrice('international') ?? '' }}"
                                            data-has-per-day-pricing="{{ $ticketType->hasPerDayPricing() ? '1' : '0' }}"
                                            data-enable-day-selection="{{ $ticketType->enable_day_selection ? '1' : '0' }}"
                                            data-all-days-access="{{ $ticketType->all_days_access ? '1' : '0' }}"
                                            data-include-all-days-option="{{ $ticketType->include_all_days_option ? '1' : '0' }}"
                                            data-available-days="{{ json_encode($ticketType->getAllAccessibleDays()->map(function($day) { return ['id' => $day->id, 'label' => $day->label, 'date' => $day->date->format('M d, Y')]; })) }}"
                                            {{ (old('ticket_type_id') == $ticketType->slug || (isset($selectedTicketType) && $selectedTicketType && $selectedTicketType->id == $ticketType->id)) ? 'selected' : '' }}>
                                        {{ $ticketType->name }} - {{ $currency }}{{ $priceFormat }}{{ $priceLabel }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        @error('ticket_type_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        
                        {{-- Day Access Info - shows which days the ticket grants access to --}}
                        <div id="day_access_info" class="mt-2" style="display: none;">
                            <div class="d-flex align-items-center flex-wrap gap-2">
                                <span class="text-muted" style="font-size: 0.875rem;">
                                    <i class="fas fa-calendar-check me-1"></i>Day Access:
                                </span>
                                <span id="day_access_badges"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Day Selection Dropdown - shown when ticket has per-day pricing --}}
                <div class="row" id="day_selection_row" style="display: none;">
                    <div class="col-md-12 mb-3">
                        <label class="form-label required-field">Select Event Day</label>
                        <select name="selected_event_day_id" class="form-select" id="selected_event_day">
                            <option value="">Select Day</option>
                        </select>
                        <small class="text-muted">Choose which day you want to attend</small>
                        @error('selected_event_day_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Number of Delegates</label>
                        <input type="number" name="delegate_count" class="form-control" 
                               value="{{ old('delegate_count', 1) }}" 
                               min="1" 
                               max="50" 
                               required 
                               id="delegate_count">
                        @error('delegate_count')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Currency</label>
                        @if(isset($isNationalityLocked) && $isNationalityLocked)
                            {{-- Hidden field to submit the value --}}
                            <input type="hidden" name="nationality" value="{{ $selectedNationality }}">
                            {{-- Display field (readonly) --}}
                            <input type="text" class="form-control" 
                                   value="{{ $selectedNationality == 'national' ? 'INR (₹)' : 'USD ($)' }}" 
                                   readonly 
                                   style="background-color: #e9ecef; cursor: not-allowed;">
                        @else
                            <select name="nationality" class="form-select" required>
                                <option value="">Select Currency</option>
                                <option value="national" {{ old('nationality', $selectedNationality ?? '') == 'national' ? 'selected' : '' }}>INR (₹)</option>
                                <option value="international" {{ old('nationality', $selectedNationality ?? '') == 'international' ? 'selected' : '' }}>USD ($)</option>
                            </select>
                        @endif
                        @error('nationality')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Organisation Information Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-building"></i>
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
                            @foreach($sectors as $sector)
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
                            @foreach($organizationTypes as $orgType)
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Country</label>
                        <select name="company_country" class="form-select" id="company_country" required>
                            <option value="">-- Select Country --</option>
                            @php
                                $countries = [
                                    'India', 'United States', 'United Kingdom', 'Canada', 'Australia', 'Germany',
                                    'France', 'Japan', 'China', 'Singapore', 'South Korea', 'Italy', 'Spain',
                                    'Netherlands', 'Sweden', 'Switzerland', 'Belgium', 'Austria', 'Norway',
                                    'Denmark', 'Finland', 'Poland', 'Portugal', 'Greece', 'Ireland', 'New Zealand',
                                    'Brazil', 'Mexico', 'Argentina', 'Chile', 'South Africa', 'Egypt', 'UAE',
                                    'Saudi Arabia', 'Israel', 'Turkey', 'Thailand', 'Malaysia', 'Indonesia',
                                    'Philippines', 'Vietnam', 'Bangladesh', 'Pakistan', 'Sri Lanka', 'Nepal',
                                    'Myanmar', 'Afghanistan', 'Iran', 'Iraq', 'Kuwait', 'Qatar', 'Oman',
                                    'Bahrain', 'Jordan', 'Lebanon', 'Other'
                                ];
                            @endphp
                            @foreach($countries as $country)
                                <option value="{{ $country }}" {{ old('company_country', 'India') == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                        @error('company_country')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">State</label>
                        <select name="company_state" class="form-select" id="company_state" required>
                            <option value="">-- Select State --</option>
                            @if(old('company_state'))
                                <option value="{{ old('company_state') }}" selected>{{ old('company_state') }}</option>
                            @endif
                        </select>
                        @error('company_state')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">City</label>
                        <input type="text" name="company_city" class="form-control" 
                               value="{{ old('company_city') }}" 
                               placeholder="Enter city" required>
                        @error('company_city')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control" 
                               value="{{ old('postal_code') }}" 
                               placeholder="Enter postal code" required>
                        @error('postal_code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Phone Number</label>
                        <input type="tel" name="phone" class="form-control" id="company_phone" 
                               value="{{ old('phone') ? preg_replace('/\s+/', '', old('phone')) : '' }}" 
                               placeholder="Enter phone number" 
                               pattern="[0-9]*"
                               inputmode="numeric"
                               required>
                        <input type="hidden" name="phone_country_code" id="company_phone_country_code" value="{{ old('phone_country_code', '+91') }}">
                        @error('phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ old('email') }}" 
                               placeholder="Enter email address" required>
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Delegates Information Section -->
            <div class="form-section" id="delegates_section" style="display: {{ old('delegate_count', 1) >= 1 ? 'block' : 'none' }};">
                <h4 class="section-title">
                    <i class="fas fa-users"></i>
                    Delegates Information
                </h4>
                <p class="text-muted mb-3">Please provide details for each delegate attending the event.</p>
                <div id="delegates_container">
                    <!-- Delegates will be dynamically added here -->
                </div>
            </div>

            <!-- Organisation Details for Raising the Invoice Section -->
            <div class="form-section">
                <h4 class="section-title">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Organisation Details for Raising the Invoice
                </h4>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label required-field">Do you require GST Invoice?</label>
                        <select name="gst_required" class="form-select" id="gst_required" required>
                            <option value="0" {{ old('gst_required') == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('gst_required') == '1' ? 'selected' : '' }}>Yes</option>
                        </select>
                        @error('gst_required')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div id="gst_fields" style="display: {{ old('gst_required') == '1' ? 'block' : 'none' }};">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">GSTIN</label>
                            <div class="input-group">
                            <input type="text" name="gstin" class="form-control" 
                                   value="{{ old('gstin') }}" 
                                   placeholder="Enter GSTIN" 
                                   id="gstin_input"
                                   maxlength="15">
                                <button type="button" class="btn btn-outline-primary" id="validateGstBtn" style="display: none;">
                                    <i class="fas fa-search me-1"></i>Validate GST
                                </button>
                            </div>
                            <small class="form-text text-muted">Enter 15-character GSTIN and click "Validate GST" to auto-fill details</small>
                            @error('gstin')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <div id="gst_loading" class="d-none mt-2">
                                <small class="text-info"><i class="fas fa-spinner fa-spin"></i> Validating GST...</small>
                            </div>
                            <div id="gst_validation_message" class="mt-2"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">GST Legal Name</label>
                            <input type="text" name="gst_legal_name" class="form-control" 
                                   value="{{ old('gst_legal_name') }}" 
                                   placeholder="Enter legal name for invoice"
                                   id="gst_legal_name_input"
                                   readonly>
                            @error('gst_legal_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">GST Address</label>
                            <textarea name="gst_address" class="form-control" rows="3" 
                                      placeholder="Enter address for invoice"
                                      id="gst_address_input"
                                      readonly>{{ old('gst_address') }}</textarea>
                            @error('gst_address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="form-label">State</label>
                            <input type="hidden" name="gst_country" value="India">
                            <select name="gst_state" class="form-select" id="gst_state">
                                <option value="">-- Select State --</option>
                                @php
                                    $indianStates = [
                                        'Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa',
                                        'Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala',
                                        'Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland',
                                        'Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura',
                                        'Uttar Pradesh','Uttarakhand','West Bengal',
                                        'Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu',
                                        'Delhi','Jammu and Kashmir','Ladakh','Lakshadweep','Puducherry'
                                    ];
                                @endphp
                                @foreach($indianStates as $state)
                                    <option value="{{ $state }}" {{ old('gst_state') == $state ? 'selected' : '' }}>
                                        {{ $state }}
                                    </option>
                                @endforeach
                            </select>
                            @error('gst_state')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                </div>
            </div>

                    <!-- Primary Contact Information - Only visible when GST is Yes -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Primary Contact Full Name</label>
                        <input type="text" name="contact_name" class="form-control" 
                               value="{{ old('contact_name') }}" 
                               placeholder="Enter full name" id="contact_name">
                        @error('contact_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Primary Contact Email Address</label>
                        <input type="email" name="contact_email" class="form-control" 
                               value="{{ old('contact_email') }}" 
                               placeholder="Enter email address" id="contact_email">
                        @error('contact_email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Primary Contact Mobile Number</label>
                        <input type="tel" name="contact_phone" class="form-control" 
                                   value="{{ old('contact_phone') ? preg_replace('/\s+/', '', old('contact_phone')) : '' }}" 
                                   placeholder="Enter mobile number" 
                                   id="contact_phone"
                                   pattern="[0-9]*"
                                   inputmode="numeric">
                        <input type="hidden" name="contact_phone_country_code" id="contact_phone_country_code" value="{{ old('contact_phone_country_code', '+91') }}">
                        @error('contact_phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- reCAPTCHA Token (hidden) -->
            @if(config('constants.RECAPTCHA_ENABLED', false))
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            @error('recaptcha')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
            @endif

            <!-- Submit Button -->
            <div class="text-center mt-4">
                <button type="submit" class="btn-submit" id="submitBtn">
                    <i class="fas fa-arrow-right me-2"></i>Continue to Preview
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- intl-tel-input -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"></script>

@if(config('constants.RECAPTCHA_ENABLED', false))
<script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}"></script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Day Selection Handler
    const ticketTypeSelect = document.getElementById('ticket_type_select');
    const lockedTicketType = document.getElementById('locked_ticket_type');
    const daySelectionRow = document.getElementById('day_selection_row');
    const selectedEventDaySelect = document.getElementById('selected_event_day');
    const dayAccessInfo = document.getElementById('day_access_info');
    const dayAccessBadges = document.getElementById('day_access_badges');
    
    // Helper function to get ticket type data (from select option or locked input)
    function getTicketTypeData() {
        if (ticketTypeSelect) {
            const selectedOption = ticketTypeSelect.options[ticketTypeSelect.selectedIndex];
            if (!selectedOption || !selectedOption.value) return null;
            return selectedOption.dataset;
        } else if (lockedTicketType) {
            return lockedTicketType.dataset;
        }
        return null;
    }
    
    function updateDayAccessInfo() {
        if (!dayAccessInfo || !dayAccessBadges) return;
        
        const data = getTicketTypeData();
        if (!data) {
            dayAccessInfo.style.display = 'none';
            dayAccessBadges.innerHTML = '';
            return;
        }
        
        const allDaysAccess = data.allDaysAccess === '1';
        const availableDaysJson = data.availableDays;
        
        try {
            if (allDaysAccess) {
                // Show "All Days" badge
                dayAccessBadges.innerHTML = '<span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 500;"><i class="fas fa-check-circle me-1"></i>All Days</span>';
                dayAccessInfo.style.display = 'block';
            } else {
                const availableDays = JSON.parse(availableDaysJson);
                if (availableDays && availableDays.length > 0) {
                    let badgesHtml = '';
                    availableDays.forEach(day => {
                        badgesHtml += `<span class="badge bg-primary me-1" style="padding: 0.35rem 0.75rem; border-radius: 6px; font-weight: 500;">${day.label}</span>`;
                    });
                    dayAccessBadges.innerHTML = badgesHtml;
                    dayAccessInfo.style.display = 'block';
                } else {
                    dayAccessInfo.style.display = 'none';
                }
            }
        } catch(e) {
            console.error('Error parsing available days:', e);
            dayAccessInfo.style.display = 'none';
        }
    }
    
    function updateDaySelection() {
        if (!daySelectionRow || !selectedEventDaySelect) return;
        
        const data = getTicketTypeData();
        if (!data) {
            daySelectionRow.style.display = 'none';
            selectedEventDaySelect.innerHTML = '<option value="">Select Day</option>';
            selectedEventDaySelect.removeAttribute('required');
            return;
        }
        
        const enableDaySelection = data.enableDaySelection === '1';
        const allDaysAccess = data.allDaysAccess === '1';
        const includeAllDaysOption = data.includeAllDaysOption === '1';
        const availableDaysJson = data.availableDays;
        const hasPerDayPricing = data.hasPerDayPricing === '1';
        
        // Show day selection only if enable_day_selection is ON
        if (enableDaySelection) {
            try {
                const availableDays = JSON.parse(availableDaysJson);
                selectedEventDaySelect.innerHTML = '<option value="">Select Day</option>';
                
                // Add "All Days" option first (if all_days_access OR include_all_days_option is enabled)
                if (allDaysAccess || includeAllDaysOption) {
                    const allDaysOption = document.createElement('option');
                    allDaysOption.value = 'all';
                    // Get date range from available days
                    if (availableDays && availableDays.length > 0) {
                        const sortedDays = [...availableDays].sort((a, b) => new Date(a.date) - new Date(b.date));
                        const startDate = sortedDays[0].date;
                        const endDate = sortedDays[sortedDays.length - 1].date;
                        
                        // Add price info for All Days
                        let priceInfo = '';
                        const nationality = document.querySelector('select[name="nationality"]')?.value || 
                                           document.querySelector('input[name="nationality"]')?.value || 'national';
                        const priceNational = data.priceNational;
                        const priceInternational = data.priceInternational;
                        if (nationality === 'international' && priceInternational) {
                            priceInfo = ' - $' + parseFloat(priceInternational).toLocaleString();
                        } else if (priceNational) {
                            priceInfo = ' - ₹' + parseFloat(priceNational).toLocaleString();
                        }
                        
                        allDaysOption.textContent = 'All Days (' + startDate + ' - ' + endDate + ')' + priceInfo;
                    } else {
                        allDaysOption.textContent = 'All Days';
                    }
                    if ('{{ old("selected_event_day_id") }}' === 'all') {
                        allDaysOption.selected = true;
                    }
                    selectedEventDaySelect.appendChild(allDaysOption);
                }
                
                // Add individual day options
                if (availableDays && availableDays.length > 0) {
                    availableDays.forEach(day => {
                        const option = document.createElement('option');
                        option.value = day.id;
                        // Show per-day price if available
                        let priceInfo = '';
                        if (hasPerDayPricing) {
                            const nationality = document.querySelector('select[name="nationality"]')?.value || 
                                               document.querySelector('input[name="nationality"]')?.value || 'national';
                            const perDayNational = data.perDayPriceNational;
                            const perDayInternational = data.perDayPriceInternational;
                            if (nationality === 'international' && perDayInternational) {
                                priceInfo = ' - $' + parseFloat(perDayInternational).toLocaleString();
                            } else if (perDayNational) {
                                priceInfo = ' - ₹' + parseFloat(perDayNational).toLocaleString();
                            }
                        }
                        option.textContent = day.label + ' (' + day.date + ')' + priceInfo;
                        // Check for old value
                        if ('{{ old("selected_event_day_id") }}' == day.id) {
                            option.selected = true;
                        }
                        selectedEventDaySelect.appendChild(option);
                    });
                }
                
                daySelectionRow.style.display = 'flex';
                selectedEventDaySelect.setAttribute('required', 'required');
            } catch(e) {
                console.error('Error parsing available days:', e);
                daySelectionRow.style.display = 'none';
                selectedEventDaySelect.removeAttribute('required');
            }
        } else {
            daySelectionRow.style.display = 'none';
            selectedEventDaySelect.innerHTML = '<option value="">Select Day</option>';
            selectedEventDaySelect.removeAttribute('required');
        }
    }
    
    // Initialize day selection and day access info on page load
    if (ticketTypeSelect) {
        ticketTypeSelect.addEventListener('change', function() {
            updateDayAccessInfo();
            updateDaySelection();
        });
        // Trigger on load in case ticket type is pre-selected
        updateDayAccessInfo();
        updateDaySelection();
    } else if (lockedTicketType) {
        // If ticket type is locked (from URL params), still initialize day selection
        updateDayAccessInfo();
        updateDaySelection();
    }

    // Delegate form generation
    const delegateCountInput = document.getElementById('delegate_count');
    const delegatesContainer = document.getElementById('delegates_container');
    const delegatesSection = document.getElementById('delegates_section');
    
    // Old values from validation errors
    const oldDelegates = @json(old('delegates', []));
    const oldDelegateCount = {{ old('delegate_count', 1) }};

    function generateDelegateForms(count) {
        delegatesContainer.innerHTML = '';
        
        for (let i = 0; i < count; i++) {
            const delegateData = oldDelegates[i] || {};
            const delegateForm = document.createElement('div');
            delegateForm.className = 'delegate-form';
            delegateForm.innerHTML = `
                <h5 class="mb-3" style="color: var(--text-primary);">Delegate ${i + 1}</h5>
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label required-field">Salutation</label>
                        <select name="delegates[${i}][salutation]" class="form-select" required>
                            <option value="">Select</option>
                            <option value="Mr" ${delegateData.salutation === 'Mr' ? 'selected' : ''}>Mr</option>
                            <option value="Mrs" ${delegateData.salutation === 'Mrs' ? 'selected' : ''}>Mrs</option>
                            <option value="Ms" ${delegateData.salutation === 'Ms' ? 'selected' : ''}>Ms</option>
                            <option value="Dr" ${delegateData.salutation === 'Dr' ? 'selected' : ''}>Dr</option>
                            <option value="Prof" ${delegateData.salutation === 'Prof' ? 'selected' : ''}>Prof.</option>
                        </select>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label required-field">First Name</label>
                        <input type="text" name="delegates[${i}][first_name]" class="form-control" 
                               value="${delegateData.first_name || ''}" 
                               placeholder="Enter first name" required>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label class="form-label required-field">Last Name</label>
                        <input type="text" name="delegates[${i}][last_name]" class="form-control" 
                               value="${delegateData.last_name || ''}" 
                               placeholder="Enter last name" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Email</label>
                        <input type="email" name="delegates[${i}][email]" class="form-control" 
                               value="${delegateData.email || ''}" 
                               placeholder="Enter email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label required-field">Mobile Number</label>
                        <input type="tel" name="delegates[${i}][phone]" class="form-control delegate-phone" 
                               id="delegate_phone_${i}"
                               value="${(() => {
                                   let phone = delegateData.phone || '';
                                   if (phone && phone.includes('-')) {
                                       const parts = phone.split('-');
                                       if (parts.length === 2 && parts[0].startsWith('+')) {
                                           return parts[1].replace(/\s/g, '');
                                       }
                                   } else if (phone && phone.startsWith('+')) {
                                       const match = phone.match(/^(\+\d{1,3})(.+)$/);
                                       if (match) {
                                           return match[2].replace(/\s/g, '');
                                       }
                                   }
                                   return phone.replace(/\s/g, '');
                               })()}" 
                               placeholder="Enter mobile number" 
                               pattern="[0-9]*"
                               inputmode="numeric"
                               required>
                        <input type="hidden" name="delegates[${i}][phone_country_code]" id="delegate_phone_country_code_${i}" value="${(() => {
                            let phone = delegateData.phone || '';
                            let countryCode = delegateData.phone_country_code || '+91';
                            if (phone && phone.includes('-')) {
                                const parts = phone.split('-');
                                if (parts.length === 2 && parts[0].startsWith('+')) {
                                    return parts[0];
                                }
                            } else if (phone && phone.startsWith('+')) {
                                const match = phone.match(/^(\+\d{1,3})(.+)$/);
                                if (match) {
                                    return match[1];
                                }
                            }
                            return countryCode;
                        })()}"
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label required-field">Job Title</label>
                        <input type="text" name="delegates[${i}][job_title]" class="form-control" 
                               value="${delegateData.job_title || ''}" 
                               placeholder="Enter job title" required>
                    </div>
                </div>
            `;
            delegatesContainer.appendChild(delegateForm);
            
            // Initialize intl-tel-input for delegate phone after a short delay to ensure DOM is ready
            setTimeout(() => {
                const delegatePhoneInput = document.getElementById(`delegate_phone_${i}`);
                const delegatePhoneCountryCode = document.getElementById(`delegate_phone_country_code_${i}`);
                if (delegatePhoneInput && typeof window.intlTelInput !== 'undefined') {
                    // Apply restriction BEFORE initializing intl-tel-input
                    restrictToNumbers(delegatePhoneInput);
                    
                    delegatePhoneInput.placeholder = '';
                    const itiDelegate = window.intlTelInput(delegatePhoneInput, {
                        initialCountry: 'in',
                        preferredCountries: ['in', 'us', 'gb'],
                        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                        separateDialCode: true,
                        nationalMode: false,
                        autoPlaceholder: 'off',
                    });
                    
                    // Store the instance for later use
                    delegatePhoneInstances.set(delegatePhoneInput, itiDelegate);
                    
                    // Re-apply restriction after intl-tel-input initialization to ensure it still works
                    restrictToNumbers(delegatePhoneInput);
                    
                    // Set old value if exists - handle +CC-NUMBER format
                    if (delegateData.phone) {
                        let phoneValue = delegateData.phone.toString();
                        let countryCode = '+91';
                        let phoneNumber = '';
                        
                        // Check if in format +CC-NUMBER
                        if (phoneValue.includes('-')) {
                            const parts = phoneValue.split('-');
                            if (parts.length === 2 && parts[0].startsWith('+')) {
                                countryCode = parts[0];
                                phoneNumber = parts[1].replace(/\s/g, '');
                            }
                        } else if (phoneValue.startsWith('+')) {
                            // Extract country code and number
                            const match = phoneValue.match(/^(\+\d{1,3})(.+)$/);
                            if (match) {
                                countryCode = match[1];
                                phoneNumber = match[2].replace(/\s/g, '');
                            }
                        } else {
                            phoneNumber = phoneValue.replace(/\s/g, '');
                        }
                        
                        // Set country code in hidden field
                        if (delegatePhoneCountryCode) {
                            delegatePhoneCountryCode.value = countryCode;
                        }
                        
                        // Determine country from country code
                        let initialCountry = 'in';
                        if (countryCode) {
                            const countryCodeNum = countryCode.replace('+', '');
                            const countryMap = {
                                '91': 'in', '1': 'us', '44': 'gb', '61': 'au', '86': 'cn',
                                '33': 'fr', '49': 'de', '81': 'jp', '82': 'kr', '65': 'sg'
                            };
                            if (countryMap[countryCodeNum]) {
                                initialCountry = countryMap[countryCodeNum];
                            }
                        }
                        
                        // Set country in intl-tel-input
                        try {
                            itiDelegate.setCountry(initialCountry);
                        } catch(e) {
                            // Fallback
                        }
                        
                        // Set phone number (without country code)
                        if (phoneNumber) {
                            delegatePhoneInput.value = phoneNumber;
                        }
                    }
                    
                    delegatePhoneInput.addEventListener('countrychange', function () {
                        const countryData = itiDelegate.getSelectedCountryData();
                        delegatePhoneCountryCode.value = '+' + countryData.dialCode;
                    });
                    
                    const initialCountryData = itiDelegate.getSelectedCountryData();
                    if (delegateData.phone_country_code) {
                        delegatePhoneCountryCode.value = delegateData.phone_country_code;
                    } else {
                        delegatePhoneCountryCode.value = '+' + initialCountryData.dialCode;
                    }
                }
            }, 100);
        }
    }

    // Initialize delegate forms
    generateDelegateForms(oldDelegateCount);

    // Update delegate forms when count changes
    delegateCountInput.addEventListener('change', function() {
        const count = parseInt(this.value) || 1;
        generateDelegateForms(count);
        delegatesSection.style.display = 'block';
    });
    
    // Load states for company country
    const companyCountrySelect = document.getElementById('company_country');
    const companyStateSelect = document.getElementById('company_state');
    
    function loadStatesForCountry(countryName) {
        if (!countryName || countryName === '' || !companyStateSelect) {
            if (companyStateSelect) {
                companyStateSelect.innerHTML = '<option value="">-- Select State --</option>';
            }
            return;
        }
        
        companyStateSelect.innerHTML = '<option value="">Loading states...</option>';
        companyStateSelect.disabled = true;
        
        const countryParam = encodeURIComponent(countryName);
        fetch(`{{ url('/api/states') }}/${countryParam}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to fetch states');
            }
            return response.json();
        })
        .then(data => {
            companyStateSelect.innerHTML = '<option value="">-- Select State --</option>';
            if (data && Array.isArray(data) && data.length > 0) {
                data.forEach(state => {
                    const option = document.createElement('option');
                    const stateName = state.name || state.state_name || state;
                    option.value = stateName;
                    option.textContent = stateName;
                    if ('{{ old("company_state") }}' === stateName) {
                        option.selected = true;
                    }
                    companyStateSelect.appendChild(option);
                });
            }
            companyStateSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading states:', error);
            companyStateSelect.innerHTML = '<option value="">-- Select State --</option>';
            companyStateSelect.disabled = false;
        });
    }
    
    // Load states when country changes
    if (companyCountrySelect && companyStateSelect) {
        companyCountrySelect.addEventListener('change', function() {
            loadStatesForCountry(this.value);
        });
        
        // Load states on page load if country is already set
        if (companyCountrySelect.value) {
            loadStatesForCountry(companyCountrySelect.value);
        }
    }
    
    // GST State dropdown - Only Indian states are shown (country is fixed to India via hidden input)
    // No JavaScript needed since states are pre-populated in HTML
    
    // Store delegate phone instances
    const delegatePhoneInstances = new Map();
    let itiCompany = null;
    let itiContact = null;
    
    // Add numeric-only validation for phone inputs
    function restrictToNumbers(input) {
        // Mark input as restricted to avoid duplicate handlers
        if (input.dataset.restricted === 'true') {
            return;
        }
        input.dataset.restricted = 'true';
        
        // Use beforeinput event (modern browsers) to prevent non-numeric input
        input.addEventListener('beforeinput', function(e) {
            // Allow deletion operations
            if (e.inputType === 'deleteContentBackward' || 
                e.inputType === 'deleteContentForward' || 
                e.inputType === 'deleteByCut') {
                return;
            }
            // For any text insertion, only allow numbers
            if (e.data && !/^\d+$/.test(e.data)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }, { capture: true, passive: false });
        
        // Use input event to filter out non-numeric characters and spaces in real-time (most reliable)
        input.addEventListener('input', function(e) {
            let value = e.target.value;
            // Get the current cursor position
            const cursorPos = e.target.selectionStart || 0;
            
            // Remove all non-numeric characters including spaces (keep only digits)
            const numbersOnly = value.replace(/[^\d]/g, '').replace(/\s/g, '');
            
            // If value changed (had non-numeric chars or spaces), update it immediately
            if (value !== numbersOnly) {
                e.target.value = numbersOnly;
                // Restore cursor position (adjusted for removed characters)
                const removedChars = value.length - numbersOnly.length;
                const newCursorPos = Math.max(0, cursorPos - removedChars);
                setTimeout(() => {
                    e.target.setSelectionRange(newCursorPos, newCursorPos);
                }, 0);
            }
        }, { capture: true });
        
        // Prevent non-numeric keypress (fallback for older browsers)
        input.addEventListener('keypress', function(e) {
            // Allow: backspace, delete, tab, escape, enter
            if ([46, 8, 9, 27, 13].indexOf(e.keyCode) !== -1 ||
                // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                (e.keyCode === 65 && e.ctrlKey === true) ||
                (e.keyCode === 67 && e.ctrlKey === true) ||
                (e.keyCode === 86 && e.ctrlKey === true) ||
                (e.keyCode === 88 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
                (e.keyCode >= 35 && e.keyCode <= 39)) {
                return;
            }
            // Only allow numeric keys (0-9) - both regular and numpad
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }, { capture: true });
        
        // Clean paste events - extract only numbers
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const numbersOnly = paste.replace(/[^\d]/g, '');
            if (numbersOnly) {
                // Insert at cursor position
                const start = this.selectionStart || 0;
                const end = this.selectionEnd || 0;
                const currentValue = this.value;
                const newValue = currentValue.substring(0, start) + numbersOnly + currentValue.substring(end);
                this.value = newValue;
                setTimeout(() => {
                    this.setSelectionRange(start + numbersOnly.length, start + numbersOnly.length);
                }, 0);
            }
        }, { capture: true });
        
        // Also clean on blur as a safety measure (remove spaces and non-numeric)
        input.addEventListener('blur', function(e) {
            let value = e.target.value;
            // Remove spaces and non-numeric characters
            const numbersOnly = value.replace(/[^\d]/g, '').replace(/\s/g, '');
            if (value !== numbersOnly && numbersOnly) {
                // If intl-tel-input is initialized, use its setNumber method
                const itiInstance = delegatePhoneInstances.get(e.target) || 
                                 (e.target.id === 'company_phone' ? itiCompany : null) ||
                                 (e.target.id === 'contact_phone' ? itiContact : null);
                if (itiInstance) {
                    try {
                        const countryCode = itiInstance.getSelectedCountryData().dialCode;
                        itiInstance.setNumber('+' + countryCode + numbersOnly);
                    } catch(err) {
                        e.target.value = numbersOnly;
                    }
                } else {
                    e.target.value = numbersOnly;
                }
            }
        });
    }
    
    // Function to clean phone numbers by removing spaces and non-numeric characters
    function cleanPhoneNumber(input) {
        if (!input) return;
        let value = input.value || '';
        // Remove all spaces and non-numeric characters
        const cleaned = value.replace(/\s/g, '').replace(/[^\d]/g, '');
        if (value !== cleaned && cleaned) {
            // If intl-tel-input is initialized, use its setNumber method
            const itiInstance = delegatePhoneInstances.get(input) || 
                             (input.id === 'company_phone' ? itiCompany : null) ||
                             (input.id === 'contact_phone' ? itiContact : null);
            if (itiInstance) {
                try {
                    const countryCode = itiInstance.getSelectedCountryData().dialCode;
                    itiInstance.setNumber('+' + countryCode + cleaned);
                } catch(err) {
                    input.value = cleaned;
                }
            } else {
                input.value = cleaned;
            }
        } else if (value !== cleaned) {
            input.value = cleaned;
        }
    }
    
    // Wait for intlTelInput to be available
    function initializePhoneInputs() {
        if (typeof window.intlTelInput === 'undefined') {
            // Retry after a short delay if library not loaded yet
            setTimeout(initializePhoneInputs, 100);
            return;
    }
    
    // Initialize intl-tel-input for company phone
    const companyPhoneInput = document.getElementById('company_phone');
    const companyPhoneCountryCode = document.getElementById('company_phone_country_code');
    
        if (companyPhoneInput) {
            // Check if we have old value in format +CC-NUMBER, split it
            let oldPhoneValue = companyPhoneInput.value || '';
            let countryCode = '+91'; // default
            let phoneNumber = '';
            
            if (oldPhoneValue && oldPhoneValue.includes('-')) {
                // Split +91-1234567890 into +91 and 1234567890
                const parts = oldPhoneValue.split('-');
                if (parts.length === 2 && parts[0].startsWith('+')) {
                    countryCode = parts[0];
                    phoneNumber = parts[1].replace(/\s/g, '');
                }
            } else if (oldPhoneValue) {
                // If old value exists but not in expected format, try to extract
                phoneNumber = oldPhoneValue.replace(/\s/g, '');
                // Check if it starts with country code
                if (oldPhoneValue.startsWith('+')) {
                    const match = oldPhoneValue.match(/^(\+\d{1,3})(.+)$/);
                    if (match) {
                        countryCode = match[1];
                        phoneNumber = match[2].replace(/\s/g, '');
                    }
                }
            }
            
            // Set the country code in hidden field
            if (companyPhoneCountryCode) {
                companyPhoneCountryCode.value = countryCode;
            }
            
            // Apply restriction BEFORE initializing intl-tel-input
            restrictToNumbers(companyPhoneInput);
            
        companyPhoneInput.placeholder = '';
            
            // Determine initial country from country code
            let initialCountry = 'in';
            if (countryCode) {
                // Try to find country by dial code
                const countryCodeNum = countryCode.replace('+', '');
                const countryMap = {
                    '91': 'in', '1': 'us', '44': 'gb', '61': 'au', '86': 'cn',
                    '33': 'fr', '49': 'de', '81': 'jp', '82': 'kr', '65': 'sg'
                };
                if (countryMap[countryCodeNum]) {
                    initialCountry = countryMap[countryCodeNum];
                }
            }
            
            itiCompany = window.intlTelInput(companyPhoneInput, {
                initialCountry: initialCountry,
            preferredCountries: ['in', 'us', 'gb'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            separateDialCode: true,
            nationalMode: false,
            autoPlaceholder: 'off',
        });
            
            // Set the country code if we have one
            if (countryCode && countryCode !== '+91') {
                try {
                    itiCompany.setCountry(initialCountry);
                } catch(e) {
                    // Fallback to default
                }
            }
            
            // Set the phone number (without country code)
            if (phoneNumber) {
                companyPhoneInput.value = phoneNumber;
            }
            
            // Re-apply restriction after intl-tel-input initialization
            restrictToNumbers(companyPhoneInput);
        
        companyPhoneInput.addEventListener('countrychange', function () {
            const countryData = itiCompany.getSelectedCountryData();
                if (companyPhoneCountryCode) {
            companyPhoneCountryCode.value = '+' + countryData.dialCode;
                }
        });
        
            // Set initial country code in hidden field
        const initialCountryData = itiCompany.getSelectedCountryData();
            if (companyPhoneCountryCode) {
        companyPhoneCountryCode.value = '+' + initialCountryData.dialCode;
            }
    }
    
    // Initialize intl-tel-input for contact phone (primary contact)
    const contactPhoneInput = document.getElementById('contact_phone');
    const contactPhoneCountryCode = document.getElementById('contact_phone_country_code');
    
        if (contactPhoneInput) {
            // Check if we have old value in format +CC-NUMBER, split it
            let oldPhoneValue = contactPhoneInput.value || '';
            let countryCode = '+91'; // default
            let phoneNumber = '';
            
            if (oldPhoneValue && oldPhoneValue.includes('-')) {
                // Split +91-1234567890 into +91 and 1234567890
                const parts = oldPhoneValue.split('-');
                if (parts.length === 2 && parts[0].startsWith('+')) {
                    countryCode = parts[0];
                    phoneNumber = parts[1].replace(/\s/g, '');
                }
            } else if (oldPhoneValue) {
                // If old value exists but not in expected format, try to extract
                phoneNumber = oldPhoneValue.replace(/\s/g, '');
                // Check if it starts with country code
                if (oldPhoneValue.startsWith('+')) {
                    const match = oldPhoneValue.match(/^(\+\d{1,3})(.+)$/);
                    if (match) {
                        countryCode = match[1];
                        phoneNumber = match[2].replace(/\s/g, '');
                    }
                }
            }
            
            // Set the country code in hidden field
            if (contactPhoneCountryCode) {
                contactPhoneCountryCode.value = countryCode;
            }
            
            // Apply restriction BEFORE initializing intl-tel-input
            restrictToNumbers(contactPhoneInput);
            
        contactPhoneInput.placeholder = '';
            
            // Determine initial country from country code
            let initialCountry = 'in';
            if (countryCode) {
                // Try to find country by dial code
                const countryCodeNum = countryCode.replace('+', '');
                const countryMap = {
                    '91': 'in', '1': 'us', '44': 'gb', '61': 'au', '86': 'cn',
                    '33': 'fr', '49': 'de', '81': 'jp', '82': 'kr', '65': 'sg'
                };
                if (countryMap[countryCodeNum]) {
                    initialCountry = countryMap[countryCodeNum];
                }
            }
            
            itiContact = window.intlTelInput(contactPhoneInput, {
                initialCountry: initialCountry,
            preferredCountries: ['in', 'us', 'gb'],
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
            separateDialCode: true,
            nationalMode: false,
            autoPlaceholder: 'off',
        });
            
            // Set the country code if we have one
            if (countryCode && countryCode !== '+91') {
                try {
                    itiContact.setCountry(initialCountry);
                } catch(e) {
                    // Fallback to default
                }
            }
            
            // Set the phone number (without country code)
            if (phoneNumber) {
                contactPhoneInput.value = phoneNumber;
            }
            
            // Re-apply restriction after intl-tel-input initialization
            restrictToNumbers(contactPhoneInput);
        
        contactPhoneInput.addEventListener('countrychange', function () {
            const countryData = itiContact.getSelectedCountryData();
                if (contactPhoneCountryCode) {
            contactPhoneCountryCode.value = '+' + countryData.dialCode;
                }
        });
        
            // Set initial country code in hidden field
        const initialCountryData = itiContact.getSelectedCountryData();
            if (contactPhoneCountryCode) {
        contactPhoneCountryCode.value = '+' + initialCountryData.dialCode;
            }
        }
    }
    
    // Initialize phone inputs
    initializePhoneInputs();
    
    // Clean phone numbers on page load (remove spaces from old values)
    document.addEventListener('DOMContentLoaded', function() {
        // Clean phone numbers immediately on page load (especially after validation errors)
        function cleanPhoneOnLoad() {
            // Clean company phone
            const companyPhone = document.getElementById('company_phone');
            if (companyPhone && companyPhone.value) {
                companyPhone.value = companyPhone.value.replace(/\s/g, '');
            }
            
            // Clean contact phone
            const contactPhone = document.getElementById('contact_phone');
            if (contactPhone && contactPhone.value) {
                contactPhone.value = contactPhone.value.replace(/\s/g, '');
            }
            
            // Clean all delegate phones
            document.querySelectorAll('.delegate-phone').forEach(function(phoneInput) {
                if (phoneInput && phoneInput.value) {
                    phoneInput.value = phoneInput.value.replace(/\s/g, '');
                }
            });
        }
        
        // Clean immediately
        cleanPhoneOnLoad();
        
        // Wait a bit for intl-tel-input to initialize
        setTimeout(function() {
            const phoneInputs = document.querySelectorAll('input[type="tel"]');
            phoneInputs.forEach(function(input) {
                // Clean existing values (remove spaces)
                cleanPhoneNumber(input);
            });
        }, 1000);
    });
    
    // Also clean when intl-tel-input is initialized
    setTimeout(function() {
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(function(input) {
            // Clean existing values
            cleanPhoneNumber(input);
            
            // Only apply restriction if not already restricted
            if (!input.hasAttribute('data-restricted')) {
                restrictToNumbers(input);
                input.setAttribute('data-restricted', 'true');
            }
        });
    }, 500);
    
    // Clean phone numbers when form is shown (after coming back from preview)
    const form = document.getElementById('ticketRegistrationForm');
    if (form) {
        // Use MutationObserver to detect when form becomes visible
        const observer = new MutationObserver(function(mutations) {
            const phoneInputs = document.querySelectorAll('input[type="tel"]');
            phoneInputs.forEach(function(input) {
                cleanPhoneNumber(input);
            });
        });
        
        // Observe form visibility
        observer.observe(form, { attributes: true, attributeFilter: ['style', 'class'] });
        
        // Also clean on focus (when user clicks on phone field)
        document.addEventListener('focusin', function(e) {
            if (e.target && e.target.type === 'tel') {
                cleanPhoneNumber(e.target);
            }
        }, true);
    }

    // GST toggle
    const gstRequired = document.getElementById('gst_required');
    const gstFields = document.getElementById('gst_fields');
    const contactName = document.getElementById('contact_name');
    const contactEmail = document.getElementById('contact_email');
    const contactPhone = document.getElementById('contact_phone');

    const validateGstBtn = document.getElementById('validateGstBtn');
    const gstLoading = document.getElementById('gst_loading');
    
    // Update ticket type prices when nationality changes
    const nationalitySelect = document.getElementById('nationality') || document.querySelector('select[name="nationality"]');
    const ticketTypeSelectForPrice = document.getElementById('ticket_type_select');
    
    if (nationalitySelect && ticketTypeSelectForPrice) {
        nationalitySelect.addEventListener('change', function() {
            const nationality = this.value;
            const isInternational = nationality === 'international';
            const currency = isInternational ? '$' : '₹';
            
            // Update all ticket type options
            ticketTypeSelectForPrice.querySelectorAll('option').forEach(function(option) {
                if (option.value && option.dataset.priceNational) {
                    const price = isInternational 
                        ? parseFloat(option.dataset.priceInternational || 0)
                        : parseFloat(option.dataset.priceNational || 0);
                    const priceFormat = isInternational 
                        ? number_format(price, 2) 
                        : number_format(price, 0);
                    
                    // Extract ticket name (before the dash)
                    const ticketName = option.textContent.split(' - ')[0];
                    option.textContent = ticketName + ' - ' + currency + priceFormat;
                }
            });
        });
    }
    
    // Helper function for number formatting
    function number_format(number, decimals) {
        const factor = Math.pow(10, decimals);
        return (Math.round(number * factor) / factor).toFixed(decimals);
    }

    gstRequired.addEventListener('change', function() {
        if (this.value === '1') {
            gstFields.style.display = 'block';
            if (contactName) contactName.required = true;
            if (contactEmail) contactEmail.required = true;
            if (contactPhone) contactPhone.required = true;
            if (validateGstBtn) validateGstBtn.style.display = 'inline-block';
        } else {
            gstFields.style.display = 'none';
            if (contactName) contactName.required = false;
            if (contactEmail) contactEmail.required = false;
            if (contactPhone) contactPhone.required = false;
            if (validateGstBtn) validateGstBtn.style.display = 'none';
        }
    });
    
    // Initialize validate button visibility
    if (gstRequired.value === '1' && validateGstBtn) {
        validateGstBtn.style.display = 'inline-block';
    }

    // GST validation via button
    const gstinInput = document.getElementById('gstin_input');
    const gstValidationMessage = document.getElementById('gst_validation_message');

    if (validateGstBtn && gstinInput) {
        validateGstBtn.addEventListener('click', function() {
            const gstin = gstinInput.value.trim().toUpperCase();
            
            // Validate format
            if (gstin.length !== 15) {
                gstValidationMessage.innerHTML = '<div class="alert alert-warning mt-2"><i class="fas fa-exclamation-triangle"></i> GSTIN must be 15 characters</div>';
                return;
            }
            
            // Validate pattern
            const gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
            if (!gstPattern.test(gstin)) {
                gstValidationMessage.innerHTML = '<div class="alert alert-warning mt-2"><i class="fas fa-exclamation-triangle"></i> Invalid GSTIN format</div>';
                return;
            }
            
            // Show loading
            validateGstBtn.disabled = true;
            gstLoading.classList.remove('d-none');
            gstValidationMessage.innerHTML = '';
            
            // Make API call
                    fetch('{{ route("tickets.validate-gst") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ gstin: gstin })
                    })
            .then(response => {
                const status = response.status;
                return response.json().then(data => ({ status, data }));
            })
            .then(({ status, data }) => {
                gstLoading.classList.add('d-none');
                validateGstBtn.disabled = false;
                
                if (data.success) {
                    // Auto-fill form fields and make them read-only
                    const legalNameInput = document.getElementById('gst_legal_name_input');
                    const addressInput = document.getElementById('gst_address_input');
                    const stateSelect = document.getElementById('gst_state');
                    
                    if (data.gst.company_name && legalNameInput) {
                        legalNameInput.value = data.gst.company_name;
                        legalNameInput.setAttribute('readonly', 'readonly');
                        legalNameInput.style.backgroundColor = '#e9ecef';
                            }
                    if (data.gst.billing_address && addressInput) {
                        addressInput.value = data.gst.billing_address;
                        addressInput.setAttribute('readonly', 'readonly');
                        addressInput.style.backgroundColor = '#e9ecef';
                    }
                    if (data.gst.state_name && stateSelect) {
                        // Find and select the state
                        const stateOption = Array.from(stateSelect.options).find(opt => opt.text === data.gst.state_name || opt.value === data.gst.state_name);
                        if (stateOption) {
                            stateSelect.value = stateOption.value;
                            stateSelect.style.backgroundColor = '#e9ecef';
                            stateSelect.disabled = true;
                            stateSelect.dataset.apiFetched = 'true';
                        }
                    }
                    
                    gstValidationMessage.innerHTML = '<div class="alert alert-success mt-2"><i class="fas fa-check-circle"></i> GST validated successfully. Fields are locked.</div>';
                } else if (status === 429 || data.limit_exceeded) {
                    // Rate limit exceeded
                    gstValidationMessage.innerHTML = '<div class="alert alert-warning mt-2"><i class="fas fa-exclamation-triangle"></i> ' + data.message + '</div>';
                        } else {
                    // Error or not found
                    gstValidationMessage.innerHTML = '<div class="alert alert-info mt-2"><i class="fas fa-info-circle"></i> ' + (data.message || 'GST not found. Please fill details manually.') + '</div>';
                        }
                    })
                    .catch(error => {
                gstLoading.classList.add('d-none');
                validateGstBtn.disabled = false;
                        console.error('GST validation error:', error);
                gstValidationMessage.innerHTML = '<div class="alert alert-danger mt-2"><i class="fas fa-times-circle"></i> Error validating GST. Please fill details manually.</div>';
            });
        });
    }

    // Form submission with reCAPTCHA
    const registrationForm = document.getElementById('registrationForm');
    const submitBtn = document.getElementById('submitBtn');

    registrationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // STEP 1: Remove spaces from all phone numbers FIRST (before validation)
        function removeSpacesFromPhone(input) {
            if (input && input.value) {
                // Remove all spaces
                input.value = input.value.replace(/\s/g, '');
            }
        }
        
        // Function to clean and format phone number with intl-tel-input
        function cleanAndFormatPhone(input, itiInstance) {
            if (!input || !input.value) return;
            
            // First, remove ALL spaces from the input value
            let phoneValue = input.value.replace(/\s/g, '').trim();
            
            if (!phoneValue) {
                input.value = '';
                return;
            }
            
            // If we have intl-tel-input instance, try to format it
            if (itiInstance) {
                try {
                    // Get the country code
                    const countryData = itiInstance.getSelectedCountryData();
                    const countryCode = '+' + countryData.dialCode;
                    
                    // If phone value doesn't start with +, handle it
                    if (!phoneValue.startsWith('+')) {
                        // Check if country code digits are already at the start
                        const countryCodeDigits = countryCode.replace('+', '');
                        if (phoneValue.startsWith(countryCodeDigits)) {
                            phoneValue = '+' + phoneValue;
                        } else {
                            // Just extract digits (the number part)
                            phoneValue = phoneValue.replace(/[^\d]/g, '');
                        }
                    } else {
                        // Already has +, ensure no spaces
                        phoneValue = phoneValue.replace(/\s/g, '');
                    }
                    
                    // Try to set the number (this will format it properly)
                    itiInstance.setNumber(phoneValue);
                    
                    // Get the formatted number back
                    const formattedNumber = itiInstance.getNumber();
                    if (formattedNumber) {
                        // Remove spaces from formatted number
                        phoneValue = formattedNumber.replace(/\s/g, '');
                    }
                } catch(err) {
                    // If setNumber fails, just use the cleaned value
                    phoneValue = phoneValue.replace(/[^\d+]/g, '');
                    if (!phoneValue.startsWith('+') && itiInstance) {
                        try {
                            const countryData = itiInstance.getSelectedCountryData();
                            phoneValue = '+' + countryData.dialCode + phoneValue;
                        } catch(e) {
                            // Fallback
                        }
                    }
                }
            } else {
                // No intl-tel-input instance, just remove spaces and keep digits and +
                phoneValue = phoneValue.replace(/[^\d+]/g, '');
            }
            
            // Final cleanup: ensure no spaces in the final value
            input.value = phoneValue.replace(/\s/g, '');
        }
        
        // STEP 1.5: Clean phone numbers and prepare for merging (but don't merge yet - keep input clean for validation)
        // Function to get merged phone number (without modifying the input)
        function getMergedPhoneNumber(phoneInput, countryCodeInput) {
            if (!phoneInput || !countryCodeInput) return '';
            
            // Get country code from hidden field or intl-tel-input
            let countryCode = countryCodeInput.value || '+91';
            if (!countryCode.startsWith('+')) {
                countryCode = '+' + countryCode;
            }
            
            // Get phone number (remove all spaces and non-digits)
            let phoneNumber = phoneInput.value || '';
            phoneNumber = phoneNumber.replace(/\s/g, '').replace(/[^\d]/g, '');
            
            // If we have intl-tel-input, get the number without country code
            const inputId = phoneInput.id;
            let itiInstance = null;
            if (inputId === 'company_phone' && itiCompany) {
                itiInstance = itiCompany;
            } else if (inputId === 'contact_phone' && itiContact) {
                itiInstance = itiContact;
            } else {
                // Try to get from delegate instances
                itiInstance = delegatePhoneInstances.get(phoneInput);
            }
            
            if (itiInstance) {
                try {
                    // Get the full number from intl-tel-input
                    const fullNumber = itiInstance.getNumber();
                    if (fullNumber) {
                        // Extract country code and phone number
                        const countryData = itiInstance.getSelectedCountryData();
                        const dialCode = '+' + countryData.dialCode;
                        countryCode = dialCode;
                        
                        // Get national number (without country code)
                        try {
                            const nationalNumber = itiInstance.getNumber(itiInstance.getSelectedCountryData().iso2);
                            if (nationalNumber) {
                                phoneNumber = nationalNumber.replace(/\s/g, '').replace(/[^\d]/g, '');
                            } else {
                                // Fallback: remove country code from full number
                                phoneNumber = fullNumber.replace(dialCode, '').replace(/\s/g, '').replace(/[^\d]/g, '');
                            }
                        } catch(e) {
                            // Fallback: remove country code from full number
                            phoneNumber = fullNumber.replace(dialCode, '').replace(/\s/g, '').replace(/[^\d]/g, '');
                        }
                    }
                } catch(err) {
                    // Fallback to using input value
                    phoneNumber = phoneInput.value.replace(/\s/g, '').replace(/[^\d]/g, '');
                }
            }
            
            // Return merged format: +CC-NUMBER
            if (phoneNumber) {
                return countryCode + '-' + phoneNumber;
            } else if (countryCode) {
                return countryCode;
            }
            return '';
        }
        
        // Clean all phone numbers FIRST (remove spaces, keep only digits)
        // Company phone
        const companyPhoneInput = document.getElementById('company_phone');
        const companyPhoneCountryCodeInput = document.getElementById('company_phone_country_code');
        if (companyPhoneInput) {
            removeSpacesFromPhone(companyPhoneInput);
            // Keep only digits in the visible input (for validation)
            let phoneValue = companyPhoneInput.value.replace(/\s/g, '').replace(/[^\d]/g, '');
            companyPhoneInput.value = phoneValue;
        }
        
        // Contact phone
        const contactPhoneInput = document.getElementById('contact_phone');
        const contactPhoneCountryCodeInput = document.getElementById('contact_phone_country_code');
        if (contactPhoneInput) {
            removeSpacesFromPhone(contactPhoneInput);
            // Keep only digits in the visible input (for validation)
            let phoneValue = contactPhoneInput.value.replace(/\s/g, '').replace(/[^\d]/g, '');
            contactPhoneInput.value = phoneValue;
        }
        
        // Delegate phones - use stored instances
        document.querySelectorAll('.delegate-phone').forEach(function(phoneInput) {
            removeSpacesFromPhone(phoneInput);
            // Keep only digits in the visible input (for validation)
            let phoneValue = phoneInput.value.replace(/\s/g, '').replace(/[^\d]/g, '');
            phoneInput.value = phoneValue;
        });
        
        // STEP 2: Validate the form after cleaning phone numbers
        // Check HTML5 validation
        if (!registrationForm.checkValidity()) {
            // Find first invalid field and focus on it
            const firstInvalid = registrationForm.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.focus();
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                // Trigger native validation message
                firstInvalid.reportValidity();
            } else {
                // Fallback: report validity on form
                registrationForm.reportValidity();
            }
            return false; // Stop submission
        }
        
        // STEP 3: If validation passes, merge phone numbers with country code BEFORE submission
        // Now merge phone numbers (after validation passes)
        if (companyPhoneInput && companyPhoneCountryCodeInput) {
            const mergedPhone = getMergedPhoneNumber(companyPhoneInput, companyPhoneCountryCodeInput);
            if (mergedPhone) {
                companyPhoneInput.value = mergedPhone;
            }
        }
        
        if (contactPhoneInput && contactPhoneCountryCodeInput) {
            const mergedPhone = getMergedPhoneNumber(contactPhoneInput, contactPhoneCountryCodeInput);
            if (mergedPhone) {
                contactPhoneInput.value = mergedPhone;
            }
        }
        
        // Delegate phones
        document.querySelectorAll('.delegate-phone').forEach(function(phoneInput) {
            const phoneName = phoneInput.name;
            const countryCodeName = phoneName.replace('[phone]', '[phone_country_code]');
            const countryCodeInput = document.querySelector(`input[name="${countryCodeName}"]`);
            if (countryCodeInput) {
                const mergedPhone = getMergedPhoneNumber(phoneInput, countryCodeInput);
                if (mergedPhone) {
                    phoneInput.value = mergedPhone;
                }
            }
        });
        
        // STEP 4: Proceed with submission
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

        @if(config('constants.RECAPTCHA_ENABLED', false))
        grecaptcha.enterprise.ready(function() {
            grecaptcha.enterprise.execute('{{ config("services.recaptcha.site_key") }}', {action: 'submit'})
                .then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                    registrationForm.submit();
                })
                .catch(function(error) {
                    console.error('reCAPTCHA error:', error);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-arrow-right me-2"></i>Continue';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Please try again. If the problem persists, please refresh the page.',
                        confirmButtonColor: 'var(--primary-color)'
                    });
                });
        });
        @else
        registrationForm.submit();
        @endif
    });

    // Display validation errors with SweetAlert
    @if($errors->any())
        // Clean phone numbers when validation errors exist
        function cleanAllPhonesOnError() {
            // Clean company phone
            const companyPhone = document.getElementById('company_phone');
            if (companyPhone && companyPhone.value) {
                companyPhone.value = companyPhone.value.replace(/\s/g, '');
            }
            
            // Clean contact phone
            const contactPhone = document.getElementById('contact_phone');
            if (contactPhone && contactPhone.value) {
                contactPhone.value = contactPhone.value.replace(/\s/g, '');
            }
            
            // Clean all delegate phones
            document.querySelectorAll('.delegate-phone').forEach(function(phoneInput) {
                if (phoneInput && phoneInput.value) {
                    phoneInput.value = phoneInput.value.replace(/\s/g, '');
                }
            });
        }
        
        // Clean phones immediately when errors are present
        cleanAllPhonesOnError();
        
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: '<ul style="text-align: left;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            confirmButtonColor: 'var(--primary-color)'
        }).then(() => {
            // Clean phones again after alert is closed
            cleanAllPhonesOnError();
            
            // Scroll to first error
            const firstError = document.querySelector('.is-invalid, .text-danger');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    @endif
}); // End of DOMContentLoaded
</script>
@endpush
