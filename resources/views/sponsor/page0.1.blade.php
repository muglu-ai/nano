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
                        <form class="multisteps-form__form">
                            <!--single form panel-->
                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                 data-animation="FadeIn">
                                {{--                                <h5 class="font-weight-bolder">Product Information</h5>--}}
                                <div class="multisteps-form__content">
                                    <div class="row mt-3">
                                        <div class="col-12 col-sm-6">
                                            <label class="form-control ms-0">Billing Country *</label>
                                            <select class="form-control" name="billing_country" id="choices-language" size=2 required>
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
                                            <select class="form-control" name="choices-sizes" id="choices-sizes" required onfocus="focused(this)" onfocusout="defocused(this)">
                                                <option value="Choice 1" selected="">Yes</option>
                                                <option value="Choice 2">No</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="gstNo" class="form-label">GST No</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       id="gstNo" onfocus="focused(this)" onfocusout="defocused(this)" required/>
                                                <small>Error message</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="panNo" class="form-label">PAN No</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       id="panNo" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="tanNo" class="form-label">TAN No</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       id="tanNo"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-6">
                                            <div class="input-group input-group-dynamic">
                                                <label for="exampleFormControlInput1" class="form-label">Company Name
                                                    *</label>
                                                <input class="multisteps-form__input form-control" type="text" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="exampleFormControlInput1" class="form-label">Company Address
                                                    *</label>
                                                <input class="multisteps-form__input form-control" type="text" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="postalCode" class="form-label">Postal Code *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="postal_code" id="postalCode" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="city" class="form-label">City *</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="city" id="city" required/>
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
                                                       name="contact_no" id="contactNo" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="email" class="form-label">Company E-Mail *</label>
                                                <input class="multisteps-form__input form-control" type="email"
                                                       name="email" id="email" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="website" class="form-label">Website</label>
                                                <input class="multisteps-form__input form-control" type="text"
                                                       name="website" id="website" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5 mb-5">
                                    <div class="col-12 col-sm-6">
                                        <label class="form-control ms-0">Main Product Category *</label>
                                        <select class="form-control" name="main_product_category" id="products-list" size="5">
                                            @foreach($productCategories as $product)
                                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    </div>
                                    <h5 class="font-weight-bolder mt-5">Event Contact Person Details:</h5>

                                    <div class="row mt-4">
                                        <div class="col-12 col-sm-4 d-flex align-items-center">
{{--                                            <label class="form-control ms-0 me-2">Salutation *</label>--}}
                                            <select class="form-control" name="salutation" id="salutation" required>
                                                <option value="Mr" selected>Mr</option>
                                                <option value="Ms">Ms</option>
                                                <option value="Mrs">Mrs</option>
                                                <option value="Dr">Dr</option>
                                                <option value="Prof">Prof</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="firstName" class="form-label">First Name *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="first_name" id="firstName" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="lastName" class="form-label">Last Name *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="last_name" id="lastName" required/>
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
                                                <input class="multisteps-form__input form-control" type="email" name="contact_email" id="contactEmail" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactPhone" class="form-label">Phone Number *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="contact_phone" id="contactPhone" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="mt-4 form-label">Type of Business:</label>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Manufacturer" id="businessType1" checked>
                                                        <label class="form-check-label" for="businessType1">Manufacturer</label>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Distributor" id="businessType2">
                                                        <label class="form-check-label" for="businessType2">Distributor</label>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Retailer" id="businessType3">
                                                        <label class="form-check-label" for="businessType3">Retailer</label>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Wholesaler" id="businessType4">
                                                        <label class="form-check-label" for="businessType4">Wholesaler</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Importer" id="businessType5">
                                                        <label class="form-check-label" for="businessType5">Importer</label>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Exporter" id="businessType6">
                                                        <label class="form-check-label" for="businessType6">Exporter</label>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Agent" id="businessType7">
                                                        <label class="form-check-label" for="businessType7">Agent</label>
                                                    </div>
                                                </div>
                                                <div class="col-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Service Provider" id="businessType8">
                                                        <label class="form-check-label" for="businessType8">Service Provider</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" value="Others" id="businessType9">
                                                        <label class="form-check-label" for="businessType9">Others</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
{{--                                    <div class="row">--}}
{{--                                        <div class="col-12">--}}
{{--                                            <label class="mt-4 form-label">Type of Business:</label>--}}
{{--                                            <select class="form-control" name="choices-tags" id="choices-tags" multiple>--}}
{{--                                                <option value="Choice 1" selected>Manufacturer</option>--}}
{{--                                                <option value="Choice 2">Distributor</option>--}}
{{--                                                <option value="Choice 3">Retailer</option>--}}
{{--                                                <option value="Choice 4">Wholesaler</option>--}}
{{--                                                <option value="Choice 5">Importer</option>--}}
{{--                                                <option value="Choice 6">Exporter</option>--}}
{{--                                                <option value="Choice 7">Agent</option>--}}
{{--                                                <option value="Choice 8">Service Provider</option>--}}
{{--                                                <option value="Choice 9">Others</option>--}}
{{--                                            </select>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <h5 class="font-weight-bolder">Billing Details:</h5>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingCompany" class="form-label">Billing Company *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="billing_company" id="billingCompany" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactName" class="form-label">Contact Name *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="contact_name" id="contactName" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="contactEmail" class="form-label">E-Mail *</label>
                                                <input class="multisteps-form__input form-control" type="email" name="contact_email" id="contactEmail" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingAddress" class="form-label">Phone Number *</label>
                                                <input class="multisteps-form__input form-control" type="number" name="phoneNumber" id="phoneNumber" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-8 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingAddress" class="form-label">Billing Address *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="billing_address" id="billingAddress" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-5">
                                        <div class="col-12 col-sm-4">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingCity" class="form-label">Billing City *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="billing_city" id="billingCity" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingPostalCode" class="form-label">Billing Postal Code *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="billing_postal_code" id="billingPostalCode" required/>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                            <div class="input-group input-group-dynamic">
                                                <label for="billingState" class="form-label">Billing State *</label>
                                                <input class="multisteps-form__input form-control" type="text" name="billing_state" id="billingState"required/>
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
