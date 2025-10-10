@extends('layouts.users')
@section('title', 'Application Info')
@section('content')
    @php
        $hide = true;
    @endphp
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="mb-0 h4 font-weight-bolder">Application Info</h3>
                                <p class="mb-4">TIN No: {{ $application->application_id }}</p>
                            </div>
                            @php $invoice = $application->invoices()->latest('id')->first(); @endphp
                            @if($invoice?->pin_no)
                                <div class="col-md-6">
                                    {{-- <h3 class="mb-0 h4 font-weight-bolder">PIN No</h3> --}}
                                    <p class="mb-4">PIN No: {{ $invoice->pin_no }}</p>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <h3 class="mb-0 h4 font-weight-bolder">Registration Date</h3>
                                <p class="mb-4">Date: {{ $application->approved_date ? \Carbon\Carbon::parse($application->approved_date)->format('Y-m-d') : '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0 h4 font-weight-bolder">Company Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="billing_country" class="form-label fw-bold text-nowrap">Billing Country:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->country->name ?? 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="gst_compliance" class="form-label fw-bold text-nowrap">GST Compliance:</label>
                                <p class="form-control-plaintext mb-0">{{ is_null($application->gst_compliance) ? 'Not Provided' : ($application->gst_compliance == 1 ? 'Yes' : 'Not Applicable') }}</p>
                            </div>
                            @if($application->gst_compliance == 1)
                                <div class="col-md-4">
                                    <label for="gst_number" class="form-label fw-bold text-nowrap">GST Number:</label>
                                    <p class="form-control-plaintext mb-0">{{ $application->gst_no ?: 'Not Provided' }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="pan_no" class="form-label fw-bold text-nowrap">PAN Number:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->pan_no ?: 'Not Provided' }}</p>
                            </div>
{{--                            @if(!empty($application->tan_no))--}}
                                <div class="col-md-4">
                                    <label for="tan_no" class="form-label fw-bold text-nowrap">TAN Number:</label>
                                    <p class="form-control-plaintext mb-0">{{ $application->tan_no ?: 'Not Provided' }}</p>
                                </div>
{{--                            @endif--}}
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="company_name" class="form-label fw-bold text-nowrap">Company Name:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->company_name ?: 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="company_address" class="form-label fw-bold text-nowrap">Company Address:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->address ?: 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label fw-bold text-nowrap">Postal Code:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->postal_code ?: 'Not Provided' }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="city" class="form-label fw-bold text-nowrap">City:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->city_id ?: 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="state" class="form-label fw-bold text-nowrap">State:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->state->name ?? 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="company_contact" class="form-label fw-bold text-nowrap">Company Contact/Landline No:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->landline ?: 'Not Provided' }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="company_email" class="form-label fw-bold text-nowrap">Company Email:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->company_email ?: 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="website" class="form-label fw-bold text-nowrap">Website:</label>
                                @php
                                    $website = $application->website;
                                    if ($website && !preg_match('/^https?:\/\//', $website)) {
                                        $website = 'https://' . $website;
                                    }
                                @endphp
                                @if($application->website)
                                    <p class="form-control-plaintext mb-0"><a href="{{ $website }}" target="_blank" style="color: blue;">{{ $application->website }}</a></p>
                                @else
                                    <p class="form-control-plaintext mb-0">Not Provided</p>
                                @endif
                            </div>
                        </div>

                        @if($hide == false)
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="main_product_category" class="form-label fw-bold text-nowrap">Main Product Category:</label>
                                @php $found = false; @endphp
                                @foreach($productCategories as $product)
                                    @if(isset($application) && $application->main_product_category == $product->id)
                                        <p class="form-control-plaintext mb-0">{{ $product->name }}</p>
                                        @php $found = true; @endphp
                                    @endif
                                @endforeach
                                @if(!$found)
                                    <p class="form-control-plaintext mb-0">Not Provided</p>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="main_product_category" class="form-label fw-bold text-nowrap">Type of Buisness:</label>
                                <p class="form-control-plaintext mb-0">
                                    @if(isset($application) && $application->type_of_business)
                                        {{ $application->type_of_business }}
                                    @else
                                        Not Provided
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0 h4 font-weight-bolder">Exhibition Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mt-3">
                            @if(!empty($application->stallNumber))
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-nowrap">Booth Number:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->stallNumber ?? '-' }}</p>
                            </div>
                            @endif
                            {{-- @if($application->stall_category !== 'Startup Booth') --}}
                            @php
                            if($application->stall_category == 'Startup Booth'){
                                $stallSize = 'Booth / POD';
                            }else{
                                $stallSize = $application->allocated_sqm ?? '-' . ' SQM';
                            }
                            @endphp
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-nowrap">Stall Size:</label>
                                    <p class="form-control-plaintext mb-0">{{ $stallSize ?: 'Not Provided' }}</p>
                                </div>
                            {{-- @endif --}}
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-nowrap">Stall Type:</label>
                                <p class="form-control-plaintext mb-0">{{ $application->stall_category ?: 'Not Provided' }}</p>
                            </div>

                            @if($hide == false)
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-nowrap">Member:</label>
                                <p class="form-control-plaintext mb-0">
                                    @if(isset($application) && !is_null($application->semi_member))
                                        {{ $application->semi_member == 1 ? 'Yes' : 'No' }}
                                    @else
                                        Not Provided
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-nowrap">Preffered Location:</label>
                                <p class="form-control-plaintext mb-0">
                                    {{ $application->pref_location ?: 'Not Provided' }}
                                </p>
                            </div>
                                @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h3 class="mb-0 h4 font-weight-bolder">Event Contact Person Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="contact_title" class="form-label fw-bold text-nowrap">Name & Designation:</label>
                                <p class="form-control-plaintext mb-0">{{ $eventContact ? trim(($eventContact->salutation ?? '') . ' ' . ($eventContact->first_name ?? '') . ' ' . ($eventContact->last_name ?? '') . ', ' . ($eventContact->job_title ?? '')) : 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="contact_email" class="form-label fw-bold text-nowrap">Contact Email:</label>
                                <p class="form-control-plaintext mb-0">{{ $eventContact ? ($eventContact->email ?: 'Not Provided') : 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="contact_number" class="form-label fw-bold text-nowrap">Mobile Number:</label>
                                <p class="form-control-plaintext mb-0">{{ $eventContact ? ($eventContact->contact_number ?: 'Not Provided') : 'Not Provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h3 class="mb-0 h4 font-weight-bolder">Billing Details</h3>
                    </div>
                    <div class="card-body">
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="billing_company" class="form-label fw-bold text-nowrap">Billing Company:</label>
                                <p class="form-control-plaintext mb-0">{{ $billingDetails ? ($billingDetails->billing_company ?: 'Not Provided') : 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="billing_contact_name" class="form-label fw-bold text-nowrap">Contact Name:</label>
                                <p class="form-control-plaintext mb-0">{{ $billingDetails ? ($billingDetails->contact_name ?: 'Not Provided') : 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="billing_email" class="form-label fw-bold text-nowrap">Email:</label>
                                <p class="form-control-plaintext mb-0">{{ $billingDetails ? ($billingDetails->email ?: 'Not Provided') : 'Not Provided' }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="billing_phone" class="form-label fw-bold text-nowrap">Phone Number:</label>
                                <p class="form-control-plaintext mb-0">{{ $billingDetails ? ($billingDetails->phone ?: 'Not Provided') : 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="billing_address" class="form-label fw-bold text-nowrap">Billing Address:</label>
                                <p class="form-control-plaintext mb-0">{{ $billingDetails ? ($billingDetails->address ?: 'Not Provided') : 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="billing_city" class="form-label fw-bold text-nowrap">Billing City:</label>
                                <p class="form-control-plaintext mb-0">{{ $billingDetails ? ($billingDetails->city_id ?: 'Not Provided') : 'Not Provided' }}</p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label for="billing_postal_code" class="form-label fw-bold text-nowrap">Billing Postal Code:</label>
                                <p class="form-control-plaintext mb-0">{{ $billingDetails ? ($billingDetails->postal_code ?: 'Not Provided') : 'Not Provided' }}</p>
                            </div>
                            <div class="col-md-4">
                                <label for="billing_state" class="form-label fw-bold text-nowrap">State:</label>
                                <p class="form-control-plaintext mb-0">{{ $billingDetails ? ($billingDetails->state->name ?? 'Not Provided') : 'Not Provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
