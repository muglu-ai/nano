@extends('layouts.dashboard')

@section('title', 'Subcategories - ' . $category->name . ' - ' . $event->event_name)

@section('content')
<style>
    .subcategories-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .subcategories-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
        margin-bottom: 2rem;
    }
    
    .subcategories-card-header {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        padding: 1.75rem 2rem;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .subcategories-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .subcategories-card-body {
        padding: 2rem;
    }
    
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e9ecef;
    }
    
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #48bb78;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        display: block;
    }
    
    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control:focus {
        border-color: #48bb78;
        box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.1);
        outline: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(72, 187, 120, 0.2);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(72, 187, 120, 0.3);
        color: white;
    }
    
    .btn-back {
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-back:hover {
        background: #cbd5e0;
        color: #2d3748;
        transform: translateY(-2px);
    }
    
    .table-wrapper {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e9ecef;
    }
    
    .table {
        margin-bottom: 0;
    }
    
    .table thead {
        background: #f8f9fa;
    }
    
    .table thead th {
        font-weight: 600;
        color: #4a5568;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e9ecef;
    }
    
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .btn-edit {
        background: #48bb78;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-edit:hover {
        background: #38a169;
        color: white;
        transform: translateY(-2px);
    }
    
    .btn-delete {
        background: #e53e3e;
        color: white;
        border: none;
        border-radius: 6px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
    }
    
    .btn-delete:hover {
        background: #c53030;
        color: white;
        transform: translateY(-2px);
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #718096;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .text-danger {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }
    
    .edit-form {
        display: none;
    }
    
    .edit-form.active {
        display: block;
    }
    
    .badge-sort {
        background: #e2e8f0;
        color: #4a5568;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
    }
</style>

<div class="subcategories-container">
    <div class="subcategories-card">
        <div class="subcategories-card-header">
            <h4>
                <i class="fas fa-layer-group"></i>
                Subcategories for {{ $category->name }} — {{ $event->event_name }}
            </h4>
            <a href="{{ route('admin.tickets.events.categories', $event->id) }}" class="btn-back" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-arrow-left"></i>
                Back to Categories
            </a>
        </div>
        <div class="subcategories-card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Add New Subcategory Form -->
            <div class="form-section">
                <h5 class="form-section-title">
                    <i class="fas fa-plus-circle"></i>
                    Add New Subcategory
                </h5>
                <form action="{{ route('admin.tickets.events.subcategories.store', [$event->id, $category->id]) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Subcategory Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       value="{{ old('name') }}"
                                       placeholder="e.g., Early Bird, Standard" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <input type="text" name="description" class="form-control"
                                       value="{{ old('description') }}"
                                       placeholder="Brief description">
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">Sort</label>
                                <input type="number" name="sort_order" class="form-control"
                                       value="{{ old('sort_order', 0) }}"
                                       placeholder="0" min="0">
                                @error('sort_order')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Existing Subcategories List -->
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Sort Order</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subcategories as $subcategory)
                            <tr id="subcategory-row-{{ $subcategory->id }}">
                                <td>
                                    <strong>{{ $subcategory->name }}</strong>
                                </td>
                                <td>
                                    {{ $subcategory->description ?: '-' }}
                                </td>
                                <td>
                                    <span class="badge-sort">{{ $subcategory->sort_order }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn-edit" onclick="editSubcategory({{ $subcategory->id }}, '{{ addslashes($subcategory->name) }}', '{{ addslashes($subcategory->description ?? '') }}', {{ $subcategory->sort_order }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.tickets.events.subcategories.delete', [$event->id, $subcategory->id]) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this subcategory?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Edit Form (Hidden by default) -->
                                    <div class="edit-form mt-3" id="edit-form-{{ $subcategory->id }}">
                                        <form action="{{ route('admin.tickets.events.subcategories.update', [$event->id, $subcategory->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input type="text" name="name" class="form-control form-control-sm"
                                                           id="edit-name-{{ $subcategory->id }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="description" class="form-control form-control-sm"
                                                           id="edit-description-{{ $subcategory->id }}" placeholder="Description">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="sort_order" class="form-control form-control-sm"
                                                           id="edit-sort-{{ $subcategory->id }}" min="0">
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm flex-fill"
                                                                onclick="cancelEdit({{ $subcategory->id }})">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="fas fa-layer-group"></i>
                                    <h5>No Subcategories</h5>
                                    <p>Add subcategories under this category to organize ticket types.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function editSubcategory(id, name, description, sortOrder) {
        document.querySelectorAll('.edit-form').forEach(form => {
            form.classList.remove('active');
        });

        const editForm = document.getElementById('edit-form-' + id);
        editForm.classList.add('active');

        document.getElementById('edit-name-' + id).value = name;
        document.getElementById('edit-description-' + id).value = description;
        document.getElementById('edit-sort-' + id).value = sortOrder;

        editForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function cancelEdit(id) {
        document.getElementById('edit-form-' + id).classList.remove('active');
    }
</script>
@endsection
