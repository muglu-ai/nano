@php
    use Illuminate\Support\Facades\Auth;

    if (!Auth::check()) {
        echo redirect()->route('login')->send();
        exit();
    }

    $subRole = Auth::user()->sub_role ?? 'admin';
    $role = Auth::user()->role ?? 'admin';

    $isFullAdmin = $role === 'admin' && $subRole === 'admin';

    $accessMap = [
        'admin' => [
            'dashboard',
            'sales',
            'users',
            'extra',
            'invoices',
            'sponsors',
            'exhibitors',
            'co_exhibitors',
            'visitors',
            'passes',
        ],
        'sales' => ['sales', 'invoices'],
        'visitor' => [
            'registration.analytics',
            'visitor.list',
            'view.attendee.details',
            'attendees.mass.approve',
            'exhibitor.list',
            'registration.matrix',
        ],
        'sponsor' => ['sponsors'],
        'exhibitor' => ['exhibitors', 'co_exhibitors', 'passes'],
        'extra_requirements' => ['extra.analytics', 'extra_requirements.admin', 'extra_requirements.admin.show', 'extra_requirements.admin.leadRetrieval'],
    ];

    $allowed = $accessMap[$subRole] ?? [];
    $currentRoute = request()->route()->getName();

    if ($subRole === 'visitor' && !in_array($currentRoute, $allowed)) {
        echo abort(403, 'Unauthorized action.');
        exit();
    }

    if ($role === 'admin' && $subRole === 'extra_requirements' && !in_array($currentRoute, $allowed)) {
        echo abort(403, 'Unauthorized action.');
        exit();
    }


    // if ($role === 'admin' && $subRole === 'visitor' && $currentRoute !== 'registration.analytics') {
    //     echo redirect()->route('registration.analytics')->send();
    //     exit();
    // }

        $hiddenRoutes = [
        'invoice.list',
        'sponsorship.lists',
        'co_exhibitors',
        'application.list',
        'application.lists',
        'visitor.analytics',
        'sales.index',
        'admin.stall-manning',
        // add more route names to hide as needed
    ];

    // make a list of name which will hide if the name mathces
@endphp

<style>
    .nav-link-text {
        white-space: normal !important;
        word-break: break-word;
        display: inline-block;
        max-width: 160px;
        /* Adjust as needed for your sidebar width */
        vertical-align: middle;
    }
</style>



