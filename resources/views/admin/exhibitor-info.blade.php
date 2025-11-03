@extends('layouts.dashboard')

@section('title', 'Exhibitor Information Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-building me-3"></i>Exhibitor Information Dashboard
                </h1>
                <p class="page-subtitle">Comprehensive overview of exhibitor data and analytics</p>
            </div>
        </div>
    </div>

    <!-- Analytics Cards -->
    <div class="row mb-4">
        <!-- Total Applications -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="analytics-card">
                <div class="analytics-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="analytics-content">
                    <div class="analytics-number">{{ number_format($analytics['total_applications']) }}</div>
                    <div class="analytics-label">Total Applications</div>
                    <div class="analytics-subtitle">Startup, Exhibitor & Sponsors</div>
                </div>
            </div>
        </div>

        <!-- Filled Information -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="analytics-card success">
                <div class="analytics-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="analytics-content">
                    <div class="analytics-number">{{ number_format($analytics['filled_count']) }}</div>
                    <div class="analytics-label">Information Filled</div>
                    <div class="analytics-subtitle">{{ $analytics['completion_rate'] }}% Complete</div>
                </div>
            </div>
        </div>

        <!-- Not Filled -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="analytics-card warning">
                <div class="analytics-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="analytics-content">
                    <div class="analytics-number">{{ number_format($analytics['not_filled_count']) }}</div>
                    <div class="analytics-label">Not Filled</div>
                    <div class="analytics-subtitle">Pending Information</div>
                </div>
            </div>
        </div>

        <!-- Incomplete Data -->
        {{-- <div class="col-xl-3 col-md-6 mb-4">
            <div class="analytics-card danger">
                <div class="analytics-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <div class="analytics-content">
                    <div class="analytics-number">{{ number_format($analytics['incomplete_count']) }}</div>
                    <div class="analytics-label">Incomplete Data</div>
                    <div class="analytics-subtitle">Needs Updates</div>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- Additional Stats Row -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">{{ number_format($analytics['recent_submissions']) }}</div>
                    <div class="stats-label">Recent (30 days)</div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stats-content">
                    <div class="stats-number">{{ $analytics['completion_rate'] }}%</div>
                    <div class="stats-label">Completion Rate</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="progress-section">
                <div class="progress-header">
                    <h5>Overall Completion Progress</h5>
                    <span class="progress-percentage">{{ $analytics['completion_rate'] }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar" style="width: {{ $analytics['completion_rate'] }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filters-section">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search exhibitors..." class="form-control">
                        </div>
                    </div>
                    {{-- <div class="col-md-6">
                        <div class="filter-controls">
                            <select id="statusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="1">Completed</option>
                                <option value="0">Incomplete</option>
                            </select>
                             <button class="btn btn-outline-primary" onclick="exportData()">
                                <i class="fas fa-download me-2"></i>Export
                            </button> 
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Exhibitor Table -->
    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-header">
                    <h5>Exhibitor Information</h5>
                    <div class="table-actions">
                        {{-- <button class="btn btn-sm btn-primary" onclick="sendDirectoryReminders()" id="sendRemindersBtn">
                            <i class="fas fa-envelope me-1"></i>Send Directory Reminders
                        </button> --}}
                        <a href="{{ route('exhibitor.directory.export') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-download me-1"></i>Export
                        </a>
                        <button class="btn btn-sm btn-outline-secondary" onclick="refreshTable()">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover" id="exhibitorTable">
                        <thead>
                            <tr>
                                <th>Stall Number</th>
                                <th>Company</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exhibitorInfo as $exhibitor)
                            <tr>
                                <td>
                                    <span class="stall-number">{{ optional($exhibitor->application)->stallNumber ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <div class="company-info">
                                        @if($exhibitor->logo)
                                            <img src="{{ asset('storage/' . $exhibitor->logo) }}" alt="Logo" class="company-logo">
                                        @else
                                            <div class="company-logo-placeholder">
                                                <i class="fas fa-building"></i>
                                            </div>
                                        @endif
                                        <div class="company-details">
                                            <div class="company-name">{{ $exhibitor->fascia_name ?? 'N/A' }}</div>
                                            <div class="company-website">
                                                @if($exhibitor->website)
                                                    <a href="{{ $exhibitor->website }}" target="_blank" class="website-link">
                                                        <i class="fas fa-external-link-alt me-1"></i>Website
                                                    </a>
                                                @else
                                                    <span class="text-muted">No website</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="contact-info">
                                        <div class="contact-name">{{ $exhibitor->contact_person ?? 'N/A' }}</div>
                                        <div class="contact-designation">{{ $exhibitor->designation ?? 'N/A' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <a href="mailto:{{ $exhibitor->email }}" class="email-link">
                                        {{ $exhibitor->email ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>
                                    <a href="tel:{{ $exhibitor->phone }}" class="phone-link">
                                        {{ $exhibitor->phone ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>
                                    @if($exhibitor->submission_status == 1)
                                        <span class="status-badge completed">Completed</span>
                                    @else
                                        <span class="status-badge incomplete">Incomplete</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewDetails({{ $exhibitor->id }})" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-secondary" onclick="editExhibitor({{ $exhibitor->id }})" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="no-data">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <h5>No Exhibitor Information Found</h5>
                                        <p class="text-muted">No exhibitor information has been submitted yet.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Exhibitor Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Edit Exhibitor Modal -->
<div class="modal fade" id="editExhibitorModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Edit Exhibitor Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editExhibitorForm" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Basic Information -->
                        <div class="col-md-6">
                            <div class="form-section">
                                <h6 class="form-section-title">
                                    <i class="fas fa-building me-2"></i>Basic Information
                                </h6>
                                
                                <div class="mb-3">
                                    <label for="edit_fascia_name" class="form-label">Company Name *</label>
                                    <input type="text" class="form-control" id="edit_fascia_name" name="fascia_name" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_contact_person" class="form-label">Contact Person *</label>
                                    <input type="text" class="form-control" id="edit_contact_person" name="contact_person" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_designation" class="form-label">Designation *</label>
                                    <input type="text" class="form-control" id="edit_designation" name="designation" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="edit_email" name="email" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_phone" class="form-label">Phone *</label>
                                    <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_submission_status" class="form-label">Status *</label>
                                    <select class="form-select" id="edit_submission_status" name="submission_status" required>
                                        <option value="0">Incomplete</option>
                                        <option value="1">Completed</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <div class="col-md-6">
                            <div class="form-section">
                                <h6 class="form-section-title">
                                    <i class="fas fa-info-circle me-2"></i>Additional Information
                                </h6>

                                <div class="mb-3">
                                    <label for="edit_address" class="form-label">Address</label>
                                    <textarea class="form-control" id="edit_address" name="address" rows="3"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_description" class="form-label">Description *</label>
                                    <textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label for="edit_logo" class="form-label">Company Logo</label>
                                    <input type="file" class="form-control" id="edit_logo" name="logo" accept="image/*">
                                    <div class="form-text">Max size: 2MB. Supported formats: JPG, PNG, GIF</div>
                                    <div id="current_logo_preview" class="mt-2" style="display: none;">
                                        <small class="text-muted">Current logo:</small>
                                        <img id="current_logo_img" src="" alt="Current Logo" class="img-thumbnail" style="max-width: 100px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Links -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-section">
                                <h6 class="form-section-title">
                                    <i class="fas fa-share-alt me-2"></i>Social Media & Links
                                </h6>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_website" class="form-label">Website</label>
                                            <input type="url" class="form-control" id="edit_website" name="website" placeholder="https://example.com">
                                        </div>

                                        <div class="mb-3">
                                            <label for="edit_linkedin" class="form-label">LinkedIn</label>
                                            <input type="url" class="form-control" id="edit_linkedin" name="linkedin" placeholder="https://linkedin.com/company/example">
                                        </div>

                                        <div class="mb-3">
                                            <label for="edit_instagram" class="form-label">Instagram</label>
                                            <input type="url" class="form-control" id="edit_instagram" name="instagram" placeholder="https://instagram.com/example">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="edit_facebook" class="form-label">Facebook</label>
                                            <input type="url" class="form-control" id="edit_facebook" name="facebook" placeholder="https://facebook.com/example">
                                        </div>

                                        <div class="mb-3">
                                            <label for="edit_youtube" class="form-label">YouTube</label>
                                            <input type="url" class="form-control" id="edit_youtube" name="youtube" placeholder="https://youtube.com/c/example">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" id="updateExhibitorBtn">
                            <i class="fas fa-save me-2"></i>Update Information
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Page Header */
.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.page-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin: 0.5rem 0 0 0;
}

/* Analytics Cards */
.analytics-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
    border-left: 4px solid #667eea;
}

.analytics-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
}

.analytics-card.success {
    border-left-color: #28a745;
}

.analytics-card.warning {
    border-left-color: #ffc107;
}

.analytics-card.danger {
    border-left-color: #dc3545;
}

.analytics-icon {
    font-size: 2.5rem;
    color: #667eea;
    margin-right: 1rem;
    width: 60px;
    text-align: center;
}

.analytics-card.success .analytics-icon {
    color: #28a745;
}

.analytics-card.warning .analytics-icon {
    color: #ffc107;
}

.analytics-card.danger .analytics-icon {
    color: #dc3545;
}

.analytics-number {
    font-size: 2rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
}

.analytics-label {
    font-size: 1rem;
    color: #6c757d;
    margin: 0.25rem 0;
}

.analytics-subtitle {
    font-size: 0.9rem;
    color: #28a745;
    font-weight: 500;
}

/* Stats Cards */
.stats-card {
    background: white;
    border-radius: 12px;
    padding: 1.25rem;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    display: flex;
    align-items: center;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.12);
}

.stats-icon {
    font-size: 2rem;
    color: #667eea;
    margin-right: 1rem;
    width: 50px;
    text-align: center;
}

.stats-number {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.stats-label {
    font-size: 0.9rem;
    color: #6c757d;
    margin: 0;
}

/* Progress Section */
.progress-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.progress-header h5 {
    margin: 0;
    color: #2c3e50;
}

.progress-percentage {
    font-size: 1.2rem;
    font-weight: 600;
    color: #667eea;
}

.progress-bar-container {
    background: #e9ecef;
    border-radius: 10px;
    height: 12px;
    overflow: hidden;
}

.progress-bar {
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    height: 100%;
    border-radius: 10px;
    transition: width 0.5s ease;
}

/* Filters Section */
.filters-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.search-box input {
    padding-left: 45px;
    border-radius: 25px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.search-box input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.filter-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.filter-controls select {
    border-radius: 25px;
    border: 2px solid #e9ecef;
    padding: 0.5rem 1rem;
}

/* Table Container */
.table-container {
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
}

.table-header h5 {
    margin: 0;
    color: #2c3e50;
}

.table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #2c3e50;
    padding: 1rem;
}

.table td {
    border: none;
    padding: 1rem;
    vertical-align: middle;
}

/* Company Info */
.company-info {
    display: flex;
    align-items: center;
}

.company-logo {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
    margin-right: 12px;
}

.company-logo-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    color: #6c757d;
}

.company-name {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 2px;
}

.website-link {
    color: #667eea;
    text-decoration: none;
    font-size: 0.85rem;
}

.website-link:hover {
    text-decoration: underline;
}

/* Contact Info */
.contact-name {
    font-weight: 500;
    color: #2c3e50;
    margin-bottom: 2px;
}

.contact-designation {
    font-size: 0.85rem;
    color: #6c757d;
}

.email-link, .phone-link {
    color: #667eea;
    text-decoration: none;
}

.email-link:hover, .phone-link:hover {
    text-decoration: underline;
}

/* Stall Number */
.stall-number {
    font-weight: 600;
    color: #667eea;
    font-size: 0.95rem;
}

/* Status Badges */
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.status-badge.completed {
    background: #d4edda;
    color: #155724;
}

.status-badge.incomplete {
    background: #f8d7da;
    color: #721c24;
}

/* Count Badges */
.count-badge {
    background: #667eea;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Action Buttons */
.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.action-buttons .btn {
    padding: 0.375rem 0.75rem;
    border-radius: 6px;
}

/* No Data */
.no-data {
    text-align: center;
    color: #6c757d;
}

.no-data i {
    color: #dee2e6;
}

/* Modal Styles */
.exhibitor-details {
    max-height: 70vh;
    overflow-y: auto;
}

.exhibitor-header {
    display: flex;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 2px solid #e9ecef;
}

.exhibitor-logo-section {
    margin-right: 1.5rem;
}

.exhibitor-logo-large {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.exhibitor-logo-placeholder-large {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.exhibitor-basic-info {
    flex: 1;
}

.exhibitor-name {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0 0 0.5rem 0;
}

.exhibitor-company {
    color: #6c757d;
    font-size: 1.1rem;
    margin: 0 0 1rem 0;
}

.status-badge-large {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge-large.completed {
    background: #d4edda;
    color: #155724;
    border: 2px solid #c3e6cb;
}

.status-badge-large.incomplete {
    background: #f8d7da;
    color: #721c24;
    border: 2px solid #f5c6cb;
}

.exhibitor-section {
    margin-bottom: 2rem;
}

.section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e9ecef;
}

.contact-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.contact-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.contact-item strong {
    color: #495057;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.contact-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
}

.contact-link:hover {
    text-decoration: underline;
}

.address-text, .description-text {
    color: #495057;
    line-height: 1.6;
    margin: 0;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #667eea;
}

.social-media-links {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}

.social-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    color: #495057;
    text-decoration: none;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
    font-weight: 500;
}

.social-link:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.application-details {
    display: grid;
    gap: 0.75rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
    border-left: 3px solid #667eea;
}

.detail-item strong {
    color: #495057;
    font-size: 0.9rem;
}

    .detail-item span {
        color: #6c757d;
        font-weight: 500;
    }

/* Edit Modal Styles */
.form-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e9ecef;
}

.form-section-title {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #667eea;
    font-size: 1.1rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.form-text {
    color: #6c757d;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    margin: 1.5rem -1.5rem -1.5rem -1.5rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-2px);
}

#updateExhibitorBtn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.img-thumbnail {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.25rem;
}

/* Loading states */
.loading {
    position: relative;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 20px;
    height: 20px;
    margin: -10px 0 0 -10px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Error states */
.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}

/* Success states */
.is-valid {
    border-color: #28a745;
}

/* Alert styles */
.alert {
    border-radius: 8px;
    border: none;
    padding: 1rem 1.5rem;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
}

/* Responsive */
@media (max-width: 768px) {
    .analytics-card {
        flex-direction: column;
        text-align: center;
    }
    
    .analytics-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .filter-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .table-header {
        flex-direction: column;
        gap: 1rem;
    }
    
    .exhibitor-header {
        flex-direction: column;
        text-align: center;
    }
    
    .exhibitor-logo-section {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .contact-grid {
        grid-template-columns: 1fr;
    }
    
    .social-media-links {
        justify-content: center;
    }
    
    .detail-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
}
</style>

<script>
// Search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('exhibitorTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Status filter
document.getElementById('statusFilter').addEventListener('change', function() {
    const filterValue = this.value;
    const table = document.getElementById('exhibitorTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const statusCell = row.cells[4]; // Status column
        
        if (filterValue === '' || statusCell.textContent.includes(filterValue === '1' ? 'Completed' : 'Incomplete')) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// View details function
function viewDetails(exhibitorId) {
    // Show loading state
    document.getElementById('modalBody').innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading exhibitor details...</p>
        </div>
    `;
    $('#viewDetailsModal').modal('show');

    // Make AJAX call to get exhibitor details
    fetch("{{ route('api.exhibitor.details', ':id') }}".replace(':id', exhibitorId))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayExhibitorDetails(data.data);
            } else {
                displayError(data.message || 'Failed to load exhibitor details', exhibitorId);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            displayError('An error occurred while loading exhibitor details', exhibitorId);
        });
}

// Display exhibitor details in modal
function displayExhibitorDetails(exhibitor) {
    const socialMediaHtml = exhibitor.social_media && Object.keys(exhibitor.social_media).length > 0 
        ? Object.entries(exhibitor.social_media).map(([platform, url]) => {
            const icons = {
                'website': 'fas fa-globe',
                'linkedin': 'fab fa-linkedin',
                'instagram': 'fab fa-instagram',
                'facebook': 'fab fa-facebook',
                'youtube': 'fab fa-youtube'
            };
            return `<a href="${url}" target="_blank" class="social-link">
                <i class="${icons[platform] || 'fas fa-link'}"></i> ${platform.charAt(0).toUpperCase() + platform.slice(1)}
            </a>`;
        }).join('')
        : '<span class="text-muted">No social media links</span>';

    document.getElementById('modalBody').innerHTML = `
        <div class="exhibitor-details">
            <!-- Header with Logo and Basic Info -->
            <div class="exhibitor-header">
                <div class="exhibitor-logo-section">
                    ${exhibitor.logo ? `<img src="${exhibitor.logo}" alt="Logo" class="exhibitor-logo-large">` : 
                        '<div class="exhibitor-logo-placeholder-large"><i class="fas fa-building"></i></div>'}
                </div>
                <div class="exhibitor-basic-info">
                    <h4 class="exhibitor-name">${exhibitor.fascia_name || 'N/A'}</h4>
                    <p class="exhibitor-company">${exhibitor.application.company_name || 'N/A'}</p>
                    <div class="status-badge-large ${exhibitor.submission_status == 1 ? 'completed' : 'incomplete'}">
                        ${exhibitor.submission_status == 1 ? 'Completed' : 'Incomplete'}
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="exhibitor-section">
                <h5 class="section-title">
                    <i class="fas fa-user me-2"></i>Contact Information
                </h5>
                <div class="contact-grid">
                    <div class="contact-item">
                        <strong>Contact Person:</strong>
                        <span>${exhibitor.contact_person || 'N/A'}</span>
                    </div>
                    <div class="contact-item">
                        <strong>Designation:</strong>
                        <span>${exhibitor.designation || 'N/A'}</span>
                    </div>
                    <div class="contact-item">
                        <strong>Email:</strong>
                        <a href="mailto:${exhibitor.email}" class="contact-link">${exhibitor.email || 'N/A'}</a>
                    </div>
                    <div class="contact-item">
                        <strong>Phone:</strong>
                        <a href="tel:${exhibitor.phone}" class="contact-link">${exhibitor.phone || 'N/A'}</a>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="exhibitor-section">
                <h5 class="section-title">
                    <i class="fas fa-map-marker-alt me-2"></i>Address
                </h5>
                <p class="address-text">${exhibitor.address || 'No address provided'}</p>
            </div>

            <!-- Description -->
            <div class="exhibitor-section">
                <h5 class="section-title">
                    <i class="fas fa-info-circle me-2"></i>Description
                </h5>
                <p class="description-text">${exhibitor.description || 'No description provided'}</p>
            </div>

            <!-- Social Media -->
            <div class="exhibitor-section">
                <h5 class="section-title">
                    <i class="fas fa-share-alt me-2"></i>Social Media & Links
                </h5>
                <div class="social-media-links">
                    ${socialMediaHtml}
                </div>
            </div>

            <!-- Application Details -->
            <div class="exhibitor-section">
                <h5 class="section-title">
                    <i class="fas fa-file-alt me-2"></i>Application Details
                </h5>
                <div class="application-details">
                    <div class="detail-item">
                        <strong>User:</strong>
                        <span>${exhibitor.application.user.name} (${exhibitor.application.user.email})</span>
                    </div>
                   
                </div>
            </div>
        </div>
    `;
}

// Display error message
function displayError(message, exhibitorId = null) {
    const retryButton = exhibitorId ? 
        `<button class="btn btn-primary" onclick="viewDetails(${exhibitorId})">
            <i class="fas fa-redo me-2"></i>Try Again
        </button>` : 
        `<button class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Close
        </button>`;
    
    document.getElementById('modalBody').innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
            <h5>Error Loading Details</h5>
            <p class="text-muted">${message}</p>
            ${retryButton}
        </div>
    `;
}

// Edit exhibitor function
function editExhibitor(exhibitorId) {
    // Show loading state
    showEditLoading();
    $('#editExhibitorModal').modal('show');

    // Fetch exhibitor data for editing #155724
    fetch("{{ route('api.exhibitor.edit', ':id') }}".replace(':id', exhibitorId))
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditForm(data.data);
            } else {
                showEditError(data.message || 'Failed to load exhibitor data', exhibitorId);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showEditError('An error occurred while loading exhibitor data', exhibitorId);
        });
}

// Show loading state in edit modal
function showEditLoading() {
    const form = document.getElementById('editExhibitorForm');
    form.innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Loading exhibitor data...</p>
        </div>
    `;
}

// Populate edit form with data
function populateEditForm(exhibitor) {
    const form = document.getElementById('editExhibitorForm');
    
    // Restore the form HTML
    form.innerHTML = `
        <div class="row">
            <!-- Basic Information -->
            <div class="col-md-6">
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="fas fa-building me-2"></i>Basic Information
                    </h6>
                    
                    <div class="mb-3">
                        <label for="edit_fascia_name" class="form-label">Company Name *</label>
                        <input type="text" class="form-control" id="edit_fascia_name" name="fascia_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_contact_person" class="form-label">Contact Person *</label>
                        <input type="text" class="form-control" id="edit_contact_person" name="contact_person" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_designation" class="form-label">Designation *</label>
                        <input type="text" class="form-control" id="edit_designation" name="designation" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email *</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Phone *</label>
                        <input type="tel" class="form-control" id="edit_phone" name="phone" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_submission_status" class="form-label">Status *</label>
                        <select class="form-select" id="edit_submission_status" name="submission_status" required>
                            <option value="0">Incomplete</option>
                            <option value="1">Completed</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="col-md-6">
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="fas fa-info-circle me-2"></i>Additional Information
                    </h6>

                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Address</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description *</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_logo" class="form-label">Company Logo</label>
                        <input type="file" class="form-control" id="edit_logo" name="logo" accept="image/*">
                        <div class="form-text">Max size: 2MB. Supported formats: JPG, PNG, GIF</div>
                        <div id="current_logo_preview" class="mt-2" style="display: none;">
                            <small class="text-muted">Current logo:</small>
                            <img id="current_logo_img" src="" alt="Current Logo" class="img-thumbnail" style="max-width: 100px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <div class="row">
            <div class="col-12">
                <div class="form-section">
                    <h6 class="form-section-title">
                        <i class="fas fa-share-alt me-2"></i>Social Media & Links
                    </h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="edit_website" name="website" placeholder="https://example.com">
                            </div>

                            <div class="mb-3">
                                <label for="edit_linkedin" class="form-label">LinkedIn</label>
                                <input type="url" class="form-control" id="edit_linkedin" name="linkedin" placeholder="https://linkedin.com/company/example">
                            </div>

                            <div class="mb-3">
                                <label for="edit_instagram" class="form-label">Instagram</label>
                                <input type="url" class="form-control" id="edit_instagram" name="instagram" placeholder="https://instagram.com/example">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_facebook" class="form-label">Facebook</label>
                                <input type="url" class="form-control" id="edit_facebook" name="facebook" placeholder="https://facebook.com/example">
                            </div>

                            <div class="mb-3">
                                <label for="edit_youtube" class="form-label">YouTube</label>
                                <input type="url" class="form-control" id="edit_youtube" name="youtube" placeholder="https://youtube.com/c/example">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-2"></i>Cancel
            </button>
            <button type="submit" class="btn btn-primary" id="updateExhibitorBtn">
                <i class="fas fa-save me-2"></i>Update Information
            </button>
        </div>
    `;

    // Populate form fields
    document.getElementById('edit_fascia_name').value = exhibitor.fascia_name || '';
    document.getElementById('edit_contact_person').value = exhibitor.contact_person || '';
    document.getElementById('edit_designation').value = exhibitor.designation || '';
    document.getElementById('edit_email').value = exhibitor.email || '';
    document.getElementById('edit_phone').value = exhibitor.phone || '';
    document.getElementById('edit_address').value = exhibitor.address || '';
    document.getElementById('edit_description').value = exhibitor.description || '';
    document.getElementById('edit_website').value = exhibitor.website || '';
    document.getElementById('edit_linkedin').value = exhibitor.linkedin || '';
    document.getElementById('edit_instagram').value = exhibitor.instagram || '';
    document.getElementById('edit_facebook').value = exhibitor.facebook || '';
    document.getElementById('edit_youtube').value = exhibitor.youtube || '';
    document.getElementById('edit_submission_status').value = exhibitor.submission_status || '0';

    // Show current logo if exists
    if (exhibitor.logo) {
        const logoPreview = document.getElementById('current_logo_preview');
        const logoImg = document.getElementById('current_logo_img');
        logoImg.src = `/storage/${exhibitor.logo}`;
        logoPreview.style.display = 'block';
    }

    // Add form submit event listener
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        updateExhibitor(exhibitor.id);
    });
}

// Show edit error
function showEditError(message, exhibitorId = null) {
    const form = document.getElementById('editExhibitorForm');
    const retryButton = exhibitorId ? 
        `<button class="btn btn-primary" onclick="editExhibitor(${exhibitorId})">
            <i class="fas fa-redo me-2"></i>Try Again
        </button>` : 
        `<button class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i>Close
        </button>`;
    
    form.innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
            <h5>Error Loading Data</h5>
            <p class="text-muted">${message}</p>
            ${retryButton}
        </div>
    `;
}

// Update exhibitor function
function updateExhibitor(exhibitorId) {
    const form = document.getElementById('editExhibitorForm');
    const submitBtn = document.getElementById('updateExhibitorBtn');
    const formData = new FormData(form);

    // Disable submit button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Updating...';
    form.classList.add('loading');

    // Clear previous validation errors
    clearValidationErrors();

    fetch("{{ route('api.exhibitor.update', ':id') }}".replace(':id', exhibitorId), {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message);
            $('#editExhibitorModal').modal('hide');
            // Refresh the table or update the specific row
            refreshTable();
        } else {
            if (data.errors) {
                showValidationErrors(data.errors);
            } else {
                showErrorMessage(data.message || 'Failed to update exhibitor');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('An error occurred while updating exhibitor');
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Update Information';
        form.classList.remove('loading');
    });
}

// Clear validation errors
function clearValidationErrors() {
    const invalidFields = document.querySelectorAll('.is-invalid');
    invalidFields.forEach(field => {
        field.classList.remove('is-invalid');
    });
    
    const errorMessages = document.querySelectorAll('.invalid-feedback');
    errorMessages.forEach(message => {
        message.remove();
    });
}

// Show validation errors
function showValidationErrors(errors) {
    Object.keys(errors).forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.classList.add('is-invalid');
            
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            errorDiv.textContent = errors[fieldName][0];
            
            field.parentNode.appendChild(errorDiv);
        }
    });
}

// Show success message
function showSuccessMessage(message) {
    // Create success alert
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show';
    alert.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the page
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alert, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Show error message
function showErrorMessage(message) {
    // Create error alert
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger alert-dismissible fade show';
    alert.innerHTML = `
        <i class="fas fa-exclamation-triangle me-2"></i>${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the page
    const container = document.querySelector('.container-fluid');
    container.insertBefore(alert, container.firstChild);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Export data function
function exportData() {
    // Implement export functionality
    console.log('Export data');
}

// Refresh table function
function refreshTable() {
    location.reload();
}

// Send directory reminders function
function sendDirectoryReminders() {
    const btn = document.getElementById('sendRemindersBtn');
    const originalText = btn.innerHTML;
    
    // Confirm before sending
    if (!confirm('Are you sure you want to send directory reminder emails to all exhibitors who haven\'t completed their directory form or have submission_status = 0?')) {
        return;
    }
    
    // Disable button and show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Sending...';
    
    // Make AJAX call to send reminders
    fetch("{{ route('exhibitor.directory.reminder.send') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const stats = data.statistics;
            const message = `Reminder emails processed successfully!\n\n` +
                          `✅ Sent: ${stats.sent}\n` +
                          `❌ Failed: ${stats.failed}\n` +
                          `⏭️ Skipped: ${stats.skipped}\n` +
                          `📊 Total Processed: ${stats.total_processed}`;
            
            alert(message);
            
            if (stats.sent > 0) {
                showSuccessMessage(`Successfully sent ${stats.sent} reminder email(s)!`);
            }
            
            if (data.errors && data.errors.length > 0) {
                console.error('Errors:', data.errors);
            }
        } else {
            showErrorMessage(data.message || 'Failed to send reminder emails');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showErrorMessage('An error occurred while sending reminder emails');
    })
    .finally(() => {
        // Re-enable button
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>
@endsection
