@extends('delegate.layouts.app')
@section('title', 'Badge - Coming Soon')

@section('content')
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fas fa-id-badge fa-5x text-muted mb-4"></i>
        <h2 class="mb-3">Badge Management</h2>
        <p class="text-muted mb-4">This feature is coming soon. You will be able to view and download your badges here.</p>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            Badge functionality will be available soon. Please check back later.
        </div>
        <a href="{{ route('delegate.dashboard') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>
</div>
@endsection
