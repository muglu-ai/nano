<style>
    .config-form-section {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e9ecef;
    }
    
    .config-form-section-title {
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
    
    .form-group {
        margin-bottom: 1.5rem;
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
        width: 100%;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .form-control::placeholder {
        color: #a0aec0;
    }
    
    textarea.form-control {
        min-height: 100px;
        resize: vertical;
        font-family: 'Courier New', monospace;
        font-size: 0.875rem;
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
    
    .switch-container:hover {
        border-color: #cbd5e0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
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
        position: relative;
    }
    
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    .form-actions {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 2px solid #e2e8f0;
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
    }
    
    .btn-back:hover {
        background: #cbd5e0;
        color: #2d3748;
        transform: translateY(-2px);
    }
    
    .text-danger {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }
    
    .help-text {
        font-size: 0.875rem;
        color: #718096;
        margin-top: 0.5rem;
    }
</style>

<form action="{{ route('admin.tickets.events.config.update', $event->id) }}" method="POST" id="configForm">
    @csrf
    
    <!-- Authentication & Selection Section -->
    <div class="config-form-section">
        <h5 class="config-form-section-title">
            <i class="fas fa-shield-alt"></i>
            Authentication & Selection Settings
        </h5>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Authentication Policy</label>
                    <select name="auth_policy" class="form-select" required>
                        <option value="guest" {{ ($config->auth_policy ?? 'guest') == 'guest' ? 'selected' : '' }}>Guest Allowed (No login required)</option>
                        <option value="otp_required" {{ ($config->auth_policy ?? '') == 'otp_required' ? 'selected' : '' }}>OTP Required</option>
                        <option value="login_required" {{ ($config->auth_policy ?? '') == 'login_required' ? 'selected' : '' }}>Login Required</option>
                    </select>
                    <div class="help-text">Choose how users authenticate when registering</div>
                    @error('auth_policy')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label class="form-label">Selection Mode</label>
                    <select name="selection_mode" class="form-select" required>
                        <option value="same_ticket" {{ ($config->selection_mode ?? 'same_ticket') == 'same_ticket' ? 'selected' : '' }}>Same Ticket for All Delegates</option>
                        <option value="per_delegate" {{ ($config->selection_mode ?? '') == 'per_delegate' ? 'selected' : '' }}>Per-Delegate Selection</option>
                    </select>
                    <div class="help-text">How tickets are assigned to delegates</div>
                    @error('selection_mode')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Feature Toggles Section -->
    <div class="config-form-section">
        <h5 class="config-form-section-title">
            <i class="fas fa-toggle-on"></i>
            Feature Toggles
        </h5>
        
        <div class="row">
            <div class="col-md-6">
                <div class="switch-container">
                    <label class="switch-label" for="allow_subcategory">
                        Allow Subcategory Selection
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="allow_subcategory" id="allow_subcategory" value="1"
                               {{ ($config->allow_subcategory ?? true) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="switch-container">
                    <label class="switch-label" for="allow_day_select">
                        Allow Day Selection
                    </label>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="allow_day_select" id="allow_day_select" value="1"
                               {{ ($config->allow_day_select ?? false) ? 'checked' : '' }}>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Email & Receipt Section -->
    <div class="config-form-section">
        <h5 class="config-form-section-title">
            <i class="fas fa-envelope"></i>
            Email & Receipt Settings
        </h5>
        
        <div class="form-group">
            <label class="form-label">Email CC (JSON array)</label>
            <textarea name="email_cc_json" class="form-control" rows="4" 
                      placeholder='["email1@example.com", "email2@example.com"]'>{{ json_encode($config->email_cc_json ?? [], JSON_PRETTY_PRINT) }}</textarea>
            <div class="help-text">Enter email addresses as a JSON array. Example: ["email1@example.com", "email2@example.com"]</div>
            @error('email_cc_json')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Receipt Pattern</label>
            <input type="text" name="receipt_pattern" class="form-control" 
                   value="{{ $config->receipt_pattern ?? '' }}" 
                   placeholder="TKT-{{ $event->slug ?? 'EVENT' }}-{{ $event->event_year }}-{seq}">
            <div class="help-text">Use placeholders: {event}, {year}, {seq} for dynamic receipt numbers</div>
            @error('receipt_pattern')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Public Registration Section -->
    <div class="config-form-section">
        <h5 class="config-form-section-title">
            <i class="fas fa-globe"></i>
            Public Registration
        </h5>
        
        <div class="switch-container">
            <label class="switch-label" for="is_active">
                Enable Public Registration
                <small class="d-block text-muted" style="font-weight: normal; margin-top: 0.25rem;">
                    Allow public users to register for tickets
                </small>
            </label>
            <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1"
                               {{ ($config->is_active ?? false) ? 'checked' : '' }}>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="form-actions">
        <button type="submit" class="btn btn-save">
            <i class="fas fa-save me-2"></i>Save Configuration
        </button>
        <a href="{{ route('admin.tickets.events') }}" class="btn btn-back">
            <i class="fas fa-arrow-left me-2"></i>Back to Events
        </a>
    </div>
</form>

<script>
    // Form validation and submission
    document.getElementById('configForm').addEventListener('submit', function(e) {
        // Validate JSON format for email_cc_json
        const emailCcField = document.querySelector('textarea[name="email_cc_json"]');
        if (emailCcField.value.trim()) {
            try {
                JSON.parse(emailCcField.value);
            } catch (error) {
                e.preventDefault();
                alert('Invalid JSON format for Email CC field. Please check the format.');
                emailCcField.focus();
                return false;
            }
        }
    });
</script>

