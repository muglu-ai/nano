<style>
    @media (max-width: 767px) {
        .dropdown button .text-truncate {
            max-width: none;
        }

        .dropdown-menu {
            min-width: 100%;
        }

        .abc {
            margin-top: 10px;
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
<style>
    .btn-primary {
        --bs-btn-color: #0a0a0a !important;
        --bs-btn-bg: #e91e63 !important;
        --bs-btn-border-color: #e91e63 !important;
        --bs-btn-hover-color: #0a0a0a !important;
        --bs-btn-hover-bg: rgb(236.3, 63.75, 122.4) !important;
        --bs-btn-hover-border-color: rgb(235.2, 52.5, 114.6) !important;
        --bs-btn-focus-shadow-rgb: 200, 27, 86 !important;
        --bs-btn-active-color: #0a0a0a !important;
        --bs-btn-active-bg: rgb(237.4, 75, 130.2) !important;
        --bs-btn-active-border-color: rgb(235.2, 52.5, 114.6) !important;
        --bs-btn-active-shadow: none !important;
        --bs-btn-disabled-color: #0a0a0a !important;
        --bs-btn-disabled-bg: #e91e63 !important;
        --bs-btn-disabled-border-color: #e91e63 !important;
    }

    .bold-text {
        font-weight: bold !important;
        color: #ffffff !important;
    }
</style>
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
<style>
    .nav-pills .nav-link {
        border: 1px solid #ccc;
        /* Light grey border */
        border-radius: 5px;
        /* Rounded corners */
        margin-right: 5px;
        /* Space between links */
        background-color: #f8f9fa;
        /* Light grey background */
        color: #333;
        /* Dark text color */
    }

    .nav-pills .nav-link.active {
        /*border-color: #007bff; !* Blue border for active link *!*/
        background-color: #007bff;
        /* Blue background for active link */
        color: #fff;
        /* White text color for active link */
    }

    .nav-pills .nav-link:hover {
        border-color: #0056b3;
        /* Darker blue border on hover */
        background-color: #e2e6ea;
        /* Slightly darker grey background on hover */
        color: #0056b3;
        /* Darker blue text color on hover */
    }
</style>
@php
//make a switch case for the route if the user->id is 87 or 146
if (!Auth::check() || !isset(Auth::user()->id)) {
    $route = '/semicon-2025/sponsorship';
} else {
    switch (Auth::user()->id) {
        case 87:
        case 146:
        case 322:
        case 111:
        case 321:
        case 370:
        case 383:
        case 418:
        case 493:
        case 506:
        case 581:
        case 613:
        case 862:
        case 237:
        case 863:
        case 581:
        case 345:
        case 863:
        case 371:
        case 874:
        case 388:
        case 374:
        case 586:
        case 574:
        case 361:
        case 889:
        case 416:
        case 927:
        case 883:
            case 931:
            $route = '/semicon-2025/sponsorship_new';
            break;
        case 509:
        case 780:
        case 646:
        case 519:
        case 768:
        case 513:
            case 943:
            $route = '/semicon-2025/sponsorship_state';
            break;
        default:
            $route = '/semicon-2025/sponsorship';
            break;
    }
}

@endphp
<nav class="navbar navbar-main navbar-expand-lg position-sticky mt-2 top-1 px-0 py-1 mx-3 shadow-none border-radius-lg z-index-sticky"
    id="navbarBlur" data-scroll="true">
    <div class="container-fluid py-1 px-2 d-flex flex-column">

        <!-- First Row: Onboarding/Sponsorship (Left), Logos (Center), Profile (Right) -->
        <div class="d-flex justify-content-between align-items-center w-100 flex-wrap">
            <!-- Left: Onboarding & Sponsorship Buttons -->
            <div class="d-flex align-items-center">
                <a href="/semicon-2025/onboarding" class="btn btn-primary me-2 bold-text">Onboarding</a>
                <a href="{{ $route }}" class="btn btn-secondary bold-text">Sponsorship</a>
            </div>

            <!-- Center: Logos -->
            <div class="d-flex align-items-center flex-grow-1 justify-content-center">
                <div class="logo-container">
                    <img src="{{ asset('asset/img/logos/SEMI_IESA_logo.png') }}" alt="SEMI IESA Logo">
                    <img src="{{ asset('asset/img/logos/meity-logo.png') }}" alt="MeitY Logo">
                    <img src="{{ asset('asset/img/logos/ism_logo.png') }}" alt="ISM Logo">
                    <img src="{{ asset('asset/img/logos/DIC_Logo.webp') }}" alt="Digital India Logo">
                </div>
            </div>

            <!-- Right: Profile Button -->
            <div class="d-flex align-items-center">
                <div class="dropdown abc">
                    <button class="btn btn-light d-flex align-items-center" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-person-circle me-2"></i>
                        <span class="text-dark text-truncate d-none d-md-block">{{ Auth::user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
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
            </div>
        </div>

        @php
            $eventExists = (object) ['event_name' => ''];
            $eventExists->event_name = 'SEMICON India 2025';
        @endphp

        <!-- Second Row: Left-Aligned Navigation -->
        <div class="container-fluid mt-2">
            <nav class="nav nav-pills flex-column flex-md-row">
                <a class="nav-link active bold-black" href="{{ route('event.list') }}">
                    <i class="fa-solid fa-house"></i>
                </a>
                <a class="nav-link disabled bold-black" href="#"
                    aria-disabled="true">{{ $eventExists ? $eventExists->event_name : 'SEMICON' }}</a>
                <a class="nav-link bold-black" href="/semicon-2025/onboarding">Onboarding</a>
            </nav>
        </div>
    </div>
</nav>
