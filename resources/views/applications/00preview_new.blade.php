@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')
    <style>
        @media (min-width: 500px) {
            .progress-bar2 {
                display: none !important;
            }
        }
    </style>
    <div class="container-fluid py-2">
        <div class="row min-vh-80 mt-5">
            <div class="col-lg-10 col-md-10 col-12 m-auto">
                {{--                <h3 class="mt-3 mb-0 text-center">Add new Product</h3>--}}
                {{--                <p class="lead font-weight-normal opacity-8 mb-7 text-center">This information will let us know more--}}
                {{--                    about you.</p>--}}
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n5 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="multisteps-form__progress">
                                <button class="multisteps-form__progress-btn js-active " disabled>
                                    <span>1. Show Profile</span>
                                </button>
                                <button class="multisteps-form__progress-btn js-active " disabled>2. Application Form
                                </button>
                                <button class="multisteps-form__progress-btn js-active " disabled>3. Terms and
                                    Conditions
                                </button>
                                <button class="multisteps-form__progress-btn js-active" disabled>4. Review
                                </button>
                            </div>
                            <small class="progress-bar2 d-block text-center text-white">4. Review</small>
                        </div>
                    </div>
                    @php
                        $action = ($application->submission_status == 'in progress') ? 'Edit' : 'View';
                    @endphp
                    <div class="col-12 mt-5">
                        <h5 class="ms-3 ms-lg-5">Application
                            Status: {{ strtoupper($application->submission_status) }}  </h5>
                        <p class="ms-3 ms-lg-5 me-3 me-lg-5">Your application process is
                            <strong>{{ ($application->submission_status) }}</strong>.
                            @if($application->submission_status != 'rejected')
                            Please submit the application for
                            the organizer to review. The review process will take a minimum of 7 working days from the
                            date of submission. You can't edit the application once it is submitted to the organizer, so
                            please review it before submission.
                            @else
                                <br>
                                <strong>Reason for rejection: </strong> {{ $application->rejection_reason }}
                            @endif</p>
                        <div class="table table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 left-align">
                                        Application Number
                                    </th>

                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Show Name
                                    </th>
                                    {{--                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Format</th>--}}
                                    <th class="ps-2 text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Region
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Requested Booth Size
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Date of Submission
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center ">
                                                <h6 class="mb-0 text-sm ">{{$application->application_id}}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-sm text-secondary mb-0">{{$application->event->event_name}} {{$application->event->event_year}}</p>
                                    </td>
                                    {{--                                    <td>--}}
                                    {{--                                        <div class="rating ms-lg-n4">--}}
                                    {{--                                            <p class="text-sm text-secondary mb-0">Onsite</p>--}}

                                    {{--                                        </div>--}}
                                    {{--                                    </td>--}}
                                    <td class="align-middle text-sm">
                                        <p class="text-sm text-secondary mb-0">{{$application->region}}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-sm">{{$application->interested_sqm}} sqm</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-sm">{{$application->submission_date}}</span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-sm">

                                             <a href="/semicon-2025/onboarding" class="mx-3" data-bs-toggle="tooltip"
                                                data-bs-original-title="Edit product"> <i
                                                     class="material-symbols-rounded text-secondary position-relative text-lg">drive_file_rename_outline</i>@php echo $action @endphp</a>

                                        </span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    @if(isset($invoice))
                        <hr>
                        <div class="col-12 mt-2">
                            <h5 class="ms-3 ms-lg-5  text-success">Approval Status: {{ strtoupper($application->submission_status) }}  </h5>
                            <p class="ms-3 ms-lg-5 me-3 me-lg-5">Your application process is {{ ucfirst($application->submission_status) }}.
                                <br>
                                We are pleased to inform you that your application has been approved! Our finance team will be sending you a proforma invoice shortly.

                                Please note that we require a 100% upfront payment for exhibitor onboarding. Once you receive the invoice, kindly settle the payment and upload the receipt on this portal for verification.

                                We look forward to completing the onboarding process once the payment is received.</p>
{{--                                Please view the proforma invoice <a href="{{ route('invoice-details', ['application_id' => $application->application_id]) }}">Clicking here</a> and make a payment of at least 40% or the full amount. After completing the payment, upload the receipt for verification.</p>--}}
                        </div>
                        <div class="table table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Date of Approved
                                    </th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Approved Booth Size
                                    </th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Allocated Booth Number
                                    </th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Billing Company
                                    </th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Billing Email
                                    </th>
                                    <th class="text-left text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Billing Mobile
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
{{--                                    <td class="text-center">{{$invoice->id}}</td>--}}
                                    <td  class="text-center">{{$application->approved_date}}</td>
                                    <td class="text-center">{{$application->allocated_sqm}} sqm</td>
                                    <td class="text-center">{{$application->stallNumber}} </td>
                                    <td class="text-center">{{$application->billingDetail->billing_company}}</td>
                                    <td class="text-center">{{$application->billingDetail->email}}</td>
                                    <td class="text-center">{{$application->billingDetail->phone}}</td>

                                    <td>
                                        <form
                                            action="{{ route('invoice-details', ['application_id' => $application->application_id]) }}"
                                            method="POST" class="mx-3" data-bs-toggle="tooltip"
                                            data-bs-original-title="View">
                                            @csrf
                                            <input type="hidden" name="application_id" value="{{ $application->id }}">
                                            <button type="submit" class="btn waves-effect waves-light">
                                                <i class="material-symbols-rounded text-secondary position-relative text-lg">visibility</i>
                                                @php echo $action @endphp
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>

                    @endif
                    <hr class="dark horizontal my-0">
{{--                    {{var_export($payments)}}--}}
                    @if(isset($paymentzzs) && !empty($paysments))
                        {{--                                    @dd($invoice)--}}
                        <hr>

                        <div class="col-12 mt-2">
                            <h5 class="ms-3 ms-lg-5  text-primary">Payment Verification Status</h5>
                        </div>
                        <div class="table table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Date of Payment
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Payment Method
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Transaction ID
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Amount
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                       Status
                                    </th>
{{--                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">--}}
{{--                                        Action--}}
{{--                                    </th>--}}
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($payments as $payment)
                                <tr>
                                    {{--                                    <td class="text-center">{{$invoice->id}}</td>--}}
                                    <td class="text-center">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y') }}</td>
                                    <td class="text-center">{{$payment->payment_method}}</td>
                                    <td class="text-center">{{$payment->transaction_id}}</td>
                                    <td class="text-center">{{$payment->amount_paid}}</td>
                                    <td class="text-center">{{$payment->verification_status}}</td>

                                    <td>
                                        <form
                                            action="{{ route('invoice-details', ['application_id' => $application->application_id]) }}"
                                            method="POST" class="mx-3" data-bs-toggle="tooltip"
                                            data-bs-original-title="View">
                                            @csrf
                                            <input type="hidden" name="application_id" value="{{ $application->id }}">
{{--                                            <button type="submit" class="btn waves-effect waves-light">--}}
{{--                                                <i class="material-symbols-rounded text-secondary position-relative text-lg">visibility</i>--}}
{{--                                                @php echo $action @endphp--}}
{{--                                            </button>--}}
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif


                    <div class="row">
                        <div class="col-12 text-center">
                            @if ($application->submission_status == 'in progress')
                                <form action="{{ route('application.final') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary mt-3">SUBMIT</button>
                                </form>
                                <p class="mt-3 mb-3">(Once the application is submitted it cannot be deleted or edited)</p>
                            @endif
                            <div class="align-content-center">
                                <br>
                            <p class="text-center">Please check your inbox and spam folder for follow-up emails. Your application will be reviewed within 7 days.</p>
                            <a href="{{ route('terms') }}" class="btn btn-link">VIEW TERMS & CONDITION</a>
                            </div>
                        </div>
                        <div class="col-6 text-center">


                            @if (isset($application->invoice) && $application->invoice->payment_status == 'partial' && $application->invoice->amount_paid >= 0.4 * $application->invoice->amount)
                                <a href="{{ route('user.dashboard') }}" class="btn btn-primary mt-3">Go to Dashboard</a>
                            @elseif (isset($application->invoice) && $application->invoice->payment_status == 'paid')
                                <a href="{{ route('user.dashboard') }}" class="btn btn-primary mt-3">Go to Dashboard</a>
                            @endif
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>
@endsection
