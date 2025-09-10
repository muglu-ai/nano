@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')
    <div class="container-fluid py-2">
        <div class="row min-vh-50">
            <div class="col-lg-8 col-md-10 col-12 m-auto">
                <h3 class="mt-2 mb-5 text-center">Sponsorship Application</h3>
{{--                <p class="lead font-weight-normal opacity-8 mb-0 text-center">A</p>--}}
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n5 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="multisteps-form__progress">
                                <button class="multisteps-form__progress-btn " disabled>
                                    <span>1. Show Profile</span>
                                </button>
                                <button class="multisteps-form__progress-btn " disabled>2. Application Form
                                </button>
                                <button class="multisteps-form__progress-btn " disabled>3. Terms and
                                    Conditions
                                </button>
                                <button class="multisteps-form__progress-btn js-active" disabled>4. Review
                                </button>
                            </div>
                        </div>
                    </div>
                    @php
                        $action = ($application->submission_status == 'in progress') ? 'Edit' : 'View';
                    @endphp
                    <div class="col-12 mt-5">
{{--                        <h5 class="ms-3 ms-lg-5">Application Status: {{ strtoupper($sponsor->status) }}  </h5>--}}
{{--                        <p class="ms-3 ms-lg-5 me-3 me-lg-5">Your application process is <strong>{{ ($sponsor->status) }}</strong>. Please submit the application for the organizer to review. The review process will take a minimum of 7 working days from the date of submission. You can't edit the application once it is submitted to the organizer, so please review it before submission.</p>--}}
                        <div class="table table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Application Number</th>

                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Show Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Applied for</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Approval Status</th>
{{--                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Requested Booth Size</th>--}}
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date of Submission</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($sponsor as $s)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $s->sponsorship_id }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="text-sm text-secondary mb-0">{{ $application->event->event_name }} {{ $application->event->event_year }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="text-sm text-secondary mb-0">{{ $s->sponsorship_item }}</p>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-sm text-secondary mb-0">{{ $s->status }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-sm">{{ $s->submitted_date }}</span>
                                        </td>
                                        <td class="align-middle text-center">
        <span class="text-secondary text-sm">
            <a href="/semicon-2025/onboarding" class="mx-3" data-bs-toggle="tooltip" data-bs-original-title="Edit product">
                <i class="material-symbols-rounded text-secondary position-relative text-lg">drive_file_rename_outline</i>@php echo $action @endphp
            </a>
        </span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if(isset($invoice))
                        {{--                                    @dd($invoice)--}}
                        <hr>

{{--                        <div class="col-12 mt-5">--}}
{{--                            <h5 class="ms-3 ms-lg-5">Approval Status: {{ strtoupper($application->submission_status) }}  </h5>--}}
{{--                            <p class="ms-3 ms-lg-5 me-3 me-lg-5">Your application process is initiated. Please submit the application for the organizer to review. The review process will take a minimum of 7 working days from the date of submission. You can't edit the application once it is submitted to the organizer, so please review it before submission.</p>--}}

{{--                        </div>--}}
                        <div class="table table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    {{--                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">--}}
                                    {{--                                        Application Number</th>--}}

                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Proforma Invoice No</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Date of Approved</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Approved Booth Size</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Billing Company</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Billing Email</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Billing Mobile</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$invoice->id}}</td>
                                    <td>{{$application->approved_date}}</td>
                                    <td>{{$application->interested_sqm}} sqm</td>
                                    <td>{{$application->billingDetail->billing_company}}</td>
                                    <td>{{$application->billingDetail->email}}</td>
                                    <td>{{$application->billingDetail->phone}}</td>

                                    <td>
                                        <form action="{{ route('invoice-details') }}" method="POST" class="mx-3" data-bs-toggle="tooltip" data-bs-original-title="View Proforma Invoice">
                                            @csrf
                                            <input type="hidden" name="application_id" value="{{ $application->id }}">
                                            <button type="submit" class="btn waves-effect waves-light">
                                                <i class="material-symbols-rounded text-secondary position-relative text-lg">visibility</i>
                                                @php echo $action @endphp
                                            </button>
                                        </form>                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>







                    @endif
                    <div class="col-12 text-center">
                        @if ($application->submission_status == 'in progress')
                            <form action="{{ route('application.final') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary mt-3">SUBMIT</button>
                            </form>
                            <p class="mt-3 mb-3">(Once the application is submitted it cannot be deleted or edited)</p>
                        @endif
{{--                        <a href="{{ route('terms') }}" class="btn btn-link">VIEW TERMS & CONDITION</a>--}}
                    </div>



                </div>
            </div>
        </div>
    </div>
@endsection
