@extends('layouts.poster-registration')

@section('title', 'Poster Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@push('head-links')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css">
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('asset/css/custom.css') }}">
<style>
    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 2rem;
        border: 1px solid #e0e0e0;
    }
    .form-container {padding: 0 !important;}
    
    .author-block {
        border: 2px solid #e0e0e0;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background: white;
        position: relative;
    }
    
    .author-block.lead-author {
        border-color: #0B5ED7;
        background: #f8f9ff;
    }
    
    .author-block-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    .author-number {
        font-size: 1.1rem;
        font-weight: 600;
        color: #0B5ED7;
    }
    
    .remove-author-btn {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .role-badges {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.5rem;
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
    
    .attendance-summary {
        background: #e7f3ff;
        border: 2px solid #0B5ED7;
        border-radius: 8px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .fee-display {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0B5ED7;
    }
    
    .word-count {
        font-size: 0.875rem;
        color: #666;
        text-align: right;
        margin-top: 0.25rem;
    }
    
    .word-count.warning {
        color: #ff6b6b;
        font-weight: 600;
    }
</style>
@endpush

@section('poster-content')
<div class="form-card">
    {{-- Form Header --}}
    <div class="form-header" style="background: linear-gradient(135deg, #0B5ED7 0%, #084298 100%);">
        <h2><i class="fas fa-file-alt"></i> Poster Registration Form</h2>
        <p>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</p>
    </div>

    <div class="form-body">
        {{-- Step Indicator --}}
        <div class="progress-container">
            <div class="step-indicator">
                <div class="step-item active">
                    <div class="step-number">1</div>
                    <div class="step-label">Registration Details</div>
                </div>
                <div class="step-connector"></div>
                <div class="step-item">
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

        {{-- Auto-save Indicator --}}
        <div id="autoSaveIndicator" class="alert alert-info d-none" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 150px; box-shadow: 0 2px 10px rgba(0,0,0,0.2); padding: 12px 20px; border-radius: 5px;">
            <i class="fas fa-spinner fa-spin"></i> <span>Saving...</span>
        </div>

        {{-- Form Container --}}
        <form id="posterRegistrationForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="session_id" value="{{ session()->getId() }}">

            {{-- 1) Registration Details --}}
            <div class="form-section">
                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-info-circle"></i> Registration Details</h5>
                <div class="row">
                    <div class="col-md-6">
                        <label for="sector" class="form-label">Sector <span class="text-danger">*</span></label>
                        <select class="form-select" id="sector" name="sector" required>
                            <option value="">Select Sector</option>
                            @foreach(config('constants.sectors') as $sector)
                                <option value="{{ $sector }}" {{ ($draft->sector ?? '') == $sector ? 'selected' : '' }}>
                                    {{ $sector }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                        <select class="form-select" id="currency" name="currency" required>
                            <option value="">Select Currency</option>
                            <option value="INR" {{ ($draft->currency ?? 'INR') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                            <option value="USD" {{ ($draft->currency ?? '') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            {{-- 2) Abstract / Poster Details --}}
            <div class="form-section">
                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-file-alt"></i> Abstract / Poster Details</h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="poster_category" class="form-label">Poster Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="poster_category" name="poster_category" required>
                            <option value="Breaking Boundaries" selected>Breaking Boundaries</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    <div class="col-md-6">
                        <label for="abstract_title" class="form-label">Abstract Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="abstract_title" name="abstract_title" 
                               value="{{ $draft->abstract_title ?? '' }}" maxlength="250" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="abstract" class="form-label">Abstract (Max 250 words) <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="abstract" name="abstract" rows="6" required>{{ $draft->abstract ?? '' }}</textarea>
                        <div class="word-count" id="wordCount">0 / 250 words</div>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="extended_abstract" class="form-label">Extended Abstract Upload (PDF only) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="extended_abstract" name="extended_abstract" accept=".pdf" required>
                        @if(isset($draft->extended_abstract_path) && $draft->extended_abstract_path)
                        <small class="text-muted">Current file: {{ basename($draft->extended_abstract_path) }}</small>
                        @endif
                        <div class="invalid-feedback"></div>
                        <small class="text-muted d-block mt-2">Max file size: 5MB. Download Extended Abstract Submission Template /Format: <a href="{{ asset('uploads/events/Bengaluru_Tech_Summit_2026-Abstract-submission-template.pdf') }}" target="_blank">Click Here</a></small>
                    </div>
                    <div class="col-md-6">
                        <label for="presentation_mode" class="form-label">Preferred Presentation <span class="text-danger">*</span></label>
                        <select class="form-select" id="presentation_mode" name="presentation_mode" required>
                            <option value="Poster" selected>Poster</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            {{-- 3) Authors Section --}}
            
            <div class="form-section">
                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Authors</h5>
                    
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> <strong>Note:</strong> Exactly 1 Lead Author is required. Maximum 1 Presenter is allowed. Lead Author and Presenter can be the same person.
                </div>

                <div id="authorsContainer">
                    {{-- Authors will be dynamically added here --}}
                </div>
                <button type="button" class="btn btn-primary btn-sm" id="addAuthorBtn">
                        <i class="fas fa-plus"></i> Add Another Author
                    </button>
            </div>

           

            {{-- 5) Attendance Summary & Pricing --}}
            <div id="priceDisplay" class="alert alert-success d-none mt-2">
                <h5><i class="fas fa-calculator"></i> Price Calculation</h5>
                <div id="priceDetails">
                    <table class="table table-bordered text-center" style="color: #000;">
                        <tr>
                            <td><strong>Attendees Count: </strong><span id="attendeeCount">0</span></td>
                            <td><strong>Rate per Attendee: </strong><span id="ratePerAttendee">₹0</span></td>
                            <td><strong>Base Price: </strong><span id="registrationFee">₹0</span></td>
                            <td><strong>GST (18%): </strong><span id="gstAmount">₹0</span></td>
                            <td><strong>Processing Charge: </strong><span id="processingCharge">₹0</span></td>
                            <td><strong>Total Amount Payable: </strong><span id="totalAmount">₹0</span></td>
                        </tr>
                    </table>
                    <div id="attendeesList" class="mt-2" style="display: none"></div>
                </div>
            </div>

            {{-- 6) Publication Permission --}}
            <div class="form-section">
               
                    <input class="form-check-input" type="checkbox" id="publication_permission" name="publication_permission" value="1" required>
                    <label class="form-check-label" for="publication_permission">
                        <strong>I grant permission to publish this abstract/poster.</strong>
                    </label>
                    <br>
                    <div class="invalid-feedback"></div>

                      <input class="form-check-input" type="checkbox" id="authors_approval" name="authors_approval" value="1" required>
                    <label class="form-check-label" for="authors_approval">
                        <strong>I declare that all authors have approved this submission and the information provided is accurate.</strong>
                    </label>
                    <div class="invalid-feedback"></div>
                
            </div>

            {{-- 7) Author(s) Approval --}}
            {{-- <div class="form-section">
                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-check-circle"></i> Author(s) Approval</h5>
                <div class="form-check">
                  
                </div>
            </div> --}}

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>Note:</strong> After submitting this form, you will be redirected to preview your registration details before making payment.
            </div>

            {{-- Submit Button --}}
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-primary btn-lg" id="submitForm">
                    <i class="fas fa-check me-2"></i> Submit & Preview
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    h5.border-bottom {
        color: var(--primary-color);
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- intl-tel-input -->
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let authorCount = -1;
    const maxAuthors = 4;
    let leadAuthorIndex = null;
    let presenterIndex = null;
    
    // Store intl-tel-input instances for phone fields
    const authorPhoneInstances = new Map();
    
    // Countries data from server
    const countriesData = @json($countries);
    
    // Pricing configuration (per attendee)
    const pricingINR = 2000;
    const pricingUSD = 25;
    const gstRate = {{ config('constants.GST_RATE') }}; // GST percentage
    const indProcessingCharge = {{ config('constants.IND_PROCESSING_CHARGE') }}; // Processing charge for INR
    const intProcessingCharge = {{ config('constants.INT_PROCESSING_CHARGE') }}; // Processing charge for USD

    // Initialize with one author
    addAuthor();

    // Word count for abstract
    const abstractField = document.getElementById('abstract');
    const wordCountDisplay = document.getElementById('wordCount');
    
    abstractField.addEventListener('input', function() {
        const text = this.value.trim();
        const words = text.length > 0 ? text.split(/\s+/).length : 0;
        wordCountDisplay.textContent = `${words} / 250 words`;
        
        if (words > 250) {
            wordCountDisplay.classList.add('warning');
        } else {
            wordCountDisplay.classList.remove('warning');
        }
    });
    
    // Trigger initial count
    abstractField.dispatchEvent(new Event('input'));

    // Add author button
    document.getElementById('addAuthorBtn').addEventListener('click', function() {
        if (authorCount < maxAuthors - 1) {
            addAuthor();
        }
    });
    
    // Restrict phone inputs to numbers only
    function restrictToNumbers(input) {
        if (input.dataset.restricted === 'true') {
            return;
        }
        input.dataset.restricted = 'true';
        
        input.addEventListener('beforeinput', function(e) {
            if (e.inputType === 'deleteContentBackward' || 
                e.inputType === 'deleteContentForward' || 
                e.inputType === 'deleteByCut') {
                return;
            }
            if (e.data && !/^\d+$/.test(e.data)) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
        }, { capture: true, passive: false });
        
        input.addEventListener('input', function(e) {
            let value = e.target.value;
            const cursorPos = e.target.selectionStart || 0;
            const numbersOnly = value.replace(/[^\d]/g, '').replace(/\s/g, '');
            
            if (value !== numbersOnly) {
                e.target.value = numbersOnly;
                e.target.setSelectionRange(cursorPos - 1, cursorPos - 1);
            }
        });
        
        input.addEventListener('keypress', function(e) {
            if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete' && e.key !== 'Tab' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') {
                e.preventDefault();
                return false;
            }
        });
    }

    function addAuthor() {
        if (authorCount >= maxAuthors - 1) {
            Swal.fire('Maximum Reached', 'You can add maximum 4 authors.', 'warning');
            return;
        }

        authorCount++;
        const container = document.getElementById('authorsContainer');
        const authorBlock = document.createElement('div');
        authorBlock.className = 'author-block';
        authorBlock.dataset.authorIndex = authorCount;
        
        authorBlock.innerHTML = `
            <div class="author-block-header">
                <div>
                    <span class="author-number">Author ${authorCount + 1}</span>
                    <div class="role-badges" id="roleBadges${authorCount}"></div>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-author-btn" onclick="removeAuthor(${authorCount})" ${authorCount === 1 ? 'disabled' : ''}>
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-2">
                    <label for="author_title_${authorCount}" class="form-label">Title <span class="text-danger">*</span></label>
                    <select class="form-select" id="author_title_${authorCount}" name="authors[${authorCount}][title]" required>
                        <option value="">Select Title</option>
                        <option value="Dr.">Dr.</option>
                        <option value="Prof.">Prof.</option>
                        <option value="Mr.">Mr.</option>
                        <option value="Ms.">Ms.</option>
                        <option value="Mrs.">Mrs.</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-5">
                    <label for="author_first_name_${authorCount}" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author_first_name_${authorCount}" name="authors[${authorCount}][first_name]" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-5">
                    <label for="author_last_name_${authorCount}" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author_last_name_${authorCount}" name="authors[${authorCount}][last_name]" required>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="author_designation_${authorCount}" class="form-label">Designation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author_designation_${authorCount}" name="authors[${authorCount}][designation]" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label for="author_email_${authorCount}" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="author_email_${authorCount}" name="authors[${authorCount}][email]" required>
                    <div class="invalid-feedback"></div>
                </div>
                
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="author_mobile_${authorCount}" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control author-mobile" id="author_mobile_${authorCount}" name="authors[${authorCount}][mobile]" 
                           pattern="[0-9]*" inputmode="numeric" required>
                    <input type="hidden" name="authors[${authorCount}][phone_country_code]" id="author_mobile_country_code_${authorCount}" value="+91">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6 cv-upload-section" id="cv_upload_section_${authorCount}" style="display: ${authorCount === 0 ? 'block' : 'none'};">
                    <label for="author_cv_${authorCount}" class="form-label">Upload CV (PDF only) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control cv-upload-input" id="author_cv_${authorCount}" name="authors[${authorCount}][cv]" accept=".pdf" ${authorCount === 0 ? 'required' : ''}>
                    <div class="invalid-feedback"></div>
                    <small class="text-muted">Required for Lead Author. Max file size: 5MB.</small>
                </div>
            </div>
             <div class="form-section">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">is this the Lead Author? <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input lead-author-checkbox" type="checkbox" id="lead_author_yes_${authorCount}" value="${authorCount}" ${authorCount === 0 ? 'checked' : ''} onchange="toggleLeadAuthor(${authorCount}, true)">
                            <label class="form-check-label" for="lead_author_yes_${authorCount}">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="lead_author_no_${authorCount}" value="-1" ${authorCount !== 0 ? 'checked' : ''} onchange="toggleLeadAuthor(${authorCount}, false)">
                            <label class="form-check-label" for="lead_author_no_${authorCount}">No</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">are you a Presenter? <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input presenter-checkbox" type="checkbox" id="presenter_yes_${authorCount}" name="authors[${authorCount}][is_presenter]" value="1" ${authorCount === 0 ? 'checked' : ''} onchange="togglePresenter(${authorCount}, true)">
                            <label class="form-check-label" for="presenter_yes_${authorCount}">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="presenter_no_${authorCount}" value="0" ${authorCount !== 0 ? 'checked' : ''} onchange="togglePresenter(${authorCount}, false)">
                            <label class="form-check-label" for="presenter_no_${authorCount}">No</label>
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-4">
                    <label class="form-label">Will Attend Event? <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input attend-checkbox" type="checkbox" id="will_attend_yes_${authorCount}" name="authors[${authorCount}][will_attend]" value="1" ${authorCount === 0 ? 'checked' : ''} onchange="toggleAttendance(${authorCount}, true)">
                            <label class="form-check-label" for="will_attend_yes_${authorCount}">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="will_attend_no_${authorCount}" value="0" ${authorCount !== 0 ? 'checked' : ''} onchange="toggleAttendance(${authorCount}, false)">
                            <label class="form-check-label" for="will_attend_no_${authorCount}">No</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="author_country_${authorCount}" class="form-label">Country <span class="text-danger">*</span></label>
                    <select class="form-select author-country-select" id="author_country_${authorCount}" name="authors[${authorCount}][country_id]" data-author-index="${authorCount}" required>
                        <option value="">Select Country</option>
                        ${countriesData.map(country => 
                            `<option value="${country.id}" ${country.code === 'IN' ? 'selected' : ''}>${country.name}</option>`
                        ).join('')}
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label for="author_state_${authorCount}" class="form-label">State <span class="text-danger">*</span></label>
                    <select class="form-select" id="author_state_${authorCount}" name="authors[${authorCount}][state_id]" required disabled>
                        <option value="">Select Country First</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="author_city_${authorCount}" class="form-label">City <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author_city_${authorCount}" name="authors[${authorCount}][city]" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label for="author_postal_code_${authorCount}" class="form-label">Postal Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author_postal_code_${authorCount}" name="authors[${authorCount}][postal_code]" required>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <h6 class="mt-4 mb-3">Affiliation</h6>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="author_institution_${authorCount}" class="form-label">Institution / Organisation <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author_institution_${authorCount}" name="authors[${authorCount}][institution]" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4">
                    <label for="author_affiliation_city_${authorCount}" class="form-label">City <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author_affiliation_city_${authorCount}" name="authors[${authorCount}][affiliation_city]" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-4">
                    <label for="author_affiliation_country_${authorCount}" class="form-label">Country <span class="text-danger">*</span></label>
                    <select class="form-select" id="author_affiliation_country_${authorCount}" name="authors[${authorCount}][affiliation_country_id]" required>
                        <option value="">Select Country</option>
                        ${countriesData.map(country => 
                            `<option value="${country.id}" ${country.code === 'IN' ? 'selected' : ''}>${country.name}</option>`
                        ).join('')}
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        `;
        
        container.appendChild(authorBlock);
        
        // Initialize intl-tel-input for mobile number
        setTimeout(() => {
            const mobileInput = document.getElementById(`author_mobile_${authorCount}`);
            const mobileCountryCodeInput = document.getElementById(`author_mobile_country_code_${authorCount}`);
            
            if (mobileInput && typeof window.intlTelInput !== 'undefined') {
                // Apply restriction BEFORE initializing intl-tel-input
                restrictToNumbers(mobileInput);
                
                mobileInput.placeholder = '';
                const itiMobile = window.intlTelInput(mobileInput, {
                    initialCountry: 'in',
                    preferredCountries: ['in', 'us', 'gb'],
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                    separateDialCode: true,
                    nationalMode: false,
                    autoPlaceholder: 'off',
                });
                
                // Store the instance for later use
                authorPhoneInstances.set(mobileInput, itiMobile);
                
                // Re-apply restriction after intl-tel-input initialization
                restrictToNumbers(mobileInput);
                
                // Update hidden field when country changes
                mobileInput.addEventListener('countrychange', function() {
                    const selectedCountryData = itiMobile.getSelectedCountryData();
                    if (mobileCountryCodeInput && selectedCountryData && selectedCountryData.dialCode) {
                        mobileCountryCodeInput.value = '+' + selectedCountryData.dialCode;
                    }
                });
            }
        }, 100);
        
        // Load states for India (default selected country)
        if (authorCount === 0) {
            // Find India's country ID from the countriesData
            const indiaCountry = countriesData.find(country => country.code === 'IN');
            if (indiaCountry) {
                setTimeout(() => {
                    loadStatesForAuthorCountry(indiaCountry.id, 0);
                }, 100);
            }
        }
        
        // Update button state
        updateAddAuthorButton();
        
        // Set first author as lead by default
        if (authorCount === 0) {
            leadAuthorIndex = 0;
            presenterIndex = 0;
            updateRoleBadges(0);
        }
    }

    window.removeAuthor = function(index) {
        if (authorCount <= 0) {
            Swal.fire('Cannot Remove', 'At least one author is required.', 'warning');
            return;
        }

        const authorBlock = document.querySelector(`[data-author-index="${index}"]`);
        if (authorBlock) {
            // Check if removing lead author
            if (leadAuthorIndex === index) {
                Swal.fire('Cannot Remove', 'Cannot remove the Lead Author. Please assign another author as Lead Author first.', 'warning');
                return;
            }
            
            // Check if removing presenter
            if (presenterIndex === index) {
                presenterIndex = null;
            }
            
            authorBlock.remove();
            authorCount--;
            updateAddAuthorButton();
            renumberAuthors();
            updateAttendanceSummary();
        }
    }

    function renumberAuthors() {
        const blocks = document.querySelectorAll('.author-block');
        let newCount = -1;
        blocks.forEach(block => {
            newCount++;
            block.dataset.authorIndex = newCount;
            block.querySelector('.author-number').textContent = `Author ${newCount + 1}`;
            
            // Enable/disable remove button
            const removeBtn = block.querySelector('.remove-author-btn');
            if (removeBtn) {
                removeBtn.disabled = newCount === 0;
            }
        });
        authorCount = newCount;
    }

    function updateAddAuthorButton() {
        const btn = document.getElementById('addAuthorBtn');
        if (authorCount >= maxAuthors - 1) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-ban"></i> Maximum Authors Reached (4)';
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus"></i> Add Another Author';
        }
    }

    window.toggleLeadAuthor = function(index, isLead) {
        const yesCheckbox = document.getElementById(`lead_author_yes_${index}`);
        const noCheckbox = document.getElementById(`lead_author_no_${index}`);
        
        if (isLead) {
            // User wants to make this author the lead
            yesCheckbox.checked = true;
            noCheckbox.checked = false;
            
            // Remove lead author styling from all blocks
            document.querySelectorAll('.author-block').forEach(block => {
                block.classList.remove('lead-author');
            });
            
            // Uncheck all other "Yes" checkboxes and check their "No" checkboxes
            document.querySelectorAll('.lead-author-checkbox').forEach(checkbox => {
                const otherIndex = checkbox.value;
                if (otherIndex !== String(index)) {
                    checkbox.checked = false;
                    const otherNoCheckbox = document.getElementById(`lead_author_no_${otherIndex}`);
                    if (otherNoCheckbox) otherNoCheckbox.checked = true;
                }
            });
            
            // Add to selected block
            const selectedBlock = document.querySelector(`[data-author-index="${index}"]`);
            if (selectedBlock) {
                selectedBlock.classList.add('lead-author');
                leadAuthorIndex = index;
                updateRoleBadges(index);
            }
        } else {
            // User wants to deselect this as lead
            if (leadAuthorIndex === index) {
                // Prevent deselecting if this is the current lead author
                Swal.fire('Cannot Deselect', 'Please select another author as Lead Author before deselecting this one.', 'warning');
                yesCheckbox.checked = true;
                noCheckbox.checked = false;
                return;
            }
            yesCheckbox.checked = false;
            noCheckbox.checked = true;
        }
    }

    window.togglePresenter = function(index, isPresenter) {
        const yesCheckbox = document.getElementById(`presenter_yes_${index}`);
        const noCheckbox = document.getElementById(`presenter_no_${index}`);
        
        if (isPresenter) {
            yesCheckbox.checked = true;
            noCheckbox.checked = false;
            
            // Uncheck all other presenter "Yes" checkboxes and check their "No" checkboxes
            document.querySelectorAll('.presenter-checkbox').forEach(checkbox => {
                if (checkbox.id !== `presenter_yes_${index}`) {
                    checkbox.checked = false;
                    const otherIndex = checkbox.id.replace('presenter_yes_', '');
                    const otherNoCheckbox = document.getElementById(`presenter_no_${otherIndex}`);
                    if (otherNoCheckbox) otherNoCheckbox.checked = true;
                }
            });
            presenterIndex = index;
        } else {
            yesCheckbox.checked = false;
            noCheckbox.checked = true;
            if (presenterIndex === index) {
                presenterIndex = null;
            }
        }
        
        updateRoleBadges(index);
    }

    window.toggleAttendance = function(index, willAttend) {
        const yesCheckbox = document.getElementById(`will_attend_yes_${index}`);
        const noCheckbox = document.getElementById(`will_attend_no_${index}`);
        
        if (willAttend) {
            yesCheckbox.checked = true;
            noCheckbox.checked = false;
        } else {
            // Check if at least one author needs to attend
            const anyAttending = Array.from(document.querySelectorAll('.attend-checkbox')).some(cb => cb.checked && cb.id !== `will_attend_yes_${index}`);
            
            if (!anyAttending) {
                Swal.fire('Cannot Deselect', 'At least one author must attend the event.', 'warning');
                yesCheckbox.checked = true;
                noCheckbox.checked = false;
                return;
            }
            
            yesCheckbox.checked = false;
            noCheckbox.checked = true;
        }
        
        updateAttendanceSummary();
    }

    function updateRoleBadges(index) {
        // Update badges for all authors
        document.querySelectorAll('.author-block').forEach(block => {
            const blockIndex = parseInt(block.dataset.authorIndex);
            const badgesContainer = document.getElementById(`roleBadges${blockIndex}`);
            const cvUploadSection = document.getElementById(`cv_upload_section_${blockIndex}`);
            const cvUploadInput = document.getElementById(`author_cv_${blockIndex}`);
            
            if (badgesContainer) {
                badgesContainer.innerHTML = '';
                
                if (leadAuthorIndex === blockIndex) {
                    badgesContainer.innerHTML += '<span class="role-badge badge-lead">Lead Author</span>';
                    // Show CV upload for lead author
                    if (cvUploadSection) cvUploadSection.style.display = 'block';
                    if (cvUploadInput) cvUploadInput.required = true;
                } else {
                    // Hide CV upload for non-lead authors
                    if (cvUploadSection) cvUploadSection.style.display = 'none';
                    if (cvUploadInput) {
                        cvUploadInput.required = false;
                        cvUploadInput.value = ''; // Clear file if any
                    }
                }
                
                if (presenterIndex === blockIndex) {
                    badgesContainer.innerHTML += '<span class="role-badge badge-presenter">Presenter</span>';
                }
            }
        });
    }

    window.updateAttendanceSummary = function() {
        const attendees = [];
        let count = 0;
        
        document.querySelectorAll('.author-block').forEach(block => {
            const index = block.dataset.authorIndex;
            const willAttend = document.getElementById(`will_attend_yes_${index}`);
            const firstName = document.getElementById(`author_first_name_${index}`);
            const lastName = document.getElementById(`author_last_name_${index}`);
            
            if (willAttend && willAttend.checked) {
                count++;
                const name = (firstName?.value || '') + ' ' + (lastName?.value || '');
                attendees.push(name.trim() || `Author ${parseInt(index) + 1} (name not entered)`);
            }
        });
        
        // Calculate fee
        const currency = document.querySelector('#currency')?.value || 'INR';
        const pricePerAttendee = currency === 'INR' ? pricingINR : pricingUSD;
        const baseAmount = count * pricePerAttendee;
        
        let gstAmount = 0;
        let processingAmount = 0;
        let totalFee = baseAmount;
        
        if (currency === 'INR') {
            // For INR: Add GST and Processing Charge
            gstAmount = (baseAmount * gstRate) / 100;
            processingAmount = (baseAmount * indProcessingCharge) / 100;
            totalFee = baseAmount + gstAmount + processingAmount;
        } else {
            // For USD: Add Processing Charge only (no GST)
            processingAmount = (baseAmount * intProcessingCharge) / 100;
            totalFee = baseAmount + processingAmount;
        }
        
        const currencySymbol = currency === 'INR' ? '₹' : '$';
        
        // Format numbers with commas
        const formatNumber = (num) => num.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Update the display if there are attendees
        if (count > 0) {
            // Update table cells
            document.getElementById('attendeeCount').textContent = count;
            document.getElementById('ratePerAttendee').textContent = `${currencySymbol}${formatNumber(pricePerAttendee)}`;
            document.getElementById('registrationFee').textContent = `${currencySymbol}${formatNumber(baseAmount)}`;
            document.getElementById('gstAmount').textContent = `${currencySymbol}${formatNumber(gstAmount)}`;
            document.getElementById('processingCharge').textContent = `${currencySymbol}${formatNumber(processingAmount)}`;
            document.getElementById('totalAmount').textContent = `${currencySymbol}${formatNumber(totalFee)}`;
            
            // Update attendees list
            const attendeesList = document.getElementById('attendeesList');
            if (attendees.length > 0) {
                attendeesList.innerHTML = '<small class="text-muted"><strong>Attendees:</strong><br>' + 
                    attendees.map(name => `• ${name}`).join('<br>') + '</small>';
            } else {
                attendeesList.innerHTML = '';
            }
            
            // Show the price display
            document.getElementById('priceDisplay').classList.remove('d-none');
        } else {
            // Hide the price display if no attendees
            document.getElementById('priceDisplay').classList.add('d-none');
        }
    }

    // Currency change listener
    const currencySelect = document.getElementById('currency');
    if (currencySelect) {
        currencySelect.addEventListener('change', updateAttendanceSummary);
    }
    
    // Function to load states for a country
    function loadStatesForAuthorCountry(countryId, authorIndex) {
        const stateSelect = document.getElementById(`author_state_${authorIndex}`);
        
        if (!countryId) {
            stateSelect.innerHTML = '<option value="">Select State</option>';
            stateSelect.disabled = false;
            return;
        }
        
        stateSelect.innerHTML = '<option value="">Loading states...</option>';
        stateSelect.disabled = true;
        
        fetch('{{ route("get.states") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ country_id: countryId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Failed to load states');
            }
            return response.json();
        })
        .then(data => {
            stateSelect.innerHTML = '<option value="">Select State</option>';
            if (data && data.length > 0) {
                data.forEach(state => {
                    const option = document.createElement('option');
                    option.value = state.id;
                    option.textContent = state.name;
                    stateSelect.appendChild(option);
                });
            }
            stateSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading states:', error);
            stateSelect.innerHTML = '<option value="">Error loading states</option>';
            stateSelect.disabled = false;
        });
    }
    
    // Event delegation for dynamically added country selects
    document.getElementById('authorsContainer').addEventListener('change', function(e) {
        if (e.target.classList.contains('author-country-select')) {
            const authorIndex = e.target.dataset.authorIndex;
            const countryId = e.target.value;
            loadStatesForAuthorCountry(countryId, authorIndex);
        }
    });
    
    // Initial call to update attendance summary on page load
    updateAttendanceSummary();

    // Function to validate mobile number based on country code
    function validateMobileNumber(mobileInput, countryCode) {
        const mobileNumber = mobileInput.value.replace(/\s/g, '');
        const dialCode = countryCode.replace('+', '');
        
        if (!mobileNumber) {
            return { valid: false, message: 'Mobile number is required.' };
        }
        
        if (dialCode === '91') {
            // India: exactly 10 digits
            if (mobileNumber.length !== 10) {
                return { valid: false, message: 'Invalid Mobile Number.' };
            }
        } else {
            // Other countries: 6-15 digits
            if (mobileNumber.length < 6 || mobileNumber.length > 15) {
                return { valid: false, message: 'Invalid Mobile Number.' };
            }
        }
        
        return { valid: true, message: '' };
    }

    // Function to check if lead author email is unique
    async function checkLeadAuthorEmailUniqueness(email) {
        try {
            const response = await fetch('{{ route("check.author.email") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ email: email })
            });
            
            const data = await response.json();
            return data.unique === true;
        } catch (error) {
            console.error('Error checking email uniqueness:', error);
            return true; // Allow submission if check fails
        }
    }

    // Form submission
    document.getElementById('submitForm').addEventListener('click', async function(e) {
        e.preventDefault();
        const form = document.getElementById('posterRegistrationForm');
        
        // Validation
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            
            // Find first invalid field and focus on it
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
            
            Swal.fire('Validation Error', 'Please fill all required fields correctly.', 'error');
            return;
        }
        
        // Check lead author
        if (leadAuthorIndex === null || leadAuthorIndex === undefined || leadAuthorIndex < 0) {
            Swal.fire('Validation Error', 'Please select exactly one Lead Author.', 'error');
            return;
        }
        
        // Check lead author email uniqueness
        const leadAuthorEmail = document.getElementById(`author_email_${leadAuthorIndex}`);
        if (leadAuthorEmail && leadAuthorEmail.value) {
            const isUnique = await checkLeadAuthorEmailUniqueness(leadAuthorEmail.value);
            if (!isUnique) {
                leadAuthorEmail.scrollIntoView({ behavior: 'smooth', block: 'center' });
                leadAuthorEmail.focus();
                Swal.fire('Validation Error', 'This email is already registered as a lead author. Please use a different email address.', 'error');
                return;
            }
        }
        
        // Validate mobile numbers for all authors
        let mobileValidationFailed = false;
        document.querySelectorAll('.author-block').forEach(block => {
            const index = block.dataset.authorIndex;
            const mobileInput = document.getElementById(`author_mobile_${index}`);
            const countryCodeInput = document.getElementById(`author_mobile_country_code_${index}`);
            
            if (mobileInput && countryCodeInput) {
                const validation = validateMobileNumber(mobileInput, countryCodeInput.value);
                if (!validation.valid) {
                    mobileValidationFailed = true;
                    mobileInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    mobileInput.focus();
                    mobileInput.classList.add('is-invalid');
                    const feedbackDiv = mobileInput.nextElementSibling?.nextElementSibling;
                    if (feedbackDiv && feedbackDiv.classList.contains('invalid-feedback')) {
                        feedbackDiv.textContent = validation.message;
                        feedbackDiv.style.display = 'block';
                    }
                    Swal.fire('Validation Error', validation.message, 'error');
                    return;
                }
            }
        });
        
        if (mobileValidationFailed) {
            return;
        }
        
        // Check lead author CV upload
        const leadAuthorCvInput = document.getElementById(`author_cv_${leadAuthorIndex}`);
        if (leadAuthorCvInput && !leadAuthorCvInput.files.length) {
            leadAuthorCvInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            Swal.fire('Validation Error', 'Please upload CV (PDF) for the Lead Author.', 'error');
            return;
        }
        
        // Check word count
        const abstract = document.getElementById('abstract').value.trim();
        const words = abstract.split(/\s+/).length;
        if (words > 250) {
            Swal.fire('Validation Error', 'Abstract exceeds 250 words. Please reduce the length.', 'error');
            return;
        }
        
        const submitBtn = this;
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        
        const formData = new FormData(form);
        
        // Combine country code and mobile number for each author
        document.querySelectorAll('.author-block').forEach(block => {
            const index = block.dataset.authorIndex;
            const mobileInput = document.getElementById(`author_mobile_${index}`);
            const countryCodeInput = document.getElementById(`author_mobile_country_code_${index}`);
            
            if (mobileInput && countryCodeInput && mobileInput.value) {
                const countryCode = countryCodeInput.value || '+91';
                const mobileNumber = mobileInput.value.replace(/\s/g, '');
                // Format: +CC-NUMBER (e.g., +91-1234567890)
                const fullPhoneNumber = `${countryCode}-${mobileNumber}`;
                formData.set(`authors[${index}][mobile]`, fullPhoneNumber);
            }
        });
        
        // Ensure lead_author has the correct value (not -1)
        formData.set('lead_author', leadAuthorIndex);
        
        fetch('{{ route("poster.register.newDraft") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                // Try to parse error as JSON, fallback to text
                return response.json().catch(() => {
                    throw new Error('Server error: ' + response.status);
                }).then(errorData => {
                    // Handle Laravel validation errors
                    if (errorData.errors) {
                        const errorMessages = Object.values(errorData.errors).flat();
                        const errorList = errorMessages.map(msg => `• ${msg}`).join('<br>');
                        throw new Error(errorList);
                    }
                    throw new Error(errorData.message || 'Validation failed. Please check the form.');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    Swal.fire('Success', 'Form submitted successfully!', 'success');
                }
            } else {
                throw new Error(data.message || 'Submission failed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            
            // Display error with HTML support for lists
            Swal.fire({
                icon: 'error',
                title: 'Error',
                html: error.message || 'An error occurred. Please try again.',
                confirmButtonText: 'OK'
            });
        });
    });
});
</script>
@endpush
@endsection
