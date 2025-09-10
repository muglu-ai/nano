@extends('attendee.app')

@section('title', 'Event Visitor Registration - SEMICON India 2025') 

@section('content')
<style>
    .captcha-img {
        user-select: none;
        pointer-events: none;
    }
</style>

<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <img src="https://portal.semiconindia.org/assets/images/semi.jpg" class="img-fluid">
        </div>
    </div>

    <h2 class="mt-4">Event Visitor Registration</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the errors below.</strong>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                setStepActive(0); // Force return to step 1
            });
        </script>
    @endif

    @php $oldAttendees = old('attendees', []); @endphp

    <form id="registrationForm" method="POST" action="{{ route('visitor_register') }}" enctype="multipart/form-data">
        @csrf
        @php $source = request()->query('source', 'default_source'); @endphp

        <div id="attendeeContainer">
            @foreach (range(0, $maxAttendees - 1) as $index)
                <x-attendee-form :index="$index" :data="$oldAttendees[$index] ?? []" :productCategories="$productCategories" :sectors="$natureOfBusiness" :active="($index === 0)" />
            @endforeach
        </div>

        <button type="button" id="addAttendeeBtn" class="btn btn-secondary my-3 {{ $maxAttendees <= 1 ? 'd-none' : '' }}">
            Add Attendee
        </button>

        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" name="captcha" id="captcha" class="form-control" maxlength="6"
                    placeholder="Enter Captcha" required>
            </div>
            <div class="col-md-6">
                <div id="captcha-img" class="captcha-img">{!! $captchaSvg !!}</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<script>
function setStepActive(index) {
    document.querySelectorAll('[data-attendee-step]').forEach((step, i) => {
        step.style.display = i === index ? '' : 'none';
        step.querySelectorAll('input, select, textarea').forEach(el => {
            el.disabled = i !== index;
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
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
                //  and don't change step
                this.blur(); // Remove focus to prevent form submission
                const firstInvalid = step1.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                }
                
               

                event.preventDefault();
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
});
</script>
@endsection
