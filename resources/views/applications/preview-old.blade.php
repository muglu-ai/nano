<!DOCTYPE html>
<html>
<head>
    <title>Profile Preview</title>
</head>
<body>
<h1>Profile Review</h1>

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
<!-- GST Certificate with public path -->
<p><strong>GST Certificate:</strong> <a href="{{ asset('storage/' . $application->certificate) }}" target="_blank">View GST Certificate</a></p>

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



<!-- Application Form Details -->
{{--<h2>Application Form Details</h2>--}}
<h2>Type of Participation</h2>
<p><strong>Type of Participation:</strong> {{ $application->participation_type }}</p>
<p><strong>Region:</strong> {{ $application->region }}</p>
<p><strong>Previous Participation:</strong> {{ $application->semi_member ? 'Yes' : 'No' }}</p>
<p><strong>Stall Category:</strong> {{ $application->stall_category }}</p>
<p><strong>Interested SQM:</strong> {{ $application->interested_sqm }}</p>
{{--<p><strong>Product Groups:</strong> {{ implode(', ', $application->product_groups) }}</p>--}}
<p><strong>Sectors:</strong> {{ implode(', ', $application->sectors->pluck('name')->toArray()) }}</p>
{{--<a href="{{ route('dashboard.exhibitor') }}">Back to Dashboard</a>--}}

<!-- Application Status -->
<p><strong>Application Status:</strong> {{ ucwords($application->status) }} </p>

<!-- Submit Button that says once submitted cannot edited-->
<form action="{{ route('application.final') }}" method="POST">
    @csrf
    <button type="submit" class="btn btn-primary mt-3">Submit</button>
</form>
</body>
</html>
