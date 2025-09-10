@extends('attendee.app')

@section('title', 'SEMICON India 2025: Registration')

@section('content')
<style>
    .captcha-img {
        user-select: none;
        pointer-events: none;
    }
</style>

<div class="container pt-5 pb-4">
    {{-- <div class="table-responsive mb-4">
        <table class="table table-borderless align-middle mb-0" style="width:100%; max-width:900px; margin:auto;">
            <tr>
                <td class="text-center p-2 logo-cell" style="width:25%;">
                    <img class="logo2"
                        src="{{ asset('asset/img/logos/meity-logo.png') }}"
                        alt="Ministry of Electronics & IT Logo">
                </td>
                <td class="text-center p-2 logo-cell" style="width:25%;">
                    <img class="logo3"
                        src="{{ asset('asset/img/logos/ism_logo.png') }}"
                        alt="ISM Logo">
                </td>
                <td class="text-center p-2 logo-cell" style="width:25%;">
                    <img class="logo4"
                        src="{{ asset('asset/img/logos/DIC_Logo.webp') }}"
                        alt="Digital India Logo">
                </td>
                <td class="text-center p-2 logo-cell" style="width:25%;">
                    <img class="logo1"
                        src="{{ asset('asset/img/logos/SEMI_IESA_logo.png') }}"
                        alt="SEMI IESA Logo">
                </td>
            </tr>
        </table>
    </div> --}}
    <style>
        .logo-cell img {
            max-height: 150px;
            max-width: 100%;
            width: 200px;
            transition: width 0.2s, max-height 0.2s;
        }
        @media (max-width: 991.98px) {
            .logo-cell img {
                width: 180px;
                max-height: 68px;
            }
        }
        @media (max-width: 767.98px) {
            .table-responsive table tr {
                display: flex;
                flex-wrap: nowrap;
            }
            .table-responsive table td {
                width: 25% !important;
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
                margin-bottom: 0;
                padding: 6px !important;
            }
            .logo-cell img {
                width: 80px;
                max-height: 36px;
            }
        }
        @media (max-width: 480px) {
            .logo-cell img {
                width: 80px;
                max-height: 35px;
            }
        }
    </style>

    <div class="alert alert-info mb-4" style="background: #e9f7fe; color: #0c5460; border-color: #b8daff; font-weight: 500; text-align: center;">
    <span>
        &#9888; Registrations for the Inaugural Session are now closed.<br>
        You can still watch the session live on our official YouTube channel through the link below:<br>
        <a href="https://www.youtube.com/@IndiaSemiconductorMission/streams" target="_blank" style="color: inherit; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
            <i class="fab fa-youtube" style="color: #ff0000; font-size: 1.2em; vertical-align: middle;"></i>
            <span style="color: #0056b3; text-decoration: underline;">@IndiaSemiconductorMission/streams</span>
        </a>
    </span>
</div>


    <h2 class="text-center">SEMICON India 2025: Registration</h2>

    @if ($errors->any())
    <div class="alert alert-danger" id="error-alert">
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
            setTimeout(function() {
                var alert = document.getElementById('error-alert');
                if (alert) alert.style.display = 'none';
            }, 30000);
        </script>
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setStepActive(0);
        });
    </script>
    @endif

    @php $oldAttendees = old('attendees', []); @endphp

    <form id="registrationForm" method="POST" action="{{ route('visitor_register') }}" enctype="multipart/form-data">
        @csrf
        @php $source = request()->query('source', 'default_source'); @endphp

        <div id="attendeeContainer">
            @foreach (range(0, $maxAttendees - 1) as $index)
            <x-attendee-form
                :index="$index"
                :data="$oldAttendees[$index] ?? []"
                :productCategories="$productCategories"
                :sectors="$natureOfBusiness"
                :active="($index === 0)"
                :captchaSvg="$captchaSvg"
                :countries="$countries" />
            @endforeach
        </div>

        {{-- <button type="button" id="addAttendeeBtn" class="btn btn-secondary my-3 {{ $maxAttendees <= 1 ? 'd-none' : '' }}">
        Add Attendee
        </button> --}}

        {{-- <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" name="captcha" id="captcha" class="form-control" maxlength="6"
                    placeholder="Enter Captcha" required>
            </div>
            <div class="col-md-6">
                <div id="captcha-img" class="captcha-img">{!! $captchaSvg !!}</div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button> --}}
    </form>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    //on page load set the loadCountries function to load the countries
    document.addEventListener('DOMContentLoaded', function() {
        loadCountries();
    });


    function setStepActive(index) {
        document.querySelectorAll('[data-attendee-step]').forEach((step, i) => {
            step.style.display = i === index ? '' : 'none';
            step.querySelectorAll('input, select, textarea').forEach(el => {
                el.disabled = i !== index;
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        let currentStep = 0;
        setStepActive(currentStep);

        document.querySelectorAll('.attendee-next-btn').forEach(btn => {
            btn.addEventListener('click', function(event) {
                const index = this.dataset.index;
                const step1 = document.getElementById('attendee-step-1-' + index);
                const inputs = step1.querySelectorAll('input, select, textarea');
                let valid = true;
                const emailSpan = step1.querySelector('span[id^="verification_0"]');
                if (emailSpan && emailSpan.style.display !== 'none' && emailSpan.getAttribute('data-verified') !== 'true') {
                    alert('Please verify your email before proceeding.');
                    event.preventDefault();
                    return;
                }


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
                    event.preventDefault();
                    const firstInvalid = step1.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                }
            });
        });

        document.querySelectorAll('.attendee-prev-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = this.dataset.index;
                document.getElementById('attendee-step-2-' + index).style.display = 'none';
                document.getElementById('attendee-step-1-' + index).style.display = '';
                document.getElementById('step2-tab-' + index).classList.remove('active');
                document.getElementById('step1-tab-' + index).classList.add('active');
            });
        });

        // Prevent Step 2 tab from being clicked directly
        document.querySelectorAll('[id^=step2-tab-]').forEach(tab => {
            tab.addEventListener('click', function(e) {
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
                    event.preventDefault();
                    // const firstInvalid = step1.querySelector('.is-invalid');
                    // if (firstInvalid) {
                    //     firstInvalid.focus();
                    // }
                    // return false; // 🔥 This stops execution
                }

                // Only runs if valid
                document.getElementById('attendee-step-1-' + index).style.display = 'none';
                document.getElementById('attendee-step-2-' + index).style.display = '';
                document.getElementById('step1-tab-' + index).classList.remove('active');
                document.getElementById('step2-tab-' + index).classList.add('active');
            });
        });


    });
