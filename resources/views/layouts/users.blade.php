<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png"
          type="image/vnd.microsoft.icon"/>
    <title>
        @yield('title', config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))
    </title>


    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
          href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900"/>
    <!-- Nucleo Icons -->
    <link href="{{ asset('asset/css/nucleo-icons.css') }}" rel="stylesheet"/>
    <link href="{{ asset('asset/css/nucleo-svg.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/asset/css/nucleo-icons.css') }}" rel="stylesheet"/>
    <link href="{{ asset('public/asset/css/nucleo-svg.css') }}" rel="stylesheet"/>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css"
          integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ=="
          crossorigin="anonymous" referrerpolicy="no-referrer">
    <!-- Material Icons -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0"/>
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('public/asset/css/material-dashboard.min.css?v=3.1.0') }}" rel="stylesheet"/>
    <link id="pagestyle" href="{{ asset('asset/css/material-dashboard.min.css?v=3.1.0') }}" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css"/>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}"/>
    <link rel="stylesheet" href="{{ asset('public/assets/css/custom.css') }}"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Anti-flicker snippet (recommended)  -->
    <style>
        .async-hide {
            opacity: 0 !important
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">

@if (Auth::user()->role === 'co-exhibitor')
    @include('partials.couser_sidebar')
@else
    @include('partials.user_sidebar')
@endif
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    <!-- Navbar -->
    @include('partials.logo')
    @include('partials.navbar')
    <!-- End Navbar -->
    @yield('content')

    <div class="position-fixed bottom-1 end-1 z-index-2">
        @if (session('payment_success'))
            <div class="toast show p-2 mt-2 bg-white" role="alert" aria-live="assertive" id="successToast"
                 aria-atomic="true" style="max-width: 100%; width: auto;">
                <div class="toast-header border-0">
                    <i class="material-symbols-rounded text-success me-2">
                        check
                    </i>
                    <span class="me-auto text-gradient text-success font-weight-bold">Success </span>
                    <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast"
                       aria-label="Close"></i>
                </div>
                <hr class="horizontal dark m-0">
                <div class="toast-body">
                    {{ session('payment_message') }}<br>
                    <strong>Invoice No:</strong> {{ session('invoice_no') }}
                </div>
            </div>
            <script>
                setTimeout(function () {
                    var toast = document.getElementById('successToast');
                    if (toast) {
                        toast.style.display = 'none';
                    }
                }, 15000);
            </script>
        @endif
        @php
            // clear the session data after displaying the toast
            session()->forget('payment_success');
            session()->forget('payment_message');
            session()->forget('invoice_no');

        @endphp
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
                                        </script> Copyright {{ config('constants.EVENT_NAME') }}, All Rights Reserved
                                    </span>
                                <span class="me-2">Powered by SCI Knowledge Interlinks Pvt. Ltd. (MM Activ)</span>
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
<script src="{{ asset('asset/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('asset/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('asset/js/plugins/smooth-scrollbar.min.js') }}"></script>
<script src="{{ asset('public/asset/js/core/popper.min.js') }}"></script>
<script src="{{ asset('public/asset/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('public/asset/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('public/asset/js/plugins/smooth-scrollbar.min.js') }}"></script>


<!--   Core JS Files   -->

<script src="{{ asset('public/asset/js/core/datatables.js') }}"></script>
<script src="{{ asset('asset/js/core/datatables.js') }}"></script>

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
<script src="{{ asset('public/asset/js/material-dashboard.min.js?v=3.1.0') }}"></script>
<script src="{{ asset('asset/js/material-dashboard.min.js?v=3.1.0') }}"></script>
<script defer src="https://static.cloudflareinsights.com/beacon.min.js/vcd15cbe7772f49c399c6a5babf22c1241717689176015"
        integrity="sha512-ZpsOmlRQV6y907TI0dKBHq9Md29nnaEIPlkf84rnaERnq6zvWvPUqr2ft8M1aS28oN72PdrCzSjY4U6VaAw1EQ=="
        data-cf-beacon='{"rayId":"9065de7f3b3040e7","serverTiming":{"name":{"cfExtPri":true,"cfL4":true,"cfSpeedBrain":true,"cfCacheStatus":true}},"version":"2025.1.0","token":"1b7cbb72744b40c580f8633c6b62637e"}'
        crossorigin="anonymous"></script>
</body>

</html>
