@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')
    <style>
        @media (min-width: 500px) {
            .progress-bar2 {
                display: none !important;
            }
        }
    </style>
    <div class="container-fluid py-2">
        <div class="row min-vh-80 mt-5">
            <div class="col-lg-8 col-md-10 col-12 m-auto">
{{--                <h3 class="mt-3 mb-0 text-center">Add new Product</h3>--}}
{{--                <p class="lead font-weight-normal opacity-8 mb-7 text-center">This information will let us know more--}}
{{--                    about you.</p>--}}
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
                        <form class="multisteps-form__form" method="POST" enctype="multipart/form-data"
                              action="{{ route('application.exhibitor.submit') }}">
                            @csrf
                            @php
                                $isDisabled = isset($application) && $application->submission_status != 'in progress' ? 'disabled' : '';

                            @endphp

                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                 data-animation="FadeIn">
                                <div class="multisteps-form__content">
                                    <div class="row mt-3">
                                        <div class="col-12 col-sm-6">
                                            <label class="form-control ms-0">Billing Country *</label>
                                            <select class="form-control" name="billing_country" id="choices-language"
                                                    size=2 required {{ $isDisabled }}>
                                                <option value="" disabled selected>Select Country</option>
                                                @foreach($countries as $countryName)
                                                    <option
                                                        value="{{ $countryName->id }}" {{ isset($application) && $application->billing_country_id == $countryName->id ? 'selected' : '' }}>
                                                        {{ $countryName->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="form-control ms-0">GST Compliance / Tax Certificate *</label>
                                            <select class="form-control" name="gst_compliance" id="choices-sizes"
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
                                    <input type="hidden" name="event_id" value="1">
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="gstNo" class="form-label">GST No</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="gst_no" id="gst_no"
{{--                                                       placeholder="22AAAAAXXXXXXX"--}}
                                                       pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}"
                                                       value="{{ $application->gst_no ?? '' }}" required
                                                       {{ $isDisabled }}
                                                />
                                                <small>Error message</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="pan_no" class="form-label">PAN No</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       placeholder="XYZPK8200S"
{{--                                                       pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"--}}
                                                       name="pan_no" id="pan_no"
                                                       value="{{ $application->pan_no ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="tan_no" class="form-label">TAN No</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="tan_no" id="tan_no"
                                                       placeholder="ABCD12345X"
                                                       value="{{ $application->tan_no ?? '' }}"
                                                       pattern="[A-Z]{4}[0-9]{5}[A-Z]{1}"
                                                    {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        function toggleGstNoRequired(select) {
                                            const gstNoInput = document.getElementById('gst_no');
                                            if (select.value === '1') {
                                                gstNoInput.setAttribute('required', 'required');
                                            } else {
                                                gstNoInput.removeAttribute('required');
                                            }
                                        }

                                        // Call the function on page load to set the initial state
                                        document.addEventListener('DOMContentLoaded', function() {
                                            toggleGstNoRequired(document.getElementById('gst_compliance'));
                                        });
                                    </script>

                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-6">
                                            <div class="input-group input-group-dynamic">
                                                <label for="exampleFormControlInput1" class="form-label">Company Name
                                                    *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="company_name" id="companyName"
                                                       value="{{ $application->company_name ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="exampleFormControlInput1" class="form-label">Company Address
                                                    *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="address"
                                                       required {{ $isDisabled }} length="120"
                                                       value="{{ $application->address ?? '' }}"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="postalCode" class="form-label">Postal Code *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="postal_code"
                                                       value="{{ $application->postal_code ?? '' }}"
                                                       required {{ $isDisabled }}
                                                       id="postalCode"/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="city" class="form-label">City *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       id="city"
                                                       name="city"
                                                       value="{{ $application->city_id ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <select class="form-control" name="state" id="choices-language" required
                                                        {{ $isDisabled }} onchange="validateState(this)">
                                                    <option value="" disabled selected>Select State</option>
                                                </select>
                                                <script>
                                                    function validateState(select) {
                                                        if (select.value === "") {
                                                            alert("Please select a billing state.");
                                                        }
                                                    }
                                                </script>
                                                <script>
                                                    $(document).ready(function () {
                                                        $('select[name="billing_country"]').change(function () { // Select country dropdown by name
                                                            var countryId = $(this).val();

                                                            if (countryId) {
                                                                $.ajax({
                                                                    url: "{{ route('get.states') }}",
                                                                    type: "POST",
                                                                    data: {
                                                                        country_id: countryId,
                                                                        _token: "{{ csrf_token() }}"
                                                                    },
                                                                    success: function (states) {
                                                                        $('select[name="state"]').empty().append('<option value="" disabled selected>Select State</option>');

                                                                        $.each(states, function (key, state) {
                                                                            $('select[name="state"]').append('<option value="' + state.id + '">' + state.name + '</option>');
                                                                        });
                                                                    },
                                                                    error: function () {
                                                                        alert("Error fetching states. Please try again.");
                                                                    }
                                                                });
                                                            } else {
                                                                $('select[name="state"]').empty().append('<option value="" disabled selected>Select State</option>');
                                                            }
                                                        });
                                                    });
                                                </script>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactNo" class="form-label">Company Contact/Landline No
                                                    *</label>
                                                <input class="multisteps-form__input form-control" type="tel"
                                                       name="company_no" id="contactNo"
                                                       pattern="\+\d{1,3}[0-9]{10}"
                                                       placeholder="+919876543210"
                                                       value="{{ $application->landline ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="email" class="form-label">Company E-Mail *</label>
                                                <input class="multisteps-form__input form-control" type="email"
                                                       name="company_email" id="email"
                                                       value="{{ $application->company_email ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="website" class="form-label">Website</label>
                                                <input class="multisteps-form__input form-control" type="url"
                                                       name="website"
                                                       value="{{ $application->website ?? '' }}" {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                    </div>
                                    {{--                                    @dd($application->main_product_category)--}}
                                    <div class="row mt-5 mb-5">
                                        <div class="col-12 col-sm-6">
                                            <label class="form-control ms-0">Main Product Category *</label>
                                            <select class="form-control" name="main_product_category" id="products-list"
                                                    size="5" required {{ $isDisabled }}>
                                                @foreach($productCategories as $product)
                                                    <option
                                                        value="{{ $product->id }}" {{ isset($application) && $application->main_product_category == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <h5 class="font-weight-bolder mt-5">Event Contact Person Details:</h5>

                                    <div class="row mt-4">
                                        <div class="col-12 col-sm-4 d-flex align-items-center">
                                            {{--                                            <label class="form-control ms-0 me-2">Salutation *</label>--}}
                                            <select class="form-control" name="event_contact_salutation" id="salutation"
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
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="firstName" class="form-label">First Name *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="event_contact_first_name" id="firstName"
                                                       value="{{ $eventContact->first_name ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="lastName" class="form-label">Last Name *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="event_contact_last_name"
                                                       value="{{ $eventContact->last_name ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="designation" class="form-label">Designation *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="event_contact_designation" id="designation"
                                                       value="{{ $eventContact->job_title ?? '' }}"
                                                       required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactEmail" class="form-label">Contact E-Mail *</label>
                                                <input class="multisteps-form__input form-control" type="email"
                                                       name="event_contact_email"
                                                       value="{{ $eventContact->email ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactPhone" class="form-label">Contact Phone Number *</label>
                                                <input class="multisteps-form__input form-control" type="tel"
                                                       name="event_contact_phone"
                                                       pattern="\+\d{1,3}[0-9]{10}"
                                                       placeholder="+919876543210"
                                                       value="{{ $eventContact->contact_number ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="mt-4 form-label">Type of Business:</label>
                                            <div class="row">
                                                @foreach($business as $id => $name)
                                                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox"
                                                                   id="type_of_business_{{ $id }}"
                                                                   name="type_of_business[]"
                                                                   value="{{ $name }}" {{ $isDisabled }} {{ isset($application) && in_array($name, explode(',', $application->type_of_business)) ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                   for="type_of_business_{{ $id }}">{{ $name }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <h5 class="font-weight-bolder">Billing Details:</h5>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingCompany" class="form-label">Billing Company *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="billing_company"
                                                       value="{{ $billing->billing_company ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactName" class="form-label">Contact Name *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="billing_contact_name"
                                                       value="{{ $billing->contact_name ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactEmail" class="form-label">Billing E-Mail *</label>
                                                <input class="multisteps-form__input form-control" type="email"
                                                       name="billing_email"
                                                       value="{{ $billing->email ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingAddress" class="form-label">Billing Phone Number *</label>
                                                <input class="multisteps-form__input form-control" type="tel"
                                                       name="billing_phone"
                                                       pattern="\+\d{1,3}[0-9]{10}"
                                                       placeholder="+919876543210"
                                                       value="{{ $billing->phone ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-8 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingAddress" class="form-label">Billing Address *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="billing_address"
                                                       required {{ $isDisabled }}
                                                       value="{{ $billing->address ?? '' }}"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingCity" class="form-label">Billing City *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="billing_city"
                                                       value="{{ $billing->city_id ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingPostalCode" class="form-label">Billing Postal Code
                                                    *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="billing_postal_code"
                                                       value="{{ $billing->postal_code ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            {{--                                            <label for="billingState" class="form-label">Billing State *</label>--}}
                                            <div class="input-group input-group-dynamic">
                                                <select class="form-control" name="billing_state" required
                                                        {{ $isDisabled }} onchange="validateBillingState(this)">
                                                    <option value="" disabled selected>Select Billing State</option>
                                                    @foreach($states as $stateName)
                                                        <option class="form-control"
                                                                value="{{ $stateName->id }}" {{ isset($billing) && $billing->state_id == $stateName->id ? 'selected' : '' }}>
                                                            {{ $stateName->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <script>
                                                    function validateBillingState(select) {
                                                        if (select.value === "") {
                                                            alert("Please select a billing state.");
                                                        }
                                                    }
                                                </script>
                                                {{--                                                <label for="billingState" class="form-label">Billing State *</label>--}}
                                                {{--                                                <input class="multisteps-form__input form-control" type="text"--}}
                                                {{--                                                       name="billing_state" id="billingState" required--}}
                                                {{--                                                />--}}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <h5>Upload GST / TAX Certificate * </h5>
                                            <div class="input-group input-group-dynamic">
                                                {{--                                                <label for="gst_application" class="form-label">Upload GST Application</label>--}}
                                                <input class="form-control" type="file" id="gst_certificate"
                                                       name="gst_certificate"
                                                       accept="application/pdf" {{ $isDisabled }}>

                                            </div>
                                            @if(isset($application) && $application->certificate)
                                                <div class="mt-3">
                                                    <h6>Uploaded GST Certificate:</h6>
                                                    <a href="{{ asset('storage/' . $application->certificate) }}"
                                                       target="_blank">View GST Certificate</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="button-row d-flex mt-4">
                                        @if (isset($application) && ($application->submission_status == 'submitted' || $application->submission_status == 'approved'))
                                            <a href="{{ route('application.show') }}"
                                               class="btn bg-gradient-dark ms-auto mb-0 js-btn-next">Next</a>
                                        @else
                                            <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next" type="submit">
                                                Next
                                            </button>
                                        @endif
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
