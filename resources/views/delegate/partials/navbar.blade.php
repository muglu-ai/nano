<nav class="navbar navbar-main navbar-expand-lg position-sticky mt-2 top-1 px-0 py-1 mx-3 shadow-none border-radius-lg z-index-sticky" id="navbarBlur" data-scroll="true">
    <div class="container-fluid py-1 px-2">
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
            <nav aria-label="breadcrumb" class="ps-2">
                <ol class="breadcrumb bg-transparent mb-0 p-0">
                    <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="{{ route('delegate.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item text-sm text-dark active font-weight-bold" aria-current="page">@yield('title')</li>
                </ol>
            </nav>
        </div>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item">
                    <span class="nav-link text-body">
                        <i class="fas fa-user-circle me-1"></i>
                        {{ Auth::guard('delegate')->user()->contact->name ?? 'Delegate' }}
                    </span>
                </li>
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
