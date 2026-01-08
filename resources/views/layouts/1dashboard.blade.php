

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />
    <title>
        @yield('title' , '{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }} Admin Panel')
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
    <!-- Navbar -->
    @include('partials.navbar')
    <!-- End Navbar -->
    @yield('content')

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
{{-- <script src="/assets/js/core/popper.min.js"></script>
<script src="/assets/js/core/bootstrap.min.js"></script>
<script src="/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="/assets/js/plugins/smooth-scrollbar.min.js"></script>
<!-- Kanban scripts -->
<script src="/assets/js/plugins/dragula/dragula.min.js"></script>
<script src="/assets/js/plugins/jkanban/jkanban.min.js"></script> --}}

<script src="/assets/plugins/jquery/jquery-2.2.0.min.js"></script>
<script src="/assets/plugins/materialize/js/materialize.min.js"></script>
<script src="/assets/plugins/material-preloader/js/materialPreloader.min.js"></script>
<script src="/assets/plugins/jquery-blockui/jquery.blockui.js"></script>
<script src="/assets/plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="/assets/plugins/jquery-steps/jquery.steps.min.js"></script>
<script src="/assets/js/alpha.min.js"></script>
<script src="/assets/js/pages/form-wizard.js"></script>

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

<style>
    /* Sidebar toggle styles */
    body.g-sidenav-hidden #sidenav-main {
        transform: translateX(-100%) !important;
        transition: transform 0.3s ease !important;
    }
    
    body.g-sidenav-show #sidenav-main {
        transform: translateX(0) !important;
        transition: transform 0.3s ease !important;
    }
    
    body.g-sidenav-hidden .main-content {
        margin-left: 0 !important;
        transition: margin-left 0.3s ease !important;
    }
    
    body.g-sidenav-show .main-content {
        margin-left: 250px !important;
        transition: margin-left 0.3s ease !important;
    }
    
    body.g-sidenav-minimized .main-content {
        margin-left: 80px !important;
        transition: margin-left 0.3s ease !important;
    }
    
    @media (max-width: 1200px) {
        body.g-sidenav-show .main-content,
        body.g-sidenav-minimized .main-content {
            margin-left: 0 !important;
        }
    }
</style>

<script>
    // Sidebar toggle functionality with three states: expanded -> minimized -> hidden
    function toggleSidebar() {
        const body = document.querySelector('body');
        const sidenav = document.getElementById('sidenav-main');
        
        if (body.classList.contains('g-sidenav-show')) {
            // From expanded to minimized
            body.classList.remove('g-sidenav-show');
            body.classList.add('g-sidenav-minimized');
            if (sidenav) {
                sidenav.classList.add('sidenav-minimized');
            }
        } else if (body.classList.contains('g-sidenav-minimized')) {
            // From minimized to hidden
            body.classList.remove('g-sidenav-minimized');
            body.classList.add('g-sidenav-hidden');
            if (sidenav) {
                sidenav.classList.remove('sidenav-minimized');
            }
        } else {
            // From hidden to expanded
            body.classList.remove('g-sidenav-hidden');
            body.classList.add('g-sidenav-show');
            if (sidenav) {
                sidenav.classList.remove('sidenav-minimized');
            }
        }
    }
    
    // Handle mobile sidebar toggle
    document.addEventListener('DOMContentLoaded', function() {
        const iconNavbarSidenav = document.getElementById('iconNavbarSidenav');
        if (iconNavbarSidenav) {
            iconNavbarSidenav.addEventListener('click', function() {
                toggleSidebar();
            });
        }
    });
</script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"9065de7f3b3040e7","serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"version":"2025.1.0","token":"1b7cbb72744b40c580f8633c6b62637e"}' crossorigin="anonymous"></script>
</body>

</html>
