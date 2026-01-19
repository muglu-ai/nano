@extends('delegate.layouts.app')
@section('title', 'Upgrade Ticket')

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-arrow-up me-2"></i>Upgrade Individual Ticket</h4>
    </div>
    <div class="card-body">
        @if($existingUpgrade)
            <div class="alert alert-info">
                <h5>Existing Upgrade Request</h5>
                <p>You have a pending upgrade request for this ticket.</p>
                <a href="{{ route('delegate.upgrades.receipt', $existingUpgrade->id) }}" class="btn btn-primary">View Request</a>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Current Ticket</h5>
                <div class="card bg-light">
                    <div class="card-body">
                        <strong>{{ $ticket->ticketType->name }}</strong><br>
                        <small>Category: {{ $ticket->ticketType->category->name ?? 'N/A' }}</small><br>
                        <small>Price: {{ $ticket->ticketType->getCurrentPrice('national') }} INR</small>
                    </div>
                </div>
            </div>
        </div>

        <form id="upgrade-form" method="POST" action="{{ route('delegate.upgrades.individual.process') }}">
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
            
            <div class="mb-3">
                <label class="form-label">Select New Ticket Type (Higher Category Only)</label>
                <select name="new_ticket_type_id" id="new_ticket_type_id" class="form-select" required>
                    <option value="">-- Select Ticket Type --</option>
                    @foreach($availableTicketTypes as $ticketType)
                        <option value="{{ $ticketType->id }}" data-price="{{ $ticketType->getCurrentPrice('national') }}">
                            {{ $ticketType->name }} ({{ $ticketType->category->name ?? 'Category' }}) - {{ $ticketType->getCurrentPrice('national') }} INR
                        </option>
                    @endforeach
                </select>
            </div>

            <div id="price-preview" class="alert alert-info" style="display: none;">
                <h6>Price Breakdown:</h6>
                <div id="price-details"></div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-check me-2"></i>Proceed with Upgrade
            </button>
            <a href="{{ route('delegate.upgrades.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
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

    fetch('{{ route("delegate.upgrades.preview") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            ticket_id: ticketId,
            new_ticket_type_id: newTicketTypeId
        })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const calc = data.calculation;
            document.getElementById('price-details').innerHTML = `
                <p>Old Price: ${calc.old_price} INR</p>
                <p>New Price: ${calc.new_price} INR</p>
                <p>Price Difference: ${calc.price_difference} INR</p>
                <p>GST: ${calc.gst_amount} INR</p>
                <p>Processing Charge: ${calc.processing_charge_amount} INR</p>
                <p><strong>Total to Pay: ${calc.total_amount} INR</strong></p>
            `;
            document.getElementById('price-preview').style.display = 'block';
        }
    });
});
</script>
@endsection
