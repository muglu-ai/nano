@extends('layouts.users')
@section('title', $slug)
<style>
    @media (max-width: 767.98px) {
            .custom-height {
                height: 500px;
            }
        }

        @media (min-width: 768px) {
            .custom-height {
                height: 400px;
            }
        }
        .mand {
            color: red;
        }
        .custom-label {
            font-size: 14px !important;
            font-weight: 500 !important;
            color: #6c757d !important;
            margin-bottom: 5px !important;
        }
</style>
@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <form class="multisteps-form__form custom-height" method="POST" action="{{ route('product.store') }}" enctype="multipart/form-data" >
                @csrf

                <!-- Panel: Product Information -->
                <div class="multisteps-form__panel border-radius-xl bg-white js-active" data-animation="FadeIn">
                    <h5 class="font-weight-bolder mb-0">Add Product</h5>
                    <p class="mb-5 text-sm">Enter product details below</p>

                    <div class="multisteps-form__content">
                        <div class="row mt-3">
                            <!-- Product Name -->
                            <div class="col-sm-6">
                                <div class="input-group input-group-dynamic is-filled">
                                    <label class="form-label custom-label">Product Name <span class="mand">*</span></label>
                                    <input type="text" name="product_name" class="form-control" required>
                                </div>
                            </div>

                            <!-- Product Image -->
                            <div class="col-sm-6 mt-4 mt-sm-0">
                                <div class="input-group input-group-dynamic is-filled">
                                    <label class="form-label custom-label">Product Image <span class="mand">*</span></label>
                                    <input type="file" name="product_image" class="form-control" accept="image/png, image/jpeg" required>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-sm-12 mt-4">
                                <div class="input-group input-group-dynamic is-filled">
                                    <label class="form-label custom-label">Description <span class="mand">*</span></label>
                                    <textarea id="description" name="description" class="form-control" rows="5" maxlength="750" required oninput="updateWordCount()"></textarea>
                                </div>
                                <small id="wordCount" class="text-muted">0 / 750 characters</small>
                            </div>
                            <script>
                                function updateWordCount() {
                                    const textarea = document.getElementById('description');
                                    const wordCount = document.getElementById('wordCount');
                                    const maxLength = 750;
                                    let value = textarea.value;
                                    if (value.length > maxLength) {
                                        textarea.value = value.substring(0, maxLength);
                                        value = textarea.value;
                                    }
                                    wordCount.textContent = `${value.length} / ${maxLength} characters`;
                                }
                                document.addEventListener('DOMContentLoaded', updateWordCount);
                            </script>
                        </div>

                        <!-- Submit Button -->
                        <div class="button-row d-flex mt-4">
                            <button type="submit" class="btn bg-gradient-success ms-auto mb-0">Add Product</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

    @if(isset($exhibitorProducts) && $exhibitorProducts->count() > 0)
        {{-- @dump($exhibitorProducts) --}}
        <div class="container mt-4">
            <div class="card">
                <div class="card-body mt-5">
                    <h5 class="font-weight-bolder mb-3 ms-4 ms-sm-4 ms-0">Existing Products</h5>
                    <div class="row g-3">
                        @foreach($exhibitorProducts as $product)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    @if($product->product_image)
                                        <img src="{{ asset('storage/' . $product->product_image) }}" class="card-img-top" alt="{{ $product->product_name }}" style="height: 180px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/default-product.png') }}" class="card-img-top" alt="No Image" style="height: 180px; object-fit: cover;">
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $product->product_name }}</h6>
                                        <p class="card-text">{{ $product->description }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection
