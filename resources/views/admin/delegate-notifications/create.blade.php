@extends('layouts.dashboard')
@section('title', 'Create Delegate Notification')

@section('content')
<div class="container-fluid py-2">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 h4 font-weight-bolder">Create Delegate Notification</h3>
        <a href="{{ route('admin.delegate-notifications.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.delegate-notifications.store') }}">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Message *</label>
                    <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Type *</label>
                    <select name="type" class="form-select" required>
                        <option value="info" {{ old('type') === 'info' ? 'selected' : '' }}>Info</option>
                        <option value="warning" {{ old('type') === 'warning' ? 'selected' : '' }}>Warning</option>
                        <option value="important" {{ old('type') === 'important' ? 'selected' : '' }}>Important</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Target *</label>
                    <select name="target_type" id="target_type" class="form-select" required>
                        <option value="all" {{ old('target_type') === 'all' ? 'selected' : '' }}>All Delegates</option>
                        <option value="contact" {{ old('target_type') === 'contact' ? 'selected' : '' }}>Specific Contact</option>
                        <option value="delegate" {{ old('target_type') === 'delegate' ? 'selected' : '' }}>Specific Delegate</option>
                    </select>
                </div>

                <div class="mb-3" id="contact-select" style="display: none;">
                    <label class="form-label">Contact</label>
                    <select name="contact_id" class="form-select">
                        <option value="">-- Select Contact --</option>
                        @php
                            $contacts = \App\Models\Ticket\TicketContact::whereHas('registrations.delegates')->get();
                        @endphp
                        @foreach($contacts as $contact)
                            <option value="{{ $contact->id }}" {{ old('contact_id') == $contact->id ? 'selected' : '' }}>
                                {{ $contact->name }} ({{ $contact->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3" id="delegate-select" style="display: none;">
                    <label class="form-label">Delegate</label>
                    <select name="delegate_id" class="form-select">
                        <option value="">-- Select Delegate --</option>
                        @php
                            $delegates = \App\Models\Ticket\TicketDelegate::with('registration.contact')->get();
                        @endphp
                        @foreach($delegates as $delegate)
                            <option value="{{ $delegate->id }}" {{ old('delegate_id') == $delegate->id ? 'selected' : '' }}>
                                {{ $delegate->full_name }} ({{ $delegate->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" name="send_email" value="1" class="form-check-input" id="send_email" {{ old('send_email') ? 'checked' : '' }}>
                    <label class="form-check-label" for="send_email">Send Email Notification</label>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Send Notification
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('target_type').addEventListener('change', function() {
    const contactSelect = document.getElementById('contact-select');
    const delegateSelect = document.getElementById('delegate-select');
    
    contactSelect.style.display = 'none';
    delegateSelect.style.display = 'none';
    
    if(this.value === 'contact') {
        contactSelect.style.display = 'block';
    } else if(this.value === 'delegate') {
        delegateSelect.style.display = 'block';
    }
});

// Trigger on page load if old value exists
if(document.getElementById('target_type').value === 'contact') {
    document.getElementById('contact-select').style.display = 'block';
} else if(document.getElementById('target_type').value === 'delegate') {
    document.getElementById('delegate-select').style.display = 'block';
}
</script>
@endsection
