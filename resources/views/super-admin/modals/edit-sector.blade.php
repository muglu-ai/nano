<div class="modal fade" id="editSectorModal{{ $sector->id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('super-admin.sectors.update', $sector->id) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sector</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Sector Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="{{ $sector->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="{{ $sector->sort_order }}" min="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="sectorActive{{ $sector->id }}" {{ $sector->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="sectorActive{{ $sector->id }}">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Sector</button>
                </div>
            </form>
        </div>
    </div>
</div>
