@extends('layouts.dashboard')
@section('title', ucfirst($slug))
@section('content')

    <style>
        .custom-header {
            background-color: #000000;
            /* Dark header */
            color: #fff;

        }

        .custom-header a {
            color: #fff !important;
        }

        th,
        td {
            vertical-align: middle !important;
            padding: 12px 16px !important;
        }

        .table-hover tbody tr:hover {
            background-color: #f4f6f9;
            cursor: pointer;
        }

        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        }

        .search-bar {
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        #pagination {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 20px;
        }
    </style>
    <style>
        /* Laravel pagination container */
        .pagination nav {
            background-color: #eef6fb;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
            gap: 16px;
            text-align: center;
        }

        /* Center the "Showing X to Y of Z results" text */
        .pagination nav>div:first-child {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .pagination nav>div:first-child>p {
            font-size: 14px;
            color: #374151;
            margin: 0;
        }

        /* Center the page number buttons */
        .pagination nav>div:last-child>span {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        /* Page button styling */
        .pagination nav .page-link,
        .pagination nav .page-link:focus {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
            height: 36px;
            padding: 0 12px;
            font-size: 14px;
            font-weight: 500;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background-color: #ffffff;
            color: #374151;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
        }

        /* Active page button */
        .pagination nav .page-item.active .page-link {
            background-color: #0d6efd;
            color: white;
            border-color: #0d6efd;
        }

        /* Hover effect */
        .pagination nav .page-link:hover {
            background-color: #e0f2fe;
            color: #0c4a6e;
            border-color: #60a5fa;
        }

        /* Disabled arrows */
        .pagination nav .page-item.disabled .page-link {
            background-color: #f3f4f6;
            color: #9ca3af;
            cursor: not-allowed;
        }

        /* Responsive layout fix */
        @media (max-width: 576px) {
            .pagination nav {
                padding: 16px;
                gap: 12px;
            }

            .pagination nav .page-link {
                font-size: 13px;
                min-width: 34px;
                height: 34px;
                padding: 0 10px;
            }
        }

        /* Custom CSS for a modern, pill-shaped search bar with a colored button */
        .input-group.search-bar {
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            background: #fff;
            border: 1px solid #f72585;
        }

        .input-group.search-bar input.form-control {
            border: none;
            background: transparent;
            font-size: 1.15rem;
            padding: 1rem 1.25rem;
            color: #22223b;
            box-shadow: none;
        }

        .input-group.search-bar input.form-control:focus {
            background: transparent;
            box-shadow: none;
            outline: none;
        }

        .input-group.search-bar .btn {
            border-radius: 0 2rem 2rem 0;
            background: #fff;
            color: #f72585;
            border: 1.5px solid #f72585;
            font-weight: 600;
            font-size: 1.1rem;
            transition: background 0.2s, color 0.2s;
        }

        .input-group.search-bar .btn:hover,
        .input-group.search-bar .btn:focus {
            background: #f72585;
            color: #fff;
            border-color: #f72585;
        }

        @media (max-width: 576px) {
            .input-group.search-bar input.form-control {
                font-size: 1rem;
                padding: 0.75rem 1rem;
            }

            .input-group.search-bar .btn {
                font-size: 1rem;
                padding: 0.5rem 1rem;
            }
        }
    </style>


    <div class="container-fluid py-3">
        <div class="row">
            <div class="col-12">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-semibold mb-0">🧾 {{ $slug }}</h4>
                </div>

                <!-- Search Bar -->
                <div class="mb-4">
                    <form method="GET" action="{{ route('exhibitor.list') }}">
                        <div class="input-group search-bar">
                            <input type="text" name="search" class="form-control"
                                placeholder="🔍 Search name, email, company, or ID..." value="{{ request('search') }}">
                            <button class="btn btn-outline-primary px-4" type="submit">Search</button>
                        </div>
                    </form>
                </div>
                <div class="d-flex justify-content-end mb-3">
                    <form method="GET" action="{{ route('export.exhibitor') }}" class="d-flex align-items-center gap-2">
                        <input type="hidden" name="status" value="{{ request('status', 'all') }}">
                        {{-- <select name="status" class="form-select" style="width: 220px;">
                            <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                                Inaugural Guest</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>
                                Without
                                Inaugural Guest</option>
                        </select> --}}
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-download"></i> Export Attendees
                        </button>
                    </form>
                </div>
                <form id="mass-approve-form" method="POST" action="{{ route('exhibitor.mass.approve') }}">
                    @csrf
                    <input type="hidden" name="selected_ids" id="selected_ids">
                    <!-- Attendees Table -->
                    <div class="card card-custom">
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">

                                    <thead class="custom-header">
                                        <tr>
                                            <th><input type="checkbox" id="select-all"></th>
                                            <th class="custom-header">#</th>
                                            <th class="custom-header">Unique ID</th>
                                            <th class="custom-header">Contact Person</th>
                                            <th class="custom-header">Company & Designation</th>
                                            <th class="custom-header">Status</th>
                                            <th class="custom-header">Action</th>
                                            {{-- <th class="custom-header">Status</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($attendees as $index => $attendee)
                                            <tr>
                                                <td>
                                                    @if ($attendee->inauguralConfirmation == 0 && $attendee->inaugural_session == 1)
                                                        <input type="checkbox" class="select-attendee" name="attendees[]"
                                                            value="{{ $attendee->id }}">
                                                    @endif
                                                </td>
                                                <td>{{ $attendees->firstItem() + $index }}</td>
                                                <td>
                                                    <a href="{{ route('view.attendee.details', $attendee->unique_id) }}"
                                                        class="text-primary text-decoration-underline" target="_blank">
                                                        {{ rtrim($attendee->unique_id, ',') }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <div>{{ $attendee->first_name }} {{ $attendee->last_name }}</div>
                                                    <div class="text-muted">{{ $attendee->email ?? '-' }}</div>
                                                    <div class="text-muted">{{ $attendee->mobile ?? '-' }}</div>
                                                </td>
                                                <td>
                                                    <div>{{ $attendee->company ?? $attendee->organisation_name ?? '-' }} </div>
                                                    <div class="text-muted">{{ $attendee->designation ?? $attendee->job_title ?? '-' }}</div>
                                                </td>
                                                <td>
                                                    @if ($attendee->inaugural_session == 0)
                                                        <span class="badge bg-secondary">Not Applied</span>
                                                    @elseif ($attendee->inaugural_session == 1 && $attendee->inauguralConfirmation == 0)
                                                        <span class="badge bg-warning">Pending</span>
                                                    @elseif ($attendee->inauguralConfirmation == 1 && $attendee->inaugural_session == 1)
                                                        <span class="badge bg-success">Approved</span>
                                                    @else
                                                        <span class="badge bg-light text-dark">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('exhibitor.pdf', $attendee->unique_id) }}"
                                                            class="btn btn-sm btn-primary" target="_blank"
                                                            style="background-color:#1976d2;border:none;">
                                                            <i class="fas fa-eye"></i> View PDF
                                                        </a>
                                                        {{-- <a href="{{ route('mail.attendee_confirmation', $attendee->unique_id) }}"
                                                            class="btn btn-sm"
                                                            style="background-color:#ff9100;color:#fff;border:none;">
                                                            <i class="fas fa-envelope"></i> Resend Mail
                                                        </a> --}}
                                                        @if ($attendee->inauguralConfirmation == 0 && $attendee->inaugural_session == 1)
                                                            <button type="button" class="btn btn-sm approve-inaugural-btn"
                                                                style="background-color:#e63980;color:#fff;border:none;"
                                                                data-unique-id="{{ rtrim($attendee->id, ',') }}">
                                                                <i class="fas fa-check"></i> Click Here to<br>Approve for Inaugural
                                                            </button>
                                                        @endif
                                                    </div>

                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">No attendees found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="text-end mt-3">
                        <button type="submit" class="btn btn-success" id="mass-approve-btn" style="display: none;">
                            <i class="fas fa-check-circle"></i> Approve Selected
                        </button>
                    </div>
                </form>
                <!-- Hidden form for approving inaugural -->
                <form id="approve-inaugural-form" action="{{ route('exhibitor.mass.approve') }}" method="POST" style="display:none;">
                    @csrf
                    <input type="hidden" name="selected_ids" id="approve-inaugural-unique-id">
                    <input type="hidden" name="status" value="approved">
                </form>
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4 pagination">
                    {{ $attendees->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.select-attendee');
            checkboxes.forEach(cb => cb.checked = this.checked);
            toggleMassApproveButton();
        });

        document.querySelectorAll('.select-attendee').forEach(cb => {
            cb.addEventListener('change', toggleMassApproveButton);
        });

        function toggleMassApproveButton() {
            const selected = [...document.querySelectorAll('.select-attendee:checked')];
            const btn = document.getElementById('mass-approve-btn');
            btn.style.display = selected.length > 0 ? 'inline-block' : 'none';
        }

        // On form submit, gather selected IDs
        document.getElementById('mass-approve-form').addEventListener('submit', function(e) {
            const selected = [...document.querySelectorAll('.select-attendee:checked')].map(cb => cb.value);
            if (selected.length === 0) {
                e.preventDefault();
                Swal.fire('No attendees selected', 'Please select at least one attendee.', 'warning');
                return;
            }
            document.getElementById('selected_ids').value = selected.join(',');
        });

        // Approve for Inaugural button handler
        document.querySelectorAll('.approve-inaugural-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const uniqueId = this.getAttribute('data-unique-id');
                document.getElementById('approve-inaugural-unique-id').value = uniqueId;
                document.getElementById('approve-inaugural-form').submit();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
