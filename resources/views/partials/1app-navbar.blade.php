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

<nav class="navbar navbar-main navbar-expand-lg position-sticky mt-2 top-1 px-0 py-1 mx-3 shadow-none border-radius-lg z-index-sticky" id="navbarBlur" data-scroll="true">
    <div class="container-fluid py-1 px-2 d-flex flex-column">

        <!-- First Row: Logo, Buttons, Logout & Email -->
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="d-flex align-items-center">
                <!-- Logo -->
                <a class="navbar-brand ms-2" href="/">
                    <svg class="navbar-brand-img" width="100" height="50" viewBox="0 0 163 40" xmlns="http://www.w3.org/2000/svg">
                        <path d="M43.751 18.973c-2.003-.363-4.369-.454-7.009-.363-8.011 9.623-20.846 17.974-29.403 19.064-2.093.272-3.641.091-4.915-.454.819.726 2.184 1.362 4.096 1.725 8.193 1.634 23.213-1.544 33.499-7.081 10.286-5.538 12.016-11.348 3.732-12.891zm-31.587 2.996c8.557-5.175 19.662-8.897 29.129-10.077C45.299 4.357 43.387-.454 35.923.545c-9.012 1.18-22.758 10.439-30.586 20.607-5.735 7.444-6.737 13.254-3.46 15.523-2.366-3.54 1.275-9.169 10.287-14.706zm58.35-.726l-4.643-1.271c-1.274-.363-1.911-.908-1.911-1.634 0-1.271 2.184-1.907 4.278-1.907 1.912 0 3.186.636 4.187 1.09.638.272 1.184.544 1.73.544 1.457 0 1.73-.635 1.73-1.18l-.182-.635c-.82-1.09-4.37-1.998-8.102-1.998-3.641 0-7.373 1.635-7.373 4.267 0 2.27 2.184 3.177 4.096 3.722l5.28 1.453c1.547.454 3.004.907 3.004 2.178 0 1.18-1.639 2.361-4.734 2.361-2.458 0-4.005-.817-5.098-1.453-.728-.363-1.274-.726-1.82-.726-.82 0-1.639.726-1.639 1.271 0 1.271 3.55 3.086 8.466 3.086 5.189 0 8.648-1.906 8.648-4.629-.091-2.724-3.004-3.722-5.917-4.539zm22.757-6.991c-6.554 0-10.013 4.086-10.013 8.08 0 3.722 2.731 8.079 10.559 8.079 5.371 0 9.103-2.178 9.103-3.268 0-1.271-1.183-1.271-1.638-1.271-.546 0-1.092.273-1.73.727-1.183.726-2.822 1.634-5.917 1.634-3.823 0-6.281-2.361-6.554-4.721h13.928c1.547 0 2.276-.454 2.276-1.452-.091-3.813-3.187-7.808-10.014-7.808zm6.19 6.991h-12.38c.273-2.452 2.367-4.812 6.19-4.812 3.732 0 5.917 2.451 6.19 4.812zm53.253-6.991c-1.093 0-1.73.545-1.73 1.544v12.981c0 .999.637 1.544 1.73 1.544 1.092 0 1.729-.545 1.729-1.544V15.796c0-.999-.637-1.544-1.729-1.544zm-26.399 2.633c1.457-1.543 4.096-2.633 6.645-2.633 4.006 0 8.375 1.816 8.375 5.72v8.896c0 .999-.637 1.543-1.73 1.543-1.092 0-1.729-.544-1.729-1.543v-8.442c0-2.542-1.639-3.722-4.916-3.722-2.458 0-5.006 1.361-5.006 3.722v8.442c0 .999-.638 1.543-1.73 1.543s-1.73-.544-1.73-1.543v-8.442c0-2.452-2.639-3.813-5.006-3.813-3.368 0-4.916 1.271-4.916 3.813v8.442c0 .999-.637 1.543-1.729 1.543-1.093 0-1.73-.544-1.73-1.543v-8.896c0-3.904 4.37-5.72 8.375-5.72 2.64 0 5.189.999 6.645 2.633l.182.091v-.091zm33.044-1.906h-.455a.196.196 0 0 1-.182-.182c0-.091.091-.181.182-.181h1.365c.091 0 .182.09.182.181a.196.196 0 0 1-.182.182h-.455v1.634c0 .091-.091.181-.182.181-.182 0-.182-.09-.182-.181v-1.634h-.091zm1.365 0c0-.273.091-.363.273-.363.091 0 .273 0 .364.181l.547 1.362.455-1.362c.091-.181.182-.181.364-.181s.273.09.273.363v1.634c0 .091-.091.181-.182.181s-.182-.09-.182-.181V15.07l-.546 1.543c0 .181-.091.181-.182.181s-.182-.09-.182-.181l-.547-1.543v1.543c0 .091-.091.181-.182.181s-.182-.09-.182-.181v-1.634h-.091z" id="Shape" fill-rule="nonzero"></path>
                    </svg>

                </a>
                <style>
                   .btn-primary {
                        --bs-btn-color: #0a0a0a !important;
                        --bs-btn-bg: #e91e63 !important;
                        --bs-btn-border-color: #e91e63 !important;
                        --bs-btn-hover-color: #0a0a0a !important;
                        --bs-btn-hover-bg: rgb(236.3,63.75,122.4) !important;
                        --bs-btn-hover-border-color: rgb(235.2,52.5,114.6) !important;
                        --bs-btn-focus-shadow-rgb: 200,27,86 !important;
                        --bs-btn-active-color: #0a0a0a !important;
                        --bs-btn-active-bg: rgb(237.4,75,130.2) !important;
                        --bs-btn-active-border-color: rgb(235.2,52.5,114.6) !important;
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

                <!-- Buttons for Onboarding & Sponsorship -->
                <a href="/semicon-2025/onboarding" class="btn btn-primary ms-3 me-2 d-none d-md-inline bold-text" >Onboarding</a>
                <a href="/semicon-2025/sponsorship" class="btn btn-secondary d-none d-md-inline bold-text">Sponsorship</a>
            </div>

            <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">
                <div class="ms-md-auto pe-md-3 d-flex flex-column flex-lg-row align-items-center">
                    <!-- Profile Icon & Email -->
                    <div class="dropdown abc">
                        <!-- Clickable User Icon & Email -->
                        <button class="btn btn-light d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-2"></i>
                            <!-- Email shown by default on medium and larger screens -->
                            <span class="text-dark text-truncate d-none d-md-block">{{ Auth::user()->name }}</span>
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
                </div>
            </div>

            <!-- Logout & Email -->
{{--            <div class="d-flex align-items-center d-none d-md-flex">--}}
{{--                <span class="me-3 text-dark"><i class="fa-solid fa-envelope"></i> {{ Auth::user()->email }}</span>--}}
{{--                <form method="POST" action="{{ route('logout') }}">--}}
{{--                    @csrf--}}
{{--                    <button type="submit" class="waves-effect waves-grey" style="display: inline-flex; align-items: center; gap: 5px; background: none; border: none; cursor: pointer;">--}}
{{--                        <i class="fa-solid fa-right-from-bracket"></i>--}}
{{--                        Sign&nbsp;Out--}}
{{--                    </button>--}}
{{--                </form>--}}
{{--            </div>--}}
        </div>


        <style>
            .nav-pills .nav-link {
                border: 1px solid #ccc; /* Light grey border */
                border-radius: 5px; /* Rounded corners */
                margin-right: 5px; /* Space between links */
                background-color: #f8f9fa; /* Light grey background */
                color: #333; /* Dark text color */
            }

            .nav-pills .nav-link.active {
                /*border-color: #007bff; !* Blue border for active link *!*/
                background-color: #007bff; /* Blue background for active link */
                color: #fff; /* White text color for active link */
            }

            .nav-pills .nav-link:hover {
                border-color: #0056b3; /* Darker blue border on hover */
                background-color: #e2e6ea; /* Slightly darker grey background on hover */
                color: #0056b3; /* Darker blue text color on hover */
            }
        </style>
        @php
            $eventExists = (object) ['event_name' => ''];
            $eventExists->event_name = "SEMICON India 2025";
        @endphp
        <style>
            /* .bold-black {
                font-weight: bold !important;
                color: #000000 !important;
            } */
            </style>
        <!-- Second Row: Left-Aligned Navigation -->
        <div class="container-fluid mt-2">
            <nav class="nav nav-pills flex-column flex-md-row">
                <a class="nav-link active bold-black" href="{{ route('event.list') }}">
                    <i class="fa-solid fa-house"></i>
                </a>
                <a class="nav-link disabled bold-black" href="#" aria-disabled="true">{{ $eventExists ? $eventExists->event_name : 'SEMICON' }}</a>
                <a class="nav-link bold-black" href="/semicon-2025/onboarding">Onboarding</a>
            </nav>
        </div>
    </div>
</nav>



{{--<nav class="navbar navbar-main navbar-expand-lg position-sticky mt-2 top-1 px-0 py-1 mx-3 shadow-none border-radius-lg z-index-sticky" id="navbarBlur" data-scroll="true">--}}
{{--    <div class="container-fluid py-1 px-2">--}}
{{--        <nav aria-label="breadcrumb" class="ps-2">--}}
{{--            <ol class="breadcrumb bg-transparent mb-0 p-0">--}}
{{--                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Home</a></li>--}}
{{--                <li class="breadcrumb-item text-sm text-dark active font-weight-bold" aria-current="page">@yield('title')</li>--}}
{{--            </ol>--}}
{{--        </nav>--}}
{{--        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4" id="navbar">--}}
{{--            <div class="ms-md-auto pe-md-3 d-flex align-items-center">--}}
{{--                <form method="POST" action="{{ route('logout') }}">--}}
{{--                    @csrf--}}
{{--                    <button type="submit" class="waves-effect waves-grey" style="display: inline-flex; align-items: center; gap: 5px; background: none; border: none; cursor: pointer;">--}}
{{--                        <i class="fa-solid fa-right-from-bracket"></i>--}}
{{--                        Sign&nbsp;Out--}}
{{--                    </button>--}}
{{--                </form>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</nav>--}}
