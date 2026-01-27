@php
    $currentStep = $currentStep ?? 1; // 1 = Registration, 2 = Preview, 3 = Confirmation
@endphp

<div class="registration-progress mb-1">
    <div class="progress-steps">
        <!-- Step 1: Registration -->
        <div class="progress-step {{ $currentStep >= 1 ? 'active' : '' }} {{ $currentStep > 1 ? 'completed' : '' }}">
            <div class="step-circle">
                @if($currentStep > 1)
                    <i class="fas fa-check"></i>
                @else
                    <span>1</span>
                @endif
            </div>
            <div class="step-label">Delegate Information</div>
        </div>
        
        <!-- Step 2: Preview -->
        <div class="progress-step {{ $currentStep >= 2 ? 'active' : '' }} {{ $currentStep > 2 ? 'completed' : '' }}">
            <div class="step-circle">
                @if($currentStep > 2)
                    <i class="fas fa-check"></i>
                @else
                    <span>2</span>
                @endif
            </div>
            <div class="step-label">Review & Preview</div>
        </div>
        
        <!-- Step 3: Confirmation -->
        <div class="progress-step {{ $currentStep >= 3 ? 'active' : '' }} {{ $currentStep > 3 ? 'completed' : '' }}">
            <div class="step-circle">
                @if($currentStep > 3)
                    <i class="fas fa-check"></i>
                @else
                    <span>3</span>
                @endif
            </div>
            <div class="step-label">Registration Confirmation</div>
        </div>
    </div>
</div>

<style>
.registration-progress {
    padding: 2rem 0;
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--progress-inactive);
    z-index: 0;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 1;
    flex: 1;
}

.step-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--progress-inactive);
    border: 3px solid var(--progress-inactive);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-secondary);
    transition: all 0.3s ease;
    margin-bottom: 0.75rem;
}

.progress-step.active .step-circle {
    background: var(--progress-active);
    border-color: var(--progress-active);
    color: white;
    box-shadow: 0 4px 12px rgba(106, 27, 154, 0.3);
    transform: scale(1.1);
}

.progress-step.completed .step-circle {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

/* Ensure completed overrides active when both are present */
.progress-step.completed.active .step-circle {
    background: #28a745;
    border-color: #28a745;
    color: white;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.step-label {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-align: center;
    transition: color 0.3s ease;
}

.progress-step.active .step-label {
    color: var(--progress-active);
    font-weight: 700;
}

.progress-step.completed .step-label {
    color: var(--text-primary);
}

/* Progress line between steps */
.progress-step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 25px;
    left: 50%;
    width: 100%;
    height: 3px;
    background: var(--progress-inactive);
    z-index: -1;
    transition: background 0.3s ease;
}

.progress-step.completed:not(:last-child)::after {
    background: #28a745;
}

.progress-step.active:not(:last-child)::after {
    background: linear-gradient(to right, #28a745 0%, var(--progress-active) 50%, var(--progress-inactive) 100%);
}

.progress-step.completed.active:not(:last-child)::after {
    background: linear-gradient(to right, #28a745 0%, var(--progress-inactive) 100%);
}

/* Responsive */
@media (max-width: 768px) {
    .step-label {
        font-size: 0.75rem;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        font-size: 0.9rem;
    }
    
    .progress-steps::before {
        top: 20px;
    }
    
    .progress-step:not(:last-child)::after {
        top: 20px;
    }
}
</style>
