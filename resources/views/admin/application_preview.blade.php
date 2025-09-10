@extends('layouts.dashboard')
@section('title', 'Application Info')
@section('content')




    <style>
        .table {
            background-color: #f3f6f6 !important;
        }

        .table tbody tr:nth-child(odd) {
            background-color: #fff;
        }

        .table-bordered th, .table-bordered td {
            border: 1px solid #000 !important;
        }

        th, td {
            text-align: left;
        }

        input[readonly] {
            background-color: transparent;
            border: none;
            outline: none;
        }

        .edit-mode input {
            background-color: white;
            border: 1px solid #ccc;
        }
    </style>

    <div class="container-fluid py-3">
        <h3 class="h4 font-weight-bold mt-4 text-dark text-uppercase">Application Info</h3>

        <div class="text-end mb-3">
            @if($application->submission_status == 'rejected')
            <form id="submitForm" method="POST" action="{{ route('submit.back', $application->id) }}" class="d-inline">
                @csrf
                <input type="hidden" name="application_id" value="{{ $application->id }}">
                <button type="submit" class="btn btn-success me-2">
                    <i class="fas fa-check"></i> Submit Back
                </button>
            </form>
            @endif
            <a href="{{ route('download.application.form.admin') }}?application_id={{ $application->application_id }}" class="btn btn-info me-2">
                <i class="fas fa-download"></i> Download Application Form
            </a>
            
            <button id="editButton" class="btn btn-primary">Edit</button>
            <button id="saveButton" class="btn btn-success d-none">Save</button>
            <button id="cancelButton" class="btn btn-secondary d-none">Cancel</button>
        </div>

        <form id="applicationForm" method="POST" action="{{ route('application.update', $application->id) }}">
            @csrf
            @method('PUT')

            <!-- Company Information -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                        <tr>
                            <th>Company Name</th>
                            <th>Website</th>
                            <th>Address</th>
                            <th>Postal Code</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="company_name" value="{{ $application->company_name }}" class="form-control" readonly></td>
                            <td><input type="url" name="website" value="{{ $application->website }}" class="form-control" readonly></td>
                            <td><input type="text" name="address" value="{{ $application->address }}" class="form-control" readonly></td>
                            <td><input type="text" name="postal_code" value="{{ $application->postal_code }}" class="form-control" readonly></td>
                        </tr>
                    </tbody>
                </table>
            </div>
             <!-- Main Product Category, Type of Business, Sectors -->
             <h4 class="h5 font-weight-bold mt-4 text-dark">Main Product & Business Info</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                        <tr>
                            <th>Main Product Category</th>
                            <th>Type of Business</th>
                            <th>Sectors</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 30%;">
                                <select name="main_product_category" class="form-control" readonly disabled>
                                    @foreach($productCategories as $category)
                                        <option value="{{ $category->id }}" {{ $application->main_product_category == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>   
                            <td style="width: 35%;"><input type="text" name="type_of_business" value="{{ $application->type_of_business }}" class="form-control" readonly></td>
                            <td style="width: 35%;">
                                <select name="sectors[]" class="form-control" multiple readonly disabled id="sectorSelect">
                                    @php
                                        if (is_array($application->sector_id)) {
                                            $sectorIds = $application->sector_id;
                                        } else {
                                            $decodedValue = json_decode($application->sector_id, true);
                                            $sectorIds = is_array($decodedValue) ? $decodedValue : explode(',', trim($application->sector_id, '[]"'));
                                        }
                                    @endphp
                            
                                    @foreach($sectors as $sector)
                                        <option value="{{ $sector->id }}" 
                                        {{ in_array((string)$sector->id, $sectorIds) ? 'selected' : 'hidden' }}>
                                        {{ $sector->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>


            @if($application->application_type == 'exhibitor')
            <h4 class="h5 font-weight-bold mt-4 text-dark">Exhibition Info</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                        <tr>
                            <th>Stall Type</th>
                            <th>Requested Stall Size (sqm)</th>
                            <th>Allocated Stall Size (sqm)</th>
                            <th>SEMI Member</th>
                            <th>Membership ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="stall_category" value="{{ $application->stall_category }}" class="form-control" readonly></td>
                            <td><input type="text" name="interested_sqm" value="{{ $application->interested_sqm }}" class="form-control" readonly></td>
                            <td><input type="text" name="allocated_sqm" value="{{ $application->allocated_sqm }}" class="form-control" readonly></td>
                            <td><input type="text" name="semi_member" value="{{ $application->semi_member == 1 ? 'Yes' : 'No' }}" class="form-control" readonly></td>
                            <td><input type="text" name="semi_memberID" value="{{ $application->semi_memberID }}" class="form-control" readonly></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

            <!-- Event Contact Person -->
            <h4 class="h5 font-weight-bold mt-4 text-dark">Event Contact Person</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                        <tr>
                            <th>Name </th>
                            <th>Designation </th>
                            <th>Email</th>
                            <th>Mobile</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="event_contact_name" value="{{ $eventContact->first_name }} {{ $eventContact->last_name }}" class="form-control" readonly></td>
                            <td><input type="text" name="event_contact_design" value="{{ $eventContact->job_title }}" class="form-control" readonly></td>
                            <td><input type="email" name="event_contact_email" value="{{ $eventContact->email }}" class="form-control" readonly></td>
                            <td><input type="text" name="event_contact_mobile" value="{{ $eventContact->contact_number }}" class="form-control" readonly></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Event Contact Person (Secondary) -->
            @if(isset($application->secondaryEventContact))
            <h4 class="h5 font-weight-bold mt-4 text-dark">Event Contact Person (Secondary)</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                        <tr>
                            <th>Name </th>
                            <th>Designation </th>
                            <th>Email</th>
                            <th>Mobile</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="secondary_contact_name" value="{{ $application->secondaryEventContact->first_name }} {{ $application->secondaryEventContact->last_name }}" class="form-control" readonly></td>
                            <td><input type="text" name="secondary_contact_design" value="{{ $application->secondaryEventContact->job_title }}" class="form-control" readonly></td>
                            <td><input type="email" name="secondary_contact_email" value="{{ $application->secondaryEventContact->email }}" class="form-control" readonly></td>
                            <td><input type="text" name="secondary_contact_mobile" value="{{ $application->secondaryEventContact->contact_number }}" class="form-control" readonly></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

             <!-- Company Details -->
             <h4 class="h5 font-weight-bold mt-4 text-dark">GST Details</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                        <tr>
                            {{-- <th>Billing Country</th> --}}
                            <th>GST Compliance</th>
                            <th>GST Number</th>
                            <th>PAN Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            {{-- <td><input type="text" name="billing_country" value="{{ $application->country->name }}" class="form-control" readonly></td> --}}
                            <td><input type="text" name="gst_compliance" value="{{ $application->gst_compliance == 1 ? 'Yes' : 'No' }}" class="form-control" readonly></td>
                            @if($application->gst_compliance == 1)
                                <td><input type="text" name="gst_no" value="{{ $application->gst_no }}" class="form-control" readonly></td>
                                <td><input type="text" name="pan_no" value="{{ $application->pan_no }}" class="form-control" readonly></td>
                            @else
                                <td colspan="2" class="text-center">N/A</td>
                            @endif
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Billing Details -->
            <h4 class="h5 font-weight-bold mt-4 text-dark">Billing Details</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                        <tr>
                            <th>Billing Company</th>
                            <th>Contact Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Billing Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="billing_company" value="{{ $billingDetails->billing_company }}" class="form-control" readonly></td>
                            <td><input type="text" name="contact_name" value="{{ $billingDetails->contact_name }}" class="form-control" readonly></td>
                            <td><input type="email" name="billing_email" value="{{ $billingDetails->email }}" class="form-control" readonly></td>
                            <td><input type="text" name="billing_phone" value="{{ $billingDetails->phone }}" class="form-control" readonly></td>
                            <td><input type="text" name="billing_address" value="{{ $billingDetails->address }}" class="form-control" readonly></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                        <tr>
                            <th>Billing Address</th>
                            <th>Billing City</th>
                            <th>Billing State</th>
                            <th>Billing Country</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="text" name="billing_address" value="{{ $billingDetails->address }}" class="form-control" readonly></td>
                            <td><input type="text" name="billing_city" value="{{ $billingDetails->city_id }}" class="form-control" readonly></td>
                            <td>
                                <select name="billing_state" class="form-control" readonly disabled>
                                    @foreach($states as $state)
                                        <option value="{{ $state->id }}" {{ $billingDetails->state_id == $state->id ? 'selected' : '' }}>
                                            {{ $state->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <select name="billing_country" class="form-control" readonly disabled>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ $billingDetails->country_id == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                           
                        </tr>
                    </tbody>
                </table>
            </div>


        </form>

        @if(!empty($application->rejection_reason))
            <div class="alert alert-danger mt-4">
                <strong>Rejection Reason:</strong> {{ $application->rejection_reason }}
            </div>
        @endif
    </div>

    <script>
        const editButton = document.getElementById('editButton');
        const saveButton = document.getElementById('saveButton');
        const cancelButton = document.getElementById('cancelButton');
        const form = document.getElementById('applicationForm');
        const inputs = form.querySelectorAll('input');
        const selects = form.querySelectorAll('select');

        let originalValues = {};

        editButton.addEventListener('click', () => {
            inputs.forEach(input => {
                input.removeAttribute('readonly');
                originalValues[input.name] = input.value;
            });
            form.classList.add('edit-mode');
            editButton.classList.add('d-none');
            saveButton.classList.remove('d-none');
            cancelButton.classList.remove('d-none');
            selects.forEach(select => {
                select.removeAttribute('disabled');
            });
        });

        cancelButton.addEventListener('click', () => {
            inputs.forEach(input => {
                input.setAttribute('readonly', true);
                input.value = originalValues[input.name];
            });
            form.classList.remove('edit-mode');
            editButton.classList.remove('d-none');
            saveButton.classList.add('d-none');
            cancelButton.classList.add('d-none');
        });

        saveButton.addEventListener('click', () => {
            form.submit();
        });

        const sectorSelect = document.getElementById('sectorSelect');

editButton.addEventListener('click', () => {
    sectorSelect.removeAttribute('disabled');
    const options = sectorSelect.options;
    for (let i = 0; i < options.length; i++) {
        options[i].hidden = false; // Show all options on edit
    }
});

cancelButton.addEventListener('click', () => {
    sectorSelect.setAttribute('disabled', true);
    const options = sectorSelect.options;
    for (let i = 0; i < options.length; i++) {
        if (!options[i].selected) {
            options[i].hidden = true; // Hide unselected options on cancel
        }
    }
});
    </script>
@endsection

