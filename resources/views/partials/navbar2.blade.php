
<style>
    @media (max-width: 767px) {
        .dropdown button .text-truncate {
            max-width: none;
        }
        .dropdown-menu {
            min-width: 100%;
        }

        .abc{
         margin-top:10px;
         margin-left: 40px;

        }
    }
    .navbar-brand span {
        display: block;
        margin: 0;
        padding: 0;
    }

    #navbarBlur {
        background-color: #fff;
    }

</style>

<nav class="navbar navbar-main navbar-expand-lg position-sticky px-0 py-1 mx-3 shadow-none border-radius-lg z-index-sticky" id="navbarBlur" data-scroll="true">
    <div class="container py-1 px-2 d-flex justify-content-between align-items-center">
        <!-- Logo -->
        <style>
.logo-container {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: center;
    background-color: white;
    gap: 24px;
    padding: 16px 0;
}
.logo-container img {
    max-width: 140px;
    width: 100%;
    height: auto;
    object-fit: contain;
    transition: transform 0.2s;
}
.logo-container img:hover {
    transform: scale(1.05);
}
@media (max-width: 600px) {
    .logo-container {
        gap: 12px;
        padding: 8px 0;
    }
    .logo-container img {
        max-width: 90px;
    }
}
</style>

<div class="logo-container">
    <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.event_logo') }}">
</div>
        {{-- <a class="navbar-brand px-4 py-3 m-0 pb-0 abc" href="#">
            <img src="/asset/img/logos/logo.png" alt="Logo" width="160" class="img-fluid">
            <span class="text-md text-dark">SEMICON India 2025</span>
        </a> --}}
        <!-- Navbar Toggler for Mobile -->
        <button class="navbar-toggler " type="button" data-bs-toggle="collapse" data-bs-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navbar Items -->
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
            <div class="ms-md-auto pe-md-3 d-flex flex-column flex-lg-row align-items-center">
                <!-- Profile Icon & Email -->
                <div class="dropdown abc">
                    <!-- Clickable User Icon & Email -->
                    <button class="btn btn-light d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-2 "></i>
                        <!-- Email shown by default on medium and larger screens -->
                        <span class="text-dark text-truncate">{{ Auth::user()->name }}</span>
                    </button>

                    <!-- Dropdown Menu -->
                    <ul class="dropdown-menu dropdown-menu-end">
                        <!-- Display email inside the dropdown -->
                        <li>
                        <span class="dropdown-item disabled">
                            <i class="bi bi-person-circle me-2"></i>
                            {{ Auth::user()->email }}
                        </span>
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span>Sign Out</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

{{--                 Sidebar Toggle for Mobile --}}
{{--            <ul class="navbar-nav d-xl-none ps-3 d-flex align-items-center">--}}
{{--                <li class="nav-item">--}}
{{--                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">--}}
{{--                        <div class="sidenav-toggler-inner">--}}
{{--                            <i class="sidenav-toggler-line"></i>--}}
{{--                            <i class="sidenav-toggler-line"></i>--}}
{{--                            <i class="sidenav-toggler-line"></i>--}}
{{--                        </div>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            </ul>--}}
        </div>
    </div>
    </div>
</nav>