</script>


<script>
    $(document).ready(function() {
        // On country change, load states for that attendee row
        $('.country-dropdown').on('change', function() {
            var countryId = $(this).val();
            var idx = $(this).data('index');
            var $stateDropdown = $('.state-dropdown[data-index="' + idx + '"]');
            console.log('Country ID:', countryId, 'Index:', idx);
            $stateDropdown.html('<option value="">Loading...</option>');
            if (countryId) {
                $.ajax({
                    url: "{{ route('get.states') }}",
                    type: "POST",
                    data: {
                        country_id: countryId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(states) {
                        var options = '<option value="">--- Select ---</option>';
                        $.each(states, function(i, state) {
                            options += '<option value="' + state.id + '">' + state.name + '</option>';
                        });
                        $stateDropdown.html(options);
                    },
                    error: function() {
                        $stateDropdown.html('<option value="">--- Select ---</option>');
                        alert("Error fetching states. Please try again.");
                    }
                });
            } else {
                $stateDropdown.html('<option value="">--- Select ---</option>');
            }
        });

        // If editing, trigger state load for pre-selected country
        $('.country-dropdown').each(function() {
            var countryId = $(this).val();
            var idx = $(this).data('index');
            var selectedState = "{{ $data['state'] ?? '' }}";
            if (countryId) {
                var $stateDropdown = $('.state-dropdown[data-index="' + idx + '"]');
                $.ajax({
                    url: "{{ route('get.states') }}",
                    type: "POST",
                    data: {
                        country_id: countryId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(states) {
                        var options = '<option value="">--- Select ---</option>';
                        $.each(states, function(i, state) {
                            options += '<option value="' + state.id + '" ' +
                                (selectedState == state.id ? 'selected' : '') + '>' + state.name + '</option>';
                        });
                        $stateDropdown.html(options);
                    }
                });
            }
        });
    });
</script>

<script>
const inputNames = [
  'attendees[0][first_name]',
  'attendees[0][middle_name]',
  'attendees[0][last_name]',
  'attendees[0][designation]',
  'attendees[0][address]',
  'attendees[0][city]'
];

inputNames.forEach(name => {
  const input = document.querySelector(`input[name="${name}"], textarea[name="${name}"]`);
  if (input) {
    input.addEventListener('input', function() {
      this.value = window.transliteration ?
        window.transliteration.transliterate(this.value).replace(/[^a-zA-Z0-9@!$%^&*()_ .\-+=~`]/g, '') :
        this.value.replace(/[^a-zA-Z0-9@!$%^&*()_ .\-+=~`]/g, '');
    });
  }
});

</script>

<script>
    setInterval(function() {
        var styleId = 'ts-custom-style';
        if (!document.getElementById(styleId)) {
            var style = document.createElement('style');
            style.id = styleId;
            style.innerHTML = `
            .ts-dropdown,
            .ts-control,
            .ts-control input {
                color: #000000 !important;
                line-height: 100% !important;
                font-family: inherit !important;
                font-size: inherit !important;
            }
        `;
            document.head.appendChild(style);
        }
    }, 2000);
</script>




@endsection