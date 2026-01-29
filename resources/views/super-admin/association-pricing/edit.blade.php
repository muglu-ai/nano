@extends('layouts.app')

@section('title', 'Edit Association Pricing Rule - Super Admin')

@section('content')
<style>
    .edit-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .edit-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
    }
    
    .edit-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        border: none;
    }
    
    .edit-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .logo-preview {
        width: 120px;
        height: 120px;
        object-fit: contain;
        border-radius: 8px;
        background: #f8f9fa;
        padding: 8px;
        border: 2px dashed #dee2e6;
    }
</style>

<div class="edit-container">
    <div class="edit-card">
        <div class="edit-card-header">
            <h4>
                <i class="fas fa-edit"></i>
                Edit Association Pricing Rule
            </h4>
        </div>
        <div class="card-body p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('super-admin.association-pricing.update', $association->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="association_name" class="form-label">Association Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('association_name') is-invalid @enderror" 
                               id="association_name" name="association_name" 
                               value="{{ old('association_name', $association->association_name) }}" required>
                        @error('association_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Unique identifier (e.g., TIESB, TIESNB)</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                               id="display_name" name="display_name" 
                               value="{{ old('display_name', $association->display_name) }}" required>
                        @error('display_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="promocode" class="form-label">Promocode</label>
                        <input type="text" class="form-control @error('promocode') is-invalid @enderror" 
                               id="promocode" name="promocode" 
                               value="{{ old('promocode', $association->promocode) }}" 
                               placeholder="e.g., TIESB2025">
                        @error('promocode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Unique promocode (optional but recommended)</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="logo" class="form-label">Logo</label>
                        <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                               id="logo" name="logo" accept="image/*" 
                               onchange="previewLogo(this)">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">PNG, JPG, GIF, SVG (max 2MB)</small>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Logo</label>
                    <div>
                        @if($association->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($association->logo_path))
                            <img src="{{ asset('storage/' . $association->logo_path) }}" 
                                 alt="{{ $association->display_name }}" 
                                 class="logo-preview" id="logoPreview">
                        @else
                            <div class="logo-preview d-flex align-items-center justify-content-center" id="logoPreview">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="base_price" class="form-label">Base Price (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" class="form-control @error('base_price') is-invalid @enderror" 
                               id="base_price" name="base_price" 
                               value="{{ old('base_price', $association->base_price) }}" required>
                        @error('base_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="special_price" class="form-label">Special Price (₹)</label>
                        <input type="number" step="0.01" class="form-control @error('special_price') is-invalid @enderror" 
                               id="special_price" name="special_price" 
                               value="{{ old('special_price', $association->special_price) }}">
                        @error('special_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Discounted price (optional)</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="max_registrations" class="form-label">Max Registrations</label>
                        <input type="number" class="form-control @error('max_registrations') is-invalid @enderror" 
                               id="max_registrations" name="max_registrations" 
                               value="{{ old('max_registrations', $association->max_registrations) }}">
                        @error('max_registrations')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Leave empty for unlimited</small>
                        @if($association->max_registrations)
                            <div class="mt-2">
                                <small class="text-info">
                                    Current: <strong>{{ $association->current_registrations ?? 0 }}</strong> / {{ $association->max_registrations }}
                                    @if(isset($association->registration_count))
                                        ({{ $association->registration_count }} applications)
                                    @endif
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="valid_from" class="form-label">Valid From</label>
                        <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                               id="valid_from" name="valid_from" 
                               value="{{ old('valid_from', $association->valid_from ? $association->valid_from->format('Y-m-d') : '') }}">
                        @error('valid_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="valid_until" class="form-label">Valid Until</label>
                        <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                               id="valid_until" name="valid_until" 
                               value="{{ old('valid_until', $association->valid_until ? $association->valid_until->format('Y-m-d') : '') }}">
                        @error('valid_until')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $association->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="entitlements" class="form-label">Entitlements</label>
                    <textarea class="form-control @error('entitlements') is-invalid @enderror" 
                              id="entitlements" name="entitlements" rows="2">{{ old('entitlements', $association->entitlements) }}</textarea>
                    @error('entitlements')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">List of benefits/entitlements (optional)</small>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_complimentary" 
                                   name="is_complimentary" value="1" 
                                   {{ old('is_complimentary', $association->is_complimentary) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_complimentary">
                                Is Complimentary (Free Registration)
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" 
                                   name="is_active" value="1" 
                                   {{ old('is_active', $association->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('super-admin.association-pricing') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Association
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('logoPreview');
            if (preview.tagName === 'IMG') {
                preview.src = e.target.result;
            } else {
                preview.innerHTML = `<img src="${e.target.result}" class="logo-preview" style="width: 100%; height: 100%; object-fit: contain;">`;
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

