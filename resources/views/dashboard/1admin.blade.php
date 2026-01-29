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
                                    <h4 class="mb-0 mt-1">{{ $analytics['totalApplications'] ?? 0 }}</h4>
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
                @php
                    $hide = false;
                @endphp
                @if(!$hide)
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 ">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Initiated</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['applicationsByStatus']['in progress'] ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><a href="{{ route('application.list', ['status' => 'in-progress']) }}"><span
                                        class="text-success font-weight-bolder"> Click here</span> </a>for more info.
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 ">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Submitted</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['applicationsByStatus']['submitted'] ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">Person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><a href="{{ route('application.list', ['status' => 'submitted']) }}">
                                    <span class="text-success font-weight-bolder">Click here </span>
                                </a>for more info.
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
                                    <h4 class="mb-0 mt-1">{{ $analytics['applicationsByStatus']['Approved'] ?? 0 }}</h4>
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

                @if(!$hide)
                <div class="col-xl-4 col-sm-6 mb-xl-0 mb-4 mt-2  mb-3">
                    <div class="card">
                        <div class="card-header p-2 ps-3">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-m mb-0 text-capitalize font-weight-black font-weight-bold">Total Rejecteds Application</p>
                                    <h4 class="mb-0 mt-1">{{ $analytics['applicationsByStatus']['rejected'] ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">weekend</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-sm"><a href="/application-list/rejected"
                                                       class="text-success font-weight-bolder">Click here</a> for more
                                info.</p>
                        </div>
                    </div>
                </div>
                @endif
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


        </div>
    </div>
    {{--    <form method="POST" action="{{ route('logout') }}">--}}
    {{--        @csrf--}}
    {{--        <button type="submit">Logout</button>--}}
    {{--    </form>--}}

@endsection
