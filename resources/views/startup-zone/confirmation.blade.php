@extends('layouts.startup-zone')

@section('title', 'Registration Confirmation - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- Success Message --}}
            <div class="card shadow-sm mb-4 border-success">
                <div class="card-header bg-success text-white text-center">
                    <h3 class="mb-0"><i class="fas fa-check-circle"></i> Registration Successful!</h3>
                </div>
                <div class="card-body text-center">
                    <p class="lead">Thank you for registering for the Startup Zone at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}!</p>
                    <p class="alert alert-info">
                        <strong>Application ID:</strong> {{ $application->application_id }}<br>
                        <strong>Status:</strong> 
                        @if($invoice->payment_status === 'paid')
                            <span class="badge bg-success">Payment Completed</span>
                        @else
                            <span class="badge bg-warning">Payment Pending</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Application Details --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Application Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Company Name:</strong><br>
                            {{ $application->company_name }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Application ID:</strong><br>
                            {{ $application->application_id }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Company Email:</strong><br>
                            {{ $application->company_email }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Submission Date:</strong><br>
                            {{ $application->created_at->format('d M Y, h:i A') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Person --}}
            @if($contact)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Contact Person</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Name:</strong><br>
                            {{ $contact->salutation ?? '' }} {{ $contact->first_name }} {{ $contact->last_name }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Designation:</strong><br>
                            {{ $contact->job_title ?? 'N/A' }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Email:</strong><br>
                            {{ $contact->email }}
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Mobile:</strong><br>
                            {{ $contact->contact_number }}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Payment Details --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Payment Details</h4>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <td><strong>Invoice Number:</strong></td>
                            <td>{{ $invoice->invoice_no ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Base Price:</strong></td>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->price, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>GST (18%):</strong></td>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->gst, 2) }}</td>
                        </tr>
                        <tr>
                            <td><strong>Processing Charges:</strong></td>
                            <td class="text-end">{{ $invoice->currency }} {{ number_format($invoice->processing_charges, 2) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td><strong>Total Amount:</strong></td>
                            <td class="text-end"><strong>{{ $invoice->currency }} {{ number_format($invoice->total_final_price, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <td><strong>Payment Status:</strong></td>
                            <td>
                                @if($invoice->payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($invoice->payment_status === 'partial')
                                    <span class="badge bg-warning">Partial Payment</span>
                                @else
                                    <span class="badge bg-danger">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Next Steps --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0"><i class="fas fa-info-circle"></i> Next Steps</h4>
                </div>
                <div class="card-body">
                    @if($invoice->payment_status === 'paid')
                        <div class="alert alert-success">
                            <strong>Payment Completed!</strong> Your registration is confirmed. You will receive a confirmation email shortly.
                        </div>
                        <ul>
                            <li>Check your email for the confirmation and invoice</li>
                            <li>You will receive further instructions via email</li>
                            <li>For any queries, please contact the event organizers</li>
                        </ul>
                    @else
                        <div class="alert alert-warning">
                            <strong>Payment Pending:</strong> Please complete the payment to confirm your registration.
                        </div>
                        <a href="{{ route('startup-zone.payment', $application->application_id) }}" class="btn btn-primary">
                            Complete Payment <i class="fas fa-credit-card"></i>
                        </a>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="text-center">
                <a href="{{ route('startup-zone.register') }}" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                @if($invoice->payment_status !== 'paid')
                <a href="{{ route('startup-zone.payment', $application->application_id) }}" class="btn btn-success">
                    <i class="fas fa-credit-card"></i> Make Payment
                </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
