@extends('layouts.users')
@section('title', 'Dashboard')
@section('content')

    <style>
        /* Style the fascia name input and button for a more modern look */
        #fascia_name {
            border: none;
            border-bottom: 2px solid #ff416c;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            font-size: 1.15rem;
            padding-left: 0;
            transition: border-color 0.2s;
        }

        #fascia_name:focus {
            border-bottom: 2.5px solid #ff416c;
            outline: none;
            background: transparent;
        }

        .btn-primary.w-100 {
            background: #f72585;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            transition: background 0.2s;
        }

        .btn-primary.w-100:hover,
        .btn-primary.w-100:focus {
            background: #d9046b;
        }

        .card .form-control::placeholder {
            color: #888;
            opacity: 1;
        }
    </style>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
            </div>
            {{-- <div class="col text-end">
                    @if ($application->submission_status == 'approved')
                        <button class="btn btn-primary" onclick="showCoExhibitorForm()">Add Co-Exhibitor</button>
                    @endif
                </div> --}}
        </div>

        {{-- Top Row of Info Cards --}}
        <div class="row mb-4">
            <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 d-flex align-items-center justify-content-center"
                             style="width:56px; height:56px; background:linear-gradient(135deg,#ff416c,#ff4b2b); border-radius:50%;">
                            <i class="fa-solid fa-ticket fa-2x text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Booth Number</h6>
                            <span class="fw-bold fs-5">{{ $application->stallNumber ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-12 mb-4 mb-lg-0">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <div class="me-3 d-flex align-items-center justify-content-center"
                             style="width:56px; height:56px; background:linear-gradient(135deg,#36d1c4,#1e90ff); border-radius:50%;">
                            <i class="fa-solid fa-store fa-2x text-white"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Stall Type / Size</h6>
                            <span class="fw-bold fs-5">{{ $application->stall_category ?? 'N/A' }} /
                            {{ $application->allocated_sqm ?? 'N/A' }} SQM</span>
                        </div>
                    </div>
                </div>
            </div>

{{--            <div class="col-lg-4 col-md-12">--}}
{{--                <div class="card shadow-sm h-100">--}}
{{--                    <div class="card-body d-flex align-items-center">--}}
{{--                        <div class="me-3 d-flex align-items-center justify-content-center"--}}
{{--                             style="width:56px; height:56px; background:linear-gradient(135deg,#43e97b,#38f9d7); border-radius:50%;">--}}
{{--                            <i class="fa-solid fa-location-dot fa-2x text-white"></i>--}}
{{--                        </div>--}}
{{--                        <div>--}}
{{--                            <h6 class="mb-1">Preferred Location</h6>--}}
{{--                            <span class="fw-bold fs-5">{{ $application->pref_location ?? 'N/A' }}</span>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}

        </div>

        {{-- Conditional Fascia Name Section --}}
        {{-- Show fascia name form only if Shell Scheme and fascia name is empty --}}
        @if ($application->stall_category === 'Shell Scheme' && empty($application->fascia_name))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-primary border-2">
                        <div class="card-header">
                            <h5 class="card-title text-primary mb-0">
                                <i class="fa-solid fa-circle-exclamation me-2"></i>Action Required: Add Your Fascia Name
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text">Please provide the name you want to be displayed on your booth's fascia
                                board. This will be used for printing.</p>
                            <form action="{{ route('user.fascia.update') }}" method="POST"
                                  class="row g-3 align-items-center">
                                @csrf
                                @method('PATCH')
                                <div class="col-md-8">
                                    <label for="fascia_name" class="visually-hidden">Fascia Name</label>
                                    <input type="text" class="form-control form-control-lg" id="fascia_name"
                                           name="fascia_name" placeholder="e.g., Your Company Name" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">Save Fascia Name</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Second Row of Info Cards --}}
        <div class="row">
            {{-- Display Fascia Name Card if it is filled and Shell Scheme --}}
            @if ($application->stall_category === 'Shell Scheme' && !empty($application->fascia_name))
                <div class="col-xl-3 col-sm-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-sm mb-0 text-capitalize">Fascia Name</p>
                                <h4 class="mb-0">{{ $application->fascia_name }}</h4>
                            </div>
                            <div class="icon icon-md icon-shape bg-gradient-info text-center border-radius-lg">
                                <i class="fa-solid fa-signature opacity-10"></i>
                            </div>
                        </div>
                        {{-- <div class="card-footer">
                                    <p class="mb-0 text-sm"><a href="{{ route('application.info') }}" class="text-info font-weight-bolder">Click to edit</a></p>
                    </div> --}}
                    </div>
                </div>
            @endif


            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Total Exhibitor Passes Allocated</p>
                            <h4 class="mb-0"><a
                                        href="exhibitor/list/stall_manning">{{ $exhibitionParticipant['stall_manning_count'] ?? 0 }}</a>
                            </h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">weekend</i>
                        </div>
                    </div>
                    <div class="card-footer">
                        <p class="mb-0 text-sm"><a href="exhibitor/list/stall_manning"
                                                   class="text-success font-weight-bolder">Click here</a> for Exhibitor
                            Registration.</p>
                    </div>
                </div>
            </div>


            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Total Inaugural Passes Allocated</p>
                            <h4 class="mb-0"><a
                                        href="exhibitor/list/inaugural_passes">{{ $exhibitionParticipant['complimentary_delegate_count'] ?? 0 }}</a>
                            </h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">weekend</i>
                        </div>
                    </div>
                    <div class="card-footer">
                        <p class="mb-0 text-sm"><a href="exhibitor/list/inaugural_passes"
                                                   class="text-success font-weight-bolder">Click here</a> for Inaugural
                            Registration.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-sm-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <div class="w-100">
                        <label class="form-label mb-2 text-sm text-capitalize fw-semibold" for="logo_link">Logo
                            Link</label>
                        @if (empty($application->logo_link))
                            <form action="{{ route('user.logo.update') }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="input-group">
                                    <input
                                            type="url"
                                            id="logo_link"
                                            name="logo_link"
                                            class="form-control"
                                            placeholder="Paste logo URL here"
                                            value="{{ old('logo_link', $application->logo_link ?? '') }}"
                                            required>
                                    <button type="submit" class="btn btn-primary ms-2">Save Logo Link</button>
                                </div>
                                @if (session('logo_success'))
                                    <div class="text-success small mt-2">{{ session('logo_success') }}</div>
                                @endif
                                @error('logo_link')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                                @enderror
                            </form>
                        @else
                            <div class="mt-2 d-flex align-items-center">
                                <i class="fa fa-link me-1 text-primary"></i>
                                <a
                                        href="{{ $application->logo_link }}"
                                        target="_blank"
                                        class="text-primary fw-medium"
                                        style="text-decoration: underline;">View Uploaded Logo Link</a>
                            </div>
                        @endif
                    </div>
                    <div class="icon icon-md icon-shape bg-gradient-info text-center border-radius-lg ms-3">
                        <i class="fa-solid fa-image opacity-10"></i>
                    </div>
                </div>
            </div>
        </div>

        @if($application->sponsorships()->exists())
            <div class="alert alert-info mt-4"
                 style="font-size:1.08rem; border-radius:1rem; background:linear-gradient(90deg,#e0eafc,#cfdef3); color:#222;">
                <strong>Dear Sponsor(s),</strong><br>
                We invite you to register participants from your organization as visitors for SEMICON INDIA 2025 by
                using the link below:<br><br>
                <span class="fw-semibold">🔗 Visitor Registration Link:</span> <a
                        href="https://portal.semiconindia.org/visitor/registration" target="_blank"
                        class="text-primary text-decoration-underline">https://portal.semiconindia.org/visitor/registration</a><br><br>
                While registering, participants may also express their interest in attending the Inaugural Program by
                selecting the checkbox that says: <span class="fw-semibold">“Participate (In-person) in SEMICON Inaugural event on 2nd Sept.”</span><br>
                <span class="text-danger">Please note that inaugural registration is subject to approval by the ISM team.</span><br><br>
                For any questions or need assistance with registration, feel free to contact us on : <a
                        href="mailto:semiconindia@semi.org" class="text-primary">semiconindia@semi.org</a>
            </div>
        @endif

    </div>
    </div>
@endsection