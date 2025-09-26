@extends('layouts.users')
@section('title', $slug ?? '')
@section('content')
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css"/>

    <style>
        .red-label {
            color: red;
        }

        .custom-label {
            font-size: 1rem !important;
        }

        .form-label {
            font-size: 0.9rem !important;
        }

        @media (max-width: 767.98px) {
            .custom-height {
                height: 1150px;
            }
        }


        @media (min-width: 768px) {
            .custom-height {
                height: 950px;
            }
        }

        .iti {
            width: 100%;
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
    @endphp

    {{-- Highlight a note over here by saying once updated cannot be changed--}}
    <div class="container mt-4">
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

    {{-- Kindly fill the below details. Once submitted cannot be changed. --}}





    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <form class="multisteps-form__form custom-height" method="POST"
                      action="{{ route('exhibitor.info.submit') }}"
                      enctype="multipart/form-data">
                    @csrf

                    <!-- Panel: Exhibitor Basic Information -->
                    <div class="multisteps-form__panel border-radius-xl bg-white js-active" data-animation="FadeIn">
                        <h5 class="font-weight-bolder mb-0">Exhibitor Information</h5>
                        <p class="mb-5 text-sm">Prefilled details & mandatory exhibitor inputs</p>
                        <p class="mb-3">
              <span class="badge text-bg-warning text-dark">
                Note: Kindly fill the below details. Once submitted cannot be changed.
              </span>
                        </p>
                        <div class="multisteps-form__content">
                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic is-filled">
                                        <label class="form-label custom-label">Company Name <span
                                                    class="red-label">*</span> </label>
                                        <input class="form-control" type="text"
                                               value="{{ $application->company_name ?? '' }}"
                                               readonly>
                                    </div>
                                </div>
                                {{--                                <div class="col-sm-6 mt-3 mt-sm-0">--}}
                                {{--                                    <div class="input-group input-group-dynamic is-filled">--}}
                                {{--                                        <label class="form-label">Booth Number <span class="red-label">*</span></label>--}}
                                {{--                                        <input class="form-control" type="text" value="{{ $application->stallNumber ?? '' }}"--}}
                                {{--                                            readonly>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">Fascia Name <span class="red-label">*</span> </label>
                                        <input class="form-control" type="text" name="fascia_name"
                                               value="{{ $fasciaName }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-4 pe-1">
                                            <div class="input-group input-group-dynamic is-filled">
                                                <label class="form-label">Salutation <span class="red-label">*</span>
                                                </label>
                                                <select class="form-control" name="salutation" required>
                                                    <option value=""
                                                            disabled {{ empty($salutation) ? 'selected' : '' }}>Select
                                                    </option>
                                                    <option value="Mr." {{ $salutation == 'Mr.' ? 'selected' : '' }}>Mr.
                                                    </option>
                                                    <option value="Ms." {{ $salutation == 'Ms.' ? 'selected' : '' }}>Ms.
                                                    </option>
                                                    <option value="Mrs." {{ $salutation == 'Mrs.' ? 'selected' : '' }}>
                                                        Mrs.
                                                    </option>
                                                    <option value="Dr." {{ $salutation == 'Dr.' ? 'selected' : '' }}>Dr.
                                                    </option>
                                                    <option value="Prof." {{ $salutation == 'Prof.' ? 'selected' : '' }}>
                                                        Prof.
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4 px-1">
                                            <div class="input-group input-group-dynamic {{ $cssClass }}">
                                                <label class="form-label">First Name <span class="red-label">*</span>
                                                </label>
                                                <input class="form-control" type="text" name="contact_first_name"
                                                       value="{{ $firstName }}" required>
                                            </div>
                                        </div>
                                        <div class="col-4 ps-1">
                                            <div class="input-group input-group-dynamic {{ $cssClass }}">
                                                <label class="form-label">Last Name <span class="red-label">*</span>
                                                </label>
                                                <input class="form-control" type="text" name="contact_last_name"
                                                       value="{{ $lastName }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">Email Address <span class="red-label">*</span></label>
                                        <input class="form-control" type="email" name="email"
                                               value="{{ $exhibitorInfo->email ?? '' }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic is-filled {{ $cssClass }}">
                                        <label class="form-label">Phone Number <span class="red-label">*</span></label>
                                        <input id="phone" class="form-control" type="tel" name="phone"
                                               value="{{ $exhibitorInfo->phone ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <div class="input-group input-group-dynamic is-filled {{ $cssClass }}">
                                        <label class="form-label">Upload Logo <span class="red-label">*</span> </label>
                                        <input class="form-control" type="file" name="logo" accept="image/*"
                                               @if (!empty($exhibitorInfo->logo)) @else required @endif>
                                        @if (!empty($exhibitorInfo->logo))
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $exhibitorInfo->logo) }}"
                                                     alt="Uploaded Logo" style="max-height: 60px;">
                                                <small class="text-success d-block">Logo already uploaded.</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-12">
                                    <label class="form-label">Company Description <span class="red-label">*</span>
                                    </label>
                                    <div class="input-group input-group-dynamic is-filled">
                                        <textarea class="form-control" name="description" id="description" rows="3"
                                                  maxlength="750" required
                                                  oninput="updateCharCount()">{{ trim($exhibitorInfo->description ?? '') }}</textarea>
                                    </div>
                                    <small id="charCount" class="text-muted">0 / 750 characters</small>
                                </div>
                            </div>


                            <hr class="my-4">

                            {{-- Website and Social Media Links --}}
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">Website <span class="red-label">*</span></label>
                                        <input class="form-control" type="url" name="website"
                                               value="{{ $exhibitorInfo->website ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic {{ $cssClass }} ">
                                        <label class="form-label">LinkedIn</label>
                                        <input class="form-control" type="url" name="linkedin"
                                               value="{{ $exhibitorInfo->linkedin ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">Instagram</label>
                                        <input class="form-control" type="url" name="instagram"
                                               value="{{ $exhibitorInfo->instagram ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">Facebook</label>
                                        <input class="form-control" type="url" name="facebook"
                                               value="{{ $exhibitorInfo->facebook ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">YouTube</label>
                                        <input class="form-control" type="url" name="youtube"
                                               value="{{ $exhibitorInfo->youtube ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="button-row d-flex mt-4">
                                <button class="btn bg-gradient-dark ms-auto mb-0" type="submit"
                                        title="Save">Submit
                                </button>
                            </div>

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- intl-tel-input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>
    <script>
        function updateCharCount() {
            const textarea = document.getElementById('description');
            const charCount = document.getElementById('charCount');
            charCount.textContent = `${textarea.value.length} / 750 characters`;
        }

        document.addEventListener('DOMContentLoaded', updateCharCount);
    </script>

    <script>
        function validateDescriptionLength() {
            const textarea = document.getElementById('description');
            if (textarea.value.length < 300) {
                textarea.setCustomValidity('Company description must be at least 300 characters.');
            } else {
                textarea.setCustomValidity('');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const textarea = document.getElementById('description');
            textarea.addEventListener('input', validateDescriptionLength);
            validateDescriptionLength();
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const phoneInput = document.querySelector("#phone");
            const form = phoneInput.closest('form');
            let iti;

            function initializePhoneInput() {
                iti = window.intlTelInput(phoneInput, {
                    initialCountry: "auto",
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    geoIpLookup: function (callback) {
                        fetch('https://ipapi.co/json')
                            .then(res => res.json())
                            .then(data => callback(data.country_code))
                            .catch(() => callback('IN'));
                    },
                    separateDialCode: true,
                    nationalMode: false,
                });

                // Set number if available
                @if (!empty($exhibitorInfo->phone))
                const serverPhone = "{{ $exhibitorInfo->phone ?? '' }}";
                if (serverPhone.startsWith('+')) {
                    iti.setNumber(serverPhone);
                } else {
                    phoneInput.value = serverPhone;
                }
                @endif
            }

            if (window.intlTelInput) {
                initializePhoneInput();
            } else {
                phoneInput.addEventListener('load', initializePhoneInput);
            }

            form.addEventListener('submit', function (e) {
                const fullNumber = iti.getNumber();
                // console.log('Full number:', fullNumber);

                if (!fullNumber || !fullNumber.startsWith('+')) {
                    e.preventDefault();
                    alert('Please enter a valid phone number with country code.');
                    phoneInput.focus();
                    return false;
                }

                // Set the final phone value in +<country_code><number> format
                phoneInput.value = fullNumber;
            });
        });
    </script>

    <script>
        // Remove the placeholder value from phone input every 10 seconds
        function clearPhonePlaceholder() {
            const phoneInput = document.querySelector("#phone");
            if (phoneInput) {
                phoneInput.setAttribute("placeholder", "");
            }
        }

        window.addEventListener("load", function () {
            clearPhonePlaceholder();
            setInterval(clearPhonePlaceholder, 10);
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@foxford/pdf-generator@1.1.6/build/es/index.min.js"></script>
    <script>
        // Helper to get form data as object
        function getFormData(form) {
            const formData = new FormData(form);
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            return data;
        }

        // Render HTML preview for A5 PDF
        function renderDirectoryPreview(data) {
            return `
                <div style="width: 420px; height: 595px; font-family: Arial, sans-serif; padding: 24px; box-sizing: border-box;">
                    <h2 style='margin-bottom: 8px;'>${data.fascia_name || ''}</h2>
                    <p style='margin: 0 0 8px 0;'><strong>Company:</strong> ${data['company_name'] || ''}</p>
                    <p style='margin: 0 0 8px 0;'><strong>Contact:</strong> ${data.salutation || ''} ${data.contact_first_name || ''} ${data.contact_last_name || ''}</p>
                    <p style='margin: 0 0 8px 0;'><strong>Email:</strong> ${data.email || ''}</p>
                    <p style='margin: 0 0 8px 0;'><strong>Phone:</strong> ${data.phone || ''}</p>
                    <p style='margin: 0 0 8px 0;'><strong>Website:</strong> ${data.website || ''}</p>
                    <p style='margin: 0 0 8px 0;'><strong>Description:</strong><br>${(data.description || '').replace(/\n/g, '<br>')}</p>
                    <div style='margin-top: 12px;'>
                        <strong>Socials:</strong>
                        <ul style='margin: 0; padding-left: 18px;'>
                            ${data.linkedin ? `<li>LinkedIn: ${data.linkedin}</li>` : ''}
                            ${data.instagram ? `<li>Instagram: ${data.instagram}</li>` : ''}
                            ${data.facebook ? `<li>Facebook: ${data.facebook}</li>` : ''}
                            ${data.youtube ? `<li>YouTube: ${data.youtube}</li>` : ''}
                        </ul>
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
            document.getElementById('cancelPreviewBtn').onclick = function () {
                modal.style.display = 'none';
            };
            // Agree button
            document.getElementById('agreeAndSubmitBtn').onclick = function () {
                modal.style.display = 'none';
                if (onAgree) onAgree();
            };
        }

        // Intercept form submit
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form.multisteps-form__form');
            if (!form) return;
            // Add hidden field for company name (readonly input is not submitted)
            if (!form.querySelector('input[name="company_name"]')) {
                const companyInput = document.createElement('input');
                companyInput.type = 'hidden';
                companyInput.name = 'company_name';
                companyInput.value = document.querySelector('input[readonly][value]')?.value || '';
                form.appendChild(companyInput);
            }
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                // Validate required fields (basic)
                if (!form.checkValidity()) {
                    form.reportValidity();
                    return;
                }
                // Gather data
                const data = getFormData(form);
                // Render preview HTML
                const htmlContent = renderDirectoryPreview(data);
                // Show preview modal
                showPdfPreview(htmlContent, function () {
                    // On agree, generate PDF and submit
                    window.FoxfordPDFGenerator.generate({
                        html: htmlContent,
                        format: 'A5',
                        orientation: 'portrait',
                    }).then(pdfBlob => {
                        // Optionally, show PDF in new tab for confirmation
                        const pdfUrl = URL.createObjectURL(pdfBlob);
                        window.open(pdfUrl, '_blank');
                        // Actually submit the form
                        form.submit();
                    });
                });
            }, {once: true}); // Only intercept once to avoid double modals
        });
    </script>
@endsection
