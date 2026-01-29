@extends('layouts.users')
@section('title', 'Co Exhibitor Dashboard')
@section('content')
    <div class="container-fluid py-4">
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h3 class="mb-0 h4 font-weight-bolder">Co Exhibitor Dashboard</h3>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end mb-0">
                        <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Status Banner -->
        {{-- <div class="alert alert-info border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-info-circle fa-2x"></i>
                </div>
                <div>
                    <h5 class="alert-heading mb-1">Welcome to Your Co-Exhibitor Portal</h5>
                    <p class="mb-0">Complete your profile and submit required documents to ensure a smooth exhibition experience.</p>
                </div>
            </div>
        </div> --}}

        <!-- Quick Actions -->
        {{-- <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="#" class="btn btn-light border w-100 py-3 text-start">
                                    <i class="fas fa-edit me-2"></i>
                                    Update Profile
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-light border w-100 py-3 text-start">
                                    <i class="fas fa-file-upload me-2"></i>
                                    Upload Documents
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-light border w-100 py-3 text-start">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    View Schedule
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="#" class="btn btn-light border w-100 py-3 text-start">
                                    <i class="fas fa-headset me-2"></i>
                                    Contact Support
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        @php 
        $head = "Main Exhibitor Information";
        $subhead = "Main Exhibitor";
        $companyName = $coExhibitor->application->company_name ?? 'Not Assigned' ;

        if($coExhibitor && $coExhibitor->pavilion_name !=null){
            $head = "Pavilion Information";
            $subhead = "Pavilion Name";
            $companyName = $coExhibitor->pavilion_name;
        }
        @endphp

        <div class="row">
            <!-- Main Exhibitor Information -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{  $head}}</h5>
                        {{-- <span class="badge bg-success">Verified</span> --}}
                    </div>
                    <div class="card-body">
                        @if($coExhibitor->application && $coExhibitor->application->company_name)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">{{$subhead}}</label>
                                        <p class="mb-0 fw-bold">{{$companyName}}</p>
                                    </div>
                                    {{-- <div class="mb-3">
                                        <label class="text-muted small mb-1">Contact Person</label>
                                        <p class="mb-0 fw-bold">{{ $coExhibitor->application->contact_person ?? 'Not Available' }}</p>
                                    </div> --}}
                                   
                                </div>
                                <div class="col-md-6">
                                     <div class="mb-3">
                                        <label class="text-muted small mb-1">Booth/Stall Number</label>
                                        <p class="mb-0 fw-bold">{{ $coExhibitor->application->stallNumber ?? 'Not Assigned' }}</p>
                                    </div>
                                    
                                    {{-- <div class="mb-3">
                                        <label class="text-muted small mb-1">Email</label>
                                        <p class="mb-0 fw-bold">{{ $coExhibitor->application->email ?? 'Not Available' }}</p>
                                    </div> --}}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No main exhibitor association found. Please contact the administrator.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Co Exhibitor Information -->
            @if(isset($coExhibitor) && $coExhibitor->application)
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Co Exhibitor Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-lg-4 col-md-12">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body d-flex align-items-center">
                                        <div class="me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px; background:linear-gradient(135deg,#ff416c,#ff4b2b); border-radius:50%;">
                                            <i class="fa-solid fa-user-tag fa-2x text-white"></i>
                                        </div>
                                        <div>
                                            <h6 class="text-muted mb-1">Co-Exhibitor Name</h6>
                                            <span class="fw-bold fs-5">{{ $coExhibitor->co_exhibitor_name ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!empty($coExhibitor->stall_size) && $coExhibitor->stall_size !== 'N/A')
                                <div class="col-lg-4 col-md-12">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px; background:linear-gradient(135deg,#36d1c4,#1e90ff); border-radius:50%;">
                                                <i class="fa-solid fa-store fa-2x text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Stall Size</h6>
                                                <span class="fw-bold fs-5">{{ $coExhibitor->stall_size }} SQM</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if(!empty($coExhibitor->booth_number) && $coExhibitor->booth_number !== 'N/A')
                                <div class="col-lg-4 col-md-12">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body d-flex align-items-center">
                                            <div class="me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px; background:linear-gradient(135deg,#43e97b,#38f9d7); border-radius:50%;">
                                                <i class="fa-solid fa-location-dot fa-2x text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="text-muted mb-1">Booth Number</h6>
                                                <span class="fw-bold fs-5">{{ $coExhibitor->booth_number }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if(
    !empty($coExhibitor->exhibitionParticipant) && 
    (
        (!empty($coExhibitor->exhibitionParticipant->stall_manning_count) && $coExhibitor->exhibitionParticipant->stall_manning_count > 0) ||
        (!empty($coExhibitor->exhibitionParticipant->complimentary_delegate_count) && $coExhibitor->exhibitionParticipant->complimentary_delegate_count > 0)
    )
)
<div class="row mt-4">
    @if(!empty($coExhibitor->exhibitionParticipant->stall_manning_count) && $coExhibitor->exhibitionParticipant->stall_manning_count > 0)
    <div class="col-lg-6 col-md-12 mb-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px; background:linear-gradient(135deg,#f7971e,#ffd200); border-radius:50%;">
                    <i class="fa-solid fa-users fa-2x text-white"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Exhibitor Passes Allocated</h6>
                    <span class="fw-bold fs-5">{{ $coExhibitor->exhibitionParticipant->stall_manning_count }}</span>
                    <br>
                    <a href="{{ route('co-exhibitor.passes') }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fa-solid fa-id-badge me-1"></i> Click here to manage the badges
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(!empty($coExhibitor->exhibitionParticipant->complimentary_delegate_count) && $coExhibitor->exhibitionParticipant->complimentary_delegate_count > 0)
    <div class="col-lg-6 col-md-12 mb-3">
        <div class="card h-100 border-0 shadow-sm">
            <div class="card-body d-flex align-items-center">
                <div class="me-3 d-flex align-items-center justify-content-center" style="width:56px; height:56px; background:linear-gradient(135deg,#36d1c4,#1e90ff); border-radius:50%;">
                    <i class="fa-solid fa-ticket fa-2x text-white"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Exhibitor Inaugural Passes Allocated</h6>
                    <span class="fw-bold fs-5">{{ $coExhibitor->exhibitionParticipant->complimentary_delegate_count }}</span>
                    <br>
                    <a href="{{ route('co-exhibitor.inauguralPasses') }}" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="fa-solid fa-id-badge me-1"></i> Click here to manage the passes
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endif

                        <!-- Progress Tracker -->
                        {{-- <div class="mt-4">
                            <h6 class="mb-3">Application Progress</h6>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">3 of 4 steps completed</small>
                                <small class="text-success">75% Complete</small>
                            </div>
                        </div> --}}
                    </div>
                </div>
            </div>
            @else
            <div class="col-12">
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    No co-exhibitor information available. Please contact the main exhibitor or administrator.
                </div>
            </div>
            @endif
        </div>
    </div>
@endsection