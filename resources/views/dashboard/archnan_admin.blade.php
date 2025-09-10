@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('content')


    <style>
        .async-hide {
            opacity: 0 !important
        }

        .p{
            font-size: 20px !important;
        }

         .custom-card {
             background-color: #81d0dc;
             color: black;
             width: 26rem;
             height: 12rem;"
         font-size: 28px  !important;
         }

        .cus-card {
            background-color: #68cac2;
            color: black;
            width: 18rem;
            height: 12rem;"
        }

        .custom-font {
            font-size: 60px;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row">
            <div class="ms-3">
                <h1 class="mb-0 h4 font-weight-bolder custom-font">Dashboard</h1>
                <p class="mb-4 text-dark custom-font">
                    Applicants analytics.
                </p>
            </div>
            <div class="ms-3">
                <p class=" font-weight-bolder text-dark custom-font">
                    Users
                </p>
            </div>

                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2 ">
                    <div class="card custom-card" >
                        <div class="card-header p-2 ps-3 custom-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class=" mb-0  text-dark" style="font-size: 20px;">Total Users</p>
                                    <h4 class="mb-0">{{ $analytics['totalUsers'] ?? 0 }} </h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-lg text-dark"><a href="/users/list" ><span class="text-success font-weight-bolder " >Click here </span></a>for
                                more info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4 mt-2 "  >
                    <div class="card custom-card" >
                        <div class="card-header p-2 ps-3 custom-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Payment Receipt Uploaded</p>
                                    <h4 class="mb-0">{{ $analytics['payments'][0] ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-lg"><a href="/invoice"><span class="text-success font-weight-bolder">Click here </span></a>for
                                more info.</p>
                        </div>
                    </div>
                </div>
            </div>


            <hr class="dark horizontal my-0">

            <div class="ms-3 mt-5">
                <p class="font-weight-bolder text-dark">
                    Exhibition
                </p>
            </div>

            <div class="row mb-3">
                <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4 mt-2 ">
                    <div class="card cus-card">
                        <div class="card-header p-2 ps-3 cus-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Total Application</p>
                                    <h4 class="mb-0">{{ $analytics['totalApplications'] ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">weekend</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0><a href="{{route('application.lists')}}"
                                                       class="text-success font-weight-bolder">Click here</a> for more
                                info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4  mt-2">
                    <div class="card cus-card">
                        <div class="card-header p-2 ps-3 cus-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Total Initiated</p>
                                    <h4 class="mb-0">{{ $analytics['applicationsByStatus']['in progress'] ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-lg"><a href="/application-list/in-progress"><span
                                        class="text-success font-weight-bolder"> Click here</span> </a>for more info.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4  mt-2">
                    <div class="card cus-card">
                        <div class="card-header p-2 ps-3 cus-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Total Submitted</p>
                                    <h4 class="mb-0">{{ $analytics['applicationsByStatus']['submitted'] ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">Person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-lg"><a href="/application-list/submitted"><span
                                        class="text-success font-weight-bolder">Click here </span> </a>for more info.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4 mt-2 mb-3">
                    <div class="card cus-card">
                        <div class="card-header p-2 ps-3 cus-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Total Approved Application</p>
                                    <h4 class="mb-0">{{ $analytics['applicationsByStatus']['approved'] ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">weekend</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-lg"><a href="/application-list/approved"
                                                       class="text-success font-weight-bolder">Click here</a> for more
                                info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4 mt-2 mb-3">
                    <div class="card cus-card">
                        <div class="card-header p-2 ps-3 cus-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Total Rejected Application</p>
                                    <h4 class="mb-0">{{ $analytics['applicationsByStatus']['rejected'] ?? 0 }}</h4>
                                </div>
                                <div
                                    class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">weekend</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-lg"><a href="/application-list/rejected"
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
                <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4 mt-4">
                    <div class="card cus-card">
                        <div class="card-header p-2 ps-3 cus-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Total Sponsorship Application</p>
                                    <h4 class="mb-0">{{ $analytics['sponsors_count'] ?? 0 }}</h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-lg"><a href="/sponsorship-list/"><span class="text-success font-weight-bolder"> Click here </span></a>for more info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4 mt-4">
                    <div class="card cus-card">
                        <div class="card-header p-2 ps-3 cus-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Total Initiated</p>
                                    <h4 class="mb-0">{{ $analytics['sponsorshipByStatus']['initiated'] ?? 0 }}</h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-lg"><a href="/sponsorship-list/in-progress"><span class="text-success font-weight-bolder"> Click here</span> </a>for more info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4 mt-4">
                    <div class="card cus-card">
                        <div class="card-header p-2 ps-3 cus-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Total Submitted</p>
                                    <h4 class="mb-0">{{ $analytics['sponsorshipByStatus']['submitted'] ?? 0 }}</h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">Person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-lg"><a href="/sponsorship-list/submitted"><span class="text-success font-weight-bolder">Click here </span></a>for more info.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4 mt-4">
                    <div class="card cus-card">
                        <div class="card-header p-2 ps-3 cus-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Total Approved Application</p>
                                    <h4 class="mb-0">{{ $analytics['sponsorshipByStatus']['approved'] ?? 0 }}</h4>
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
                <div class="col-xl-2 col-sm-6 mb-xl-0 mb-4 mt-4">
                    <div class="card cus-card">
                        <div class="card-header p-2 ps-3 cus-card">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p class="text-lg mb-0 text-capitalize text-dark">Total Rejected Application</p>
                                    <h4 class="mb-0">{{ $analytics['sponsorshipByStatus']['rejected'] ?? 0 }}</h4>
                                </div>
                                <div class="icon icon-md icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-lg">
                                    <i class="material-symbols-rounded opacity-10">Person</i>
                                </div>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-2 ps-3">
                            <p class="mb-0 text-lg"><a href="/sponsorship-list/rejected"><span class="text-success font-weight-bolder">Click here </span></a>for more info.</p>
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
