


    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>@yield('title', 'SEEMICON 2025')</title>
        <link rel="apple-touch-icon" sizes="76x76" href="/asset/img/apple-icon.png">
        <link rel="icon" type="image/png" href="/asset/img/favicon.png">
        <!--     Fonts and icons     -->
        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
        <!-- Nucleo Icons -->
        <link href="https://demos.creative-tim.com/material-dashboard-pro/assets/asset/css/nucleo-icons.css" rel="stylesheet" />
        <link href="https://demos.creative-tim.com/material-dashboard-pro/assets/asset/css/nucleo-svg.css" rel="stylesheet" />
        <!-- Font Awesome Icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
        <!-- Material Icons -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

        <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>

        <!-- CSS Files -->
        <link id="pagestyle" href="https://demos.creative-tim.com/material-dashboard-pro/assets/css/material-dashboard.min.css?v=3.1.0" rel="stylesheet" />
        <style>
            .async-hide {
                opacity: 0 !important
            }
        </style>
    </head>
    <body class="g-sidenav-show bg-gray-100">
@include('components.dash_sidebar')
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">

@include('components.header_nav')

@yield('content')
</main>

<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/core/popper.min.js"></script>
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/core/bootstrap.min.js"></script>
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/perfect-scrollbar.min.js"></script>
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/smooth-scrollbar.min.js"></script>
<!-- Kanban scripts -->
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/dragula/dragula.min.js"></script>
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/jkanban/jkanban.min.js"></script>
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/chartjs.min.js"></script>
<script src="https://demos.creative-tim.com/material-dashboard-pro/assets/js/plugins/world.js"></script>




<!-- Javascripts -->
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
<script src="/asset/js/material-dashboard.min.js?v=3.2.0"></script>


<!-- Page Container -->
</body>
</html>
