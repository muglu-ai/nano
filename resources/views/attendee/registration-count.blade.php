@extends('layouts.dashboard')

@section('title', 'Registration Count Dashboard')

@section('content')
<style>
    .dashboard-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
    }
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2);
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
        text-align: center;
    }
    
    .stat-label {
        font-size: 1.1rem;
        color: #6c757d;
        text-align: center;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .stat-percentage {
        font-size: 1.2rem;
        font-weight: 600;
        color: #28a745;
        text-align: center;
        margin-top: 10px;
    }
    
    .breakdown-section {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        margin-bottom: 20px;
    }
    
    .breakdown-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 20px;
        text-align: center;
    }
    
    .breakdown-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
    }
    
    .breakdown-item {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        border-left: 4px solid #667eea;
    }
    
    .breakdown-item .count {
        font-size: 1.8rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 5px;
    }
    
    .breakdown-item .label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .breakdown-item .percentage {
        font-size: 1rem;
        color: #28a745;
        font-weight: 600;
        margin-top: 5px;
    }
    
    .total-card {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        text-align: center;
    }
    
    .total-card .stat-number {
        color: white;
        font-size: 4rem;
    }
    
    .total-card .stat-label {
        color: rgba(255,255,255,0.9);
        font-size: 1.3rem;
    }
    
    .refresh-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 15px 20px;
        font-size: 1.1rem;
        font-weight: 600;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .refresh-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
    }
    
    .last-updated {
        text-align: center;
        margin-top: 20px;
    }
    
    .last-updated-content {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 500;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        transition: all 0.3s ease;
    }
    
    .last-updated-content:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    .last-updated-content i {
        color: #ffd700;
        font-size: 1rem;
    }
    
    .no-data {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 20px;
    }
</style>

