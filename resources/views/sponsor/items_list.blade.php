@extends('layouts.dashboard')
@section('title', ucfirst($slug))
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- //print_r($sponsorItems); --}}
    
    <link rel="stylesheet" href="/asset/css/sponsor_styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet">

    <div class="container">
        {{-- <h2>{{ $slug }}</h2> --}}
         <header>
            <h1>Add Sponsor Item</h1>
            <a href="{{ route('sponsor.add') }}" id="addNewBt n " class="btn btn-primary">+ New Sponsor Item</a>
        </header>

        <!-- Category Dropdown -->
        <div class="mb-3">
            <label for="categoryDropdown" class="form-label">Select Sponsor Category</label>
            <select id="categoryDropdown" class="form-select">
                <option value="">-- All Categories --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Sponsor Items Table -->
        <div class="table-container table-responsive">
            {{-- <table id="sponsorTable"> --}}
        <table class="table table-bordered align-middle" id="sponsorTable">
            <thead class="table-light">
                <tr>
                    <th style="max-width: 150px; word-wrap: break-word; white-space: normal;">Name</th>
                    <th>Image</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>No Of Items</th>
                    <th>Member Price</th>
                    <th>Price</th>
                    <th>Actions</th>

                </tr>
            </thead>
            <tbody id="sponsorItemsTableBody">
                @foreach ($sponsorItems as $item)
                    <tr data-category-id="{{ $item->category_id }}">
                        <td class="name-clickable"  
                            data-id="{{ $item->id }}"
                            data-name="{{ $item->name }}"
                            data-image="{{ $item->image_url }}" data-description="{{ e($item->deliverables) }}"
                            data-status="{{ $item->status }}" data-no-items="{{ $item->no_of_items }}"
                            data-mem_price="{{ $item->mem_price }}" data-price="{{ $item->price }}"
                            style="text-decoration: none; max-width: 150px; word-wrap: break-word; white-space: normal; overflow: hidden; text-overflow: ellipsis;">
                            {{ $item->name }}
                        </td>
                        <td>
                            <div class="item-image">
                                {{-- <p>{{ $item->id }}</p> --}}
                            @if ($item->image_url)
                                <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="thumbnail"
                                    style="max-width: 100px; max-height: 100px;"
                                    onclick="openImagePreviewModal('{{ $item->image_url }}')" />
                                <div class="image-overlay">
                                    <button class="view-btn" type="button" onclick="openImagePreviewModal('{{ $item->image_url }}')">View</button>
                                </div>
                            @else
                                N/A
                            @endif
                            </div>
                        </td>
                        <td style="text-align: justify;">
                            @if ($item->deliverables)
                                <ul class="description-list">
                                    @foreach (explode('•', $item->deliverables) as $deliverable)
                                        @if (trim($deliverable))
                                            <li>
                                                @php
                                                    $words = explode(' ', trim($deliverable));
                                                    $chunks = array_chunk($words, 5);
                                                @endphp
                                                @foreach ($chunks as $chunk)
                                                    <span
                                                        style="display: block; text-align: justify;">{{ implode(' ', $chunk) }}</span>
                                                @endforeach
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            @else
                                N/A
                            @endif
                        </td>

                        <td style="text-align: center;">
                            @php
                                $status = strtolower($item->status);
                                $badgeClass = $status === 'active' ? 'active' : ($status === 'inactive' ? 'inactive' : 'pending');
                            @endphp
                            <span class="status-badge {{ $badgeClass }}">{{ ucfirst($item->status) }}</span>
                        </td>
                        <td class="text-center" style="text-align: center;">{{ $item->no_of_items }}</td>
                        <td class="price">
                            {{ $item->mem_price }}
                        </td>
                        <td class="price">{{ $item->price }}</td>
                        <td>
                            <div class="action-buttons">
                                <a href="{{ url('/sponsor/' . $item->id . '/update') }}" class="edit-btnd" title="Edit"><span class="edit-icon"></span></a>
                                <button class="delete-btn" title="De-active"><span class="delete-icon"></span></button>
                            </div>
                        </td>


                    </tr>
                @endforeach
            </tbody>
        </table>
        <!-- Modal -->
        <div class="modal fade" id="sponsorDetailsModal" tabindex="-1" aria-labelledby="sponsorDetailsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="sponsorDetailsModalLabel">Sponsor Item Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table">
                            <tbody id="modalContentBody">
                                <!-- Content will be dynamically inserted -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


       



    </div>

     <div id="sponsorModal" class="modal ">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Sponsor Item</h2>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="sponsorForm" enctype="multipart/form-data">
                   
                    
                    <input type="hidden" name="itemNo" id="itemNo" min="0" step="0.01">
                    <div class="form-group">
                        <label for="itemName">Name</label>
                        <input type="text" id="itemName" name="itemName" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="itemImage">Image</label>
                        <div class="file-upload">
                            <input type="file" id="itemImage" name="itemImage" accept="image/*">
                            <label for="itemImage" class="file-label">Choose file</label>
                            <span class="file-name">No file chosen</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="itemDescription">Description</label>
                        <textarea id="itemDescription" name="itemDescription" rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category_id" class="form-select" required>
                            <option value="">-- Select Category --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="itemStatus">Status</label>
                            <div class="custom-select">
                                <select name="itemStatus" id="itemStatus">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="pending">Pending</option>
                                </select>
                                <div class="select-arrow"></div>
                            </div>
                        </div>
                        
                        <div class="form-group half">
                            <label for="itemQuantity">No Of Items</label>
                            <input type="number" name="itemQuantity" id="itemQuantity" min="1" value="1">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group half">
                            <label for="memberPrice">Member Price</label>
                            <div class="price-input">
                                <span class="currency">₹</span>
                                <input type="number" name="memberPrice" id="memberPrice" min="0" step="0.01">
                            </div>
                        </div>
                        
                        <div class="form-group half">
                            <label for="regularPrice">Regular Price</label>
                            <div class="price-input">
                                <span class="currency">₹</span>
                                <input type="number" name="regularPrice" id="regularPrice" min="0" step="0.01">
                            </div>
                        </div>

                        
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="cancelBtn">Cancel</button>
                        <button type="submit" class="btn-primary">Save Item</button>
                    </div>
                </form>
            </div>
        </div>
    </div>  

    <div id="imagePreviewModal" class="modal">
        <div class="modal-content image-preview-content">
            <div class="modal-header">
                <h2>Image Preview</h2>
                <button class="close-btn" onclick="closeImagePreviewModal()">&times;</button>
            </div>
            <div class="modal-body image-preview-body">
                <img id="previewImage" src="" alt="Preview">
            </div>
        </div>
    </div>
    <script>
        function openImagePreviewModal(imageUrl) {
            const modal = document.getElementById('imagePreviewModal');
            const img = document.getElementById('previewImage');
            img.src = imageUrl || '';
            modal.style.display = 'block';
        }
        function closeImagePreviewModal() {
            const modal = document.getElementById('imagePreviewModal');
            modal.style.display = 'none';
        }
        // Optional: Close modal when clicking outside content
        window.onclick = function(event) {
            const modal = document.getElementById('imagePreviewModal');
            if (event.target === modal) {
                closeImagePreviewModal();
            }
        }
        // Optional: Close modal on ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeImagePreviewModal();
            }
        });
    </script>

    <div id="overlay"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.getElementById('categoryDropdown');
            const rows = document.querySelectorAll('#sponsorTable tbody tr');

            dropdown.addEventListener('change', function() {
                const selectedId = this.value;

                rows.forEach(row => {
                    const rowCategoryId = row.getAttribute('data-category-id');

                    // Show rows matching the selected category, hide others
                    if (!selectedId || selectedId === rowCategoryId) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
    <script>
        function showSponsorDetails(element) {
            // console.log("Clicked element:", element);

            const name = element.getAttribute('data-name');
            const image = element.getAttribute('data-image');
            const description = element.getAttribute('data-description');
            const status = element.getAttribute('data-status');
            const noItems = element.getAttribute('data-no-items');
            const memPrice = element.getAttribute('data-mem-price');
            const price = element.getAttribute('data-price');
            const itemId = element.getAttribute('data-itemId');
            //console.log("Item ID:", itemId);

            console.log("Data extracted:", {
                name,
                image,
                description,
                status,
                noItems,
                memPrice,
                price,
                itemId
            });

            const imageHtml = image ? `<img src="${image}" alt="${name}" style="max-width: 100px;">` : 'N/A';
            const descriptionHtml = description ?
                `<ul>${description.split('•').map(item => `<li>${item.trim()}</li>`).join('')}</ul>` :
                'N/A';

            const modalContent = `
        <tr><th>Name</th><td>${name}</td></tr>
        <tr><th>Image</th><td>${imageHtml}</td></tr>
        <tr><th>Description</th><td style="word-wrap: break-word; white-space: pre-wrap;">${descriptionHtml.replace(/\n/g, '')}</td></tr>
        <tr><th>Status</th><td>${status}</td></tr>
        <tr><th>No of Items</th><td>${noItems}</td></tr>
        <tr><th>Member Price</th><td>${memPrice}</td></tr>
        <tr><th>Price</th><td>${price}</td></tr>
        <tr>
            <td colspan="2">
                <button class="btn btn-primary" style="position: absolute; bottom: -10px; right: 10px;" onclick="editSponsorItem('${name}')">Edit</button>
            </td>
        </tr>
    `;

            document.getElementById('modalContentBody').innerHTML = modalContent;

            try {
                const modal = new bootstrap.Modal(document.getElementById('sponsorDetailsModal'));
                modal.show();
                console.log("Modal displayed.");
            } catch (error) {
                console.error("Error showing modal:", error);
            }
        }
    </script>
   
    <script src="/asset/js/sponsor_script.js"></script>
    <!-- Bootstrap and Popper.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.min.js"></script>

@endsection
