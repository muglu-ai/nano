    @extends('layouts.dashboard')
@section('title', ucfirst($slug))
@section('content')

    <style>

        th {
            text-align: left !important;
            padding-left:8px !important;
        }
        .badge-fixed {
            display: inline-block;
            width: 120px; /* Adjust based on your design */
            height: 40px; /* Adjust as needed */
            text-align: center;
            line-height: 30px;
            font-size: 14px; /* Ensures text consistency */


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
                                <p class="text-sm mb-0 text-dark">
                                    List of all {{$slug}}.
                                </p>
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
                    <div class="card-body px-0 pb-0">
                        <div class="table-responsive">
                            <table class="table table-flush" id="datatable-basic">
                                <thead class="thead-light table-dark">
                                <tr>
                                    @php
                                        $status = explode(' - ', trim($slug))[0];
                                    @endphp
                                    <th class="text-start text-uppercase text-white text-wrap" style="min-width: 150px;">Company Name</th>
                                    <th class=" text-uppercase text-white" style="min-width: 150px;">Country</th>
                                    <th class="text-uppercase text-white text-wrap">Requested Booth Size <br> (in sqm)</th>
                                    <th class="text-uppercase text-white text-wrap">Preferred Location</th>
                                    <th class="text-uppercase text-white text-wrap">Stall Type</th>
                                    <th class="text-uppercase text-white text-wrap">Semi Member</th>
                                    <th class=" text-uppercase text-secondary text-xs font-weight-bolder text-wrap " style="min-width: 150px;">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0  text-uppercase text-white text-wrap">Name</h6>
                                            <p class="text-xs text-secondary mb-0 text-uppercase text-dark text-white" style="font-weight: bold;">JOB TITLE</p>
                                        </div>
                                    </th>
                                    <th class=" text-uppercase text-secondary text-xs font-weight-bolder text-wrap">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0  text-uppercase text-white">Email</h6>
                                            <p class="text-xs text-secondary mb-0 text-uppercase text-dark text-white" style="font-weight: bold;">Contact No</p>
                                        </div>
                                    </th>
                                    {{--                                    <th>Mobile Number</th>--}}
                                    <th  class=" text-uppercase text-start text-white text-wrap">Date of Submission</th>
                                    <th class=" text-uppercase text-center text-white">Status</th>
                                    {{--                                    @if($status != 'in progress')--}}
                                    <th class=" text-uppercase text-center text-white">Action</th>
                                    {{--                                    @endif--}}
                                </tr>
                                </thead>
                                <tbody>
                                @if($applications->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center">No applications found.</td>
                                    </tr>
                                @else
                                    @foreach ($applications as $application)
                                        <tr>
                                            <td class="custom-td" style="min-width: 80px; word-wrap: break-word;">
                                                <div class="d-flex flex-column" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 150px;">
                                                    <p class="mb-0 text-md text-dark"> <a class="text-md text-info " href="{{ route('application.view', ['application_id' => $application->application_id]) }}">
                                                            {{ $application->company_name ?? '' }}
                                                        </a>
                                                    </p>
                                                </div>
                                            </td>
                                            <td class="custom-td text-start">
                                                <div class="d-flex px-2 py-1 text-start">
                                                    <div class="d-flex flex-column" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 2000px;" >
                                                        <p class="mb-0 text-md text-dark text-start" >{{ $application->country->name ?? '' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-start custom-td">
                                                <div class="d-flex px-2 py-1 text-start">
                                                    <div class="d-flex flex-column">
                                                        <p class="mb-0 text-md text-dark">{{ $application->interested_sqm ?? 0 }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="custom-td">
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column" >
                                                        <p class="mb-0 text-md text-dark">{{ $application->pref_location ?? ''}}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="custom-td">
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column" >
                                                        <p class="mb-0 text-md text-dark">{{ $application->stall_category ?? 'N/A'}}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="custom-td">
                                                <div class="d-flex flex-column" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 200px;" >
                                                    <p class="mb-0 text-md text-dark">{{ $application->semi_member == 1 ? 'Y' : 'N' }}</p>
                                                    @if(($application->semi_member == 1))
                                                        <p class="text-md text-secondary mb-0">{{ $application->semi_memberID }}</p>
                                                        @if($application->membership_verified == 1)
                                                            <i class="material-symbols-rounded text-success" data-bs-toggle="tooltip" data-bs-original-title="Membership Verified">verified</i>
                                                        @else
                                                            <i class="material-symbols-rounded text-danger" data-bs-toggle="tooltip" data-bs-original-title="Membership Not Verified">cancel</i>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                            <td class=" custom-td">
                                                <div class="d-flex flex-column " style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 180px;">
                                                    <p class="mb-0 text-md text-dark">{{ $application->eventContact->first_name ?? '' }} {{ $application->eventContact->last_name ?? '' }}</p>
                                                    <p class="text-md text-secondary mb-0">{{  $application->eventContact->job_title ?? '' }}</p>
                                                </div>
                                            </td>
                                            <td class="align-middle custom-td text-sm">
                                                <p class="text-md text-dark  mb-0">{{ $application->eventContact->email ?? '' }}</p>
                                                <p class="text-md text-dark">{{ $application->eventContact->contact_number ?? '' }}</p>
                                            </td>
                                            <td class="align-left custom-td  text-md">
                                                <span
                                                    class="text-md text-dark">{{ $application->submission_date ?? '' }}</span>
                                            </td>

                                            <td class=" text-sm text-center custom-td">
                                                <span class=" badge d-block w-72
                                                    {{ $application->submission_status === 'in progress' ? 'badge-secondary' :
                                                    ($application->submission_status === 'submitted' ? 'badge-warning' :
                                                    ($application->submission_status === 'pending' ? 'badge-danger' :
                                                    ($application->submission_status === 'approved' ? 'badge-success' :
                                                    ($application->submission_status === 'rejected' ? 'badge-danger' :
                                                    'badge-dark')))) }}">
                                                    {{ $application->submission_status }}
                                                </span>
{{--                                                @if ($application->application_status === 'submitted')--}}
{{--                                                    <span--}}
{{--                                                        class="badge badge-xs badge-danger badge-fixed text-center">{{ $application->submission_status }} </span>--}}
{{--                                                @elseif ($application->application_status === 'in progress')--}}
{{--                                                    <span--}}
{{--                                                        class="mt-3 badge badge-xs bg-gradient-primary badge-fixed text-center ">{{ $application->submission_status }}</span>--}}
{{--                                                @else--}}
{{--                                                    <span--}}
{{--                                                        class="badge badge-xs bg-gradient-warning badge-fixed text-center">{{ $application->submission_status }} </span>--}}
{{--                                                @endif--}}
                                            </td>
                                            @if($application->submission_status != 'in progress')
                                                <td class="text-md align-content-start ">
                                                    <div class="d-flex flex-column align-content-start">
                                                        <button type="submit" data-bs-toggle="tooltip"
                                                                data-bs-original-title="Follow Up Application"
                                                                style="border:none; background:none; padding:0; margin-top: 5px;"
                                                                class="align-content-start"
                                                                onclick="showModifiedModal('{{ $application->id }}', '{{ $application->stall_category }}', {{ $application->interested_sqm }}, {{ $application->interested_sqm }}, '{{ $application->pref_location }}', '{{ $application->stall_category }}')">
                                                                <i class="fa-solid fa-person-walking "></i> Follow Up
                                                        </button>
                                                        <button type="submit" data-bs-toggle="tooltip"
                                                                data-bs-original-title="View Application"
                                                                style="border:none; background:none; padding:0; margin-top: 5px; padding-right: 30px; gap:5px;"
                                                                onclick="window.location.href='/applicationView?application_id={{ $application->application_id }}'">
                                                            <i class="material-symbols-rounded text-secondary position-relative text-lg">visibility</i> View
                                                        </button>

                                                        @if($application->submission_status == 'submitted')
                                                            <button type="submit" data-bs-toggle="tooltip"
                                                                    data-bs-original-title="Verify Membership"
                                                                    style="border:none; background:none; padding:0; margin-top: 5px; padding-right: 30px; gap:5px;"
                                                                    onclick="verifyMembership('{{ $application->application_id }}', '{{ $application->semi_memberID }}')">
                                                                <i class="material-symbols-rounded text-secondary position-relative text-lg">verified</i> Verify Membership
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            @else
                                                <td>
                                                    <button type="submit" data-bs-toggle="tooltip"
                                                            data-bs-original-title="Send Reminder"
                                                            style="border:none; background:none; padding:0;"
                                                            onclick="sendReminder('{{ $application->application_id }}','{{$application->billingDetail->email}}', 'reminder')">
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
                function sendReminder(applicationId, email, emailType) {
                    fetch('{{route('send.email')}}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            application_id: applicationId,
                            to: email,
                            email_type: emailType
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

                function showModifiedModal(application_id, stallCategory, requestSqm, allocateSqm, prefLocation, booth_cat) {
                    Swal.fire({
                        title: 'Application Details',
                        html: `
            <form id="applicationForm">
                <div style="text-align: left;">
                    <label for="requestSqm" style="display: block; margin-bottom: 5px;">Requested ${stallCategory} Booth Size in Sqm:</label>
                    <input type="text" id="requestSqm" class="swal2-input" value="${requestSqm}" disabled>

                    <label for="allocateSqm" style="display: block; margin-top: 5px;">Allocate Sqm:</label>
                    <input type="number" id="allocateSqm" class="swal2-input" value="${allocateSqm}" placeholder="Enter sqm">

                    <label for="stallNumber" style="display: block; margin-top: 5px;">Stall Number:</label>
                    <input type="text" id="stallNumber" class="swal2-input" value="" placeholder="Enter Stall Number">

                    <label for="boothType" style="display: block; margin-top: 5px;">Booth Type:</label>
                    <div id="boothType" class="swal2-radio">
                        <label>
                            <input type="radio" name="boothType" value="Premium" ${prefLocation === 'Premium' ? 'checked' : ''}> Premium
                        </label>
                        <label>
                            <input type="radio" name="boothType" value="Standard" ${prefLocation === 'Standard' ? 'checked' : ''}> Standard
                        </label>
                    </div>
                    <label for="boothType" style="display: block; margin-top: 5px;">Booth Category:</label>
                    <div id="boothType" class="swal2-radio">
                        <label>
                            <input type="radio" name="booth_cat" value="Bare Space" ${booth_cat === 'Bare Space' ? 'checked' : ''}> Bare Space
                        </label>
                        <label>
                            <input type="radio" name="booth_cat" value="Shell Scheme" ${booth_cat === 'Shell Scheme' ? 'checked' : ''}> Shell Scheme
                        </label>
                    </div>

                    <input type="hidden" id="isPavilion" class="swal2-checkbox">
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
                            const stallNumber = form.querySelector('#stallNumber').value;
                            const selectedBoothType = document.querySelector('input[name="boothType"]:checked')?.value || '';
                            const selectedBoothCat = document.querySelector('input[name="booth_cat"]:checked')?.value || '';
                            if (!selectedBoothType) {
                                Swal.showValidationMessage('Please select a Booth Type');
                                return false;
                            }
                            if (!selectedBoothCat) {
                                Swal.showValidationMessage('Please select a Booth Category');
                                return false;
                            }

                            if (!stallNumber) {
                                Swal.showValidationMessage('Please enter a valid Stall Number');
                                return false;
                            }

                            // if (!allocateSqmValue || allocateSqmValue <= 0) {
                            //     Swal.showValidationMessage('Please enter a valid Allocate Sqm');
                            //     return false;
                            // }

                            

                            return {
                                allocateSqm: allocateSqmValue,
                                isPavilion: isPavilionChecked,
                                stallNumber: stallNumber,
                                boothType: selectedBoothType,
                                booth_cat: selectedBoothCat
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Please wait while we submit your request.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

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
                                    stallNumber: result.value.stallNumber,
                                    boothType: result.value.boothType,
                                    booth_cat: result.value.booth_cat
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
                                        return false;
                                    }
                                    return reason;
                                }
                            }).then((rejectResult) => {
                                if (rejectResult.isConfirmed) {
                                    Swal.fire({
                                        title: 'Processing...',
                                        text: 'Submitting your rejection...',
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });

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

            <script>
                function verifyMembership(applicationId, semiMemberID) {
                    Swal.fire({
                        title: 'Verify Membership',
                        text: "Please confirm to membership ID to verify " + semiMemberID,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Verify',
                        cancelButtonText: 'Reject',
                        customClass: {
                            confirmButton: 'btn bg-gradient-success',
                            cancelButton: 'btn bg-gradient-danger'
                        },
                        preConfirm: () => {
                            return {
                                action: 'verify'
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('/membership/verify', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    application_id: applicationId,
                                    semi_memberID: semiMemberID
                                })
                            })
                                .then(response => response.json())
                                .then(data => {
                                    Swal.fire('Success', 'Membership verified successfully!', 'success').then(() => {
                                        location.reload();
                                    });
                                })
                                .catch(error => {
                                    Swal.fire('Error', 'Verification failed!', 'error');
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
                                    fetch('/membership/reject', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            application_id: applicationId,
                                            reason: rejectResult.value
                                        })
                                    })
                                        .then(response => response.json())
                                        .then(data => {
                                            Swal.fire('Rejected', 'Membership rejected successfully!', 'error').then(() => {
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
