@extends('layouts.app')

@section('title', 'Association Pricing Rules - Super Admin')

@section('content')
<style>
    .associations-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .associations-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
    }
    
    .associations-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .associations-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .btn-create {
        background: white;
        color: #667eea;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-create:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        color: #667eea;
    }
    
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .table thead {
        background: #f8f9fa;
    }
    
    .table thead th {
        font-weight: 600;
        color: #4a5568;
        border-bottom: 2px solid #e2e8f0;
        padding: 1rem;
    }
    
    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }
    
    .table tbody tr:hover {
        background: #f8f9fa;
    }
    
    .association-logo {
        width: 60px;
        height: 60px;
        object-fit: contain;
        border-radius: 8px;
        background: #f8f9fa;
        padding: 4px;
    }
    
    .btn-action {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-size: 0.875rem;
        margin-right: 0.25rem;
        transition: all 0.2s;
    }
    
    .btn-edit {
        background: #667eea;
        color: white;
        border: none;
    }
    
    .btn-edit:hover {
        background: #5568d3;
        color: white;
        transform: translateY(-1px);
    }
    
    .btn-delete {
        background: #e53e3e;
        color: white;
        border: none;
    }
    
    .btn-delete:hover {
        background: #c53030;
        color: white;
        transform: translateY(-1px);
    }
    
    .badge-status {
        padding: 0.4rem 0.8rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .badge-active {
        background: #48bb78;
        color: white;
    }
    
    .badge-inactive {
        background: #a0aec0;
        color: white;
    }
    
    .badge-complimentary {
        background: #ed8936;
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #a0aec0;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
    }
    
    .price-info {
        font-weight: 600;
    }
    
    .price-special {
        color: #48bb78;
    }
    
    .price-base {
        color: #4a5568;
    }
</style>

<div class="associations-container">
    <div class="associations-card">
        <div class="associations-card-header">
            <h4>
                <i class="fas fa-tags"></i>
                Association Pricing Rules
            </h4>
            <button type="button" class="btn-create" data-bs-toggle="modal" data-bs-target="#createAssociationModal">
                <i class="fas fa-plus me-2"></i>Create New Association
            </button>
        </div>
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Association Name</th>
                            <th>Display Name</th>
                            <th>Promocode</th>
                            <th>Pricing</th>
                            <th>Registrations</th>
                            <th>Validity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($associations as $association)
                            <tr>
                                <td>
                                    @if($association->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($association->logo_path))
                                        <img src="{{ asset('storage/' . $association->logo_path) }}" 
                                             alt="{{ $association->display_name }}" 
                                             class="association-logo">
                                    @else
                                        <div class="association-logo bg-light d-flex align-items-center justify-content-center">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $association->association_name }}</strong>
                                </td>
                                <td>{{ $association->display_name }}</td>
                                <td>
                                    @if($association->promocode)
                                        <code class="bg-light px-2 py-1 rounded">{{ $association->promocode }}</code>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($association->is_complimentary)
                                        <span class="badge-complimentary">Complimentary</span>
                                    @elseif($association->special_price)
                                        <div class="price-info">
                                            <span class="price-special">₹{{ number_format($association->special_price, 2) }}</span>
                                            <br>
                                            <small class="text-muted text-decoration-line-through">₹{{ number_format($association->base_price, 2) }}</small>
                                        </div>
                                    @else
                                        <span class="price-info price-base">₹{{ number_format($association->base_price, 2) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($association->max_registrations)
                                        <strong>{{ $association->current_registrations ?? 0 }}</strong> / {{ $association->max_registrations }}
                                        @if(isset($association->registration_count))
                                            <br><small class="text-muted">({{ $association->registration_count }} apps)</small>
                                        @endif
                                    @else
                                        <strong>{{ $association->current_registrations ?? 0 }}</strong>
                                        @if(isset($association->registration_count))
                                            <br><small class="text-muted">({{ $association->registration_count }} apps)</small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($association->valid_from || $association->valid_until)
                                        <small>
                                            @if($association->valid_from)
                                                From: {{ \Carbon\Carbon::parse($association->valid_from)->format('M d, Y') }}<br>
                                            @endif
                                            @if($association->valid_until)
                                                Until: {{ \Carbon\Carbon::parse($association->valid_until)->format('M d, Y') }}
                                            @endif
                                        </small>
                                    @else
                                        <span class="text-muted">No limit</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge-status {{ $association->is_active ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $association->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('super-admin.association-pricing.edit', $association->id) }}" 
                                       class="btn btn-action btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('super-admin.association-pricing.delete', $association->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this association? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-action btn-delete">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="fas fa-tags"></i>
                                        <h5>No associations found</h5>
                                        <p>Get started by creating your first association pricing rule.</p>
                                        <button type="button" class="btn-create mt-3" data-bs-toggle="modal" data-bs-target="#createAssociationModal">
                                            <i class="fas fa-plus me-2"></i>Create New Association
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create Association Modal -->
<div class="modal fade" id="createAssociationModal" tabindex="-1" aria-labelledby="createAssociationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAssociationModalLabel">
                    <i class="fas fa-plus me-2"></i>Create New Association Pricing Rule
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('super-admin.association-pricing.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="association_name" class="form-label">Association Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('association_name') is-invalid @enderror" 
                                   id="association_name" name="association_name" 
                                   value="{{ old('association_name') }}" required>
                            @error('association_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Unique identifier (e.g., TIESB, TIESNB)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('display_name') is-invalid @enderror" 
                                   id="display_name" name="display_name" 
                                   value="{{ old('display_name') }}" required>
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
                                   value="{{ old('promocode') }}" 
                                   placeholder="e.g., TIESB2025">
                            @error('promocode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Unique promocode (optional but recommended)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="logo" class="form-label">Logo</label>
                            <input type="file" class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" name="logo" accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">PNG, JPG, GIF, SVG (max 2MB)</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="base_price" class="form-label">Base Price (₹) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('base_price') is-invalid @enderror" 
                                   id="base_price" name="base_price" 
                                   value="{{ old('base_price', 52000) }}" required>
                            @error('base_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="special_price" class="form-label">Special Price (₹)</label>
                            <input type="number" step="0.01" class="form-control @error('special_price') is-invalid @enderror" 
                                   id="special_price" name="special_price" 
                                   value="{{ old('special_price') }}">
                            @error('special_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Discounted price (optional)</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="max_registrations" class="form-label">Max Registrations</label>
                            <input type="number" class="form-control @error('max_registrations') is-invalid @enderror" 
                                   id="max_registrations" name="max_registrations" 
                                   value="{{ old('max_registrations') }}">
                            @error('max_registrations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Leave empty for unlimited</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="valid_from" class="form-label">Valid From</label>
                            <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                                   id="valid_from" name="valid_from" 
                                   value="{{ old('valid_from') }}">
                            @error('valid_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="valid_until" class="form-label">Valid Until</label>
                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                   id="valid_until" name="valid_until" 
                                   value="{{ old('valid_until') }}">
                            @error('valid_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="entitlements" class="form-label">Entitlements</label>
                        <textarea class="form-control @error('entitlements') is-invalid @enderror" 
                                  id="entitlements" name="entitlements" rows="2">{{ old('entitlements') }}</textarea>
                        @error('entitlements')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">List of benefits/entitlements (optional)</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_complimentary" 
                                       name="is_complimentary" value="1" {{ old('is_complimentary') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_complimentary">
                                    Is Complimentary (Free Registration)
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Association
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

