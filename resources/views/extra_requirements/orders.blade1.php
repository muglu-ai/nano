@extends('layouts.users')
@section('title', 'Orders')

@section('content')
    {{-- @dd($billingDetails) --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css" />

    <style>
        #billingDetailsForm input[type="text"],
        #billingDetailsForm input[type="email"],
        #billingDetailsForm input[type="file"],
        #billingDetailsForm textarea {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 10px 12px;
            background-color: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-size: 15px;
            box-shadow: none;
        }

        #billingDetailsForm input:focus,
        #billingDetailsForm textarea:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
            outline: none;
        }

        #billingDetailsForm input[type="file"] {
            padding: 8px;
        }

        #billingDetailsForm .form-label {
            font-weight: 600;
            margin-bottom: 6px;
        }

        .iti {
            width: 100%;
        }

        .iti__flag-container {
            padding: 0;
        }

        .iti input[type="tel"] {
            width: 100%;
            height: 100%;
            border-radius: 8px;
            border: 1px solid #ced4da;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: #fff;
        }

        .iti input[type="tel"]:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
            outline: none;
        }

        .iti__flag-container .iti__selected-flag {
            height: 100%;
            border-radius: 8px 0 0 8px;
            background-color: #f8f9fa;
            border-right: 1px solid #ced4da;
        }
    </style>

    <div class="container">
        <h2 class="mb-4">Your Orders</h2>
        @if(request()->has('uploadSuccess'))
    <div class="alert mb-4" style="
        background: linear-gradient(90deg, #1e90ff 0%, #36d1c4 100%);
        color: #fff;
        font-size: 1.2rem;
        font-weight: 500;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(30,144,255,0.08);
        text-align: center;
        padding: 1.5rem 1rem;
        border: none;
    ">
        <div>
            <i class="fa-solid fa-clock fa-lg me-2"></i>
            <span style="font-weight:700;">Your payment receipt is under review.</span>
        </div>
        <div style="font-size:1rem; font-weight:400; margin-top:0.5rem;">
            Once verified, you will be notified.
        </div>
    </div>
@endif

        @php
            $hide = false;
        @endphp
        @if ($hide == true)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Billing Details</h5>
                    <button id="editBillingBtn" class="btn btn-warning btn-sm">Edit</button>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-3" id="editWarning" style="display: none;">
                        <strong>Warning:</strong> Billing details can be changed only once. Please review carefully before
                        saving.
                    </div>
                    <form id="billingDetailsForm" action="" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <th>Company:</th>
                                    <td>
                                        <span class="view-mode">{{ $billingDetails->billing_company }}</span>
                                        <input type="text" name="billing_company" class="form-control edit-mode"
                                            value="{{ $billingDetails->billing_company }}" style="display:none;" required>
                                    </td>
                                    <th>Contact Name:</th>
                                    <td>
                                        <span class="view-mode">{{ $billingDetails->contact_name }}</span>
                                        <input type="text" name="contact_name" class="form-control edit-mode"
                                            value="{{ $billingDetails->contact_name }}" style="display:none;" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>
                                        <span class="view-mode">{{ $billingDetails->email }}</span>
                                        <input type="email" name="email" class="form-control edit-mode"
                                            value="{{ $billingDetails->email }}" style="display:none;" required>
                                    </td>
                                    <th>Phone:</th>
                                    <td>
                                        <span class="view-mode">{{ $billingDetails->phone }}</span>
                                        <input type="text" name="phone" class="form-control edit-mode"
                                            value="{{ $billingDetails->phone }}" style="display:none;" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Address:</th>
                                    <td>
                                        <span class="view-mode">{{ $billingDetails->address }}</span>
                                        <input type="text" name="address" class="form-control edit-mode"
                                            value="{{ $billingDetails->address }}" style="display:none;" required>
                                    </td>
                                    <th>GST:</th>
                                    <td>
                                        <span class="view-mode">{{ $application->gst_no ?? 'N/A' }}</span>
                                        <input type="text" name="gst_no" class="form-control edit-mode"
                                            value="{{ $application->gst_no ?? '' }}" style="display:none;">
                                    </td>
                                </tr>
                                <tr>
                                    <th>City:</th>
                                    <td>
                                        <span class="view-mode">{{ $billingDetails->city_id }}</span>
                                        <input type="text" name="city_id" class="form-control edit-mode"
                                            value="{{ $billingDetails->city_id }}" style="display:none;" required>
                                    </td>
                                    <th>State:</th>
                                    <td>
                                        <span class="view-mode">{{ $billingDetails->state->name }}</span>
                                        <input type="text" name="state" class="form-control edit-mode"
                                            value="{{ $billingDetails->state->name }}" style="display:none;" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Country:</th>
                                    <td>
                                        <span class="view-mode">{{ $billingDetails->country->name }}</span>
                                        <input type="text" name="country" class="form-control edit-mode"
                                            value="{{ $billingDetails->country->name }}" style="display:none;" required>
                                    </td>
                                    <th>Postal Code:</th>
                                    <td>
                                        <span class="view-mode">{{ $billingDetails->postal_code }}</span>
                                        <input type="text" name="postal_code" class="form-control edit-mode"
                                            value="{{ $billingDetails->postal_code }}" style="display:none;" required>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="mt-3 edit-mode" style="display:none;">
                            <button type="submit" class="btn btn-success">Save</button>
                            <button type="button" id="cancelEditBtn" class="btn btn-secondary">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                document.getElementById('editBillingBtn').addEventListener('click', function() {
                    document.querySelectorAll('.view-mode').forEach(el => el.style.display = 'none');
                    document.querySelectorAll('.edit-mode').forEach(el => el.style.display = '');
                    document.getElementById('editWarning').style.display = '';
                    this.style.display = 'none';
                });
                document.getElementById('cancelEditBtn').addEventListener('click', function() {
                    document.querySelectorAll('.view-mode').forEach(el => el.style.display = '');
                    document.querySelectorAll('.edit-mode').forEach(el => el.style.display = 'none');
                    document.getElementById('editWarning').style.display = 'none';
                    document.getElementById('editBillingBtn').style.display = '';
                });
            </script>
        @endif

        @if ($orders->isEmpty())
            <p>No orders found.</p>
        @else
            @foreach ($orders as $order)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Order #{{ $order->id }} | Invoice #{{ $order->invoice->invoice_no ?? 'N/A' }}</h5>
                        <small>Placed on: {{ $order->created_at->format('d M Y, h:i A') }}</small>
                    </div>
                    <div class="card-body">
                        <h6>Order Items:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Quantity</th>
                                        <th>Unit Price</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->orderItems as $item)
                                        <tr>
                                            <td>{{ $item->requirement->item_name }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>₹{{ number_format($item->unit_price, 2) }}</td>
                                            <td>₹{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                 @if($order->invoice->surCharge > 0)
                                    <h5>Surcharge @ {{ $order->invoice->surChargepercentage }} %: ₹{{ number_format($order->invoice->surCharge, 2) }}</h5>
                                @endif
                                <h5>Total Amount: ₹{{ number_format($order->invoice->amount ?? 0, 2) }}</h5>
                                <p><strong>Payment Status:</strong> {{ ucfirst($order->invoice->payment_status ?? 'N/A') }}
                                </p>
                            </div>
                            <div>
                                @php
                                    $verifiedPayment =
                                        $order->invoice->payment_status == 'paid'
                                            ? $order->invoice->payments->first()
                                            : null;
                                    $latestPayment = $order->invoice->payments->sortByDesc('payment_date')->first();
                                    $paymentToShow = $verifiedPayment ?? $latestPayment;
                                @endphp

                                @if ($order->invoice->payment_status == 'paid')
                                    <h5><strong>Amount Received: </strong>{{ $order->invoice->currency }}
                                        {{ $order->invoice->amount_paid ?? 'N/A' }} </h5>

                                    <p><strong>Payment Date: </strong>
                                        {{ $order->invoice->updated_at ? \Carbon\Carbon::parse($order->invoice->updated_at)->format('d M Y') : 'N/A' }}
                                    </p>
                                @endif
                                @if ($verifiedPayment)
                                    <p><strong>Delivery Status:</strong> {{ $order->order_status ?? 'Pending' }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- @if ($order->invoice->payment_status == 'unpaid') --}}
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                @if ($order->invoice->payment_status == 'paid' || $order->invoice->payment_status == 'unpaid')
                                    <a href="{{ route('download.extra-requirements', $order->invoice->invoice_no) }}"
                                        class="btn btn-success" target="_blank">
                                        Download Proforma Invoice
                                    </a>
                                @endif
                            </div>
                            <div>
                                @if ($order->invoice->payment_status == 'unpaid')
                                    <a href="/payment/{{ $order->invoice->invoice_no }}" target="_blank"
                                        class="btn btn-primary">
                                        Pay Now
                                    </a>
                                @endif
                            </div>
                        </div>
                        {{-- @endif --}}
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="modal fade" id="uploadReceiptModal" tabindex="-1" aria-labelledby="uploadReceiptModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadReceiptModalLabel">Upload Payment Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadReceiptForm" action="{{ route('upload.receipt_user') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <meta name="csrf-token" content="{{ csrf_token() }}">

                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        <label for="app_id" class="form-control ms-0">Application No *</label>
                        <div class="mb-3 input-group input-group-dynamic">
                            <input type="text" class="form-control" id="app_id" name="invoice_id" value=""
                                readonly required>
                        </div>
                        <label for="payment_method" class="form-control ms-0">Payment Method *</label>
                        <div class="mb-3">
                            <select class="form-control" id="payment_method" name="payment_method" required>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Credit Card">Credit Card</option>
                                <option value="UPI">UPI</option>
                                <option value="PayPal">PayPal</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Cash">Cash</option>
                            </select>
                        </div>
                        <label for="transaction_no" class="form-label">Transaction No *</label>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="transaction_no" name="transaction_no"
                                required>
                        </div>
                        <label for="amount_paid" class="form-label">Amount Paid *</label>
                        <div class="mb-3">
                            <input type="number" class="form-control" id="amount_paid" name="amount_paid" readonly
                                required>
                        </div>
                        <label for="payment_date" class="form-label">Payment Date *</label>
                        <div class="mb-3">
                            <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                        </div>
                        <label for="receipt" class="form-label">Payment Receipt *</label>
                        <div class="mb-3">
                            <input type="file" class="form-control" id="receipt" name="receipt_image"
                                accept=".pdf,.jpeg,.jpg,.png" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.upload-receipt-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('app_id').value = this.dataset.invoice;
                document.getElementById('amount_paid').value = this.dataset.amount;
            });
        });

        document.getElementById('uploadReceiptForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.message) {
                        Swal.fire('Success', 'Payment receipt uploaded successfully!', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error', data.message || 'Something went wrong!', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Error', 'Something went wrong!', 'error');
                });
        });
    </script>
@endsection
