@extends('layouts.dashboard')
@section('title', ucfirst($slug))
@section('content')
    <style>
        .badge {
            font-size: 0.2rem;
            padding: 0.25rem 0.50rem;
            border-radius: 0.30rem;
        }


        th {
            text-align: left !important;
            padding-left: 8px !important;
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
                    <div class="card-header pb-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="d-flex flex-column">
                                <h5 class="mb-0">All {{ $slug }}</h5>
                                <p class="text-sm mb-0 text-dark">List of all {{ $slug }}.</p>
                            </div>
                            {{--                            {{ //str_replace(' - Sponsor Application List', '', $slug) }} --}}

                            <div class="d-flex  gap-3 flex-nowrap">
                                <form action="{{ route('export.sponsorships') }}" method="GET"
                                    class="d-flex align-items-center gap-3">
                                    <select name="status" id="statusSelect" class="form-select" aria-label="Status"
                                        style="min-width: 180px;">
                                        @php
                                            $statusOptions = [
                                                'all' => 'All Application',
                                                'initiated' => 'In progress',
                                                'submitted' => 'Submitted',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                            ];
                                            // Strip any unwanted characters from $slug to ensure correct comparison
                                            $currentStatus = trim(
                                                str_replace(' - Sponsor Application List', '', $slug),
                                            );
                                        @endphp

                                        @foreach ($statusOptions as $value => $label)
                                            <option value="{{ $value }}" @selected($currentStatus === $value)>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-info text-nowrap px-4  ps-2 mt-2">Export</button>
                                </form>
                            </div>
                            <script>
                                document.getElementById('statusSelect').addEventListener('change', function() {
                                    var selectedStatus = this.value;
                                    var baseUrl = "{{ url('sponsorship-list') }}";

                                    if (selectedStatus === 'all') {
                                        window.location.href = baseUrl; // Redirect to /application-list
                                    } else if (selectedStatus === 'initiated') {
                                        window.location.href = baseUrl + "/initiated"; // Redirect to /application-list/in-progress
                                    } else {
                                        window.location.href = baseUrl + "/" + selectedStatus; // Redirect to /application-list/{status}
                                    }
                                });
                            </script>


                        </div>
                        <div class="card-body px-0 pb-0">
                            <div class="table-responsive">
                                <table class="table table-flush" id="datatable-basic">
                                    <thead class="thead-light table-dark">
                                        <tr>
                                            <th class=" text-uppercase text-white">Company Name <br> <br></th>
                                            <th class=" text-uppercase  text-white  ">Sponsor Item<br> <br></th>
                                            <th class=" text-uppercase  text-white ">Sponsor Price<br> <br></th>
                                            <th class=" text-uppercase  text-white ">Quantity<br> <br></th>
                                            <th class=" text-uppercase  text-white ">Country<br> <br></th>
                                            <th class="text-uppercase text-white text-wrap">Semi Member</th>
                                            <th class=" text-uppercase  text-white  text-sm font-weight-bolder table-dark">
                                                <div class="d-flex flex-column table-dark">
                                                    <h6 class="mb-0 text-uppercase text-white" style="padding-bottom: 10px">
                                                        Name</h6>
                                                    <p class=" mb-0 text-uppercase text-dark text-xs text-white"
                                                        style="font-weight: bold;">JOB TITLE</p>
                                                </div>
                                            </th>
                                            <th class=" text-uppercase  text-white text-sm font-weight-bolder ">
                                                <div class="d-flex flex-column table-dark">
                                                    <h6 class="mb-0 text-uppercase table-dark" style="padding-bottom: 10px">
                                                        Email</h6>
                                                    <p class="text-xs  mb-0 text-uppercase text-dark text-white"
                                                        style="font-weight: bold;">Contact No</p>
                                                </div>
                                            </th>
                                            <th class="text-uppercase  text-white">Date of<br> Submission </th>
                                            <th class="text-uppercase  text-white text-center">Status<br> <br></th>
                                            <th class="text-uppercase  text-white" style="  padding-left:22px !important">
                                                Action<br> <br></th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @if ($applications->isEmpty())
                                            <tr>
                                                <td colspan="9" class="text-center ">No applications found.</td>
                                            </tr>
                                        @else
                                            @foreach ($applications as $application)
                                                @foreach ($application->sponsorship as $sponsorships)
                                                    @php

                                                        if (
                                                            $currentStatus !== 'Sponsor Application List' &&
                                                            $sponsorships->status !== $currentStatus
                                                        ) {
                                                            continue;
                                                    } @endphp
                                                    <tr>
                                                        <td class="custom-td">
                                                            <div class="d-flex flex-column ">
                                                                <p class="mb-0 text-md text-dark"
                                                                    style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 150px;">
                                                                    <a class="text-md text-info "
                                                                        href="{{ route('application.view', ['application_id' => $application->application_id]) }}">
                                                                        {{ $application->company_name }}
                                                                    </a>

                                                                </p>
                                                            </div>

                                                        </td>

                                                        <td class="custom-td">
                                                            <div class="d-flex flex-column ">
                                                                <p class="mb-0 text-md text-dark">
                                                                    {{ $sponsorships->sponsorship_item }}
                                                                </p>
                                                            </div>

                                                        </td>
                                                        <td class="custom-td">
                                                            <div class="d-flex flex-columnr">
                                                                <p class="mb-0 text-md text-dark">
                                                                    INR 
                                                                    {{ $sponsorships->price }}
                                                                </p>
                                                            </div>
                                                        </td>
                                                        <td class="custom-td">
                                                            <div class="d-flex flex-columnr">
                                                                <p class="mb-0 text-md text-dark">
                                                                    {{ $sponsorships->sponsorship_item_count }}
                                                                    {{-- {{ $sponsorships->price }} --}}
                                                                </p>
                                                            </div>
                                                        </td>
                                                        <td class="custom-td">

                                                            <div class="d-flex flex-column ">
                                                                <p class="mb-0 text-md text-dark">
                                                                    {{ $application->country->name }}
                                                                </p>
                                                            </div>
                                                        </td>
                                                        <td class="custom-td">
                                                            <div class="d-flex flex-column"
                                                                style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 200px;">
                                                                <p class="mb-0 text-md text-dark">
                                                                    {{ $application->semi_member == 1 ? 'Y' : 'N' }}</p>
                                                                @if ($application->semi_member == 1)
                                                                    <p class="text-md text-secondary mb-0">
                                                                        {{ $application->semi_memberID }}</p>
                                                                    @if ($application->membership_verified == 1)
                                                                        <i class="material-symbols-rounded text-success"
                                                                            data-bs-toggle="tooltip"
                                                                            data-bs-original-title="Membership Verified">verified</i>
                                                                    @else
                                                                        <i class="material-symbols-rounded text-danger"
                                                                            data-bs-toggle="tooltip"
                                                                            data-bs-original-title="Membership Not Verified">cancel</i>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </td>


                                                        <td class="custom-td">
                                                            <div class="d-flex flex-column ">
                                                                <p class="mb-0 text-md text-dark"
                                                                    style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 150px;">
                                                                    {{ $application->eventContact->salutation }}
                                                                    {{ $application->eventContact->first_name }}
                                                                    {{ $application->eventContact->last_name }}</p>

                                                                <p class="text-sm text-secondary mb-0">
                                                                    {{ $application->eventContact->job_title }}</p>
                                                            </div>
                                                        </td>
                                                        <td class="custom-td">
                                                            <div class="d-flex flex-column">
                                                                <p class="mb-0 text-md text-dark">
                                                                    {{ $application->eventContact->email }}</p>
                                                                <p class="text-md text-secondary mb-0">
                                                                    {{ $application->eventContact->contact_number }}</p>
                                                            </div>
                                                        </td>
                                                        <td class="custom-td">{{ $application->submission_date }}</td>
                                                        <td class="custom-td">
                                                            <span
                                                                class=" badge d-block w-72
                                                    {{ $sponsorships->status === 'initiated'
                                                        ? 'badge-secondary'
                                                        : ($sponsorships->status === 'submitted'
                                                            ? 'badge-warning'
                                                            : ($sponsorships->status === 'pending'
                                                                ? 'badge-danger'
                                                                : ($sponsorships->status === 'approved'
                                                                    ? 'badge-success'
                                                                    : ($sponsorships->status === 'rejected'
                                                                        ? 'badge-danger'
                                                                        : 'badge-dark')))) }}">
                                                                {{ $sponsorships->status }}
                                                            </span>

                                                        </td>
                                                        <td class="text-sm custom-td">
                                                            <div class="d-flex flex-column">
                                                                <button type="submit" data-bs-toggle="tooltip"
                                                                    data-bs-original-title="Follow Up Application"
                                                                    style="border:none; background:none; padding:0; margin-top: 5px;"
                                                                    onclick="showModifiedModal('{{ $application->id }}', '{{ $sponsorships->sponsorship_item ?? '' }}', '{{ $sponsorships->price ?? '' }}', '{{ $sponsorships->id ?? '' }}', '{{ $sponsorships->sponsorship_item_count ?? '' }}')">
                                                                    <i class="fa-solid fa-person-walking"></i> Follow Up
                                                                </button>
                                                                {{--                                                    <button --}}
                                                                {{--                                                        type="submit" --}}
                                                                {{--                                                        data-bs-toggle="tooltip" --}}
                                                                {{--                                                        data-bs-original-title="Follow Up Application" --}}
                                                                {{--                                                        onclick="showModifiedModal('{{ $application->id }}', '{{ $sponsorships->sponsorship_item ?? '' }}', '{{ $sponsorships->price ?? '' }}', '{{ $sponsorships->id ?? '' }}')" --}}
                                                                {{--                                                        class="btn btn-sm btn-info" --}}
                                                                {{--                                                        style="border:none; background:none; padding:0; margin-top: 5px;" --}}
                                                                {{--                                                    > --}}
                                                                {{--                                                        <i class="fa-solid fa-person-walking"></i> Follow Up --}}
                                                                {{--                                                    </button> --}}

                                                                @if ($application->submission_status == 'submitted')
                                                                    <button type="submit" data-bs-toggle="tooltip"
                                                                        data-bs-original-title="Verify Membership"
                                                                        style="border:none; background:none; padding:0; margin-top: 5px; padding-right: 30px; gap:5px;"
                                                                        onclick="verifyMembership('{{ $application->application_id }}', '{{ $application->semi_memberID }}')">
                                                                        <i
                                                                            class="material-symbols-rounded text-secondary position-relative text-lg">verified</i>
                                                                        Verify Membership
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach

                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        <script>
            function showModifiedModal(application_id, item, price, sponsorship_id, quantity) {
                //console.log("Opening modal for:", {application_id, item, price, sponsorship_id});

                Swal.fire({
                    title: 'Sponsorship Application Details',
                    html: `
                <form id="applicationForm">
                <div>
                <h3>Sponsorship Details</h3>
                <p><strong>Item:</strong> ${item || 'N/A'}</p>
                <p><strong>Price:</strong> ${price || 'N/A'}</p>
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px; margin-left: 150px;">
                    <label for="quantityInput" style="min-width: 80px;"><strong>Quantity:</strong></label>
                    <input type="number" id="quantityInput" name="quantity" value="${quantity}" class="form-control" min="1" required style="width: 100px;">
                </div>
                </div>
                </form>
                `,
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Reject',
                    preConfirm: () => {
                        //console.log("Submitting application with ID:", application_id);
                        return fetch('/sponsorship/submit', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    id: application_id,
                                    sponsorship_id,
                                    quantity: document.getElementById('quantityInput').value
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log("Response received:", data);
                                Swal.fire('Success', data.message, 'success').then(() => location.reload());
                            })
                            .catch(error => {
                                //console.error("Error submitting application:", error);
                                Swal.fire('Error', 'Submission failed!', 'error');
                            });
                    }
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.cancel) {
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
                                console.log("Rejecting application:", application_id, "Reason:", rejectResult
                                    .value);
                                fetch('/sponsorship/reject', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            id: application_id,
                                            sponsorship_id,
                                            reason: rejectResult.value
                                        })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        console.log("Rejected successfully:", data);
                                        Swal.fire('Rejected', data.message, 'error').then(() => location
                                            .reload());
                                    })
                                    .catch(error => {
                                        console.error("Error rejecting application:", error);
                                        Swal.fire('Error', 'Rejection submission failed!', 'error');
                                    });
                            }
                        });
                    }
                });
            }
        </script>
    @endsection
