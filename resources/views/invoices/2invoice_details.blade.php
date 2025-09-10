@extends('layouts.dashboard')

@section('title', 'Payments')

@section('content')
<style>
    th {
    text-align: left !important;
    padding-left:8px !important;
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
                        url: '/verify-payment',
                        type: 'POST',
                        data: {
                            payment_id: paymentId,
                            status: status,
                            remarks: remarks,
                            user: user,
                            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                        },
                        beforeSend: function () {
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
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Payment verified successfully.',
                            }).then(() => {
                                location.reload(); // Reload page to reflect changes
                            });
                        },
                        error: function (xhr) {
                            let errorMessage = xhr.responseJSON?.message || 'An error occurred while verifying the payment.';
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
                        <h6>Payments For {{$applications->billingDetail->billing_company}}</h6>

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
{{--                                <th class="text-wrap">Currency</th>--}}
                                <th class="text-wrap">Receipt Image</th>
                                <th class="text-wrap">Verification Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_method }}</td>
                                    <td>INR {{ $payment->amount }}</td>
                                    <td>INR {{ $payment->amount_paid }}</td>
                                    <td>{{ $payment->transaction_id }}</td>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
{{--                                    <td>{{ $payment->currency }}</td>--}}
                                    <td>
                                        @if($payment->receipt_image)
                                            <button class="btn btn-info" onclick="openModal({{ $payment->id }})">View</button>
                                        @else
                                            <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ ucfirst($payment->verification_status) }}</td>
                                </tr>



                                <!-- Modal for each payment -->
                                <div class="modal fade" id="receiptModal{{ $payment->id }}" tabindex="-1" aria-labelledby="receiptModalLabel{{ $payment->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="receiptModalLabel{{ $payment->id }}">Receipt Image</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                               @if($payment->receipt_image)
                                                    @php
                                                        $extension = strtolower(pathinfo($payment->receipt_image, PATHINFO_EXTENSION));
                                                    @endphp
                                                    @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']))
                                                        <img src="{{ asset('storage/' . $payment->receipt_image) }}" alt="Receipt Image" class="img-fluid">
                                                    @elseif($extension === 'pdf')
                                                        <embed src="{{ asset('storage/' . $payment->receipt_image) }}" type="application/pdf" width="100%" height="500px" />
                                                        <p>
                                                            <a href="{{ asset('storage/' . $payment->receipt_image) }}" target="_blank" class="btn btn-primary mt-2">Open PDF in new tab</a>
                                                        </p>
                                                    @else
                                                        <p class="text-muted">Unsupported file type</p>
                                                    @endif
                                                @else
                                                    <p class="text-muted">No receipt available</p>
                                                @endif

                                                <div class="mt-3 input-group input-group-dynamic">
                                                   <textarea id="remarks{{ $payment->id }}" name="remarks" class="form-control" placeholder="Enter remarks for payment {{ $payment->id }}" required></textarea>
                                                </div>
                                                    <div class="mt-3 text-center">
                                                        <button type="button" class="btn btn-success" onclick="verifyPayment({{ $payment->id }}, 'verified', '{{ Auth::user()->name }}')">Verify</button>
                                                        <button type="button" class="btn btn-danger" onclick="verifyPayment({{ $payment->id }}, 'rejected','{{ Auth::user()->name }}')">Reject</button>
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

@endsection
