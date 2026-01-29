@extends('layouts.application')

@section('title', 'Onboarding Form')
@section('content')
    <main class="mn-inner2">
        <div class="row">
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">

                        <div class="container">
                            <div class="row">
                                <div class="col s12">
                                    <ul class="tabs">
                                        <li class="tab col s3"><a href="#"> Personal Info</a></li>
                                        <li class="tab col s3"><a href="#"> Product Info </a></li>
                                        <li class="tab col s3"><a href="#" class="active waves-effect waves-teal"> Terms and Conditions </a></li>
                                        <li class="tab col s3"><a href="#"> Review</a></li>
                                    </ul>
                                </div>
                            </div>

<h2>Company Information</h2>
<p><strong>Company Name:</strong> {{ $application->company_name }}</p>
<p><strong>Address:</strong> {{ $application->address }}</p>
<p><strong>Postal Code:</strong> {{ $application->postal_code }}</p>
<p><strong>City:</strong> {{ $application->city_id }}</p>
<p><strong>State:</strong> {{ $application->state->name }}</p>
<p><strong>Country:</strong> {{ $application->country->name }}</p>
<p><strong>Landline:</strong> {{ $application->landline }}</p>
<p><strong>Company Email:</strong> {{ $application->company_email }}</p>
<p><strong>Website:</strong> {{ $application->website }}</p>
<p><strong>Main Product Category:</strong> {{ $application->main_product_category }}</p>
<p><strong>GST No:</strong> {{ $application->gst_no }}</p>
<p><strong>PAN No:</strong> {{ $application->pan_no }}</p>
<p><strong>Type of Business:</strong> {{ $application->type_of_business }}</p>

<h2>Event Contact Person</h2>
<p><strong>Salutation:</strong> {{ $eventContact->salutation }}</p>
<p><strong>First Name:</strong> {{ $eventContact->first_name }}</p>
<p><strong>Last Name:</strong> {{ $eventContact->last_name }}</p>
<p><strong>Email:</strong> {{ $eventContact->email }}</p>
<p><strong>Contact Number:</strong> {{ $eventContact->contact_number }}</p>

<h2>Billing Details</h2>
<p><strong>Billing Company:</strong> {{ $billing->billing_company }}</p>
<p><strong>Contact Name:</strong> {{ $billing->contact_name }}</p>
<p><strong>Email:</strong> {{ $billing->email }}</p>
<p><strong>Phone:</strong> {{ $billing->phone }}</p>
<p><strong>Address:</strong> {{ $billing->address }}</p>
<p><strong>Postal Code:</strong> {{ $billing->postal_code }}</p>
<p><strong>City:</strong> {{ $billing->city_id }}</p>
<p><strong>State:</strong> {{ $billing->state->name }}</p>
<p><strong>Country:</strong> {{ $billing->country->name }}</p>



<!-- Application Status -->
<p><strong>Application Status:</strong> {{ ucwords($application->status) }} </p>



<a href="">Back to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </main>
@endsection