<div class="dashboard-container">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="text-white text-center mb-3">
                    <i class="fas fa-chart-bar me-3"></i>Registration Count Dashboard
                </h2>
                <p class="text-white text-center mb-0">Real-time registration statistics and breakdowns</p>
            </div>
        </div>

        <!-- Main Statistics -->
        <div class="stats-grid">
            <!-- Exhibitor Passes (Stall Manning) -->
            <div class="stat-card">
                <div class="stat-number">{{ number_format($exhibitorPasses) }}</div>
                <div class="stat-label">Exhibitor Passes</div>
                <div class="stat-percentage">
                    {{ $totalRegistration > 0 ? number_format(($exhibitorPasses / $totalRegistration) * 100, 1) : 0 }}%
                </div>
            </div>

            <!-- Complimentary Delegates -->
            <div class="stat-card">
                <div class="stat-number">{{ number_format($totalComplimentaryDelegates) }}</div>
                <div class="stat-label">Complimentary Delegates</div>
                <div class="stat-percentage">
                    {{ $totalRegistration > 0 ? number_format(($totalComplimentaryDelegates / $totalRegistration) * 100, 1) : 0 }}%
                </div>
            </div>

            <!-- Ticket Allocations -->
            {{-- <div class="stat-card">
                <div class="stat-number">{{ number_format($ticketAllocations) }}</div>
                <div class="stat-label">Ticket Allocations</div>
                <div class="stat-percentage">
                    {{ $totalRegistration > 0 ? number_format(($ticketAllocations / $totalRegistration) * 100, 1) : 0 }}%
                </div>
            </div> --}}

            <!-- Total Registration -->
            <div class="stat-card total-card">
                <div class="stat-number">{{ number_format($totalRegistration) }}</div>
                <div class="stat-label">Total Registration</div>
            </div>
        </div>

        <!-- Ticket Type Breakdown -->
        @if($allTicketTypes && count($allTicketTypes) > 0)
        <div class="breakdown-section">
            <h3 class="breakdown-title">
                <i class="fas fa-ticket-alt me-2"></i>Ticket Type Breakdown
            </h3>
            <div class="breakdown-grid">
                @foreach($allTicketTypes as $ticketType => $count)
                <div class="breakdown-item">
                    <div class="count">{{ number_format($count) }}</div>
                    <div class="label">{{ $ticketType }}</div>
                    <div class="percentage">
                        {{ $totalRegistration > 0 ? number_format(($count / $totalRegistration) * 100, 1) : 0 }}%
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="breakdown-section">
            <div class="no-data">
                <i class="fas fa-info-circle me-2"></i>No ticket data found
            </div>
        </div>
        @endif

        <!-- Debug Information -->
        {{-- <div class="breakdown-section">
            <h3 class="breakdown-title">
                <i class="fas fa-bug me-2"></i>Debug Information
            </h3>
            <div class="row">
                <div class="col-md-6">
                    <h5>Raw Counts:</h5>
                    <ul>
                        <li>Exhibitor Passes: {{ $exhibitorPasses }}</li>
                        <li>Complimentary Delegates: {{ $totalComplimentaryDelegates }}</li>
                        <li>Ticket Allocations: {{ $ticketAllocations }}</li>
                        <li>Total Registration: {{ $totalRegistration }}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5>Ticket Breakdown Totals:</h5>
                    <ul>
                        @if($stallManningBreakdown && count($stallManningBreakdown) > 0)
                            <li><strong>Stall Manning by Type:</strong></li>
                            @foreach($stallManningBreakdown as $type => $data)
                                <li>{{ $type }}: {{ $data->count }}</li>
                            @endforeach
                        @endif
                        @if($complimentaryBreakdown && count($complimentaryBreakdown) > 0)
                            <li><strong>Complimentary by Type:</strong></li>
                            @foreach($complimentaryBreakdown as $type => $data)
                                <li>{{ $type }}: {{ $data->count }}</li>
                            @endforeach
                        @endif
                    </ul>
                    <p><strong>Combined Total: {{ $allTicketTypes->sum() }}</strong></p>
                    <p><strong>Missing from Breakdown: {{ $totalRegistration - $allTicketTypes->sum() }}</strong></p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <h5>Data Quality Check:</h5>
                    <p>If there's a mismatch, it likely means some records don't have ticket types assigned. Check the logs for detailed breakdown.</p>
                </div>
            </div>
        </div> --}}

        <!-- Last Updated -->
        <div class="last-updated">
            <div class="last-updated-content">
                <i class="fas fa-clock me-2"></i>
                <span>Last updated: {{ now()->format('M d, Y \a\t h:i A') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Refresh Button -->
<button class="refresh-btn" onclick="refreshData()">
    <i class="fas fa-sync-alt me-2"></i>Refresh Data
</button>

<script>
function refreshData() {
    // Show loading state
    const btn = document.querySelector('.refresh-btn');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Refreshing...';
    btn.disabled = true;
    
    // Fetch updated data via AJAX
    fetch('{{ route("api.registration.count") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateDashboard(data.data);
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        // Fallback to page reload
        window.location.reload();
    })
    .finally(() => {
        // Reset button state
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}

function updateDashboard(data) {
    // Update main statistics
    document.querySelector('.stat-card:nth-child(1) .stat-number').textContent = formatNumber(data.exhibitor_passes);
    document.querySelector('.stat-card:nth-child(1) .stat-percentage').textContent = 
        data.total_registration > 0 ? formatPercentage((data.exhibitor_passes / data.total_registration) * 100) + '%' : '0%';
    
    document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = formatNumber(data.complimentary_delegates);
    document.querySelector('.stat-card:nth-child(2) .stat-percentage').textContent = 
        data.total_registration > 0 ? formatPercentage((data.complimentary_delegates / data.total_registration) * 100) + '%' : '0%';
    
    document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = formatNumber(data.ticket_allocations);
    document.querySelector('.stat-card:nth-child(3) .stat-percentage').textContent = 
        data.total_registration > 0 ? formatPercentage((data.ticket_allocations / data.total_registration) * 100) + '%' : '0%';
    
    document.querySelector('.stat-card:nth-child(4) .stat-number').textContent = formatNumber(data.total_registration);
    
    // Update ticket breakdown
    const breakdownContainer = document.querySelector('.breakdown-grid');
    if (data.all_ticket_types && Object.keys(data.all_ticket_types).length > 0) {
        breakdownContainer.innerHTML = '';
        Object.entries(data.all_ticket_types).forEach(([ticketType, count]) => {
            const percentage = data.total_registration > 0 ? (count / data.total_registration) * 100 : 0;
            breakdownContainer.innerHTML += `
                <div class="breakdown-item">
                    <div class="count">${formatNumber(count)}</div>
                    <div class="label">${ticketType}</div>
                    <div class="percentage">${formatPercentage(percentage)}%</div>
                </div>
            `;
        });
    } else {
        breakdownContainer.innerHTML = '<div class="no-data"><i class="fas fa-info-circle me-2"></i>No ticket data found</div>';
    }
    
    // Update last updated time
    document.querySelector('.last-updated').innerHTML = 
        `<i class="fas fa-clock me-2"></i>Last updated: ${data.last_updated}`;
    
    // Add update animation
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach(card => {
        card.style.transform = 'scale(1.05)';
        setTimeout(() => {
            card.style.transform = 'scale(1)';
        }, 200);
    });
}

function formatNumber(num) {
    return new Intl.NumberFormat().format(num);
}

function formatPercentage(num) {
    return num.toFixed(1);
}

// Auto-refresh every 2 minutes
setInterval(() => {
    refreshData();
}, 120000);

// Add some animation on load
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.stat-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection
