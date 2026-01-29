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
{{--                                    <th>Application No</th>--}}
                                    <th class="text-center" >Company Name</th>
                                    <th class="text-center" >Country</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">Name</h6>
                                            <p class="text-xs text-secondary mb-0">Email</p>
                                        </div>
                                    </th>
                                    <th>Mobile Number</th>
                                    <th>Date of Submission</th>
                                    <th>Status</th>
                                    <th>Action</th>
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
                                        <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">
                                                {{ $application->company_name }} </h6>
                                        </div>
                                        </td>
{{--                                        <td>--}}
{{--                                            <div class="d-flex">--}}
{{--                                                <div class="form-check my-auto">--}}
{{--                                                    <input class="form-check-input" type="checkbox" id="customCheck1">--}}
{{--                                                </div>--}}
{{--                                                <h6 class="ms-3 text-xs my-auto">{{$application->application_id}}</h6>--}}
{{--                                            </div>--}}
{{--                                        </td>--}}
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-xs">{{ ($application->country->name) }}</h6>
{{--                                                    <p class="text-xs text-secondary mb-0">{{ $application->description }}</p>--}}
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">
                                                    {{ $application->eventContact->first_name }} {{ $application->eventContact->last_name }} </h6>
                                                <p class="text-sm text-secondary mb-0">{{ $application->eventContact->email }}</p>
                                            </div>
                                        </td>

                                        <td class="align-middle text-center text-sm">
                                            <p class="text-secondary text-sm font-weight-bold">{{ $application->eventContact->contact_number }}</p>
                                            {{--                                            <p class="text-xs text-secondary mb-0">{{ $application->organization }}</p>--}}
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span
                                                class="text-secondary text-sm font-weight-bold">{{ $application->submission_date }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if ($application->application_status === 'submitted')
                                                <span
                                                    class="badge badge-sm badge-danger">{{ $application->submission_status }}</span>
                                            @elseif ($application->application_status === 'in progress')
                                                <span
                                                    class="mt-3 badge badge-sm bg-gradient-primary">{{ $application->submission_status }}</span>
                                            @else
                                                <span
                                                    class="badge badge-sm bg-gradient-warning">{{ $application->submission_status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-sm">


                                            <button type="submit" data-bs-toggle="tooltip"
                                                    data-bs-original-title="Approve Application"
                                                    style="border:none; background:none; padding:0;"
                                                    onclick="showModifiedModal('{{ $application->id }}', '{{ $application->stall_category }}', {{ $application->interested_sqm }}, {{ $application->interested_sqm }})">
                                                <i class="material-symbols-rounded text-secondary position-relative text-lg">visibility</i>
                                            </button>

                                            {{--                                        <form action="{{ route('approve', ['id' => $application->id]) }}" method="POST" style="display:inline;">--}}
                                            {{--                                            @csrf--}}
                                            {{--                                            <button type="submit" data-bs-toggle="tooltip" data-bs-original-title="Approve Application" style="border:none; background:none; padding:0;">--}}
                                            {{--                                                <i class="material-symbols-rounded text-secondary position-relative text-lg">visibility</i>--}}
                                            {{--                                            </button>--}}
                                            {{--                                        </form>--}}
                                            {{--                                        <a href="javascript:;" class="mx-3" data-bs-toggle="tooltip" data-bs-original-title="Edit product">--}}
                                            {{--                                            <i class="material-symbols-rounded text-secondary position-relative text-lg">drive_file_rename_outline</i>--}}
                                            {{--                                        </a>--}}
                                            {{--                                        <a href="javascript:;" data-bs-toggle="tooltip" data-bs-original-title="Delete product">--}}
                                            {{--                                            <i class="material-symbols-rounded text-secondary position-relative text-lg">delete</i>--}}
                                            {{--                                        </a>--}}
                                        </td>
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
                // Function to show the modified modal
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

                        <label for="isPavilion" style="display: block; margin-top: 10px; margin-bottom: 5px;">Is Pavilion:</label>
                        <input type="checkbox" id="isPavilion" class="swal2-checkbox">
                        <label for="isPavilion" style="display: inline;">No</label>
                    </div>
                </form>
            `,
                        confirmButtonText: 'Submit',
                        customClass: {
                            confirmButton: 'btn bg-gradient-success',
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
                        },
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
                                    Swal.fire('Success', data.message + ' ' + data.company_name, 'success', 2000).then(() => {
                                        location.reload();
                                    });
                                })
                                .catch(error => {
                                    Swal.fire('Error', 'Submission failed!', 'error');
                                });
                        }
                    });

                    // Set default state of the Is Pavilion slider to "No"
                    const isPavilion = document.getElementById('isPavilion');
                    isPavilion.checked = false;
                }
            </script>

@endsection
