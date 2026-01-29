@extends('layouts.users')
@section('title', 'Receipt(s)')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="container-fluid py-2">
        <div class="row">
            <div class="ms-3 mt-3">
                <h3 class="mb-0 h4 font-weight-bolder mb-3">Receipt and Documents</h3>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Receipt</h6>

                        {{--                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadReceiptModal">--}}
                        {{--                            Upload New Payment Receipt--}}
                        {{--                        </button>--}}
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table table-flush mb-0">
                                <thead class="thead-light table-dark">
                                <tr>
                                    <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">
                                        Date
                                    </th>
                                    <th class="text-left  text-uppercase text-white text-md font-weight-bolder ps-2">
                                        Receipt ID
                                    </th>
                                    <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">
                                        Type
                                    </th>
                                    {{--                                       <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Total Amount</th> --}}
                                    <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">
                                        Total Amount Paid
                                    </th>
                                    {{--                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Partial Paid </th>--}}
                                    {{--   <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Pending Amount </th> --}}
                                    <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">
                                        Status
                                    </th>
                                    <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">
                                        View
                                    </th>

                                </tr>
                                </thead>
                                <tbody>

                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td class="text-left text-dark text-md">{{ $invoice->created_at->format('M d, Y') }}</td>
                                        <td class="text-left text-dark text-md">{{ $invoice->invoice_no }}</td>
                                        <td class="text-left text-dark text-md">
                                            {{ $invoice->type == 'extra_requirement' ? 'Extra Requirements' : $invoice->type }}
                                        </td>
                                        <td class="text-left text-dark text-md">{{ $invoice->amount_paid }}</td>
                                        {{--                                            @if($invoice->payment_status == 'partial')--}}
                                        {{--                                            <td class="text-center">{{ $invoice->amount }}</td>--}}
                                        {{--                                            @endif--}}
                                        {{--                                            @if($invoice->payment_status == 'partial')
                                                                                       <td class="text-left text-dark text-md">{{ $invoice->pending_amount }}</td>
                                                                                   @elseif($invoice->payment_status == 'paid')
                                                                                       <td class="text-left text-dark text-md">0</td>
                                                                                   @elseif($invoice->payment_status == 'unpaid')
                                                                                       <td class="text-left text-dark text-md">{{ $invoice->amount }}</td>
                                                                                   @else
                                                                                       <td class="text-left text-dark text-md">{{ $invoice->pending_amount }}</td>
                                                                                   @endif --}}
                                        {{--                                            <td class="text-center">{{ $invoice->payment_status }}</td>--}}
                                        @php
                                            $statusColor = match($invoice->payment_status) {
                                                'paid' => 'success',
                                                'partial' => 'warning',
                                                default => 'danger', // for 'unpaid' or any other status
                                            };
                                        @endphp
                                        <td class="text-center text-white text-md text-bold ">
                                            <span class="d-block w-50 bg-{{ $statusColor }} rounded-pill">
                                                {{ ucfirst($invoice->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="text-left text-dark text-md">
                                            @if($invoice->type == 'Stall Booking')
                                                <a href="{{ route('invoice.mail.view', $invoice->application_no) }}"
                                                   class="btn btn-sm btn-info" target="_blank">
                                                    View
                                                </a>
                                            @elseif($invoice->type == 'Co-Exhibitor')
                                                <a href="{{ route('co-invoice.mail.view', $invoice->co_exhibitorID) }}"
                                                   class="btn btn-sm btn-info" target="_blank">
                                                    View
                                                </a>
                                            @else
                                                <a href="{{ route('extra-requirements.email', $invoice->invoice_no) }}"
                                                   class="btn btn-sm btn-info" target="_blank">
                                                    View
                                                </a>
                                            @endif

                                            @if($invoice->payment_status == 'unpaid')
                                                <a href="{{ route('paypal.form', $invoice->invoice_no) }}"
                                                   class="btn btn-sm btn-success" target="_blank">
                                                    Pay Now
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @php
            $hide = false;
        @endphp
        @if($hide == true)
            <div class="row mt-5 ">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <h6>Payment Receipt Uploaded</h6>
                            {{--                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadReceiptModal">--}}
                            {{--                            Upload New Payment Receipt--}}
                            {{--                        </button>--}}
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table table-flush mb-0">
                                    <thead class="thead-light table-dark">
                                    <tr>
                                        <th class="text-left text-uppercase text-white text-md font-weight-bolder  ps-2">
                                            Date
                                        </th>
                                        <th class="text-left text-uppercase text-white text-md font-weight-bolder ps-2 ">
                                            Transaction No
                                        </th>
                                        <th class="text-left text-uppercase text-white text-md font-weight-bolder  ps-2">
                                            Total Amount Paid
                                        </th>
                                        <th class="text-left text-uppercase text-white text-md font-weight-bolder  ps-2">
                                            Verification Status
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($payments as $payment)

                                        <tr>
                                            <td class="text-dark text-md">{{ $payment->created_at->format('M d, Y') }}</td>
                                            <td class="text-dark text-md">{{ $payment->transaction_id }}</td>
                                            <td class="text-dark text-md">{{ $payment->amount_paid }}</td>
                                            <td class="text-dark text-md"><span
                                                        class=" badge d-block w-35 bg-{{ $payment->verification_status  == 'Verified' ? 'success' : 'danger'}}">{{ ucfirst($payment->verification_status) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Modal -->
        {{--        <div class="modal fade" id="uploadReceiptModal" tabindex="-1" aria-labelledby="uploadReceiptModalLabel" aria-hidden="true">--}}
        {{--    <div class="modal-dialog">--}}
        {{--        <div class="modal-content">--}}
        {{--            <div class="modal-header">--}}
        {{--                <h5 class="modal-title" id="uploadReceiptModalLabel">Upload Payment Receipt</h5>--}}
        {{--                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>--}}
        {{--            </div>--}}
        {{--            <div class="modal-body">--}}
        {{--                <form id="uploadReceiptForm" action="{{ route('upload.receipt') }}" method="POST" enctype="multipart/form-data">--}}
        {{--                    @csrf--}}
        {{--                    <meta name="csrf-token" content="{{ csrf_token() }}">--}}

        {{--                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">--}}
        {{--                    <div class="mb-3 input-group input-group-dynamic">--}}
        {{--                        <label for="invoice_id" class="form-label">Invoice ID * </label>--}}
        {{--                        <input type="text" class="multisteps-form__input form-control" id="invoice_id" name="invoice_id" value="{{ $invoice->invoice_no }}" required>--}}
        {{--                    </div>--}}
        {{--                    <div class="mb-3 input-group input-group-dynamic">--}}
        {{--                        <label for="payment_method" class="form-control ms-0">Payment Method * </label>--}}
        {{--                        <small>Select one of the options</small>--}}
        {{--                        <select class="form-control" id="payment_method" name="payment_method" required>--}}
        {{--                            <option value="Bank Transfer">Bank Transfer</option>--}}
        {{--                            <option value="Credit Card">Credit Card</option>--}}
        {{--                            <option value="UPI">UPI</option>--}}
        {{--                            <option value="PayPal">PayPal</option>--}}
        {{--                            <option value="Cheque">Cheque</option>--}}
        {{--                            <option value="Cash">Cash</option>--}}
        {{--                        </select>--}}
        {{--                    </div>--}}
        {{--                    <div class="mb-3 input-group input-group-dynamic">--}}
        {{--                        <label for="invoice_id" class="form-label">Transaction No * </label>--}}
        {{--                        <input type="text" class="multisteps-form__input form-control" id="transaction_no" name="transaction_no" required>--}}
        {{--                    </div>--}}
        {{--                    <div class="mb-3 input-group input-group-dynamic">--}}
        {{--                        <label for="amount_paid" class="form-label">Amount Paid*</label>--}}
        {{--                        <input type="number" class="multisteps-form__input form-control" id="amount_paid" name="amount_paid" required>--}}
        {{--                    </div>--}}
        {{--                    <div class="mb-3 d-flex">--}}
        {{--                        <label for="payment_date" class="form-label me-2">Payment Date*</label>--}}
        {{--                        <input type="date" class="form-control" id="payment_date" name="payment_date" required>--}}
        {{--                    </div>--}}
        {{--                    <div class="mb-3">--}}
        {{--                        <label for="currency" class="form-label">Currency*</label>--}}
        {{--                        <input type="hidden" class="form-control" id="currency" name="currency" value="{{ $application->payment_currency }}" readonly>--}}
        {{--                    </div>--}}
        {{--                    <div class="mb-5 input-group input-group-dynamic">--}}
        {{--                        <label for="receipt" class="form-label">Payment Receipt*</label>--}}
        {{--                    </div>--}}
        {{--                    <div class="mb-3 input-group input-group-dynamic">--}}
        {{--                        <input type="file" class="form-control" id="receipt" name="receipt_image" required>--}}
        {{--                    </div>--}}
        {{--                    <button type="submit" class="btn btn-primary">Upload</button>--}}
        {{--                </form>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        {{--    </div>--}}

        {{--            <script>--}}
        {{--                document.getElementById('uploadReceiptForm').addEventListener('submit', function(event) {--}}
        {{--                    event.preventDefault(); // Prevent default form submission--}}

        {{--                    const form = this;--}}
        {{--                    const formData = new FormData(form);--}}

        {{--                    fetch('{{ route('upload.receipt') }}', {--}}
        {{--                        method: 'POST',--}}
        {{--                        headers: {--}}
        {{--                            'X-Requested-With': 'XMLHttpRequest', // Important to indicate an AJAX request--}}
        {{--                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Get CSRF token from meta tag--}}
        {{--                        },--}}
        {{--                        body: formData--}}
        {{--                    })--}}
        {{--                        .then(response => response.json())--}}
        {{--                        .then(data => {--}}
        {{--                            if (data.message) {--}}
        {{--                                console.log(data);--}}
        {{--                                Swal.fire({--}}
        {{--                                    icon: 'success',--}}
        {{--                                    title: 'Success!',--}}
        {{--                                    text: data.message--}}
        {{--                                }).then(() => {--}}
        {{--                                    var uploadReceiptModal = bootstrap.Modal.getInstance(document.getElementById('uploadReceiptModal'));--}}
        {{--                                    uploadReceiptModal.hide();--}}
        {{--                                    form.reset(); // Reset the form after successful submission--}}
        {{--                                    location.reload(); // Reload page to reflect changes--}}
        {{--                                });--}}
        {{--                            } else {--}}
        {{--                                Swal.fire({--}}
        {{--                                    icon: 'error',--}}
        {{--                                    title: 'Upload Failed',--}}
        {{--                                    text: data.error ? Object.values(data.error) : 'Something went wrong, please try again.'--}}
        {{--                                });--}}
        {{--                            }--}}
        {{--                        })--}}
        {{--                        .catch(error => {--}}
        {{--                            Swal.fire({--}}
        {{--                                icon: 'error',--}}
        {{--                                title: 'Something went wrong!',--}}
        {{--                                text: 'Error: ' + data.error ? Object.values(data.error) : 'Something went wrong, please try again.'--}}
        {{--                            });--}}
        {{--                        });--}}
        {{--                });--}}
        {{--            </script>--}}
        {{--</div>--}}
    </div>

@endsection
