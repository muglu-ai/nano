@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('content')
    <style>
    @media (max-width: 768px) {
    .card-title {
    font-size: 14px; /* Adjust as needed */
    }

    .card-text {
    font-size: 12px; /* Adjust as needed */
    }
    }
    </style>
    <div class="container-fluid py-3">
        <h3 class="h4 font-weight-bold mt-4">Application Info</h3>
        <div class="row">
            <div class="col-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="h4 font-weight-bold">Main Product Category</h3>
                        @foreach($productCategories as $product)
                            @if(isset($application) && $application->main_product_category == $product->id)
                                <p class="form-control-plaintext mb-0">{{ $product->name }}</p>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="h4 font-weight-bold">Type of Business:</h3>
                        @if(isset($application))
                            {{ $application->type_of_business }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="h4 font-weight-bold">Sectors</h3>
                        @php
                            $sectorNames = [];
                            if (isset($application)) {
                                foreach ($sectors as $sector) {
                                    if (in_array($sector->id, json_decode($application->sector_id, true))) {
                                        $sectorNames[] = $sector->name;
                                    }
                                }
                            }
                        @endphp
                        <p class="form-control-plaintext mb-0">{{ implode(', ', $sectorNames) }}</p>
                    </div>
                </div>
            </div>
        </div>
        @if($application->application_type == 'exhibitor')
            <h3 class="h4 font-weight-bold mt-4">Exhibition Info</h3>
            <div class="row">
                <div class="col-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h3 class="h4 font-weight-bold">Stall Type</h3>
                                    <p class="form-control-plaintext mb-0">{{ $application->stall_category }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h3 class="h4 font-weight-bold">Requested Stall Size:</h3>
                                    <p class="form-control-plaintext mb-0 ">{{ $application->interested_sqm }} sqm</p>
                                </div>
                                @if(isset($application->allocated_sqm) && $application->allocated_sqm != null)
                                    <div>
                                        <h3 class="h4 font-weight-bold">Allocated Stall Size:</h3>
                                        <p class="form-control-plaintext mb-0">{{ $application->allocated_sqm }} sqm</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card shadow-sm border-0">
                    </div>
                </div>
            </div>
        @endif
        <h3 class="h4 font-weight-bold mt-4">Company Details</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Billing Country</h3>
                    <p class="form-control-plaintext mb-0">{{ $application->country->name }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">GST Compliance</h3>
                    <p class="text-muted">{{ $application->gst_compliance == 1 ? 'Yes' : 'No' }}</p>
                </div>
            </div>
            @if($application->gst_compliance == 1)
                <div class="col-md-4">
                    <div class="card p-3 border-0 shadow-sm">
                        <h3 class="h4 font-weight-bold">GST Number</h3>
                        <p class="form-control-plaintext mb-0">{{ $application->gst_no }}</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">PAN Number</h3>
                    <p class="form-control-plaintext mb-0">{{ $application->pan_no }}</p>
                </div>
            </div>
            @if(isset($application->tan_no))

                <div class="col-md-4">
                    <div class="card p-3 border-0 shadow-sm">
                        <h3 class="h4 font-weight-bold">TAN Number</h3>
                        <p class="form-control-plaintext mb-0">{{ $application->tan_no }}</p>
                    </div>
                </div>
            @endif
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">GST Certificate</h3>
                    <p><a href="{{ Storage::url($application->certificate) }}" target="_blank" class="text-primary">View
                            GST Certificate</a></p>
                </div>
            </div>
        </div>

        <h3 class="h4 font-weight-bold mt-4">Company Information</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Company Name</h3>
                    <p class="form-control-plaintext mb-0">{{ $application->company_name }}</p>
                    <h3 class="h4 font-weight-bold">Website</h3>

                    <p class="text"><a href="{{ $application->website }}"
                                       target="_blank">{{ $application->website }}</a></p>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Address</h3>
                    <p class="form-control-plaintext mb-0">{{ $application->address}}, {{$application->city_id}}
                        , {{  $application->state->name}} <br>
                        {{  $application->country->name}}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Postal Code</h3>
                    <p class="form-control-plaintext mb-0">{{ $application->postal_code }}</p>
                </div>
            </div>
        </div>

        <h3 class="h4 font-weight-bold mt-4">Event Contact Person</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Name & Designation</h3>
                    <p class="form-control-plaintext mb-0">{{ $eventContact->salutation }} {{ $eventContact->first_name }} {{ $eventContact->last_name }}
                        , <br>{{ $eventContact->job_title }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Email</h3>
                    <p class="form-control-plaintext mb-0">{{ $eventContact->email }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Mobile</h3>
                    <p class="form-control-plaintext mb-0">{{ $eventContact->contact_number }}</p>
                </div>
            </div>
        </div>

        <h3 class="h4 font-weight-bold mt-4">Billing Details</h3>
        <div class="row">
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Billing Company</h3>
                    <p class="form-control-plaintext mb-0">{{ $billingDetails->billing_company }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Contact Name</h3>
                    <p class="form-control-plaintext mb-0">{{ $billingDetails->contact_name }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Email</h3>
                    <p class="form-control-plaintext mb-0">{{ $billingDetails->email }}</p>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Phone Number</h3>
                    <p class="form-control-plaintext mb-0">{{ $billingDetails->phone }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Billing Address</h3>
                    <p class="form-control-plaintext mb-0">{{ $billingDetails->address }}, {{ $billingDetails->city_id }} <br>
                        {{ $billingDetails->state->name }}, {{ $billingDetails->country->name }}</p>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card p-3 border-0 shadow-sm">
                    <h3 class="h4 font-weight-bold">Billing City</h3>
                    <p class="form-control-plaintext mb-0">{{ $billingDetails->city_id }}</p>
                </div>
            </div>
        </div>

    </div>
@endsection
