<style>
    .navbar-logo-container {
        width: 100%;
        text-align: center;
        padding: 8px 0;
        border-bottom: 1px solid #e3e6f0;
        margin-bottom: 10px;
    }
    
    .navbar-logo {
        max-width: 250px;
        max-height: 70px;
        width: auto;
        height: auto;
        object-fit: contain;
    }
    
    @media (max-width: 768px) {
        .navbar-logo {
            max-width: 180px;
            max-height: 50px;
        }
    }
</style>

<style>
    .navbar-logo-container {
        width: 100%;
        text-align: center;
        padding: 8px 0;
        border-bottom: 1px solid #e3e6f0;
        margin-bottom: 10px;
    }
    
    .navbar-logo {
        max-width: 250px;
        max-height: 70px;
        width: auto;
        height: auto;
        object-fit: contain;
    }
    
    @media (max-width: 768px) {
        .navbar-logo {
            max-width: 180px;
            max-height: 50px;
        }
    }
</style>

<nav class="navbar navbar-main navbar-expand-lg position-sticky mt-2 top-1 px-0 py-1 mx-3 shadow-none border-radius-lg z-index-sticky" id="navbarBlur" data-scroll="true">
    <div class="container-fluid py-1 px-2">
        <!-- Logo Section Above Breadcrumb -->
        <div class="navbar-logo-container">
            @if(config('constants.event_logo'))
                <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.EVENT_NAME') }} Logo" class="navbar-logo">
            @else
                <img src="{{ asset('asset/img/logos/SEMI_IESA_logo.png') }}" alt="Logo" class="navbar-logo">
            @endif
        </div>
        
        <div class="d-flex align-items-center w-100">
            <div class="sidenav-toggler sidenav-toggler-inner d-xl-block d-none ">
                <a href="javascript:void(0)" class="nav-link text-body p-0" onclick="toggleSidebar()" >
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </div>
                </a>
            </div>
            @php
            $route = 'user.dashboard'; // Default route
            if(Auth::user()->role === 'super-admin') {
                $route = 'super-admin.event-config';
            } else if(Auth::user()->role === 'co-exhibitor') {
                $route = 'dashboard.co-exhibitor';
            } else if(Auth::user()->role === 'exhibitor') {
                $route = 'user.dashboard';
            } else if(Auth::user()->role === 'admin') {
                $route = 'dashboard.admin';
                if(Auth::user()->sub_role === 'visitor') {
                    $route = 'registration.analytics';
                }
            }
            @endphp
            <nav aria-label="breadcrumb" class="ps-2">
                <ol class="breadcrumb bg-transparent mb-0 p-0">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{route($route)}}">Dashboard</a></li>
                    <li class="breadcrumb-item text-sm text-dark active font-weight-bold" aria-current="page">@yield('title')</li>
                </ol>
            </nav>
        </div>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex align-items-center">
{{--                <div class="input-group input-group-outline">--}}
{{--                    <label class="form-label">Search</label>--}}
{{--                    <input type="text" class="form-control">--}}
{{--                </div>--}}

            </div>

            <ul class="navbar-nav  justify-content-end">
                {{--                    <li class="nav-item">--}}
                {{--                        <a href="../../pages/authentication/signin/illustration.html" class="px-1 py-0 nav-link line-height-0" target="_blank">--}}
                {{--                            <i class="material-symbols-rounded">--}}
                {{--                                account_circle--}}
                {{--                            </i>--}}
                {{--                        </a>--}}
                {{--                    </li>--}}
                {{--                    <li class="nav-item">--}}
                {{--                        <a href="javascript:;" class="nav-link py-0 px-1 line-height-0">--}}
                {{--                            <i class="material-symbols-rounded fixed-plugin-button-nav">--}}
                {{--                                settings--}}
                {{--                            </i>--}}
                {{--                        </a>--}}
                {{--                    </li>--}}
{{--                <li class="nav-item dropdown py-0 pe-3">--}}
{{--                    <a href="javascript:;" class="nav-link py-0 px-1 position-relative line-height-0" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">--}}
{{--                        <i class="material-symbols-rounded">--}}
{{--                            notifications--}}
{{--                        </i>--}}
{{--                        <span class="position-absolute top-5 start-100 translate-middle badge rounded-pill bg-danger border border-white small py-1 px-2">--}}
{{--                  <span class="small">11</span>--}}
{{--                  <span class="visually-hidden">unread notifications</span>--}}
{{--                </span>--}}
{{--                    </a>--}}
{{--                    <ul class="dropdown-menu dropdown-menu-end p-2 me-sm-n4" aria-labelledby="dropdownMenuButton">--}}
{{--                        <li class="mb-2">--}}
{{--                            <a class="dropdown-item border-radius-md" href="javascript:;">--}}
{{--                                <div class="d-flex align-items-center py-1">--}}
{{--                                    <span class="material-symbols-rounded">email</span>--}}
{{--                                    <div class="ms-2">--}}
{{--                                        <h6 class="text-sm font-weight-normal my-auto">--}}
{{--                                            Check new messages--}}
{{--                                        </h6>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li class="mb-2">--}}
{{--                            <a class="dropdown-item border-radius-md" href="javascript:;">--}}
{{--                                <div class="d-flex align-items-center py-1">--}}
{{--                                    <span class="material-symbols-rounded">podcasts</span>--}}
{{--                                    <div class="ms-2">--}}
{{--                                        <h6 class="text-sm font-weight-normal my-auto">--}}
{{--                                            Manage podcast session--}}
{{--                                        </h6>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                        <li>--}}
{{--                            <a class="dropdown-item border-radius-md" href="javascript:;">--}}
{{--                                <div class="d-flex align-items-center py-1">--}}
{{--                                    <span class="material-symbols-rounded">shopping_cart</span>--}}
{{--                                    <div class="ms-2">--}}
{{--                                        <h6 class="text-sm font-weight-normal my-auto">--}}
{{--                                            Payment successfully completed--}}
{{--                                        </h6>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </a>--}}
{{--                        </li>--}}
{{--                    </ul>--}}
{{--                </li>--}}
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
