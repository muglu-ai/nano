@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')
    <style>
        @media (min-width: 500px) {
            .progress-bar2 {
                display: none !important;
            }
        }

        .red-label {
            color: red;
            font-weight: bold;
        }

        .custom-hr {
            border: none;
            height: 3px;
            background: #bfb8b8;
            width: 100%;
            margin: 20px auto;
        }

        .choices .choices__list.choices__list--single .choices__item--selectable {
            margin-bottom: 0 !important;
        }
    </style>
    <div class="container py-2">
        <div class="row min-vh-80 mt-5">
            <div class="col-lg-10 col-md-10 col-12 m-auto">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n5 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="multisteps-form__progress">
                                <button class="multisteps-form__progress-btn js-active" disabled>
                                    <span>1. Personal Info</span>
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>2. Product
                                    Info
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>3. Terms and
                                    Conditions
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>4. Review
                                </button>
                            </div>
                            <small class="progress-bar2 d-block text-center text-white">1. Personal Info</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <form class="multisteps-form__form" id="step1" method="POST" enctype="multipart/form-data"
                              action="{{ route('application.exhibitor.submit') }}">
                            @csrf
                            @php
                                $isDisabled = isset($application) && $application->submission_status != 'in progress' ? 'disabled' : '';
                            @endphp

                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                 data-animation="FadeIn">
                                <div class="multisteps-form__content">
                                    <div class="container">
                                        <div class="row mt-3 ">
                                            {{--                                            <div class="col-12 col-sm-6">--}}
                                            {{--                                                <label class="form-control ms-0 red-label">Billing Country <span--}}
                                            {{--                                                        class="red-label">*</span></label>--}}
                                            {{--                                                <select class="form-control" name="billing_country" id="choices-language" required {{ $isDisabled }}>--}}
                                            {{--                                                    <option value="" disabled selected>Select Country</option>--}}
                                            {{--                                                    @foreach($countries as $country)--}}
                                            {{--                                                        <option value="{{ $country->code }}"--}}
                                            {{--                                                            {{ isset($application) && $application->billing_country_id == $country->id ? 'selected' : '' }}>--}}
                                            {{--                                                            {{ $country->name }}--}}
                                            {{--                                                        </option>--}}
                                            {{--                                                    @endforeach--}}
                                            {{--                                                </select>--}}

                                            {{--                                            </div>--}}
                                            <div class="col-12 col-sm-6">
                                                <label class="form-control ms-0 red-label ">Billing Country <span
                                                        class="red-label">*</span></label>
                                                <select class="choices__list choices__list--dropdown text-sm text-dark"
                                                        name="billing_country" id="choices-language"
                                                    {{ $isDisabled !== 'disabled' ? 'required' : '' }} {{ $isDisabled }}>
                                                    <option value="" disabled selected>Select Country</option>
                                                    @foreach($countries as $country)
                                                        <option class="text-sm text-dark" value="{{ $country->id }}"
                                                            {{ isset($application) && $application->billing_country_id == $country->id ? 'selected' : '' }}>
                                                            {{ $country->name }}

                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="form-control ms-0 red-label ">GST Compliance / Tax
                                                    Certificate <span class="red-label">*</span></label>
                                                <select class="form-control red-label " name="gst_compliance"
                                                        id="choices-sizes"
                                                        required {{ $isDisabled }} onchange="toggleGstNoRequired(this)">
                                                    <option
                                                        value="1" {{ isset($application) && $application->gst_compliance == 1 ? 'selected' : '' }}>
                                                        Yes
                                                    </option>
                                                    <option
                                                        value="0" {{ isset($application) && $application->gst_compliance == 0 ? 'selected' : '' }}>
                                                        No
                                                    </option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <hr class="custom-hr">
                                    <input type="hidden" name="event_id" value="1">
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <label for="gstNo" id="gstlabel" class="form-label red-label ">GST / Tax
                                                Number</label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="gst_no" id="gst_no"
                                                       {{--                                                       placeholder="22AAAAAXXXXXXX"--}}
                                                       {{--                                                       pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{21}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}"--}}
                                                       value="{{ $application->gst_no ?? '' }}"
                                                    {{ $isDisabled }}
                                                />
                                                <small>Error message</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="pan_no" class="form-label red-label ">PAN Number</label>
                                            <div class="input-group input-group-dynamic">
                                                <input class="multisteps-form__input form-control" type="text"
                                                       {{--                                                       placeholder="XYZPK8200S"--}}
                                                       {{--                                                       pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"--}}
                                                       name="pan_no" id="pan_no"
                                                       value="{{ $application->pan_no ?? '' }}"
                                                    {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="tan_no" class="form-label red-label ">TAN Number</label>
                                            <div class="input-group input-group-dynamic">
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="tan_no" id="tan_no"
                                                       {{--                                                       placeholder="ABCD12345X"--}}
                                                       value="{{ $application->tan_no ?? null }}"
                                                    {{--                                                       pattern="[A-Z]{4}[0-9]{5}[A-Z]{1}"--}}
                                                    {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <script>
                                        function toggleGstNoRequired(select) {
                                            const gstNoInput = document.getElementById('gst_no');
                                            const gstLabel = document.getElementById('gstlabel');
                                            const gst_Certificate = document.getElementById('gst_certificate');
                                            const gstCertificateLabel = document.getElementById('gst_label');
                                            const uploadGstCertificate = document.getElementById('gst_certificate_uploaded') || null;
                                            console.log(uploadGstCertificate);
                                            if (select.value === '1') {
                                                gstNoInput.setAttribute('required', 'required');
                                                gstLabel.innerHTML += ' <span class="red-label">*</span>';
                                                if (uploadGstCertificate === null) {
                                                    gst_Certificate.setAttribute('required', 'required');
                                                    gstCertificateLabel.innerHTML += ' <span class="red-label">*</span>';
                                                }

                                                //gst_Certificate.setAttribute('required', 'required');
                                                // gstCertificateLabel.innerHTML += ' <span class="red-label">*</span>';


                                            } else {
                                                gst_Certificate.setAttribute('required', 'required');
                                                gstCertificateLabel.textContent = 'Upload GST / TAX Certificate';
                                                gstNoInput.removeAttribute('required');
                                                gstLabel.textContent = 'GST / Tax Number';
                                                gst_Certificate.removeAttribute('required');
                                                gst_Certificate.textContent = 'GST / Tax Certificate';
                                            }
                                        }

                                        //on load if gstNoInput value is 1 then add * to gstlabel
                                        // document.addEventListener('DOMContentLoaded', function () {
                                        //     const gstCompliance = document.getElementById('choices-sizes');
                                        //     console.log(gstCompliance);
                                        //     const gstLabel = document.getElementById('gstlabel');
                                        //     if (gstCompliance.value === '1') {
                                        //         gstLabel.textContent += ' *';
                                        //     }
                                        //     toggleGstNoRequired(gstCompliance);
                                        // });


                                        // Call the function on page load to set the initial state
                                        document.addEventListener('DOMContentLoaded', function () {
                                            var gstCompliance = document.getElementById('choices-sizes');

                                            console.log('GST Compliance value:', gstCompliance.value);
                                            toggleGstNoRequired(gstCompliance);
                                            // toggleGstNoRequired(document.getElementById('choices-sizes').value);

                                        });
                                    </script>

                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-6">
                                            <label for="exampleFormControlInput1" class="form-label red-label ">Company Name
                                                <span class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="company_name" id="companyName"
                                                       value="{{ $application->company_name ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                            <label for="exampleFormControlInput1" class="form-label red-label ">Address
                                                <span class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="address"
                                                       id="companyAddress"
                                                       required {{ $isDisabled }} length="120"
                                                       value="{{ $application->address ?? '' }}"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">

                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="city" class="form-label red-label ">City <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="city"
                                                       id="city"
                                                       value="{{ $application->city_id ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>


                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="city" class="form-label red-label ">State <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">
                                                @if($isDisabled == 'disabled')
                                                    <input class="multisteps-form__input form-control" type="text"
                                                           name="state" id="state"
                                                           value="{{ $application->state->name ?? '' }}"
                                                           required {{ $isDisabled }}/>
                                                @else
                                                    <select class="form-control" name="state" id="choices-language"
                                                            required
                                                            {{ $isDisabled }} onchange="validateState(this)">
                                                        <option value="" disabled selected>Select State</option>
                                                    </select>
                                                @endif
                                                <script>
                                                    function validateState(select) {
                                                        if (select.value === "") {
                                                            alert("Please select a billing state.");
                                                        }
                                                    }
                                                </script>
                                                {{--                                                <script>--}}
                                                {{--                                                    $(document).ready(function () {--}}

                                                {{--                                                        $('select[name="billing_country"]').change(function () { // Select country dropdown by name--}}
                                                {{--                                                            var countryId = $(this).val();--}}

                                                {{--                                                            if (countryId) {--}}
                                                {{--                                                                $.ajax({--}}
                                                {{--                                                                    url: "{{ route('get.states') }}",--}}
                                                {{--                                                                    type: "POST",--}}
                                                {{--                                                                    data: {--}}
                                                {{--                                                                        country_id: countryId,--}}
                                                {{--                                                                        _token: "{{ csrf_token() }}"--}}
                                                {{--                                                                    },--}}
                                                {{--                                                                    success: function (states) {--}}
                                                {{--                                                                        $('select[name="state"]').empty().append('<option value="" disabled selected>Select State</option>');--}}

                                                {{--                                                                        $.each(states, function (key, state) {--}}
                                                {{--                                                                            $('select[name="state"]').append('<option value="' + state.id + '">' + state.name + '</option>');--}}
                                                {{--                                                                        });--}}
                                                {{--                                                                    },--}}
                                                {{--                                                                    error: function () {--}}
                                                {{--                                                                        alert("Error fetching states. Please try again.");--}}
                                                {{--                                                                    }--}}
                                                {{--                                                                });--}}
                                                {{--                                                            } else {--}}
                                                {{--                                                                $('select[name="state"]').empty().append('<option value="" disabled selected>Select State</option>');--}}
                                                {{--                                                            }--}}
                                                {{--                                                        });--}}
                                                {{--                                                    });--}}
                                                {{--                                                </script>--}}

                                                <script>
                                                    $(document).ready(function () {
                                                        function loadStates(countryId, selectedStateId = null) {
                                                            if (countryId) {
                                                                $.ajax({
                                                                    url: "{{ route('get.states') }}",
                                                                    type: "POST",
                                                                    data: {
                                                                        country_id: countryId,
                                                                        _token: "{{ csrf_token() }}"
                                                                    },
                                                                    success: function (states) {
                                                                        var stateDropdown = $('select[name="state"]');
                                                                        stateDropdown.empty().append('<option value="" disabled selected>Select State</option>');

                                                                        $.each(states, function (key, state) {
                                                                            stateDropdown.append('<option value="' + state.id + '" ' +
                                                                                (selectedStateId == state.id ? 'selected' : '') + '>' + state.name + '</option>');
                                                                        });
                                                                    },
                                                                    error: function () {
                                                                        alert("Error fetching states. Please try again.");
                                                                    }
                                                                });
                                                            } else {
                                                                $('select[name="state"]').empty().append('<option value="" disabled selected>Select State</option>');
                                                            }
                                                        }

                                                        // Load states on page load if billing_country_id exists
                                                        var selectedCountryId = "{{$application->billing_country_id ?? ''}}";
                                                        var selectedStateId = "{{$application->state_id ?? ''}}";

                                                        if (selectedCountryId) {
                                                            loadStates(selectedCountryId, selectedStateId);
                                                        }

                                                        // Update states when country dropdown changes
                                                        $('select[name="billing_country"]').change(function () {
                                                            var countryId = $(this).val();
                                                            loadStates(countryId);
                                                        });
                                                    });
                                                </script>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <label for="contactNo" class="form-label red-label ">Company
                                                Contact/Landline No
                                                <span class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">
                                                <div class="d-flex">
                                                    <select class="form-control" id="choices-languages"
                                                            name="contactNoCode" style="max-width: 80px;">
                                                        @foreach($countries->unique('code') as $country)
                                                            <option value="{{ $country->code }}"
                                                                {{ (isset($application) && explode('-', $application->landline)[0] == $country->code) || (!isset($application) && $country->code == '91') ? 'selected' : '' }}>
                                                                {{ $country->code }}
                                                            </option>
                                                        @endforeach
                                                    </select>


                                                    <input class="multisteps-form__input form-control" type="tel"
                                                           name="company_no" id="contactNo"
                                                           value="{{ isset($application) ? explode('-', $application->landline)[1] ?? '' : '' }}"
                                                           required {{ $isDisabled }}/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="email" class="form-label red-label ">Company E-Mail <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="email"
                                                       name="company_email" id="company_email"
                                                       value="{{ $application->company_email ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="website" class="form-label red-label ">Website <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="website" required
                                                       pattern="(https:\/\/|www\.).*"
                                                       value="{{ $application->website ?? '' }}" {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                    </div>
                                    {{--                                    @dd($application->main_product_category)--}}
                                    <div class="row mt-5 mb-5">
                                        <div class="col-12 col-sm-6">
                                            <label class="form-control ms-0 red-label">Main Product Category <span
                                                    class="red-label">*</span></label>
                                            <select class="form-control" name="main_product_category" id="products-list"
                                                    size="5" required {{ $isDisabled }}>
                                                @foreach($productCategories as $product)
                                                    <option
                                                        value="{{ $product->id }}" {{ isset($application) && $application->main_product_category == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <h5 class="font-weight-bolder mt-5  ">Event Contact Person Details:</h5>

                                    <div class="row mt-4">
                                        <div class="col-md-4 col-sm-4 mt-0 mt-sm-0">

                                            <label class="form-control red-label ">Salutation <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">
                                                <select class="form-control" name="event_contact_salutation"
                                                        id="salutation"
                                                        required {{ $isDisabled }}>
                                                    <option
                                                        value="Mr" {{ isset($eventContact) && $eventContact->salutation == 'Mr' ? 'selected' : '' }}>
                                                        Mr
                                                    </option>
                                                    <option
                                                        value="Ms" {{ isset($eventContact) && $eventContact->salutation == 'Ms' ? 'selected' : '' }}>
                                                        Ms
                                                    </option>
                                                    <option
                                                        value="Mrs." {{ isset($eventContact) && $eventContact->salutation == 'Mrs.' ? 'selected' : '' }}>
                                                        Mrs.
                                                    </option>
                                                    <option
                                                        value="Dr" {{ isset($eventContact) && $eventContact->salutation == 'Dr' ? 'selected' : '' }}>
                                                        Dr
                                                    </option>
                                                    <option
                                                        value="Prof" {{ isset($eventContact) && $eventContact->salutation == 'Prof' ? 'selected' : '' }}>
                                                        Prof
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4  mt-sm-0">
                                            <label for="firstName" class="form-label red-label ">First Name <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control mt-2" type="text"
                                                       name="event_contact_first_name" id="event_contact_first_name"
                                                       value="{{ $eventContact->first_name ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-sm-4 mt-4 mt-sm-0">
                                            <label for="lastName" class="form-label red-label ">Last Name <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control mt-2" type="text"
                                                       name="event_contact_last_name"
                                                       id="event_contact_last_name"
                                                       value="{{ $eventContact->last_name ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <label for="designation" class="form-label red-label ">Job Title <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="event_contact_designation" id="designation"
                                                       value="{{ $eventContact->job_title ?? '' }}"
                                                       {{ $isDisabled }}
                                                       required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="contactEmail" class="form-label red-label ">Contact E-Mail <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="email"
                                                       name="event_contact_email"
                                                       id="contactEmail"
                                                       value="{{ $eventContact->email ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="contactPhone" class="form-label red-label ">Contact Phone Number
                                                <span class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">
                                                <select class="form-control" id="choices-languages"
                                                        name="contactPhone_code" style="max-width: 80px;">
                                                    @foreach($countries->unique('code') as $country)
                                                        <option value="{{ $country->code }}"
                                                            {{ isset($eventContact) && explode('-', $eventContact->contact_number)[0] == $country->code || (!isset($application) && $country->code == '91') ? 'selected' : '' }}>
                                                            {{ $country->code }}
                                                        </option>
                                                    @endforeach
                                                </select>


                                                <input class="multisteps-form__input form-control" type="tel"
                                                       name="event_contact_phone"
                                                       id="contactPhone"
                                                       value="{{ isset($eventContact) ? explode('-', $eventContact->contact_number)[1] ?? '' : '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function () {
                                            document.getElementById("step1").addEventListener("submit", function (event) {
                                                let checkboxes = document.querySelectorAll("input[name='type_of_business[]']");
                                                let checked = Array.from(checkboxes).some(checkbox => checkbox.checked);

                                                if (!checked) {
                                                    Swal.fire({
                                                        icon: 'warning',
                                                        title: 'Validation Error',
                                                        text: 'Please select at least one type of business.',
                                                    });
                                                    event.preventDefault(); // Prevent form submission
                                                }
                                            });
                                        });
                                    </script>
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="mt-4 form-label red-label ">Type of Business: <span
                                                    class="red-label">*</span></label>
                                            <div class="row">
                                                @foreach($business as $id => $name)
                                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                   id="type_of_business_{{ $id }}"
                                                                   name="type_of_business[]"
                                                                   value="{{ $name }}" {{ $isDisabled }}
                                                                {{ isset($application) && in_array($name, explode(',', $application->type_of_business)) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                   for="type_of_business_{{ $id }}">{{ $name }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <input type="hidden" name="type_of_business_validation"
                                               id="type_of_business_validation" required>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-6">
                                                    <h5 class="font-weight-bolder ms-1">Billing Details:</h5>

                                                </div>
                                                <div class="col-6">

                                                    <div class="form-check ms-auto">
                                                        <input class="form-check-input" type="checkbox"
                                                               id="copyCompanyDetails"
                                                               onclick="copyDetails()" {{ $isDisabled }}>
                                                        <label class="form-check-label" for="copyCompanyDetails">Same as
                                                            Company Details</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <label for="billingCompany" class="form-label red-label ">Billing Company
                                                <span class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="billing_company"
                                                       id="billing_company"
                                                       value="{{ isset($application) ? $billing->billing_company ?? '' : '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="contactName" class="form-label red-label ">Contact Name <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">
                                                <input class="multisteps-form__input form-control" type="text"

                                                       name="billing_contact_name"
                                                       id="billing_contact_name"
                                                       value="{{ isset($application) ? $billing->contact_name ?? '' : '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="contactEmail" class="form-label red-label ">Billing E-Mail <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="email"

                                                       name="billing_email"
                                                       id="billing_email"
                                                       value="{{ isset($billing) ? $billing->email ?? '' : '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <label for="billingAddress" class="form-label red-label ">Billing Phone
                                                Number
                                                <span class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">
                                                <select class="form-control" id="choices-languages"
                                                        name="billing_phoneCode" style="max-width: 80px;">

                                                    @foreach($countries->unique('code') as $countryName)
                                                        <option
                                                            value="{{ $countryName->code }}"
                                                            {{ isset($eventContact) && explode('-', $billing->phone)[0] == $countryName->code || (!isset($application) && $countryName->code == '91') ? 'selected' : '' }}>
                                                            {{ $countryName->code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <input class="multisteps-form__input form-control" type="tel"
                                                       name="billing_phone"
                                                       id="billing_phone"
                                                       value="{{ isset($billing->phone) ? explode('-', $billing->phone)[1] ?? '' : '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-8 mt-3 mt-sm-0">
                                            <label for="billingAddress" class="form-label red-label ">Billing Address
                                                <span class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="billing_address"
                                                       id="billing_address"
                                                       required {{ $isDisabled }}
                                                       value="{{ isset($application) ? $billing->address ?? '' : '' }}"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <label for="billingCity" class="form-label red-label ">Billing City <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="text"
                                                       id="billing_city"
                                                       name="billing_city"
                                                       value="{{ isset($application) ? $billing->city_id ?? '' : '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="billingPostalCode" class="form-label red-label ">Billing Postal
                                                Code
                                                <span class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">

                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="billing_postal_code"
                                                       id="billing_postal_code"
                                                       value="{{ isset($application) ? $billing->postal_code ?? '' : '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <label for="billingState" class="form-label red-label ">Billing State <span
                                                    class="red-label">*</span></label>
                                            <div class="input-group input-group-dynamic">
                                                @if($isDisabled === 'disabled')
                                                    <input class="multisteps-form__input form-control" type="text"
                                                           name="billing_state" id="billingState"
                                                           value="{{ isset($billing->state) ? $billing->state->name : '' }}"
                                                           required {{ $isDisabled }}/>

                                                @else
                                                    <select class="form-control" id="billing_state" name="billing_state"
                                                            required
                                                            {{ $isDisabled }} onchange="validateBillingState(this)">
                                                        <option value="" disabled selected>Select Billing State</option>
                                                    </select>
                                                @endif

                                                <script>
                                                    document.addEventListener("DOMContentLoaded", function () {
                                                        let billingCountry = document.getElementById("choices-language"); // Billing country dropdown
                                                        let contactCode = document.querySelector("select[name='contactNoCode']"); // Landline country code dropdown
                                                        let contactPhoneCode = document.querySelector("select[name='contactPhone_code']"); // Phone country code dropdown
                                                        let billingPhoneCode = document.querySelector("select[name='billing_phoneCode']"); // Phone country code dropdown
                                                        //console.log(billingCountry);
                                                        console.log(contactCode.value, contactPhoneCode.value, billingPhoneCode.value);

                                                        billingCountry.addEventListener("change", function () {
                                                            let selectedCountryId = billingCountry.value; // Get selected country code

                                                            if (selectedCountryId) {
                                                                $.ajax({
                                                                    url: "/get-country-code",
                                                                    type: "POST",
                                                                    data: {
                                                                        country_id: selectedCountryId,
                                                                        _token: "{{ csrf_token() }}"
                                                                    },
                                                                    success: function (response) {
                                                                        let countryCode = response.code;
                                                                        console.log('Country code:', countryCode);
                                                                        contactCode.value = countryCode; // Update landline country code
                                                                        contactPhoneCode.value = countryCode; // Update phone country code
                                                                        billingPhoneCode.value = countryCode; // Update billing phone country code
                                                                    },
                                                                    error: function () {
                                                                        alert("Error fetching country code. Please try again.");
                                                                    }
                                                                });
                                                            }
                                                        });


                                                        function validateBillingState(select) {
                                                            if (select.value === "") {
                                                                alert("Please select a billing state.");
                                                            }
                                                        }
                                                    });
                                                </script>
                                                <script>
                                                    $(document).ready(function () {
                                                        function loadStates(countryId, selectedStateId = null) {
                                                            if (countryId) {

                                                                $.ajax({
                                                                    url: "{{ route('get.states') }}",
                                                                    type: "POST",
                                                                    data: {
                                                                        country_id: countryId,
                                                                        _token: "{{ csrf_token() }}"
                                                                    },
                                                                    success: function (states) {
                                                                        var stateDropdown = $('select[name="billing_state"]');
                                                                        stateDropdown.empty().append('<option value="" disabled selected>Select State</option>');

                                                                        $.each(states, function (key, state) {
                                                                            stateDropdown.append('<option value="' + state.id + '" ' +
                                                                                (selectedStateId == state.id ? 'selected' : '') + '>' + state.name + '</option>');
                                                                        });
                                                                    },
                                                                    error: function () {
                                                                        alert("Error fetching states. Please try again.");
                                                                    }
                                                                });
                                                            } else {
                                                                $('select[name="billing_state"]').empty().append('<option value="" disabled selected>Select State</option>');
                                                            }
                                                        }

                                                        // Load states on page load if billing_country_id exists
                                                        var selectedCountryId = "{{$application->billing_country_id ?? ''}}";
                                                        var selectedStateId = "{{$application->billingDetail->state_id ?? ''}}"; // Assuming a stored state ID exists

                                                        if (selectedCountryId) {
                                                            // console.log('Selected country ID:', selectedCountryId);
                                                            loadStates(selectedCountryId, selectedStateId);
                                                        }

                                                        // Update states when country dropdown changes
                                                        $('select[name="billing_country"]').change(function () {
                                                            var countryId = $(this).val();
                                                            loadStates(countryId);
                                                        });
                                                    });
                                                </script>

                                                {{--                                                <label for="billingState" class="form-label">Billing State *</label>--}}
                                                {{--                                                <input class="multisteps-form__input form-control" type="text"--}}
                                                {{--                                                       name="billing_state" id="billingState" required--}}
                                                {{--                                                />--}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-md-6 col-sm-4 mt-3 mt-sm-0">
                                            @if( $isDisabled != 'disabled')
                                                <h5 id="gst_label">Upload GST / TAX Certificate</h5>
                                                <div class="input-group input-group-dynamic">
                                                    {{--                                                <label for="gst_application" class="form-label">Upload GST Application</label>--}}
                                                    <input class="form-control" type="file" id="gst_certificate"
                                                           name="gst_certificate"
                                                           accept="application/pdf" {{ $isDisabled }}>

                                                </div>
                                            @endif
                                            @if(isset($application) && $application->certificate)
                                                <div class="mt-3">
                                                    <input id="gst_certificate_uploaded" type="hidden"
                                                           name="gst_certificate_uploaded"
                                                           value="{{ $application->certificate }}">
                                                    <h6>Uploaded GST Certificate:</h6>
                                                    <a href="{{ asset('storage/' . $application->certificate) }}"
                                                       target="_blank">View GST Certificate</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-md-12 d-flex mt-4 pe-5">
                                            @if (isset($application) && ($application->submission_status == 'submitted' || $application->submission_status == 'approved'))
                                                <a href="{{ route('application.show') }}"
                                                   class="btn  bg-gradient-dark ms-auto mb-0 js-btn-next">Next</a>
                                            @else
                                                <button class="btn btn-info ms-auto mb-0 w-fixed h-fixed  js-btn-next"
                                                        type="submit">
                                                    Next
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function copyDetails() {
                if (document.getElementById("copyCompanyDetails").checked) {
                    //console.log("Selected state value by id:", document.getElementById('choices-language').value);
                    //console.log("Selected state value by name:", document.getElementsByName('state')[0].value);
                    // Copy Company Details and store in a variable to use later such as bt id companyName, firstName, lastName, email, contactNo, companyAddress, city, postalCode
                    var companyDetails = {
                        companyName: document.getElementById("companyName").value,
                        firstName: document.getElementById("event_contact_first_name").value,
                        lastName: document.getElementById("event_contact_last_name").value,
                        email: document.getElementById("contactEmail").value,
                        contactNo: document.getElementById("contactPhone").value,
                        companyAddress: document.getElementById("companyAddress").value,
                        city: document.getElementById("city").value,
                        postalCode: document.getElementById("postalCode").value,
                        // state: document.getElementsByName('state')[0].value,
                        state: document.getElementsByName('state')[0].value,
                    };

                    console.log(companyDetails.state);

                    //write js to set the value of document.getElementById("billing_state") to companyDetails.state


                    //console.log(companyDetails);

                    document.getElementById("billing_company").value = companyDetails['companyName'];
                    document.getElementById("billing_contact_name").value = companyDetails['firstName'] + ' ' + companyDetails['lastName'];
                    document.getElementById("billing_email").value = companyDetails['email'];
                    document.getElementById("billing_phone").value = companyDetails['contactNo'];
                    document.getElementById("billing_address").value = companyDetails['companyAddress'];
                    document.getElementById("billing_city").value = companyDetails['city'];
                    document.getElementById("billing_postal_code").value = companyDetails['postalCode'];
                    document.getElementById("billing_state").value = companyDetails['state'];


                } else {
                    document.getElementById("billing_company").value = "";
                    document.getElementById("billing_contact_name").value = "";
                    document.getElementById("billing_email").value = "";
                    document.getElementById("billing_phone").value = "";

                    document.getElementById("billing_address").value = "";
                    document.getElementById("billing_city").value = "";
                    document.getElementById("billing_postal_code").value = "";
                    document.getElementById('billing_state').value = '';
                }
            }

        </script>
@endsection
