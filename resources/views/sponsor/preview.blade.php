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
                                    <span>1. Personal Info</span>
                                </button>
                                <button class="multisteps-form__progress-btn js-active " disabled>2. Product
                                    Info
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
                        $action = 'Edit';
//                        ($sponsorship->status == 'initiated') ? 'Edit' : 'View';
                    @endphp
                    <div class="col-12 mt-5">
                        {{--                        <h5 class="ms-3 ms-lg-5">Sponsorship--}}
                        {{--                            Status:--}}
                        {{--                            --}}{{--                            {{ strtoupper($sponsorship->status) }}--}}
                        {{--                        </h5>--}}
                        <p class="ms-3 ms-lg-5 me-3 me-lg-5">
                            Please submit the Sponsorship application for
                            the organizer to review. The review process will take a minimum of 7 working days from the
                            date of submission. <br>You can't edit the sponsorship application once it is submitted to
                            the organizer, so
                            please review it before submission.</p>
                        <div class="table table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Sponsorship Item Name
                                    </th>

                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Price
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Billing Company
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Billing Email
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Date of Submission
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status
                                    </th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Action
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($sponsorships as $sponsorship)
                                    @php $action = ($sponsorship->status == 'initiated') ? 'Edit' : 'View';
                                    @endphp
                                    <tr class="text-center">
                                        <td class="text-center">
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{$sponsorship->sponsorship_item}}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-sm text-secondary mb-0">{{$application->payment_currency}} {{$sponsorship->price}}</p>
                                        </td>
                                        <td class="align-content-center text-sm">
                                            <p class="text-sm  align-content-center text-secondary mb-0">{{$application->billingDetail->billing_company}}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span
                                                class="text-secondary text-sm">{{$application->billingDetail->email}} </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-sm">{{$sponsorship->submitted_date}}</span>
                                        </td>

                                        <td class="align-middle text-center">
                                            <style>
                                                td .status-submitted {
                                                    color: orange !important;
                                                }
                                            </style>
                                            @php
                                                $statusClasses = [
                                                    'initiated' => 'text-warning',
                                                    'submitted' => 'status-submitted',
                                                    'approved' => 'text-success',
                                                    'rejected' => 'text-danger'
                                                ];
                                                $statusClass = $statusClasses[$sponsorship->status] ?? 'text-secondary';
                                            @endphp
                                            <span class="text-secondary text-sm {{ $statusClass }}">
                                             <strong>{{ ucfirst($sponsorship->status) }}</strong>
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                        <span class="text-secondary text-sm">
                                             @if ($action != 'View')
                                                <form action="{{ route('sponsor.delete') }}" method="POST" class="mx-3"
                                                      data-bs-toggle="tooltip" data-bs-original-title="Delete"
                                                      onsubmit="return confirm('Are you sure you want to delete the sponsorship application?');">
                                                    @csrf
                                                    <input type="hidden" name="sponsor_id"
                                                           value="{{ $sponsorship->id }}">
                                                    <button type="submit" class="btn btn-link p-0">
                                                        <i class="material-symbols-rounded text-secondary position-relative text-lg">delete</i>Delete
                                                    </button>
                                                </form>

                                                <form action="{{ route('sponsor.submit') }}" method="POST" class="mx-3"
                                                      data-bs-toggle="tooltip"
                                                      data-bs-original-title="Submit">
                                                    @csrf
                                                    <input type="hidden" name="sponsor_id"
                                                           value="{{ $sponsorship->id }}">
                                                    <button type="submit" class="btn btn-link p-0">
                                                        <i class="material-symbols-rounded text-secondary position-relative text-lg">send</i>Submit
                                                    </button>
                                                </form>

                                            @endif
                                            {{--                                                 {{ $sponsorship->status }}--}}
                                                <form action="{{ route('sponsor.review') }}" method="GET" class="mx-3"
                                                      data-bs-toggle="tooltip" data-bs-original-title="View">
                                                    <button type="submit" class="btn btn-link p-0">
                                                        <i class="material-symbols-rounded text-secondary position-relative text-lg">visibility</i>View
                                                    </button>
                                                </form>
                                        </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr class="dark horizontal my-0">
                    
                    <hr class="dark horizontal my-0">
                    @if(isset($payments) && $payments != null)
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
                                                data-bs-original-title="View Proforma Invoice">
                                                @csrf
                                                <input type="hidden" name="application_id"
                                                       value="{{ $application->id }}">
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
                    {{--                    <div class="col-12 text-center">--}}
                    {{--                        @if ($sponsorship->status == 'initiated')--}}
                    {{--                            <form action="{{ route('sponsor.submit') }}" method="POST">--}}
                    {{--                                @csrf--}}
                    {{--                                <input type="hidden" name="sponsor_id" value="{{ $sponsorship->id }}">--}}
                    {{--                                <button type="submit" class="btn btn-primary mt-3">SUBMIT</button>--}}
                    {{--                            </form>--}}
                    {{--                            <p class="mt-3 mb-3">(Once the application is submitted it cannot be deleted or edited)</p>--}}
                    {{--                        @endif--}}
                    {{--                        <a href="{{ route('terms') }}" class="btn btn-link">VIEW TERMS & CONDITION</a>--}}
                    {{--                    </div>--}}


                </div>
            </div>
        </div>
    </div>
@endsection
