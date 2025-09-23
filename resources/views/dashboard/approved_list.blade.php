@extends('layouts.dashboard')
@section('title', ucfirst($slug))
@section('content')

    <style>
        thead.custom-header {
            background-color: #000; /* Light gray */
            color: #fff; /* Dark text */
        }
        th {
            text-align: left !important;
            padding-left:8px !important;
        }
        .custom-td {
            text-align: start !important;
            padding-left: 8px !important;
        }

    </style>
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">All {{$slug}}</h5>
                                <p class="text-md mb-0 text-dark">
                                    List of all {{$slug}}.
                                </p>
                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                            </div>
                            <div class="d-flex align-items-center gap-3 flex-nowrap">
                                <form action="{{ route('export.applications') }}" method="GET" class="d-flex align-items-center gap-3">
                                    <select name="status" id="statusSelect" class="form-select" aria-label="Status" style="min-width: 180px;">
                                        @php
                                            $statusOptions = [
                                                'all' => 'All Application',
                                                'initiated' => 'In progress',
                                                'submitted' => 'Submitted',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected'
                                            ];
                                            // Strip any unwanted characters from $slug to ensure correct comparison
                                            $currentStatus = trim(str_replace(' - Application List', '', $slug));
                                        @endphp

                                        @foreach ($statusOptions as $value => $label)
                                            <option value="{{ $value }}" @selected($currentStatus === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-info text-nowrap px-4 pt-2 ps-2">Export</button>
                                </form>
                            </div>
                            <script>
                                document.getElementById('statusSelect').addEventListener('change', function() {
                                    var selectedStatus = this.value;
                                    var baseUrl = "{{ url('application-list') }}";

                                    if (selectedStatus === 'all') {
                                        window.location.href = baseUrl; // Redirect to /application-list
                                    } else if (selectedStatus === 'initiated') {
                                        window.location.href = baseUrl + "/in-progress"; // Redirect to /application-list/in-progress
                                    } else {
                                        window.location.href = baseUrl + "/" + selectedStatus; // Redirect to /application-list/{status}
                                    }
                                });
                            </script>
                        </div>
                    </div>
                        </div>
                    </div>




                    <div class="card-body px-0 pb-0">
                        <div class="table-responsive min-vh-40">
                            <!-- Total Revenue small card -->
                            <div class="card card-body shadow-sm border-0 rounded-3 mt-4 mb-4 bg-light">
                                <div class="row align-items-center">
                                    <div class="col-md-6 col-sm-12">
                                        <h5 class="mb-2 text-primary fw-semibold">Total Revenue</h5>
                                        @php
                                            function indianFormat($number) {
                                                $number = (string) $number;
                                                $afterPoint = '';
                                                if (strpos($number, '.') !== false) {
                                                    list($number, $afterPoint) = explode('.', $number);
                                                    $afterPoint = '.' . $afterPoint;
                                                }

                                                $lastThree = substr($number, -3);
                                                $restUnits = substr($number, 0, -3);
                                                if ($restUnits != '') {
                                                    $lastThree = ',' . $lastThree;
                                                }
                                                $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
                                                return $restUnits . $lastThree . $afterPoint;
                                            }
                                        @endphp

                                        <p class="fs-4 fw-bold text-dark mb-0">
                                            ₹{{ indianFormat($totalRevenue) }}
                                            {{-- @if($totalRevenue >= 10000000)
                                                ({{ round($totalRevenue / 10000000, 2) }} Cr)
                                            @elseif($totalRevenue >= 100000)
                                                ({{ round($totalRevenue / 100000, 2) }} Lakh)
                                            @elseif($totalRevenue >= 1000)
                                                ({{ round($totalRevenue / 1000, 2) }} Thousand)
                                            @endif --}}
                                        </p>

                                    </div>
                                </div>
                            </div>
                            

                                    
                            <table class="table table-flush" id="datatable-basic">
                                <thead class="thead-light table-dark">
                                <tr>
                                    @php
                                        $status = explode(' - ', trim($slug))[0];
                                    @endphp
                                    {{--                                    {{ $status }}--}}
                                    {{--                                    <th>Application No</th>--}}

                                     <th class="text-uppercase text-white text-wrap">Registration Date</th>
                                    <th class="text-uppercase text-white text-wrap">Company Name</th>
{{--                                    <th class="text-uppercase text-white text-wrap text-start">Country</th>--}}
{{--                                    <th class="text-uppercase text-white text-wrap">Allocated Booth Size (in sqm) </th>--}}
                                     <th class="text-uppercase  text-xs font-weight-bolder text-white" style="max-width: 180px;">
                                        <div class="d-flex flex-column " >
                                            <span class=" text-uppercase text-dark text-white">Stall Type   </span>

                                            <p class="text-xs text-uppercase  mb-0 text-dark text-white">Stall Size</p>
                                        </div>
                                    </th>
                                    {{-- <th class="text-uppercase text-white text-wrap">Preferred Location</th>
                                    <th class="text-uppercase text-white text-wrap">Stall Type</th> --}}
{{--                                    <th class="text-uppercase text-white text-wrap">Semi Member</th>--}}
{{--                                    <th class="text-uppercase text-white text-wrap">Booth Number</th>--}}
                                    <th class="text-uppercase  text-xs font-weight-bolder text-white" style="max-width: 180px;">
                                        <div class="d-flex flex-column " >
                                            <span class=" text-uppercase text-dark text-white">Contact Name   </span>

                                            <p class="text-xs text-uppercase  mb-0 text-dark text-white">Job Title</p>
                                        </div>
                                    </th>
                                    <th class="text-uppercase  text-xs font-weight-bolder ">
                                        <div class="d-flex flex-column ">
                                            <h6 class="mb-0  text-white">Contact Email</h6>
                                            <p class="text-xs  mb-0 text-dark text-white">Contact No</p>
                                        </div>
                                    </th>
{{--                                     <th  class=" text-uppercase text-start text-white text-wrap">Date of Submission</th>--}}
                                    <th class="text-uppercase  text-xs font-weight-bolder ">
                                        <div class="d-flex flex-column ">
                                            <h6 class="mb-0  text-white">Price</h6>
                                            <p class="text-xs  mb-0 text-dark text-white">(Excl. Tax)</p>
                                        </div>
                                    </th>
                                    {{--                                    <th>Mobile Number</th>--}}
                                    {{-- <th class="text-uppercase text-white text-wrap">Price (Excl. Tax)</th> --}}
                                    <th class="text-uppercase text-white text-wrap">Paid Amount</th>
                                    {{--                                    @if($status != 'in progress')--}}
                                    <th class="text-uppercase text-center text-white ">Action</th>
                                    {{--                                    @endif--}}
                                </tr>
                                </thead>
                                <tbody>
                                @if($applications->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center">No applications found.</td>
                                    </tr>
                                @else
                                @php
                                // $total = 0;
                                @endphp
                                    @foreach ($applications as $application)
                                        {{-- @php
                                            $total += $application->invoice->price ?? 0;
                                        @endphp --}}
                                        {{-- Total Revenue -- }}
                                        
                                        {{--                                        @dd($application->invoice->amount, $application->invoice->amount_paid) --}}
{{--                                        @dd($application->invoices, $application->id)--}}
                                        <tr>
                                            {{-- Add registration date --}}
                                            <td class="align-left custom-td  text-md">
                                                <span
                                                    class="text-md text-dark">{{ $application->approved_date ? \Carbon\Carbon::parse($application->approved_date)->format('d M, Y') : '' }}</span>
                                            </td>
                                            <td class="custom-td" style="min-width: 80px; word-wrap: break-word;">
                                                <div class="d-flex flex-column" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 150px;">
                                                    <p class="mb-0 text-md text-dark"> <a class="text-md text-info " target="_blank" href="{{ route('application.view', ['application_id' => $application->application_id]) }}">
                                                        {{ $application->company_name }}
                                                        </a>
                                                    </p>
                                                </div>
                                            </td>
{{--                                            <td class="custom-td text-start">--}}
{{--                                                <div class="d-flex px-2 py-1 text-start">--}}
{{--                                                    <div class="d-flex flex-column"  >--}}
{{--                                                        <p class="mb-0 text-md text-dark text-start" >{{ $application->country->name }}</p>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </td>--}}
{{--                                            <td class="text-start custom-td">--}}
{{--                                                <div class="d-flex px-2 py-1 text-start">--}}
{{--                                                    <div class="d-flex flex-column">--}}
{{--                                                        <p class="mb-0 text-md text-dark">{{ $application->allocated_sqm}}</p>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </td>--}}
                                            <td class="custom-td">
                                                <div class="d-flex flex-column text-start" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 200px;" >
                                                    <p class="mb-0 text-md text-dark">{{ $application->stall_category ?? 'N/A' }} </p>
                                                    <p class="text-md text-secondary mb-0">{{  $application->allocated_sqm }}</p>
                                                </div>
                                            </td>
                                            {{-- <td class="custom-td">
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column" >
                                                        <p class="mb-0 text-md text-dark">{{ $application->pref_location}}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="custom-td">
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column" >
                                                        <p class="mb-0 text-md text-dark">{{ $application->stall_category ?? 'N/A'}}</p>
                                                    </div>
                                                </div>
                                            </td> --}}
                                            {{-- <td class="custom-td">
                                                <div class="d-flex flex-column" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 200px;" >
                                                    <p class="mb-0 text-md text-dark">{{ $application->semi_member == 1 ? 'Y' : 'N' }}</p>
                                                    @if(($application->semi_member == 1))
                                                        <p class="text-md text-secondary mb-0">{{ $application->semi_memberID }}</p>
                                                    @endif
                                                </div>
                                            </td> --}}



{{--                                            <td class="custom-td">--}}
{{--                                                <div class="d-flex px-2 py-1">--}}
{{--                                                    <div class="d-flex flex-column" >--}}
{{--                                                        <p class="mb-0 text-md text-dark">{{ $application->stallNumber}}</p>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </td>--}}
                                            <td class="custom-td">
                                                <div class="d-flex flex-column text-start" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 200px;" >
                                                    <p class="mb-0 text-md text-dark">{{ $application->eventContact->first_name }} {{ $application->eventContact->last_name }}</p>
                                                    <p class="text-md text-secondary mb-0">{{  $application->eventContact->job_title }}</p>
                                                </div>
                                            </td>

                                            <td class="align-middle custom-td  text-md" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 220px;">
                                                <p class="text-md text-secondary mb-0 text-dark">{{ $application->eventContact->email }}</p>
                                                <p class="text-secondary text-md font-weight-bold">{{ $application->eventContact->contact_number }}</p>
                                            </td>
{{--                                             <td class="align-left custom-td  text-md">--}}
{{--                                                <span--}}
{{--                                                    class="text-md text-dark">{{ $application->submission_date ?? '' }}</span>--}}
{{--                                            </td>--}}
{{--                                            @dd($application->invoice->amount, $application->invoice->amount_paid,$application->invoice->id )--}}
{{--                                            <td class="align-middle  text-md">--}}
{{--                                                <span--}}
{{--                                                    class="text-secondary text-md font-weight-bold">{{ $application->invoice->amount ?? 0 }}--}}
{{--                                                </span>--}}
{{--                                            </td>--}}
                                            <td class="align-middle  text-md">
                                                <span
                                                    class="text-secondary text-md font-weight-bold" > INR  {{ $application->invoice->price ?? 0 }}
                                                </span>
                                            </td>
                                            <td class="align-middle  text-md">
                                                <span
                                                    class="text-secondary text-md font-weight-bold"> INR  {{ $application->invoice->amount_paid ?? 0 }}
                                                </span>
                                            </td>
                                            @if($status != 'in progress')
                                                <td class=" text-md custom-td " >
                                                    <div class="d-flex flex-column ">
{{--                                                        <button type="submit" data-bs-toggle="tooltip"--}}
{{--                                                                data-bs-original-title="Follow Up Application"--}}
{{--                                                                style="border:none; background:none; padding:0; margin-top: 5px;"--}}
{{--                                                                onclick="showModifiedModal('{{ $application->id }}', '{{ $application->stall_category }}', {{ $application->interested_sqm }}, {{ $application->interested_sqm }})">--}}
{{--                                                            <i class="fa-solid fa-person-walking"></i> Follow Up--}}
{{--                                                        </button>--}}
                                                        <button type="submit" data-bs-toggle="tooltip"
                                                                data-bs-original-title="View Application"
                                                                style="border:none; background:none; padding:0; margin-top: 5px;"
                                                                onclick="window.location.href='/applicationView?application_id={{ $application->application_id }}'">
                                                            <i class="material-symbols-rounded text-secondary position-relative text-lg">visibility</i>View
                                                        </button>
                                                        <button type="submit" data-bs-toggle="tooltip"
                                                                data-bs-original-title="Send Reminder"
                                                                style="border:none; background:none; padding:0; margin-top: 5px;"
                                                                onclick="sendReminder('{{ $application->application_id }}', '{{ $application->eventContact->email }}')">
                                                            <i class="material-symbols-rounded text-secondary position-relative text-lg">notifications</i>Send Reminder
                                                        </button>
{{--                                                        <button type="submit" data-bs-toggle="tooltip"--}}
{{--                                                                data-bs-original-title="Delete Application"--}}
{{--                                                                style="border:none; background:none; padding:0; margin-top: 5px;"--}}
{{--                                                                onclick="deleteApplication('{{ $application->id }}')">--}}
{{--                                                            <i class="material-symbols-rounded text-secondary position-relative text-lg">delete</i>Delete--}}
{{--                                                        </button>--}}
                                                    </div>
                                                </td>
                                            @else
                                                <td class="custom-td">
                                                    <button type="submit" data-bs-toggle="tooltip"
                                                            data-bs-original-title="Send Reminder"
                                                            style="border:none; background:none; padding:0;"
                                                            onclick="sendReminder('{{ $application->id }}')">
                                                        <i class="material-symbols-rounded text-secondary position-relative text-lg">notifications</i>Send
                                                        Reminder
                                                    </button>
                                                </td>
                                            @endif

                                        </tr>
                                    @endforeach
                                @endif

                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

            <script>
                function sendReminder(applicationId, email) {
                    fetch('{{route('send.email')}}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            application_id: applicationId,
                            to: email,
                            email_type : 'invoice'
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Success', 'Reminder sent successfully!', 'success');
                        })
                        .catch(error => {
                            Swal.fire('Error', 'Failed to send reminder!', 'error');
                        });
                }
                // Function to show the modified modal
{{--                function showModifiedModal(application_id, stallCategory, requestSqm, allocateSqm) {--}}
{{--                    Swal.fire({--}}
{{--                        title: 'Application Details',--}}
{{--                        html: `--}}
{{--                <form id="applicationForm">--}}
{{--                    <div style="text-align: left;">--}}
{{--                        <label for="requestSqm" style="display: block; margin-bottom: 5px;">Requested ${stallCategory} Booth Size in Sqm:</label>--}}
{{--                        <input type="text" id="requestSqm" class="swal2-input" value="${requestSqm}" disabled>--}}

{{--                        <label for="allocateSqm" style="display: block; margin-bottom: 5px;">Allocate Sqm:</label>--}}
{{--                        <input type="number" id="allocateSqm" class="swal2-input" value="${allocateSqm}" placeholder="Enter sqm">--}}

{{--<!--                        <label for="isPavilion" style="display: block; margin-top: 10px; margin-bottom: 5px;">Is Pavilion:</label>-->--}}
{{--                        <input type="hidden" id="isPavilion" class="swal2-checkbox">--}}
{{--<!--                        <input type="checkbox hidden" id="isPavilion" class="swal2-checkbox">-->--}}
{{--                        <label for="isPavilion" style="display: inline;">No</label>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            `,--}}
{{--                        confirmButtonText: 'Submit',--}}
{{--                        customClass: {--}}
{{--                            confirmButton: 'btn bg-gradient-success',--}}
{{--                        },--}}
{{--                        preConfirm: () => {--}}
{{--                            const form = document.getElementById('applicationForm');--}}
{{--                            const allocateSqmValue = form.querySelector('#allocateSqm').value;--}}
{{--                            const isPavilionChecked = form.querySelector('#isPavilion').checked;--}}


{{--                            if (!allocateSqmValue || allocateSqmValue <= 0) {--}}
{{--                                Swal.showValidationMessage('Please enter a valid Allocate Sqm');--}}
{{--                            }--}}

{{--                            return {--}}
{{--                                allocateSqm: allocateSqmValue,--}}
{{--                                isPavilion: isPavilionChecked,--}}
{{--                            };--}}
{{--                        },--}}
{{--                    }).then((result) => {--}}
{{--                        if (result.isConfirmed) {--}}
{{--                            fetch('/application/submit', {--}}
{{--                                method: 'POST',--}}
{{--                                headers: {--}}
{{--                                    'Content-Type': 'application/json',--}}
{{--                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'--}}

{{--                                },--}}
{{--                                body: JSON.stringify({--}}
{{--                                    id: application_id,--}}
{{--                                    stallCategory: stallCategory,--}}
{{--                                    requestSqm: requestSqm,--}}
{{--                                    allocateSqm: result.value.allocateSqm,--}}
{{--                                    isPavilion: result.value.isPavilion,--}}
{{--                                })--}}
{{--                            })--}}

{{--                                .then(response => response.json())--}}
{{--                                .then(data => {--}}
{{--                                    Swal.fire('Success', data.message + ' ' + data.company_name, 'success', 2000).then(() => {--}}
{{--                                        location.reload();--}}
{{--                                    });--}}
{{--                                })--}}
{{--                                .catch(error => {--}}
{{--                                    Swal.fire('Error', 'Submission failed!', 'error');--}}
{{--                                });--}}
{{--                        }--}}
{{--                    });--}}

{{--                    // Set default state of the Is Pavilion slider to "No"--}}
{{--                    const isPavilion = document.getElementById('isPavilion');--}}
{{--                    isPavilion.checked = false;--}}
{{--                }--}}

                function showModifiedModal(application_id, stallCategory, requestSqm, allocateSqm) {
                    Swal.fire({
                        title: 'Application Details',
                        html: `
            <form id="applicationForm">
                <div style="text-align: left;">
                    <label for="requestSqm" style="display: block; margin-bottom: 5px;">Requested ${stallCategory} Booth Size in Sqm:</label>
                    <input type="text" id="requestSqm" class="swal2-input" value="${requestSqm}" disabled>

                    <label for="allocateSqm" style="display: block; margin-bottom: 5px;">Allocate Sqm:</label>
                    <input type="number" id="allocateSqm" class="swal2-input" value="${allocateSqm}" placeholder="Enter sqm">

                    <input type="hidden" id="isPavilion" class="swal2-checkbox">
<!--                    <label for="isPavilion" style="display: inline;">No</label>-->
                </div>
            </form>
        `,
                        showCancelButton: true,
                        confirmButtonText: 'Submit',
                        cancelButtonText: 'Reject',
                        customClass: {
                            confirmButton: 'btn bg-gradient-success',
                            cancelButton: 'btn bg-gradient-danger'
                        },
                        preConfirm: () => {
                            const form = document.getElementById('applicationForm');
                            const allocateSqmValue = form.querySelector('#allocateSqm').value;
                            const isPavilionChecked = form.querySelector('#isPavilion').checked;

                            if (!allocateSqmValue || allocateSqmValue <= 0) {
                                Swal.showValidationMessage('Please enter a valid Allocate Sqm');
                            }

                            return {
                                allocateSqm: allocateSqmValue,
                                isPavilion: isPavilionChecked,
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('/application/submit', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    id: application_id,
                                    stallCategory: stallCategory,
                                    requestSqm: requestSqm,
                                    allocateSqm: result.value.allocateSqm,
                                    isPavilion: result.value.isPavilion,
                                })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.fire('Success', data.message + ' ' + data.company_name, 'success').then(() => {
                                        location.reload();
                                    });
                                })
                                .catch(error => {
                                    Swal.fire('Error', 'Submission failed!', 'error');
                                });
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            Swal.fire({
                                title: 'Rejection Reason',
                                input: 'textarea',
                                inputPlaceholder: 'Enter reason for rejection...',
                                showCancelButton: true,
                                confirmButtonText: 'Submit',
                                cancelButtonText: 'Cancel',
                                preConfirm: (reason) => {
                                    if (!reason) {
                                        Swal.showValidationMessage('Please provide a reason for rejection');
                                    }
                                    return reason;
                                }
                            }).then((rejectResult) => {
                                if (rejectResult.isConfirmed) {
                                    fetch('/application/reject', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            id: application_id,
                                            reason: rejectResult.value
                                        })
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            Swal.fire('Rejected', data.message, 'error').then(() => {
                                                location.reload();
                                            });
                                        })
                                        .catch(error => {
                                            Swal.fire('Error', 'Rejection submission failed!', 'error');
                                        });
                                }
                            });
                        }
                    });
                }

            </script>



@endsection
