@php use Illuminate\Support\Facades\Auth; @endphp


@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')



    <style>
        :root {
            --primary: #e91e63;
            --primary-light: #fce7ef;
            --text-primary: #111827;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --background: #ffffff;
            --card-background: #ffffff;
        }

        body {
            color: var(--text-primary);
            background-color: var(--background);
        }

        .card {
            background-color: var(--card-background);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            /*min-height: 800px;*/
            /*height: auto;*/
        }
        .deliv li{
            list-style-type: none;
            list-style-position: inside;
            padding-left: -40px !important;
            line-height: 200%;
        }
       .deliv {
            display: block;
            list-style-type: disc;
            margin-block-start: 1em;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
            padding-inline-start: 0 !important;
            unicode-bidi: isolate;
        }


    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Sponsorship Application Section -->
            <div class="col-lg-12" >
                <!-- Menu Button for Mobile Screens -->
                <div class="d-lg-none " style="display: none;">
                    <button class="btn btn-dark mb-3" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#sidenav-main" aria-controls="sidenav-main">
                        ☰ Menu
                    </button>
                </div>
                <div class="container">
                    <div class="mb-4">
                        <h1 class="h5">Sponsorship Application</h1>
                        <p class="text-muted mt-2">
                            Showcase your brand at SEMICON India 2025
                        </p>
                    </div>
                  @if(!isset($sponsorItems) || (is_countable($sponsorItems) && count($sponsorItems) === 0))
                    <div class="card p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle p-3 bg-light">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="12" y1="8" x2="12" y2="12"></line>
                                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                </svg>
                            </div>

                            <div>
                                <h3 class="h6">Sponsorships Coming Soon</h3>
                                <p class="text-muted">
                                    We're finalizing our sponsorship packages. Please check back soon or visit our website <a href="https://www.semiconindia.org/" target="_blank">SEMICON India</a> for more information.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                @else
                <div class="row">
                    <div class="row">
                        @foreach($sponsorItems as $item)
                            @php
                                $userHasSponsorship = Auth::check() && Auth::user()->sponsorships
                                    ? Auth::user()->sponsorships->contains('sponsorship_item_id', $item->id)
                                    : false;
                                //check the count of sponsorships item id from sponsorship table
                            $count = \App\Models\Sponsorship::where('sponsorship_item_id', $item->id)->count();
                            //if count is greater than or equal to no of items then store In Stock or out of stock
                            $status = $count >= $item->no_of_items ? 'Out of Stock' : 'In Stock';



                            @endphp

                            <div class="col-lg-4 mb-4">
                                <form action="{{ route('sponsor.store') }}" method="POST" class="mb-4">
                                    @csrf
                                    <div class="card">
                                        <div class="card-header pb-0">
                                            <h3 class="mt-lg-0 mt-4">{{ $item->name }}</h3>
                                        </div>
                                        <div class="card-body">
                                            <h6 class="mb-0">Price</h6>
                                            <h5>₹{{ number_format($item->price) }}</h5>
                                            <span
                                                class="badge {{$status == 'Out of Stock' ? 'badge-danger' : 'badge-success'}}">{{$status}}</span>

                                            <label class="mt-4">Deliverables</label>
                                            <ul class="deliv flex">
                                                @foreach ((is_array(json_decode($item->deliverables, true)) ? json_decode($item->deliverables, true) : []) as $deliverable)
                                                    <li class="flex items-center gap-2"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                                                            <circle cx="12" cy="8" r="7"></circle>
                                                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                                                        </svg>   {{ $deliverable }}</li>
                                                @endforeach
                                            </ul>

                                            <div class="row mt-4">
                                                <div class="col-lg-6">
                                                    <label>Quantity</label>
                                                    <select class="form-control" name="quantity"
                                                            id="choices-quantity-{{ $item->id }}">
                                                        @for ($i = 1; $i <= $item->no_of_items; $i++)
                                                            <option value="{{ $i }}">{{ $i }}</option>
                                                        @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" name="item_id" value="{{ $item->id }}">


                                            <div class="row mt-4">
                                                <div class="col-lg-12">
                                                    @if($userHasSponsorship)
                                                        <button class="btn btn-success mb-0 w-100" style="bottom: 0;" type="button"
                                                                onclick="window.location.href='{{ route('sponsor.review') }}'">
                                                            Check Status
                                                        </button>
                                                    @elseif($status == 'Out of Stock')
                                                        <button class="btn bg-gradient-dark mb-0 w-100" type="button" style="bottom: 0;"
                                                                disabled>Out of Stock
                                                        </button>
                                                    @else
                                                        <button class="btn bg-gradient-dark mb-0 w-100" style="bottom: 0;" type="submit">
                                                            Apply
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar (Hidden on Large Screens, Visible as Offcanvas on Mobile) -->
            <aside class="offcanvas offcanvas-start d-lg-none bg-white" tabindex="-1" id="sidenav-main"
                   aria-labelledby="sidenavLabel" data-bs-backdrop="true" style="width: 280px;">

                <div class="offcanvas-header">
                    <h5 id="sidenavLabel">Menu</h5>
                    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                </div>

                <div class="offcanvas-body d-flex flex-column" id="sidenav-scrollbar">
                    <a href="#" class="btn btn-outline-dark mb-2">Onboarding</a>
                    <a href="#" class="btn btn-outline-dark mb-2">Sponsorship</a>

                    <!-- Push Email and Logout to Bottom -->
                    <div class="mt-auto">
                        <hr>
                        <p class="text-muted"><i
                                class="material-symbols-rounded opacity-5">email</i> {{ Auth::user()->email }}</p>
                        <a href="{{ route('logout') }}" class="btn btn-outline-danger w-100"
                           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Keep existing scrollbar script -->
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>

@endsection