<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2  bg-white my-2"
       style="background: #FFFFFF;" id="sidenav-main">
    <hr class="horizontal dark mt-0 mb-2">
    <div class="collapse navbar-collapse  w-auto h-auto  min-vh-75" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <li class="nav-item mb-2 mt-0">

                <p href="#ProfileNav" class="nav-link text-dark d-flex align-items-center" aria-controls="ProfileNav"
                   role="button" aria-expanded="false">
                    <span class="ms-2 ps-1 text-truncate" style="max-width: 150px;">{{ Auth::user()->name }}</span>
                </p>
            </li>

            <hr class="horizontal dark mt-0">
            @if (in_array('dashboard', $allowed))
                <li class="nav-item">
                    <ul class="nav ">
                        <li class="nav-item active">
                            @php
                                $active = route('dashboard.admin') ? 'active' : '';
                            @endphp
                            <a class="nav-link text-dark" href="{{ route('dashboard.admin') }}">
                                {{--                            <i class="fa-solid fa-chart-line"></i> --}}
                                {{--                            <i class="material-symbols-rounded  text-dark " style="margin-left:-6px">space_dashboard</i> --}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                     fill="currentColor" class="bi bi-ui-checks-grid" viewBox="0 0 16 16"
                                     style="margin-left:-5px">
                                    <path
                                            d="M2 10h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1m9-9h3a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-3a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1m0 9a1 1 0 0 0-1 1v3a1 1 0 0 0 1 1h3a1 1 0 0 0 1-1v-3a1 1 0 0 0-1-1zm0-10a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h3a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM2 9a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h3a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2zm7 2a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-3a2 2 0 0 1-2-2zM0 2a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm5.354.854a.5.5 0 1 0-.708-.708L3 3.793l-.646-.647a.5.5 0 1 0-.708.708l1 1a.5.5 0 0 0 .708 0z" />
                                </svg>
                                <span class="nav-link-text text-dark ms-1"> Dashboard </span>
                            </a>
                        </li>
                    </ul>
                </li>
                @if(!in_array('sales.index', $hiddenRoutes))
                <li class="nav-item">
                    <a href="{{ route('sales.index') }}" class="nav-link text-dark " aria-controls="pagesExamples" role="button"
                       aria-expanded="false">
                        <i class="fa-solid fa-chart-line"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark ">Sales</span>
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a href="{{ route('users.list') }}" class="nav-link text-dark " aria-controls="pagesExamples" role="button"
                       aria-expanded="false">
                        <i class="fa-regular fa-user"></i>
                        <span class="nav-link-text ms-1 ps-1">Users</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#extraRequirements" class="nav-link text-dark"
                       aria-controls="extraRequirements" role="button" aria-expanded="false">
                        <i class="fa-solid fa-list"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark">Extra Requirements</span>
                    </a>
                    <div class="collapse" id="extraRequirements">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('extra_requirements.admin.show') }}">
                                    <span class="sidenav-mini-icon"> R </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">Master Requirement List</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('extra_requirements.admin') }}">
                                    <span class="sidenav-mini-icon"> O </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">Orders</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('extra.analytics') }}">
                                    <span class="sidenav-mini-icon"> A </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">Analytics</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('extra_requirements.admin.leadRetrieval') }}">
                                    <span class="sidenav-mini-icon"> L </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">Lead Retrieval List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @if(!in_array('invoice.list', $hiddenRoutes))
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#invoices" class="nav-link text-dark "
                       aria-controls="pagesExamples" role="button" aria-expanded="false">
                        <i class="fa-solid fa-file-invoice"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark ">Invoices</span>
                    </a>
                    <div class="collapse" id="invoices">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('invoice.list') }}">
                                    <span class="sidenav-mini-icon"> A </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark "> All Invoices </span>
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>
                @endif
                @if(!in_array('sponsorship.lists', $hiddenRoutes))
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#sponsorship" class="nav-link text-dark "
                       aria-controls="pagesExamples" role="button" aria-expanded="false">
                        <i
                                class="material-symbols-rounded opacity-5 {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">s</i>
                        <span class="nav-link-text ms-1 ps-1 text-dark ">Sponsors</span>
                    </a>
                    <div class="collapse " id="sponsorship">
                        <ul class="nav ">
                            <li class="nav-item ">
                                <a class="nav-link text-dark " href="{{ route('sponsor.create_new') }}">
                                    <span class="sidenav-mini-icon"> M </span>
                                    <span class="sidenav-normal  ms-1  ps-1 text-dark "> Manage Sponsor Items </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-dark " href="{{ route('sponsorship.lists') }}">
                                    <span class="sidenav-mini-icon"> T </span>
                                    <span class="sidenav-normal  ms-1  ps-1 text-dark "> Total Applications </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-dark " href="{{ route('sponsorship.list', ['status' => 'in-progress']) }}">
                                    <span class="sidenav-mini-icon"> I </span>
                                    <span class="sidenav-normal  ms-1  ps-1 text-dark "> Initiated Applications </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-dark " href="{{ route('sponsorship.list', ['status' => 'submitted']) }}">
                                    <span class="sidenav-mini-icon"> S </span>
                                    <span class="sidenav-normal  ms-1  ps-1 text-dark "> Submitted Applications </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-dark " href="{{ route('sponsorship.list', ['status' => 'approved']) }}">

                                    <span class="sidenav-mini-icon"> A </span>
                                    <span class="sidenav-normal  ms-1  ps-1 text-dark "> Approved Applications </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif

                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#exhibitors" class="nav-link text-dark "
                       aria-controls="pagesExamples" role="button" aria-expanded="false">
                        <i
                                class="material-symbols-rounded opacity-5 {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">E</i>
                        <span class="nav-link-text ms-1 ps-1 text-dark ">Exhibitors</span>
                    </a>
                    <div class="collapse " id="exhibitors">
                        <ul class="nav ">
                            {{-- hide this route --}}
                            <li class="nav-item ">
                                <a class="nav-link text-dark " href="{{route('application.lists')}}">
                                    <span class="sidenav-mini-icon"> T </span>
                                    <span class="sidenav-normal  ms-1  ps-1 text-dark "> Total Applications </span>
                                </a>
                            </li>
                            @if(!in_array('application.lists', $hiddenRoutes))
                            <li class="nav-item ">
                                <a class="nav-link text-dark " href="{{ route('application.list', ['status' => 'in-progress']) }}">
                                    <span class="sidenav-mini-icon"> I </span>
                                    <span class="sidenav-normal  ms-1  ps-1 text-dark "> Initiated Applications </span>
                                </a>
                            </li>
                            @endif
                            <li class="nav-item ">
                                <a class="nav-link text-dark " href="{{ route('application.list', ['status' => 'submitted']) }}">
                                    <span class="sidenav-mini-icon"> S </span>
                                    <span class="sidenav-normal  ms-1  ps-1 text-dark "> Submitted Applications </span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a class="nav-link text-dark " href="{{ route('application.list', ['status' => 'approved']) }}">

                                    <span class="sidenav-mini-icon"> A </span>
                                    <span class="sidenav-normal  ms-1  ps-1 text-dark "> Approved Applications </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item ">
                    <a class="nav-link text-dark " href="{{ route('booth.management') }}">
                        <span class="sidenav-mini-icon"> B </span>
                        <span class="sidenav-normal  ms-1  ps-1 text-dark "> Booth Management </span>
                    </a>
                </li>
                @if(!in_array('invoice.list', $hiddenRoutes))
                <li class="nav-item">
                    <a href="{{ route('co_exhibitors') }}" class="nav-link text-dark " aria-controls="pagesExamples"
                       role="button" aria-expanded="false">
                        <i
                                class="material-symbols-rounded opacity-5 {% if page.brand == 'RTL' %}ms-2{% else %} me-2{% endif %}">C</i>
                        <span class="nav-link-text ms-1 ps-1 text-dark ">Co - Exhibitors</span>
                    </a>
                </li>
                @endif
                <!-- Exhibitors Passes -->

                {{--
                 Add Exhibitors Complimentary Allocation with route as passes.allocation
                 --}}
                <li class="nav-item">
                    <a href="{{ route('passes.allocation') }}" class="nav-link text-dark " aria-controls="pagesExamples"
                       role="button" aria-expanded="false">
                        <i class="fa-solid fa-gift"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark ">Exhibitors Complimentary Passes Allocation</span>
                    </a>
                </li>

                @if(!in_array('admin.stall-manning', $hiddenRoutes))

                <li class="nav-item">
                    <a href="{{ route('admin.stall-manning') }}" class="nav-link text-dark">
                        <i class="fa-solid fa-passport"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark">Exhibitors Registration</span>
                    </a>
                </li>
                @endif

                <li class="nav-item">
                    <a href="{{ route('admin.complimentary.delegate') }}" class="nav-link text-dark">
                        <i class="fa-solid fa-ticket"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark">Complimentary Exhibitors Registration</span>
                    </a>
                </li>

                @if(!in_array('visitor.analytics', $hiddenRoutes))
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#visitors" class="nav-link text-dark"
                       aria-controls="visitors" role="button" aria-expanded="false">
                        <i class="fa-solid fa-user-group"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark">Visitors</span>
                    </a>
                    <div class="collapse" id="visitors">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('registration.analytics') }}">
                                    <span class="sidenav-mini-icon"> A </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">Analytics</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('visitor.list') }}">
                                    <span class="sidenav-mini-icon"> L </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">List</span>
                                </a>
                            </li>


                        </ul>
                    </div>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('registration.count') }}">
                        <i class="fa-solid fa-table"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark">Registration Matrix</span>
                    </a>
                </li>

            @endif
            @if($subRole == 'extra_requirements')

                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#extraRequirements" class="nav-link text-dark"
                       aria-controls="extraRequirements" role="button" aria-expanded="false">
                        <i class="fa-solid fa-list"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark">Extra Requirements</span>
                    </a>
                    <div class="collapse" id="extraRequirements">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('extra_requirements.admin.show') }}">
                                    <span class="sidenav-mini-icon"> R </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">Master Requirement List</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('extra_requirements.admin') }}">
                                    <span class="sidenav-mini-icon"> O </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">Orders</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('extra.analytics') }}">
                                    <span class="sidenav-mini-icon"> A </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">Analytics</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('extra_requirements.admin.leadRetrieval') }}">
                                    <span class="sidenav-mini-icon"> L </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">Lead Retrieval List</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            @endif

            <li class="nav-item">
                <a class="nav-link text-dark" href="{{ route('exhibitor.directory.list') }}">
                    <i class="fa-solid fa-address-book"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark">Exhibitor Directory</span>
                </a>
            </li>

            {{-- add feedback analytics --}}
            <li class="nav-item">
                <a href="{{ route('admin.feedback.index') }}" class="nav-link text-dark">
                    <i class="fa-solid fa-chart-line"></i>
                    <span class="nav-link-text ms-1 ps-1 text-dark">Feedback Analytics</span>
                </a>
            </li>
            @if ($subRole === 'visitor')
                <li class="nav-item">
                    <a data-bs-toggle="collapse" href="#visitors" class="nav-link text-dark"
                       aria-controls="visitors" role="button" aria-expanded="false">
                        <i class="fa-solid fa-user-group"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark">Visitors</span>
                    </a>
                    <div class="collapse" id="visitors">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('registration.analytics') }}">
                                    <span class="sidenav-mini-icon"> A </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">Analytics</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('visitor.list') }}">
                                    <span class="sidenav-mini-icon"> L </span>
                                    <span class="sidenav-normal ms-1 ps-1 text-dark">List</span>
                                </a>
                            </li>

                        </ul>
                    </div>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('registration.count') }}">
                        <i class="fa-solid fa-table"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark">Registration Matrix</span>
                    </a>
                </li>

                </li>
                <li class="nav-item">
                    <a href="{{ route('exhibitor.list') }}" class="nav-link text-dark">
                        <i class="fa-solid fa-ticket"></i>
                        <span class="nav-link-text ms-1 ps-1 text-dark">Exhibitor Inaugural Passes</span>
                    </a>
                </li>
            @endif

        </ul>
    </div>
    <div class="mt-auto">
        <ul class="navbar-nav">
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="waves-effect waves-grey nav-link text-dark"
                            style="display: inline-flex; align-items: center; gap: 5px; background: none; border: none; cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                  d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z" />
                            <path fill-rule="evenodd"
                                  d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z" />
                        </svg>
                        <span class="nav-link-text ms-1 ps-1 text-dark test-md "> Sign out </span>
                    </button>
                </form>
            </li>
        </ul>
    </div>
</aside>
