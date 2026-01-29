@extends('layouts.poster-registration')

@section('title', 'Redirecting to PayPal - ' . config('constants.EVENT_NAME'))

@push('styles')
<style>
    .loading-container {
        min-height: 400px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }
    .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #0B5ED7;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
<script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&currency=USD"></script>
@endpush

@section('poster-content')
<div class="container py-5">
    <div class="loading-container">
        <div class="spinner"></div>
        <h3 class="mt-4">Redirecting to PayPal...</h3>
        <p class="text-muted">Please wait while we redirect you to the payment gateway.</p>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Call the backend to create PayPal order
    fetch('{{ route("paypal.form", ["id" => $invoiceNo]) }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) {
            // Redirect to PayPal approval URL
            const approvalUrl = data.links.find(link => link.rel === 'approve');
            if (approvalUrl) {
                window.location.href = approvalUrl.href;
            } else {
                throw new Error('Approval URL not found');
            }
        } else {
            throw new Error(data.error || 'Failed to create PayPal order');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to redirect to PayPal: ' + error.message);
        window.location.href = '{{ route("poster.register.payment", ["tin_no" => $invoiceNo]) }}';
    });
});
</script>
@endsection
