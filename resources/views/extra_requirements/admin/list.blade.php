@extends('layouts.dashboard')
@section('title', 'Extra Requirement Orders')

@section('content')
    @php
        // Helper for JSON check
        function is_json($string)
        {
            json_decode($string);
            return json_last_error() == JSON_ERROR_NONE;
        }
    @endphp
    <style>
        th {
            text-align: left !important;
            padding-left: 10px !important;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            max-width: 220px;
        }

        td {
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
            max-width: 220px;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF Token Meta -->
    <script>
        function openModal(paymentId) {
            var modal = new bootstrap.Modal(document.getElementById('receiptModal' + paymentId));
            modal.show();
        }

        function verifyPayment(paymentId, status, user) {
            let remarks = document.getElementById('remarks' + paymentId).value;

            if (!remarks.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Remarks Required',
                    text: 'Please provide remarks before verifying the payment.',
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to mark this payment as ${status.toUpperCase()}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, confirm!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with AJAX request
                    $.ajax({
                        url: '/verify-extra-payment',
                        type: 'POST',
                        data: {
                            payment_id: paymentId,
                            status: status,
                            remarks: remarks,
                            user: user,
                            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Please wait while we verify the payment.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Payment verified successfully.',
                            }).then(() => {
                                location.reload(); // Reload page to reflect changes
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON?.message ||
                                'An error occurred while verifying the payment.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                            });
                        }
                    });
                }
            });
        }
    </script>

    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">@yield('title')</h2>
            <div class="d-flex align-items-center">
                <form method="GET" action="" class="me-3" id="filterForm">
                    <select name="status" class="form-select" id="paymentStatusSelect">
                        <option value="">All Status</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </form>
                <a href="#" id="exportDataBtn" class="btn btn-primary me-5">Export Data</a>
                <script>
                    // Submit filter form on dropdown change
                    document.getElementById('paymentStatusSelect').addEventListener('change', function() {
                        console.log('Filter form submitted with status:', this.value);
                        let status = this.value;
                        document.getElementById('filterForm').submit();
                    });

                    // Export button logic (kept as is, but uses status param)
                    document.getElementById('exportDataBtn').addEventListener('click', function(e) {
                        e.preventDefault();
                        let status = document.getElementById('paymentStatusSelect').value;
                        console.log('Exporting data with status:', status);
                        let url = "{{ route('export.requirements') }}";
                        if (status) {
                            url += '?payment_status=' + encodeURIComponent(status);
                        }
                        window.location.href = url;
                    });
                </script>
            </div>
        </div>

        @if ($orders->isEmpty())
            <p>No orders have been placed yet.</p>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">

                            <div class="table-responsive">
                                <table class="table table-flush  coe" id="datatable-basic">
                                    <thead class="thead-light table-dark">
                                        <tr>
                                            <th class="text-uppercase text-white">Invoice No</th>
                                            <th class="text-uppercase text-white">Company</th>
                                            <th class="text-uppercase text-white">Email</th>
                                            <th class="text-uppercase text-white" style="padding-left:20px !important;">
                                                Order Items</th>
                                            <th class="text-uppercase text-white" style="padding-left:4px !important;">Total
                                                Amount</th>
                                            <th scope="col" class="text-uppercase text-white"
                                                style="padding-left:4px !important;">Offline Receipt</th>
                                                <th class="text-uppercase text-white" style="padding-left:4px !important;">
                                                Payment Mode</th>

                                            <th class="text-uppercase text-white" style="padding-left:4px !important;">
                                                Payment Status</th>
                                            <th class="text-uppercase text-white" style="padding-left:10px !important;">
                                                Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                            @php
                                                $billingDetails = $order->application_id
                                                    ? \App\Models\Application::find($order->application_id)
                                                        ->billingDetail
                                                    : null;
                                                $payments = \App\Models\Payment::where(
                                                    'invoice_id',
                                                    $order->invoice_id,
                                                )->get();
                                            @endphp

                                            @if ($billingDetails != null)
                                                @php
                                                    $company = $billingDetails->billing_company;
                                                    $email = $billingDetails->email;
                                                @endphp
                                            @endif
                                           @php
    // Default values from billing details
    $company = $billingDetails->billing_company ?? 'N/A';
    $email = $billingDetails->email ?? ($order->user->email ?? 'N/A');

    // If user_id exists in CoExhibitor, override company and email
    $coExhibitor = \App\Models\CoExhibitor::where('user_id', $order->user_id)->first();
    if ($coExhibitor) {
        $company = $coExhibitor->co_exhibitor_name ?? $company;
        $email = $coExhibitor->email ?? $email;
    }
