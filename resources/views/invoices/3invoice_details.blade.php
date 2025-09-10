@extends('layouts.dashboard')

@section('title', 'Payments')

@section('content')
    <style>
        th {
            text-align: left !important;
            padding-left: 8px !important;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF Token Meta -->
    <script>
        function openModal(paymentId) {
            var modal = new bootstrap.Modal(document.getElementById('receiptModal' + paymentId));
            modal.show();
        }

        function verifyPayment(paymentId, status, user) {
            let remarks = document.getElementById('remarks' + paymentId).value.trim();
            if (!remarks) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Remarks Required',
                    text: 'Please provide remarks before verifying the payment.',
                });
                return;
            }
            // Surcharge slab validation (if exists)
            let slabSelect = document.getElementById('surchargeSlab' + paymentId);
            let selectedSlab = null;
            let originalSurcharge = null;
            let reason = null;
            if (slabSelect) {
                selectedSlab = slabSelect.value;
                originalSurcharge = slabSelect.getAttribute('data-original') || null;
                if (!selectedSlab) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Select Surcharge',
                        text: 'Please select a surcharge percentage.',
                    });
                    return;
                }
                if (parseInt(selectedSlab) !== parseInt({{ $surcharge }})) {
                    reason = document.getElementById('surchargeChangeReason' + paymentId).value.trim();
                    if (!reason) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Reason Required',
                            text: 'Please provide a reason for changing the surcharge.',
                        });
                        return;
                    }
                }
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
                    $.ajax({
                        url: '/verify-payment',
                        type: 'POST',
                        data: {
                            payment_id: paymentId,
                            status: status,
                            remarks: remarks,
                            user: user,
                            surcharge_slab: selectedSlab,
                            surcharge_change_reason: reason,
                            _token: $('meta[name="csrf-token"]').attr('content')
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
                                location.reload();
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

    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Payments For {{ $applications->billingDetail->billing_company }}</h6>

                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead class="table-dark">
                                <tr>
                                    <th class="text-wrap">Payment Method</th>
                                    <th class="text-wrap">Total Amount</th>
                                    <th class="text-wrap">Amount Paid</th>
                                    <th class="text-wrap">Transaction ID</th>
                                    <th class="text-wrap">Payment Date</th>
                                    {{--                                <th class="text-wrap">Currency</th> --}}
                                    <th class="text-wrap">Receipt Image</th>
                                    <th class="text-wrap">Verification Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td>{{ $payment->payment_method }}</td>
                                        <td>INR {{ $invoice->amount }}</td>
                                        <td>INR {{ $payment->amount_paid }}</td>
                                        <td>{{ $payment->transaction_id }}</td>
                                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
                                        {{--                                    <td>{{ $payment->currency }}</td> --}}
                                        <td>
                                            @if ($payment->receipt_image)
                                                <button class="btn btn-info"
                                                    onclick="openModal({{ $payment->id }})">View</button>
                                            @else
                                                <span class="text-muted">No Image</span>
                                            @endif
                                        </td>
                                        <td>{{ ucfirst($payment->verification_status) }}</td>
                                    </tr>



                                    <!-- Modal for each payment -->
                                    <div class="modal fade" id="receiptModal{{ $payment->id }}" tabindex="-1"
                                        aria-labelledby="receiptModalLabel{{ $payment->id }}" aria-hidden="true">
                                        <style>
                                            /* Modal content styling */
                                            .modal-content {
                                                border-radius: 16px;
                                                box-shadow: 0 8px 32px rgba(30, 144, 255, 0.12);
                                                border: none;
                                                background: #f9fbfd;
                                            }

                                            /* Modal header */
                                            .modal-header {
                                                background: linear-gradient(90deg, #1e90ff 0%, #36d1c4 100%);
                                                color: #fff;
                                                border-top-left-radius: 16px;
                                                border-top-right-radius: 16px;
                                                border-bottom: none;
                                                padding: 1.25rem 1.5rem;
                                            }

                                            .modal-title {
                                                font-weight: 700;
                                                font-size: 1.2rem;
                                                letter-spacing: 0.5px;
                                            }

                                            /* Form labels */
                                            .form-label,
                                            .modal-body label {
                                                font-weight: 600;
                                                color: #1e90ff;
                                                margin-bottom: 0.5rem;
                                                display: block;
                                            }

                                            /* Surcharge removal section */
                                            .form-label.text-danger {
                                                color: #e74c3c !important;
                                            }

                                            #reasonField{{ $payment->id }} label {
                                                color: #e74c3c !important;
                                            }

                                            /* Radio buttons */
                                            .modal-body input[type="radio"] {
                                                accent-color: #1e90ff;
                                                margin-right: 4px;
                                            }

                                            /* Textareas and inputs */
                                            .modal-body textarea,
                                            .modal-body input[type="text"],
                                            .modal-body input[type="email"] {
                                                border-radius: 8px;
                                                border: 1.5px solid #bcdffb;
                                                padding: 10px 14px;
                                                font-size: 1rem;
                                                margin-bottom: 1.2rem;
                                                background: #fff;
                                                transition: border-color 0.2s, box-shadow 0.2s;
                                            }

                                            .modal-body textarea:focus,
                                            .modal-body input[type="text"]:focus,
                                            .modal-body input[type="email"]:focus {
                                                border-color: #1e90ff;
                                                box-shadow: 0 0 0 0.15rem rgba(30, 144, 255, 0.08);
                                                outline: none;
                                            }

                                            /* Buttons */
                                            .modal-body .btn-success,
                                            .modal-body .btn-danger {
                                                font-weight: 600;
                                                padding: 0.5rem 2rem;
                                                border-radius: 8px;
                                                font-size: 1rem;
                                                margin: 0 0.5rem;
                                                box-shadow: 0 2px 8px rgba(30, 144, 255, 0.08);
                                                transition: background 0.2s, box-shadow 0.2s;
                                            }

                                            .modal-body .btn-success {
                                                background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
                                                border: none;
                                                color: #fff;
                                            }

                                            .modal-body .btn-success:hover {
                                                background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%);
                                            }

                                            .modal-body .btn-danger {
                                                background: linear-gradient(90deg, #ff416c 0%, #ff4b2b 100%);
                                                border: none;
                                                color: #fff;
                                            }

                                            .modal-body .btn-danger:hover {
                                                background: linear-gradient(90deg, #ff4b2b 0%, #ff416c 100%);
                                            }

                                            /* Responsive adjustments */
                                            @media (max-width: 576px) {
                                                .modal-dialog {
                                                    margin: 1rem;
                                                }

                                                .modal-body {
                                                    padding: 1rem;
                                                }
                                            }
                                        </style>
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="receiptModalLabel{{ $payment->id }}">
                                                        Receipt Image</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    @if ($payment->receipt_image)
                                                        @php
                                                            $extension = strtolower(
                                                                pathinfo($payment->receipt_image, PATHINFO_EXTENSION),
                                                            );
                                                        @endphp
                                                        @if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                                                            <img src="{{ rtrim(config('app.url'), '/') . '/storage/' . ltrim($payment->receipt_image, '/') }}"
                                                                alt="Receipt Image" class="img-fluid">
                                                        @elseif($extension === 'pdf')
                                                            <embed
                                                                src="{{ rtrim(config('app.url'), '/') . '/storage/' . ltrim($payment->receipt_image, '/') }}"
                                                                type="application/pdf" width="100%" height="500px" />
                                                            <p>
                                                                <a href="{{ rtrim(config('app.url'), '/') . '/storage/' . ltrim($payment->receipt_image, '/') }}"
                                                                    target="_blank" class="btn btn-primary mt-2">Open PDF in
                                                                    new tab</a>
                                                            </p>
                                                        @else
                                                            <p class="text-muted">Unsupported file type</p>
                                                        @endif
                                                    @else
                                                        <p class="text-muted">No receipt available</p>
                                                    @endif

                                                    @if($payment->verification_status === 'Pending')
                                                

                                                    @if (isset($invoiceType) && $invoiceType === 'extra_requirement')
                                                        <div class="mb-2">
                                                            <span class="fw-bold text-info">Current Surcharge:
                                                                {{ $surcharge }}%</span>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-danger">
                                                                Select Surcharge Percentage <span
                                                                    class="text-danger">*</span>
                                                            </label>
                                                            <select id="surchargeSlab{{ $payment->id }}"
                                                                name="surcharge_slab" class="form-select"
                                                                onchange="toggleSurchargeReason({{ $payment->id }}, {{ $surcharge }})"
                                                                required>
                                                                <option value="">-- Select Slab --</option>
                                                                <option value="0"
                                                                    {{ $surcharge == 0 ? 'selected' : '' }}>0%</option>
                                                                <option value="30"
                                                                    {{ $surcharge == 30 ? 'selected' : '' }}>30%</option>
                                                                <option value="50"
                                                                    {{ $surcharge == 50 ? 'selected' : '' }}>50%</option>
                                                                <option value="75"
                                                                    {{ $surcharge == 75 ? 'selected' : '' }}>75%</option>
                                                            </select>
                                                        </div>
                                                        <div id="surchargeReasonField{{ $payment->id }}"
                                                            style="display:none;">
                                                            <label for="surchargeChangeReason{{ $payment->id }}"
                                                                class="form-label fw-bold text-danger">
                                                                Please provide a reason for changing the surcharge <span
                                                                    class="text-danger">*</span>
                                                            </label>
                                                            <textarea id="surchargeChangeReason{{ $payment->id }}" name="surcharge_change_reason" class="form-control"></textarea>
                                                        </div>
                                                        <script>
                                                            function toggleSurchargeReason(paymentId, originalSurcharge) {
                                                                let slab = document.getElementById('surchargeSlab' + paymentId).value;
                                                                let reasonBox = document.getElementById('surchargeReasonField' + paymentId);
                                                                let reasonInput = document.getElementById('surchargeChangeReason' + paymentId);
                                                                if (slab !== '' && parseInt(slab) !== parseInt(originalSurcharge)) {
                                                                    reasonBox.style.display = '';
                                                                    reasonInput.setAttribute('required', true);
                                                                } else {
                                                                    reasonBox.style.display = 'none';
                                                                    reasonInput.removeAttribute('required');
                                                                    reasonInput.value = '';
                                                                }
                                                            }
                                                        </script>
                                                    @endif

                                                    <div class="mt-3 input-group input-group-dynamic">
                                                        <textarea id="remarks{{ $payment->id }}" name="remarks" class="form-control"
                                                            placeholder="Enter remarks for payment {{ $payment->id }}" required></textarea>
                                                    </div>
                                                    <div class="mt-3 text-center">
                                                        <button type="button" class="btn btn-success"
                                                            onclick="verifyPayment({{ $payment->id }}, 'verified', '{{ Auth::user()->name }}')">Verify</button>
                                                        <button type="button" class="btn btn-danger"
                                                            onclick="verifyPayment({{ $payment->id }}, 'rejected','{{ Auth::user()->name }}')">Reject</button>
                                                    </div>
                                                    @else
                                                        <div class="alert alert-info border border-danger shadow-sm rounded-3 px-3 py-2" style="background: linear-gradient(90deg, #f8fafc 0%, #e0f7fa 100%); color: #0d47a1;">
                                                            <i class="fas fa-info-circle text-danger me-2"></i>
                                                            <strong>Payment Status:</strong> This payment has already been verified.<br>
                                                            <span class="fw-semibold">Remark:</span> {{ $payment->remarks }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
