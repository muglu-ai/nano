

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />
    <title>
        @yield('title' , 'SEMICON 2025 Admin Panel')
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="/asset/css/material-dashboard.min.css?v=3.1.0" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />
    <link rel="stylesheet" href="/assets/css/custom.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">



    <!-- Anti-flicker snippet (recommended)  -->
    <style>
        .async-hide {
            opacity: 0 !important

        }

        /*body{*/
        /*    background-color: #dbdfdf !important;*/
        /*    font-size: 18px  !important;*/
        /*    font-family: "Times New Roman";*/

        /*}*/
        /*.p{*/
        /*    font-size: 20px !important;*/
        /*}*/

    </style>
</head>

<body class="g-sidenav-show  ">
<!-- Extra details for Live View on GitHub Pages -->
@include('partials.sidebar')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    @include('partials.logo')
    <!-- Navbar -->
    @include('partials.navbar')
    <!-- End Navbar -->
    @yield('content')

    <div class="position-fixed bottom-1 end-1 z-index-2">
            @if ($errors->any())
                <div class="toast show p-2 mt-2 bg-white" role="alert" aria-live="assertive" id="dangerToast"
                    aria-atomic="true" style="max-width: 100%; width: auto;">
                    <div class="toast-header border-0">
                        <i class="material-symbols-rounded text-danger me-2">
                            campaign
                        </i>
                        <span class="me-auto text-gradient text-danger font-weight-bold">Error </span>

                        <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast"
                            aria-label="Close"></i>
                    </div>
                    <hr class="horizontal dark m-0">
                    <div class="toast-body">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </div>
                </div>
            @endif
            @if (session('success'))
                <div class="toast show p-2 mt-2 bg-white" role="alert" aria-live="assertive" id="successToast"
                    aria-atomic="true" style="max-width: 100%; width: auto;">
                    <div class="toast-header border-0">
                        <i class="material-symbols-rounded text-success me-2">
                            check_circle
                        </i>
                        <span class="me-auto text-gradient text-success font-weight-bold">Success</span>

                        <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast"
                            aria-label="Close"></i>
                    </div>
                    <hr class="horizontal dark m-0">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if (session('error'))
                <div class="toast show      p-2 mt-2 bg-white" role="alert" aria-live="assertive" id="errorToast"
                    aria-atomic="true" style="max-width: 100%; width: auto;">
                    <div class="toast-header border-0">
                        <i class="material-symbols-rounded text-danger me-2">
                            error
                        </i>
                        <span class="me-auto text-gradient text-danger font-weight-bold">Error</span>

                        <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast"
                            aria-label="Close"></i>
                    </div>
                    <hr class="horizontal dark m-0">
                    <div class="toast-body">
                        {{ session('error') }}
                    </div>
                </div>
            @endif
        </div>

<footer class="footer py-4  ">
            <div class="container-fluid">
                <div class="row align-items-center justify-content-lg-between">
                    <div class="col-lg-12 mb-lg-0 mb-4">
                        <div class="copyright text-center text-sm text-muted text-lg-start">
                            <ul class="nav nav-footer">
                                <li class="nav-item d-flex justify-content-between w-100">
                                    <span class="ms-2">©
                                        <script>
                                            document.write(new Date().getFullYear()) 
                                        </script> Copyright {{config('constants.EVENT_NAME')}}, All Rights Reserved
                                    </span>
                                    <span class="me-2">Powered by MM Activ Sci-Tech Communications PVT. LTD.</span>
                                </li>

                            </ul>

                        </div>
                    </div>

                </div>
            </div>
        </footer>
</main>