@endphp

                                            <tr>
                                                <td class="text-dark text-md"
                                                    style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 220px;">
                                                    <a href="{{ route('receipt.extra-requirements', ['invoice_id' => $order->invoice->invoice_no]) }}"
                                                        target="_blank"
                                                        style="color:#e63980;font-weight:600;text-decoration:underline;cursor:pointer;">
                                                        {{ $order->invoice->invoice_no ?? 'N/A' }}
                                                    </a>
                                                </td>
                                                <td class="text-dark text-md"
                                                    style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 220px;">
                                                    {{ $company ?? 'N/A' }}</td>
                                                <td class="text-dark text-md"
                                                    style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 220px;">
                                                    {{ $email ?? $order->user->email }}</td>
                                                <td class="text-dark text-md"
                                                    style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 220px;">
                                                    <ul class="text-dark text-md"
                                                        style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 220px;">
                                                        @foreach ($order->orderItems as $item)
                                                            <li class="text-dark text-md">
                                                                {{ $item->requirement->item_name }} ({{ $item->quantity }}
                                                                units)
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </td>
                                                <td class="text-dark text-md"
                                                    style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 220px;">
                                                    ₹{{ number_format($order->invoice->amount ?? 0, 2) }}</td>
                                                <td class="text-dark text-md">
                                                    @if ($payments->isNotEmpty())
    <a href="{{ route('invoice.show', ['id' => $order->invoice->invoice_no]) }}"
        class="btn btn-info text-uppercase">View Receipt</a>
    <br>
    @php
        // Collect all statuses
        $statuses = $payments->pluck('verification_status')->toArray();
        // Set default
        $displayStatus = null;
        if (in_array('Verified', $statuses)) {
            $displayStatus = 'Verified';
        } elseif (in_array('Pending', $statuses)) {
            $displayStatus = 'Pending';
        } elseif (in_array('Rejected', $statuses)) {
            $displayStatus = 'Rejected';
        }
    @endphp

    @if ($displayStatus == 'Verified')
        <span class="text-success">Payment Verified</span>
    @elseif ($displayStatus == 'Pending')
        <span class="text-warning">Payment Pending</span>
    @elseif ($displayStatus == 'Rejected')
        <span class="text-danger">Payment Rejected</span>
    @endif
@else
    <span class="text-muted">No Receipt</span>
