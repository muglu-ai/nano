@extends('layouts.app')

@section('title', 'Manage Sectors & Organization Types - Super Admin')

@section('content')
<div class="container-fluid py-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Sectors Section -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-industry"></i> Sectors</h4>
            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addSectorModal">
                <i class="fas fa-plus"></i> Add Sector
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Sort Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sectors as $sector)
                            <tr>
                                <td>{{ $sector->name }}</td>
                                <td>
                                    <span class="badge {{ $sector->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $sector->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $sector->sort_order }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#editSectorModal{{ $sector->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('super-admin.sectors.delete', $sector->id) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @include('super-admin.modals.edit-sector', ['sector' => $sector])
                        @empty
                            <tr><td colspan="4" class="text-center">No sectors found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Sub-Sectors Section -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-tags"></i> Sub-Sectors</h4>
            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addSubSectorModal">
                <i class="fas fa-plus"></i> Add Sub-Sector
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Sort Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subSectors as $subSector)
                            <tr>
                                <td>{{ $subSector->name }}</td>
                                <td>
                                    <span class="badge {{ $subSector->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $subSector->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $subSector->sort_order }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#editSubSectorModal{{ $subSector->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('super-admin.sub-sectors.delete', $subSector->id) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @include('super-admin.modals.edit-sub-sector', ['subSector' => $subSector])
                        @empty
                            <tr><td colspan="4" class="text-center">No sub-sectors found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Organization Types Section -->
    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0"><i class="fas fa-building"></i> Organization Types</h4>
            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addOrgTypeModal">
                <i class="fas fa-plus"></i> Add Organization Type
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Sort Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orgTypes as $orgType)
                            <tr>
                                <td>{{ $orgType->name }}</td>
                                <td>
                                    <span class="badge {{ $orgType->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $orgType->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $orgType->sort_order }}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#editOrgTypeModal{{ $orgType->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('super-admin.org-types.delete', $orgType->id) }}" 
                                          method="POST" class="d-inline" 
                                          onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @include('super-admin.modals.edit-org-type', ['orgType' => $orgType])
                        @empty
                            <tr><td colspan="4" class="text-center">No organization types found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('super-admin.modals.add-sector')
@include('super-admin.modals.add-sub-sector')
@include('super-admin.modals.add-org-type')
@endsection
