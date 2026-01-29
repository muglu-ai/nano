@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')
    <style>
        @media (min-width: 500px) {
            .progress-bar2 {
                display: none !important;
            }
        }

        .form-check-input.is-filled {
            color: black;
        }

        .form-label, label {
            color: #000;

        }

        .red-label {
            color: red;
            font-weight: bold;
        }

        .textB {
            color: #000 !important;
        }

        .custom-hr {
            border: none;
            height: 3px;
            background: #bfb8b8;
            width: 100%;
            margin: 20px auto;
        }

        .dropdown-item:hover {
            background-color: transparent !important;
        }
        .form-check-label {
            word-wrap: break-word;
            display: inline-block;  /* Makes sure the label behaves properly inside the flex container */
            max-width: 100%;        /* Ensures it doesn't overflow the container */
        }

    </style>
    <div class="container py-2">
        <div class="row min-vh-220 mt-5">
            <div class="col-lg-12 col-md-10 col-12 m-auto">
                <div class="card">
                    <div class="card-header p-0 position-relative mt-n5 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="multisteps-form__progress">
                                <button class="multisteps-form__progress-btn js-active" disabled>
                                    <span>1. Show Profile</span>
                                </button>
                                <button class="multisteps-form__progress-btn js-active" disabled>2. Application
                                    Form
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>3. Terms and
                                    Conditions
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>4. Review
                                </button>
                            </div>
                            <small class="progress-bar2 d-block text-center text-white">2. Product Info</small>

                        </div>
                    </div>
                    <div class="card-body" id="card-body">
                        <form class="multisteps-form__form" id="step2" action="{{ route('event-participation.store') }}"
                              method="POST">
                            @csrf
                            @php
                                $isDisabled = isset($application) && $application->submission_status != 'in progress' ? 'disabled' : '';
                                $selectedSectors = $application->sectors->pluck('id')->toArray() ?? [];
                            @endphp

                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                 data-animation="FadeIn">
                                <div class="multisteps-form__content">
                                    <div class="text-sm text-justify mb-2 ms-1">
                                        <p>
                                            Please complete the form below.  The organizer will review our application and will approve or reject based on your submission.  The status of application can be reviewed in the Review Application menu.  Once the application is approved, the next steps will be enabled.  Rejected applications can be re-applied with necessary update information.
                                    </p>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <label class="red-label" for="participation_type">Please select an option of
                                                your participation in SEMICON India <span
                                                    class="red-label">*</span></label>
                                            <div style="display: flex; flex-wrap: wrap; gap: 10px;"
                                                 class="form-check is-filled">
                                                @foreach($participation_type as $type => $status)
                                                    <div style="display: flex; align-items: center;">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="participation_type"
                                                            value="{{ $type }}"
                                                            id="participation_{{ $loop->index }}"
                                                            {{ $status == 'disabled' || $isDisabled }}
                                                            {{ old('participation_type', $application->participation_type) == $type || $type == 'Onsite' ? 'checked' : '' }}
                                                            {{$isDisabled}}
                                                            style="margin-right: 5px;"
                                                            required
                                                        >
                                                        <label for="participation_{{ $loop->index }}"
                                                               style=" margin-top: 10px;">{{ $type }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>


                                    </div>

                                    <div class="row mt-3">
                                        <div class="row mt-3">
                                            <div class="col-12 col-sm-6 mt-3">
                                                <label class="red-label" for="region">Region <span
                                                        class="red-label">*</span></label>
                                                <div style="display: flex; flex-wrap: wrap; gap: 40px;"
                                                     class="form-check is-filled">
                                                    @foreach(['India', 'International'] as $region)
                                                        <div style="display: flex; align-items: center;">
                                                            <input
                                                                class="form-check-input"
                                                                type="radio"
                                                                name="region"
                                                                value="{{ $region }}"
                                                                id="region_{{ $loop->index }}"
                                                                {{ $isDisabled }}
                                                                {{ old('region', $application->region) == $region || $region == 'India' ? 'checked' : '' }}
                                                                style="margin-right: 5px;"
                                                                required
                                                            >
                                                            <label for="region_{{ $loop->index }}"
                                                                   style=" margin-top: 10px;">{{ $region }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 mt-3">
                                                <label class="red-label" for="previous_participation">Previous
                                                    Participation <span class="red-label">*</span></label>
                                                <div style="display: flex; flex-wrap: wrap; gap: 40px;"
                                                     class="form-check is-filled">
                                                    <div style="display: flex; align-items: center; ">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="previous_participation"
                                                            value="1"
                                                            id="previous_participation_yes"
                                                            {{ $isDisabled }}
                                                            {{ old('previous_participation', $application->participated_previous) == 1 ? 'checked' : '' }}
                                                            style="margin-right: 5px;"
                                                            required
                                                        >
                                                        <label for="previous_participation_yes"
                                                               style=" margin-top: 10px;">Yes</label>
                                                    </div>
                                                    <div style="display: flex; align-items: center;">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="previous_participation"
                                                            value="0"
                                                            id="previous_participation_no"
                                                            {{ $isDisabled }}
                                                            {{ old('previous_participation', $application->participated_previous) == 0 ? 'checked' : '' }}
                                                            style="margin-right: 5px;"
                                                            required
                                                        >
                                                        <label for="previous_participation_no"
                                                               style=" margin-top: 10px;">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr class="custom-hr">
                                    <div class="row mt-3 align-items-center me-2">
                                        <!-- Stall Categories -->
                                        <div class="col-12 col-sm-6 d-flex align-items-start">
                                            <label for="stall_category" class=" red-label form-label me-3 mt-1 textB">Stall
                                                Categories <span class="red-label">*</span></label>
                                            <div class="d-flex flex-wrap gap-3">
                                                @foreach($stall_type as $type)
                                                    <div class="form-check ">
                                                        <input
                                                            class="form-check-input"
                                                            type="radio"
                                                            name="stall_category"
                                                            value="{{ $type }}"
                                                            id="stall_{{ $loop->index }}"
                                                            {{ $isDisabled }}
                                                            {{ old('stall_category', $application->stall_category) == $type ? 'checked' : '' }}
                                                            required
                                                            onchange="updateStallSize()"
                                                            style="color:#000">
                                                        <label for="stall_{{ $loop->index }}"
                                                               class="form-check-label ms-2">{{ $type }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <script>
                                            function updateStallSize() {
                                                //console.log("Updating stall size dropdown...");
                                                let selectedCategory = document.querySelector('input[name="stall_category"]:checked')?.value;
                                                let stallSizeDropdown = document.getElementById("stall_size");


                                                stallSizeDropdown.innerHTML = '<li><a class="dropdown-item" href="#" data-value="">Select Stall Size</a></li>'; // Reset dropdown

                                                let minSize = selectedCategory === "Bare Space" ? 18 : 9; // Bare starts at 18, Shell at 9
                                                let maxSize = 900;

                                                for (let i = minSize; i <= maxSize; i += 9) { // Incrementing by 9 sqm
                                                    let option = document.createElement("li");
                                                    option.innerHTML = `<a class="dropdown-item" href="#" data-value="${i}">${i} sqm</a>`;
                                                    stallSizeDropdown.appendChild(option);
                                                }

                                                let interestedSqm = {{ $application->interested_sqm ?? 'null' }};
                                                //console.log("Interested SQM:", interestedSqm);
                                                if (interestedSqm) {
                                                    let selectedItem = document.querySelector(`#stall_size a[data-value="${interestedSqm}"]`);
                                                    console.log("Selected Item:", selectedItem);
                                                    if (selectedCategory === "Bare Space" && interestedSqm < 18) {
                                                        //console.log("Invalid SQM for Bare Space. Resetting.");
                                                        document.getElementById("stallSizeDropdown").textContent = "Select Stall Size";
                                                        document.getElementById("interested_sqm").value = null;

                                                    } else if (selectedItem) {
                                                        document.getElementById("interested_sqm").value = interestedSqm;
                                                        document.getElementById("stallSizeDropdown").textContent = selectedItem.textContent;
                                                    }
                                                }
                                                let selectedItem = document.querySelector(`#stall_size a[data-value="${interestedSqm}"]`);
                                                if(selectedCategory === "Bare Space"){
                                                    document.getElementById("interested_sqm").value = 18;
                                                    document.getElementById("stallSizeDropdown").textContent = "18 sqm";
                                                }
                                                if (selectedCategory === "Bare Space" && interestedSqm < 18) {
                                                    //console.log("Invalid SQM for Bare Space. Resetting.");
                                                    document.getElementById("stallSizeDropdown").textContent = "Select Stall Size";
                                                    document.getElementById("interested_sqm").value = null;

                                                } else if (selectedItem) {
                                                    document.getElementById("interested_sqm").value = interestedSqm;
                                                    document.getElementById("stallSizeDropdown").textContent = selectedItem.textContent;
                                                }
                                            }

                                            document.addEventListener("DOMContentLoaded", function () {
                                                updateStallSize();

                                                document.getElementById("stall_size").addEventListener("click", function (event) {
                                                    if (event.target.tagName === "A") {
                                                        event.preventDefault();
                                                        let value = event.target.getAttribute("data-value");

                                                        if (value) {
                                                            document.getElementById("interested_sqm").value = value;
                                                            document.getElementById("stallSizeDropdown").textContent = event.target.textContent;
                                                            document.getElementById("stallSizeError").style.display = "none"; // Hide error message
                                                        }
                                                    }
                                                });

                                                document.getElementById("step2").addEventListener("submit", function (event) {
                                                    let interestedSqm = document.getElementById("interested_sqm").value;
                                                    // console.log(interestedSqm);
                                                    if (!interestedSqm) {
                                                        event.preventDefault();
                                                        document.getElementById("stallSizeError").style.display = "block";
                                                       document.getElementById("stallSizeError").scrollIntoView({ behavior: "smooth" });
                                                    }
                                                });
                                            });
                                        </script>

                                        <!-- Interested SQM -->
                                        <div class="col-12 col-sm-6 d-flex align-items-center">
                                            <label for="interested_sqm" class="red-label form-label me-3">
                                                Interested SQM <span class="red-label">*</span>
                                                <div id="stallSizeError" class="text-danger mt-2" style="display: none;">Please select a stall size.</div>
                                            </label>

                                            <div class="dropdown w-auto">
                                                <button class="btn btn-secondary dropdown-toggle" type="button" id="stallSizeDropdown" data-bs-toggle="dropdown"
                                                        aria-expanded="false" style="max-height: 200px; overflow-y: auto; color: #FFFFFF">
                                                    Select Stall Size
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="stallSizeDropdown" id="stall_size"
                                                    style="max-height: 200px; overflow-y: auto;">
                                                    <li><a class="dropdown-item" href="#" data-value="">Select Stall Size</a></li>
                                                </ul>
                                                <input type="hidden" name="interested_sqm" id="interested_sqm" required>

                                            </div>
                                        </div>






                                        {{--                                    <div class="row mt-3">--}}
                                        {{--                                        <div class="col-12 col-sm-6 mt-3">--}}
                                        {{--                                            <label for="interested_sqm" class="mr-8 mx-4">Interested SQM *</label>--}}
                                        {{--                                        </div>--}}
                                        {{--                                        <div class="col-12 col-sm-6 mt-3">--}}
                                        {{--                                            <div class="multisteps-form__content">--}}
                                        {{--                                                <select class="multisteps-form__input form-control" name="interested_sqm" id="choice-sqm" {{ $isDisabled }} required>--}}
                                        {{--                                                    <option value="">Select SQM</option> <!-- Options will be loaded dynamically -->--}}
                                        {{--                                                </select>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}
                                        {{--                                    </div>--}}








                                        {{--                                    <div class="row mt-3">--}}
                                        {{--                                        <div class="col-12 col-sm-12 mt-3">--}}
                                        {{--                                            <label for="stall_category">Stall Categories *</label>--}}
                                        {{--                                            <div style="display: flex; flex-wrap: wrap; gap: 10px;"--}}
                                        {{--                                                 class="form-check is-filled">--}}
                                        {{--                                                @foreach($stall_type as $type)--}}
                                        {{--                                                    <div style="display: flex; align-items: center;">--}}
                                        {{--                                                        <input--}}
                                        {{--                                                            class="form-check-input"--}}
                                        {{--                                                            type="radio"--}}
                                        {{--                                                            name="stall_category"--}}
                                        {{--                                                            value="{{ $type }}"--}}
                                        {{--                                                            id="stall_{{ $loop->index }}"--}}
                                        {{--                                                            {{ $isDisabled }}--}}
                                        {{--                                                            {{ old('stall_category', $application->stall_category) == $type ? 'checked' : '' }}--}}
                                        {{--                                                            required--}}
                                        {{--                                                            style="margin-right: 5px;"--}}

                                        {{--                                                        >--}}
                                        {{--                                                        <label for="stall_{{ $loop->index }}" style=" margin-top: 10px;">{{ $type }}</label>--}}
                                        {{--                                                    </div>--}}
                                        {{--                                                @endforeach--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}
                                        {{--                                    </div>--}}


                                        {{--                                    @php--}}
                                        {{--                                        $selectedCategory = old('stall_category', $application->stall_category);--}}
                                        {{--                                        $startValue = ($selectedCategory === 'Bare Space') ? 18 : 9;--}}
                                        {{--                                    @endphp--}}

                                        {{--                                    <div class="row mt-3">--}}
                                        {{--                                        <div class="col-12 col-sm-6 mt-3">--}}
                                        {{--                                            <label for="interested_sqm" class="mr-8 mx-4">Interested SQM *</label>--}}
                                        {{--                                        </div>--}}
                                        {{--                                        <div class="col-12 col-sm-6 mt-3">--}}
                                        {{--                                            <div class="multisteps-form__content">--}}
                                        {{--                                                <select--}}
                                        {{--                                                    class="multisteps-form__input form-control"--}}
                                        {{--                                                    name="interested_sqm"--}}
                                        {{--                                                    id="choice-sqm"--}}
                                        {{--                                                    {{ $isDisabled }}--}}
                                        {{--                                                    required--}}
                                        {{--                                                >--}}
                                        {{--                                                    @for ($i = $startValue; $i <= 900; $i += 9)--}}
                                        {{--                                                        <option value="{{ $i }}" {{ old('interested_sqm', $application->interested_sqm) == $i ? 'selected' : '' }}>--}}
                                        {{--                                                            {{ $i }} sqm--}}
                                        {{--                                                        </option>--}}
                                        {{--                                                    @endfor--}}
                                        {{--                                                </select>--}}
                                        {{--                                            </div>--}}
                                        {{--                                        </div>--}}
                                        {{--                                    </div>--}}

                                        {{--                                    <script>--}}
                                        {{--                                        document.addEventListener('DOMContentLoaded', function () {--}}
                                        {{--                                            function updateSQMOptions() {--}}
                                        {{--                                                const stallTypeRadios = document.querySelectorAll('input[name="stall_category"]');--}}
                                        {{--                                                const sqmSelect = document.getElementById('choice-sqm');--}}

                                        {{--                                                if (!sqmSelect || stallTypeRadios.length === 0) {--}}
                                        {{--                                                    console.error("Error: SQM dropdown or radio buttons not found!");--}}
                                        {{--                                                    return;--}}
                                        {{--                                                }--}}

                                        {{--                                                let selectedStallType = document.querySelector('input[name="stall_category"]:checked');--}}

                                        {{--                                                if (!selectedStallType) {--}}
                                        {{--                                                    console.warn("No stall type selected. Using default.");--}}
                                        {{--                                                    return;--}}
                                        {{--                                                }--}}

                                        {{--                                                selectedStallType = selectedStallType.value.trim(); // Ensure clean string comparison--}}

                                        {{--                                                console.log("Selected Stall Type:", selectedStallType);--}}

                                        {{--                                                // Determine start value--}}
                                        {{--                                                const startValue = (selectedStallType === "Bare Space") ? 18 : 9;--}}

                                        {{--                                                console.log("Start Value:", startValue);--}}

                                        {{--                                                // Store previous selection if still valid--}}
                                        {{--                                                let previousValue = parseInt(sqmSelect.value, 10) || startValue;--}}

                                        {{--                                                // Clear current options--}}
                                        {{--                                                sqmSelect.innerHTML = '';--}}

                                        {{--                                                // Populate the dropdown dynamically--}}
                                        {{--                                                for (let i = startValue; i <= 900; i += 9) {--}}
                                        {{--                                                    const option = document.createElement('option');--}}
                                        {{--                                                    option.value = i;--}}
                                        {{--                                                    option.textContent = `${i} sqm`;--}}

                                        {{--                                                    // Preserve previous selection if valid--}}
                                        {{--                                                    if (i === previousValue) {--}}
                                        {{--                                                        option.selected = true;--}}
                                        {{--                                                    }--}}

                                        {{--                                                    sqmSelect.appendChild(option);--}}
                                        {{--                                                }--}}

                                        {{--                                                // Ensure the value is at least the minimum required--}}
                                        {{--                                                if (previousValue < startValue) {--}}
                                        {{--                                                    sqmSelect.value = startValue;--}}
                                        {{--                                                }--}}
                                        {{--                                            }--}}

                                        {{--                                            // Attach event listeners to stall type radio buttons--}}
                                        {{--                                            document.querySelectorAll('input[name="stall_category"]').forEach(radio => {--}}
                                        {{--                                                radio.addEventListener('change', updateSQMOptions);--}}
                                        {{--                                            });--}}

                                        {{--                                            // Run on page load--}}
                                        {{--                                            updateSQMOptions();--}}
                                        {{--                                        });--}}
                                        {{--                                    </script>--}}



                                        <div class="col-12 col-sm-6 d-flex align-items-start mt-3">
                                            <label for="stall_category" class=" red-label form-label me-3 mt-1 textB">Preferred Location
                                                <span class="red-label">*</span></label>
                                            <div class="d-flex flex-wrap gap-3">
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="pref_location"
                                                        value="Premium"
                                                        id="pref_location_premium"
                                                        {{ $isDisabled }}
                                                        {{ old('pref_location', $application->pref_location) == 'Premium' ? 'checked' : '' }}
                                                        required
                                                        style="color:#000">
                                                    <label for="pref_location_premium" class="form-check-label ms-2">Premium</label>
                                                </div>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="pref_location"
                                                        value="Standard"
                                                        id="pref_location_standard"
                                                        {{ $isDisabled }}
                                                        {{ old('pref_location', $application->pref_location) == 'Standard' ? 'checked' : '' }}
                                                        required
                                                        style="color:#000">
                                                    <label for="pref_location_standard" class="form-check-label ms-2">Standard</label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="custom-hr">

                                        <div class="row mt-3">
                                            <div class="col-12 col-sm-12 mt-3">
                                                <p style="text-align: justify; color: black;"><strong>We shall be
                                                        presenting exhibits and/or services, which belong to the
                                                        following product group(s) in the SEMICON India product index.
                                                        This information is necessary for correct space
                                                        allocations.</strong> (But request you to limit your selection
                                                    only to product groups that your products represent. This will help
                                                    the search engine refine the results more accurately when buyers
                                                    search for products.)</p>


                                                <label class="red-label" for="product_groups">Product Groups <span
                                                        class="red-label">*</span></label>
                                                <div class="form-check is-filled" >
                                                    <div class="row">
                                                        @foreach($productGroups as $group)
                                                            <div class="col-12 col-md-6 col-sm-6 d-flex " style=" margin-left:-20px">
                                                                <div class="form-check">
                                                                    <input
                                                                        class="form-check-input"
                                                                        type="checkbox"
                                                                        name="product_groups[]"
                                                                        value="{{ $group }}"
                                                                        id="group_{{ $loop->index }}"
                                                                        {{ $isDisabled }}
                                                                        {{ in_array($group, old('product_groups', json_decode($application->product_groups, true) ?? [])) ? 'checked' : '' }}
                                                                        "
                                                                    >
                                                                    <label class="form-check-label" for="group_{{ $loop->index }}" style="word-wrap: break-word; min-width: 200px;" >
                                                                        {{ $group }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="product_groups_validation" id="product_groups_validation" required>
                                </div>
                                <script>
                                    function validateProductGroups() {
                                        const checkboxes = document.querySelectorAll('input[name="product_groups[]"]');
                                        const validationInput = document.getElementById('product_groups_validation');
                                        let isChecked = false;
                                        checkboxes.forEach((checkbox) => {
                                            if (checkbox.checked) {
                                                isChecked = true;
                                            }
                                        });
                                        checkboxes.forEach((checkbox) => {
                                            checkbox.required = !isChecked;
                                        });
                                        validationInput.required = !isChecked;
                                    }

                                    document.addEventListener('DOMContentLoaded', validateProductGroups);
                                    document.querySelectorAll('input[name="product_groups[]"]').forEach((checkbox) => {
                                        checkbox.addEventListener('change', validateProductGroups);
                                    });
                                </script>


                                    <div class="row mt-3">
                                        <div class="col-12 col-sm-12 mt-3" style="text-align: justify; color: black; margin-left:8px">
                                            <label class="red-label" for="sectors"
                                                   style="text-align: justify; color: black;margin-left:-5px">Sectors <span
                                                    class="red-label">*</span></label>
                                            <div class="form-check is-filled">
                                                <div class="row" style="text-align: justify; color: black;">
                                                    @foreach($sectors as $sector)
                                                        <div class="col-12 col-sm-6"
                                                             style="text-align: justify; color: #000;">
                                                            <input
                                                                class="form-check-input is-filled"
                                                                type="checkbox"
                                                                name="sectors[]"
                                                                value="{{ $sector->id }}"
                                                                id="sector_{{ $loop->index }}"
                                                                {{ $isDisabled }}
                                                                {{ in_array($sector->id, old('sectors', $selectedSectors)) ? 'checked' : '' }}
                                                                onchange="validateSectors()"
                                                                style="text-align: justify; color: #000;"
                                                            >
                                                            <label
                                                                for="sector_{{ $loop->index }}">{{ $sector->name }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <input type="hidden" name="sectors_validation" id="sectors_validation"
                                                   required>
                                        </div>
                                    </div>
                                    <script>
                                        function validateSectors() {
                                            const checkboxes = document.querySelectorAll('input[name="sectors[]"]');
                                            const validationInput = document.getElementById('sectors_validation');
                                            let isChecked = false;
                                            checkboxes.forEach((checkbox) => {
                                                if (checkbox.checked) {
                                                    isChecked = true;
                                                }
                                            });
                                            checkboxes.forEach((checkbox) => {
                                                checkbox.required = !isChecked;
                                            });
                                            validationInput.required = !isChecked;
                                        }

                                        document.addEventListener('DOMContentLoaded', validateSectors);
                                    </script>






                                <div class="row mt-3">
                                    <div class="col-12 col-sm-12 mt-3">
                                        <input
                                            type="hidden"
                                            name="terms_accepted"
                                            id="terms_accepted"
                                            value="1"
                                            {{ $isDisabled }}
                                            {{ old('terms_accepted', $application->terms_accepted) ? 'checked' : '' }}
                                        >
                                    </div>
                                </div>

{{--                                    <div class="row mt-3">--}}
{{--                                        <div class="col-12 col-sm-12 mt-3">--}}
{{--                                            <div class="form-check">--}}
{{--                                                <input--}}
{{--                                                    class="form-check-input"--}}
{{--                                                    type="checkbox"--}}
{{--                                                    name="terms_accepted"--}}
{{--                                                    id="terms_accepted"--}}
{{--                                                    {{ $isDisabled }}--}}
{{--                                                    {{ old('terms_accepted', $application->terms_accepted) ? 'checked' : '' }}--}}
{{--                                                    required--}}
{{--                                                    style="text-align: justify;color:#000">--}}
{{--                                                <label class="form-check-label text" for="terms_accepted"--}}
{{--                                                       style="text-align: justify;color:#000">--}}
{{--                                                    I accept the terms and conditions <span class="red-label">*</span>--}}
{{--                                                </label>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="button-row d-flex mt-4 justify-content-center">
                                        @if ($application->submission_status == 'in progress')
                                            <button class="btn btn-info mb-0 js-btn-next"
                                                    type="submit" {{ $isDisabled }}>Submit
                                            </button>
                                        @else
                                            <a href="{{ route('terms') }}"
                                               class="btn btn-info mb-0 js-btn-next">Next</a>
                                        @endif
                                    </div>
                                </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
