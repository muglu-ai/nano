@extends('layouts.dashboard')
@section('title', 'Declaration Forms - ' . ucfirst(str_replace('_', ' ', $status)))
@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 font-weight-bolder">Declaration Forms - {{ ucfirst(str_replace('_', ' ', $status)) }}</h3>
            </div>
            <div class="col-auto">
                <a href="{{ route('admin.declarations.export', ['status' => $status]) }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Export (ZIP with PDFs & CSV)
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.declarations.list') }}">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by company name, application ID, or email..." 
                                       value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Search
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.declarations.list', ['status' => $status]) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Application ID</th>
                                <th>Company Name</th>
                                <th>Email</th>
                                <th>Submission Status</th>
                                <th>Declaration Status</th>
                                @if($status === 'filled')
                                <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $application)
                                <tr>
                                    <td>{{ $application->application_id }}</td>
                                    <td>{{ $application->company_name }}</td>
                                    <td>{{ $application->user->email ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge badge-{{ $application->submission_status === 'approved' ? 'success' : ($application->submission_status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($application->submission_status ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($application->declarationStatus == 1)
                                            <span class="badge badge-success">Filled</span>
                                        @else
                                            <span class="badge badge-danger">Not Filled</span>
                                        @endif
                                    </td>
                                    @if($status === 'filled')
                                    <td>
                                        @php
                                            $companyName = preg_replace('/[^A-Za-z0-9]/', '', (string) $application->company_name);
                                            $fileName = $companyName . 'declaration.pdf';
                                            $filePath = storage_path('app/public/declarations/' . $application->application_id . '/' . $fileName);
                                        @endphp
                                        @if(file_exists($filePath))
                                            <a href="{{ route('admin.declarations.view', ['id' => $application->id]) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-info">
                                                <i class="fas fa-file-pdf"></i> View PDF
                                            </a>
                                        @else
                                            <span class="text-muted">PDF not found</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $status === 'filled' ? '6' : '5' }}" class="text-center">
                                        No applications found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

