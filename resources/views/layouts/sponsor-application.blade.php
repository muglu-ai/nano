

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
    <style>
        @media (min-width: 500px) {
            .progress-bar {
                display: none !important;
            }
        }

        /* Loader Style */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body class="g-sidenav-show  bg-gray-100">

<div id="loader"  class="loader">
    <div class="spinner"> </div>
</div>

<script>
    window.onload = function() {
        // Hide the loader and show the content
        document.getElementById('loader').style.display = 'none';
        document.getElementById('main-content').style.display = 'block';
    };
</script>

<script>
    $(window).on('load', function() {
        // Hide the loader and show the content
        $('#loader').fadeOut();
        $('#main-content').fadeIn();
    });
</script>
<!-- Extra details for Live View on GitHub Pages -->
{{--@include('partials.sidebar')--}}
<main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg " id="main-content" style="display: none;">
    <!-- Navbar -->
    @include('partials.app-navbar')
    <!-- End Navbar -->
    @yield('content')
    <div class="position-fixed bottom-1 end-1 z-index-2">
        @if (session('success'))
            <div class="toast show p-2 mt-2 bg-white" role="alert" aria-live="assertive" id="successToast" aria-atomic="true" style="max-width: 100%; width: auto;">
                <div class="toast-header border-0">
                    <i class="material-symbols-rounded text-success me-2">
                        check
                    </i>
                    <span class="me-auto text-gradient text-success font-weight-bold">Success </span>
                    <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
                </div>
                <hr class="horizontal dark m-0">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="toast fade hide p-2 bg-white" role="alert" aria-live="assertive" id="successToast" aria-atomic="true">
            <div class="toast-header border-0">
                <i class="material-symbols-rounded text-success me-2">
                    check
                </i>
                <span class="me-auto font-weight-bold">Material Dashboard </span>
                <small class="text-body">11 mins ago</small>
                <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <hr class="horizontal dark m-0">
            <div class="toast-body">
                Hello, world! This is a notification message.
            </div>
        </div>
        <div class="toast fade hide p-2 mt-2 bg-gradient-info" role="alert" aria-live="assertive" id="infoToast" aria-atomic="true">
            <div class="toast-header bg-transparent border-0">
                <i class="material-symbols-rounded text-white me-2">
                    notifications
                </i>
                <span class="me-auto text-white font-weight-bold">Material Dashboard </span>
                <small class="text-white">11 mins ago</small>
                <i class="fas fa-times text-md text-white ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <hr class="horizontal light m-0">
            <div class="toast-body text-white">
                Hello, world! This is a notification message.
            </div>
        </div>
        <div class="toast fade hide p-2 mt-2 bg-white" role="alert" aria-live="assertive" id="warningToast" aria-atomic="true">
            <div class="toast-header border-0">
                <i class="material-symbols-rounded text-warning me-2">
                    travel_explore
                </i>
                <span class="me-auto font-weight-bold">Material Dashboard </span>
                <small class="text-body">11 mins ago</small>
                <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
            </div>
            <hr class="horizontal dark m-0">
            <div class="toast-body">
                Hello, world! This is a notification message.
            </div>
        </div>
        @if ($errors->any())
            <div class="toast show p-2 mt-2 bg-white" role="alert" aria-live="assertive" id="dangerToast" aria-atomic="true" style="max-width: 100%; width: auto;">
                <div class="toast-header border-0">
                    <i class="material-symbols-rounded text-danger me-2">
                        campaign
                    </i>
                    <span class="me-auto text-gradient text-danger font-weight-bold">Error </span>

                    <i class="fas fa-times text-md ms-3 cursor-pointer" data-bs-dismiss="toast" aria-label="Close"></i>
                </div>
                <hr class="horizontal dark m-0">
                <div class="toast-body">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</main>
<script src="/asset/js/core/datatables.js"></script>



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
    if (document.getElementById('edit-deschiption')) {
        var quill = new Quill('#edit-deschiption', {
            theme: 'snow' // Specify theme in configuration
        });
    };

    if (document.getElementById('country-list')) {
        var element = document.getElementById('country-list');
        const example = new Choices(element, {
            searchEnabled: false
        });
    };

    if (document.getElementById('products-list')) {
        var element = document.getElementById('products-list');
        const example = new Choices(element, {
            searchEnabled: false
        });
    };

    if (document.getElementById('choices-language')) {
        var element = document.getElementById('choices-language');
        const example = new Choices(element, {
            searchEnabled: true
        });
    };
    // if (document.getElementById('billing_country')) {
    //     var element = document.getElementById('billing_country');
    //     const example = new Choices(element, {
    //         searchEnabled: true
    //     });
    // };
    // if (document.getElementById('state')) {
    //     var element = document.getElementById('state');
    //     const example = new Choices(element, {
    //         searchEnabled: false
    //     });
    // };
    if (document.getElementById('contactNoCode')) {
        var element = document.getElementById('contactNoCode');
        const example = new Choices(element, {
            searchEnabled: false
        });
    };
    if (document.getElementById('billing_phoneCode')) {
        var element = document.getElementById('billing_phoneCode');
        const example = new Choices(element, {
            searchEnabled: false
        });
    };
    if (document.getElementById('contactPhone_code')) {
        var element = document.getElementById('contactPhone_code');
        const example = new Choices(element, {
            searchEnabled: false
        });
    };
    if (document.getElementById('salutation')) {
        var element = document.getElementById('salutation');
        const example = new Choices(element, {
            searchEnabled: false
        });
    };

    if (document.getElementById('choices-category')) {
        var element = document.getElementById('choices-category');
        const example = new Choices(element, {
            searchEnabled: false
        });
    };

    // if (document.getElementById('choices-sizes')) {
    //     var element = document.getElementById('choices-sizes');
    //     const example = new Choices(element, {
    //         searchEnabled: false
    //     });
    // };
    if (document.getElementById('choice-sqm')) {
        var element = document.getElementById('choice-sqm');
        const example = new Choices(element, {
            searchEnabled: false
        });
    };

    if (document.getElementById('choices-currency')) {
        var element = document.getElementById('choices-currency');
        const example = new Choices(element, {
            searchEnabled: false
        });
    };

    if (document.getElementById('choices-tags')) {
        var tags = document.getElementById('choices-tags');
        const examples = new Choices(tags, {
            removeItemButton: true
        });

        // examples.setChoices(
        //     [{
        //         value: 'One',
        //         label: 'Expired',
        //         disabled: true
        //     },
        //         {
        //             value: 'Two',
        //             label: 'Out of Stock',
        //             selected: true
        //         }
        //     ],
        //     'value',
        //     'label',
        //     false,
        // );
    }

    // if (document.getElementById('choices-language')) {
    //     var language = document.getElementById('choices-language');
    //     const example = new Choices(language);
    // }

</script>


<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{ asset('asset/js/core/datatables.js') }}"></script>
<script src="{{ asset('public/asset/js/core/datatables.js') }}"></script>
</body>

</html>
