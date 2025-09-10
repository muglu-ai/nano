@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')
    <div class="container-fluid py-2">
        <div class="row min-vh-80">
            <div class="col-lg-8 col-md-10 col-12 m-auto">
                <h3 class="mt-3 mb-0 text-center">Add new Product</h3>
                <p class="lead font-weight-normal opacity-8 mb-7 text-center">This information will let us know more
                    about you.</p>
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
                        </div>
                    </div>


                    <div class="card-body">
                        <form class="multisteps-form__form" method="POST" action="{{ route('application.exhibitor.submit') }}">
                            @csrf
                            @php
                                $isDisabled = isset($application) && $application->submission_status != 'in progress' ? 'disabled' : '';

                                @endphp
                            <!--single form panel-->
                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                 data-animation="FadeIn">
                                {{--                                <h5 class="font-weight-bolder">Product Information</h5>--}}

{{--                                {{$isDisabled}}--}}
                                <div class="multisteps-form__content">
                                    <div class="row mt-3">
                                        <div class="col-12 col-sm-6">
                                            <label class="form-control ms-0">Billing Country *</label>
                                            <select class="form-control" name="billing_country" id="choices-language" size=2 required {{ $isDisabled }}>
                                                @foreach($countries as $countryName)
                                                    <option
                                                        value="{{ $countryName->id }}" {{ isset($application) && $application->billing_country_id == $countryName->id ? 'selected' : '' }}>
                                                        {{ $countryName->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-6">
                                            <label class="form-control ms-0">GST Compliance *</label>
                                            <select class="form-control" name="gst_compliance" id="choices-sizes" required onfocus="focused(this)" onfocusout="defocused(this)" {{ $isDisabled }}>
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
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="gstNo" class="form-label">GST No</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       id="gst_no" onfocus="focused(this)" onfocusout="defocused(this)" required
                                                       value="{{ $application->gst_no ?? '' }}"
                                                       placeholder="22AAAAAXXXXXXX"
                                                       pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}"
                                                />
                                                <small>Error message</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="pan_no" class="form-label">PAN No</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       placeholder="XYZPK8200S"
                                                       value="{{ $application->pan_no ?? '' }}"
                                                       pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"
                                                       id="pan_no" required
                                                    {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="tan_no" class="form-label">TAN No</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       placeholder="ABCD12345X"
                                                       value="{{ $application->tan_no ?? '' }}"
                                                       pattern="[A-Z]{4}[0-9]{5}[A-Z]{1}" {{ $isDisabled }}
                                                       id="tan_no"/>
                                            </div>
                                        </div>
                                    </div>

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
                                                <label for="state" class="form-label">State *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="state" id="state" required/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactNo" class="form-label">Company Contact/Landline No
                                                    *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="company_no" id="contactNo" value="{{ $application->landline ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="email" class="form-label">Company E-Mail *</label>
                                                <input class="multisteps-form__input form-control" type="email"
                                                       name="company_email" id="email" value="{{ $application->company_email ?? '' }}"
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
                                    <div class="row mt-5 mb-5">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-control ms-0">Main Product Category *</label>
                                        <select class="form-control" name="main_product_category" id="products-list" size="5" required {{ $isDisabled }}>
                                            @foreach($productCategories as $product)
                                                <option value="{{ $product->id }}" {{ isset($application) && $application->main_product_category == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    </div>
                                    <h5 class="font-weight-bolder mt-5">Event Contact Person Details:</h5>

                                    <div class="row mt-4">
                                        <div class="col-12 col-sm-4 d-flex align-items-center">
{{--                                            <label class="form-control ms-0 me-2">Salutation *</label>--}}
                                            <select class="form-control" name="event_contact_salutation" id="salutation" required {{ $isDisabled }}>
                                                <option value="Mr" {{ isset($eventContact) && $eventContact->salutation == 'Mr' ? 'selected' : '' }}>Mr</option>
                                                <option value="Ms" {{ isset($eventContact) && $eventContact->salutation == 'Ms' ? 'selected' : '' }}>Ms</option>
                                                <option value="Mrs." {{ isset($eventContact) && $eventContact->salutation == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
                                                <option value="Dr" {{ isset($eventContact) && $eventContact->salutation == 'Dr' ? 'selected' : '' }}>Dr</option>
                                                <option value="Prof" {{ isset($eventContact) && $eventContact->salutation == 'Prof' ? 'selected' : '' }}>Prof</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="firstName" class="form-label">First Name *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="event_contact_first_name" id="firstName" value="{{ $eventContact->first_name ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="lastName" class="form-label">Last Name *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="event_contact_last_name"
                                                       value="{{ $eventContact->last_name ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="designation" class="form-label">Designation *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="designation" id="designation" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactEmail" class="form-label">E-Mail *</label>
                                                <input class="multisteps-form__input form-control" type="email" name="event_contact_designation"
                                                       value="{{ $eventContact->job_title ?? '' }}"
                                                       required {{ $isDisabled }}/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactPhone" class="form-label">Phone Number *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="event_contact_phone"
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
                                                    <div class="col-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="type_of_business_{{ $id }}" name="type_of_business[]" value="{{ $name }}" {{ $isDisabled }} {{ isset($application) && in_array($name, explode(',', $application->type_of_business)) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="type_of_business_{{ $id }}">{{ $name }}</label>
                                                        </div>
                                                    </div>
                                                    @if(($loop->index + 1) % 4 == 0)
                                            </div><div class="row">
                                                @endif
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
                                                <label for="contactEmail" class="form-label">E-Mail *</label>
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
                                                <label for="billingAddress" class="form-label">Phone Number *</label>
                                                <input class="multisteps-form__input form-control" type="number"
                                                       name="billing_phone"
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
                                                <label for="billingPostalCode" class="form-label">Billing Postal Code *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="billing_postal_code"
                                                       value="{{ $billing->postal_code ?? '' }}"
                                                       required {{ $isDisabled }}
                                                />
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingState" class="form-label">Billing State *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="billing_state" id="billingState"required
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <h5>Upload GST Certificate</h5>
                                            <div class="input-group input-group-dynamic">
{{--                                                <label for="gst_application" class="form-label">Upload GST Application</label>--}}
                                                <input type="file" class="form-control" id="gst_application" name="gst_application" accept="application/pdf" {{ $isDisabled }}>
                                            </div>
                                        </div>
                                    </div>



{{--                                    <div class="row">--}}
{{--                                        <div class="col-sm-6">--}}
{{--                                            <label class="mt-4">Description</label>--}}
{{--                                            <p class="form-text text-muted text-xs ms-1 d-inline">--}}
{{--                                                (optional)--}}
{{--                                            </p>--}}
{{--                                            <div id="edit-deschiption" class="h-50">--}}
{{--                                                <p>Some initial <strong>bold</strong> text</p>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-sm-6 mt-sm-3 mt-5">--}}
{{--                                            <label class="form-control ms-0">Category</label>--}}
{{--                                            <select class="form-control" name="choices-category" id="choices-category">--}}
{{--                                                <option value="Choice 1" selected="">Clothing</option>--}}
{{--                                                <option value="Choice 2">Real Estate</option>--}}
{{--                                                <option value="Choice 3">Electronics</option>--}}
{{--                                                <option value="Choice 4">Furniture</option>--}}
{{--                                                <option value="Choice 5">Others</option>--}}
{{--                                            </select>--}}
{{--                                            <label class="form-control ms-0">Sizes</label>--}}
{{--                                            <select class="form-control" name="choices-sizes" id="choices-sizes">--}}
{{--                                                <option value="Choice 1" selected="">Medium</option>--}}
{{--                                                <option value="Choice 2">Small</option>--}}
{{--                                                <option value="Choice 3">Large</option>--}}
{{--                                                <option value="Choice 4">Extra Large</option>--}}
{{--                                                <option value="Choice 5">Extra Small</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="button-row d-flex mt-4">
                                        <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next" type="submit">
                                            Next
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!--single form panel-->
{{--                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white" data-animation="FadeIn">--}}
{{--                                <h5 class="font-weight-bolder">Media</h5>--}}
{{--                                <div class="multisteps-form__content">--}}
{{--                                    <div class="row mt-3">--}}
{{--                                        <div class="col-12">--}}
{{--                                            <label class="form-control mb-0">Product images</label>--}}
{{--                                            <div action="/file-upload" class="form-control border dropzone"--}}
{{--                                                 id="productImg"></div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="button-row d-flex mt-4">--}}
{{--                                        <button class="btn bg-gradient-light mb-0 js-btn-prev" type="button"--}}
{{--                                                title="Prev">Prev--}}
{{--                                        </button>--}}
{{--                                        <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next" type="button"--}}
{{--                                                title="Next">Next--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <!--single form panel-->
{{--                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white" data-animation="FadeIn">--}}
{{--                                <h5 class="font-weight-bolder">Socials</h5>--}}
{{--                                <div class="multisteps-form__content">--}}
{{--                                    <div class="row mt-3">--}}
{{--                                        <div class="col-12">--}}
{{--                                            <div class="input-group input-group-dynamic">--}}
{{--                                                <label class="form-label">Shoppify Handle</label>--}}
{{--                                                <input class="multisteps-form__input form-control" type="text"/>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-12 mt-3">--}}
{{--                                            <div class="input-group input-group-dynamic">--}}
{{--                                                <label class="form-label">Facebook Account</label>--}}
{{--                                                <input class="multisteps-form__input form-control" type="text"/>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-12 mt-3">--}}
{{--                                            <div class="input-group input-group-dynamic">--}}
{{--                                                <label class="form-label">Instagram Account</label>--}}
{{--                                                <input class="multisteps-form__input form-control" type="text"/>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="row">--}}
{{--                                        <div class="button-row d-flex mt-4 col-12">--}}
{{--                                            <button class="btn bg-gradient-light mb-0 js-btn-prev" type="button"--}}
{{--                                                    title="Prev">Prev--}}
{{--                                            </button>--}}
{{--                                            <button class="btn bg-gradient-dark ms-auto mb-0 js-btn-next" type="button"--}}
{{--                                                    title="Next">Next--}}
{{--                                            </button>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <!--single form panel-->
{{--                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white h-100"--}}
{{--                                 data-animation="FadeIn">--}}
{{--                                <h5 class="font-weight-bolder">Pricing</h5>--}}
{{--                                <div class="multisteps-form__content mt-3">--}}
{{--                                    <div class="row">--}}
{{--                                        <div class="col-3">--}}
{{--                                            <div class="input-group input-group-dynamic">--}}
{{--                                                <label class="form-label">Price</label>--}}
{{--                                                <input type="email" class="form-control w-100" id="exampleInputEmail1"--}}
{{--                                                       aria-describedby="emailHelp">--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-4">--}}
{{--                                            <select class="form-control" name="choices-sizes" id="choices-currency">--}}
{{--                                                <option value="Choice 1" selected="">USD</option>--}}
{{--                                                <option value="Choice 2">EUR</option>--}}
{{--                                                <option value="Choice 3">GBP</option>--}}
{{--                                                <option value="Choice 4">CNY</option>--}}
{{--                                                <option value="Choice 5">INR</option>--}}
{{--                                                <option value="Choice 6">BTC</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-5">--}}
{{--                                            <div class="input-group input-group-dynamic">--}}
{{--                                                <label class="form-label">SKU</label>--}}
{{--                                                <input class="multisteps-form__input form-control" type="text"/>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="row">--}}
{{--                                        <div class="col-12">--}}
{{--                                            <label class="mt-4 form-label">Tags</label>--}}
{{--                                            <select class="form-control" name="choices-tags" id="choices-tags" multiple>--}}
{{--                                                <option value="Choice 1" selected>In Stock</option>--}}
{{--                                                <option value="Choice 2">Out of Stock</option>--}}
{{--                                                <option value="Choice 3">Sale</option>--}}
{{--                                                <option value="Choice 4">Black Friday</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="button-row d-flex mt-0 mt-md-4">--}}
{{--                                        <button class="btn bg-gradient-light mb-0 js-btn-prev" type="button"--}}
{{--                                                title="Prev">Prev--}}
{{--                                        </button>--}}
{{--                                        <button class="btn bg-gradient-dark ms-auto mb-0" type="button" title="Send">--}}
{{--                                            Send--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection
