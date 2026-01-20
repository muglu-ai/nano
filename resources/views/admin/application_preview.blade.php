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
                <form id="submitForm" method="POST" action="{{ route('submit.back', $application->id) }}"
                      class="d-inline">
                    @csrf
                    <input type="hidden" name="application_id" value="{{ $application->id }}">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="fas fa-check"></i> Submit Back
                    </button>
                </form>
            @endif
            <a href="{{ route('download.application.form.admin') }}?application_id={{ $application->application_id }}"
               class="btn btn-info me-2">
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
                        <td><input type="text" name="company_name" value="{{ $application->company_name ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                        <td><input type="url" name="website" value="{{ $application->website ?: 'Not Provided' }}" class="form-control"
                                   readonly></td>
                        <td><input type="text" name="address" value="{{ $application->address ?: 'Not Provided' }}" class="form-control"
                                   readonly></td>
                        <td><input type="text" name="postal_code" value="{{ $application->postal_code ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Company Registration Certificate -->
            @php
                $certificatePath = null;
                // Check for certificate in different possible locations
                if (!empty($application->certificate)) {
                    $certificatePath = $application->certificate;
                } elseif ($application->application_type === 'startup-zone') {
                    // For startup zone, check if there's a draft with certificate_path
                    $draft = \App\Models\StartupZoneDraft::where('converted_to_application_id', $application->id)->first();
                    if ($draft && !empty($draft->certificate_path)) {
                        $certificatePath = $draft->certificate_path;
                    }
                }
            @endphp
            @if($certificatePath)
            <h4 class="h5 font-weight-bold mt-4 text-dark">Company Registration Certificate</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                    <tr>
                        <th>Certificate File</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            @php
                                // Determine the full path to the certificate
                                $fileExists = false;
                                $downloadUrl = null;
                                
                                // Normalize path - remove leading storage/ or public/ if present
                                $normalizedPath = preg_replace('#^(storage/|public/|/storage/|/public/)#', '', $certificatePath);
                                
                                // Check if file exists in public storage
                                if (\Storage::disk('public')->exists($normalizedPath)) {
                                    $fileExists = true;
                                    $downloadUrl = \Storage::disk('public')->url($normalizedPath);
                                } elseif (\Storage::disk('public')->exists($certificatePath)) {
                                    $fileExists = true;
                                    $downloadUrl = \Storage::disk('public')->url($certificatePath);
                                } elseif (file_exists(storage_path('app/public/' . $normalizedPath))) {
                                    $fileExists = true;
                                    $downloadUrl = asset('storage/' . $normalizedPath);
                                } elseif (file_exists(storage_path('app/public/' . $certificatePath))) {
                                    $fileExists = true;
                                    $downloadUrl = asset('storage/' . $certificatePath);
                                }
                                
                                $fileName = basename($certificatePath);
                            @endphp
                            @if($fileExists)
                                <div class="d-flex align-items-center gap-3">
                                    <i class="fas fa-file-pdf text-danger" style="font-size: 2rem;"></i>
                                    <div>
                                        <strong>{{ $fileName }}</strong>
                                        <br>
                                        <a href="{{ $downloadUrl }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                            <i class="fas fa-download"></i> View/Download PDF
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="text-muted">
                                    <i class="fas fa-exclamation-triangle"></i> Certificate file not found at: {{ $certificatePath }}
                                </div>
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            @endif
            <!-- Main Product Category, Type of Business, Sectors -->
            <h4 class="h5 font-weight-bold mt-4 text-dark">Sector and Sub-Sector</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                    <tr>
{{--                        <th>Main Product Category</th>--}}
{{--                        <th>Type of Business</th>--}}
                        <th>Sectors</th>
                        <th>Sub-Sector</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
{{--                        <td style="width: 30%;">--}}
{{--                            <select name="main_product_category" class="form-control" readonly disabled>--}}
{{--                                @foreach($productCategories as $category)--}}
{{--                                    <option value="{{ $category->id }}" {{ $application->main_product_category == $category->id ? 'selected' : '' }}>--}}
{{--                                        {{ $category->name }}--}}
{{--                                    </option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </td>--}}
{{--                        <td style="width: 35%;"><input type="text" name="type_of_business"--}}
{{--                                                       value="{{ $application->type_of_business }}" class="form-control"--}}
{{--                                                       readonly></td>--}}
                        <td style="width: 35%;">
                            @php
                                $sectorDisplay = 'Not Provided';
                                $sectorIds = [];
                                
                                // Handle different formats: array, JSON string, comma-separated string, or plain string
                                if (!empty($application->sector_id)) {
                                    if (is_array($application->sector_id)) {
                                        $sectorIds = $application->sector_id;
                                    } elseif (is_string($application->sector_id)) {
                                        // Try to decode as JSON first
                                        $decodedValue = json_decode($application->sector_id, true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedValue)) {
                                            $sectorIds = $decodedValue;
                                        } elseif (strpos($application->sector_id, ',') !== false) {
                                            // Comma-separated string
                                            $sectorIds = array_filter(array_map('trim', explode(',', $application->sector_id)));
                                        } elseif (is_numeric($application->sector_id)) {
                                            // Single numeric ID
                                            $sectorIds = [(string)$application->sector_id];
                                        } else {
                                            // Plain string - try to find matching sector by name
                                            $matchingSector = \App\Models\Sector::where('name', 'like', '%' . $application->sector_id . '%')->first();
                                            if ($matchingSector) {
                                                $sectorIds = [(string)$matchingSector->id];
                                            } else {
                                                // If no match found, display the string as-is
                                                $sectorDisplay = $application->sector_id;
                                            }
                                        }
                                    } elseif (is_numeric($application->sector_id)) {
                                        $sectorIds = [(string)$application->sector_id];
                                    }
                                    
                                    // Build sector display from IDs
                                    if (!empty($sectorIds) && $sectorDisplay === 'Not Provided') {
                                        $sectorNames = [];
                                        foreach ($sectorIds as $sid) {
                                            $sid = trim($sid);
                                            if (is_numeric($sid)) {
                                                $sector = \App\Models\Sector::find($sid);
                                                if ($sector) {
                                                    $sectorNames[] = $sector->name;
                                                }
                                            }
                                        }
                                        $sectorDisplay = !empty($sectorNames) ? implode(', ', $sectorNames) : 'Not Provided';
                                    }
                                }
                            @endphp
                            <select name="sectors[]" class="form-control" multiple readonly disabled id="sectorSelect">
                                @foreach($sectors as $sector)
                                    <option value="{{ $sector->id }}"
                                            {{ in_array((string)$sector->id, array_map('strval', $sectorIds)) ? 'selected' : 'hidden' }}>
                                        {{ $sector->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-2">
                                <strong>Display:</strong> <span id="sectorDisplay">{{ $sectorDisplay }}</span>
                            </div>
                        </td>
                        <td style="width: 35%;"><input type="text" name="sub_sector"
                                                       value="{{ $application->subSector ?: 'Not Provided' }}" class="form-control"
                                                       readonly></td>

                    </tr>
                    </tbody>
                </table>
            </div>


            <!-- Booth Information - Show for all application types -->
            <h4 class="h5 font-weight-bold mt-4 text-dark">Booth Information</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                    <tr>
                        <th>Stall/Booth Type</th>
                        <th>Requested Size</th>
                        <th>Allocated Size</th>
                        @if($application->application_type == 'exhibitor')
                        <th>Stall Number</th>
                        <th>Zone</th>
                        <th>Hall Number</th>
                        @endif
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input type="text" name="stall_category" value="{{ $application->stall_category ?: 'Not Provided' }}"
                                       class="form-control" readonly></td>
{{--                            <td><input type="text" name="interested_sqm" value="{{ $application->interested_sqm }}"--}}
{{--                                       class="form-control" readonly></td>--}}
                            <td><input type="text" name="allocated_sqm" value="{{ $application->allocated_sqm ?: 'Not Provided' }}"
                                       class="form-control" readonly></td>
{{--                            <td><input type="text" name="semi_member"--}}
{{--                                       value="{{ $application->semi_member == 1 ? 'Yes' : 'No' }}" class="form-control"--}}
{{--                                       readonly></td>--}}
{{--                            <td><input type="text" name="semi_memberID" value="{{ $application->semi_memberID }}"--}}
{{--                                       class="form-control" readonly></td>--}}
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
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Email</th>
                        <th>Mobile</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input type="text" name="event_contact_name"
                                   value="{{ trim(($eventContact->first_name ?? '') . ' ' . ($eventContact->last_name ?? '')) ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                        <td><input type="text" name="event_contact_design" value="{{ ($eventContact->job_title ?? '') ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                        <td><input type="email" name="event_contact_email" value="{{ ($eventContact->email ?? '') ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                        <td><input type="text" name="event_contact_mobile" value="{{ ($eventContact->contact_number ?? '') ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
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
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Email</th>
                            <th>Mobile</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><input type="text" name="secondary_contact_name"
                                       value="{{ isset($application->secondaryEventContact) ? (trim(($application->secondaryEventContact->first_name ?? '') . ' ' . ($application->secondaryEventContact->last_name ?? '')) ?: 'Not Provided') : 'Not Provided' }}"
                                       class="form-control" readonly></td>
                            <td><input type="text" name="secondary_contact_design"
                                       value="{{ isset($application->secondaryEventContact) ? (($application->secondaryEventContact->job_title ?? '') ?: 'Not Provided') : 'Not Provided' }}" class="form-control"
                                       readonly></td>
                            <td><input type="email" name="secondary_contact_email"
                                       value="{{ isset($application->secondaryEventContact) ? (($application->secondaryEventContact->email ?? '') ?: 'Not Provided') : 'Not Provided' }}" class="form-control"
                                       readonly></td>
                            <td><input type="text" name="secondary_contact_mobile"
                                       value="{{ isset($application->secondaryEventContact) ? (($application->secondaryEventContact->contact_number ?? '') ?: 'Not Provided') : 'Not Provided' }}"
                                       class="form-control" readonly></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- GST & Tax Details -->
            <h4 class="h5 font-weight-bold mt-4 text-dark">GST & Tax Details</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                    <tr>
                        <th>GST Compliance</th>
                        <th>GST Number</th>
                        <th>PAN Number</th>
                        <th>TAN Number</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            @php
                                $gstCompliance = $application->gst_compliance;
                                if (is_null($gstCompliance)) {
                                    $gstDisplay = 'Not Provided';
                                } elseif ($gstCompliance === 1 || $gstCompliance === true || $gstCompliance === '1' || strtolower($gstCompliance) === 'yes' || strtolower($gstCompliance) === 'true') {
                                    $gstDisplay = 'Yes';
                                } else {
                                    $gstDisplay = 'No';
                                }
                            @endphp
                            <input type="text" name="gst_compliance" value="{{ $gstDisplay }}" class="form-control" readonly>
                        </td>
                        <td>
                            @php
                                $gstNo = $application->gst_no;
                                // Handle string values - check if it contains any GST number pattern
                                if (empty($gstNo) || $gstNo === null || $gstNo === 'null' || $gstNo === '') {
                                    $gstNoDisplay = 'Not Provided';
                                } else {
                                    $gstNoDisplay = $gstNo;
                                }
                            @endphp
                            <input type="text" name="gst_no" value="{{ $gstNoDisplay }}" class="form-control" readonly>
                        </td>
                        <td>
                            @php
                                $panNo = $application->pan_no;
                                if (empty($panNo) || $panNo === null || $panNo === 'null' || $panNo === '') {
                                    $panNoDisplay = 'Not Provided';
                                } else {
                                    $panNoDisplay = $panNo;
                                }
                            @endphp
                            <input type="text" name="pan_no" value="{{ $panNoDisplay }}" class="form-control" readonly>
                        </td>
                        <td>
                            @php
                                $tanNo = $application->tan_no;
                                if (empty($tanNo) || $tanNo === null || $tanNo === 'null' || $tanNo === '') {
                                    $tanNoDisplay = 'Not Provided';
                                } else {
                                    $tanNoDisplay = $tanNo;
                                }
                            @endphp
                            <input type="text" name="tan_no" value="{{ $tanNoDisplay }}" class="form-control" readonly>
                        </td>
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
                        <td><input type="text" name="billing_company" value="{{ ($billingDetails->billing_company ?? '') ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                        <td><input type="text" name="contact_name" value="{{ ($billingDetails->contact_name ?? '') ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                        <td><input type="email" name="billing_email" value="{{ ($billingDetails->email ?? '') ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                        <td><input type="text" name="billing_phone" value="{{ ($billingDetails->phone ?? '') ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                        <td><input type="text" name="billing_address" value="{{ ($billingDetails->address ?? '') ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
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
                        <td><input type="text" name="billing_address" value="{{ ($billingDetails->address ?? '') ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                        <td><input type="text" name="billing_city" value="{{ ($billingDetails->city_id ?? '') ?: 'Not Provided' }}"
                                   class="form-control" readonly></td>
                        <td>
                            <select name="billing_state" class="form-control" readonly disabled>
                                @if(empty($billingDetails->state_id))
                                    <option selected>Not Provided</option>
                                @endif
                                @foreach($states as $state)
                                    <option value="{{ $state->id  }}" {{ ($billingDetails->state_id ?? null) == $state->id ? 'selected' : '' }}>
                                    <option value="{{ $state->id }}" {{ ($billingDetails->state_id ?? null) == $state->id ? 'selected' : 'hidden' }}>
                                        {{ $state->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <select name="billing_country" class="form-control" readonly disabled>
                                @if(empty($billingDetails->country_id))
                                    <option selected>Not Provided</option>
                                @endif
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}" {{ ($billingDetails->country_id ?? null) == $country->id ? 'selected' : '' }}>
                                        <option value="{{ $country->id }}" {{ ($billingDetails->country_id ?? null) == $country->id ? 'selected' : 'hidden' }}>
                                            {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>

                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Assigned Sales Person / Portal Handler -->
            <h4 class="h5 font-weight-bold mt-4 text-dark">Assigned Sales Person / Portal Handler</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-dark text-white text-center">
                    <tr>
                        <th>Sales Person</th>
                        <th>Registration Source</th>
                        <th>Application Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            @php
                                $salesPerson = $application->salesPerson ?? null;
                                if (empty($salesPerson) || $salesPerson === 'null' || $salesPerson === '') {
                                    $salesPersonDisplay = 'Not Assigned';
                                } else {
                                    $salesPersonDisplay = $salesPerson;
                                }
                            @endphp
                            <input type="text" name="salesPerson" value="{{ $salesPersonDisplay }}" class="form-control" readonly>
                        </td>
                        <td>
                            @php
                                $regSource = $application->RegSource ?? null;
                                if (empty($regSource) || $regSource === 'null' || $regSource === '') {
                                    $regSourceDisplay = 'Not Provided';
                                } else {
                                    $regSourceDisplay = $regSource;
                                }
                            @endphp
                            <input type="text" name="RegSource" value="{{ $regSourceDisplay }}" class="form-control" readonly>
                        </td>
                        <td>
                            @php
                                $createdByUser = $application->user ?? null;
                                if ($createdByUser) {
                                    $createdByDisplay = $createdByUser->name . ' (' . $createdByUser->email . ')';
                                } else {
                                    $createdByDisplay = 'N/A';
                                }
                            @endphp
                            <input type="text" value="{{ $createdByDisplay }}" class="form-control" readonly>
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

