<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />
    <title>
        @yield('title', 'Delegate Panel') - {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}
    </title>
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('asset/css/material-dashboard.min.css?v=3.1.0') }}" rel="stylesheet" />
    <link id="pagestyle" href="{{ asset('public/asset/css/material-dashboard.min.css?v=3.1.0') }}" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" />
    <link rel="stylesheet" href="{{ asset('public/assets/css/custom.css') }}" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Anti-flicker snippet (recommended)  -->
    <style>
        .async-hide {
            opacity: 0 !important
        }
        
        /* Global Delegate Panel Styles */
        body {
            background-color: #f8f9fc;
        }
        
        .main-content {
            background-color: #f8f9fc;
        }
        
        /* Smooth transitions */
        * {
            transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }
        
        /* Better button styles */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5568d3 0%, #653a8f 100%);
        }
        
        /* Card improvements */
        .card {
            border-radius: 12px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: none;
        }
        
        /* Better form controls */
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        /* Loading states */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>
    @stack('styles')
</head>

<body class="g-sidenav-show">
<!-- Extra details for Live View on GitHub Pages -->
@include('delegate.partials.sidebar')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    @include('delegate.partials.navbar')
    <!-- Navbar -->
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
            <div class="toast show p-2 mt-2 bg-white" role="alert" aria-live="assertive" id="errorToast"
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

    <footer class="footer py-4">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
                <div class="col-lg-12 mb-lg-0 mb-4">
                    <div class="copyright text-center text-sm text-muted text-lg-start">
                        <ul class="nav nav-footer">
                            <li class="nav-item d-flex justify-content-between w-100 align-items-center flex-wrap">
                                <span class="ms-2 d-inline-flex align-items-center" style="white-space: nowrap;">
                                    © <script>document.write(new Date().getFullYear())</script> Copyright {{ config('constants.EVENT_NAME') }}, All Rights Reserved
                                </span>
                                <span class="me-2 d-inline-flex align-items-center" style="white-space: nowrap;">
                                    Powered by SCI Knowledge Interlinks Pvt. Ltd. (MM Activ)
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</main>


<!--   Core JS Files   -->
<script src="{{ asset('asset/js/core/popper.min.js') }}"></script>
<script src="{{ asset('public/asset/js/core/popper.min.js') }}"></script>
<script src="{{ asset('asset/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('public/asset/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('asset/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('public/asset/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('asset/js/plugins/smooth-scrollbar.min.js') }}"></script>
<script src="{{ asset('public/asset/js/plugins/smooth-scrollbar.min.js') }}"></script>
<!-- Kanban scripts -->
<script src="{{ asset('asset/js/plugins/dragula/dragula.min.js') }}"></script>
<script src="{{ asset('public/asset/js/plugins/dragula/dragula.min.js') }}"></script>
<script src="{{ asset('asset/js/plugins/jkanban/jkanban.min.js') }}"></script>
<script src="{{ asset('public/asset/js/plugins/jkanban/jkanban.min.js') }}"></script>

<!--   Core JS Files   -->

<script src="{{ asset('asset/js/core/datatables.js') }}"></script>
<script src="{{ asset('public/asset/js/core/datatables.js') }}"></script>

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
<script src="{{ asset('asset/js/material-dashboard.min.js?v=3.1.0') }}"></script>
<script src="{{ asset('public/asset/js/material-dashboard.min.js?v=3.1.0') }}"></script>

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

@stack('scripts')
</body>

</html>
