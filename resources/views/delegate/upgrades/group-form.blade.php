@extends('delegate.layouts.app')
@section('title', 'Upgrade Group Registration')

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
    
    .registration-info {
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        border: 2px solid #e3e6f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .registration-info h5 {
        color: #2d3748;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .upgrade-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .upgrade-table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border: none;
    }
    
    .upgrade-table tbody td {
        padding: 1rem;
        border-top: 1px solid #e3e6f0;
        vertical-align: middle;
    }
    
    .upgrade-table tbody tr:hover {
        background-color: #f8f9fc;
    }
    
    .form-select {
        border: 2px solid #e3e6f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .form-select:disabled {
        background-color: #f8f9fc;
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        cursor: pointer;
    }
    
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .total-preview {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: 2px solid #2196f3;
        border-radius: 12px;
        padding: 1.5rem;
        margin: 1.5rem 0;
    }
    
    .total-preview h6 {
        color: #1976d2;
        font-weight: 600;
        margin-bottom: 1rem;
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
    
    .btn-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="upgrade-form-card">
        <div class="card-header">
            <h4><i class="fas fa-users"></i>Upgrade Group Registration</h4>
        </div>
        <div class="card-body p-4">
            <div class="registration-info">
                <h5><i class="fas fa-building me-2"></i>{{ $registration->company_name }}</h5>
                <p class="mb-0" style="color: #4a5568;">
                    <i class="fas fa-users me-1"></i>
                    {{ $registration->delegates->count() }} delegate(s) in this registration
                </p>
            </div>

            <form method="POST" action="{{ route('delegate.upgrades.group.process') }}">
                @csrf
                <input type="hidden" name="registration_id" value="{{ $registration->id }}">

                <h5 class="mb-3"><i class="fas fa-list-check me-2"></i>Select Tickets to Upgrade</h5>
                <div class="table-responsive mb-4">
                    <table class="upgrade-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">Select</th>
                                <th>Delegate</th>
                                <th>Current Ticket</th>
                                <th>New Ticket Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" name="ticket_ids[]" value="{{ $ticket->id }}" class="form-check-input ticket-checkbox" id="ticket_{{ $ticket->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $ticket->delegate->full_name }}</strong>
                                        <br>
                                        <small style="color: #4a5568;">{{ $ticket->delegate->email }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $ticket->ticketType->name }}</strong>
                                        <br>
                                        <small style="color: #4a5568;">
                                            <i class="fas fa-tag me-1"></i>
                                            {{ $ticket->ticketType->category->name ?? 'Category' }}
                                        </small>
                                        <br>
                                        <small class="text-primary fw-bold">
                                            {{ number_format($ticket->ticketType->getCurrentPrice('national'), 2) }} INR
                                        </small>
                                    </td>
                                    <td>
                                        <select name="new_ticket_type_ids[]" class="form-select new-ticket-select" disabled>
                                            <option value="">-- Select New Ticket --</option>
                                            @foreach($availableTicketTypes as $ticketType)
                                                <option value="{{ $ticketType->id }}" data-price="{{ $ticketType->getCurrentPrice('national') }}">
                                                    {{ $ticketType->name }} ({{ $ticketType->category->name ?? 'Category' }}) - {{ number_format($ticketType->getCurrentPrice('national'), 2) }} INR
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div id="total-preview" class="total-preview" style="display: none;">
                    <h6><i class="fas fa-calculator me-2"></i>Estimated Total</h6>
                    <div id="total-details"></div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-submit" id="submit-btn" disabled>
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
document.querySelectorAll('.ticket-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const row = this.closest('tr');
        const select = row.querySelector('.new-ticket-select');
        select.disabled = !this.checked;
        if(!this.checked) {
            select.value = '';
        }
        updateSubmitButton();
        updateTotalPreview();
    });
});

document.querySelectorAll('.new-ticket-select').forEach(select => {
    select.addEventListener('change', function() {
        updateSubmitButton();
        updateTotalPreview();
    });
});

function updateSubmitButton() {
    const checked = document.querySelectorAll('.ticket-checkbox:checked');
    const allSelected = Array.from(checked).every(cb => {
        const select = cb.closest('tr').querySelector('.new-ticket-select');
        return select.value !== '';
    });
    document.getElementById('submit-btn').disabled = checked.length === 0 || !allSelected;
}

function updateTotalPreview() {
    const checked = document.querySelectorAll('.ticket-checkbox:checked');
    if(checked.length === 0) {
        document.getElementById('total-preview').style.display = 'none';
        return;
    }
    
    // This is a simplified preview - actual calculation happens on server
    let total = 0;
    checked.forEach(cb => {
        const select = cb.closest('tr').querySelector('.new-ticket-select');
        if(select.value) {
            const price = parseFloat(select.options[select.selectedIndex].dataset.price || 0);
            total += price;
        }
    });
    
    if(total > 0) {
        document.getElementById('total-preview').style.display = 'block';
        document.getElementById('total-details').innerHTML = `
            <p class="mb-1"><strong>Estimated Amount:</strong> ${total.toLocaleString('en-IN', {maximumFractionDigits: 2})} INR</p>
            <small style="color: #4a5568;">Final amount including taxes and charges will be calculated during checkout.</small>
        `;
    } else {
        document.getElementById('total-preview').style.display = 'none';
    }
}
</script>
@endsection
