@extends('delegate.layouts.app')
@section('title', 'Upgrade Ticket')

@push('styles')
<style>
    .upgrade-form-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        border: none;
        overflow: hidden;
    }
    
    .upgrade-form-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }
    
    .upgrade-form-card .card-header h4 {
        color: white;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .current-ticket-card {
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        border: 2px solid #e3e6f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .current-ticket-card h5 {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .current-ticket-card h5 i {
        color: #667eea;
    }
    
    .ticket-info-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #e3e6f0;
    }
    
    .ticket-info-item:last-child {
        border-bottom: none;
    }
    
    .ticket-info-item strong {
        color: #4a5568;
        display: inline-block;
        min-width: 120px;
    }
    
    .form-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
    }
    
    .form-select, .form-control {
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .form-select:focus, .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .price-preview {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 2px solid #2196f3;
        border-radius: 12px;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }
    
    .price-preview h6 {
        color: #1976d2;
        font-weight: 600;
        margin-bottom: 1rem;
    }
    
    .price-item {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(33, 150, 243, 0.2);
    }
    
    .price-item:last-child {
        border-bottom: none;
        border-top: 2px solid #2196f3;
        margin-top: 0.5rem;
        padding-top: 1rem;
        font-weight: 700;
        font-size: 1.1rem;
        color: #1976d2;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .alert-existing {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid #ffc107;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="upgrade-form-card">
        <div class="card-header">
            <h4><i class="fas fa-arrow-up"></i>Upgrade Individual Ticket</h4>
        </div>
        <div class="card-body p-4">
            @if($existingUpgrade)
                <div class="alert-existing">
                    <h5 class="mb-2"><i class="fas fa-info-circle me-2"></i>Existing Upgrade Request</h5>
                    <p class="mb-3">You have a pending upgrade request for this ticket.</p>
                    <a href="{{ route('delegate.upgrades.receipt', $existingUpgrade->id) }}" class="btn btn-warning">
                        <i class="fas fa-eye me-2"></i>View Request
                    </a>
                </div>
            @endif

            <div class="current-ticket-card">
                <h5><i class="fas fa-ticket-alt"></i>Current Ticket</h5>
                <div class="ticket-info-item">
                    <strong>Ticket Type:</strong>
                    <span>{{ $ticket->ticketType->name }}</span>
                </div>
                <div class="ticket-info-item">
                    <strong>Category:</strong>
                    <span>{{ $ticket->ticketType->category->name ?? 'N/A' }}</span>
                </div>
                <div class="ticket-info-item">
                    <strong>Current Price:</strong>
                    <span class="text-primary fw-bold">{{ number_format($ticket->ticketType->getCurrentPrice('national'), 2) }} INR</span>
                </div>
            </div>

            <form id="upgrade-form" method="POST" action="{{ route('delegate.upgrades.individual.process') }}">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-arrow-up me-1 text-primary"></i>
                        Select New Ticket Type (Higher Category Only)
                    </label>
                    <select name="new_ticket_type_id" id="new_ticket_type_id" class="form-select" required>
                        <option value="">-- Select Ticket Type --</option>
                        @foreach($availableTicketTypes as $ticketType)
                            <option value="{{ $ticketType->id }}" data-price="{{ $ticketType->getCurrentPrice('national') }}">
                                {{ $ticketType->name }} ({{ $ticketType->category->name ?? 'Category' }}) - {{ number_format($ticketType->getCurrentPrice('national'), 2) }} INR
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Only higher category tickets are available for upgrade.
                    </small>
                </div>

                <div id="price-preview" class="price-preview" style="display: none;">
                    <h6><i class="fas fa-calculator me-2"></i>Price Breakdown</h6>
                    <div id="price-details"></div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-check me-2"></i>Proceed with Upgrade
                    </button>
                    <a href="{{ route('delegate.upgrades.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('new_ticket_type_id').addEventListener('change', function() {
    const ticketId = {{ $ticket->id }};
    const newTicketTypeId = this.value;
    
    if(!newTicketTypeId) {
        document.getElementById('price-preview').style.display = 'none';
        return;
    }

    // Show loading state
    document.getElementById('price-preview').style.display = 'block';
    document.getElementById('price-details').innerHTML = '<p class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Calculating...</p>';

    fetch('{{ route("delegate.upgrades.preview") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            ticket_id: ticketId,
            new_ticket_type_id: newTicketTypeId
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success && data.calculation) {
            const calc = data.calculation;
            const currency = 'INR';
            document.getElementById('price-details').innerHTML = `
                <div class="price-item">
                    <span>Old Price:</span>
                    <span>${parseFloat(calc.old_price).toLocaleString('en-IN', {maximumFractionDigits: 2})} ${currency}</span>
                </div>
                <div class="price-item">
                    <span>New Price:</span>
                    <span class="text-success">${parseFloat(calc.new_price).toLocaleString('en-IN', {maximumFractionDigits: 2})} ${currency}</span>
                </div>
                <div class="price-item">
                    <span>Price Difference:</span>
                    <span>${parseFloat(calc.price_difference).toLocaleString('en-IN', {maximumFractionDigits: 2})} ${currency}</span>
                </div>
                <div class="price-item">
                    <span>GST (${calc.gst_rate || 18}%):</span>
                    <span>${parseFloat(calc.gst_amount || 0).toLocaleString('en-IN', {maximumFractionDigits: 2})} ${currency}</span>
                </div>
                <div class="price-item">
                    <span>Processing Charge:</span>
                    <span>${parseFloat(calc.processing_charge_amount || 0).toLocaleString('en-IN', {maximumFractionDigits: 2})} ${currency}</span>
                </div>
                <div class="price-item">
                    <span>Total to Pay:</span>
                    <span>${parseFloat(calc.total_amount || calc.remaining_amount).toLocaleString('en-IN', {maximumFractionDigits: 2})} ${currency}</span>
                </div>
            `;
        } else {
            document.getElementById('price-details').innerHTML = '<p class="text-danger">Error calculating price. Please try again.</p>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('price-details').innerHTML = '<p class="text-danger">Error calculating price. Please try again.</p>';
    });
});
</script>
@endsection
