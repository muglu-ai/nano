@extends('layouts.users')
@section('title', $slug ?? '')
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />
    <style>
        .red-label {
            color: red;
        }

        .custom-label {
            font-size: 1rem !important;
        }

        .form-label {
            font-size: 0.9rem !important;
            font-weight: 500 !important;
            color: #000000 !important;
        }

        @media (max-width: 767.98px) {
            .custom-height {
                height: 2650px;
            }
        }

        /*
        if screen size is less than 350px then set the height to 2800px
        */
        @media (max-width: 767.98px) {
            .custom-height {
                height: 2650px !important;
            }
        }

        /*
        if screen size is less than 350px then set the height to 2800px
        */
        @media (max-width: 350px) {
            .custom-height {
                height: 2850px !important;
            }
        }


        @media (min-width: 768px) {
            .custom-height {
                height: 1900px !important;
            }
        }

        .iti {
            width: 100%;
        }

        .preview-field {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 0.75rem;
            border-radius: 0.375rem;
            min-height: 2.5rem;
            display: flex;
            align-items: center;
        }

        .preview-textarea {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 0.75rem;
            border-radius: 0.375rem;
            min-height: 6rem;
            /* white-space: pre-wrap; */
        }

        .preview-image {
            max-height: 60px;
            border-radius: 0.375rem;
        }
    </style>

    @php
        //if exhibitorInfo is filled then set the css value to is-filled
        $fasciaName = $exhibitorInfo->fascia_name ?? '';
        $cssClass = $fasciaName !== '' ? 'is-filled' : '';

        //break down the name into salutation, first and last name
        $contactPerson = $exhibitorInfo->contact_person ?? '';
        $salutation = '';
        $firstName = '';
        $lastName = '';

        if (!empty($contactPerson)) {
            // Match salutation (ends with a dot), first name, last name
            if (preg_match('/^([A-Za-z\.]+)\s+([^\s]+)\s*(.*)$/', $contactPerson, $matches)) {
                $salutation = trim($matches[1] ?? '');
                $firstName = trim($matches[2] ?? '');
                $lastName = trim($matches[3] ?? '');
            }
        }

        // Safe access to application properties
        $applicationCompanyName = (isset($application) && is_object($application)) ? ($application->company_name ?? '') : '';
        $applicationStallNumber = (isset($application) && is_object($application)) ? ($application->stallNumber ?? '') : '';
        $applicationFullAddress = $exhibitorInfo->address;
        $applicationCategory = (isset($application) && is_object($application)) ? ($application->category ?? '') : '';
        $applicationAssocMem = (isset($application) && is_object($application)) ? ($application->assoc_mem ?? '') : '';
        //dd($application);
    @endphp

    <div class="container mt-4">
        {{-- @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif --}}

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <div class=" border-radius-xl bg-white js-active custom-height " data-animation="FadeIn">
                    <h5 class="font-weight-bolder mb-0">Exhibitor
                        Directory @if ($applicationAssocMem == 'Startup Exhibitor')
                            - Startup Innovation Zone
                        @endif
                    </h5>
                    
                    @if (empty($exhibitorInfo) || empty($exhibitorInfo->submission_status) || $exhibitorInfo->submission_status == 0)
                        <div class="alert alert-warning mt-3" role="alert">
                            <strong>Note:</strong> This is a preview of your exhibitor information. Please review all details carefully before submitting.
                        </div>
                    @else
                        <div class="alert alert-success mt-3" role="alert">
                            <strong>Submitted:</strong> Your exhibitor information has been submitted successfully.
                        </div>
                    @endif

                    <div class=" custom-height">
                        @if (!is_null($applicationAssocMem) && $applicationAssocMem != 'Startup Exhibitor')
                            <div class="row mt-5">
                                <div class="col-sm-12">
                                    <label class="form-label">Select Category</label>
                                    <div class="preview-field">
                                        {{ $applicationCategory ?: 'Not selected' }}
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mt-5">
                            <div class="col-sm-6">
                                <label class="form-label">Name of the Exhibitor (Organization Name)</label>
                                <div class="preview-field">
                                    {{ $applicationCompanyName ?: 'Not provided' }}
                                </div>
                            </div>
                            <div class="col-sm-6 mt-3 mt-sm-0">
                                <label class="form-label">Booth Number</label>
                                <div class="preview-field">
                                    {{ $applicationStallNumber ?: 'Not assigned' }}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-6">
                                <label class="form-label">Name for Fascia (Fascia name will be written on Stall)</label>
                                <div class="preview-field mb-3">
                                    {{ $fasciaName ?: 'Not provided' }}
                                </div>
                                <div class="col-sm-6 mt-5 mt-sm-0">
                                    <small class="text-muted">Note: For Shell scheme stall only.</small>
                                </div>
                            </div>
                            
                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-12">
                                <label class="form-label">Sector</label>
                                <div class="preview-field">
                                    {{ $exhibitorInfo->sector ?? 'Not selected' }}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-4 pe-1">
                                        <label class="form-label">Contact Person Salutation</label>
                                        <div class="preview-field">
                                            {{ $salutation ?: 'Not provided' }}
                                        </div>
                                    </div>
                                    <div class="col-4 px-1">
                                        <label class="form-label">Contact Person First Name</label>
                                        <div class="preview-field">
                                            {{ $firstName ?: 'Not provided' }}
                                        </div>
                                    </div>
                                    <div class="col-4 ps-1">
                                        <label class="form-label">Contact Person Last Name</label>
                                        <div class="preview-field">
                                            {{ $lastName ?: 'Not provided' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-6">
                                <label class="form-label">Contact Person Designation</label>
                                <div class="preview-field">
                                    {{ $exhibitorInfo->designation ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-12">
                                <label class="form-label">Organisation Address</label>
                                <div class="preview-textarea">
                                    {{ $applicationFullAddress ?: 'Not provided' }}
                                </div>
                                <small class="text-muted mt-2">Note: Do not enter city, state, country, or ZIP code in
                                    the text area. Please use the following designated fields provided for these
                                    details.</small>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-3">
                                <label class="form-label">Country</label>
                                <div class="preview-field">
                                    {{ $exhibitorInfo->country ?? 'Not selected' }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">State</label>
                                <div class="preview-field">
                                    {{ $exhibitorInfo->state ?? 'Not selected' }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">City</label>
                                <div class="preview-field">
                                    {{ $exhibitorInfo->city ?? 'Not provided' }}
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label class="form-label">Zip Code</label>
                                <div class="preview-field">
                                    {{ $exhibitorInfo->zip_code ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-6">
                                <label class="form-label">Contact Email Address</label>
                                <div class="preview-field">
                                    {{ $exhibitorInfo->email ?? 'Not provided' }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Mobile Number</label>
                                <div class="preview-field">
                                    {{ $exhibitorInfo->phone ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-6">
                                <label class="form-label">Telephone Number</label>
                                <div class="preview-field">
                                    {{ $exhibitorInfo->telPhone ?? 'Not provided' }}
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Website</label>
                                <div class="preview-field">
                                    {{ $exhibitorInfo->website ?? 'Not provided' }}
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-sm-6">
                                <label class="form-label">Upload Logo</label>
                                <div class="preview-field">
                                    @if (!empty($exhibitorInfo->logo))
                                        <img src="{{ asset('storage/' . $exhibitorInfo->logo) }}" 
                                             alt="Uploaded Logo" class="preview-image">
                                    @else
                                        No logo uploaded
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row mt-5">
                            <div class="col-12">
                                <label class="form-label">Company Description</label>
                                <div class="preview-textarea">
                                    {{ $exhibitorInfo->description ?? 'Not provided' }}
                                </div>
                                <small class="text-muted">
                                    Character count: {{ strlen($exhibitorInfo->description ?? '') }} / 700 characters
                                </small>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('exhibitor.info') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Go Back
                            </a>

                            <div class="d-flex gap-2">
                                <!-- PDF Generation Button -->
                                <button type="button" id="openPdfModalBtn" class="btn btn-primary">
                                    <i class="fas fa-file-pdf me-2"></i>Preview PDF
                                </button>

                                @if ($exhibitorInfo->submission_status == 0)
                                    <form method="POST" action="{{ route('exhibitor.info.submit.final') }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>Submit
                                        </button>
                                    </form>
                                @else
                                    <div class="alert alert-info mb-0" role="alert">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Information already submitted
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PDF Preview Script (if needed) -->
    <script src="https://cdn.jsdelivr.net/npm/@foxford/pdf-generator@1.1.6/build/es/index.min.js"></script>
    <script>
        // Inline PDF preview modal using iframe
        (function(){
            function ensurePdfModal(){
                var modal = document.getElementById('inlinePdfModal');
                if (modal) return modal;
                modal = document.createElement('div');
                modal.id = 'inlinePdfModal';
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.width = '100vw';
                modal.style.height = '100vh';
                modal.style.background = 'rgba(0,0,0,0.7)';
                modal.style.zIndex = '9999';
                modal.style.display = 'none';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                modal.innerHTML = '\
                    <div style="background:#fff; width: 90%; height: 90%; border-radius:8px; display:flex; flex-direction:column;">\
                        <div style="padding:8px; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #ddd;">\
                            <strong>Exhibitor PDF Preview</strong>\
                            <div>\
                                <a id="downloadPdfLink" class="btn btn-sm btn-dark" style="margin-right:8px;" href="{{ route('exhibitor.info.pdf') }}">Download</a>\
                                <button id="closePdfModalBtn" class="btn btn-sm btn-secondary">Close</button>\
                            </div>\
                        </div>\
                        <iframe id="pdfIframe" src="" style="flex:1; width:100%; border:0;"></iframe>\
                    </div>';
                document.body.appendChild(modal);
                document.getElementById('closePdfModalBtn').onclick = function(){ modal.style.display = 'none'; };
                return modal;
            }
            document.addEventListener('DOMContentLoaded', function(){
                var btn = document.getElementById('openPdfModalBtn');
                if (!btn) return;
                btn.addEventListener('click', function(){
                    var modal = ensurePdfModal();
                    var iframe = document.getElementById('pdfIframe');
                    // Request inline stream (controller streams when inline=1)
                    iframe.src = '{{ route('exhibitor.info.pdf') }}?inline=1&ts=' + Date.now();
                    var dl = document.getElementById('downloadPdfLink');
                    dl.href = '{{ route('exhibitor.info.pdf') }}';
                    modal.style.display = 'flex';
                });
            });
        })();
        // Helper to get form data as object
        function getFormData(form) {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            return data;
        }

        // Render HTML preview for A5 PDF with header image and improved layout
        function renderDirectoryPreview(data) {
            return `
                <div style="width: 420px; height: 595px; font-family: Arial, sans-serif; padding: 32px 24px 24px 24px; box-sizing: border-box; background: #fff;">
                    <div style='text-align:center;margin-bottom:18px;'>
                        <img src="https://bengalurutechsummit.com/exhibitor_directory_logo.png" alt="Exhibitor Directory" style="max-width: 260px; max-height: 60px; display:block; margin:0 auto 8px auto;" />
                    </div>
                    <h2 style='margin-bottom: 18px; text-align:center; font-size: 1.3rem; letter-spacing: 1px; font-weight: bold;'>${data.fascia_name || ''}</h2>
                    <table style="width:100%; font-size: 1rem; border-collapse: collapse; margin-bottom: 10px;">
                        <tr>
                            <td style="vertical-align:top; width:110px;"><strong>Contact<br>Person:</strong></td>
                            <td>${data.salutation || ''} ${data.contact_first_name || ''} ${data.contact_last_name || ''}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;"><strong>Designation:</strong></td>
                            <td>${data.designation || ''}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;"><strong>Mobile:</strong></td>
                            <td>${data.phone || ''}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;"><strong>Telephone:</strong></td>
                            <td>${data.telPhone || ''}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;"><strong>Email:</strong></td>
                            <td>${data.email || ''}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;"><strong>Address:</strong></td>
                            <td>${data.address || ''}</td>
                        </tr>
                        <tr>
                            <td style="vertical-align:top;"><strong>Website:</strong></td>
                            <td>${data.website || ''}</td>
                        </tr>
                    </table>
                    <div style="margin-bottom: 6px;"><strong>Profile:</strong></div>
                    <div style="font-size:0.97rem; text-align:justify; line-height:1.5;">
                        ${(data.description || '').replace(/\n/g, '<br>')}
                    </div>
                </div>
            `;
        }

        // Show modal with PDF preview
        function showPdfPreview(htmlContent, onAgree) {
            // Create modal if not exists
            let modal = document.getElementById('pdfPreviewModal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'pdfPreviewModal';
                modal.style.position = 'fixed';
                modal.style.top = '0';
                modal.style.left = '0';
                modal.style.width = '100vw';
                modal.style.height = '100vh';
                modal.style.background = 'rgba(0,0,0,0.7)';
                modal.style.zIndex = '9999';
                modal.style.display = 'flex';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                modal.innerHTML = `
                    <div style="background: #fff; border-radius: 8px; padding: 24px; max-width: 480px; width: 100%; box-shadow: 0 2px 16px rgba(0,0,0,0.2);">
                        <h5 style='margin-bottom: 16px;'>Exhibitor Directory Preview (A5 PDF)</h5>
                        <div id="pdfPreviewContainer" style="width: 420px; height: 595px; border: 1px solid #ccc; margin-bottom: 16px; overflow: auto;"></div>
                        <div class="d-flex justify-content-end gap-2">
                            <button id="agreeAndSubmitBtn" class="btn btn-success">Agree & Submit</button>
                            <button id="cancelPreviewBtn" class="btn btn-secondary">Cancel</button>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }
            // Render HTML preview
            document.getElementById('pdfPreviewContainer').innerHTML = htmlContent;
            modal.style.display = 'flex';
            // Cancel button
            document.getElementById('cancelPreviewBtn').onclick = function() {
                modal.style.display = 'none';
            };
            // Agree button
            document.getElementById('agreeAndSubmitBtn').onclick = function() {
                modal.style.display = 'none';
                if (onAgree) onAgree();
            };
        }

        // Intercept form submit for PDF preview
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('exhibitor.info.submit') }}"]');
            if (!form) return;
            
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Gather data
                const data = getFormData(form);
                
                // Render preview HTML
                const htmlContent = renderDirectoryPreview(data);
                
                // Show preview modal
                showPdfPreview(htmlContent, function() {
                    // On agree, generate PDF and submit
                    window.FoxfordPDFGenerator.generate({
                        html: htmlContent,
                        format: 'A4',
                        orientation: 'portrait',
                    }).then(pdfBlob => {
                        // Optionally, show PDF in new tab for confirmation
                        const pdfUrl = URL.createObjectURL(pdfBlob);
                        window.open(pdfUrl, '_blank');
                        // Actually submit the form
                        form.submit();
                    });
                });
            });
        });
    </script>
@endsection
