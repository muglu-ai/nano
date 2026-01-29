@extends('layouts.users')
@section('title', 'Extra Requirements')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="d-flex justify-content-between w-100">
                                <h5 class="mb-4">Extra Requirements Page</h5>
                            </div>

                            @if(config('constants.EXTRA_REQUIREMENTS_ACTIVE') == false)

                                <div class="alert alert-warning text-center my-4">
                                    Coming Soon
                                </div>
                            @else


                            {{-- <div class="alert alert-info mb-4" role="alert">
                                    <ul class="mb-0" style="color: #fff;">

                                    <li><strong>Standard Rates:</strong> Applicable for orders placed on or before August
                                        10, 2025.</li>
                                    <li><strong>30% Surcharge:</strong> Applies to orders placed between August 11 and
                                        August 15, 2025.</li>
                                    <li><strong>50% Surcharge:</strong> Applies to orders placed after August 15, 2025, and
                                        is strictly subject to availability.</li>
                                    <li><strong>75% Surcharge:</strong> Applies to onsite orders, which will only be
                                        accepted if resources are available.</li>
                                </ul>
                            </div> --}}
                            {{-- only online payment online through credit card, netbanking or UPI --}}
                            <div class="alert alert-info mb-4" role="alert">
                                <ul class="mb-0" style="color: #fff;">
                                    <li><strong>Payment Method:</strong> All payments must be made online through credit
                                        card, net banking, or UPI.</li>
                                    <!-- <li><strong>Payment Deadline:</strong> Full payment is required at the time of order
                                        placement to ensure processing and confirmation.</li> -->
                                    <!-- <li><strong>Order Confirmation:</strong> Orders will only be confirmed upon receipt of
                                        full payment.</li> -->
                                </ul>
                            </div>

                        </div>

                        <div class="row">
                            <!-- Grand Total Section -->
                            <div class="d-flex justify-content-end mt-4">
                                <h5>Total Price: <span id="grand_total">INR 0.00</span></h5>
                            </div>

                            <!-- Add to Cart Button -->
                            <div class="d-flex justify-content-end mt-2">
                                <button class="btn btn-success" id="add-to-cart">Add to Cart</button>
                            </div>

                            <div class="table table-responsive">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-7">
                                                Item Code</th>
                                            <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-7">
                                                Product</th>
                                            <th
                                                class="text-uppercase text-secondary text-sm font-weight-bolder opacity-7 ps-2">
                                                Price</th>
                                            <th
                                                class="text-uppercase text-center text-secondary text-sm font-weight-bolder opacity-7 ps-2">
                                                Quantity</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-sm font-weight-bolder opacity-7">
                                                Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @foreach ($items as $item)
                                            @php
                                                //skip these for two items MMA-27 MMA-28
                                                if ($item->status === 'out_of_stock' || $item->item_code === 'MMA-28') {
                                                    continue;
                                                }
                                                $is_special = $item->item_code === 'MMA-39';
                                                $min_qty = $is_special ? 18 : 0;
                                                $default_qty = $is_special ? 0 : 0;
                                                $max_qty = $is_special ? 999 : 100;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="px-2 py-1">
                                                        <p class="text-sm text-secondary mb-0">{{ $item->item_code }}</p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex ">
                                                        <div>
                                                            @if ($item->image)
                                                                <img src="{{ $item->image }}" class="avatar avatar-md me-3"
                                                                    alt="table image">
                                                            @else
                                                                <img src="https://placehold.jp/3d4070/ffffff/150x150.png?text={{ $item->item_name }}"
                                                                    class="avatar avatar-md me-3" alt="no image">
                                                            @endif
                                                        </div>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $item->item_name }}</h6>
                                                            @if ($item->item_code === 'MMA-40')
                                                                <a href="https://portal.semiconindia.org/assets/docs/Lead-Generation-Process.pdf"
                                                                    target="_blank" style="color: blue">Click Here for more
                                                                    information</a>
                                                            @endif
                                                            <small class="text-secondary mb-0"
                                                                style="white-space: normal; word-break: break-word; display: block;">
                                                                {{ $item->size_or_description }}
                                                            </small>
                                                            @if ($is_special)
                                                                <small class="text-warning">* Minimum 18 sqm</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-sm text-secondary mb-0">
                                                        INR {{ $item->price_for_expo }}
                                                        @if ($is_special)
                                                            <span class="text-muted">/sqm</span>
                                                        @endif
                                                        @if ($item->item_code == 'MMA-41' || $item->item_code == 'MMA-42')
                                                            <span class="text-muted">/per day</span>
                                                        @endif
                                                        @if($item->item_code == 'MMA-40' || $item->item_code == 'MMA-00')
                                                            <span class="text-muted">/per user</span>
                                                        @endif
                                                    </p>
                                                </td>
                                                <td>
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <button class="btn btn-outline-secondary btn-sm decrease-btn"
                                                            data-id="{{ $item->id }}"
                                                            data-price="{{ $item->price_for_expo }}"
                                                            data-special="{{ $is_special ? '1' : '0' }}">
                                                            -
                                                        </button>
                                                        <input type="number" id="quantity_{{ $item->id }}"
                                                            class="form-control text-center mx-2 quantity-input"
                                                            value="{{ $default_qty }}" min="{{ $min_qty }}"
                                                            max="{{ $max_qty }}" style="width: 60px;" readonly>
                                                        <button class="btn btn-outline-secondary btn-sm increase-btn"
                                                            data-id="{{ $item->id }}"
                                                            data-price="{{ $item->price_for_expo }}"
                                                            data-special="{{ $is_special ? '1' : '0' }}">
                                                            +
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <p class="text-sm text-secondary mb-0" id="total_{{ $item->id }}">
                                                        INR 0.00</p>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>

                            <!-- Grand Total Section -->
                            <div class="d-flex justify-content-end mt-4">
                                <h5>Total Price: <span id="grand_total2">INR 0.00</span></h5>
                            </div>

                            <!-- Add to Cart Button -->
                            <div class="d-flex justify-content-end mt-2">
                                <button class="btn btn-success" id="add-to-cart2">Add to Cart</button>
                            </div>

                        </div>

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and SweetAlert JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if(config('constants.EXTRA_REQUIREMENTS_ACTIVE') == true)
    <script>
        $(document).ready(function() {
            function getSelectedItems() {
                let selectedItems = [];

                $('.quantity-input').each(function() {
                    let itemId = $(this).attr('id').split('_')[1];
                    let quantity = parseInt($(this).val());

                    if (quantity > 0) {
                        selectedItems.push({
                            item_id: itemId,
                            quantity: quantity
                        });
                    }
                });

                return selectedItems;
            }

            $('#add-to-cart, #add-to-cart2').click(function() {
                let itemsData = getSelectedItems();

                if (itemsData.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No Items Selected',
                        text: 'Please add at least one item to the cart.',
                    });
                    return;
                }

                $.ajax({
                    url: '/extra_requirements',
                    method: 'POST',
                    data: JSON.stringify({
                        items: itemsData,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }),
                    contentType: "application/json",
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Items successfully added to cart!',
                        }).then(() => {
                            window.location.href = '/exhibitor/orders';
                        });
                    },
                    error: function(xhr) {
                        console.error("Error:", xhr.responseText);

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops!',
                            text: 'An error occurred while adding items to the cart.',
                        });
                    }
                });
            });

            // Increase quantity
            $('.increase-btn').click(function() {
                let itemId = $(this).data('id');
                let price = $(this).data('price');
                let isSpecial = $(this).data('special') == 1;
                let quantityInput = $(`#quantity_${itemId}`);
                let currentValue = parseInt(quantityInput.val());

                if (isSpecial) {
                    let newValue = currentValue < 18 ? 18 : currentValue + 3;
                    quantityInput.val(newValue);
                } else {
                    if (currentValue < 100) {
                        quantityInput.val(currentValue + 1);
                    }
                }

                updateTotal(itemId, price);
            });

            // Decrease quantity
            $('.decrease-btn').click(function() {
                let itemId = $(this).data('id');
                let price = $(this).data('price');
                let isSpecial = $(this).data('special') == 1;
                let quantityInput = $(`#quantity_${itemId}`);
                let currentValue = parseInt(quantityInput.val());

                if (isSpecial) {
                    if (currentValue > 18) {
                        quantityInput.val(currentValue - 3);
                    } else {
                        quantityInput.val(0);
                    }
                } else {
                    if (currentValue > 0) {
                        quantityInput.val(currentValue - 1);
                    }
                }

                updateTotal(itemId, price);
            });

            function updateTotal(itemId, price) {
                let quantityInput = $(`#quantity_${itemId}`);
                let total = quantityInput.val() * price;
                $(`#total_${itemId}`).html(`INR ${total.toFixed(2)}`);
                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let grandTotal = 0;

                $('.quantity-input').each(function() {
                    let itemId = $(this).attr('id').split('_')[1];
                    let price = parseFloat($(`.increase-btn[data-id='${itemId}']`).data('price'));
                    let quantity = parseInt($(this).val());
                    grandTotal += quantity * price;
                });

                $('#grand_total, #grand_total2').html(`INR ${grandTotal.toFixed(2)}`);
            }

            // Initial total calculation
            $('.quantity-input').each(function() {
                let itemId = $(this).attr('id').split('_')[1];
                let price = parseFloat($(`.increase-btn[data-id='${itemId}']`).data('price'));
                updateTotal(itemId, price);
            });
        });
    </script>
    @endif

@endsection
