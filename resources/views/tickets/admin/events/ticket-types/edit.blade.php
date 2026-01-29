@extends('layouts.dashboard')

@section('title', 'Edit Ticket Type - ' . $event->event_name)

@section('content')
<style>
    .ticket-form-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .ticket-form-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
    }
    
    .ticket-form-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.75rem 2rem;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .ticket-form-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .ticket-form-body {
        padding: 2rem;
    }
    
    .form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e9ecef;
    }
    
    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #667eea;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        display: block;
    }
    
    .form-control, .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .switch-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        background: white;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }
    
    .switch-label {
        font-weight: 500;
        color: #4a5568;
        font-size: 0.95rem;
        flex: 1;
        margin: 0;
    }
    
    .form-check-input {
        width: 48px;
        height: 24px;
        cursor: pointer;
        margin: 0;
        border: 2px solid #cbd5e0;
        background-color: #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .btn-save {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
    }
    
    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.3);
        color: white;
    }
    
    .btn-back {
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-back:hover {
        background: #cbd5e0;
        color: #2d3748;
        transform: translateY(-2px);
    }
</style>

<div class="ticket-form-container">
    <div class="ticket-form-card">
        <div class="ticket-form-header">
            <h4>
                <i class="fas fa-edit"></i>
                Edit Ticket Type - {{ $ticketType->name }}
            </h4>
            <a href="{{ route('admin.tickets.events.ticket-types', $event->id) }}" class="btn-back" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
                <i class="fas fa-arrow-left"></i>
                Back to List
            </a>
        </div>
        <div class="ticket-form-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('admin.tickets.events.ticket-types.update', [$event->id, $ticketType->id]) }}" method="POST">
                @csrf
                @method('PUT')
                @include('tickets.admin.events.ticket-types._form')
                
                <div class="form-actions mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save me-2"></i>Update Ticket Type
                    </button>
                    <a href="{{ route('admin.tickets.events.ticket-types', $event->id) }}" class="btn btn-back">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