{{--<div class="fixed-plugin">--}}
{{--    <a class="fixed-plugin-button text-dark position-fixed px-3 py-2">--}}
{{--        <i class="material-symbols-rounded py-2">settings</i>--}}
{{--    </a>--}}
{{--    <div class="card shadow-lg">--}}
{{--        <div class="card-header pb-0 pt-3">--}}
{{--            <div class="float-start">--}}
{{--                <h5 class="mt-3 mb-0">Material UI Configurator</h5>--}}
{{--                <p>See our dashboard options.</p>--}}
{{--            </div>--}}
{{--            <div class="float-end mt-4">--}}
{{--                <button class="btn btn-link text-dark p-0 fixed-plugin-close-button">--}}
{{--                    <i class="material-symbols-rounded">clear</i>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <!-- End Toggle Button -->--}}
{{--        </div>--}}
{{--        <hr class="horizontal dark my-1">--}}
{{--        <div class="card-body pt-sm-3 pt-0">--}}
{{--            <!-- Sidebar Backgrounds -->--}}
{{--            <div>--}}
{{--                <h6 class="mb-0">Sidebar Colors</h6>--}}
{{--            </div>--}}
{{--            <a href="javascript:void(0)" class="switch-trigger background-color">--}}
{{--                <div class="badge-colors my-2 text-start">--}}
{{--                    <span class="badge filter bg-gradient-primary" data-color="primary" onclick="sidebarColor(this)"></span>--}}
{{--                    <span class="badge filter bg-gradient-dark active" data-color="dark" onclick="sidebarColor(this)"></span>--}}
{{--                    <span class="badge filter bg-gradient-info" data-color="info" onclick="sidebarColor(this)"></span>--}}
{{--                    <span class="badge filter bg-gradient-success" data-color="success" onclick="sidebarColor(this)"></span>--}}
{{--                    <span class="badge filter bg-gradient-warning" data-color="warning" onclick="sidebarColor(this)"></span>--}}
{{--                    <span class="badge filter bg-gradient-danger" data-color="danger" onclick="sidebarColor(this)"></span>--}}
{{--                </div>--}}
{{--            </a>--}}
{{--            <!-- Sidenav Type -->--}}
{{--            <div class="mt-3">--}}
{{--                <h6 class="mb-0">Sidenav Type</h6>--}}
{{--                <p class="text-sm">Choose between different sidenav types.</p>--}}
{{--            </div>--}}
{{--            <div class="d-flex">--}}
{{--                <button class="btn bg-gradient-dark px-3 mb-2" data-class="bg-gradient-dark" onclick="sidebarType(this)">Dark</button>--}}
{{--                <button class="btn bg-gradient-dark px-3 mb-2 ms-2" data-class="bg-transparent" onclick="sidebarType(this)">Transparent</button>--}}
{{--                <button class="btn bg-gradient-dark px-3 mb-2  active ms-2" data-class="bg-white" onclick="sidebarType(this)">White</button>--}}
{{--            </div>--}}
{{--            <p class="text-sm d-xl-none d-block mt-2">You can change the sidenav type just on desktop view.</p>--}}
{{--            <!-- Navbar Fixed -->--}}
{{--            <div class="mt-3 d-flex">--}}
{{--                <h6 class="mb-0">Navbar Fixed</h6>--}}
{{--                <div class="form-check form-switch ps-0 ms-auto my-auto">--}}
{{--                    <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarFixed" onclick="navbarFixed(this)">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <hr class="horizontal dark my-3">--}}
{{--            <div class="mt-2 d-flex">--}}
{{--                <h6 class="mb-0">Sidenav Mini</h6>--}}
{{--                <div class="form-check form-switch ps-0 ms-auto my-auto">--}}
{{--                    <input class="form-check-input mt-1 ms-auto" type="checkbox" id="navbarMinimize" onclick="navbarMinimize(this)">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <hr class="horizontal dark my-3">--}}
{{--            <div class="mt-2 d-flex">--}}
{{--                <h6 class="mb-0">Light / Dark</h6>--}}
{{--                <div class="form-check form-switch ps-0 ms-auto my-auto">--}}
{{--                    <input class="form-check-input mt-1 ms-auto" type="checkbox" id="dark-version" onclick="darkMode(this)">--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <hr class="horizontal dark my-sm-4">--}}

{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}
<!--   Core JS Files   -->
<script src="/asset/js/core/popper.min.js"></script>
<script src="/asset/js/core/bootstrap.min.js"></script>
<script src="/asset/js/plugins/perfect-scrollbar.min.js"></script>
<script src="/asset/js/plugins/smooth-scrollbar.min.js"></script>
<!-- Kanban scripts -->
<script src="/asset/js/plugins/dragula/dragula.min.js"></script>
<script src="/asset/js/plugins/jkanban/jkanban.min.js"></script>

<!--   Core JS Files   -->

<script src="/asset/js/core/datatables.js"></script>

<script>
    const dataTableBasic = new simpleDatatables.DataTable("#datatable-basic", {
        searchable: true,
        fixedHeight: true
    });
    const dataTableBasic2 = new simpleDatatables.DataTable("#datatable-basic2", {
        searchable: true,
        fixedHeight: true
    });

    const dataTableSearch = new simpleDatatables.DataTable("#datatable-search", {
        searchable: true,
        fixedHeight: true
    });
</script>
<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>
<script>
    const dataTable = new simpleDatatables.DataTable("#datatable-basic3", {
        searchable: false,
        paging: false,
        perPage: false,
        perPageSelect: false

    });
</script>

<script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="/asset/js/material-dashboard.min.js?v=3.1.0"></script>

</body>

</html>
