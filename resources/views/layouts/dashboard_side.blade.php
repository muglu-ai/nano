

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />
    <title>
        {{ config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR') . ' Admin Panel' }}
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
    <!-- Anti-flicker snippet (recommended)  -->
    <style>
        .async-hide {
            opacity: 0 !important
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">
<!-- Extra details for Live View on GitHub Pages -->
{{--@include('partials.sidebar')--}}
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
    <!-- Navbar -->
    @include('partials.navbar2')
    <!-- End Navbar -->
    @yield('content')

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
<script src="{{ asset('asset/js/core/datatables.js') }}"></script>
<script src="{{ asset('public/asset/js/core/datatables.js') }}"></script>


</body>

</html>
