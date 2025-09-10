<!-- resources/views/sponsor_items/create.blade.php -->

@extends('layouts.dashboard')
@section('title', 'Create Sponsor Item')

@section('content')
<div class="container">
    <h2>Create Sponsor Item</h2>
    <form action="{{ route('sponsor_items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Item Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>

        <!-- Category -->
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select class="form-select" id="category_id" name="category_id" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Price -->
        <div class="mb-3">
            <label for="price" class="form-label">Price (₹)</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
        </div>

        <!-- Member Price -->
        <div class="mb-3">
            <label for="mem_price" class="form-label">Member Price (₹)</label>
            <input type="number" step="0.01" class="form-control" id="mem_price" name="mem_price" required>
        </div>

        <!-- Number of Items -->
        <div class="mb-3">
            <label for="no_of_items" class="form-label">Number of Items</label>
            <input type="number" class="form-control" id="no_of_items" name="no_of_items" required>
        </div>

        <!-- Deliverables -->
        <div class="mb-3">
            <label for="deliverables" class="form-label">Deliverables</label>
            <textarea class="form-control" id="deliverables" name="deliverables" rows="3" required></textarea>
        </div>

        <!-- Image URL Upload -->
        <div class="mb-3">
            <label for="image_url" class="form-label">Image (optional)</label>
            <input type="file" class="form-control" id="image_url" name="image_url">
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="active" selected>Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <!-- Submit -->
        <button type="submit" class="btn btn-primary">Create Sponsor Item</button>

    </form>
</div>
@endsection
