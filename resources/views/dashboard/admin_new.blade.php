@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('content')
    @php
        $hide = true;
    @endphp
    <style>
        .card {
            min-height: 130px;
            /* Adjust height as needed */
            display: flex;
            min-width: 200px;
            /* Adjust width as needed */
            flex-direction: column;
            justify-content: space-between;
            margin: 10px;
            /* Adjust margin as needed */

        }

        .icon {
            width: 48px;
            height: 48px;
            /* padding: 10px; */
        }
    </style>
    <div class="container-fluid py-2">
        <div class="row">
            <div class="ms-3">
                <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
                <p class="mb-4">
                    Applicants analytics.
                </p>
            </div>
            <div class="ms-3">
                <p class=" font-weight-bolder">
                    Users
                </p>
            </div>
            <div class="row">
                <!-- Left Side Content -->
                <div class="col-md-12">
                    <div class="row mb-3">
                        <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
                            <div class="card">
                                <div class="card-header p-2 ps-3">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">
                                                Total
                                                Users</p>
                                            <h4 class="mb-0 mt-1">{{ $analytics['totalUsers'] ?? 0 }}</h4>
                                        </div>
                                        <div
                                                class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                            <i class="material-symbols-rounded opacity-10">person</i>
                                        </div>
                                    </div>
                                </div>

                                <hr class="dark horizontal my-0">
                                <div class="card-footer p-2 ps-3">
                                    <p class="mb-0 text-sm"><a href="{{ route('users.list') }}"><span
                                                    class="text-success font-weight-bolder">Click here </span></a>for
                                        more info.</p>
                                </div>
                            </div>


                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-2">
                                    <div class="card">
                                        <div class="card-header p-2 ps-3">
                                            Active User Analytics
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">
                                                    </p>
                                                    <h4 class="mb-0 mt-1"></h4>
                                                </div>
                                                <div class="card-footer p-2 ps-3">
                                                    <p class="mb-0 text-sm">
                                                        <a href="{{ route('active.users.analytics') }}"
                                                        <span class="text-warning font-weight-bolder">Click Here</span> </a>
                                                        —
                                                        for more information.
                                                    </p>
                                                </div>


                                                {{-- <div
                                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                                    <i class="material-symbols-rounded opacity-10">person</i>
                                                </div> --}}
                                            </div>
                                        </div>


                                    </div>
                                </div>

                            </div>
                        </div>

                        @if($hide == false)
                            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
                                <div class="card">
                                    <div class="card-header p-2 ps-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">
                                                    Payment Receipt Uploaded</p>
                                                <h4 class="mb-0 mt-1">{{ $analytics['payments'][0] ?? 0 }}</h4>
                                            </div>
                                            <div
                                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                                <i class="material-symbols-rounded opacity-10">person</i>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="dark horizontal my-0">
                                    <div class="card-footer p-2 ps-3">
                                        <p class="mb-0 text-sm"><a href="{{route('invoice.list')}}"><span
                                                        class="text-success font-weight-bolder">Click here </span></a>for
                                            more info.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2">
                                <div class="card">
                                    <div class="card-header p-2 ps-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="mb-0 text-capitalize font-weight-bold"
                                                   style="font-size: 1rem;">
                                                    Co-Exhibitor Applications Awaiting Approval
                                                </p>
                                                <h4 class="mb-0 mt-1">
                                                    <span class="text-warning">{{ $coExhibitorCount ?? 0 }}</span>
                                                    /
                                                    <span class="text-success">{{ $approvedCoexhibitorCount }}</span>
                                                </h4>
                                            </div>
                                            <div
                                                    class="icon icon-md icon-shape bg-gradient-warning shadow text-center border-radius-lg">
                                                <i class="fa-solid fa-user-clock opacity-10"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <hr class="dark horizontal my-0">
                                    <div class="card-footer p-2 ps-3">
                                        <p class="mb-0 text-sm">
                                            <a href="{{ route('co_exhibitors') }}"
                                            <span class="text-warning font-weight-bolder">Pending review</span> </a> —
                                            Click
                                            here for more information.
                                        </p>
                                    </div>
                                </div>
                            </div>

                        @endif
                    </div>
                </div>
                @if($hide == false)
                    <div class="col-md-6">
                        <div class="row mb-3">
                            <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-2">
                                <div class="card">
                                    <div class="card-header p-2 ps-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">
                                                    Requested SQM Sum</p>
                                                <h4 class="mb-0 mt-1">{{ $analytics['req_sqm_sum'] . ' SQM' ?? 0 }}</h4>
                                            </div>
                                            <div
                                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     fill="currentColor" class="bi bi-archive" viewBox="0 0 16 16"
                                                     style="color: #FFFFFF; margin-top: 15px;">
                                                    <path
                                                            d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5zm13-3H1v2h14zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    {{--                        <hr class="dark horizontal my-0"> --}}
                                    {{--                        <div class="card-footer p-2 ps-3"> --}}
                                    {{--                            <p class="mb-0 text-sm"><a href="/users/list"><span class="text-success font-weight-bolder">Click here </span></a>for --}}
                                    {{--                                more info.</p> --}}
                                    {{--                        </div> --}}
                                </div>
                            </div>
                            <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-2">
                                <div class="card">
                                    <div class="card-header p-2 ps-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">
                                                    Approved SQM Sum</p>
                                                <h4 class="mb-0 mt-1">{{ $analytics['approved_sqm_sum'] . ' SQM' ?? 0 }}</h4>
                                            </div>
                                            <div
                                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                     fill="currentColor" class="bi bi-archive-fill" viewBox="0 0 16 16"
                                                     style="color: #FFFFFF; margin-top: 15px;">
                                                    <path
                                                            d="M12.643 15C13.979 15 15 13.845 15 12.5V5H1v7.5C1 13.845 2.021 15 3.357 15zM5.5 7h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1M.8 1a.8.8 0 0 0-.8.8V3a.8.8 0 0 0 .8.8h14.4A.8.8 0 0 0 16 3V1.8a.8.8 0 0 0-.8-.8z"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    {{--                        <hr class="dark horizontal my-0"> --}}
                                    {{--                        <div class="card-footer p-2 ps-3"> --}}
                                    {{--                            <p class="mb-0 text-sm"><a href="/invoice"><span class="text-success font-weight-bolder">Click here </span></a>for --}}
                                    {{--                                more info.</p> --}}
                                    {{--                        </div> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                <hr class="dark horizontal my-0">

                <div class="ms-3 mt-2">
                    <p class="font-weight-bolder">
                        Exhibition
                    </p>
                </div>

                <div class="row mb-3">
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 "> <!-- Updated mr-12 to me-4 -->
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total
                                            Application</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['startupZone']['total'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">weekend</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="{{ route('application.lists', ['type' => 'startup-zone']) }}"
                                                           class="text-success font-weight-bolder">Click here</a> for
                                    more info.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 ">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">
                                            Total Initiated</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['startupZone']['initiated'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">person</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="{{ route('application.list', ['status' => 'in-progress', 'type' => 'startup-zone']) }}"><span
                                                    class="text-success font-weight-bolder"> Click here</span> </a>for
                                    more info.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 ">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total
                                            Submitted</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['startupZone']['submitted'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">Person</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="{{ route('application.list', ['status' => 'submitted', 'type' => 'startup-zone']) }}"><span
                                                class="text-success font-weight-bolder">Click here </span> </a>for more
                                    info.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2  mb-3">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total
                                            Approved Application</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['startupZone']['approved'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">weekend</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="{{ route('application.list', ['status' => 'approved', 'type' => 'startup-zone']) }}"
                                                           class="text-success font-weight-bolder">Click here</a> for
                                    more
                                    info.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2  mb-3">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Not Paid</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['startupZone']['notPaid'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">weekend</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="{{ route('application.list', ['status' => 'submitted', 'type' => 'startup-zone', 'payment_status' => 'unpaid']) }}"
                                                           class="text-success font-weight-bolder">Click here</a> for
                                    more
                                    info.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2  mb-3">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Paid</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['startupZone']['paid'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">weekend</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="{{ route('application.list', ['status' => 'submitted', 'type' => 'startup-zone', 'payment_status' => 'paid']) }}"
                                                           class="text-success font-weight-bolder">Click here</a> for
                                    more
                                    info.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="dark horizontal my-0">

                {{-- <hr class="dark horizontal my-0"> --}}

                <div class="ms-3 mt-2">
                    <p class="font-weight-bolder">
                        Declaration Forms
                    </p>
                </div>
    
                <div class="row mb-3">
                    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-4">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Declaration Forms Filled</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['declarationsFilled'] ?? 0 }}</h4>
                                    </div>
                                    <div class="icon icon-md icon-shape bg-gradient-success shadow-success shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">check_circle</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="{{ route('admin.declarations.list', ['status' => 'filled']) }}"><span class="text-success font-weight-bolder">Click here </span></a>for more info.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-4">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Declaration Forms Not Filled</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['declarationsNotFilled'] ?? 0 }}</h4>
                                    </div>
                                    <div class="icon icon-md icon-shape bg-gradient-danger shadow-danger shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">cancel</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="{{ route('admin.declarations.list', ['status' => 'not_filled']) }}"><span class="text-success font-weight-bolder">Click here </span></a>for more info.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{--
                <div class="ms-3 mt-2">
                    <p class="font-weight-bolder">
                        Sponsorship
                    </p>
                </div>

                <div class="row mb-3">
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-4">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total
                                            Sponsorship Application</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['sponsors_count'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">person</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="/sponsorship-list/"><span
                                                class="text-success font-weight-bolder"> Click here </span></a>for more
                                    info.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-4">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total
                                            Initiated</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['sponsorshipByStatus']['initiated'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">person</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="/sponsorship-list/in-progress"><span
                                                class="text-success font-weight-bolder"> Click here</span> </a>for more
                                    info.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-4">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total
                                            Submitted</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['sponsorshipByStatus']['submitted'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">Person</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="/sponsorship-list/submitted"><span
                                                class="text-success font-weight-bolder">Click here </span></a>for more
                                    info.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4  mt-4">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total
                                            Approved Application</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['sponsorshipByStatus']['approved'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                        <i class="material-symbols-rounded opacity-10">Person</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="/sponsorship-list/approved"><span
                                                class="text-success font-weight-bolder">Click here </span></a>for more
                                    info.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-4">
                        <div class="card">
                            <div class="card-header p-2 ps-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total
                                            Rejected Application</p>
                                        <h4 class="mb-0 mt-1">{{ $analytics['sponsorshipByStatus']['rejected'] ?? 0 }}</h4>
                                    </div>
                                    <div
                                            class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg ">
                                        <i class="material-symbols-rounded opacity-10 ">Person</i>
                                    </div>
                                </div>
                            </div>
                            <hr class="dark horizontal my-0">
                            <div class="card-footer p-2 ps-3">
                                <p class="mb-0 text-sm"><a href="/sponsorship-list/rejected"><span
                                                class="text-success font-weight-bolder">Click here </span></a>for more
                                    info.</p>
                            </div>
                        </div>
                    </div>
                </div>
                --}}

                @if($hide == false)
                <div class="ms-3 mt-2">
                    <p class="font-weight-bolder">
                        Statistics
                    </p>
                </div>
                <!-- Right Side Content -->
                <div class="row">
                    <div class="col-md-6">

                        <div class="card card-raised">
                            <div class="card-body">
                                <h2 class="overline">Country Wise Stats Submitted Applications</h2>
                                <div class="list-group list-group-flush list-group-light">
                                    <div class="list-group-item d-flex justify-content-between px-0">
                                        <div class="me-2">Total Countries</div>
                                        <div class="text-end"><strong>{{ $totalCountries }}</strong></div>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between px-0">
                                        <div class="me-2">Total Applications</div>
                                        <div class="text-end">
                                            <strong>{{ $indiaInternationalStats->india_count + $indiaInternationalStats->international_count }}</strong>
                                        </div>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between px-0">
                                        <div class="caption font-monospace ">India</div>
                                        <div class="caption font-monospace  ms-2 text-end">
                                            {{ $indiaInternationalStats->india_count }} /
                                            {{ $indiaInternationalStats->india_sqm }} sqm
                                        </div>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between px-0">
                                        <div class="caption font-monospace ">International</div>
                                        <div class="caption font-monospace  ms-2 text-end">
                                            {{ $indiaInternationalStats->international_count }} /
                                            {{ $indiaInternationalStats->international_sqm }} sqm
                                        </div>
                                    </div>

                                    @php
                                        $tots =
                                            $indiaInternationalStats->india_sqm +
                                            $indiaInternationalStats->international_sqm;
                                    @endphp
                                    {{-- <p class="text-white">Total Countries with Submitted Applications: <strong>{{ $totalCountries }}</strong></p> --}}

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Country</th>
                                                <th>Total Companies</th>
                                                <th>Total SQM / Share</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($applicationsByCountry as $data)
                                                @php
                                                    $share = ($data->total_sqm / $tots) * 100;
                                                    $share = number_format($share, 2);
                                                @endphp
                                                <tr>
                                                    <td>{{ $data->country_name }}</td>
                                                    <td class="text-center">{{ $data->total_companies }}</td>
                                                    <td class="text-center">{{ $data->total_sqm }} / {{ $share }}
                                                        %
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="card card-raised">
                            <div class="card-body">
                                <h2 class="overline">Country Wise Stats Approved Applications</h2>
                                <div class="list-group list-group-flush list-group-light">
                                    <div class="list-group-item d-flex justify-content-between px-0">
                                        <div class="me-2">Total Countries</div>
                                        <div class="text-end"><strong>{{ $totalApprovedCountries }}</strong></div>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between px-0">
                                        <div class="me-2">Total Application</div>
                                        <div class="text-end">
                                            <strong>{{ $approvedIndiaInternationalStats->india_count + $approvedIndiaInternationalStats->international_count }}</strong>
                                        </div>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between px-0">
                                        <div class="caption font-monospace ">India</div>
                                        <div class="caption font-monospace  ms-2 text-end">
                                            {{ $approvedIndiaInternationalStats->india_count }} /
                                            {{ $approvedIndiaInternationalStats->india_sqm }} sqm
                                        </div>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between px-0">
                                        <div class="caption font-monospace ">International</div>
                                        <div class="caption font-monospace  ms-2 text-end">
                                            {{ $approvedIndiaInternationalStats->international_count }} /
                                            {{ $approvedIndiaInternationalStats->international_sqm }} sqm
                                        </div>
                                    </div>

                                    @php
                                        $app_tots =
                                            $approvedIndiaInternationalStats->india_sqm +
                                            $approvedIndiaInternationalStats->international_sqm;
                                    @endphp

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Country</th>
                                                <th>Total Companies</th>
                                                <th>Total SQM / Share</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($approvedApplicationsByCountry as $data)
                                                @php
                                                    $share = ($data->total_sqm / $app_tots) * 100;
                                                    $share = number_format($share, 2);
                                                @endphp
                                                <tr>
                                                    <td>{{ $data->country_name }}</td>
                                                    <td class="text-center">{{ $data->total_companies }}</td>
                                                    <td class="text-center">{{ $data->total_sqm }} / {{ $share }}
                                                        %
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

                @endif


            </div>
        </div>
    {{--    <form method="POST" action="{{ route('logout') }}"> --}}
    {{--        @csrf --}}
    {{--        <button type="submit">Logout</button> --}}
    {{--    </form> --}}

@endsection
