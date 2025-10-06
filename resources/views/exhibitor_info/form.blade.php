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
        @media (max-width: 350px) {
            .custom-height {
                height: 2850px;
            }
        }


        @media (min-width: 768px) {
            .custom-height {
                height: 1900px;
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

    {{-- Highlight a note over here by saying once updated cannot be changed --}}
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
                    action="{{ route('exhibitor.info.submit') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Panel: Exhibitor Basic Information -->
                    <div class="multisteps-form__panel border-radius-xl bg-white js-active" data-animation="FadeIn">
                        <h5 class="font-weight-bolder mb-0">Exhibitor
                            Directory @if ($application->assoc_mem == 'Startup Exhibitor')
                                - Startup Innovation Zone
                            @endif
                        </h5>
                        {{-- <p class="mb-5 text-sm">Prefilled details & mandatory exhibitor inputs</p>
                        <p class="mb-3">
                            <span class="badge text-bg-warning text-dark">
                                Note: Kindly fill the below details. Once submitted cannot be changed.
                            </span>
                        </p> --}}
                        <div class="multisteps-form__content">
                            @if ($application->assoc_mem != 'Startup Exhibitor')
                                <div class="row mt-5">
                                    <div class="col-sm-12">
                                        <div class="input-group input-group-dynamic is-filled">
                                            <label class="">Select Category
                                                <span class="red-label">*</span>
                                            </label>
                                            <select class="form-control" name="category" required>
                                                <option value="" disabled
                                                    {{ empty($application->category) ? 'selected' : '' }}>
                                                    Select
                                                </option>
                                                <option value="Exhibitor"
                                                    {{ $application->category == 'Exhibitor' ? 'selected' : '' }}>
                                                    Exhibitor
                                                </option>
                                                <option value="Sponsor"
                                                    {{ $application->category == 'Sponsor' ? 'selected' : '' }}>
                                                    Sponsor
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="row mt-5">


                                <div class="col-sm-6">
                                    <label class="form-label">Name of the Exhibitor (Organisation Name) <span
                                            class="red-label">*</span> </label>
                                    <div class="input-group input-group-dynamic is-filled">
                                        <input class="form-control" type="text" name="company_name"
                                            value="{{ $application->company_name ?? '' }}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <label class="form-label">Booth Number</label>
                                    <div class="input-group input-group-dynamic is-filled">

                                        <input class="form-control" type="text"
                                            value="{{ $application->stallNumber ?? '' }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <label class="form-label">Name for Fascia (Fascia name will be written on Stall)
                                        <span class="red-label">*</span> </label>
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">

                                        <input class="form-control" type="text" name="fascia_name"
                                            value="{{ $fasciaName }}" required>
                                    </div>



                                </div>
                                <small class="text-muted mt-2">Note: For Shell scheme stall only.</small>
                            </div>

                            {{-- make a div that will show the sector --}}
                            <div class="row mt-5">
                                <div class="col-sm-12">
                                    <label class="form-label">Sector <span class="red-label">*</span></label>
                                    <div class="input-group input-group-dynamic is-filled">
                                        <select class="form-control" name="sector" required>
                                            <option value="" disabled {{ empty($exhibitorInfo->sector) ? 'selected' : '' }}>Select Sector</option>
                                            @foreach($sectors as $sector)
                                                <option value="{{ $sector['name'] }}" {{ (isset($exhibitorInfo->sector) && $exhibitorInfo->sector == $sector['name']) ? 'selected' : '' }}>
                                                    {{ $sector['name'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-4 pe-1">
                                            <label class="form-label">Contact Person Salutation <span
                                                    class="red-label">*</span>
                                            </label>
                                            <div class="input-group input-group-dynamic is-filled">

                                                <select class="form-control" name="salutation" required>
                                                    <option value="" disabled
                                                        {{ empty($salutation) ? 'selected' : '' }}>Select
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
                                            <label class="form-label"> Contact Person First Name <span
                                                    class="red-label">*</span>
                                            </label>
                                            <div class="input-group input-group-dynamic {{ $cssClass }}">

                                                <input class="form-control" type="text" name="contact_first_name"
                                                    value="{{ $firstName }}" required>
                                            </div>
                                        </div>
                                        <div class="col-4 ps-1">
                                            <label class="form-label">Contact Person Last Name <span
                                                    class="red-label">*</span>
                                            </label>
                                            <div class="input-group input-group-dynamic {{ $cssClass }}">

                                                <input class="form-control" type="text" name="contact_last_name"
                                                    value="{{ $lastName }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="row mt-5">

                                <div class="col-sm-6">
                                    <label class="form-label">Contact Person Designation <span
                                            class="red-label">*</span></label>
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">

                                        <input class="form-control" type="text" name="designation"
                                            value="{{ $exhibitorInfo->designation ?? '' }}" required>
                                    </div>
                                </div>


                            </div>

                            <div class="row mt-5">
                                <div class="col-12">
                                    <label class="form-label ">Organisation Address <span
                                            class="red-label">*</span></label>

                                    <div class="input-group input-group-dynamic is-filled">
                                        <textarea class="form-control" name="address" rows="2" required>{{ $application->full_address ?? '' }}</textarea>
                                    </div>

                                    {{-- Please check the Address Properly because it can't be changed again --}}

                                </div>
                                <small class="text-muted mt-2">Note: Do not enter city, state, country, or ZIP code in
                                    the text area. Please use the following designated fields provided for these
                                    details.</small>

                            </div>


                            <div class="row mt-5">
                                <div class="col-sm-3">
                                    <label class="form-label">Country <span class="red-label">*</span></label>
                                    <div class="input-group input-group-dynamic is-filled">

                                        <select class="form-control" name="country" id="countrySelect" required>
                                            <option value="">Select Country</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label class="form-label">State <span class="red-label">*</span></label>
                                    <div class="input-group input-group-dynamic is-filled">

                                        <select class="form-control" name="state" id="stateSelect" required>
                                            <option value="">Select State</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label class="form-label">City <span class="red-label">*</span></label>
                                    <div class="input-group input-group-dynamic is-filled">

                                        <input class="form-control" type="text" name="city"
                                            value="{{ $exhibitorInfo->city ?? '' }}" required>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <label class="form-label">Zip Code <span class="red-label">*</span></label>
                                    <div class="input-group input-group-dynamic is-filled">

                                        <input class="form-control" type="text" name="zip_code"
                                            value="{{ $exhibitorInfo->zip_code ?? '' }}" required>
                                    </div>
                                </div>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const countrySelect = document.getElementById('countrySelect');
                                    const stateSelect = document.getElementById('stateSelect');
                                    const selectedCountry = "{{ $exhibitorInfo->country ?? '' }}";
                                    const selectedState = "{{ $exhibitorInfo->state ?? '' }}";

                                    // Store country mapping for ISO2 lookup
                                    let countryMap = {};

                                    // Fetch countries
                                    fetch("{{ route('api.countries') }}")
                                        .then(res => res.json())
                                        .then(countries => {
                                            countries.forEach(country => {
                                                const opt = document.createElement('option');
                                                opt.value = country.name;
                                                opt.textContent = country.name;
                                                
                                                // Store mapping for ISO2 lookup
                                                countryMap[country.name] = country.iso2;
                                                
                                                if (country.name === selectedCountry || country.iso2 === selectedCountry) {
                                                    opt.selected = true;
                                                }
                                                countrySelect.appendChild(opt);
                                            });
                                            if (selectedCountry) {
                                                // Find the ISO2 for the selected country
                                                const selectedCountryIso2 = countryMap[selectedCountry] || selectedCountry;
                                                loadStates(selectedCountryIso2);
                                            }
                                        });

                                    countrySelect.addEventListener('change', function() {
                                        const selectedCountryName = this.value;
                                        const countryIso2 = countryMap[selectedCountryName];
                                        loadStates(countryIso2);
                                    });

                                    function loadStates(countryIso) {
                                        stateSelect.innerHTML = '<option value="">Select State</option>';
                                        if (!countryIso) return;
                                        fetch(`{{ url('/api/states') }}/${countryIso}`)
                                            .then(res => res.json())
                                            .then(states => {
                                                states.forEach(state => {
                                                    const opt = document.createElement('option');
                                                    opt.value = state.name;
                                                    opt.textContent = state.name;
                                                    if (state.name === selectedState) opt.selected = true;
                                                    stateSelect.appendChild(opt);
                                                });
                                            });
                                    }
                                });
                            </script>

                            <div class="row mt-5">
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <label class="form-label">Contact Email Address <span
                                            class="red-label">*</span></label>
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">

                                        <input class="form-control" type="email" name="email"
                                            value="{{ $exhibitorInfo->email ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Mobile Number <span class="red-label">*</span></label>
                                    <div class="input-group input-group-dynamic is-filled {{ $cssClass }}">

                                        <input id="phone" class="form-control iti" type="tel" name="phone"
                                            value="{{ $exhibitorInfo->phone ?? '' }}" required autocomplete="off">
                                    </div>
                                </div>

                            </div>
                            
                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <label class="form-label">Telephone Number </label>
                                    <div class="input-group input-group-dynamic is-filled {{ $cssClass }}">
                                        <input id="telPhone" class="form-control iti" type="tel" name="telPhone"
                                            value="{{ $exhibitorInfo->telPhone ?? '' }}" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <label class="form-label">Website <span class="red-label">*</span></label>
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">

                                        <input class="form-control" type="url" name="website"
                                            value="{{ $exhibitorInfo->website ?? '' }}">
                                    </div>
                                    <small class="text-muted mt-4" style="font-size: 0.7rem;">Note: Please enter the full
                                        URL including https:// e.g. https://www.bengalurutechsummit.com
                                    </small>
                                </div>
                            </div>

                            <div class="row mt-5">
                                @if (empty($exhibitorInfo) || empty($exhibitorInfo->submission_status) || $exhibitorInfo->submission_status == 0)
                                    <div class="col-sm-6 mt-3 mt-sm-0">
                                        <label class="form-label">Upload Logo <span class="red-label">*</span>
                                        </label>
                                        <div class="input-group input-group-dynamic is-filled {{ $cssClass }}">

                                            <input class="form-control" type="file" name="logo" accept="image/*"
                                                required>
                                        </div>

                                    </div>
                                    <small class="text-muted mt-2">Note: Image dimension should be 200px (width) x 125px
                                        (height), size less than 100KB. Allowed formats: JPG, JPEG, PNG only.</small>
                                @else
                                    <div class="col-sm-6 mt-3 mt-sm-0">
                                        <div class="input-group input-group-dynamic is-filled {{ $cssClass }}">
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $exhibitorInfo->logo) }}"
                                                    alt="Uploaded Logo" style="max-height: 60px;">

                                            </div>

                                        </div>
                                        <small class="text-success d-block">Your uploaded logo.</small>
                                    </div>
                                @endif
                            </div>



                            <div class="row mt-5">
                                <div class="col-12">
                                    <label class="form-label">Company Description <span class="red-label">*</span>
                                    </label>
                                    <div class="input-group input-group-dynamic is-filled">
                                        <textarea class="form-control" name="description" id="description" rows="6" maxlength="700" required
                                            oninput="updateCharCount()">{{ trim($exhibitorInfo->description ?? '') }}</textarea>
                                    </div>
                                    <small id="charCount" class="text-muted">0 / 700 characters</small>

                                    <div class="mt-3">
                                        <div class="">
                                            <h6 class="mb-2"><strong>Note:</strong></h6>
                                            <ul class="mb-2">
                                                <li>This organisational profile will get published in Exhibitors Directory
                                                </li>
                                                <li>Please write at least 250 characters & maximum 700 characters</li>
                                                <li>If you're copying and pasting from an external document, make sure to
                                                    press enter and then delete that blank enter line to activate profile
                                                    box.</li>
                                            </ul>
                                            <p class="mb-0">
                                                <strong>If you have any technical problem, please contact:</strong><br>
                                                <strong>Name:</strong> Mr. Vivek Patil<br>
                                                <strong>Email:</strong> support@bengalurutechsummit.com
                                            </p>
                                        </div>
                                    </div>


                                </div>
                            </div>



                            {{-- make a address field that will fetch the details from the $exhibitorInfo->address --}}


                            <hr class="my-4">

                            {{-- Website and Social Media Links --}}


                            {{-- <div class="row mt-5">
                                <div class="col-sm-6">
                                    <label class="form-label">LinkedIn</label>
                                    <div class="input-group input-group-dynamic {{ $cssClass }} ">
                                      
                                        <input class="form-control" type="url" name="linkedin"
                                            value="{{ $exhibitorInfo->linkedin ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <label class="form-label">Instagram</label>
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        
                                        <input class="form-control" type="url" name="instagram"
                                            value="{{ $exhibitorInfo->instagram ?? '' }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <label class="form-label">Facebook</label>
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                     
                                        <input class="form-control" type="url" name="facebook"
                                            value="{{ $exhibitorInfo->facebook ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <label class="form-label">YouTube</label>
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                   
                                        <input class="form-control" type="url" name="youtube"
                                            value="{{ $exhibitorInfo->youtube ?? '' }}">
                                    </div>
                                </div>
                            </div> --}}


                            @if (empty($exhibitorInfo) || empty($exhibitorInfo->submission_status) || $exhibitorInfo->submission_status == 0)
                                <div class="button-row d-flex mt-4">
                                    <button class="btn bg-gradient-dark ms-auto mb-0" type="submit"
                                        title="Save">Preview
                                    </button>
                                </div>
                            @else
                                <div class="button-row d-flex mt-4">
                                    <a class="btn bg-gradient-dark ms-auto mb-0" 
                                       href="{{ route('exhibitor.info.pdf') }}" 
                                       target="_blank"
                                       title="Preview PDF">
                                        Preview PDF
                                    </a>
                                </div>
                                <div class="alert alert-info mt-4" role="alert">
                                    You have already submitted your exhibitor information.
                                </div>
                            @endif
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
        document.addEventListener("DOMContentLoaded", function() {
            var phoneInputs = document.querySelectorAll("#phone, #telPhone");
            var itiInstances = new Map();

            phoneInputs.forEach(function(phoneInput) {
                function initializePhoneInput() {
                    if (itiInstances.has(phoneInput)) return;
                    var iti = window.intlTelInput(phoneInput, {
                        initialCountry: "auto",
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                        geoIpLookup: function(callback) {
                            fetch('https://ipapi.co/json')
                                .then(res => res.json())
                                .then(data => callback(data.country_code))
                                .catch(() => callback('IN'));
                        },
                        separateDialCode: true,
                        nationalMode: false,
                    });
                    itiInstances.set(phoneInput, iti);

                    // Set number if available (only for #phone, as per original logic)
                    @if (!empty($exhibitorInfo->phone))
                        if (phoneInput.id === "phone") {
                            var serverPhone = "{{ $exhibitorInfo->phone ?? '' }}";
                            if (serverPhone.startsWith('+')) {
                                iti.setNumber(serverPhone);
                            } else {
                                phoneInput.value = serverPhone;
                            }
                        }
                    @endif
                }

                // Wait for utilsScript to be loaded
                if (typeof window.intlTelInputUtils !== 'undefined') {
                    initializePhoneInput();
                } else {
                    phoneInput.addEventListener('focus', initializePhoneInput, {
                        once: true
                    });
                }

                var form = phoneInput.closest('form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        // Skip validation for telPhone (telephone) field as it's optional
                        if (phoneInput.id === 'telPhone') {
                            var iti = itiInstances.get(phoneInput);
                            if (iti && phoneInput.value.trim() !== '') {
                                var fullNumber = iti.getNumber();
                                if (fullNumber && fullNumber.startsWith('+')) {
                                    phoneInput.value = fullNumber;
                                }
                            }
                            return; // Skip validation for telPhone
                        }
                        
                        // Apply validation only for phone (mobile) field
                        var iti = itiInstances.get(phoneInput);
                        if (!iti) initializePhoneInput();
                        iti = itiInstances.get(phoneInput);
                        var fullNumber = iti ? iti.getNumber() : phoneInput.value;
                        if (!fullNumber || !fullNumber.startsWith('+')) {
                            e.preventDefault();
                            alert('Please enter a valid phone number with country code.');
                            phoneInput.focus();
                            return false;
                        }
                        phoneInput.value = fullNumber;
                    });
                }
            });
        });
    </script>

    <script>
        function updateCharCount() {
            const textarea = document.getElementById('description');
            const charCount = document.getElementById('charCount');
            charCount.textContent = `${textarea.value.length} / 700 characters`;
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

        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('description');
            textarea.addEventListener('input', validateDescriptionLength);
            validateDescriptionLength();
        });
    </script>

    <script>
        // Remove the placeholder value from phone inputs every 10 seconds
        function clearPhonePlaceholder() {
            const phoneInputs = document.querySelectorAll("#phone, #telPhone");
            phoneInputs.forEach(function(phoneInput) {
                if (phoneInput) {
                    phoneInput.setAttribute("placeholder", "");
                }
            });
        }

        window.addEventListener("load", function() {
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

        // Intercept form submit
        // document.addEventListener('DOMContentLoaded', function() {
        //     const form = document.querySelector('form.multisteps-form__form');
        //     if (!form) return;
        //     // Add hidden field for company name (readonly input is not submitted)
        //     if (!form.querySelector('input[name="company_name"]')) {
        //         const companyInput = document.createElement('input');
        //         companyInput.type = 'hidden';
        //         companyInput.name = 'company_name';
        //         companyInput.value = document.querySelector('input[readonly][value]')?.value || '';
        //         form.appendChild(companyInput);
        //     }
        //     form.submit();
        // });
        //     form.addEventListener('submit', function(e) {
        //         e.preventDefault();
        //         // Validate required fields (basic)
        //         if (!form.checkValidity()) {
        //             form.reportValidity();
        //             return;
        //         }
        //         // Gather data
        //         const data = getFormData(form);
        //         // Render preview HTML
        //         const htmlContent = renderDirectoryPreview(data);
        //         // Show preview modal
        //         showPdfPreview(htmlContent, function() {
        //             // On agree, generate PDF and submit
        //             window.FoxfordPDFGenerator.generate({
        //                 html: htmlContent,
        //                 format: 'A4',
        //                 orientation: 'portrait',
        //             }).then(pdfBlob => {
        //                 // Optionally, show PDF in new tab for confirmation
        //                 const pdfUrl = URL.createObjectURL(pdfBlob);
        //                 window.open(pdfUrl, '_blank');
        //                 // Actually submit the form
        //                 form.submit();
        //             });
        //         });
        //     }, {
        //         once: true
        //     }); // Only intercept once to avoid double modals
        // });
    </script>
@endsection
