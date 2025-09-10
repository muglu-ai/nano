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

<style>
    .sponsorship-item {
        transition: transform 0.3s;
        height: 100%;
    }
    .sponsorship-item:hover {
        transform: translateY(-5px);
    }
    .sponsorship-image {
    height: 300px;  /* Standard height */
    width: 100%;    /* Full width of container */
    object-fit: cover;  /* Maintains aspect ratio without distortion */
}
    .price-tag {
        font-weight: 600;
        font-size: 1.2rem;
    }
    .tax-note {
        font-size: 0.8rem;
        color: #6c757d;
    }
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #212529;
        font-weight: 600;
    }
    .detail-label {
        font-weight: 600;
        color: #495057;
    }
    .detail-value {
        color: #212529;
    }
    .buy-now-btn {
        background-color: #0d6efd;
        border: none;
        padding: 8px 20px;
        font-weight: 500;
    }
    .buy-now-btn:hover {
        background-color: #0b5ed7;
    }
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
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    .custom-height {
                        height: 330px !important; /* Set a fixed height for the card body */
                        overflow: hidden !important; /* Hide overflow content */
                    }
                </style>
                <div class="accordion" id="sponsorshipAccordion">
                    @foreach($categories as $index => $category)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading{{ $index }}">
                                <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}" style="background-color: #f8f9fa; color: #212529; font-size: 15px; font-weight: bold;">
                                    {{ $category->name }}
                                </button>
                            </h2>
                            <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#sponsorshipAccordion">
                                <div class="accordion-body">
                                    <div class="row g-4">
                                        @if(!$category->items || $category->items->isEmpty())
                                            <div class="col-12">
                                                <div class="card p-4">
                                                    <h3 class="h5">More Sponsorships Coming Soon</h3>
                                                    <p class="text-muted">
                                                        We're finalizing our sponsorship packages. Please check back soon or visit our website <a href="https://www.semiconindia.org/" target="_blank">SEMICON India</a> for more information.
                                                    </p>
                                                </div>
                                            </div>
                                        @endif
                                       
                                        @foreach($category->items as $item)



                                            @php
                                                $userHasSponsorship = Auth::check() && Auth::user()->sponsorships
                                                    ? Auth::user()->sponsorships->contains('sponsorship_item_id', $item->id)
                                                    : false;
                                                $count = \App\Models\Sponsorship::where('sponsorship_item_id', $item->id)->count();
                                                $status = $count >= $item->no_of_items ? 'Out of Stock' : 'In Stock';
                                        
                                                
                                        @endphp



                                            <div class="col-md-6 col-lg-4">
                                                <form action="{{ route('sponsor.store') }}" method="POST">
                                                    @csrf
                                                    <div class="card sponsorship-item shadow-sm">
                                                        
                                                        @if($item->image_url)
                                                            <a href="{{ $item->image_url }}" target="_blank" data-bs-toggle="modal" data-bs-target="#imageModal{{ $item->id }}">
                                                                <img src="{{ $item->image_url }}" class="card-img-top sponsorship-image" alt="{{ $item->name }}">
                                                            </a>
                                                        @endif

                                                        <!-- Modal -->

                                                        <div class="card-body">
                                                            <div class="custom-height">
                                                            <h5 class="card-title fw-bold">{{ $item->name }}</h5>
                                                            <p class="card-text">{{ $item->description }} </p>
                                                            <div class=" deliverables  mb-2">
                                                                {!! nl2br(e($item->deliverables)) !!}
                                                            </div>
                                                        </div>
                                                            <div class="details mt-3">
                                                               
                                                                <div class="row mb-2">
                                                                    <div class="col-sm-6 detail-label">Number of Sponsors:</div>
                                                                    <div class="col-sm-6 detail-value">{{ $item->no_of_items }}</div>
                                                                    @if(!is_numeric($item->quantity_desc))
                                                                        <small>{{ $item->quantity_desc }}</small>
                                                                    @endif
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-sm-6 detail-label">Member Price:</div>
                                                                    <div class="col-sm-6 detail-value price-tag">INR {{ number_format($item->mem_price) }}</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-sm-6 detail-label">Non-Member Price:</div>
                                                                    <div class="col-sm-6 detail-value price-tag">INR {{ number_format($item->price) }}</div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-12 tax-note">* 18% tax not included</div>
                                                                </div>
                                                                <div class="row mb-2">
                                                                    <div class="col-12">
                                                                        <span class="badge {{ $status == 'Out of Stock' ? 'bg-danger' : 'bg-success' }}">
                                                                            {{ $status }}
                                                                        </span>
                                                                    </div>
                                                                </div>

                                                                <div class="row mt-3">
                                                                    <div class="col-lg-6">
                                                                        <label>Quantity</label>
                                                                        <select class="form-control" name="quantity">
                                                                            @for ($i = 1; $i <= $item->no_of_items; $i++)
                                                                                <option value="{{ $i }}">{{ $i }}</option>
                                                                            @endfor
                                                                        </select>
                                                                    </div>
                                                                </div>


                                                                <input type="hidden" name="item_id" value="{{ $item->id }}">
                                                            </div>

                                                            <div class="row mt-4">
                                                                <div class="col-12">
                                                                    @if($userHasSponsorship)
                                                                        <button class="btn btn-success w-100" type="button" onclick="window.location.href='{{ route('sponsor.review') }}'">
                                                                            Check Status
                                                                        </button>
                                                                    @elseif($status == 'Out of Stock')
                                                                        <button class="btn btn-secondary w-100 " type="button" disabled>Out of Stock</button>
                                                                    @else
                                                                        <button class="btn btn-primary w-100" type="submit">Apply</button>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="imageModal{{ $item->id }}" tabindex="-1" aria-labelledby="imageModalLabel{{ $item->id }}" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="imageModalLabel{{ $item->id }}">{{ $item->name }}</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body text-center">
                                                                    <img src="{{ $item->image_url ?? '/placeholder.svg?height=180&width=320' }}" class="img-fluid" alt="{{ $item->name }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                    <a href="#" class="btn btn-outline-dark mb-2 bold" >Onboarding</a>
                    <a href="#" class="btn btn-outline-dark mb-2 bold">Sponsorship</a>

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
