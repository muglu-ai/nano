@include('layouts.dashboard')

<style>
    .feedback-analytics-card {
        border-radius: 18px;
        border: none;
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.12);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }

    .rating-badge {
        font-weight: 600;
        border-radius: 30px;
        padding: 0.35rem 0.85rem;
    }

    .table-shadow {
        border-radius: 18px;
        overflow: hidden;
        border: none;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.12);
    }

    .filter-panel {
        border-radius: 18px;
        border: none;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0 text-primary">Exhibitor Feedback & Analytics</h2>
            <p class="text-muted mb-0">Real-time insights from the exhibitor feedback portal</p>
        </div>
        <div>
            <a href="{{ route('feedback.show') }}" class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-up-right"></i> Public Feedback Form
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card feedback-analytics-card h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Submissions</p>
                    <h3 class="fw-bold">{{ number_format($stats['total_submissions']) }}</h3>
                    @if($stats['latest_submission'])
                        <small class="text-muted">Last entry {{ $stats['latest_submission']->created_at->diffForHumans() }}</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card feedback-analytics-card h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Avg. Event Rating</p>
                    <h3 class="fw-bold">{{ $stats['avg_event_rating'] }} / 5</h3>
                    <small class="text-muted">Overall onsite experience</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card feedback-analytics-card h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Avg. Portal Rating</p>
                    <h3 class="fw-bold">{{ $stats['avg_portal_rating'] }} / 5</h3>
                    <small class="text-muted">Portal usability</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card feedback-analytics-card h-100">
                <div class="card-body">
                    <p class="text-muted mb-1">Avg. Overall Rating</p>
                    <h3 class="fw-bold">{{ $stats['avg_overall_rating'] }} / 5</h3>
                    <small class="text-muted">End-to-end experience</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card filter-panel">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label text-muted">Search</label>
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control"
                                   placeholder="Name, email or company">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted">Event Rating</label>
                            <select name="event_rating" class="form-select">
                                <option value="">All</option>
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" @selected(($filters['event_rating'] ?? '') == $i)>{{ $i }} ★</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted">Recommend</label>
                            <select name="would_recommend" class="form-select">
                                <option value="">All</option>
                                <option value="yes" @selected(($filters['would_recommend'] ?? '') === 'yes')>Yes</option>
                                <option value="maybe" @selected(($filters['would_recommend'] ?? '') === 'maybe')>Maybe</option>
                                <option value="no" @selected(($filters['would_recommend'] ?? '') === 'no')>No</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted">From</label>
                            <input type="date" name="date_from" class="form-control"
                                   value="{{ $filters['date_from'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted">To</label>
                            <input type="date" name="date_to" class="form-control"
                                   value="{{ $filters['date_to'] ?? '' }}">
                        </div>
                        <div class="col-md-1">
                            <label class="form-label text-muted">Per Page</label>
                            <select name="per_page" class="form-select">
                                @foreach([10,15,25,50] as $limit)
                                    <option value="{{ $limit }}" @selected(($filters['per_page'] ?? 15) == $limit)>{{ $limit }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2 justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel"></i> Apply Filters
                            </button>
                            <a href="{{ route('admin.feedback.index') }}" class="btn btn-outline-secondary">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card feedback-analytics-card h-100">
                <div class="card-body">
                    <p class="text-muted mb-2">Recommendation Sentiment</p>
                    <div class="d-flex gap-3">
                        <div>
                            <h4 class="mb-0 text-success">{{ $stats['recommend_yes'] }}</h4>
                            <small class="text-muted">Yes</small>
                        </div>
                        <div>
                            <h4 class="mb-0 text-warning">{{ $stats['recommend_maybe'] }}</h4>
                            <small class="text-muted">Maybe</small>
                        </div>
                        <div>
                            <h4 class="mb-0 text-danger">{{ $stats['recommend_no'] }}</h4>
                            <small class="text-muted">No</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100 table-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Event Rating Distribution</h5>
                    </div>
                    <canvas id="ratingDistributionChart" height="220"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card h-100 table-shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Submission Trend (last 14 days)</h5>
                    </div>
                    <canvas id="submissionTrendChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-shadow">
        <div class="card-body table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr class="text-muted">
                        <th>#</th>
                        <th>Name & Company</th>
                        <th>Ratings</th>
                        <th>Contact</th>
                        <th>Recommendation</th>
                        <th>Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feedback as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($feedback->currentPage() - 1) * $feedback->perPage() }}</td>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                <div class="text-muted small">{{ $item->company_name ?? '—' }}</div>
                            </td>
                            <td>
                                <span class="rating-badge bg-primary-subtle text-primary me-1">
                                    Event: {{ $item->event_rating }} ★
                                </span>
                                <span class="rating-badge bg-info-subtle text-info me-1">
                                    Portal: {{ $item->portal_rating }} ★
                                </span>
                                @if($item->overall_experience_rating)
                                    <span class="rating-badge bg-success-subtle text-success">
                                        Overall: {{ $item->overall_experience_rating }} ★
                                    </span>
                                @endif
                            </td>
                            <td>
                                <a href="mailto:{{ $item->email }}">{{ $item->email }}</a>
                                <div class="text-muted small">{{ $item->phone ?? '—' }}</div>
                            </td>
                            <td>
                                @php
                                    $badgeClass = match($item->would_recommend) {
                                        'yes' => 'bg-success-subtle text-success',
                                        'no' => 'bg-danger-subtle text-danger',
                                        'maybe' => 'bg-warning-subtle text-warning',
                                        default => 'bg-secondary-subtle text-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }} text-uppercase">
                                    {{ $item->would_recommend ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <div>{{ $item->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $item->created_at->format('H:i A') }}</small>
                            </td>
                        </tr>
                        @if($item->what_liked_most || $item->what_could_be_improved || $item->additional_comments)
                            <tr class="table-light">
                                <td></td>
                                <td colspan="5">
                                    @if($item->what_liked_most)
                                        <div class="mb-2">
                                            <strong class="text-success">Highlights:</strong>
                                            <span>{{ $item->what_liked_most }}</span>
                                        </div>
                                    @endif
                                    @if($item->what_could_be_improved)
                                        <div class="mb-2">
                                            <strong class="text-warning">Improvements:</strong>
                                            <span>{{ $item->what_could_be_improved }}</span>
                                        </div>
                                    @endif
                                    @if($item->additional_comments)
                                        <div>
                                            <strong class="text-primary">Additional Notes:</strong>
                                            <span>{{ $item->additional_comments }}</span>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                No feedback found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $feedback->links() }}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ratingLabels = @json(array_keys($ratingDistribution));
    const ratingData = @json(array_values($ratingDistribution));

    new Chart(document.getElementById('ratingDistributionChart'), {
        type: 'bar',
        data: {
            labels: ratingLabels.map(label => `${label} ★`),
            datasets: [{
                label: 'Responses',
                data: ratingData,
                backgroundColor: '#3b82f6',
                borderRadius: 8
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });

    const trendLabels = @json($trendData->pluck('date'));
    const trendValues = @json($trendData->pluck('total'));

    new Chart(document.getElementById('submissionTrendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Submissions',
                data: trendValues,
                borderColor: '#10b981',
                fill: true,
                backgroundColor: 'rgba(16, 185, 129, 0.2)',
                tension: 0.4
            }]
        },
        options: {
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { precision: 0 } }
            }
        }
    });
</script>

