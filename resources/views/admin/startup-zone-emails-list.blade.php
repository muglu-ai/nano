@extends('layouts.dashboard')
@section('title', 'Startup Zone Email Previews')
@section('content')

<style>
    .email-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .email-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .email-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
    .preview-iframe {
        width: 100%;
        height: 800px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-lg-flex">
                        <div>
                            <h5 class="mb-0">Startup Zone Email Previews</h5>
                            <p class="text-sm mb-0 text-dark">
                                Preview all emails that will be sent to users during the startup zone registration process.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Admin Notification Email -->
                        <div class="col-md-4 mb-4">
                            <div class="card email-card h-100" onclick="previewEmail('admin_notification')">
                                <div class="card-body text-center">
                                    <div class="email-icon text-warning">
                                        <i class="fas fa-bell"></i>
                                    </div>
                                    <h5 class="card-title">Admin Notification</h5>
                                    <p class="card-text text-muted">
                                        Email sent to admin when a startup zone application is submitted.
                                    </p>
                                    <div class="mt-3">
                                        <span class="badge bg-warning text-dark">To: Admin</span>
                                        <span class="badge bg-info">On: Submission</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Approval Email -->
                        <div class="col-md-4 mb-4">
                            <div class="card email-card h-100" onclick="previewEmail('approval')">
                                <div class="card-body text-center">
                                    <div class="email-icon text-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <h5 class="card-title">Approval Email</h5>
                                    <p class="card-text text-muted">
                                        Email sent to user when their application is approved by admin.
                                    </p>
                                    <div class="mt-3">
                                        <span class="badge bg-success">To: User</span>
                                        <span class="badge bg-info">On: Approval</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Thank You Email -->
                        <div class="col-md-4 mb-4">
                            <div class="card email-card h-100" onclick="previewEmail('payment_thank_you')">
                                <div class="card-body text-center">
                                    <div class="email-icon text-primary">
                                        <i class="fas fa-thumbs-up"></i>
                                    </div>
                                    <h5 class="card-title">Payment Thank You</h5>
                                    <p class="card-text text-muted">
                                        Email sent to user after successful payment confirmation.
                                    </p>
                                    <div class="mt-3">
                                        <span class="badge bg-primary">To: User</span>
                                        <span class="badge bg-info">On: Payment</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Preview Section -->
                    <div class="row mt-4" id="previewSection" style="display: none;">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0" id="previewTitle">Email Preview</h5>
                                    <button type="button" class="btn btn-sm btn-secondary" onclick="closePreview()">
                                        <i class="fas fa-times"></i> Close
                                    </button>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">Select Application (Optional - for real data preview):</label>
                                        <select class="form-select" id="applicationSelect" onchange="updatePreview()">
                                            <option value="">Use Sample Data</option>
                                            @php
                                                $applications = \App\Models\Application::where('application_type', 'startup-zone')
                                                    ->orderBy('created_at', 'desc')
                                                    ->limit(20)
                                                    ->get();
                                            @endphp
                                            @foreach($applications as $app)
                                                <option value="{{ $app->application_id }}">{{ $app->application_id }} - {{ $app->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <iframe id="emailPreview" class="preview-iframe" src=""></iframe>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Details Table -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Email Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Email Type</th>
                                                    <th>Recipient</th>
                                                    <th>Trigger</th>
                                                    <th>Subject</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>Admin Notification</strong></td>
                                                    <td>Admin</td>
                                                    <td>Application Submitted</td>
                                                    <td>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }} - New Startup Zone Application Submitted</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" onclick="previewEmail('admin_notification')">
                                                            <i class="fas fa-eye"></i> Preview
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Approval Email</strong></td>
                                                    <td>User</td>
                                                    <td>Application Approved</td>
                                                    <td>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }} - Startup Zone Application Approved & Payment Link</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" onclick="previewEmail('approval')">
                                                            <i class="fas fa-eye"></i> Preview
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Payment Thank You</strong></td>
                                                    <td>User</td>
                                                    <td>Payment Confirmed</td>
                                                    <td>Thank You for Your Payment - {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }} Startup Exhibition</td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" onclick="previewEmail('payment_thank_you')">
                                                            <i class="fas fa-eye"></i> Preview
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentEmailType = '';

    function previewEmail(emailType) {
        currentEmailType = emailType;
        const applicationId = document.getElementById('applicationSelect')?.value || '';
        
        let url = '';
        switch(emailType) {
            case 'admin_notification':
                if (applicationId) {
                    url = '{{ route("email-preview.startup-zone.admin-notification", ["applicationId" => ":id"]) }}'.replace(':id', applicationId);
                } else {
                    url = '{{ route("email-preview.startup-zone.admin-notification") }}';
                }
                document.getElementById('previewTitle').textContent = 'Admin Notification Email Preview';
                break;
            case 'approval':
                if (applicationId) {
                    url = '{{ route("email-preview.startup-zone.approval", ["applicationId" => ":id"]) }}'.replace(':id', applicationId);
                } else {
                    url = '{{ route("email-preview.startup-zone.approval") }}';
                }
                document.getElementById('previewTitle').textContent = 'Approval Email Preview';
                break;
            case 'payment_thank_you':
                if (applicationId) {
                    url = '{{ route("email-preview.startup-zone.payment-thank-you", ["applicationId" => ":id"]) }}'.replace(':id', applicationId);
                } else {
                    url = '{{ route("email-preview.startup-zone.payment-thank-you") }}';
                }
                document.getElementById('previewTitle').textContent = 'Payment Thank You Email Preview';
                break;
        }
        
        document.getElementById('emailPreview').src = url;
        document.getElementById('previewSection').style.display = 'block';
        
        // Scroll to preview section
        document.getElementById('previewSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function updatePreview() {
        if (currentEmailType) {
            previewEmail(currentEmailType);
        }
    }

    function closePreview() {
        document.getElementById('previewSection').style.display = 'none';
        document.getElementById('emailPreview').src = '';
        currentEmailType = '';
    }
</script>

@endsection

