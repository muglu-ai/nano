@extends('layouts.dashboard')
@section('title', 'Exhibitor Passes')
@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0">Exhibitor Passes</h1>
                            <p class="text-muted small">Manage and monitor all Exhibitor Passes entries</p>
                        </div>
                        {{-- <div class="btn-group">
                            <button type="button" onclick="window.print()" class="btn btn-outline-secondary">
                                <i class="fas fa-print me-2"></i>
                                Print List
                            </button>
                            <button type="button" onclick="exportToExcel()" class="btn btn-primary">
                                <i class="fas fa-file-excel me-2"></i>
                                Export Excel
                            </button>
                        </div> --}}
                    </div>

                    <!-- Search Form with Stats -->
                    <div class="card mt-4">
                        <div class="card-body">


                            <!-- Quick Stats -->
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="p-3 rounded-circle bg-opacity-10 me-3">
                                                    <i class="fas fa-users text-primary"></i>
                                                </div>
                                                <h6 class="card-subtitle fw-bold text-primary mb-0">Total Entries</h6>
                                            </div>
                                            <h2 class="card-title display-6 fw-bold mb-0">
                                                {{ number_format($totalEntries) }}</h2>
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="p-3 rounded-circle bg-success bg-opacity-10 me-3">
                                                <i class="fas fa-calendar-day text-success"></i>
                                            </div>
                                            <h6 class="card-subtitle fw-bold text-success mb-0">Today's Entries</h6>
                                        </div>
                                        <h2 class="card-title display-6 fw-bold mb-0>{{ number_format($stallManningList->where('created_at', '>=', \Carbon\Carbon::today())->count()) }}</h2>
                                    </div>
                                </div>
                            </div> --}}
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="p-3 rounded-circle bg-opacity-10 me-3">
                                                    <i class="fas fa-building text-info"></i>
                                                </div>
                                                <h6 class="card-subtitle fw-bold text-info mb-0">Companies</h6>
                                            </div>
                                            <h2 class="card-title display-6 fw-bold mb-0">{{ number_format($totalCompanyCount) }}
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card border-0 shadow-sm h-100">
                                        <div class="card-body p-4">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="p-3 bg-opacity-10 me-3">
                                                    <i class="fas fa-ticket-alt text-success"></i>
                                                </div>
                                                <h6 class="card-subtitle fw-bold text-success mb-0">Inaugural Passes</h6>
                                            </div>
                                            <h2 class="card-title display-6 fw-bold mb-0">{{ number_format($inauguralApplied) }}</h2>
                                            <p class="text-muted small mt-1 mb-0">Total passes applied</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- <div class="col-md-3">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="p-3 rounded-circle bg-warning bg-opacity-10 me-3">
                                                <i class="fas fa-search text-warning"></i>
                                            </div>
                                            <h6 class="card-subtitle fw-bold text-warning mb-0">Search Results</h6>
                                        </div>
                                        <h2 class="card-title display-6 fw-bold mb-0">{{ number_format($stallManningList->count()) }}</h2>
                                    </div>
                                </div>
                            </div> --}}
                            </div>
                        </div>
                    </div>
                    </header>

                    <!-- Results Table -->
                    <div class="card mt-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div>
                                    <!-- You can add a title or other content here if needed -->
                                </div>
                                <form id="searchForm" action="{{ route('admin.stall-manning') }}" method="GET" style="width: 350px;">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text border-end-0 bg-white">
                                            <i class="fas fa-search text-muted"></i>
                                        </span>
                                        <input type="text"
                                            name="search"
                                            id="searchInput"
                                            class="form-control border-start-0 ps-0"
                                            placeholder="Search by name, email, phone or company"
                                            value="{{ request('search') }}"
                                            style="border-radius: 0 8px 8px 0;">
                                    </div>
                                </form>
                            </div>
                            <div id="searchResults" class="table-responsive">
                                @include('admin.stall-manning.table-content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @push('scripts')
            <script>
                $(document).ready(function() {
                    let searchTimer;
                    let currentRequest = null;

                    $('#searchInput').on('input', function() {
                        clearTimeout(searchTimer);

                        searchTimer = setTimeout(() => {
                            const searchQuery = $(this).val();

                            // Abort previous request if it exists
                            if (currentRequest) {
                                currentRequest.abort();
                            }

                            // Start new request
                            currentRequest = $.ajax({
                                url: "{{ route('admin.stall-manning') }}",
                                method: 'GET',
                                data: {
                                    search: searchQuery,
                                    ajax: true
                                },
                                beforeSend: function() {
                                    $('#searchResults').addClass('opacity-50');
                                },
                                success: function(response) {
                                    $('#searchResults').html(response);

                                    // Update URL with search parameter
                                    const url = new URL(window.location);
                                    if (searchQuery) {
                                        url.searchParams.set('search', searchQuery);
                                    } else {
                                        url.searchParams.delete('search');
                                    }
                                    window.history.pushState({}, '', url);
                                },
                                error: function() {
                                    // Handle error if needed
                                },
                                complete: function() {
                                    $('#searchResults').removeClass('opacity-50');
                                    currentRequest = null;
                                }
                            });
                        }, 300); // 300ms delay to prevent too many requests
                    });

                    // Handle pagination clicks
                    $(document).on('click', '.pagination a', function(e) {
                        e.preventDefault();
                        let page = $(this).attr('href').split('page=')[1];
                        let searchQuery = $('#searchInput').val();

                        $.ajax({
                            url: "{{ route('admin.stall-manning') }}",
                            method: 'GET',
                            data: {
                                search: searchQuery,
                                page: page,
                                ajax: true
                            },
                            beforeSend: function() {
                                $('#searchResults').addClass('opacity-50');
                            },
                            success: function(response) {
                                $('#searchResults').html(response);
                                window.scrollTo(0, 0);
                            },
                            complete: function() {
                                $('#searchResults').removeClass('opacity-50');
                            }
                        });
                    });
                });
            </script>
            @endpush
        @endsection
