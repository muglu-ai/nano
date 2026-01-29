@extends('layouts.app')

@section('title', 'Create Event - Super Admin')

@section('content')
<style>
    .event-form-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .form-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        background: #fff;
    }
    
    .form-card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem 2rem;
        border: none;
    }
    
    .form-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .form-card-body {
        padding: 2rem;
    }
    
    .form-label {
        font-weight: 500;
        color: #4a5568;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .form-control, .form-select {
        border: 1px solid #cbd5e0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.2s;
        font-size: 0.95rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        color: white;
        transition: all 0.3s;
        box-shadow: 0 4px 6px rgba(102, 126, 234, 0.3);
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
        color: white;
    }
    
    .btn-cancel {
        background: #e2e8f0;
        color: #4a5568;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s;
    }
    
    .btn-cancel:hover {
        background: #cbd5e0;
        color: #4a5568;
    }
    
    .image-preview {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
        margin-top: 1rem;
        border: 2px dashed #cbd5e0;
        padding: 1rem;
        display: none;
    }
    
    .image-preview img {
        width: 100%;
        height: auto;
        border-radius: 6px;
    }
</style>

<div class="event-form-container">
    <div class="form-card">
        <div class="form-card-header">
            <h4>
                <i class="fas fa-plus-circle"></i>
                Create New Event
            </h4>
        </div>
        <div class="form-card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('super-admin.events.store') }}" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">
                            Event Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="event_name" 
                               value="{{ old('event_name') }}" required 
                               placeholder="e.g., Bengaluru Tech Summit">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">
                            Event Year <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="event_year" 
                               value="{{ old('event_year', date('Y')) }}" required 
                               placeholder="e.g., 2025">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">
                            Event Date <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" name="event_date" 
                               value="{{ old('event_date') }}" required>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" class="form-control" name="start_date" 
                               value="{{ old('start_date') }}">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">End Date</label>
                        <input type="date" class="form-control" name="end_date" 
                               value="{{ old('end_date') }}">
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">
                            Event Location <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="event_location" 
                               value="{{ old('event_location') }}" required 
                               placeholder="e.g., Bengaluru, India">
                    </div>
                    <div class="col-12 mb-4">
                        <label class="form-label">
                            Event Description <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" name="event_description" rows="5" required 
                                  placeholder="Enter event description...">{{ old('event_description') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Event Status</label>
                        <select class="form-control" name="status">
                            <option value="upcoming" {{ old('status', 'upcoming') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                            <option value="ongoing" {{ old('status') == 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                            <option value="over" {{ old('status') == 'over' ? 'selected' : '' }}>Over</option>
                        </select>
                    </div>
                    <div class="col-12 mb-4">
                        <label class="form-label">Event Image</label>
                        <input type="file" class="form-control" name="event_image" 
                               accept="image/*" id="event_image_input">
                        <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF (Max: 2MB)</small>
                        <div class="image-preview" id="image_preview">
                            <img id="preview_img" src="" alt="Preview">
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-3">
                    <button type="submit" class="btn btn-submit">
                        <i class="fas fa-save me-2"></i>Create Event
                    </button>
                    <a href="{{ route('super-admin.events') }}" class="btn btn-cancel">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('event_image_input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview_img').src = e.target.result;
                document.getElementById('image_preview').style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.getElementById('image_preview').style.display = 'none';
        }
    });
</script>
@endsection
