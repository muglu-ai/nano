@extends('layouts.dashboard')
@section('title', 'Invoices and Payments')
@section('content')



    <style>

        th {
            text-align: left !important;
            padding-left:8px !important;
        }
    </style>
    <div class="container-fluid py-2">
        <div class="row">
            <div class="ms-3">
                <h3 class="mb-0 h4 font-weight-bolder">@yield('title')</h3>
            </div>
        </div>
    </div>
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6 class="text-md">Invoices</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="datatable-basic">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" class=" text-uppercase text-white ">Company Name</th>
                                        <th scope="col" class=" text-uppercase  text-white">Category</th>
                                        <th scope="col" class=" text-uppercase   text-white">Total Amount</th>
                                        <th scope="col" class=" text-uppercase  text-white ">Paid Amount</th>
                                        <th scope="col" class=" text-uppercase  text-white text-center">Status</th>
                                        <th scope="col" class=" text-uppercase   text-white">Date</th>
                                        <th scope="col" class=" text-uppercase  text-white">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($applications as $application)
                                    @foreach($application->invoices as $invoice)
{{--                                        @dd($invoice)--}}
                                        <tr class="text-start">
                                            <td class="text-dark text-md ">
                                                <div class="mb-0 text-md text-dark text-start" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 150px;">
                                                    <a class="text-md text-info " href="{{ route('application.view', ['application_id' => $application->application_id]) }}">
                                                    {{ $application->billingDetail->billing_company ?? 'N/A' }}
                                                    </a>

                                                </div>
                                            </td>
{{--                                            <td>{{ $invoice->invoice_no }}</td>--}}
                                            <td><div class="text-md text-dark" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 150px;"> {{ $invoice->type ?? 'N/A' }}
                                            </div>
                                            </td>
{{--                                            <td>{{ $application->billingDetail->billing_company ?? 'N/A' }}</td>--}}

                                            <td class="text-dark text-md"> INR {{  number_format($invoice->amount, 2) }}</td>
                                            <td class="text-dark text-md"> INR {{  number_format($invoice->amount_paid, 2) }}</td>
                                            <td class="text-dark text-md">

                                         <span class=" badge d-block w-72
                                                {{ $invoice->payment_status === 'unpaid' ? 'badge-danger' :
                                                ($invoice->payment_status === 'partial' ? 'badge-warning' :
                                                ($invoice->payment_status === 'paid' ? 'badge-success' :'danger')) }}">
                                                {{ ucfirst($invoice->payment_status) }}
                                                </span>
                                            </td>
                                            <td class="text-dark text-md">{{ $invoice->created_at->format('Y-m-d') }}</td>
                                            <td class="text-dark text-md">
                                                <a href="{{ route('invoice.show', ['id' => $invoice->invoice_no]) }}" class="btn btn-info text-uppercase">View Receipt</a>
                                                <button type="button" class="btn btn-primary"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#uploadReceiptModal"
                                                        data-invoice="{{ $invoice->invoice_no }}"
                                                        data-app-id="{{ $application->application_id }}"
                                                        data-currency="INR">
                                                    Upload Payment Info and Onboard Exhibitor
                                                </button>

                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No invoices found.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="container-fluid py-2">
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
                                <label for="payment_method" class="form-control ms-0">Application No * </label>
                                <div class="mb-3 input-group input-group-dynamic">
                                    {{--                            <label for="invoice_id" class="form-label">Application No * </label>--}}
                                    <input type="text" class="multisteps-form__input form-control" id="app_id" name="app_id"
                                           value="{{$application->application_id}}" readonly required></div>
                                <div class="mb-3 input-group input-group-dynamic">
                                    <label for="payment_method" class="form-control ms-0">Payment Method * </label>
                                    <small>Select one of the options</small>
                                    <select class="form-control" id="payment_method" name="payment_method" required>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                        <option value="Credit Card">Credit Card</option>
                                        <option value="UPI">UPI</option>
                                        <option value="PayPal">PayPal</option>
                                        <option value="Cheque">Cheque</option>
                                        <option value="Cash">Cash</option>
                                    </select>
                                </div>
                                <label for="invoice_id" class="form-label">Transaction No * </label>
                                <div class="mb-3 input-group input-group-dynamic">
                                    <input type="text" class="multisteps-form__input form-control" id="transaction_no"
                                           name="transaction_no" required>
                                </div>
                                <label for="amount_paid" class="form-label">Amount Paid*</label>
                                <div class="mb-3 input-group input-group-dynamic">

                                    <input type="number" class="multisteps-form__input form-control" id="amount_paid"
                                           name="amount_paid" required>
                                </div>
                                <div class="mb-3 d-flex">
                                    <label for="payment_date" class="form-label me-2">Payment Date*</label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                                </div>
                                                    <div class="mb-3">
                                                        <label for="currency" class="form-label">Currency*</label>
                                <input type="text" class="form-control" id="currency" name="currency"
                                       value="INR" readonly>
                                                    </div>
{{--                                <div class="mb-5 input-group input-group-dynamic">--}}
{{--                                    <label for="receipt" class="form-label">Payment Receipt*</label>--}}
{{--                                </div>--}}
{{--                                <div class="mb-3 input-group input-group-dynamic">--}}
{{--                                    <input type="file" class="form-control" id="receipt" name="receipt_image"--}}
{{--                                           accept=".pdf,.jpeg,.jpg,.png" required>--}}
{{--                                </div>--}}
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                document.getElementById('uploadReceiptForm').addEventListener('submit', function (event) {
                    event.preventDefault(); // Prevent default form submission

                    const form = this;
                    const formData = new FormData(form);

                    fetch('{{ route('upload.receipt') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', // Important to indicate an AJAX request
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Get CSRF token from meta tag
                        },
                        body: formData
                    })

                        .then(response => response.json())
                        .then(data => {
                            if (data.message) {
                                console.log(data);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: data.message
                                }).then(() => {
                                    var uploadReceiptModal = bootstrap.Modal.getInstance(document.getElementById('uploadReceiptModal'));
                                    uploadReceiptModal.hide();
                                    form.reset(); // Reset the form after successful submission
                                    location.reload(); // Reload page to reflect changes

                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Upload Failed',
                                    text: data.error ? Object.values(data.error) : 'Something went wrong, please try again.'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Something went wrong!',
                                text: 'Error: ' + data.error ? Object.values(data.error) : 'Something went wrong, please try again.'
                            });
                        });
                    //console.log(formData);
                });

            </script>
        </div>



        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var uploadReceiptModal = document.getElementById('uploadReceiptModal');
                uploadReceiptModal.addEventListener('show.bs.modal', function (event) {
                    var button = event.relatedTarget; // Button that triggered the modal

                    // Extract dynamic data attributes
                    var invoiceNo = button.getAttribute('data-invoice');
                    var appId = button.getAttribute('data-app-id');
                    var currency = button.getAttribute('data-currency');

                    // Populate modal fields
                    document.getElementById('app_id').value = appId;
                    document.getElementById('currency').value = currency;
                });
            });
        </script>



@endsection
