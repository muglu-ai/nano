@extends('delegate.layouts.app')
@section('title', 'Upgrade Group Registration')

@section('content')
<div class="card">
    <div class="card-header">
        <h4><i class="fas fa-users me-2"></i>Upgrade Group Registration</h4>
    </div>
    <div class="card-body">
        <div class="mb-4">
            <h5>Registration: {{ $registration->company_name }}</h5>
            <p class="text-muted">{{ $registration->delegates->count() }} delegate(s) in this registration</p>
        </div>

        <form method="POST" action="{{ route('delegate.upgrades.group.process') }}">
            @csrf
            <input type="hidden" name="registration_id" value="{{ $registration->id }}">

            <h5>Select Tickets to Upgrade</h5>
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Delegate</th>
                            <th>Current Ticket</th>
                            <th>New Ticket Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td>
                                    <input type="checkbox" name="ticket_ids[]" value="{{ $ticket->id }}" class="ticket-checkbox">
                                </td>
                                <td>{{ $ticket->delegate->full_name }}</td>
                                <td>
                                    {{ $ticket->ticketType->name }}<br>
                                    <small class="text-muted">{{ $ticket->ticketType->getCurrentPrice('national') }} INR</small>
                                </td>
                                <td>
                                    <select name="new_ticket_type_ids[]" class="form-select new-ticket-select" disabled>
                                        <option value="">-- Select --</option>
                                        @foreach($availableTicketTypes as $ticketType)
                                            <option value="{{ $ticketType->id }}" data-price="{{ $ticketType->getCurrentPrice('national') }}">
                                                {{ $ticketType->name }} - {{ $ticketType->getCurrentPrice('national') }} INR
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div id="total-preview" class="alert alert-info" style="display: none;">
                <h6>Estimated Total:</h6>
                <div id="total-details"></div>
            </div>

            <button type="submit" class="btn btn-primary" id="submit-btn" disabled>
                <i class="fas fa-check me-2"></i>Proceed with Upgrade
            </button>
            <a href="{{ route('delegate.upgrades.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<script>
document.querySelectorAll('.ticket-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const select = this.closest('tr').querySelector('.new-ticket-select');
        select.disabled = !this.checked;
        if(!this.checked) {
            select.value = '';
        }
        updateSubmitButton();
    });
});

document.querySelectorAll('.new-ticket-select').forEach(select => {
    select.addEventListener('change', function() {
        updateSubmitButton();
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
</script>
@endsection
