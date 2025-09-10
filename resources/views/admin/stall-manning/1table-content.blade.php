{{-- @include('layouts.includes.messages') --}}
<table class="table table-hover" id="datatable-basic3">
    <thead>
        <tr>
            <th style="width: 200px; white-space: normal;">Name</th>
            <th style="width: 220px; white-space: normal;">Contact Details</th>
            <th style="width: 180px; white-space: normal;">Company</th>
            <th style="width: 170px; white-space: normal;">Registration Date</th>
            <th class="" style="width: 120px; white-space: normal;">Type</th>
        </tr>
    </thead>
    <tbody>
        @forelse($stallManningList as $entry)
            @php
                if (empty($entry->full_name)) {
                    continue;
                }
            @endphp
            <tr>
                <td style="width: 200px; white-space: normal; word-break: break-word;">
                    <div class="d-flex align-items-center">
                        <div class="bg-opacity-10 rounded-circle p-2 me-3">
                            <span class="text-primary">{{ strtoupper(substr($entry->first_name, 0, 1)) }}</span>
                        </div>
                        <div>
                            <div>{{ $entry->full_name }}</div>
                        </div>
                    </div>
                </td>
                <td style="width: 220px; white-space: normal; word-break: break-word;">
                    <div>{{ $entry->email }}</div>
                    <small class="text-muted">{{ $entry->mobile }}</small>
                </td>
                <td style="width: 180px; white-space: normal; word-break: break-word;">
                    <span class="text-muted">{{ $entry->company_name }}</span>
                </td>
                <td style="width: 170px; white-space: normal; word-break: break-word;">
                    <div>{{ $entry->created_at->format('M d, Y') }}</div>
                    <small class="text-muted">{{ $entry->created_at->format('h:i A') }}</small>
                </td>
                <td class="text-end" style="width: 120px; white-space: normal; word-break: break-word;">
                    <span class="text-secondary">{{ $entry->pass_type }}</span>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-5">
                    <div class="text-center">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No records found</p>
                    </div>
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<!-- Pagination -->
<div class="d-flex justify-content-between align-items-center mt-4">
    <div class="text-muted">
        Showing {{ $stallManningList->firstItem() ?? 0 }} to {{ $stallManningList->lastItem() ?? 0 }} of {{ $stallManningList->total() }} entries
    </div>
    <div>
        {{ $stallManningList->links() }}
    </div>
</div>
