@extends('layouts.dashboard_side')
@section('title', 'Applicant Details ')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                {{--                <div class="text-end mb-2" style="margin-right: 5px;"> --}}
                {{--                    <a href="javascript:" class="btn bg-gradient-dark ms-auto mb-0 text-end">Proforma Invoice</a> --}}
                {{--                </div> --}}
                <div class="card mb-4">
                    <div class="card-header p-3 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="w-50">
                                <h6>Exhibitor Order Details</h6>
                                <p class="text-sm mb-0">
                                    Application No: <b>{{ $application->application_id }}</b> <br> Date:
                                    <b>{{ $application->approved_date }}</b>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3 pt-0">
                        <hr class="horizontal dark mt-0 mb-4">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-12">
                                <div class="d-flex">
                                    <div>
                                        <h6 class="text-lg mb-0 mt-2">{{ $application->company_name }}</h6>
                                        <span class="mb-2 text-xs">Type of Business: <span
                                                class="text-dark font-weight-bold ms-2">{{ $application->type_of_business }}</span></span><br>
                                        <span class="mb-2 text-xs">Product Category: <span
                                                class="text-dark font-weight-bold ms-2">{{ $application->main_product_category }}</span></span><br>
                                        <span class="mb-2 text-xs">Sector(s): <span class="text-dark font-weight-bold ms-2">
                                                @foreach ($application->sectors as $sector)
                                                    {{ $sector['name'] }}@if (!$loop->last)
                                                        ,
                                                    @endif
                                                @endforeach
                                            </span></span>
                                        <br>
                                        {{--                                        <span class="mb-2 text-xs">Pay Status:</span> --}}
                                        {{--                                        <span --}}
                                        {{--                                            class="badge badge-sm bg-gradient-success">{{$invoice->payment_status}}</span> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-12 my-auto text-start">
                                <ul class="list-group">
                                    <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-3 text-sm">Stall Details </h6>
                                            <span class="mb-2 text-xs">Stall Category: <span
                                                    class="text-dark font-weight-bold ms-2">{{ $application->stall_category }}</span></span>
                                            <span class="mb-2 text-xs">Booth Size: <span
                                                    class="text-dark ms-2 font-weight-bold">{{ $application->interested_sqm }}
                                                    sqm</span></span>
                                            <span class="mb-2 text-xs">Booth Type: <span
                                                    class="text-dark ms-2 font-weight-bold">{{ $application->pref_location }}
                                                </span></span>
                                            <span class="mb-2 text-xs">Booth No: <span
                                                    class="text-dark ms-2 font-weight-bold">{{ $application->stallNumber }}
                                                </span></span>
                                            {{--                                    <span class="text-xs">GST Number: <span class="text-dark ms-2 font-weight-bold">FRB1235476</span></span> --}}
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <hr class="horizontal dark mt-4 mb-4">
                        <div class="row">
                            <div class="col-lg-5 col-md-6 col-12">
                                <h6 class="mb-3 mt-4">Billing Information</h6>
                                <ul class="list-group">
                                    <li class="list-group-item border-0 d-flex p-4 mb-2 bg-gray-100 border-radius-lg">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-3 text-sm">{{ $application->billingDetail->contact_name }}</h6>
                                            <span class="mb-2 text-xs">Company Name: <span
                                                    class="text-dark font-weight-bold ms-2">{{ $application->billingDetail->billing_company }}</span></span>
                                            <span class="mb-2 text-xs">Email Address: <span
                                                    class="text-dark ms-2 font-weight-bold">{{ $application->billingDetail->email }}</span></span>
                                            <span class="mb-2 text-xs">Mobile No: <span
                                                    class="text-dark ms-2 font-weight-bold">{{ $application->billingDetail->phone }}</span></span>
                                            <span class="mb-2 text-xs">Address: <span
                                                    class="text-dark ms-2 font-weight-bold">{{ $application->billingDetail->address }},
                                                    {{ $application->billingDetail->city_id }},
                                                    {{ $application->billingDetail->state->name }},
                                                    {{ $application->billingDetail->country->name }} </span></span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-lg-5 col-12 ms-auto">
                                <h6 class="mb-3">Order Summary</h6>
                                <div class="d-flex justify-content-between">
                                    <span class="mb-2 text-sm">
                                        Product Price:
                                    </span>
                                    <span class="text-dark font-weight-bold ms-2">INR
                                        {{ $application->invoice->price }}</span>
                                </div>
                                {{--                                <div class="d-flex justify-content-between"> --}}
                                {{--                    <span class="mb-2 text-sm"> --}}
                                {{--                      Processing Charge: --}}
                                {{--                    </span> --}}
                                {{--                                    <span --}}
                                {{--                                        class="text-dark ms-2 font-weight-bold">{{$application->invoice->currency}} {{$application->invoice->processing_charges}}</span> --}}
                                {{--                                </div> --}}
                                <div class="d-flex justify-content-between">
                                    <span class="text-sm">
                                        Taxes:
                                    </span>
                                    <span class="text-dark ms-2 font-weight-bold">INR
                                        {{ $application->invoice->gst }}</span>
                                </div>
                                <div class="d-flex justify-content-between mt-4">
                                    <span class="mb-2 text-lg">
                                        Total:
                                    </span>
                                    <span class="text-dark text-lg ms-2 font-weight-bold">INR
                                        {{ $application->invoice->amount }}</span>
                                </div>
                                @if ($application->invoice->payment_status == 'partial')
                                    <div class="d-flex justify-content-between mt-4">
                                        <span class="text-sm">
                                            Total Pending Amount Payable :
                                        </span>
                                        <span
                                            class="text-dark ms-2 font-weight-bold">{{ $application->invoice->pending_amount }}</span>
                                    </div>
                                @endif
                                @if ($application->invoice->partial_payment_percentage > 0)
                                    <div class="d-flex justify-content-between mt-4">
                                        <span class="text-sm">
                                            Total {{ $application->invoice->partial_payment_percentage }}% Partial Amount
                                            Payable :
                                        </span>
                                        <span class="text-dark ms-2 font-weight-bold">INR
                                            {{ $application->invoice->total_final_price }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-end" style="margin-right: 5px;">
                    {{--                    <a href="#" class="btn bg-gradient-primary" data-bs-toggle="modal" data-bs-target="#paymentModal">Pay --}}
                    {{--                        Now</a> --}}
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                        data-bs-target="#uploadReceiptModal">
                        Upload New Payment Receipt
                    </button>
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
                        <form id="uploadReceiptForm" action="{{ route('upload.receipt_user') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <meta name="csrf-token" content="{{ csrf_token() }}">

                            <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                            <label for="app_id" class="form-label">Application No *</label>
                            <div class="mb-3">
                                <input type="text" class="form-control" id="app_id" name="app_id"
                                    value="{{ $application->application_id }}" readonly required>
                            </div>

                            <label for="payment_method" class="form-label">Payment Method *</label>
                            <small>Select one of the options</small>
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

                            <label for="amount_paid" class="form-label">Amount Paid*</label>
                            <div class="mb-3">
                                <input type="number" class="form-control" id="amount_paid" name="amount_paid" value="{{ $application->invoice->amount }}" readonly required>
                            </div>

                            <label for="payment_date" class="form-label">Payment Date*</label>
                            <div class="mb-3">
                                <input type="date" class="form-control" id="payment_date" name="payment_date"
                                    required>
                            </div>

                            <input type="hidden" class="form-control" id="currency" name="currency" value="INR"
                                readonly>

                            <label for="receipt" class="form-label">Payment Receipt*</label>
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
            document.getElementById('uploadReceiptForm').addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                const form = this;
                const formData = new FormData(form);

                fetch('{{ route('upload.receipt_user') }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', // Important to indicate an AJAX request
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content') // Get CSRF token from meta tag
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message
                            }).then(() => {
                                const uploadReceiptModal = bootstrap.Modal.getInstance(document
                                    .getElementById('uploadReceiptModal'));
                                uploadReceiptModal.hide();
                                form.reset(); // Reset the form after successful submission
                                window.location.href = '/preview'; // Redirect to preview page
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload Failed',
                                text: data.error ? Object.values(data.error).join(', ') :
                                    'Something went wrong, please try again.'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong!',
                            text: 'Error: ' + error.message,

                        });
                        console.error('Error:', request);
                    });
            });
        </script>
    </div>


    <!-- Payment gateway Modal hidden -->
    {{--    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true"> --}}
    {{--        <div class="modal-dialog"> --}}
    {{--            <div class="modal-content"> --}}
    {{--                <div class="modal-header"> --}}
    {{--                    <h5 class="modal-title" id="paymentModalLabel">Payment Options</h5> --}}
    {{--                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
    {{--                </div> --}}
    {{--                <div class="modal-body"> --}}
    {{--                    <p>Please select your payment option:</p> --}}
    {{--                    <form action="{{ route('payment.full') }}" method="POST" style="display:inline;"> --}}
    {{--                        @csrf --}}
    {{--                        <input type="hidden" name="application_no" value="{{ $application->application_id }}"> --}}

    {{--                        <button type="submit" class="btn btn-primary" onclick="window.location.href='#'">Pay in Full --}}

    {{--                        </button> --}}
    {{--                    </form> --}}
    {{--                    <form action="{{ route('payment.partial') }}" method="POST" style="display:inline;"> --}}
    {{--                        @csrf --}}
    {{--                        <input type="hidden" name="application_no" value="{{ $application->application_id }}"> --}}
    {{--                        <button type="submit" class="btn btn-secondary">Partial Payment</button> --}}
    {{--                    </form> --}}
    {{--                </div> --}}
    {{--            </div> --}}
    {{--        </div> --}}
    {{--    </div> --}}
@endsection