@endif
                                                </td>
                                                 <td class="text-dark text-md">
                                                    {{ $order->payment_method ?? 'N/A' }}
                                                </td>
                                                <td class="text-dark text-md"><span
                                                        class=" badge d-block w-75 bg-{{ $order->invoice->payment_status == 'paid' ? 'success' : 'danger' }}">{{ ucfirst($order->invoice->payment_status ?? 'N/A') }}</span>
                                                </td>
                                                <td>
                                                    <!-- Mark as Deliver Button (opens modal) -->
                                                    @if ($order->invoice->payment_status == 'paid')
                                                        @if ($order->delivery_status != 'delivered')
                                                            <button class="btn btn-warning"
                                                                onclick="openModal('deliver{{ $order->id }}')">Mark as
                                                                Delivered</button>
                                                        @else
                                                            <span class="badge bg-success">Delivered</span>
                                                        @endif
                                                        <br>
                                                        @if (!empty($order->invoice->tax_invoice))
                                                            @php
                                                                $taxInvoices = is_array($order->invoice->tax_invoice)
                                                                    ? $order->invoice->tax_invoice
                                                                    : (is_json($order->invoice->tax_invoice)
                                                                        ? json_decode(
                                                                            $order->invoice->tax_invoice,
                                                                            true,
                                                                        )
                                                                        : [$order->invoice->tax_invoice]);
                                                            @endphp
                                                            @if (!empty($taxInvoices))
                                                                <div class="mt-2">
                                                                    <strong>Uploaded Tax Invoice(s):</strong>
                                                                    <ul class="mb-0">
                                                                        @foreach ($taxInvoices as $taxInvoice)
                                                                            <li>
                                                                                <a href="{{ asset('storage/' . ltrim($taxInvoice, '/')) }}"
                                                                                    target="_blank"
                                                                                    class="text-primary text-decoration-underline">
                                                                                    {{ basename($taxInvoice) }}
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                        @endif
                                                        <br>
                                                        <!-- Upload Tax Invoice Button -->
                                                        <button class="btn btn-info mt-2" data-bs-toggle="modal"
                                                            data-bs-target="#uploadTaxInvoiceModal{{ $order->id }}">
                                                            Upload Tax Invoice
                                                        </button>




                                                        <!-- Modal for Upload Tax Invoice -->
                                                        <div class="modal fade"
                                                            id="uploadTaxInvoiceModal{{ $order->id }}" tabindex="-1"
                                                            aria-labelledby="uploadTaxInvoiceLabel{{ $order->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="uploadTaxInvoiceLabel{{ $order->id }}">
                                                                            Upload Tax Invoice</h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>


                                                                    </div>
                                                                    <form
                                                                        action="{{ route('extra_requirements.upload_tax_invoice', $order->id) }}"
                                                                        method="POST" enctype="multipart/form-data">
                                                                        @csrf
                                                                        <div class="modal-body">
                                                                            <div class="mb-3">
                                                                                <label
                                                                                    for="tax_invoice_file{{ $order->id }}"
                                                                                    class="form-label">Select PDF
                                                                                    File</label>
                                                                                <input type="file" class="form-control"
                                                                                    id="tax_invoice_file{{ $order->id }}"
                                                                                    name="tax_invoice"
                                                                                    accept="application/pdf" required>
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="submit"
                                                                                class="btn btn-primary">Upload &
                                                                                Send</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif ($order->invoice->payment_status == 'unpaid')
                                                        {{-- Do not show anything for unpaid --}}
                                                    @endif

                                                    <!-- Modal for Mark as Deliver -->
                                                    <div class="modal fade" id="receiptModaldeliver{{ $order->id }}"
                                                        tabindex="-1"
                                                        aria-labelledby="deliverModalLabel{{ $order->id }}"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title"
                                                                        id="deliverModalLabel{{ $order->id }}">Mark as
                                                                        Delivered</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label for="deliverRemarks{{ $order->id }}"
                                                                            class="form-label">Remarks (required)</label>
                                                                        <textarea id="deliverRemarks{{ $order->id }}" class="form-control" placeholder="Enter remarks for delivery"
                                                                            required></textarea>
                                                                    </div>
                                                                    <div class="text-center">
                                                                        <button type="button" class="btn btn-success"
                                                                            onclick="
                                                            let remarks = document.getElementById('deliverRemarks{{ $order->id }}').value;
                                                            if (!remarks.trim()) {
                                                                Swal.fire({icon: 'warning', title: 'Remarks Required', text: 'Please provide remarks before marking as delivered.'});
                                                                return;
                                                            }
                                                            $.ajax({
                                                                url: '/mark-as-delivered',
                                                                type: 'POST',
                                                                data: {
                                                                    order_id: '{{ $order->id }}',
                                                                    remarks: remarks,
                                                                    _token: $('meta[name=csrf-token]').attr('content')
                                                                },
                                                                beforeSend: function () {
                                                                    Swal.fire({title: 'Processing...', allowOutsideClick: false, showConfirmButton: false, didOpen: () => {Swal.showLoading();}});
                                                                },
                                                                success: function (response) {
                                                                    Swal.fire({icon: 'success', title: 'Success!', text: response.message || 'Order marked as delivered.'}).then(() => {location.reload();});
                                                                },
                                                                error: function (xhr) {
                                                                    let errorMessage = xhr.responseJSON?.message || 'An error occurred.';
                                                                    Swal.fire({icon: 'error', title: 'Error!', text: errorMessage});
                                                                }
                                                            });
                                                        ">
                                                                            Confirm Delivery
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>



                                            {{-- @foreach ($payments as $payment)
                            <div class="modal fade" id="receiptModal{{ $payment->id }}" tabindex="-1" aria-labelledby="receiptModalLabel{{ $payment->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="receiptModalLabel{{ $payment->id }}">Receipt Image</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            @if ($payment->receipt_image)
                                                <img src="{{ asset('storage/' . $payment->receipt_image) }}" alt="Receipt Image" class="img-fluid">
                                            @else
                                                <p class="text-muted">No receipt available</p>
                                            @endif

                                            <div class="mt-3 input-group input-group-dynamic">
                                                <textarea id="remarks{{ $payment->id }}" name="remarks" class="form-control" placeholder="Enter remarks for payment {{ $payment->id }}" required></textarea>
                                            </div>
                                            <div class="mt-3 text-center">
                                                <button type="button" class="btn btn-success" onclick="verifyPayment({{ $payment->id }}, 'verified', '{{ Auth::user()->name }}')">Verify</button>
                                                <button type="button" class="btn btn-danger" onclick="verifyPayment({{ $payment->id }}, 'rejected', '{{ Auth::user()->name }}')">Reject</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach --}}
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
        @endif
    </div>
@endsection
