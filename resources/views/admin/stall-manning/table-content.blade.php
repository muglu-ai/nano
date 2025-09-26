{{-- @include('layouts.includes.messages') --}}
<table class="table table-hover" id="datatable-basic3">
    <thead>
    <tr>
        <th style="width: 170px; white-space: normal; text-align: center;">Registration Date</th>
        <th style="width: 50px; white-space: normal; text-align: center;">TIN No</th>
        <th style="width: 200px; white-space: normal; text-align: center;">Name</th>
        <th style="width: 220px; white-space: normal; text-align: center;">Contact Details</th>
        <th style="width: 180px; white-space: normal; text-align: center;">Company</th>
        <th style="width: 120px; white-space: normal; text-align: center;">Type</th>
        <th style="width: 100px; white-space: normal; text-align: center;">Actions</th>
    </tr>
    </thead>
    <tbody>
    @forelse($stallManningList as $entry)
        @php
            // dd($entry);
                if (empty($entry->first_name)) {
                    continue;
                }
                $entry->full_name = trim("{$entry->first_name} {$entry->middle_name} {$entry->last_name}");
                $entry->company_name = $entry->organisation_name ?: ($entry->exhibitionParticipant->coExhibitor->company_name ?? 'N/A');
        @endphp
        <tr>
            <td style="width: 170px; white-space: normal; word-break: break-word;">
                <div>{{ $entry->created_at->format('M d, Y') }}</div>
                <small class="text-muted">{{ $entry->created_at->format('h:i A') }}</small>
            </td>
            <td style="width: 50px; white-space: normal;">{{ $entry->unique_id }}</td>
            <td style="width: 200px; white-space: normal; word-break: break-word;">
                <div class="d-flex align-items-center">
                    <div class="bg-opacity-10 rounded-circle p-2 me-3">
                        <span class="text-primary">{{ strtoupper(substr($entry->first_name, 0, 1)) }}</span>
                    </div>
                    <div>
                        <div>
                            {{ $entry->full_name }}
                            <a href="{{ route('exhibitor.pdf', ['id' => $entry->unique_id]) }}" target="_blank"
                               class="ms-2 text-primary" title="View PDF">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                        </div>
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

            <td class="text-end" style="width: 120px; white-space: normal; word-break: break-word;">
                <span class="text-secondary">{{ $entry->pass_type }}</span>
            </td>
            <td class="text-end" style="width: 120px; white-space: normal; word-break: break-word;">
                <a href="{{ route('mail.exhibitor_confirmation', ['id' => $entry->unique_id]) }}"
                   class="btn btn-link btn-sm text-primary ms-2"
                   title="Resend Confirmation Email">
                    <i class="fas fa-envelope"></i> Resend Email
                </a>
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
        Showing {{ $stallManningList->firstItem() ?? 0 }} to {{ $stallManningList->lastItem() ?? 0 }}
        of {{ $stallManningList->total() }} entries
    </div>
    <div>
        {{ $stallManningList->links() }}
    </div>
</div>
