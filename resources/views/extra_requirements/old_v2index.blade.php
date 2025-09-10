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
                                        <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-7">Item Code</th>
                                        <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-7">Product</th>
                                        <th class="text-uppercase text-secondary text-sm font-weight-bolder opacity-7 ps-2">Price</th>
                                        <th class="text-uppercase text-center text-secondary text-sm font-weight-bolder opacity-7 ps-2">Quantity</th>
                                        <th class="text-center text-uppercase text-secondary text-sm font-weight-bolder opacity-7">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($items as $item)
                                        @if($item->status == 'available')
                                        <tr>
                                            <td>
                                                <div class="px-2 py-1">
                                                <p class="text-sm text-secondary mb-0">{{$item->item_code}}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex ">
                                                    <div>
                                                        @if($item->image)
                                                            <img src="{{$item->image}}" class="avatar avatar-md me-3" alt="table image">
                                                        @else
                                                            <img src="https://placehold.jp/3d4070/ffffff/150x150.png?text={{ $item->item_name }}" class="avatar avatar-md me-3" alt="no image">
                                                        @endif
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{$item->item_name}}</h6>
                                                        <small class="text-secondary mb-0"><span style="word-break: break-word;">{{$item->size_or_description}}</span></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm text-secondary mb-0">INR {{$item->price_for_expo}}</p>
                                            </td>
                                            <td>
                                                <div class="d-flex justify-content-center align-items-center">
                                                    <button class="btn btn-outline-secondary btn-sm decrease-btn" data-id="{{ $item->id }}" data-price="{{ $item->price_for_expo }}">-</button>

                                                    <input type="number" id="quantity_{{ $item->id }}" class="form-control text-center mx-2 quantity-input" value="0" min="0" max="10" style="width: 60px;" readonly>
                                                    <button class="btn btn-outline-secondary btn-sm increase-btn" data-id="{{ $item->id }}" data-price="{{ $item->price_for_expo }}">+</button>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-sm text-secondary mb-0" id="total_{{ $item->id }}">INR 0.00</p>
                                            </td>
                                        </tr>
                                        @endif
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery and SweetAlert JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Function to collect selected items and their quantities
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

            // Add to Cart Button Click Event with SweetAlert
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
                    url: '/extra_requirements',  // Update with your actual Laravel route
                    method: 'POST',
                    data: JSON.stringify({
                        items: itemsData,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    }),
                    contentType: "application/json", // Ensures correct request format
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
                let quantityInput = $(`#quantity_${itemId}`);
                let currentValue = parseInt(quantityInput.val());

                if (currentValue < 10) {
                    quantityInput.val(currentValue + 1);
                    updateTotal(itemId, price);
                }
            });

            // Decrease quantity
            $('.decrease-btn').click(function() {
                let itemId = $(this).data('id');
                let price = $(this).data('price');
                let quantityInput = $(`#quantity_${itemId}`);
                let currentValue = parseInt(quantityInput.val());

                if (currentValue > 0) {
                    quantityInput.val(currentValue - 1);
                    updateTotal(itemId, price);
                }
            });

            // Update total
            function updateTotal(itemId, price) {
                let quantityInput = $(`#quantity_${itemId}`);
                let total = quantityInput.val() * price;
                $(`#total_${itemId}`).html(`INR ${total.toFixed(2)}`);

                calculateGrandTotal();
            }

            // Calculate grand total
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
        });
    </script>

@endsection
