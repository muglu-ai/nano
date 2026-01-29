@extends('layouts.dashboard')
@section('title', ucfirst($slug))
@section('content')
<link rel="stylesheet" href="/asset/css/sponsor_styles.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">
<div class="sponsor-form-container">
    @php
    $sponsorItem = $sponsorItem ?? null;
    $categories = $categories ?? [];

    $itemName = $sponsorItem ? $sponsorItem->name : '';
    $itemImage = $sponsorItem ? $sponsorItem->image_url : '';
    $itemDescription = $sponsorItem ? $sponsorItem->deliverables : '';
    $category_id = $sponsorItem ? $sponsorItem->category_id : '';
    $mem_price = $sponsorItem ? $sponsorItem->mem_price : '';
    $regular_price = $sponsorItem ? $sponsorItem->price : '';
    $itemStatus = $sponsorItem ? $sponsorItem->status : '';
    $itemQuantity = $sponsorItem ? $sponsorItem->no_of_items : '1';
    $itemId = $sponsorItem ? $sponsorItem->id : '';
    $itemImagePath = $sponsorItem ?  $sponsorItem->image_url : '';

    //if itemID is not null then use route as item_update else sponsor_items.store

    $route = $itemId ? route('sponsor_items.update', ['id' => $itemId]) : route('sponsor_items.store');
    $formMethod = $itemId ? 'PUT' : 'POST';

    @endphp

    <div class="">
        <form action="{{$route}}" id="sponsorForm" enctype="multipart/form-data" method="POST">
            @csrf
            @if($itemId)
                @method('PUT')
            @endif
            <input type="hidden" name="itemNo" id="itemNo" min="0" step="0.01" value="{{$itemId}}">
            <div class="form-group">
                <label for="itemName">Name</label>
                <input type="text" id="itemName" name="itemName" value="{{$itemName}}" required>
            </div>
            
            <div class="form-group">
                <label for="itemImage">Image</label>
                <div class="file-upload">
                    <input type="file" id="itemImage" name="itemImage" accept="image/*">
                    <div class="file-preview">
                        @if ($itemImage)
                            <img src="{{ $itemImagePath }}" alt="Item Image" class="preview-image" style="width: 120px; height: 120px; object-fit: cover;">
                        @endif
                        <span class="file-name">
                            @if ($itemImage)
                                {{ basename($itemImagePath) }}
                            @else
                                No file chosen
                            @endif
                        </span>
                    </div>
                    <label for="itemImage" class="btn btn-secondary mt-2">Upload Image</label>
                </div>
                <script>
                    document.getElementById('itemImage').addEventListener('change', function(e) {
                        const fileName = e.target.files[0] ? e.target.files[0].name : 'No file chosen';
                        document.querySelector('.file-name').textContent = fileName;
                    });
                </script>
            </div>
            
            <div class="form-group">
                <label for="itemDescription">Description</label>
                <textarea id="itemDescription" name="itemDescription" rows="4">{{$itemDescription}}</textarea>
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select id="category" name="category_id" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $category_id == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-row">
                <div class="form-group half">
                    <label for="itemStatus">Status</label>
                    <div class="custom-select">
                        <select name="itemStatus" id="itemStatus">
                            <option value="active" {{ $itemStatus == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $itemStatus == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="pending" {{ $itemStatus == 'pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                        <div class="select-arrow"></div>
                    </div>
                </div>
                
                <div class="form-group half">
                    <label for="itemQuantity">No Of Items</label>
                    <input type="number" name="itemQuantity" id="itemQuantity" min="1" value="{{$itemQuantity}}" >
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group half">
                    <label for="memberPrice">Member Price</label>
                    <div class="price-input">
                        <span class="currency">₹</span>
                        <input type="number" name="memberPrice" id="memberPrice" min="0" step="0.01" value="{{$mem_price}}">
                    </div>
                </div>
                
                <div class="form-group half">
                    <label for="regularPrice">Regular Price</label>
                    <div class="price-input">
                        <span class="currency">₹</span>
                        <input type="number" name="regularPrice" id="regularPrice" min="0" step="0.01" value="{{$regular_price}}">
                    </div>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="reset" class="btn-secondary" id="cancelBtn">Cancel</button>
                <button type="submit" class="btn-primary">Save Item</button>
            </div>
        </form>
    </div>
</div>





@endsection