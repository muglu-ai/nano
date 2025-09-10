@extends('layouts.users')
@section('title', 'Orders')

@section('content')

    <div class="container">
        <h2 class="mb-4">Your Orders</h2>

        @if($orders->isEmpty())
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
                                <h5>Total Amount: ₹{{ number_format($order->invoice->amount ?? 0, 2) }}</h5>
                                <p><strong>Payment Status:</strong> {{ ucfirst($order->invoice->payment_status ?? 'N/A') }}</p>
                            </div>
                            <div>
                                @php
                                    $verifiedPayment = $order->invoice->payments->firstWhere('verification_status', 'Verified');
                                    $latestPayment = $order->invoice->payments->sortByDesc('payment_date')->first();
                                    $paymentToShow = $verifiedPayment ?? $latestPayment;
                                @endphp
                                <p><strong>Payment Date:</strong> {{ $paymentToShow && $paymentToShow->payment_date ? \Carbon\Carbon::parse($paymentToShow->payment_date)->format('d M Y') : 'N/A' }}</p>
                                @if($verifiedPayment)
                                    <p><strong>Delivery Status:</strong> {{ $order->order_status ?? 'Pending' }}</p>
                                @endif
                            </div>
                        </div>

                        @if($order->invoice->payment_status == 'unpaid')
                            <button type="button" class="btn btn-primary upload-receipt-btn"
                                    data-bs-toggle="modal" data-bs-target="#uploadReceiptModal"
                                    data-invoice="{{ $order->invoice->invoice_no ?? 'N/A' }}"
                                    data-amount="{{ $order->invoice->amount ?? 0 }}">
                                Upload New Payment Receipt
                            </button>
                        @endif
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
                    <form id="uploadReceiptForm" action="{{ route('upload.receipt') }}" method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        <meta name="csrf-token" content="{{ csrf_token() }}">

                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        <label for="app_id" class="form-control ms-0">Application No *</label>
                        <div class="mb-3 input-group input-group-dynamic">
                            <input type="text" class="form-control" id="app_id" name="invoice_id" value="" readonly
                                   required>
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
                            <input type="text" class="form-control" id="transaction_no" name="transaction_no" required>
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
            button.addEventListener('click', function () {
                document.getElementById('app_id').value = this.dataset.invoice;
                document.getElementById('amount_paid').value = this.dataset.amount;
            });
        });

        document.getElementById('uploadReceiptForm').addEventListener('submit', function (event) {
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
