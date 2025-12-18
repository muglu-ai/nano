@extends('attendee.app')

@section('title', 'Visitor Registration - {{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}')

@section('content')
<div class="container py-5">
    {{-- Header Logos --}}
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-center align-items-center">
            <div class="row w-100">
                <div class="col-md-4 text-center">
                    <img src="{{ asset('asset/img/logos/SEMI_IESA_logo.png') }}" alt="SEMI IESA Logo" style="max-height: 80px;">
                </div>
                <div class="col-md-4 text-center">
                    <img src="{{ asset('asset/img/logos/meity-logo.png') }}" alt="MeitY Logo" style="max-height: 80px;">
                </div>
                <div class="col-md-4 text-center">
                    <img src="{{ asset('asset/img/logos/ism_logo.png') }}" alt="ISM Logo" style="max-height: 80px;">
                </div>
            </div>
        </div>
    </div>

    {{-- Page Heading --}}
    <h2 class="text-center mt-4">Visitor Registration</h2>

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger" id="error-alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <style>
                #error-alert {
                    animation: fadeOutAlert 0.5s ease-in 29.5s forwards;
                }
                @keyframes fadeOutAlert {
                    to {
                        opacity: 0;
                        visibility: hidden;
                    }
                }
            </style>
            <script>
                setTimeout(() => {
                    const alert = document.getElementById('error-alert');
                    if (alert) alert.style.display = 'none';
                }, 30000);
                document.addEventListener('DOMContentLoaded', () => setStepActive(0));
            </script>
        </div>
    @endif

    @php
        $oldAttendees = old('attendees', []);
        $source = request()->query('source', 'default_source');
    @endphp

    <form id="registrationForm" method="POST" action="{{ route('visitor_register') }}" enctype="multipart/form-data">
        @csrf

        <div id="attendeeContainer">
            @foreach (range(0, $maxAttendees - 1) as $index)
                <x-attendee-form_new 
                    :index="$index" 
                    :data="$oldAttendees[$index] ?? []" 
                    :productCategories="$productCategories" 
                    :sectors="$natureOfBusiness" 
                    :captchaSvg="$captchaSvg"
                    :countries="$countries"
                />
            @endforeach
        </div>
    </form>
</div>

{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Custom JS --}}
<script src="{{ asset('assets/js/visitor-form.js') }}"></script>

{{-- Inline Step Control Logic --}}
<script>
    function setStepActive(index) {
        document.querySelectorAll('[data-attendee-step]').forEach((step, i) => {
            step.style.display = i === index ? '' : 'none';
            step.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = i !== index;
            });
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        let currentStep = 0;
        setStepActive(currentStep);

        document.querySelectorAll('.attendee-next-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const index = this.dataset.index;
                const step1 = document.getElementById('attendee-step-1-' + index);
                const inputs = step1.querySelectorAll('input, select, textarea');
                let valid = true;

                inputs.forEach(input => {
                    if (input.hasAttribute('required') && !input.value.trim()) {
                        valid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (valid) {
                    document.getElementById('attendee-step-1-' + index).style.display = 'none';
                    document.getElementById('attendee-step-2-' + index).style.display = '';
                    document.getElementById('step1-tab-' + index).classList.remove('active');
                    document.getElementById('step2-tab-' + index).classList.add('active');
                } else {
                    alert('Please fill in all required fields in Step 1 before proceeding.');
                }
            });
        });

        document.querySelectorAll('.attendee-prev-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const index = this.dataset.index;
                document.getElementById('attendee-step-2-' + index).style.display = 'none';
                document.getElementById('attendee-step-1-' + index).style.display = '';
                document.getElementById('step2-tab-' + index).classList.remove('active');
                document.getElementById('step1-tab-' + index).classList.add('active');
            });
        });

        // Block direct click on Step 2 tab if Step 1 is incomplete
        document.querySelectorAll('[id^=step2-tab-]').forEach(tab => {
            tab.addEventListener('click', function (e) {
                const index = this.id.split('-').pop();
                const step1 = document.getElementById('attendee-step-1-' + index);
                const inputs = step1.querySelectorAll('input, select, textarea');
                let valid = true;

                inputs.forEach(input => {
                    if (input.hasAttribute('required') && !input.value.trim()) {
                        valid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (!valid) {
                    alert('Please fill in all required fields in Step 1 before proceeding.');
                    e.preventDefault();
                } else {
                    document.getElementById('attendee-step-1-' + index).style.display = 'none';
                    document.getElementById('attendee-step-2-' + index).style.display = '';
                    document.getElementById('step1-tab-' + index).classList.remove('active');
                    document.getElementById('step2-tab-' + index).classList.add('active');
                }
            });
        });
    });
</script>
@endsection
