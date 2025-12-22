@extends('layouts.startup-zone')

@section('title', 'Payment - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@push('styles')
<style>
    .payment-option-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e0e0e0;
    }
    .payment-option-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 4px 8px rgba(27, 55, 131, 0.2);
        transform: translateY(-2px);
    }
    .payment-option-card.border-primary {
        border-color: var(--primary-color) !important;
        background-color: #f0f8ff;
    }
    #paypal-button-container {
        margin-top: 1rem;
    }
</style>
<!-- PayPal SDK -->
<script src="https://www.paypal.com/sdk/js?client-id=Af98MdWNTOZO-rKE9MdjRJE50vr3Rp9DOYfr3TwidA9kzexdt2NGYAfXP9DfjK_5PTmTzxsxtoufZCyT&currency=USD"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('receipt_success'))
                <div class="alert alert-success text-center mb-4">
                    <strong>Payment Receipt Uploaded Successfully.</strong><br>
                    <span>Your payment receipt is currently under verification. You will be notified once it has been reviewed and approved.</span>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card"></i> Payment</h4>
                </div>
                <div class="card-body">
                    {{-- Application Summary --}}
                    @if(isset($invoice->application_id) && $invoice->application_id)
                        @php
                            $application = \App\Models\Application::find($invoice->application_id);
                        @endphp
                        @if($application)
                            <div class="alert alert-info mb-4">
                                <strong>Application ID:</strong> {{ $application->application_id }}<br>
                                <strong>Company:</strong> {{ $application->company_name }}
                            </div>
                        @endif
                    @endif

                    <div class="row g-4">
                        <!-- Order Information -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="card-title mb-0">Order Information</h5>
                                    <p class="card-subtitle text-muted small">Review your order details</p>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-5 fw-medium">Order ID:</div>
                                        <div class="col-7">{{ $invoice->invoice_no }}</div>

                                        <div class="col-5 fw-medium">Billing Company:</div>
                                        <div class="col-7">{{ $billingDetail->billing_company }}</div>

                                        <div class="col-5 fw-medium">Billing Name:</div>
                                        <div class="col-7">{{ $billingDetail->contact_name }}</div>

                                        <div class="col-5 fw-medium">Billing Email:</div>
                                        <div class="col-7 text-break">{{ $billingDetail->email }}</div>

                                        <div class="col-5 fw-medium">Billing Phone:</div>
                                        <div class="col-7">{{ $billingDetail->phone }}</div>

                                        <div class="col-5 fw-medium">Billing Address:</div>
                                        <div class="col-7">{{ $billingDetail->address }}</div>

                                        @if(isset($billingDetail->state) && $billingDetail->state)
                                            <div class="col-5 fw-medium">Billing State:</div>
                                            <div class="col-7">{{ $billingDetail->state->name }}</div>
                                        @endif

                                        @if(isset($billingDetail->country) && $billingDetail->country)
                                            <div class="col-5 fw-medium">Billing Country:</div>
                                            <div class="col-7">{{ $billingDetail->country->name }}</div>
                                        @endif

                                        <div class="col-5 fw-medium">Zipcode:</div>
                                        <div class="col-7">{{ $billingDetail->postal_code ?? '' }}</div>
                                    </div>

                                    @if(isset($orders) && $orders->count() > 0)
                                        <hr class="my-3">
                                        <div>
                                            <h6 class="fw-medium mb-2">Order Items:</h6>
                                            <ul class="list-unstyled">
                                                @foreach ($orders as $order)
                                                    @foreach ($order->orderItems as $item)
                                                        <li>{{ $item->requirement->item_name ?? 'N/A' }}</li>
                                                    @endforeach
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if ($invoice->payment_status == 'unpaid')
                                        <div class="mb-3 mt-3">
                                            <button class="btn btn-link p-0" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#editBillingDetails" aria-expanded="false"
                                                aria-controls="editBillingDetails">
                                                Change Billing Details?
                                            </button>
                                        </div>

                                        <div class="collapse" id="editBillingDetails">
                                            <form method="POST" action="{{ route('extra_requirements.billing') }}"
                                                id="billingDetailsForm">
                                                @csrf
                                                @method('POST')
                                                <input type="hidden" name="invoice_id" value="{{ $invoice->invoice_no }}">
                                                <div class="row g-2">
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">Billing Company</label>
                                                        <input type="text" name="billing_company" class="form-control"
                                                            value="{{ old('billing_company', $billingDetail->billing_company) }}"
                                                            required>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">Billing Name</label>
                                                        <input type="text" name="contact_name" class="form-control"
                                                            value="{{ old('contact_name', $billingDetail->contact_name) }}"
                                                            required>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">Billing Email</label>
                                                        <input type="email" name="email" class="form-control"
                                                            value="{{ old('email', $billingDetail->email) }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">Billing Phone</label>
                                                        <input type="text" name="phone" class="form-control"
                                                            value="{{ old('phone', $billingDetail->phone) }}" required>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">GST No</label>
                                                        <input type="text" name="gst" class="form-control"
                                                            value="{{ old('gst', $billingDetail->gst ?? '') }}">
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">PAN No</label>
                                                        <input type="text" name="pan_no" class="form-control"
                                                            value="{{ old('pan_no', $billingDetail->pan_no ?? '') }}">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">Billing Address</label>
                                                        <input type="text" name="address" class="form-control"
                                                            value="{{ old('address', $billingDetail->address) }}"
                                                            required>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">Country</label>
                                                        <select name="country_id" class="form-select" required>
                                                            @foreach ($countries as $country)
                                                                <option value="{{ $country->id }}"
                                                                    @if (isset($billingDetail->country_id) && $billingDetail->country_id == $country->id) selected @endif>
                                                                    {{ $country->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <label class="form-label">State</label>
                                                        <select name="state_id" id="state_id" class="form-select"
                                                            required>
                                                            @if (isset($billingDetail->country) && $billingDetail->country && isset($billingDetail->country->states))
                                                                @foreach ($billingDetail->country->states as $state)
                                                                    <option value="{{ $state->id }}"
                                                                        @if (isset($billingDetail->state_id) && $billingDetail->state_id == $state->id) selected @endif>
                                                                        {{ $state->name }}
                                                                    </option>
                                                                @endforeach
                                                            @elseif(isset($billingDetail->state) && $billingDetail->state)
                                                                <option value="{{ $billingDetail->state_id }}" selected>
                                                                    {{ $billingDetail->state->name }}</option>
                                                            @else
                                                                <option value="">Select State</option>
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-primary">Update Billing Details</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary & Options -->
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-white">
                                    <h5 class="card-title mb-0">Payment Summary</h5>
                                    <p class="card-subtitle text-muted small">Review your payment details</p>
                                </div>
                                <div class="card-body">
                                    @if ($invoice->payment_status == 'unpaid')
                                        @php
                                            $color = '#FFA500';
                                        @endphp
                                        <div class="alert alert-warning" role="alert" id="not-paid">
                                            Your order is pending payment. Please complete the payment to proceed.
                                        </div>
                                    @else
                                        @php
                                            $color = '#28a745';
                                        @endphp
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle"></i> Payment completed successfully!
                                        </div>
                                    @endif

                                    <div class="row mb-2" style="color:{{ $color }};">
                                        <div class="col-6 fw-medium">Payment Status:</div>
                                        <div class="col-6" id="pay_status">{{ ucfirst($invoice->payment_status) }}</div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="row mb-2">
                                        <div class="col-6 fw-medium">Item Total:</div>
                                        <div class="col-6">{{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->price, 2) }}</div>
                                    </div>

                                    @if(isset($invoice->surCharge) && $invoice->surCharge > 0)
                                        <div class="row mb-2">
                                            <div class="col-6 fw-medium">Surcharge ({{ $invoice->surChargepercentage ?? 0 }}%):</div>
                                            <div class="col-6">{{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->surCharge, 2) }}</div>
                                        </div>
                                    @endif

                                    @if(isset($invoice->processing_charges) && $invoice->processing_charges > 0)
                                        <div class="row mb-2">
                                            <div class="col-6 fw-medium">Processing Charges:</div>
                                            <div class="col-6">{{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->processing_charges, 2) }}</div>
                                        </div>
                                    @endif

                                    @if(isset($invoice->gst) && $invoice->gst > 0)
                                        <div class="row mb-2">
                                            <div class="col-6 fw-medium">GST Tax:</div>
                                            <div class="col-6">{{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->gst, 2) }}</div>
                                        </div>
                                    @endif

                                    <hr class="my-3">

                                    <div class="row mb-2">
                                        <div class="col-6 fw-medium"><strong>Total Amount:</strong></div>
                                        <div class="col-6"><strong>{{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->total_final_price ?? $invoice->amount, 2) }}</strong></div>
                                    </div>

                                    @if (isset($billingDetail->country) && $billingDetail->country && strtolower($billingDetail->country->name) != 'india' && isset($invoice->int_amount_value))
                                        <div class="row">
                                            <div class="col-6 fw-medium">Total (USD):</div>
                                            <div class="col-6">USD {{ number_format($invoice->int_amount_value, 2) }}</div>
                                        </div>
                                    @endif
                                </div>

                                @if ($invoice->payment_status == 'unpaid')
                                    <div class="card-footer bg-white">
                                        <h5 class="card-title mb-3">Payment Method</h5>

                                        @if(isset($billingDetail->country) && $billingDetail->country && strtolower($billingDetail->country->name) == 'india')
                                            <div class="mb-4">
                                                <h6 class="mb-3">Indian Payments (INR)</h6>
                                                <p class="text-muted small mb-3">For INR payments, please use CCAvenue gateway or upload payment receipt for offline payment.</p>
                                                <a href="{{ route('payment.ccavenue', ['id' => $invoice->invoice_no]) }}" class="btn btn-primary w-100 mb-3">
                                                    <i class="fas fa-credit-card"></i> Pay via CCAvenue
                                                </a>
                                            </div>
                                        @else
                                            <div class="mb-4">
                                                <h6 class="mb-3">International Payments (USD)</h6>
                                                <div id="paypal-button-container" class="mt-3"></div>
                                            </div>
                                        @endif

                                        <!-- Offline Payment Method -->
                                        <div class="mt-4 pt-4 border-top">
                                            <h6 class="card-title mb-3">Offline Payment</h6>
                                            <p class="text-muted small">For offline payment, please upload your payment receipt and transaction details below.</p>
                                            <button type="button" class="btn btn-outline-primary w-100 upload-receipt-btn"
                                                data-bs-toggle="modal" data-bs-target="#uploadReceiptModal"
                                                data-invoice="{{ $invoice->invoice_no }}"
                                                data-amount="{{ (isset($billingDetail->country) && $billingDetail->country && strtolower($billingDetail->country->name) == 'india') ? $invoice->amount : ($invoice->int_amount_value ?? $invoice->amount) }}">
                                                <i class="fas fa-upload me-1"></i> Upload Payment Receipt
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Payment Receipt Modal -->
<div class="modal fade" id="uploadReceiptModal" tabindex="-1" aria-labelledby="uploadReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="uploadReceiptForm" action="{{ route('upload.receipt_extra') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadReceiptModalLabel">Upload Payment Receipt</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="app_id" class="form-label">Invoice</label>
                        <input type="text" class="form-control" id="app_id" name="invoice_id" readonly
                            value="{{ $invoice->invoice_no }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="amount_paid" class="form-label">Amount</label>
                        <input type="number" class="form-control" id="amount_paid" name="amount_paid" min="0" step="any" pattern="[0-9]*" inputmode="decimal">
                    </div>
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="UPI">UPI</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Cash">Cash</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="transaction_id" class="form-label">Transaction ID</label>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_no" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="receipt_file" class="form-label">Payment Receipt (PDF/JPG/PNG)</label>
                        <input type="file" class="form-control" name="receipt_image" id="receipt_file" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Upload receipt button handler
    document.querySelectorAll('.upload-receipt-btn').forEach(button => {
        button.addEventListener('click', function() {
            document.getElementById('app_id').value = this.dataset.invoice;
            document.getElementById('amount_paid').value = this.dataset.amount;
        });
    });

    // Upload receipt form submission
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

    // Billing details form - country/state change handler
    @if ($invoice->payment_status == 'unpaid')
    document.addEventListener('DOMContentLoaded', function() {
        const countrySelect = document.querySelector('select[name="country_id"]');
        const stateSelect = document.getElementById('state_id');

        if (countrySelect && stateSelect) {
            countrySelect.addEventListener('change', function() {
                const countryId = this.value;
                stateSelect.innerHTML = '<option value="">Loading...</option>';
                fetch('/get-states', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            country_id: countryId
                        })
                    })
                    .then(response => response.json())
                    .then(states => {
                        let options = '<option value="">Select State</option>';
                        states.forEach(state => {
                            options += `<option value="${state.id}">${state.name}</option>`;
                        });
                        stateSelect.innerHTML = options;
                    })
                    .catch(() => {
                        stateSelect.innerHTML = '<option value="">Select State</option>';
                    });
            });

            // AJAX submit for billing details form
            const billingForm = document.getElementById('billingDetailsForm');
            if (billingForm) {
                billingForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(billingForm);
                    fetch(billingForm.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (response.ok) {
                                window.location.reload();
                            } else {
                                alert('Failed to update billing details.');
                            }
                        })
                        .catch(() => {
                            alert('Failed to update billing details.');
                        });
                });
            }
        }
    });
    @endif

    // PayPal button integration
    @if ($invoice->payment_status == 'unpaid' && isset($billingDetail->country) && $billingDetail->country && strtolower($billingDetail->country->name) != 'india')
    paypal.Buttons({
        createOrder: function(data, actions) {
            return fetch('/paypal/create-order', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        invoice: '{{ $invoice->invoice_no }}',
                    })
                }).then(response => response.json())
                .then(order => order.id);
        },
        onApprove: function(data, actions) {
            return fetch(`/paypal/capture-order/${data.orderID}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                }).then(response => response.json())
                .then(details => {
                    // Check if response has redirect URL (for startup zone)
                    if (details.redirect) {
                        window.location.href = details.redirect;
                        return;
                    }
                    
                    // If status is COMPLETED then hide the div with id = not-paid and set the id = pay_status with Paid
                    if (details.status === 'COMPLETED') {
                        if (document.getElementById('not-paid')) {
                            document.getElementById('not-paid').style.display = 'none';
                        }
                        if (document.getElementById('pay_status')) {
                            document.getElementById('pay_status').innerHTML = 'Paid';
                        }
                    }

                    alert('Transaction completed by ' + details.payer.name.given_name);
                });
        }
    }).render('#paypal-button-container');
    @endif
</script>
@endpush
