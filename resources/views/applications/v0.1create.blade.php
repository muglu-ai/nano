<!DOCTYPE html>
<html>
<head>
    <title>{{ ucfirst($role) }} Profile Form</title>
</head>
<body>
<h1>{{ ucfirst($role) }} Profile Form</h1>

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

<form method="POST" action="{{ url("$role/application") }}" enctype="multipart/form-data">
    @csrf

    <!-- Billing Country as dropdown -->
    <label for="billing_country">Billing Country*</label>
    <select name="billing_country" required>
        @foreach($countries as $countryName)
            <option value="{{ $countryName->id }}" {{ isset($application) && $application->billing_country_id == $countryName->id ? 'selected' : '' }}>
                {{ $countryName->name }}
            </option>
        @endforeach
    </select><br>

    <!-- GST Compliance and related fields -->
    <label for="gst_compliance">GST Compliance*</label>
    <select name="gst_compliance" required>
        <option value="1" {{ isset($application) && $application->gst_compliance == 1 ? 'selected' : '' }}>Yes</option>
        <option value="0" {{ isset($application) && $application->gst_compliance == 0 ? 'selected' : '' }}>No</option>
    </select><br>

    <label for="gst_no">GST No*</label>
    <input type="text" name="gst_no" value="{{ $application->gst_no ?? '' }}" required pattern="[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}[Z]{1}[0-9A-Z]{1}"><br>

    <label for="pan_no">PAN No*</label>
    <input type="text" name="pan_no" value="{{ $application->pan_no ?? '' }}" required pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"><br>

    <label for="tan_no">TAN No*</label>
    <input type="text" name="tan_no" value="{{ $application->tan_no ?? '' }}" required pattern="[A-Z]{4}[0-9]{5}[A-Z]{1}"><br>

    <br> <br>

    <label for="company_name">Company Name*</label>
    <input type="text" name="company_name" value="{{ $application->company_name ?? '' }}" required><br>

    <label for="address">Company Address*</label>
    <textarea name="address" required>{{ $application->address ?? '' }}</textarea><br>

    <label for="postal_code">Postal Code*</label>
    <input type="text" name="postal_code" value="{{ $application->postal_code ?? '' }}" required><br>

    <label for="city">City*</label>
    <input type="text" name="city" value="{{ $application->city_id ?? '' }}" required><br>

    <label for="country">Country*</label>
    <select name="country" required>
        @foreach($countries as $countryName)
            <option value="{{ $countryName->id }}" {{ isset($application) && $application->country_id == $countryName->id ? 'selected' : '' }}>
                {{ $countryName->name }}
            </option>
        @endforeach
    </select><br>

    <label for="state">State*</label>
    <select name="state" required>
        @foreach($states as $stateName)
            <option value="{{ $stateName->id }}" {{ isset($application) && $application->state_id == $stateName->id ? 'selected' : '' }}>
                {{ $stateName->name }}
            </option>
        @endforeach
    </select><br>

    <label for="company_email">Company Contact/Landline No*</label>
    <input type="text" name="company_no" value="{{ $application->landline ?? '' }}" required><br>

    <label for="company_email">Company E-Mail*</label>
    <input type="email" name="company_email" value="{{ $application->company_email ?? '' }}" required><br>

    <label for="website">Website</label>
    <input type="url" name="website" value="{{ $application->website ?? '' }}"><br>

    <br>

    <!-- Main Product Category -->
    <label for="main_product_category">Main Product Category*</label>
    <select name="main_product_category" required>
        @foreach($productCategories as $product)
            <option value="{{ $product->id }}" {{ isset($application) && $application->main_product_category == $product->id ? 'selected' : '' }}>
                {{ $product->name }}
            </option>
        @endforeach
    </select><br>

    <h2>Event Contact Person Details:</h2>
    <label for="event_contact_salutation">Salutation*</label>
    <select name="event_contact_salutation" required>
        <option value="Mr." {{ isset($eventContact) && $eventContact->salutation == 'Mr.' ? 'selected' : '' }}>Mr.</option>
        <option value="Ms." {{ isset($eventContact) && $eventContact->salutation == 'Ms.' ? 'selected' : '' }}>Ms.</option>
        <option value="Mrs." {{ isset($eventContact) && $eventContact->salutation == 'Mrs.' ? 'selected' : '' }}>Mrs.</option>
        <option value="Dr." {{ isset($eventContact) && $eventContact->salutation == 'Dr.' ? 'selected' : '' }}>Dr.</option>
    </select><br>

    <label for="event_contact_first_name">First Name*</label>
    <input type="text" name="event_contact_first_name" value="{{ $eventContact->first_name ?? '' }}" required><br>

    <label for="event_contact_last_name">Last Name*</label>
    <input type="text" name="event_contact_last_name" value="{{ $eventContact->last_name ?? '' }}" required><br>

    <label for="event_contact_email">E-Mail*</label>
    <input type="email" name="event_contact_email" value="{{ $eventContact->email ?? '' }}" required><br>

    <label for="event_contact_phone">Phone Number*</label>
    <input type="text" name="event_contact_phone" value="{{ $eventContact->contact_number ?? '' }}" required><br>

    <h2>Type of Business:</h2>
    @foreach($business as $id => $name)
        <label for="type_of_business_{{ $id }}">
            <input type="checkbox" name="type_of_business[]" value="{{ $name }}" id="type_of_business_{{ $id }}"
                {{ isset($application) && in_array($name, explode(',', $application->type_of_business)) ? 'checked' : '' }}>
            {{ $name }}
        </label><br>
    @endforeach

    <!-- Billing Details -->
    <h2>Billing Details:</h2>
    <label for="billing_company">Billing Company*</label>
    <input type="text" name="billing_company" value="{{ $billing->billing_company ?? '' }}" required><br>

    <label for="billing_contact_name">Contact Name*</label>
    <input type="text" name="billing_contact_name" value="{{ $billing->contact_name ?? '' }}" required><br>

    <label for="billing_email">E-Mail*</label>
    <input type="email" name="billing_email" value="{{ $billing->email ?? '' }}" required><br>

    <label for="billing_phone">Phone Number*</label>
    <input type="text" name="billing_phone" value="{{ $billing->phone ?? '' }}" required><br>

    <label for="billing_address">Billing Address*</label>
    <textarea name="billing_address" required>{{ $billing->address ?? '' }}</textarea><br>


    <label for="billing_city">Billing City*</label>
    <input type="text" name="billing_city" value="{{ $billing->city_id ?? '' }}" required><br>

    <label for="billing_postal_code">Billing Postal Code*</label>
    <input type="text" name="billing_postal_code" value="{{ $billing->postal_code ?? '' }}" required><br>

    <label for="billing_state">Billing State*</label>
    <select name="billing_state" required>
        @foreach($states as $stateName)
            <option value="{{ $stateName->id }}" {{ isset($billing) && $billing->state_id == $stateName->id ? 'selected' : '' }}>
                {{ $stateName->name }}
            </option>
        @endforeach
    </select><br>

    <label for="gst_certificate">Upload GST Certificate*</label>
    <input type="file" name="gst_certificate" accept="application/pdf" {{ !empty($application->certificate) ? '' : 'required' }}><br>
    <button type="submit">Submit Application</button>
</form>
</body>
</html>
