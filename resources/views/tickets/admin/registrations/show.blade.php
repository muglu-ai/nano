@extends('layouts.dashboard')
@section('title', 'Ticket Registration Details')
@section('content')

    <style>
        .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #6A1B9A;
            color: white;
            border-bottom: none;
        }
        
        .card-header h5 {
            color: white;
            font-weight: 600;
            margin: 0;
        }
        
        .table th {
            background-color: #f8f9fc;
            font-weight: 600;
            width: 40%;
        }
        
        .table-bordered {
            border: 1px solid #dee2e6 !important;
        }
        
        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6 !important;
            padding: 0.75rem;
            vertical-align: middle;
        }
        
        .table-bordered thead th {
            border-bottom-width: 2px !important;
            border: 1px solid #dee2e6 !important;
        }
        
        .table-bordered tbody tr td {
            border: 1px solid #dee2e6 !important;
        }
        
        .table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .back-btn {
            background-color: #6A1B9A;
            border-color: #6A1B9A;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 0.35rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .back-btn:hover {
            background-color: #4A0072;
            border-color: #4A0072;
            color: white;
            text-decoration: none;
        }
        
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            font-weight: 600;
        }
        
        .badge-status-pending {
            background-color: #ffc107;
            color: #000;
        }
        
        .badge-status-paid {
            background-color: #28a745;
            color: white;
        }
        
        .badge-status-cancelled {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-status-refunded {
            background-color: #6c757d;
            color: white;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <a href="{{ route('admin.tickets.registrations') }}" class="back-btn">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <div class="action-buttons">
                        <a href="{{ route('admin.tickets.registrations.edit', $registration->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#resendEmailModal">
                            <i class="fas fa-envelope me-2"></i>Resend Email
                        </button>
                        @if($registration->order)
                            <a href="{{ route('email-preview.ticket-registration', $registration->order->order_no) }}" target="_blank" class="btn btn-secondary">
                                <i class="fas fa-eye me-2"></i>Preview Email
                            </a>
                        @endif
                    </div>
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @php
                    $order = $registration->order;
                    $currency = $registration->nationality === 'International' ? 'USD' : 'INR';
                    $currencySymbol = $currency === 'USD' ? '$' : '₹';
                @endphp

                <!-- Order Summary -->
                @if($order)
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-receipt me-2"></i>Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Order Number (TIN):</th>
                                        <td><strong>{{ $order->order_no }}</strong></td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if($order->status === 'pending')
                                                <span class="badge-status badge-status-pending">Pending</span>
                                            @elseif($order->status === 'paid')
                                                <span class="badge-status badge-status-paid">Paid</span>
                                            @elseif($order->status === 'cancelled')
                                                <span class="badge-status badge-status-cancelled">Cancelled</span>
                                            @elseif($order->status === 'refunded')
                                                <span class="badge-status badge-status-refunded">Refunded</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Order Date:</th>
                                        <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                    @if($payment)
                                    <tr>
                                        <th>Payment Gateway:</th>
                                        <td>
                                            @if($payment->gateway_name === 'ccavenue')
                                                <span class="badge bg-primary">CCAvenue</span>
                                            @elseif($payment->gateway_name === 'paypal')
                                                <span class="badge bg-info">PayPal</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($payment->gateway_name) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Transaction ID:</th>
                                        <td>{{ $payment->gateway_txn_id ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Payment Date:</th>
                                        <td>{{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : '-' }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Subtotal:</th>
                                        <td class="text-end">{{ $currencySymbol }}{{ number_format($order->subtotal, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>GST:</th>
                                        <td class="text-end">{{ $currencySymbol }}{{ number_format($order->gst_total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <th>Processing Charge:</th>
                                        <td class="text-end">{{ $currencySymbol }}{{ number_format($order->processing_charge_total, 2) }}</td>
                                    </tr>
                                    @if($order->discount_amount > 0)
                                    <tr>
                                        <th>Discount:</th>
                                        <td class="text-end text-success">-{{ $currencySymbol }}{{ number_format($order->discount_amount, 2) }}</td>
                                    </tr>
                                    @endif
                                    <tr class="table-primary">
                                        <th><strong>Total:</strong></th>
                                        <td class="text-end"><strong>{{ $currencySymbol }}{{ number_format($order->total, 2) }}</strong></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Company Information -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-building me-2"></i>Company Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Company Name:</th>
                                <td>{{ $registration->company_name }}</td>
                            </tr>
                            <tr>
                                <th>Company Country:</th>
                                <td>{{ $registration->company_country ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Company State:</th>
                                <td>{{ $registration->company_state ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Company City:</th>
                                <td>{{ $registration->company_city ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Company Phone:</th>
                                <td>{{ $registration->company_phone ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>Nationality:</th>
                                <td>
                                    @if($registration->nationality === 'Indian')
                                        <span class="badge bg-warning text-dark">Indian</span>
                                    @else
                                        <span class="badge bg-primary">International</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>GST Required:</th>
                                <td>
                                    @if($registration->gst_required)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </td>
                            </tr>
                            @if($registration->gst_required)
                                @if($registration->gstin)
                                <tr>
                                    <th>GSTIN:</th>
                                    <td>{{ $registration->gstin }}</td>
                                </tr>
                                <tr>
                                    <th>GST Legal Name:</th>
                                    <td>{{ $registration->gst_legal_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>GST Address:</th>
                                    <td>{{ $registration->gst_address ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>GST State:</th>
                                    <td>{{ $registration->gst_state ?? '-' }}</td>
                                </tr>
                                @else
                                <tr>
                                    <th>GST Details:</th>
                                    <td><span class="text-muted">GST required but details not provided</span></td>
                                </tr>
                                @endif
                            @else
                            <tr>
                                <th>GST Details:</th>
                                <td><span class="text-muted">GST is not required</span></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-user me-2"></i>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        @if($registration->contact)
                        <table class="table table-bordered">
                            <tr>
                                <th>Name:</th>
                                <td>{{ $registration->contact->name }}</td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td>{{ $registration->contact->email }}</td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td>{{ $registration->contact->phone ?? '-' }}</td>
                            </tr>
                        </table>
                        @else
                        <p class="text-muted">No contact information available.</p>
                        @endif
                    </div>
                </div>

                <!-- Order Items -->
                @if($order && $order->items->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-ticket-alt me-2"></i>Order Items</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Ticket Type</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-end">GST</th>
                                    <th class="text-end">Processing Charge</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>{{ $item->ticketType ? $item->ticketType->name : 'N/A' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($item->subtotal, 2) }}</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($item->gst_amount, 2) }}</td>
                                    <td class="text-end">{{ $currencySymbol }}{{ number_format($item->processing_charge_amount, 2) }}</td>
                                    <td class="text-end"><strong>{{ $currencySymbol }}{{ number_format($item->total, 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Delegates -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-users me-2"></i>Delegates ({{ $registration->delegates->count() }})</h5>
                    </div>
                    <div class="card-body">
                        @if($registration->delegates->count() > 0)
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Designation</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($registration->delegates as $index => $delegate)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ trim("{$delegate->salutation} {$delegate->first_name} {$delegate->last_name}") }}</td>
                                    <td>{{ $delegate->email }}</td>
                                    <td>{{ $delegate->phone ?? '-' }}</td>
                                    <td>{{ $delegate->job_title ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <p class="text-muted">No delegates found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resend Email Modal -->
    <div class="modal fade" id="resendEmailModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Resend Email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('admin.tickets.registrations.resend-email', $registration->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Email Type</label>
                            <select name="email_type" class="form-control" required>
                                <option value="auto">Auto (Based on Order Status)</option>
                                <option value="registration">Registration Confirmation</option>
                                <option value="payment">Payment Confirmation</option>
                            </select>
                            <small class="form-text text-muted">
                                Select the type of email to send. Auto will send payment confirmation if order is paid, otherwise registration confirmation.
                            </small>
                        </div>
                        @if($registration->contact)
                        <div class="alert alert-info">
                            <strong>Email will be sent to:</strong> {{ $registration->contact->email }}
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
