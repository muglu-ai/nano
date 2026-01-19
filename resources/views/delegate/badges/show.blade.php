@extends('delegate.layouts.app')
@section('title', 'Badge')

@push('styles')
<style>
    .badge-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        overflow: hidden;
    }
    
    .badge-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }
    
    .badge-card .card-header h4 {
        color: white;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .badge-preview {
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        border: 2px dashed #e3e6f0;
        border-radius: 12px;
        padding: 3rem;
        text-align: center;
        margin: 2rem 0;
    }
    
    .badge-icon {
        font-size: 5rem;
        color: #667eea;
        margin-bottom: 1rem;
        opacity: 0.7;
    }
    
    .coming-soon {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .coming-soon i {
        font-size: 5rem;
        color: #667eea;
        margin-bottom: 1.5rem;
        opacity: 0.5;
    }
    
    .info-alert {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 2px solid #2196f3;
        border-radius: 12px;
        padding: 1.25rem;
        margin: 2rem 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="badge-card">
        <div class="card-header">
            <h4><i class="fas fa-id-badge"></i>Badge Management</h4>
        </div>
        <div class="card-body p-4">
            <div class="coming-soon">
                <i class="fas fa-id-badge"></i>
                <h3 class="mb-3">Badge Feature Coming Soon</h3>
                <p class="mb-4" style="color: #4a5568;">
                    You will be able to view and download your badges with QR codes here.
                </p>
                <div class="info-alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3 text-primary"></i>
                        <div>
                            <h5 class="mb-1">Badge Information</h5>
                            <p class="mb-0">
                                Badge functionality will be available soon. Once enabled, you'll be able to:
                            </p>
                            <ul class="mt-2 mb-0 text-start">
                                <li>View your digital badge</li>
                                <li>Download PDF version with QR code</li>
                                <li>Share your badge with others</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <a href="{{ route('delegate.dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
