

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />
    <title>
        @yield('title' , 'SemiCon 2025 Admin Panel')
    </title>
    <!-- Extra details for Live View on GitHub Pages -->
    <!-- Canonical SEO -->
    <link rel="canonical" href="https://www.creative-tim.com/product/material-dashboard-pro" />
    <!--  Social tags      -->

    <!-- Open Graph data -->

    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <link href="https://demos.creative-tim.com/material-dashboard-pro/assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="https://demos.creative-tim.com/material-dashboard-pro/assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <!-- CSS Files -->
    <link id="pagestyle" href="https://demos.creative-tim.com/material-dashboard-pro/assets/css/material-dashboard.min.css?v=3.1.0" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="/assets/css/custom.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Anti-flicker snippet (recommended)  -->
    <style>
        .async-hide {
            opacity: 0 !important
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">

@if(Auth::user()->role === 'co-exhibitor')
    @include('partials.couser_sidebar')
@else
    @include('partials.user_sidebar')
@endif
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    @include('partials.navbar')
    <!-- End Navbar -->
    @yield('content')
    <footer class="footer py-4  ">
        <div class="container-fluid">
            <div class="row align-items-center justify-content-lg-between">
                <div class="col-lg-6 mb-lg-0 mb-4">
                    <div class="copyright text-center text-sm text-muted text-lg-start">
                        © <script>
                            document.write(new Date().getFullYear())
                        </script> Copyright MM Activ, All Rights Reserved

                    </div>
                </div>
{{--                <div class="col-lg-6">--}}
{{--                    <ul class="nav nav-footer justify-content-center justify-content-lg-end">--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="https://www.creative-tim.com" class="nav-link text-muted" target="_blank">Creative Tim</a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="https://www.creative-tim.com/presentation" class="nav-link text-muted" target="_blank">About Us</a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="https://www.creative-tim.com/blog" class="nav-link text-muted" target="_blank">Blog</a>--}}
{{--                        </li>--}}
{{--                        <li class="nav-item">--}}
{{--                            <a href="https://www.creative-tim.com/license" class="nav-link pe-0 text-muted" target="_blank">License</a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </div>--}}
            </div>
        </div>
    </footer>
</main>

<!--   Core JS Files   -->
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/core/popper.min.js"></script>
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/core/bootstrap.min.js"></script>
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/smooth-scrollbar.min.js"></script>
<!-- Kanban scripts -->
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/dragula/dragula.min.js"></script>
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/jkanban/jkanban.min.js"></script>

<!--   Core JS Files   -->

<script src="/asset/js/core/datatables.js"></script>

<script>
    $('#uploadReceiptModal').on('shown.bs.modal', function () {
        if (document.getElementById('payment_method')) {
            var element = document.getElementById('payment_method');
            const example = new Choices(element, {
                searchEnabled: false
            });
        }
    });
</script>
<script>
    const dataTableBasic = new simpleDatatables.DataTable("#datatable-basic", {
        searchable: false,
        fixedHeight: true
    });

    const dataTableSearch = new simpleDatatables.DataTable("#datatable-search", {
        searchable: true,
        fixedHeight: true
    });
    if (document.getElementById('payment_method')) {
        var language = document.getElementById('payment_method');
        const example = new Choices(language);
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
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/material-dashboard.min.js?v=3.1.0"></script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015" integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ==" data-cf-beacon='{"rayId":"9065de7f3b3040e7","serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"version":"2025.1.0","token":"1b7cbb72744b40c580f8633c6b62637e"}' crossorigin="anonymous"></script>
</body>

</html>
