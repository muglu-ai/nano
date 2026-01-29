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
                    <li class="breadcrumb-item text-sm">
                        <a class="opacity-5 text-dark text-decoration-none" href="{{ route('delegate.dashboard') }}">
                            <i class="fas fa-home me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item text-sm active font-weight-bold" aria-current="page" style="color: #2d3748;">
                        @yield('title')
                    </li>
                </ol>
            </nav>
        </div>
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item dropdown pe-2">
                    <a class="nav-link text-body p-0 position-relative" href="javascript:;" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                <i class="fas fa-user-circle text-white text-sm"></i>
                            </div>
                            <span class="d-sm-inline d-none ms-1" style="color: #2d3748; font-weight: 500;">{{ Auth::guard('delegate')->user()->contact->name ?? 'Delegate' }}</span>
                            <i class="fas fa-chevron-down ms-1 text-xs" style="color: #4a5568;"></i>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" aria-labelledby="dropdownMenuButton">
                        <li>
                            <a class="dropdown-item border-radius-md" href="{{ route('delegate.dashboard') }}">
                                <div class="d-flex align-items-center py-1">
                                    <i class="fas fa-home me-2 text-primary"></i>
                                    <div class="ms-2">
                                        <h6 class="text-sm font-weight-normal mb-0" style="color: #2d3748;">Dashboard</h6>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item border-radius-md" href="{{ route('delegate.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <div class="d-flex align-items-center py-1">
                                    <i class="fas fa-sign-out-alt me-2 text-danger"></i>
                                    <div class="ms-2">
                                        <h6 class="text-sm font-weight-normal mb-0" style="color: #2d3748;">Logout</h6>
                                    </div>
                                </div>
                            </a>
                            <form id="logout-form" action="{{ route('delegate.logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
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

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .dropdown-menu {
        border-radius: 12px;
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25);
        border: none;
        padding: 0.5rem;
    }
    
    .dropdown-item {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }
    
    .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        transform: translateX(5px);
    }
</style>
