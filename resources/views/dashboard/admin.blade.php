@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('content')
<style>
    .card {
        min-height: 130px; /* Adjust height as needed */
        display: flex;
        min-width: 200px; /* Adjust width as needed */
        flex-direction: column;
        justify-content: space-between;
        margin: 10px; /* Adjust margin as needed */

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
            <div class="row mb-3">
                <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-2">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Users</p>
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
                            <p class="mb-0 text-sm"><a href="/users/list"><span class="text-success font-weight-bolder">Click here </span></a>for
                                more info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-2">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Payment Receipt Uploaded</p>
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
                            <p class="mb-0 text-sm"><a href="/invoice"><span class="text-success font-weight-bolder">Click here </span></a>for
                                more info.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-2">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Requested SQM Sum</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['req_sqm_sum'] . ' SQM' ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-archive" viewBox="0 0 16 16" style="color: #FFFFFF; margin-top: 15px;">
                                        <path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5zm13-3H1v2h14zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
{{--                        <hr class="dark horizontal my-0">--}}
{{--                        <div class="card-footer p-2 ps-3">--}}
{{--                            <p class="mb-0 text-sm"><a href="/users/list"><span class="text-success font-weight-bolder">Click here </span></a>for--}}
{{--                                more info.</p>--}}
{{--                        </div>--}}
                    </div>
                </div>
                <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4 mt-2">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Approved SQM Sum</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['approved_sqm_sum'] . ' SQM' ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-archive-fill" viewBox="0 0 16 16" style="color: #FFFFFF; margin-top: 15px;">
                                        <path d="M12.643 15C13.979 15 15 13.845 15 12.5V5H1v7.5C1 13.845 2.021 15 3.357 15zM5.5 7h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1M.8 1a.8.8 0 0 0-.8.8V3a.8.8 0 0 0 .8.8h14.4A.8.8 0 0 0 16 3V1.8a.8.8 0 0 0-.8-.8z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
{{--                        <hr class="dark horizontal my-0">--}}
{{--                        <div class="card-footer p-2 ps-3">--}}
{{--                            <p class="mb-0 text-sm"><a href="/invoice"><span class="text-success font-weight-bolder">Click here </span></a>for--}}
{{--                                more info.</p>--}}
{{--                        </div>--}}
                    </div>
                </div>
            </div>


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
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Application</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['startupZone']['total'] ?? 0 }}</h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">weekend</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><a href="{{route('application.lists')}}"
                                                       class="text-success font-weight-bolder">Click here</a> for more info.</p>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 ">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Initiated</p>
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
                            <p class="mb-0 text-sm"><a href="/application-list/in-progress"><span
                                        class="text-success font-weight-bolder"> Click here</span> </a>for more info.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 ">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Submitted</p>
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
                            <p class="mb-0 text-sm"><a href="/application-list/submitted"><span
                                        class="text-success font-weight-bolder">Click here </span> </a>for more info.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2  mb-3">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Approved Application</p>
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
                            <p class="mb-0 text-sm"><a href="/application-list/approved"
                                                       class="text-success font-weight-bolder">Click here</a> for more
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
                            <p class="mb-0 text-sm"><a href="/application-list/submitted"
                                                       class="text-success font-weight-bolder">Click here</a> for more
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
                            <p class="mb-0 text-sm"><a href="/application-list/approved"
                                                       class="text-success font-weight-bolder">Click here</a> for more
                                info.</p>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="dark horizontal my-0">

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
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Sponsorship Application</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['sponsors_count'] ?? 0 }}</h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><a href="/sponsorship-list/"><span class="text-success font-weight-bolder"> Click here </span></a>for more info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Initiated</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['sponsorshipByStatus']['initiated'] ?? 0 }}</h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><a href="/sponsorship-list/in-progress"><span class="text-success font-weight-bolder"> Click here</span> </a>for more info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Submitted</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['sponsorshipByStatus']['submitted'] ?? 0 }}</h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">Person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><a href="/sponsorship-list/submitted"><span class="text-success font-weight-bolder">Click here </span></a>for more info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4  mt-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Approved Application</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['sponsorshipByStatus']['approved'] ?? 0 }}</h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">Person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><a href="/sponsorship-list/approved"><span class="text-success font-weight-bolder">Click here </span></a>for more info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-4">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Rejected Application</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['sponsorshipByStatus']['rejected'] ?? 0 }}</h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg ">
                                    <i class="material-symbols-rounded opacity-10 ">Person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><a href="/sponsorship-list/rejected"><span class="text-success font-weight-bolder">Click here </span></a>for more info.</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="dark horizontal my-0">

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


        </div>
    </div>
    {{--    <form method="POST" action="{{ route('logout') }}">--}}
    {{--        @csrf--}}
    {{--        <button type="submit">Logout</button>--}}
    {{--    </form>--}}

@endsection
