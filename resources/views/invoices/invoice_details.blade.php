@extends('layouts.dashboard')

@section('title', 'Payments')

@section('content')
    <style>
        th {
            text-align: left !important;
            padding-left: 8px !important;
        }
        
        .invoice-details {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .invoice-details h6 {
            color: #495057;
            font-weight: 600;
            border-bottom: 2px solid #007bff;
            padding-bottom: 8px;
        }
        
        .invoice-details .row {
            margin-bottom: 15px;
        }
        
        .invoice-details p {
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        
        .invoice-details .text-end {
            font-family: 'Courier New', monospace;
            font-weight: 500;
        }
        
        .payment-status-badge {
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        
        .amount-highlight {
            background: rgba(0, 123, 255, 0.1);
            padding: 8px 12px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #f5c6cb;
            margin-bottom: 15px;
        }
        
        .payment-info-box {
            background: rgba(0, 123, 255, 0.1);
            border: 1px solid rgba(0, 123, 255, 0.2);
            border-radius: 8px;
            padding: 15px;
            height: 100%;
        }
        
        .payment-info-box p {
            font-size: 0.9rem;
            margin-bottom: 8px;
        }
        
        .payment-info-box strong {
            color: #007bff;
        }
        
        .tds-modal .modal-content {
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(30, 144, 255, 0.12);
            border: none;
            background: #f9fbfd;
        }
        
        .tds-modal .modal-header {
            background: linear-gradient(90deg, #1e90ff 0%, #36d1c4 100%);
            color: #fff;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
            border-bottom: none;
            padding: 1.25rem 1.5rem;
        }
        
        .tds-modal .modal-title {
            font-weight: 700;
            font-size: 1.2rem;
            letter-spacing: 0.5px;
        }
        
        .tds-modal .form-label {
            font-weight: 600;
            color: #1e90ff;
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .tds-modal .form-control {
            border-radius: 8px;
            border: 1.5px solid #bcdffb;
            padding: 10px 14px;
            font-size: 1rem;
            margin-bottom: 1.2rem;
            background: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        
        .tds-modal .form-control:focus {
            border-color: #1e90ff;
            box-shadow: 0 0 0 0.15rem rgba(30, 144, 255, 0.08);
            outline: none;
        }
        
        .tds-modal .btn-primary {
            background: linear-gradient(90deg, #1e90ff 0%, #36d1c4 100%);
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 0.5rem 2rem;
            border-radius: 8px;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(30, 144, 255, 0.08);
            transition: background 0.2s, box-shadow 0.2s;
        }
        
        .tds-modal .btn-primary:hover {
            background: linear-gradient(90deg, #36d1c4 0%, #1e90ff 100%);
            box-shadow: 0 4px 12px rgba(30, 144, 255, 0.15);
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
            let amountPaid = document.getElementById('amountPaid' + paymentId).value.trim();
            
            if (!remarks) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Remarks Required',
                    text: 'Please provide remarks before verifying the payment.',
                });
                return;
            }
            
            if (!amountPaid || parseFloat(amountPaid) <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Amount Required',
                    text: 'Please enter a valid amount paid before verifying the payment.',
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
                            amount_paid: amountPaid,
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
        
        function openTdsModal(invoiceId) {
            document.getElementById('invoiceId').value = invoiceId;
            document.getElementById('tdsAmount').value = '';
            document.getElementById('tdsReason').value = '';
            var tdsModal = new bootstrap.Modal(document.getElementById('tdsModal'));
            tdsModal.show();
        }
        
        function saveTdsAmount() {
            let invoiceId = document.getElementById('invoiceId').value;
            let tdsAmount = document.getElementById('tdsAmount').value.trim();
            let tdsReason = document.getElementById('tdsReason').value.trim();
            
            if (!tdsAmount || parseFloat(tdsAmount) <= 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'TDS Amount Required',
                    text: 'Please enter a valid TDS amount.',
                });
                return;
            }
            
            // TDS reason is optional, so no validation needed
            
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to add TDS amount to this invoice.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, add TDS!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/add-tds-amount',
                        type: 'POST',
                        data: {
                            invoice_id: invoiceId,
                            tds_amount: tdsAmount,
                            tds_reason: tdsReason,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Please wait while we add the TDS amount.',
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
                                text: response.message || 'TDS amount added successfully.',
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            let errorMessage = xhr.responseJSON?.message ||
                                'An error occurred while adding TDS amount.';
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
                    
                    <!-- Invoice Details Section -->
                    <div class="card-body border-bottom">
                        @if(!$invoice)
                            <div class="error-message">
                                <strong>⚠️ Error:</strong> Invoice data not found. Please check the invoice ID or contact support.
                            </div>
                        @else
                            <div class="invoice-details">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">Invoice Details</h6>
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1"><strong>Subtotal:</strong></p>
                                                @if($invoice->surCharge > 0)
                                                    <p class="mb-1"><strong>SurCharge:</strong></p>
                                                @endif
                                                
                                                <p class="mb-1"><strong>GST:</strong></p>
                                                <p class="mb-1"><strong>Processing Charges:</strong></p>
                                                {{--  @if($invoice->type == 'Stall Booking') --}}
                                                <p class="mb-1"><strong>TDS Amount:</strong></p>
                                                {{-- @endif --}}
                                                <p class="mb-1"><strong>Total Final Price:</strong></p>
                                                <p class="mb-1"><strong>Amount Paid:</strong></p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-1 text-end">INR {{ number_format($invoice->price ?? 0, 2) }}</p>
                                                @if($invoice->surCharge > 0)
                                                <p class="mb-1 text-end">
                                                   
                                                        INR {{ number_format($invoice->surCharge, 2) }}
                                                   
                                                </p>
                                                 @endif
                                                <p class="mb-1 text-end">INR {{ number_format($invoice->gst ?? 0, 2) }}</p>
                                                <p class="mb-1 text-end">INR {{ number_format($invoice->processing_charges ?? 0, 2) }}</p>
                                                {{-- @if($invoice->type == 'Stall Booking') --}}
                                                <p class="mb-1 text-end">
                                                    @if($invoice->tds_amount)
                                                        INR {{ number_format($invoice->tds_amount, 2) }}
                                                    @else
                                                        <button class="btn btn-sm btn-outline-primary" onclick="openTdsModal({{ $invoice->id }})">Add TDS</button>
                                                    @endif
                                                </p>
                                                {{-- @endif --}}
                                                <p class="mb-1 text-end text-success fw-bold amount-highlight">INR {{ number_format($invoice->total_final_price ?? 0, 2) }}</p>
                                                <p class="mb-1 text-end text-info fw-bold amount-highlight">INR {{ number_format($invoice->amount_paid ?? 0, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-primary mb-3">Payment Summary</h6>
                                        @php
                                            try {
                                                $totalPaid = $invoice->amount_paid ?? 0;
                                                
                                                $remainingAmount = ($invoice->total_final_price ?? 0) - $totalPaid - $invoice->tds_amount;
                                                
                                                // Get payment status from invoice model
                                                $paymentStatus = $invoice->payment_status ?? 'Unknown';
                                                
                                                // Set status color based on payment status
                                                switch(strtolower($paymentStatus)) {
                                                    case 'paid':
                                                        $statusColor = 'success';
                                                        break;
                                                    case 'partially paid':
                                                        $statusColor = 'warning';
                                                        break;
                                                    case 'unpaid':
                                                        $statusColor = 'danger';
                                                        break;
                                                    case 'pending':
                                                        $statusColor = 'info';
                                                        break;
                                                    default:
                                                        $statusColor = 'secondary';
                                                        break;
                                                }
                                            } catch (Exception $e) {
                                                $totalPaid = 0;
                                                $remainingAmount = $invoice->total_final_price ?? 0;
                                                $paymentStatus = 'Error';
                                                $statusColor = 'secondary';
                                            }
                                        @endphp
                                        <div class="row">
                                            <div class="col-6">
                                                <p class="mb-1"><strong>Total Paid:</strong></p>
                                                <p class="mb-1"><strong>Remaining Amount:</strong></p>
                                                <p class="mb-1"><strong>Payment Status:</strong></p>
                                            </div>
                                            <div class="col-6">
                                                <p class="mb-1 text-end text-success">INR {{ number_format($totalPaid, 2) }}</p>
                                                <p class="mb-1 text-end {{ $remainingAmount > 0 ? 'text-danger' : 'text-success' }}">INR {{ number_format($remainingAmount, 2) }}</p>
                                                <p class="mb-1 text-end">
                                                    <span class="badge bg-{{ $statusColor }} payment-status-badge">{{ $paymentStatus }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
                                        <td>{{ $invoice->currency }} {{ $payment->amount_paid }}</td>
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

                                                    <!-- Amount Paid Input Section -->
                                                    <div class="mt-4 mb-3">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label for="amountPaid{{ $payment->id }}" class="form-label fw-bold text-primary">
                                                                    Amount Paid <span class="text-danger">*</span>
                                                                </label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text">₹</span>
                                                                    <input type="number" 
                                                                           id="amountPaid{{ $payment->id }}" 
                                                                           name="amount_paid" 
                                                                           class="form-control" 
                                                                           placeholder="Enter amount paid"
                                                                           value="{{ $payment->amount_paid ?? '' }}"
                                                                           step="0.01" 
                                                                           min="0" 
                                                                           required>
                                                                </div>
                                                                <small class="text-muted">Enter the actual amount received from the customer</small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="payment-info-box">
                                                                    <p class="mb-1"><strong>Invoice Total:</strong> ₹{{ number_format($invoice->total_final_price ?? 0, 2) }}</p>
                                                                    <p class="mb-1"><strong>Previously Paid:</strong> ₹{{ number_format($invoice->amount_paid ?? 0, 2) }}</p>
                                                                    <p class="mb-0"><strong>Remaining:</strong> ₹{{ number_format(($invoice->total_final_price ?? 0) - ($invoice->amount_paid ?? 0), 2) }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

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

    <!-- TDS Modal -->
    <div class="modal fade tds-modal" id="tdsModal" tabindex="-1" aria-labelledby="tdsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tdsModalLabel">Add TDS Amount</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tdsForm">
                        <input type="hidden" id="invoiceId" name="invoice_id">
                        <div class="mb-3">
                            <label for="tdsAmount" class="form-label">
                                TDS Amount (INR) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" 
                                       id="tdsAmount" 
                                       name="tds_amount" 
                                       class="form-control" 
                                       placeholder="Enter TDS amount"
                                       step="0.01" 
                                       min="0" 
                                       required>
                            </div>
                            <small class="text-muted">Enter the TDS amount to be deducted from the invoice</small>
                        </div>
                        <div class="mb-3">
                            <label for="tdsReason" class="form-label">
                                TDS Reason <span class="text-muted">(Optional)</span>
                            </label>
                            <textarea id="tdsReason" 
                                      name="tds_reason" 
                                      class="form-control" 
                                      placeholder="Enter reason for TDS deduction (optional)"
                                      rows="3"></textarea>
                            {{-- <small class="text-muted">This will be logged but not stored in the database</small> --}}
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="saveTdsAmount()">Save TDS</button>
                </div>
            </div>
        </div>
    </div>

@endsection
