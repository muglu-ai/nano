@extends('layouts.dashboard')

@section('title', 'Ticket Categories - ' . $event->event_name)

@section('content')
<style>
    .categories-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .categories-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
        margin-bottom: 2rem;
    }
    
    .categories-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.75rem 2rem;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .categories-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .categories-card-body {
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
        border-bottom: 2px solid #667eea;
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
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
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
        background: #667eea;
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
        background: #5568d3;
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
    
    .btn-subcategories {
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
    
    .btn-subcategories:hover {
        background: #38a169;
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
    
    .badge-count {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.75rem;
    }
</style>

<div class="categories-container">
    <div class="categories-card">
        <div class="categories-card-header">
            <h4>
                <i class="fas fa-list"></i>
                Ticket Categories - {{ $event->event_name }}
            </h4>
            <a href="{{ route('admin.tickets.events.setup', $event->id) }}" class="btn-back" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-arrow-left"></i>
                Back to Setup
            </a>
        </div>
        <div class="categories-card-body">
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

            <!-- Add New Category Form -->
            <div class="form-section">
                <h5 class="form-section-title">
                    <i class="fas fa-plus-circle"></i>
                    Add New Ticket Category
                </h5>
                <form action="{{ route('admin.tickets.events.categories.store', $event->id) }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" 
                                       value="{{ old('name') }}" 
                                       placeholder="e.g., Delegate, VIP, Workshop" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-5">
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
                        <div class="col-md-1">
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
                        <div class="col-md-1 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Existing Categories List -->
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Subcategories</th>
                            <th>Sort Order</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr id="category-row-{{ $category->id }}">
                                <td>
                                    <strong>{{ $category->name }}</strong>
                                </td>
                                <td>
                                    {{ $category->description ?: '-' }}
                                </td>
                                <td>
                                    @php
                                        $subcategoryCount = $category->subcategories->count();
                                    @endphp
                                    @if($subcategoryCount > 0)
                                        <span class="badge-count">{{ $subcategoryCount }} Subcategory{{ $subcategoryCount > 1 ? 'ies' : '' }}</span>
                                    @else
                                        <span class="text-muted">No subcategories</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $category->sort_order }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.tickets.events.subcategories', [$event->id, $category->id]) }}" 
                                           class="btn-subcategories">
                                            <i class="fas fa-layer-group"></i> Subcategories
                                        </a>
                                        <button type="button" class="btn-edit" onclick="editCategory({{ $category->id }}, '{{ addslashes($category->name) }}', '{{ addslashes($category->description ?? '') }}', {{ $category->sort_order }})">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="{{ route('admin.tickets.events.categories.delete', [$event->id, $category->id]) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this ticket category? This will also delete all subcategories and ticket types under it.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                    
                                    <!-- Edit Form (Hidden by default) -->
                                    <div class="edit-form mt-3" id="edit-form-{{ $category->id }}">
                                        <form action="{{ route('admin.tickets.events.categories.update', [$event->id, $category->id]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input type="text" name="name" class="form-control form-control-sm" 
                                                           id="edit-name-{{ $category->id }}" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" name="description" class="form-control form-control-sm" 
                                                           id="edit-description-{{ $category->id }}" placeholder="Description">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="sort_order" class="form-control form-control-sm" 
                                                           id="edit-sort-{{ $category->id }}" min="0">
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="d-flex gap-2">
                                                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-secondary btn-sm flex-fill" 
                                                                onclick="cancelEdit({{ $category->id }})">
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
                                <td colspan="5" class="empty-state">
                                    <i class="fas fa-list"></i>
                                    <h5>No Ticket Categories</h5>
                                    <p>Add ticket categories to organize your ticket types.</p>
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
    function editCategory(id, name, description, sortOrder) {
        // Hide all other edit forms
        document.querySelectorAll('.edit-form').forEach(form => {
            form.classList.remove('active');
        });
        
        // Show edit form for this category
        const editForm = document.getElementById('edit-form-' + id);
        editForm.classList.add('active');
        
        // Populate form fields
        document.getElementById('edit-name-' + id).value = name;
        document.getElementById('edit-description-' + id).value = description;
        document.getElementById('edit-sort-' + id).value = sortOrder;
        
        // Scroll to form
        editForm.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    function cancelEdit(id) {
        document.getElementById('edit-form-' + id).classList.remove('active');
    }
</script>
@endsection

