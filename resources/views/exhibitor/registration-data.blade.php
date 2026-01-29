@extends('layouts.users')
@section('title', 'Registration Data')
@section('content')

<style>
    .card {
        border: 1px solid #e3e6f0;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border-radius: 0.5rem;
    }
    
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 1.25rem 1.5rem;
    }
    
    .card-header h5 {
        color: white;
        font-weight: 600;
        margin: 0;
    }
    
    .table th {
        background-color: #5a5c69;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem 0.75rem;
        border: none;
    }
    
    .table td {
        padding: 0.75rem;
        vertical-align: middle;
        border-top: 1px solid #e3e6f0;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fc;
    }
    
    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.85em;
        font-weight: 600;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .stats-card h3 {
        color: white;
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }
    
    .stats-card p {
        color: rgba(255, 255, 255, 0.9);
        margin: 0.5rem 0 0 0;
        font-size: 0.9rem;
    }
    
    .alert {
        border-radius: 0.35rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        opacity: 0.3;
        margin-bottom: 1rem;
    }
    
    .info-badge {
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
    }
    
    .info-badge i {
        color: #5a5c69;
        margin-right: 0.5rem;
    }
</style>

<div class="container-fluid py-4">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Card -->
    <div class="stats-card">
        <h3>{{ $registrationData->count() }}</h3>
        <p>Total Registered data</p>
    </div>

    <!-- Info Badge -->
    <div class="info-badge">
        <i class="fas fa-info-circle"></i>
        <strong>Company:</strong> {{ $application->company_name ?? 'N/A' }}
    </div>

    <!-- Main Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>Registration Data
            </h5>
        </div>

        <!-- Table Section -->
        <div class="card-body p-0">
            @if($registrationData->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Job Title</th>
                                <th>Organisation Name</th>
                                <th>Pass Name</th>
                                <th>Registration Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrationData as $index => $registration)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <strong>{{ $registration->full_name ?? 'N/A' }}</strong>
                                        @if($registration->unique_id)
                                            <br>
                                            <small class="text-muted">ID: {{ $registration->unique_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="mailto:{{ $registration->email }}">{{ $registration->email ?? 'N/A' }}</a>
                                    </td>
                                    <td>{{ $registration->mobile ?? 'N/A' }}</td>
                                    <td>{{ $registration->job_title ?? 'N/A' }}</td>
                                    <td>{{ $registration->organisation_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $registration->pass_name ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($registration->created_at)
                                            {{ \Carbon\Carbon::parse($registration->created_at)->format('M d, Y') }}
                                            <br>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($registration->created_at)->format('h:i A') }}
                                            </small>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h5>No Registration Data Found</h5>
                    <p class="text-muted">You haven't registered any complimentary delegates yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

