<div class="modal fade" id="editOrgTypeModal{{ $orgType->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('super-admin.org-types.update', $orgType->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit Organization Type</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Organization Type Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $orgType->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="{{ $orgType->sort_order }}" min="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="orgTypeActive{{ $orgType->id }}" {{ $orgType->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="orgTypeActive{{ $orgType->id }}">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Organization Type</button>
                </div>
            </form>
        </div>
    </div>
</div>
