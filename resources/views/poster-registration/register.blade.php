@extends('layouts.poster-registration')

@section('title', 'Poster Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

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
                        <small class="text-muted d-block mt-2">Max file size: 5MB. Download Extended Abstract Submission Template /Format: <a href="{{ asset('asset/docs/Extended_Abstract_Template.pdf') }}" target="_blank">Click Here</a></small>
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

            {{-- 4) Presentation Preference --}}
            <div class="form-section">
                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-presentation"></i> Presentation Preference</h5>
                <div class="row">
                    <div class="col-md-6">
                        <label for="presentation_mode" class="form-label">Preferred Mode of Presentation <span class="text-danger">*</span></label>
                        <select class="form-select" id="presentation_mode" name="presentation_mode" required>
                            <option value="Poster only" selected>Poster only</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            {{-- 5) Attendance Summary & Pricing --}}
            <div class="attendance-summary">
                <h5 class="mb-3"><i class="fas fa-users"></i> Summary & Pricing</h5>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Attendees Count:</label>
                        <p class="h4" id="attendeeCount">0</p>
                        <div id="attendeesList" class="mt-2"></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Registration Fee:</label>
                        <p class="fee-display" id="registrationFee">₹ 0</p>
                        <small class="text-muted" id="feeCalculation"></small>
                    </div>
                </div>
            </div>

            {{-- 6) Publication Permission --}}
            <div class="form-section">
                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-file-signature"></i> Publication Permission</h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="publication_permission" name="publication_permission" value="1" required>
                    <label class="form-check-label" for="publication_permission">
                        <strong>I grant permission to publish this abstract/poster.</strong>
                    </label>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            {{-- 7) Author(s) Approval --}}
            <div class="form-section">
                <h5 class="mb-3 border-bottom pb-2"><i class="fas fa-check-circle"></i> Author(s) Approval</h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="authors_approval" name="authors_approval" value="1" required>
                    <label class="form-check-label" for="authors_approval">
                        <strong>I declare that all authors have approved this submission and the information provided is accurate.</strong>
                    </label>
                    <div class="invalid-feedback"></div>
                </div>
            </div>

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
<script>
document.addEventListener('DOMContentLoaded', function() {
    let authorCount = 0;
    const maxAuthors = 4;
    let leadAuthorIndex = null;
    let presenterIndex = null;
    
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
        if (authorCount < maxAuthors) {
            addAuthor();
        }
    });

    function addAuthor() {
        if (authorCount >= maxAuthors) {
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
                    <span class="author-number">Author ${authorCount}</span>
                    <div class="role-badges" id="roleBadges${authorCount}"></div>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-author-btn" onclick="removeAuthor(${authorCount})" ${authorCount === 1 ? 'disabled' : ''}>
                    <i class="fas fa-trash"></i> Remove
                </button>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="author_first_name_${authorCount}" class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author_first_name_${authorCount}" name="authors[${authorCount}][first_name]" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label for="author_last_name_${authorCount}" class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="author_last_name_${authorCount}" name="authors[${authorCount}][last_name]" required>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="author_email_${authorCount}" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="author_email_${authorCount}" name="authors[${authorCount}][email]" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="col-md-6">
                    <label for="author_mobile_${authorCount}" class="form-label">Mobile Number <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="author_mobile_${authorCount}" name="authors[${authorCount}][mobile]" required>
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            
            <div class="row mb-3 cv-upload-section" id="cv_upload_section_${authorCount}" style="display: ${authorCount === 1 ? 'block' : 'none'};">
                <div class="col-md-12">
                    <label for="author_cv_${authorCount}" class="form-label">Upload CV (PDF only) <span class="text-danger">*</span></label>
                    <input type="file" class="form-control cv-upload-input" id="author_cv_${authorCount}" name="authors[${authorCount}][cv]" accept=".pdf" ${authorCount === 1 ? 'required' : ''}>
                    <div class="invalid-feedback"></div>
                    <small class="text-muted">Required for Lead Author. Max file size: 5MB.</small>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Lead Author? <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input lead-author-radio" type="radio" name="lead_author" id="lead_author_yes_${authorCount}" value="${authorCount}" ${authorCount === 1 ? 'checked' : ''} onchange="updateLeadAuthor(${authorCount})">
                            <label class="form-check-label" for="lead_author_yes_${authorCount}">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="lead_author" id="lead_author_no_${authorCount}" value="0" ${authorCount !== 1 ? 'checked' : ''}>
                            <label class="form-check-label" for="lead_author_no_${authorCount}">No</label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Presenter? <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input presenter-checkbox" type="checkbox" id="presenter_${authorCount}" name="authors[${authorCount}][is_presenter]" value="1" ${authorCount === 1 ? 'checked' : ''} onchange="updatePresenter(${authorCount})">
                            <label class="form-check-label" for="presenter_${authorCount}">Yes</label>
                        </div>
                    </div>
                    <small class="text-muted">Optional: Only one presenter allowed</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Will Attend Event? <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input attend-checkbox" type="checkbox" id="will_attend_${authorCount}" name="authors[${authorCount}][will_attend]" value="1" ${authorCount === 1 ? 'checked' : ''} onchange="updateAttendanceSummary()">
                            <label class="form-check-label" for="will_attend_${authorCount}">Yes</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <h6 class="mt-4 mb-3">Affiliation Address</h6>
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
                    <select class="form-select author-state-select" id="author_state_${authorCount}" name="authors[${authorCount}][state_id]" data-author-index="${authorCount}" required>
                        <option value="">Select State</option>
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
                    <label for="author_institution_${authorCount}" class="form-label">Institution <span class="text-danger">*</span></label>
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
        
        // Load states for India (default selected country)
        if (authorCount === 1) {
            // Find India's country ID from the countriesData
            const indiaCountry = countriesData.find(country => country.code === 'IN');
            if (indiaCountry) {
                setTimeout(() => {
                    loadStatesForAuthorCountry(indiaCountry.id, 1);
                }, 100);
            }
        }
        
        // Update button state
        updateAddAuthorButton();
        
        // Set first author as lead by default
        if (authorCount === 1) {
            leadAuthorIndex = 1;
            updateRoleBadges(1);
        }
    }

    window.removeAuthor = function(index) {
        if (authorCount <= 1) {
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
        let newCount = 0;
        blocks.forEach(block => {
            newCount++;
            block.dataset.authorIndex = newCount;
            block.querySelector('.author-number').textContent = `Author ${newCount}`;
            
            // Enable/disable remove button
            const removeBtn = block.querySelector('.remove-author-btn');
            if (removeBtn) {
                removeBtn.disabled = newCount === 1;
            }
        });
        authorCount = newCount;
    }

    function updateAddAuthorButton() {
        const btn = document.getElementById('addAuthorBtn');
        if (authorCount >= maxAuthors) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-ban"></i> Maximum Authors Reached';
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-plus"></i> Add Another Author';
        }
    }

    window.updateLeadAuthor = function(index) {
        // Remove lead author styling from all blocks
        document.querySelectorAll('.author-block').forEach(block => {
            block.classList.remove('lead-author');
        });
        
        // Add to selected block
        const selectedBlock = document.querySelector(`[data-author-index="${index}"]`);
        if (selectedBlock) {
            selectedBlock.classList.add('lead-author');
            leadAuthorIndex = index;
            updateRoleBadges(index);
        }
    }

    window.updatePresenter = function(index) {
        const checkbox = document.getElementById(`presenter_${index}`);
        
        if (checkbox.checked) {
            // Uncheck all other presenter checkboxes
            document.querySelectorAll('.presenter-checkbox').forEach(cb => {
                if (cb.id !== `presenter_${index}`) {
                    cb.checked = false;
                }
            });
            presenterIndex = index;
        } else {
            presenterIndex = null;
        }
        
        updateRoleBadges(index);
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
            const willAttend = document.getElementById(`will_attend_${index}`);
            const firstName = document.getElementById(`author_first_name_${index}`);
            const lastName = document.getElementById(`author_last_name_${index}`);
            
            if (willAttend && willAttend.checked) {
                count++;
                const name = (firstName?.value || '') + ' ' + (lastName?.value || '');
                attendees.push(name.trim() || `Author ${index} (name not entered)`);
            }
        });
        
        // Update count
        document.getElementById('attendeeCount').textContent = count;
        
        // Update attendees list
        const attendeesList = document.getElementById('attendeesList');
        if (attendees.length > 0) {
            attendeesList.innerHTML = '<small class="text-muted"><strong>Attendees:</strong><br>' + 
                attendees.map(name => `• ${name}`).join('<br>') + '</small>';
        } else {
            attendeesList.innerHTML = '';
        }
        
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
        document.getElementById('registrationFee').textContent = `${currencySymbol} ${totalFee.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        
        let calculationText = `${count} attendee(s) × ${currencySymbol}${pricePerAttendee.toLocaleString()} = ${currencySymbol}${baseAmount.toLocaleString()}<br>`;
        
        if (currency === 'INR') {
            calculationText += `GST (${gstRate}%): ${currencySymbol}${gstAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}<br>`;
            calculationText += `Processing Charge (${indProcessingCharge}%): ${currencySymbol}${processingAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}<br>`;
        } else {
            calculationText += `Processing Charge (${intProcessingCharge}%): ${currencySymbol}${processingAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}<br>`;
        }
        
        calculationText += `<strong>Total: ${currencySymbol}${totalFee.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</strong>`;
        
        document.getElementById('feeCalculation').innerHTML = calculationText;
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
        .then(response => response.json())
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

    // Form submission
    document.getElementById('submitForm').addEventListener('click', function() {
        const form = document.getElementById('posterRegistrationForm');
        
        // Validation
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            Swal.fire('Validation Error', 'Please fill all required fields.', 'error');
            return;
        }
        
        // Check lead author
        if (!leadAuthorIndex) {
            Swal.fire('Validation Error', 'Please select exactly one Lead Author.', 'error');
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
        
        fetch('{{ route("poster.register.newDraft") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
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
            Swal.fire('Error', error.message || 'An error occurred. Please try again.', 'error');
        });
    });
});
</script>
@endpush
@endsection
