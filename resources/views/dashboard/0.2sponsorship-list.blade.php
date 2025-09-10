@extends('layouts.dashboard')
@section('title', ucfirst($slug))
@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">All {{$slug}}</h5>
                                <p class="text-sm mb-0">
                                    List of all {{$slug}}.
                                </p>
                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                            </div>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-0">
                        <div class="table-responsive">
                            <table class="table table-flush" id="products-list">
                                <thead class="thead-light">
                                <tr>
                                    @php
                                        $status = explode(' - ', trim($slug))[0];
                                    @endphp
                                    {{--                                    {{ $status }}--}}
                                    {{--                                    <th>Application No</th>--}}
                                    <th class="text-center">Company Name</th>
                                    <th class="text-center">Country</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">Name</h6>
                                            <p class="text-xs text-secondary mb-0">Designation</p>
                                        </div>
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">Email</h6>
                                            <p class="text-xs text-secondary mb-0">Contact No</p>
                                        </div>
                                    </th>
                                    {{--                                    <th>Mobile Number</th>--}}
                                    <th>Date of Submission</th>
                                    <th>Status</th>
                                    {{--                                    @if($status != 'in progress')--}}
                                    <th>Action</th>
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
                                        {{--                                        @dd($application->sponsorships)--}}
                                        <tr>
                                            <td class="text-center">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $application->company_name }}</h6>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-xs">{{ $application->country->name }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $application->eventContact->first_name }} {{ $application->eventContact->last_name }}</h6>
                                                    <p class="text-sm text-secondary mb-0">{{  $application->eventContact->job_title }}</p>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-sm text-secondary mb-0">{{ $application->eventContact->email }}
                                                    {{--                                                    @php--}}
                                                    {{--                                                        echo $application->sponsorships->sponsorship_item;--}}
                                                    {{--                                                    @endphp--}}
                                                </p>
                                                <p class="text-secondary text-sm font-weight-bold">{{ $application->eventContact->contact_number }}</p>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <span
                                                    class="text-secondary text-sm font-weight-bold">{{ $application->submission_date }}</span>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                @if ($application->application_status === 'submitted')
                                                    <span
                                                        class="badge badge-xs badge-danger">{{ $application->submission_status }}</span>
                                                @elseif ($application->application_status === 'in progress')
                                                    <span
                                                        class="mt-3 badge badge-xs bg-gradient-primary">{{ $application->submission_status }}</span>
                                                @else
                                                    <span
                                                        class="badge badge-xs bg-gradient-warning">{{ $application->submission_status }}</span>
                                                @endif
                                            </td>

                                            @if($status != 'in progress')
                                                <td class="text-center text-sm">
                                                    <div class="d-flex flex-column">
                                                        <button type="submit" data-bs-toggle="tooltip"
                                                                data-bs-original-title="Follow Up Application"
                                                                style="border:none; background:none; padding:0; margin-top: 5px;"
                                                                onclick="showModifiedModal({{ $application->id }}, '{{ $application->sponsorships->sponsorship_item }}', '{{ $application->sponsorships->price }}', '{{ $application->sponsorships->id }}')">
                                                            <i class="fa-solid fa-person-walking"></i> Follow Up
                                                        </button>
                                                        <button type="submit" data-bs-toggle="tooltip"
                                                                data-bs-original-title="View Application"
                                                                style="border:none; background:none; padding:0; margin-top: 5px;"
                                                                onclick="window.location.href='/applicationView?application_id={{ $application->application_id }}'">
                                                            <i class="material-symbols-rounded text-secondary position-relative text-lg">visibility</i>View
                                                        </button>
                                                        <button type="submit" data-bs-toggle="tooltip"
                                                                data-bs-original-title="Delete Application"
                                                                style="border:none; background:none; padding:0; margin-top: 5px;"
                                                                onclick="deleteApplication('{{ $application->id }}')">
                                                            <i class="material-symbols-rounded text-secondary position-relative text-lg">delete</i>Delete
                                                        </button>
                                                    </div>
                                                </td>
                                            @else
                                                <td>
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
                function showModifiedModal(application_id, item, price, sponsorship_id) {
                    Swal.fire({
                        title: 'Sponsorship Application Details',
                        html: `
           <form id="applicationForm" style="max-width: 400px; margin: auto; padding: 20px; border-radius: 10px; background: #f9f9f9; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);">
    <div style="text-align: left;">
        <h3 style="margin-bottom: 10px; color: #333;">Sponsorship Details</h3>
        <p style="font-size: 16px; margin-bottom: 5px;"><strong>Sponsorship Item:</strong> ${item}</p>
        <p style="font-size: 16px; margin-bottom: 15px;"><strong>Sponsorship Price:</strong> ${price}</p>

        <input type="hidden" id="sponsorshipID" value="${sponsorship_id}">
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

                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('/sponsorship/submit', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    id: application_id,
                                    sponsorship_id: sponsorship_id
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
                                    fetch('/sponsorship/reject', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            id: application_id,
                                            sponsorship_id : sponsorship_id,
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
