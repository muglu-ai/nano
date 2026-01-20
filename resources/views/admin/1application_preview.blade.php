@extends('layouts.dashboard')
@section('title', 'Application Info')
@section('content')

    <style>
        /* General Table Styling */
        .table {
            background-color: #f3f6f6 !important; /* Light Blue Background */
        }

        /* Table Header Styling */
        /* .table thead th {
            /*background-color: #007bff;
            color: black;
            text-align: left;
            padding: 10px;
        }*/


        .table tbody tr:nth-child(odd) {
            background-color: #fff; /* Alternate row color */
        }

        /* Table Borders */
        .table-bordered th, .table-bordered td {
            border: 1px solid #000 !important; /* Light border */
        }

        /* Responsive Table Styling */
        @media (max-width: 768px) {
            .table td, .table th {
                font-size: 12px;
            }

            .table td {
                display: block;
                text-align: left;
                background-color: white !important; /* Keep white background on mobile */
            }
            .table td:before {
                content: attr(data-label);
                font-weight: bold;
                float: left;
            }
        }
        th, td {

            text-align: left; /* Adjust text alignment if needed */
        }


        /* Mobile Styles */
        @media screen and (min-width: 480px){
            .table-responsive {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

        }
        .table td {
            white-space: nowrap; /* Prevent text from breaking */
            overflow: hidden;
            text-overflow: ellipsis; /* Add '...' for long text */
        }

        @media (max-width: 768px) {
            .table-responsive td {
                display: table-cell !important;
            }
        }

    </style>
    <div class="container-fluid py-3">
        <h3 class="h4 font-weight-bold mt-4 text-dark text-uppercase">Application Info</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped shadow-sm">
                <thead class="table-dark text-white text-center">
                <tr>
                    <th class="text-nowrap text-white">Main Product Category</th>
                    <th class="text-nowrap text-white">Type of Business</th>
                    <th class="text-nowrap text-white">Sectors</th>
                </tr>
                </thead>
                <tbody>
                <tr>

                    <td class="text-dark">
                        @foreach($productCategories as $product)
                            @if(isset($application) && $application->main_product_category == $product->id)
                                {{ $product->name }}
                            @endif
                        @endforeach
                    </td>
                    <td class=" text-dark">{{ $application->type_of_business ?? 'N/A' }}</td>
                    <td class=" text-dark">
                        @php
                            $sectorNames = [];

                            if (isset($application) && isset($sectors)) {
                                $sectorIds = json_decode($application->sector_id, true);

                                // Ensure $sectorIds is an array
                                if (is_array($sectorIds)) {
                                    foreach ($sectors as $sector) {
                                        if (in_array($sector->id, $sectorIds)) {
                                            $sectorNames[] = $sector->name;
                                        }
                                    }
                                }
                            }

                            $sectorNames = !empty($sectorNames) ? implode(', ', $sectorNames) : 'N/A';
                        @endphp

                        {{ $sectorNames }}

                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

        @if($application->application_type == 'exhibitor')
            <div class="container-fluid py-3">
                <h3 class="h4 font-weight-bold mt-4 text-dark text-uppercase">Exhibition Info</h3>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped shadow-sm">
                        <thead class="table-dark text-white text-center">
                        <tr>
                            <th class="text-nowrap">Stall Type</th>
                            <th class="text-nowrap">Requested Stall Size</th>
                            @if(!empty($application->allocated_sqm))
                                <th class="text-nowrap">Allocated Stall Size</th>
                            @endif
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="text-start text-dark">{{ $application->stall_category }}</td>
                            <td class="text-start text-dark">{{ $application->interested_sqm }} sqm</td>
                            @if(!empty($application->allocated_sqm))
                                <td class="text-start text-dark">{{ $application->allocated_sqm }} sqm</td>
                            @endif
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif




    <div class="container-fluid py-3">
    <h3 class="h4 font-weight-bold mt-4 text-dark text-uppercase">Company Details</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-striped shadow-sm">
            <thead class="table-dark text-white text-center">
            <tr>
                <th class="text-nowrap text-white">Billing Country</th>
                <th class="text-nowrap text-white">GST Compliance</th>
                @if($application->gst_compliance == 1)
                    <th class="text-nowrap text-white">GST Number</th>

                <th class="text-nowrap text-white">PAN Number</th>
                @endif
                @if(isset($application->tan_no))
                    <th class="text-nowrap text-white">TAN Number</th>
                @endif
{{--                <th class="text-nowrap text-white">GST Certificate</th>--}}
            </tr>
            </thead>
            <tbody>
            <tr class="text-left">
                <td class="text-dark">{{ $application->country->name }}</td>
                <td class="text-dark">{{ $application->gst_compliance == 1 ? 'Yes' : 'No' }}</td>
                @if($application->gst_compliance == 1)
                    <td class="text-dark">{{ $application->gst_no }}</td>

                <td class="text-dark">{{ $application->pan_no }}</td>
                @endif
                @if(isset($application->tan_no))
                    <td class="text-dark">{{ $application->tan_no }}</td>
                @endif
{{--                <td>--}}
{{--                    <a href="{{ Storage::url($application->certificate) }}" target="_blank" class="text-primary" style="color: #0d6efd;">--}}
{{--                        View GST Certificate--}}
{{--                    </a>--}}
{{--                </td>--}}
            </tr>
            </tbody>
        </table>
    </div>
    </div>


    <div class="container-fluid py-3">
    <h3 class="h4 font-weight-bold mt-4 text-dark text-uppercase">Company Information</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-striped shadow-sm">
            <thead class="text-white text-center table-dark">
            <tr>
                <th class="text-nowrap text-white">Company Name</th>
                <th class="text-nowrap text-white">Website</th>
                <th class="text-nowrap text-white">Address</th>
                <th class="text-nowrap text-white">Postal Code</th>
            </tr>
            </thead>
            <tbody>
            <tr >
                <td class="text-dark">{{ $application->company_name }}</td>
                <td>
                    <a href="{{ $application->website }}" target="_blank" class="text-primary">
                        {{ $application->website ?? 'N/A' }}
                    </a>
                </td>
                <td class="text-dark">
                    {{ $application->address }}, {{ $application->city_id }},
                    {{ $application->state->name }}, {{ $application->country_name }}
                </td>
                <td class="text-dark">{{ $application->postal_code }}</td>
            </tr>
            </tbody>
        </table>
    </div>
    </div>



    <div class="container-fluid py-3">
        <h3 class="h4 font-weight-bold mt-4 text-dark text-uppercase">Event Contact Person</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="text-white text-center table-dark">
                <tr>
                    <th class="text-nowrap text-white">Name & Designation</th>
                    <th class="text-nowrap text-white">Email</th>
                    <th class="text-nowrap text-white">Mobile</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-dark">{{ $eventContact->salutation }} {{ $eventContact->first_name }} {{ $eventContact->last_name }}, {{ $eventContact->job_title }}</td>
                    <td class="text-dark">{{ $eventContact->email }}</td>
                    <td class="text-dark">{{ $eventContact->contact_number }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
{{--    @dd($application->secondaryEventContact)--}}
    @if(isset($application->secondaryEventContact) && $application->secondaryEventContact->email)
    <div class="container-fluid py-3">
        <h3 class="h4 font-weight-bold mt-4 text-dark text-uppercase">Event Contact Person (Secondary)</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="text-white text-center table-dark">
                <tr>
                    <th class="text-nowrap text-white">Name & Designation</th>
                    <th class="text-nowrap text-white">Email</th>
                    <th class="text-nowrap text-white">Mobile</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-dark">{{ $application->secondaryEventContact->salutation }} {{ $application->secondaryEventContact->first_name }} {{ $application->secondaryEventContact->last_name }}, {{ $application->secondaryEventContact->job_title }}</td>
                    <td class="text-dark">{{ $application->secondaryEventContact->email }}</td>
                    <td class="text-dark">{{ $application->secondaryEventContact->contact_number }}</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="container-fluid py-3">
    <h3 class="h4 font-weight-bold mt-4 text-dark text-uppercase">Billing Details</h3>
    <div class="table-responsive">
        <table class="table table-bordered table-striped shadow-sm">
            <thead class="table-dark text-white text-center">
            <tr>
                <th class="text-nowrap text-white">Billing Company</th>
                <th class="text-nowrap text-white">Contact Name</th>
                <th class="text-nowrap text-white">Email</th>
                <th class="text-nowrap text-white">Phone Number</th>
                <th class="text-nowrap text-white">Billing Address</th>
            </tr>
            </thead>
            <tbody>
            <tr class="text-left">
                <td class="text-dark">{{ $billingDetails->billing_company }}</td>
                <td class="text-dark">{{ $billingDetails->contact_name }}</td>
                <td class="text-dark">{{ $billingDetails->email }}</td>
                <td class="text-dark">{{ $billingDetails->phone }}</td>
                <td class="text-dark">
                    {{ $billingDetails->address }}, {{ $billingDetails->city_id }},
                    {{ $billingDetails->state->name }}, {{ $billingDetails->country->name }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    </div>



@endsection
