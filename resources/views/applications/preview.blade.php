@extends('layouts.application')

@section('title', 'Onboarding Form')
@section('content')
    <style>
        .application-status.initiated {
            color: blue;
        }

        .application-status.approved {
            color: green;
        }

        .application-status.pending {
            color: orange;
        }

        .application-status.submitted {
            color: orange;
        }

        .application-status.rejected {
            color: red;
        }

        .strong {
            font-weight: bold;
        }
    </style>
    <main class="mn-inner2">
        <div class="row">
            {{--            <div class="col s12">--}}
            {{--                <div class="page-title">@yield('title')</div>--}}
            {{--            </div>--}}
            <div class="col s12 m12 l12">
                <div class="card">
                    <div class="card-content">

                        <div class="container">
                            <div class="row">
                                <div class="col s12">
                                    <ul class="tabs">
                                        <li class="tab col s3"><a href="#"> Personal Info</a></li>
                                        <li class="tab col s3"><a href="#"> Product Info </a></li>
                                        <li class="tab col s3"><a href="#"> Terms
                                                and Conditions </a></li>
                                        <li class="tab col s3"><a href="#" class="active waves-effect waves-teal">
                                                Review</a></li>
                                    </ul>
                                </div>
                            </div>
                            @php
                                $action = ($application->submission_status == 'in progress') ? 'Edit' : 'View';
                            @endphp
                            <div class="row">
                                <div class="col s12">
                                    <h5>Application Status: <span
                                            class="application-status {{ strtolower($application->submission_status) }}">{{ strtoupper($application->submission_status) }}</span>
                                    </h5>
                                    <p>Your application process is <strong>initiated</strong>. Please submit the
                                        application for the organizer to review. The review process will take a minimum
                                        of <strong> 7 working days</strong> from the date of submission. You can't edit
                                        the application once it is submitted to the organizer, so please review it
                                        before submission.</p>
                                    <div class="table-responsive">
                                        <table class="table responsive-table ">
                                            <thead>
                                            <tr>
                                                <th>Application Number</th>
                                                <th>Show Name</th>
                                                <th>Format</th>
                                                <th>Region</th>
{{--                                                /**--}}
{{--                                                * ToDo--}}
{{--                                                * Add a new column to display the approved booth size--}}
{{--                                                **/--}}
                                                <th>Requested  Booth Size</th>
                                                <th>Date of Submission</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>{{$application->application_id}}</td>
                                                <td>{{$application->event->event_name}} {{$application->event->event_year}} </td>
                                                <td>Onsite</td>
                                                <td>{{$application->region}}</td>
                                                <td>{{$application->interested_sqm}} sqm</td>
                                                <td>{{$application->submission_date}}</td>
                                                <td>

                                                    <a href="{{route('application.exhibitor')}}">@php echo $action @endphp</a>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    @if(isset($invoice))
{{--                                    @dd($invoice)--}}
                                    <hr>
                                    <div class="row">
                                        <div class="col s12">
                                            <h5>Approval Details:
                                                <span
                                                    class="application-status {{ strtolower($application->submission_status) }}">
                                                    {{ strtoupper($application->submission_status) }}
                                                </span>
                                            </h5>

                                            <div class="table-responsive">
                                                <table class="table responsive-table ">
                                                    <thead>
                                                    <tr>
                                                        <th>Proforma Invoice No</th>
                                                        <th>Date of Approved</th>
                                                        <th>Approved Booth Size</th>
                                                        <th>Billing Company</th>
                                                        <th>Billing Email</th>
                                                        <th>Billing Mobile</th>
                                                        <th>Proforma Invoice</th>
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
                                                            <form action="{{ route('invoice-details') }}" method="POST">
                                                                @csrf
                                                                <input type="hidden" name="application_id" value="{{ $application->id }}">
                                                                <button type="submit" class="btn waves-effect waves-light">View</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                    </div>
                                    @endif


                                    <div class="col s12 center-align">
                                        @if ($application->submission_status == 'in progress')
                                            <form action="{{ route('application.final') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn waves-effect waves-light"
                                                        style="margin-top: 20px;">SUBMIT
                                                </button>
                                            </form>
                                            <p style="margin-top: 20px; margin-bottom: 20px;">(Once the application is
                                                submitted it cannot be deleted or edited)</p>
                                        @endif
                                        <a href="{{ route('terms') }}" class="btn-flat">VIEW TERMS & CONDITION</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </main>

    <script>
        document.querySelector('form').addEventListener('submit', function (event) {
            if (!document.getElementById('terms_accepted').checked) {
                event.preventDefault();
                alert('Please acknowledge that you have read the terms and conditions.');
            }
        });
    </script>
@endsection
