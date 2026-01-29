@extends('layouts.application')

@section('title', 'Onboarding Form')
@section('content')
    <main class="mn-inner2">
        <div class="row">
            <div class="col s12">
                <div class="page-title">@yield('title')</div>
            </div>
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">
                        @if ($errors->any())
                            <div>
                                <h2>Errors:</h2>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form id="example-form" method="POST" action="{{ url("$role/application") }}"
                              enctype="multipart/form-data">
                            @csrf
                            @php
                                $isDisabled = isset($application) && $application->submission_status != 'in progress' ? 'disabled' : '';
                            @endphp
                            <div class="row">
                                <div class="col s12">
                                    <ul class="tabs">
                                        <li class="tab col s3"><a href="#" class="active waves-effect waves-teal"> Personal Info</a></li>
                                        <li class="tab col s3"><a href="#" > Product Info </a></li>
                                        <li class="tab col s3"><a href="#"> Terms and Conditions </a></li>
                                        <li class="tab col s3"><a href="#"> Review</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div>

                                <section>
                                    <div class="wizard-content">
                                        <div class="row">
                                            <!-- Billing Country as dropdown -->
                                            <label for="billing_country">Billing Country*</label>
                                            <select name="billing_country" required {{ $isDisabled }}>
                                                @foreach($countries as $countryName)
                                                    <option
                                                        value="{{ $countryName->id }}" {{ isset($application) && $application->billing_country_id == $countryName->id ? 'selected' : '' }}>
                                                        {{ $countryName->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div class="col m6">
                                                <div class="row">
                                                    <!-- GST Compliance and related fields -->
                                                    <div class="input-field col m6 s12">
                                                        <select name="gst_compliance" required {{ $isDisabled }}>
                                                            <option value="" disabled selected>Choose your option
                                                            </option>
                                                            <option
                                                                value="1" {{ isset($application) && $application->gst_compliance == 1 ? 'selected' : '' }}>
                                                                Yes
                                                            </option>
                                                            <option
                                                                value="0" {{ isset($application) && $application->gst_compliance == 0 ? 'selected' : '' }}>
                                                                No
                                                            </option>
                                                        </select>
                                                        <label for="gst_compliance">GST Compliance*</label>
                                                    </div>
                                                    <div class="input-field col m6 s12">
                                                        <label for="gst_no">GST No*</label>
                                                        <input placeholder="22AAAAAXXXXXXX" type="text" name="gst_no"
                                                               value="{{ $application->gst_no ?? '' }}" required
                                                               pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}" {{ $isDisabled }}><br>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col m6">
                                                <div class="row">
                                                    <div class="input-field col m6 s12">
                                                        <label for="pan_no">PAN No*</label>
                                                        <input placeholder="XYZPK8200S" type="text" name="pan_no"
                                                               value="{{ $application->pan_no ?? '' }}" required
                                                               pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" {{ $isDisabled }}><br>
                                                    </div>
                                                    <div class="input-field col m6 s12">
                                                        <label for="tan_no">TAN No*</label>
                                                        <input placeholder="ABCD12345X" type="text" name="tan_no"
                                                               value="{{ $application->tan_no ?? '' }}" required
                                                               pattern="[A-Z]{4}[0-9]{5}[A-Z]{1}" {{ $isDisabled }}><br>

                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col m6">
                                                <div class="row">
                                                    <div class="input-field col s12">
                                                        <label for="company_name">Company Name*</label>
                                                        <input type="text" name="company_name"
                                                               value="{{ $application->company_name ?? '' }}"
                                                               required {{ $isDisabled }}>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col m6">
                                                <div class="row">
                                                    <div class="input-field col s12">
                                                        <label for="address textarea1">Company Address*</label>
                                                        <input type="text" id="textarea1" name="address"
                                                               required {{ $isDisabled }} length="120"
                                                               value="{{ $application->address ?? '' }}">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col m6">
                                                <div class="row">
                                                    <div class="input-field col m6 s12">
                                                        <label for="postal_code">Postal Code*</label>
                                                        <input type="text" name="postal_code"
                                                               value="{{ $application->postal_code ?? '' }}"
                                                               required {{ $isDisabled }}>
                                                    </div>

                                                    <div class="input-field col m6 s12">
                                                        <label for="city">City*</label>
                                                        <input type="text" name="city"
                                                               value="{{ $application->city_id ?? '' }}"
                                                               required {{ $isDisabled }}>
                                                    </div>
                                                    <div class="input-field col m6 s12">
                                                        <label for="company_email">Company Contact/Landline No*</label>
                                                        <input type="text" name="company_no"
                                                               value="{{ $application->landline ?? '' }}"
                                                               required {{ $isDisabled }}>
                                                    </div>
                                                    <div class="input-field col m6 s12">
                                                        <label for="company_email">Company E-Mail*</label>
                                                        <input type="email" name="company_email"
                                                               value="{{ $application->company_email ?? '' }}"
                                                               required {{ $isDisabled }}>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col m6">
                                                <div class="row">
                                                    <div class="input-field col m6 s12">
                                                        <label for="country">Country*</label>
                                                        <select name="country" required {{ $isDisabled }}>
                                                            @foreach($countries as $countryName)
                                                                <option
                                                                    value="{{ $countryName->id }}" {{ isset($application) && $application->country_id == $countryName->id ? 'selected' : '' }}>
                                                                    {{ $countryName->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="input-field col m6 s12">
                                                        <label for="state">State*</label>
                                                        <select name="state" required {{ $isDisabled }}>
                                                            @foreach($states as $stateName)
                                                                <option
                                                                    value="{{ $stateName->id }}" {{ isset($application) && $application->state_id == $stateName->id ? 'selected' : '' }}>
                                                                    {{ $stateName->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="input-field col m6 s12">
                                                        <label for="website">Website</label>
                                                        <input type="url" name="website"
                                                               value="{{ $application->website ?? '' }}" {{ $isDisabled }}><br>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col m12">
                                                <div class="row">
                                                    <div class="input-field col m6 s12">
                                                        <!-- Main Product Category -->
                                                        <label for="main_product_category">Main Product
                                                            Category*</label>
                                                        <select name="main_product_category" required {{ $isDisabled }}>
                                                            @foreach($productCategories as $product)
                                                                <option
                                                                    value="{{ $product->id }}" {{ isset($application) && $application->main_product_category == $product->id ? 'selected' : '' }}>
                                                                    {{ $product->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <h4 class="font-weight-bold">Event Contact Person Details:</h4>
                                                <div class="col m6">
                                                    <div class="row">
                                                        <div class="input-field col m6 s12">
                                                            <label for="event_contact_salutation">Salutation*</label>
                                                            <select name="event_contact_salutation"
                                                                    required {{ $isDisabled }}>
                                                                <option
                                                                    value="Mr." {{ isset($eventContact) && $eventContact->salutation == 'Mr.' ? 'selected' : '' }}>
                                                                    Mr.
                                                                </option>
                                                                <option
                                                                    value="Ms." {{ isset($eventContact) && $eventContact->salutation == 'Ms.' ? 'selected' : '' }}>
                                                                    Ms.
                                                                </option>
                                                                <option
                                                                    value="Mrs." {{ isset($eventContact) && $eventContact->salutation == 'Mrs.' ? 'selected' : '' }}>
                                                                    Mrs.
                                                                </option>
                                                                <option
                                                                    value="Dr." {{ isset($eventContact) && $eventContact->salutation == 'Dr.' ? 'selected' : '' }}>
                                                                    Dr.
                                                                </option>
                                                            </select>
                                                        </div>
                                                        <div class="input-field col m6 s12">
                                                            <label for="event_contact_first_name">First Name*</label>
                                                            <input type="text" name="event_contact_first_name"
                                                                   value="{{ $eventContact->first_name ?? '' }}"
                                                                   required {{ $isDisabled }}>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col m6">
                                                    <div class="row">
                                                        <div class="input-field col m6 s12">
                                                            <label for="event_contact_last_name">Last Name*</label>
                                                            <input type="text" name="event_contact_last_name"
                                                                   value="{{ $eventContact->last_name ?? '' }}"
                                                                   required {{ $isDisabled }}>
                                                        </div>
                                                        <div class="input-field col m6 s12">
                                                            <label for="event_contact_last_name">Designation*</label>
                                                            <input type="text" name="event_contact_designation"
                                                                   value="{{ $eventContact->job_title ?? '' }}"
                                                                   required {{ $isDisabled }}>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="event_id" value="{{ request()->input('event_id') ?? '1' }}" {{ $isDisabled }}>
                                                <div class="col m6">
                                                    <div class="row">
                                                        <div class="input-field col m6 s12">
                                                            <label for="event_contact_email">E-Mail*</label>
                                                            <input type="email" name="event_contact_email"
                                                                   value="{{ $eventContact->email ?? '' }}"
                                                                   required {{ $isDisabled }}>
                                                        </div>
                                                        <div class="input-field col m6 s12">
                                                            <label for="event_contact_phone">Phone Number*</label>
                                                            <input type="text" name="event_contact_phone"
                                                                   value="{{ $eventContact->contact_number ?? '' }}"
                                                                   required {{ $isDisabled }}><br>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col m6">
                                                    <div class="row">
                                                        {{--                                        <div class="input-field col m6 s12">--}}
                                                        {{--                                            <label for="event_contact_designation">Designation*</label>--}}
                                                        {{--                                            <input type="text" name="event_contact_designation"--}}
                                                        {{--                                                   value="{{ $eventContact->designation ?? '' }}"--}}
                                                        {{--                                                   required {{ $isDisabled }}>--}}
                                                        {{--                                        </div>--}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col m12">
                                                <h4 class="font-weight-bold">Type of Business:</h4>
                                                <div class="row">
                                                    <div class="checkbox-group"
                                                         style="display: flex; flex-wrap: wrap; gap: 16px;">
                                                        @foreach($business as $id => $name)
                                                            <div style="flex: 1 0 22%; box-sizing: border-box;">
                                                                <input type="checkbox"
                                                                       class="filled-in"
                                                                       id="type_of_business_{{ $id }}"
                                                                       name="type_of_business[]"
                                                                       value="{{ $name }}"
                                                                    {{ $isDisabled }}
                                                                    {{ isset($application) && in_array($name, explode(',', $application->type_of_business)) ? 'checked' : '' }}>
                                                                <label
                                                                    for="type_of_business_{{ $id }}">{{ $name }}</label>
                                                            </div>
                                                        @endforeach
                                                        <!-- Special "Other" checkbox -->
                                                        {{--                                                        <div style="flex: 1 0 22%; box-sizing: border-box;">--}}
                                                        {{--                                                            <input type="checkbox"--}}
                                                        {{--                                                                   class="filled-in"--}}
                                                        {{--                                                                   id="type_of_business_other"--}}
                                                        {{--                                                                   name="type_of_business[]"--}}
                                                        {{--                                                                   value="Other">--}}
                                                        {{--                                                            <label for="type_of_business_other">Other</label>--}}
                                                        {{--                                                        </div>--}}

                                                    </div>
                                                    <!-- Textbox for "Other" input -->
                                                    <div id="other-business-input"
                                                         style="margin-top: 16px; display: none;">
                                                        <label for="other_business_name">Please specify:</label>
                                                        <input type="text" id="other_business_name"
                                                               name="other_business_name" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Billing Details -->
                                            <div class="col m12">
                                                <h4 class="font-weight-bold">Billing Details:</h4>
                                                <div class="col m6">
                                                    <div class="row">
                                                        <div class="input-field col m6 s12">

                                                            <label for="billing_company">Billing Company*</label>
                                                            <input type="text" name="billing_company"
                                                                   value="{{ $billing->billing_company ?? '' }}"
                                                                   required {{ $isDisabled }}>
                                                        </div>
                                                        <div class="input-field col m6 s12">
                                                            <label for="billing_contact_name">Contact Name*</label>
                                                            <input type="text" name="billing_contact_name"
                                                                   value="{{ $billing->contact_name ?? '' }}"
                                                                   required {{ $isDisabled }}>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col m6">
                                                    <div class="row">
                                                        <div class="input-field col m6 s12">
                                                            <label for="billing_email">E-Mail*</label>
                                                            <input type="email" name="billing_email"
                                                                   value="{{ $billing->email ?? '' }}"
                                                                   required {{ $isDisabled }}>
                                                        </div>
                                                        <div class="input-field col m6 s12">
                                                            <label for="billing_phone">Phone Number*</label>
                                                            <input type="text" name="billing_phone"
                                                                   value="{{ $billing->phone ?? '' }}"
                                                                   required {{ $isDisabled }}>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="input-field col m12 s12">
                                                    <label for="billing_address">Billing Address*</label>
                                                    <input type="text" name="billing_address"
                                                           required {{ $isDisabled }}
                                                           value="{{ $billing->address ?? '' }}"
                                                    >
                                                </div>
                                                <div class="col m12">
                                                    <div class="row">
                                                        <div class="input-field col m4 s12">
                                                            <label for="billing_city">Billing City*</label>
                                                            <input type="text" name="billing_city"
                                                                   value="{{ $billing->city_id ?? '' }}"
                                                                   required {{ $isDisabled }}>
                                                        </div>
                                                        <div class="input-field col m4 s12">

                                                            <label for="billing_postal_code">Billing Postal
                                                                Code*</label>
                                                            <input type="text" name="billing_postal_code"
                                                                   value="{{ $billing->postal_code ?? '' }}"
                                                                   required {{ $isDisabled }}>
                                                        </div>
                                                        <div class="input-field col m4 s12">

                                                            <label for="billing_state">Billing State*</label>
                                                            <select name="billing_state" required {{ $isDisabled }}>
                                                                @foreach($states as $stateName)
                                                                    <option
                                                                        value="{{ $stateName->id }}" {{ isset($billing) && $billing->state_id == $stateName->id ? 'selected' : '' }}>
                                                                        {{ $stateName->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col m4"></div>
                                                <div class="col m4 align-center">
                                                    <div class="form-group">
                                                        <div class="custom-file-input-wrapper">
                                                            <input type="file" id="gst_certificate" name="gst_certificate" accept="application/pdf"
                                                                   class="custom-file-input"
                                                                {{ !empty($application->certificate) ? '' : 'required' }}>
                                                            <label for="gst_certificate" class="custom-label">
                                                                {{ !empty($application->certificate) ? '' : 'Upload GST Certificate*' }}
                                                            </label>
                                                            @if (!empty($application->certificate))
                                                                <p>Current file: {{ $application->certificate }}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col m4"></div>


                                                @if (isset($application) && ($application->submission_status == 'submitted' || $application->submission_status == 'approved'))
                                                    <a href="{{ route('application.show') }}" class="btn">Next</a>
                                                @else
                                                    <button type="submit" {{ $isDisabled }}>Submit Application</button>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </section>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection
