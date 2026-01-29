@php use Illuminate\Support\Facades\Auth; @endphp
@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <!-- Sponsorship Application Section -->
            <div class="col-lg-12">
                <!-- Menu Button for Mobile Screens -->
                <div class="d-lg-none " style="display: none;">
                    <button class="btn btn-dark mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidenav-main"
                        aria-controls="sidenav-main">
                        ☰ Menu
                    </button>
                </div>
                <div class="container">
                    <div class="row mb-4 align-items-center">
                        <div class="col-md-6">
                            <h1 class="h5">Sponsorship Application</h1>
                            <p class="text-muted mt-2">
                                Showcase your brand at SEMICON India 2025
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            @if($application->semi_member == 1)
                                <span class="badge bg-success">Member: Verified</span>
                            @else
                                <span class="badge bg-warning">Member: Waiting for Verification</span>
                            @endif
                            @if ($sponsorshipExists)
                                <a href="{{ route('sponsor.review') }}" class="btn btn-primary ms-3">Applied Sponsorship</a>
                            @endif
                        </div>
                    </div>
                    @if (!isset($sponsorItems) || (is_countable($sponsorItems) && count($sponsorItems) === 0))
                        <div class="card p-4">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle p-3 bg-light">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="text-primary">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <line x1="12" y1="8" x2="12" y2="12"></line>
                                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                                    </svg>
                                </div>

                                <div>
                                    <h3 class="h6">Sponsorships Coming Soon</h3>
                                    <p class="text-muted">
                                        We're finalizing our sponsorship packages. Please check back soon or visit our
                                        website <a href="https://www.semiconindia.org/" target="_blank">SEMICON India</a>
                                        for more information.
                                    </p>
                                </div>
                            </div>
                        </div>

                </div>
            @else
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
                    rel="stylesheet">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
                <style>
                    .sponsorship-image {
                        height: 300px;
                        /* Standard height */
                        width: 100%;
                        /* Full width of container */
                        object-fit: cover;
                        /* Maintains aspect ratio without distortion */
                    }

                    .scrollable-tabs {
                        overflow-x: auto;
                        white-space: nowrap;
                        -webkit-overflow-scrolling: touch;
                    }

                    .scrollable-tabs .nav-link {
                        display: inline-block;
                        min-width: max-content;
                        padding: 0.5rem 1rem;
                    }
                </style>
                <style>
                    .pyro {
                        position: absolute;
                        left: 50%;
                        top: 30%;
                        transform: translate(-50%, -50%);
                        width: 0;
                        height: 0;
                        box-shadow:
                            0 -100px #ff3,
                            86.6px -50px #f0f,
                            86.6px 50px #0ff,
                            0 100px #0f0,
                            -86.6px 50px #f00,
                            -86.6px -50px #00f;
                        animation: fireworks 1.5s ease-out forwards;
                    }

                    @keyframes fireworks {
                        0% {
                            opacity: 1;
                            transform: scale(0);
                        }

                        100% {
                            opacity: 0;
                            transform: scale(2);
                        }
                    }

                    #tier-banner {
                        font-weight: bold;
                        color: #000 !important;
                        background-color: #ffeeba !important;
                        border: 1px solid #f0ad4e;
                    }

                    #discount-banner {
                        font-weight: bold;
                        color: #155724;
                        background-color: #d4edda;
                        border: 1px solid #c3e6cb;
                        padding: 10px;
                        border-radius: 5px;
                        margin-bottom: 10px;
                    }
                </style>

                {{-- <div class="scrollable-tabs mt-3 border-bottom">
                    <ul class="nav nav-tabs flex-nowrap" id="sponsorshipTabs" role="tablist">
                        @foreach ($categories as $index => $category)
                            @php
                                // Assign icons manually based on category name
                                $iconMap = [
                                    'Onsite Promotional Opportunities' => 'bi-megaphone',
                                    'Onsite Branding Opportunities' => 'bi-palette',
                                    'Conference Sponsorship' => 'bi-easel2',
                                    'Welcome Dinner Sponsors' => 'bi-cup-straw',
                                    'Title Sponsors' => 'bi-award',
                                ];
                                $icon = $iconMap[$category->name] ?? 'bi-star';

                                //dd($sponsorApplied);
                                //dd($sponsorApplied);
                            @endphp

                            @if ($category->name !== 'Tcitle Sponsors')
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link @if ($index == 0) active @endif"
                                        id="tab-{{ $category->id }}" data-bs-toggle="tab"
                                        data-bs-target="#tab-content-{{ $category->id }}" type="button" role="tab"
                                        aria-controls="tab-content-{{ $category->id }}"
                                        aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                        <i class="bi {{ $icon }} me-1"></i> {{ $category->name }}
                                    </button>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div> --}}
                @php
                
                   /* $titleSponsors = $sponsorCategories->firstWhere('name', 'Title Sponsors')->items->toArray();
                   //for each title sponsor, get the price and name
                    $titleSponsorTiers = collect($titleSponsors)->map(fn($item) => ['price' => $item['price'], 'name' => $item['name']])->sortBy('price')->values();
                    $titleSponsorTiers = $titleSponsorTiers->toArray();
                    $titleSponsorTiers = array_values($titleSponsorTiers);
                    $titleSponsorTiers = array_map(function ($item) {
                        return [
                            'price' => $item['price'],
                            'name' => $item['name'],
                        ];
                    }, $titleSponsorTiers);

                    */

                @endphp
<div class="container mt-4">
    <!-- Render Tabs -->
    <ul class="nav nav-tabs mb-3" id="sponsorTabs" role="tablist">
        @foreach ($sponsorCategories->filter(function ($category) {
            return $category->name === 'Title Sponsors';
        }) as $index => $category)
            <li class="nav-item" role="presentation">
                <button class="nav-link @if ($category->name === 'Title Sponsors') active @endif"
                        id="tab-{{ $category->id }}"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-content-{{ $category->id }}"
                        type="button" role="tab"
                        aria-controls="tab-content-{{ $category->id }}"
                        aria-selected="{{ $category->name === 'Title Sponsors' ? 'true' : 'false' }}">
                    @if ($category->name === 'Title Sponsors')
                        <i class="bi bi-award-fill"></i> 
                    @endif
                    {{ $category->name }}
                </button>
            </li>
        @endforeach
    </ul>

    <ul class="nav nav-tabs mb-3" id="sponsorTabs" role="tablist">
        @foreach ($sponsorCategories->filter(function ($category) {
            return $category->name !== 'Title Sponsors';
        }) as $index => $category)
            <li class="nav-item" role="presentation">
                <button class="nav-link @if ($category->name === 'Title Sponsors') active @endif"
                        id="tab-{{ $category->id }}"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-content-{{ $category->id }}"
                        type="button" role="tab"
                        aria-controls="tab-content-{{ $category->id }}"
                        aria-selected="{{ $category->name === 'Title Sponsors' ? 'true' : 'false' }}">
                    @if ($category->name === 'Title Sponsors')
                        <i class="bi bi-award-fill"></i> 
                    @endif
                    {{ $category->name }}
                </button>
            </li>
        @endforeach
    </ul>

    <!-- Render Tab Contents -->
    <div class="tab-content mt-4" id="sponsorTabContent">
        @foreach ($sponsorCategories->sortByDesc(function ($category) {
            return $category->name === 'Title Sponsors' ? 1 : 0;
        }) as $category)
            <div class="tab-pane fade @if ($category->name === 'Title Sponsors') show active @endif"
                 id="tab-content-{{ $category->id }}" role="tabpanel">

                @if ($category->name === 'Title Sponsors')
                    <div class="alert alert-primary">
                        To become a title sponsor, choose the items worth of Title Sponsor Price from the below-mentioned category. Title Sponsorship is automatically calculated based on your cart total.
                    </div>
                @endif

                <div class="row">
                    @foreach ($category->items as $item)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                @if ($item->image_url)
                                    <img src="{{ asset($item->image_url) }}"
                                         class="card-img-top sponsorship-image" alt="{{ $item->name }}">
                                @endif

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $item->name }}</h5>
                                    <p class="text-muted"> {!! nl2br(e($item->deliverables)) !!}</p>

                                    <ul class="small mb-2">
                                        @if ($item->quantity_desc)
                                            <li>{!! nl2br(e($item->quantity_desc)) !!}</li>
                                        @endif
                                        @if ($item->deadline)
                                            <li><strong>Material Deadline:</strong>
                                                {{ \Carbon\Carbon::parse($item->deadline)->format('M d, Y') }}
                                            </li>
                                        @endif
                                        <li><strong>Available:</strong> {{ $item->no_of_items }}</li>
                                    </ul>

                                    <div class="mt-auto">
                                        <p class="mb-1">
                                            <strong>Member:</strong> ₹{{ number_format($item->mem_price) }}<br>
                                            <strong>Non-Member:</strong> ₹{{ number_format($item->price) }}<br>
                                            <small class="text-muted">+ 18% tax</small>
                                        </p>

                                        @php
                                            $appliedCount = $sponsorApplied[$item->id] ?? 0;
                                        @endphp

                                        @if ($appliedCount >= $item->no_of_items)
                                            <button class="btn btn-danger w-100 mt-2 text-white" disabled>
                                                Sold Out
                                            </button>
                                        @else
                                            <button class="btn btn-primary w-100 mt-2 add-to-cart-btn text-white"
                                                    data-item-id="{{ $item->id }}"
                                                    data-item-name="{{ $item->name }}"
                                                    data-item-price="{{ $item->price }}"
                                                    data-item-max="{{ $item->no_of_items }}">
                                                + Add
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>




                <!-- Cart Button (toggle) -->
                <button class="btn btn-outline-primary position-fixed top-50 end-0 translate-middle-y me-2 z-3"
                    type="button" data-bs-toggle="offcanvas" data-bs-target="#cartSidebar" aria-controls="cartSidebar">
                    <i class="bi bi-cart-fill"></i> <span class="badge bg-danger" id="cart-count">0</span>
                    <br>
                    Your Items
                </button>


                @endif
            </div>

            <!-- Offcanvas Cart Sidebar -->
            <div class="offcanvas offcanvas-end" tabindex="-1" id="cartSidebar" aria-labelledby="cartSidebarLabel">
                <div class="offcanvas-header">
                    <h5 id="cartSidebarLabel">Your Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column">
                    <ul class="list-group mb-3" id="cart-items">
                        <!-- Dynamic content -->
                    </ul>

                    <div class="alert alert-info d-none" id="tier-banner">
                        You're close to <strong>Platinum Tier</strong><br>
                        Add ₹<span id="tier-gap">0</span> more!
                    </div>
                    @php
                    $sqm = $application->allocated_sqm;
                    $discountMap = [
                        72 => 10,
                        125 => 15,
                        175 => 20,
                        200 => 25,
                        225 => 30,
                    ];

                    $eligibleDiscount = 0;
                    foreach ($discountMap as $threshold => $percent) {
                        if ($sqm >= $threshold) {
                            $eligibleDiscount = $percent;
                        }
                    }
                    $exhibition_price = $application->invoice->price;
                    @endphp
                    <div class="mt-auto" >
                        <form id="apply-now-form" method="POST" action="{{ route('sponsor.store') }}">
                            @csrf
                        <p class="fw-bold">Total: ₹<span id="cart-total">0.00</span></p>

                       

                        @if (isset($application) && $application->allocated_sqm > 72)
                        
                        <p class="text-success">🎁 You're eligible for <strong>₹{{ round($exhibition_price * ($eligibleDiscount / 100)) }} </strong> credit towards Title Sponsorship added from the Exhibition Booth </p>
                        {{-- <p id="discount-banner" class="alert alert-success">
                            Credit note:   
                        </p> --}}

                        @endif

                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="optOutTitleSponsorship" name="opt_out_title_sponsorship" value="1">
                            <label class="form-check-label" for="optOutTitleSponsorship">Opt out from eligible Title Sponsorship</label>
                        </div>

                        <!-- Apply Now Button -->
                        
                            <input type="hidden" name="items" id="cart-items-json">
                            <button class="btn btn-success w-100 mt-2" id="applyNowBtn" disabled>
                                Apply Now
                            </button>
                        </form>
                    </div>
                </div>
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
                    <a href="#" class="btn btn-outline-dark mb-2 bold">Onboarding</a>
                    <a href="#" class="btn btn-outline-dark mb-2 bold">Sponsorship</a>

                    <!-- Push Email and Logout to Bottom -->
                    <div class="mt-auto">
                        <hr>
                        <p class="text-muted"><i class="material-symbols-rounded opacity-5">email</i>
                            {{ Auth::user()->email }}</p>
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
    <script>
        const titleSponsorTiers = @json(optional($sponsorCategories->firstWhere('name', 'Title Sponsors'))->items->map(fn($item) => ['price' => $item->price, 'name' => $item->name])->sortBy('price')->values());
        const isDiscountEligible = @json($discountEligible);
        console.log(isDiscountEligible);
    </script>

    <script>
        let cart = [];

        document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const itemId = btn.dataset.itemId;
                const name = btn.dataset.itemName;
                const price = parseFloat(btn.dataset.itemPrice);
                const maxQty = parseInt(btn.dataset.itemMax);

                if (isNaN(price)) {
                    alert("Price not available.");
                    return;
                }

                const exists = cart.find(item => item.id === itemId);
                if (!exists) {
                    cart.push({
                        id: itemId,
                        name,
                        price,
                        quantity: 1,
                        maxQty
                    });
                    btn.classList.add('disabled');
                    btn.innerText = '✔ Added';
                    updateCartDisplay();
                }
            });
        });

        function updateCartDisplay() {
            const list = document.getElementById('cart-items');
            const totalEl = document.getElementById('cart-total');
            const tierBanner = document.getElementById('tier-banner');
            const applyBtn = document.getElementById('applyNowBtn');
            const countEl = document.getElementById('cart-count');

            const discountBannerId = 'discount-banner';
            const existingDiscount = document.getElementById(discountBannerId);
            if (existingDiscount) existingDiscount.remove();

            if (typeof isDiscountEligible !== 'undefined' && isDiscountEligible) {
                const discountBanner = document.createElement('div');
                discountBanner.id = discountBannerId;
                discountBanner.innerHTML = `🎁 You're eligible for a 10% discount as you have 72+ sqm allocated!`;
                const cartSidebarBody = document.querySelector('#cartSidebar .offcanvas-body');
                cartSidebarBody.insertBefore(discountBanner, cartSidebarBody.firstChild);
            }

            list.innerHTML = '';
            let total = 0;

            cart.forEach((item, i) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center flex-wrap';
                li.innerHTML = `
              <div class="w-75">
                <strong>${item.name}</strong><br>
                ₹${item.price.toLocaleString()} x 
                <button class="btn btn-sm btn-outline-secondary px-2" onclick="changeQty(${i}, -1)">−</button>
                <span class="mx-1">${item.quantity}</span>
                <button class="btn btn-sm btn-outline-secondary px-2" onclick="changeQty(${i}, 1)">+</button>
                <div class="text-muted small">Max: ${item.maxQty}</div>
              </div>
              <div class="text-end">
                ₹${itemTotal.toLocaleString()}<br>
                <button class="btn btn-sm btn-danger mt-2" onclick="removeFromCart(${i})">Remove</button>
              </div>
            `;
                list.appendChild(li);
                document.getElementById('cart-items-json').value = JSON.stringify(
                    cart.map(item => ({
                        item_id: item.id,
                        quantity: item.quantity
                    }))
                );
            });

            let finalTotal = total;
            if (typeof isDiscountEligible !== 'undefined' && isDiscountEligible) {
                finalTotal = total * 0.9;
            }

            totalEl.innerText = finalTotal.toLocaleString(undefined, {
                minimumFractionDigits: 2
            });
            countEl.innerText = cart.length;
            applyBtn.disabled = cart.length === 0;

            if (typeof titleSponsorTiers !== 'undefined' && Array.isArray(titleSponsorTiers)) {
                const fireworkContainerId = 'fireworks-container';

                const oldFireworks = document.getElementById(fireworkContainerId);
                if (oldFireworks) oldFireworks.remove();

                let matchedTier = null;
                let nextTier = null;

                for (let i = 0; i < titleSponsorTiers.length; i++) {
                    const tier = titleSponsorTiers[i];
                    if (finalTotal >= tier.price) {
                        matchedTier = tier;
                    } else {
                        nextTier = tier;
                        break;
                    }
                }

                if (matchedTier) {
                    tierBanner.classList.remove('d-none');
                    tierBanner.innerHTML = `
                    🎉 <strong>Congratulations!</strong><br>
                    You're eligible for the <strong>${matchedTier.name} Title Sponsorship </strong> worth ₹${matchedTier.price.toLocaleString()}!
                `;

                    const fw = document.createElement('div');
                    fw.id = fireworkContainerId;
                    fw.innerHTML = `
                    <div style="position:relative;width:100%;height:100%;pointer-events:none;z-index:1050;">
                        <div class="pyro"></div>
                    </div>
                `;
                    document.getElementById('cartSidebar').appendChild(fw);

                } else if (nextTier) {
                    tierBanner.classList.remove('d-none');
                    tierBanner.innerHTML = `
                    Add ₹<strong>${(nextTier.price - finalTotal).toLocaleString()}</strong> more to become a <strong>${nextTier.name}</strong> Title Sponsorship (₹${nextTier.price.toLocaleString()}).
                `;
                } else {
                    tierBanner.classList.add('d-none');
                }
            }
        }

        function changeQty(index, change) {
            cart[index].quantity += change;
            if (cart[index].quantity < 1) cart[index].quantity = 1;
            if (cart[index].quantity > cart[index].maxQty) {
                cart[index].quantity = cart[index].maxQty;
                alert("Maximum quantity reached.");
            }
            updateCartDisplay();
        }

        function removeFromCart(index) {
            const removed = cart.splice(index, 1)[0];
            updateCartDisplay();

            document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                if (btn.dataset.itemId === removed.id) {
                    btn.classList.remove('disabled');
                    btn.innerText = '+ Add';
                }
            });
        }
    </script>




@endsection
