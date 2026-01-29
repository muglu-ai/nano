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

                                {{--                                <button id="purchase-extra-item" class="btn btn-primary">Purchase Extra Item</button>--}}
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
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Product</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Price</th>
                                        <th class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantity</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <img src="{{$item->image}}" class="avatar avatar-md me-3" alt="table image">
                                                    </div>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{$item->item_name}}</h6>
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
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Grand Total Section -->
                            <div class="d-flex justify-content-end mt-4">
                                <h5>Total Price: <span id="grand_total">INR 0.00</span></h5>
                            </div>

                            <!-- Add to Cart Button -->
                            <div class="d-flex justify-content-end mt-2">
                                <button class="btn btn-success" id="add-to-cart">Add to Cart</button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- JavaScript Code -->
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

            // Add to Cart Button Click Event
            $('#add-to-cart').click(function() {
                let itemsData = getSelectedItems();

                // Send data to backend (ExtraRequirementsController)
                $.ajax({
                    url: '/extra_requirements',  // Update with your actual Laravel route
                    method: 'POST',
                    data: {
                        items: itemsData,
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel security
                    },
                    success: function(response) {
                        console.log("Server Response:", response);
                        alert("Items successfully added to cart!");
                    },
                    error: function(error) {
                        console.log(data);
                        console.error("Error:", error);
                        alert("An error occurred while adding items to cart.");
                    }
                });

                // Log selected items in console
                console.log("Selected Items:", itemsData);
            });
        });


        $(document).ready(function() {
            // Increase quantity
            $('.increase-btn').click(function() {
                let itemId = $(this).data('id');
                let price = $(this).data('price');
                let quantityInput = $(#quantity_${itemId});
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
                let quantityInput = $(#quantity_${itemId});
                let currentValue = parseInt(quantityInput.val());

                if (currentValue > 0) {
                    quantityInput.val(currentValue - 1);
                    updateTotal(itemId, price);
                }
            });

            // Update total
            function updateTotal(itemId, price) {
                let quantityInput = $(#quantity_${itemId});
                let baseTotal = quantityInput.val() * price;
                let gstAmount = 0;
                let finalTotal = baseTotal + gstAmount;

                $(#total_${itemId}).html(&#8377; ${finalTotal.toFixed(2)});

                calculateGrandTotal();
            }

            // Calculate grand total
            function calculateGrandTotal() {
                let grandTotal = 0;

                $('.quantity-input').each(function() {
                    let itemId = $(this).attr('id').split('_')[1];
                    let price = parseFloat($(.increase-btn[data-id='${itemId}']).data('price'));
                    let quantity = parseInt($(this).val());
                    let baseTotal = quantity * price;
                    let gstAmount =  0;
                    let finalTotal = baseTotal + gstAmount;

                    grandTotal += finalTotal;
                });

                $('#grand_total').html(INR ${grandTotal.toFixed(2)});
            }

            // Add to Cart Button
            $('#add-to-cart').click(function() {
                alert("Items added to cart successfully!");
            });

            // Purchase Extra Item - AJAX call
            $('#purchase-extra-item').click(function() {
                $.ajax({
                    url: '/extra_requirements/list',
                    method: 'GET',
                    success: function(data) {
                        let itemsHtml = '';
                        data.forEach(function(item) {
                            itemsHtml += <div class="item">
                                <h5>${item.item_name}</h5>
                                <p>Days: ${item.days}</p>
                                <p>Price: ${item.price_for_expo}</p>
                                <p>Status: ${item.status}</p>
                            </div>;
                        });
                        $('.row').html(itemsHtml);
                    },
                    error: function(error) {
                        console.log('Error:', error);
                    }
                });
            });
        });
    </script>

@endsection
