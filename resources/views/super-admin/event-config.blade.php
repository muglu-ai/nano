@extends('layouts.app')

@section('title', 'Event Configuration - Super Admin')

@section('content')
<style>
    .super-admin-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .config-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
    }
    
    .config-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        border: none;
    }
    
    .config-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .config-card-header i {
        font-size: 1.25rem;
    }
    
    .config-card-body {
        padding: 2rem;
    }
    
    .form-section {
        margin-bottom: 2.5rem;
    }
    
    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1.5rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-label {
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .form-control {
        border: 1px solid #cbd5e0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
        font-size: 0.95rem;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .form-control::placeholder {
        color: #a0aec0;
    }
    
    .text-danger {
        color: #e53e3e;
        font-weight: 600;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        color: white;
        transition: all 0.3s;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-save:active {
        transform: translateY(0);
    }
    
    .alert-success {
        border-radius: 8px;
        border: none;
        background: #f0fff4;
        color: #22543d;
        border-left: 4px solid #48bb78;
    }
    
    .section-divider {
        border: none;
        height: 1px;
        background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        margin: 2rem 0;
    }
    
    .input-group-custom {
        position: relative;
    }
    
    .input-group-custom .form-control {
        padding-right: 2.5rem;
    }
    
    @media (max-width: 768px) {
        .super-admin-container {
            padding: 1rem 0.5rem;
        }
        
        .config-card-body {
            padding: 1.5rem;
        }
        
        .form-section-title {
            font-size: 1.1rem;
        }
    }
</style>

<div class="super-admin-container">
    <div class="config-card">
        <div class="config-card-header">
            <div class="d-flex justify-content-between align-items-center w-100">
                <h4>
                    <i class="fas fa-cog"></i>
                    Event Configuration
                </h4>
                <a href="{{ route('super-admin.events') }}" class="btn btn-light">
                    <i class="fas fa-calendar-alt me-2"></i>Manage Events
                </a>
            </div>
        </div>
        <div class="config-card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('super-admin.event-config.update') }}">
                @csrf
                
                <!-- Event Basic Information -->
                <div class="form-section">
                    <h5 class="form-section-title">
                        <i class="fas fa-calendar-alt"></i>
                        Event Information
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                Event Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="event_name" 
                                   value="{{ old('event_name', $config->event_name ?? '') }}" 
                                   required placeholder="Enter event name">
                            @error('event_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                Event Year <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="event_year" 
                                   value="{{ old('event_year', $config->event_year ?? '') }}" 
                                   required placeholder="e.g., 2025">
                            @error('event_year')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">
                                Short Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="short_name" 
                                   value="{{ old('short_name', $config->short_name ?? '') }}" 
                                   required placeholder="e.g., BTS">
                            @error('short_name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Event Website</label>
                            <input type="url" class="form-control" name="event_website" 
                                   value="{{ old('event_website', $config->event_website ?? '') }}"
                                   placeholder="https://example.com">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Event Start Date</label>
                            <input type="text" class="form-control" name="event_date_start" 
                                   value="{{ old('event_date_start', $config->event_date_start ?? '') }}" 
                                   placeholder="DD-MM-YYYY">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Event End Date</label>
                            <input type="text" class="form-control" name="event_date_end" 
                                   value="{{ old('event_date_end', $config->event_date_end ?? '') }}" 
                                   placeholder="DD-MM-YYYY">
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label">Event Venue</label>
                            <textarea class="form-control" name="event_venue" rows="3" 
                                      placeholder="Enter full venue address">{{ old('event_venue', $config->event_venue ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Organizer Information -->
                <div class="form-section">
                    <h5 class="form-section-title">
                        <i class="fas fa-building"></i>
                        Organizer Information
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Organizer Name</label>
                            <input type="text" class="form-control" name="organizer_name" 
                                   value="{{ old('organizer_name', $config->organizer_name ?? '') }}"
                                   placeholder="Organization name">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Organizer Email</label>
                            <input type="email" class="form-control" name="organizer_email" 
                                   value="{{ old('organizer_email', $config->organizer_email ?? '') }}"
                                   placeholder="contact@example.com">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Organizer Phone</label>
                            <input type="text" class="form-control" name="organizer_phone" 
                                   value="{{ old('organizer_phone', $config->organizer_phone ?? '') }}"
                                   placeholder="+1-234-567-8900">
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Organizer Website</label>
                            <input type="url" class="form-control" name="organizer_website" 
                                   value="{{ old('organizer_website', $config->organizer_website ?? '') }}"
                                   placeholder="https://example.com">
                        </div>
                        <div class="col-12 mb-4">
                            <label class="form-label">Organizer Address</label>
                            <textarea class="form-control" name="organizer_address" rows="3" 
                                      placeholder="Enter full address">{{ old('organizer_address', $config->organizer_address ?? '') }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Pricing Configuration -->
                <div class="form-section">
                    <h5 class="form-section-title">
                        <i class="fas fa-dollar-sign"></i>
                        Pricing Configuration
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Shell Scheme Rate (per sqm)</label>
                            <div class="input-group-custom">
                                <input type="number" step="0.01" class="form-control" name="shell_scheme_rate" 
                                       value="{{ old('shell_scheme_rate', $config->shell_scheme_rate ?? '') }}"
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">Raw Space Rate (per sqm)</label>
                            <div class="input-group-custom">
                                <input type="number" step="0.01" class="form-control" name="raw_space_rate" 
                                       value="{{ old('raw_space_rate', $config->raw_space_rate ?? '') }}"
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-4 mb-4">
                            <label class="form-label">GST Rate (%)</label>
                            <div class="input-group-custom">
                                <input type="number" step="0.01" class="form-control" name="gst_rate" 
                                       value="{{ old('gst_rate', $config->gst_rate ?? '') }}"
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Indian Processing Charge (%)</label>
                            <div class="input-group-custom">
                                <input type="number" step="0.01" class="form-control" name="ind_processing_charge" 
                                       value="{{ old('ind_processing_charge', $config->ind_processing_charge ?? '') }}"
                                       placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">International Processing Charge (%)</label>
                            <div class="input-group-custom">
                                <input type="number" step="0.01" class="form-control" name="int_processing_charge" 
                                       value="{{ old('int_processing_charge', $config->int_processing_charge ?? '') }}"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booth Sizes Configuration -->
                <div class="form-section">
                    <h5 class="form-section-title">
                        <i class="fas fa-cube"></i>
                        Booth Sizes Configuration
                    </h5>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Raw Space Sizes (comma-separated, e.g., 36,48,54,72,108,135)</label>
                            <div class="input-group-custom">
                                @php
                                    $boothSizes = json_decode($config->booth_sizes ?? '{}', true);
                                    $rawSizes = isset($boothSizes['Raw']) ? implode(',', $boothSizes['Raw']) : '36,48,54,72,108,135';
                                @endphp
                                <input type="text" class="form-control" name="booth_sizes_raw" 
                                       value="{{ old('booth_sizes_raw', $rawSizes) }}"
                                       placeholder="36,48,54,72,108,135">
                            </div>
                            <small class="form-text text-muted">Enter booth sizes in sqm separated by commas</small>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label">Shell Space Sizes (comma-separated, e.g., 9,12,15,18,27)</label>
                            <div class="input-group-custom">
                                @php
                                    $shellSizes = isset($boothSizes['Shell']) ? implode(',', $boothSizes['Shell']) : '9,12,15,18,27';
                                @endphp
                                <input type="text" class="form-control" name="booth_sizes_shell" 
                                       value="{{ old('booth_sizes_shell', $shellSizes) }}"
                                       placeholder="9,12,15,18,27">
                            </div>
                            <small class="form-text text-muted">Enter booth sizes in sqm separated by commas</small>
                        </div>
                    </div>
                </div>

                <hr class="section-divider">

                <!-- Startup Zone Pricing Configuration -->
                <div class="form-section">
                    <h5 class="form-section-title">
                        <i class="fas fa-rocket"></i>
                        Startup Zone Pricing Configuration
                    </h5>
                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="form-label">Early Bird Cutoff Date</label>
                            <input type="date" class="form-control" name="startup_zone_early_bird_cutoff_date" 
                                   value="{{ old('startup_zone_early_bird_cutoff_date', $config->startup_zone_early_bird_cutoff_date ?? '') }}"
                                   placeholder="YYYY-MM-DD">
                            <small class="form-text text-muted">Date when early bird pricing ends</small>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3" style="color: #667eea; font-weight: 600;">
                                <i class="fas fa-rupee-sign"></i> INR Pricing
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Regular Price (Without TV) - INR</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" class="form-control" name="startup_zone_regular_price_inr" 
                                       value="{{ old('startup_zone_regular_price_inr', $config->startup_zone_regular_price_inr ?? '') }}"
                                       placeholder="52000.00">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Regular Price (With TV) - INR</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" class="form-control" name="startup_zone_regular_price_with_tv_inr" 
                                       value="{{ old('startup_zone_regular_price_with_tv_inr', $config->startup_zone_regular_price_with_tv_inr ?? '') }}"
                                       placeholder="60000.00">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Early Bird Price (Without TV) - INR</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" class="form-control" name="startup_zone_early_bird_price_inr" 
                                       value="{{ old('startup_zone_early_bird_price_inr', $config->startup_zone_early_bird_price_inr ?? '') }}"
                                       placeholder="30000.00">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Early Bird Price (With TV) - INR</label>
                            <div class="input-group">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.01" class="form-control" name="startup_zone_early_bird_price_with_tv_inr" 
                                       value="{{ old('startup_zone_early_bird_price_with_tv_inr', $config->startup_zone_early_bird_price_with_tv_inr ?? '') }}"
                                       placeholder="37500.00">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3" style="color: #667eea; font-weight: 600;">
                                <i class="fas fa-dollar-sign"></i> USD Pricing
                            </h6>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Regular Price (Without TV) - USD</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="startup_zone_regular_price_usd" 
                                       value="{{ old('startup_zone_regular_price_usd', $config->startup_zone_regular_price_usd ?? '') }}"
                                       placeholder="0.00">
                            </div>
                            <small class="form-text text-muted">Leave empty to calculate from INR using exchange rate</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Regular Price (With TV) - USD</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="startup_zone_regular_price_with_tv_usd" 
                                       value="{{ old('startup_zone_regular_price_with_tv_usd', $config->startup_zone_regular_price_with_tv_usd ?? '') }}"
                                       placeholder="0.00">
                            </div>
                            <small class="form-text text-muted">Leave empty to calculate from INR using exchange rate</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Early Bird Price (Without TV) - USD</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="startup_zone_early_bird_price_usd" 
                                       value="{{ old('startup_zone_early_bird_price_usd', $config->startup_zone_early_bird_price_usd ?? '') }}"
                                       placeholder="0.00">
                            </div>
                            <small class="form-text text-muted">Leave empty to calculate from INR using exchange rate</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Early Bird Price (With TV) - USD</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="startup_zone_early_bird_price_with_tv_usd" 
                                       value="{{ old('startup_zone_early_bird_price_with_tv_usd', $config->startup_zone_early_bird_price_with_tv_usd ?? '') }}"
                                       placeholder="0.00">
                            </div>
                            <small class="form-text text-muted">Leave empty to calculate from INR using exchange rate</small>
                        </div>
                    </div>
                </div>

                <div class="mt-5 pt-3 border-top">
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save me-2"></i>
                        Save Configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
