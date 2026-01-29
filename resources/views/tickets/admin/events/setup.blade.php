@extends('layouts.dashboard')

@section('title', 'Ticket Setup - ' . $event->event_name)

@section('content')
<style>
    .setup-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .setup-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
        margin-bottom: 2rem;
    }
    
    .setup-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.75rem 2rem;
        border: none;
    }
    
    .setup-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .setup-card-body {
        padding: 2rem;
    }
    
    .progress-section {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: 1px solid #e9ecef;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }
    
    .progress-section h6 {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1rem;
        font-size: 1rem;
    }
    
    .progress {
        height: 28px;
        border-radius: 6px;
        background: #e2e8f0;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.08);
        position: relative;
    }
    
    .progress-bar {
        background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.875rem;
        color: white;
        transition: width 0.6s ease;
        height: 100%;
        border-radius: 6px;
    }
    
    .progress-info {
        margin-top: 0.75rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #718096;
    }
    
    .progress-info i {
        color: #718096;
        font-size: 1rem;
    }
    
    .progress-info.text-muted {
        color: #718096;
    }
    
    .progress-info.text-success {
        color: #38a169;
        font-weight: 500;
    }
    
    .progress-info.text-success i {
        color: #38a169;
    }
    
    .nav-tabs-wrapper {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 0.5rem;
        margin-bottom: 2rem;
    }
    
    .nav-pills {
        border: none;
        gap: 0.5rem;
    }
    
    .nav-pills .nav-link {
        border-radius: 8px;
        padding: 0.75rem 1.25rem;
        color: #4a5568;
        font-weight: 500;
        background: transparent;
        border: 2px solid transparent;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .nav-pills .nav-link:hover {
        background: #e2e8f0;
        color: #2d3748;
        border-color: #cbd5e0;
    }
    
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    
    .tab-content {
        min-height: 400px;
    }
    
    .tab-pane {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .btn-manage {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }
    
    .btn-manage:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        color: white;
    }
</style>

<div class="setup-container">
    <div class="setup-card">
        <div class="setup-card-header">
            <h4>
                <i class="fas fa-cog"></i>
                Ticket Configuration Setup - {{ $event->event_name }}
            </h4>
        </div>
        <div class="setup-card-body">
            <!-- Progress Section -->
            <div class="progress-section">
                <h6 style="font-weight: 600; color: #2d3748; margin-bottom: 1rem;">Setup Progress</h6>
                <div class="progress">
                    <div class="progress-bar" 
                         role="progressbar" 
                         style="width: {{ $progress['percentage'] }}%"
                         aria-valuenow="{{ $progress['percentage'] }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        {{ $progress['completed'] }}/{{ $progress['total'] }} Complete ({{ number_format($progress['percentage']) }}%)
                    </div>
                </div>
                @if(!$progress['is_complete'])
                    <p class="progress-info text-muted" style="margin-top: 0.75rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-info-circle" style="font-size: 1rem;"></i>
                        Complete all setup steps to enable public registration.
                    </p>
                @else
                    <p class="progress-info text-success" style="margin-top: 0.75rem; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; color: #38a169; font-weight: 500;">
                        <i class="fas fa-check-circle" style="font-size: 1rem; color: #38a169;"></i>
                        Setup complete! Public registration is enabled.
                    </p>
                @endif
            </div>

            <!-- Setup Steps Tabs -->
            <div class="nav-tabs-wrapper">
                <ul class="nav nav-pills nav-fill" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#config" role="tab" aria-controls="config" aria-selected="true">
                            <i class="fas fa-cog"></i>
                            <span>Configuration</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#days" role="tab" aria-controls="days" aria-selected="false">
                            <i class="fas fa-calendar"></i>
                            <span>Event Days</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#reg-categories" role="tab" aria-controls="reg-categories" aria-selected="false">
                            <i class="fas fa-tags"></i>
                            <span>Registration Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#categories" role="tab" aria-controls="categories" aria-selected="false">
                            <i class="fas fa-list"></i>
                            <span>Ticket Categories</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#ticket-types" role="tab" aria-controls="ticket-types" aria-selected="false">
                            <i class="fas fa-ticket-alt"></i>
                            <span>Ticket Types</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#rules" role="tab" aria-controls="rules" aria-selected="false">
                            <i class="fas fa-link"></i>
                            <span>Rules</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Configuration Tab -->
                <div class="tab-pane fade show active" id="config" role="tabpanel">
                    @include('tickets.admin.events.partials.config-form')
                </div>

                <!-- Event Days Tab -->
                <div class="tab-pane fade" id="days" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <h5 class="mb-3">Manage Event Days</h5>
                        <p class="text-muted mb-4">Configure the days for this event</p>
                        <a href="{{ route('admin.tickets.events.days', $event->id) }}" class="btn btn-manage">
                            <i class="fas fa-calendar me-2"></i>Manage Event Days
                        </a>
                    </div>
                </div>

                <!-- Registration Categories Tab -->
                <div class="tab-pane fade" id="reg-categories" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="mb-3">Manage Registration Categories</h5>
                        <p class="text-muted mb-4">Set up registration categories for this event</p>
                        <a href="{{ route('admin.tickets.events.registration-categories', $event->id) }}" class="btn btn-manage">
                            <i class="fas fa-tags me-2"></i>Manage Registration Categories
                        </a>
                    </div>
                </div>

                <!-- Ticket Categories Tab -->
                <div class="tab-pane fade" id="categories" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="fas fa-list fa-3x text-muted mb-3"></i>
                        <h5 class="mb-3">Manage Ticket Categories</h5>
                        <p class="text-muted mb-4">Organize tickets into categories</p>
                        <a href="{{ route('admin.tickets.events.categories', $event->id) }}" class="btn btn-manage">
                            <i class="fas fa-list me-2"></i>Manage Ticket Categories
                        </a>
                    </div>
                </div>

                <!-- Ticket Types Tab -->
                <div class="tab-pane fade" id="ticket-types" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                        <h5 class="mb-3">Manage Ticket Types</h5>
                        <p class="text-muted mb-4">Create and configure ticket types</p>
                        <a href="{{ route('admin.tickets.events.ticket-types', $event->id) }}" class="btn btn-manage">
                            <i class="fas fa-ticket-alt me-2"></i>Manage Ticket Types
                        </a>
                    </div>
                </div>

                <!-- Rules Tab -->
                <div class="tab-pane fade" id="rules" role="tabpanel">
                    <div class="text-center py-5">
                        <i class="fas fa-link fa-3x text-muted mb-3"></i>
                        <h5 class="mb-3">Manage Ticket Rules</h5>
                        <p class="text-muted mb-4">Configure ticket combination rules</p>
                        <a href="{{ route('admin.tickets.events.rules', $event->id) }}" class="btn btn-manage">
                            <i class="fas fa-link me-2"></i>Manage Ticket Rules
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Smooth tab transitions
    document.querySelectorAll('a[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function () {
            // Optional: Update progress without full reload
            // You can add AJAX call here to refresh progress
        });
    });
</script>
@endsection

