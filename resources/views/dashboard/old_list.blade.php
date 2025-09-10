@extends('layouts.dashboard')
@section('title', ucfirst($slug))
@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">{{$slug}}</h6>
                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                <tr>
                                    <th class="text-uppercase text-center text-secondary text-xs font-weight-bolder opacity-7">Application No</th>
                                    <th class="text-uppercase text-center text-secondary text-xs font-weight-bolder opacity-7 ps-2">Company Name</th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7"><div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">Name</h6>
                                            <p class="text-xs text-secondary mb-0">Email</p>
                                        </div></th>
                                    <th class="text-center text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Mobile Number</th>
                                    <th class="text-secondary opacity-7 text-center">Date of Submission</th>
                                    <th class="text-secondary opacity-7 text-center">Status</th>
                                    <th class="text-secondary opacity-7 text-center">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($applications as $application)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <p class="text-xs font-weight-bold mb-0">{{$application->application_id}}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ ($application->company_name) }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $application->description }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column justify-content-center">
{{--                                                @foreach ($application->eventContacts as $contact)--}}
                                                    <h6 class="mb-0 text-sm">
                                                    {{ $application->eventContact->first_name }} {{ $application->eventContact->last_name }} </h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $application->eventContact->email }}</p>
{{--                                                @endforeach--}}
{{--                                                {{ $eventContact->first_name }} {{ $eventContact->last_name }}--}}

                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-secondary text-xs font-weight-bold">{{ $application->eventContact->contact_number }}</p>
{{--                                            <p class="text-xs text-secondary mb-0">{{ $application->organization }}</p>--}}
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $application->submission_date }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if ($application->status === 'Online')
                                                <span class="badge badge-sm bg-gradient-success">{{ $application->submission_status }}</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-warning">{{ $application->submission_status }}</span>
                                            @endif
                                        </td>
                                        <td class="align-middle">
                                            <a href="#" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Edit application">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
