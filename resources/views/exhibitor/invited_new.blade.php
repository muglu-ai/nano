@props(['index', 'data', 'productCategories' => [], 'sectors' => [], 'captchaSvg' => '', 'countries' => []])

@php
$sectors = $natureOfBusiness ?? [];
$titles = ['mr' => 'Mr.', 'mrs' => 'Mrs.', 'ms' => 'Ms.', 'dr' => 'Dr.', 'prof' => 'Prof.'];
$jobCategories = ['Industry', 'Government', 'Media', 'Academic', 'Others'];
$purposes = [
'Purchase new products and services',
'Source new vendors for an ongoing project',
'Join the buyer-seller program & meet potential suppliers',
'To connect & engage with existing suppliers',
'Stay up to date with the latest innovations',
'Compare and Benchmark technologies / solutions',
];

$idTypes = ['Aadhaar Card', 'PAN Card', 'Driving License', 'Passport', 'Voter ID'];
//var_dump($countries);
//die;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
      <link rel="icon" href="https://www.semiconindia.org/themes/custom/semicon/favicon.ico" type="image/vnd.microsoft.icon" />
    <title>SEMICON India 2025 - Registration </title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .custom-dropdown-container {
            position: relative;
            width: 100%;
        }

        .custom-dropdown {
            position: relative;
            width: 100%;
        }

        .dropdown-selected {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            background-color: #fff;
            cursor: pointer;
            min-height: 38px;
        }

        .dropdown-selected:hover {
            border-color: #86b7fe;
        }

        .dropdown-selected.active {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .selected-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            /* flex: 1; */
        }

        .selected-tag {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            background-color: #e9ecef;
            border-radius: 12px;
            font-size: 0.875rem;
            gap: 4px;
        }

        .selected-tag .remove {
            cursor: pointer;
            font-weight: bold;
            color: #6c757d;
        }

        .selected-tag .remove:hover {
            color: #dc3545;
        }

        .selected-text {
            
            color: #6c757d;
            flex: 1;
        }

        .dropdown-arrow {
            margin-left: auto;
            margin-right: 0;
            float: right;
            transition: transform 0.2s;
        }

        .dropdown-selected.active .dropdown-arrow {
            transform: rotate(180deg);
        }

        .dropdown-options {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .dropdown-options.show {
            display: block;
        }

        .dropdown-option {
            display: flex;
            align-items: center;
            padding: 8px 12px;
            cursor: pointer;
            margin: 0;
            gap: 8px;
        }

        .dropdown-option:hover {
            background-color: #f8f9fa;
        }

        .dropdown-option input[type="checkbox"] {
            margin: 0;
        }

        .dropdown-option span {
            flex: 1;
        }

        .important {
            color: #dc3545;
        }
    </style>

</head>
<body>

<div class="container text-left mt-3">
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center align-items-center mb-4">
            <div class="row w-100">
                 
                <div class="col-md-3 d-flex justify-content-center align-items-center">
                    <img class="logo2"
                        src="{{ asset('asset/img/logos/meity-logo.png') }}"
                        alt="Ministry of Electronics & IT Logo" style="max-height: 80px;">
                </div>
                <div class="col-md-3 d-flex justify-content-center align-items-center">
                    <img class="logo3"
                        src="{{ asset('asset/img/logos/ism_logo.png') }}"
                        alt="ISM Logo" style="max-height: 80px;">
                </div>
                <div class="col-md-3 d-flex justify-content-center align-items-center">
                    <img class="logo4"
                        src="{{ asset('asset/img/logos/DIC_Logo.webp') }}"
                        alt="DIC Logo" style="max-height: 80px;">
                </div>
                <div class="col-md-3 d-flex justify-content-center align-items-center">
                    <img class="logo1"
                        src="{{ asset('asset/img/logos/SEMI_IESA_logo.png') }}"
                        alt="SEMI IESA Logo" style="max-height: 80px;">
                </div>
            </div>
    </div>
</div>

<div class="container d-flex justify-content-center">
    @if(isset($notFound) && $notFound == true)
        <div class="col-md-6 d-flex flex-column justify-content-center mt-3">
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Error!</h4>
            {{--  <p>Invalid or expired invitation link.</p> --}}
            <p> Inaugural registration for SEMICON India 2025 is now closed as all seats are filled. </p>
        </div>
        </div>
    @elseif(isset($token) && !empty($token) && $token == 'success')
        <div class="col-md-6 d-flex flex-column justify-content-center mt-3">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Success!</h4>
            <!-- <p>Thank you for filling out the form.</p> -->
              <p> Inaugural registration for SEMICON India 2025 is now closed as all seats are filled. </p>
        </div>
        </div>
    @else

    <div class="col-md-8 d-flex flex-column justify-content-center mt-3">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            {{-- <h3>You are Invited as a Delegate under {{$companyName}}</h3> --}}
            <!-- <h3>  Inaugural registration is now closed </h3> -->
            <h3> Inaugural Registration - Sept 2 is now closed. </h3>


            @php 
            $hide = true;
            @endphp
            @if($hide == false)
            <h3> Exhibitor Registration Form for Inaugural</h3>
            <p>Please fill in the below details.</p>

            <form class="mt-3" id="addForm" action="{{ route('inaugural.invitee.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="card mb-4">
                    <div class="card-body">
                         <div class="row g-3">
                             <div class="col-md-3">
                                <label>Prefix <span class="text-danger">*</span></label>
                                <select name="title" class="form-control" required>
                                    <option value="" disabled {{ empty($data['title']) ? 'selected' : '' }}>--- Select ---
                                    </option>
                                    @foreach ($titles as $val => $label)
                                    <option value="{{ $val }}" {{ ($data['title'] ?? '') === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                             </div>
                             <div class="col-md-3">                                <label>First Name <span class="text-danger">*</span></label>
                                  <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                      required value="{{ old('first_name', $data['first_name'] ?? '') }}">
                                  @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                  @enderror
                             </div>
                             <div class="col-md-3">
                                <label>Middle Name</label>
                                <input type="text" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror"
                                    value="{{ old('middle_name', $data['middle_name'] ?? '') }}">
                                @error('middle_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label>Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                    required value="{{ old('last_name', $data['last_name'] ?? '') }}">
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>                            <div class="col-md-4">
                                <label>Designation <span class="text-danger">*</span></label>
                                <input type="text" name="designation" class="form-control @error('designation') is-invalid @enderror"
                                    required value="{{ old('designation', $data['designation'] ?? '') }}">
                                @error('designation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label>Organization <span class="text-danger">*</span></label>
                                <input type="text" name="company" class="form-control @error('company') is-invalid @enderror" required
                                    value="{{ old('company', $data['company'] ?? ($companyName ?? '')) }}">
                                @error('company')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label>Address <span class="text-danger">*</span></label>
                                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror" required
                                    value="{{ old('address', $data['address'] ?? '') }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label>Country <span class="text-danger">*</span></label>
                                <select class="form-select country-dropdown" name="country"
                                    data-index="0" required>
                                    <option value="">--- Select ---</option>
                                    @foreach ($countries as $country)
                                    @if (!in_array($country->id, [251, 354, 416, 457, 460]))
                                    <option value="{{ $country->id }}"
                                        {{ isset($data['country']) && $data['country'] == $country->id ? 'selected' : '' }}>
                                        {{ $country->name }}
                                    </option>
                                    @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>State <span class="text-danger">*</span></label>
                                <select class="form-select state-dropdown" name="state"
                                     data-index="0" required>
                                    <option value="">{{ $data['state'] ?? '--- Select ---' }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>City <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="city" required
                                    value="{{ $data['city'] ?? '' }}">
                            </div>                            <div class="col-md-4">
                                <label>Postal/Pin Code <span class="text-danger">*</span></label>
                                <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror"
                                    pattern="^[0-9]{4,6}$"
                                    minlength="4"
                                    maxlength="6"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                    required value="{{ old('postal_code', $data['postal_code'] ?? '') }}">
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                
                            </div>
                            <div class="col-md-4">
                                <label>Mobile Number <span class="text-danger">*</span></label>
                                <input type="tel" id="phone" name="mobile" placeholder=""
                                    class="form-control phone-input @error('mobile') is-invalid @enderror" 
                                    required value="{{ old('mobile', $data['mobile'] ?? '') }}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                                <small class="form-text text-muted">Preferably WhatsApp number to receive badge</small>
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var phoneInput = document.getElementById('phone');
                                    var placeholderTimeout;

                                    function removePlaceholderAfterDelay() {
                                        clearTimeout(placeholderTimeout);
                                        placeholderTimeout = setTimeout(function() {
                                            if (phoneInput) {
                                                phoneInput.placeholder = '';
                                            }
                                        }, 500); // 5 seconds
                                    }

                                    if (phoneInput) {
                                        // Initial 5s removal
                                        removePlaceholderAfterDelay();

                                        // On focus, start timer
                                        phoneInput.addEventListener('focus', function() {
                                            removePlaceholderAfterDelay();
                                        });

                                        // On input, if field is empty, start timer again
                                        phoneInput.addEventListener('input', function() {
                                            if (phoneInput.value === '') {
                                                removePlaceholderAfterDelay();
                                            } else {
                                                clearTimeout(placeholderTimeout);
                                            }
                                        });

                                        // On blur, if field is empty, start timer again
                                        phoneInput.addEventListener('blur', function() {
                                            if (phoneInput.value === '') {
                                                removePlaceholderAfterDelay();
                                            }
                                        });
                                    }
                                });
                            </script>
                            <div class="col-md-4">
                                <label>Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required 
                                    value="{{ $inviteeEmail ?? '' }}" readonly>
                            </div>
                            <div class="col-md-12 mt-3">
                                <label>Nature of your Business: <span class="important">*</span></label>
                                <div class="custom-dropdown-container">
                                    <div class="custom-dropdown" >
                                        <div class="dropdown-selected" onclick="toggleCustomDropdown(this)">
                                            <div class="selected-tags" ></div>
                                            <span class="selected-text" data-placeholder="Select business nature...">Select business nature...</span>
                                            <i class="dropdown-arrow">▼</i>
                                        </div>
                                        <div class="dropdown-options" >
                                            @foreach ($sectors as $i => $sector)
                                            @if (!empty($sector['name']))
                                            <label class="dropdown-option">
                                                <input type="checkbox" name="business_nature[]"
                                                    value="{{ $sector['name'] }}"
                                                    data-label="{{ $sector['name'] }}"
                                                    {{ !empty($data['business_nature']) && in_array($sector['name'], (array) $data['business_nature']) ? 'checked' : '' }}
                                                    onchange="updateCustomDropdownText(this)">
                                                <span>{{ $sector['name'] }}</span>
                                            </label>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Purpose of visit --}}
                            <!-- <div class="col-md-12">
                                <label>The purpose of your visits: </label>
                                <div class="custom-dropdown-container">
                                    <div class="custom-dropdown" >
                                        <div class="dropdown-selected" onclick="toggleCustomDropdown(this)">
                                            <div class="selected-tags" ></div>
                                            <span class="selected-text" data-placeholder="Select purpose of visit...">Select purpose of visit...</span>
                                            <i class="dropdown-arrow">▼</i>
                                        </div>
                                        <div class="dropdown-options">
                                            @foreach ($purposes as $i => $label)
                                            <label class="dropdown-option">
                                                <input type="checkbox" name="purpose[]"
                                                    value="{{ $label }}"
                                                    data-label="{{ $label }}"
                                                    {{ in_array($label, $data['purpose'] ?? []) ? 'checked' : '' }}
                                                    onchange="updateCustomDropdownText(this)">
                                                <span>{{ $label }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            {{-- Product Categories of Interest --}}
                            <div class="col-md-12 mt-3">
                                <label>Product Categories of your interest: <span class="important">*</span></label>
                                <div class="custom-dropdown-container">
                                    <div class="custom-dropdown" >
                                        <div class="dropdown-selected" onclick="toggleCustomDropdown(this)">
                                            <div class="selected-tags" ></div>
                                            <span class="selected-text" data-placeholder="Select product categories...">Select product categories...</span>
                                            <i class="dropdown-arrow">▼</i>
                                        </div>
                                        <div class="dropdown-options" >
                                            @foreach ($productCategories as $opt)
                                            <label class="dropdown-option">
                                                <input type="checkbox" name="products[]"
                                                    value="{{ $opt }}"
                                                    data-label="{{ $opt }}"
                                                    {{ !empty($data['products']) && in_array($opt, $data['products']) ? 'checked' : '' }}
                                                    onchange="updateCustomDropdownText(this)">
                                                <span>{{ $opt }}</span>
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>                            {{-- ID Card Type and Number (conditional) --}}
                           
                            <div class="col-md-6 id-fields" >
                                <label>ID Card Type <span class="text-danger">*</span></label>
                                <select name="id_card_type"
                                    class="form-select id-card-type-select" required data-index="0">
                                    <option value="">Select ID Card Type</option>
                                    @foreach ($idTypes as $type)
                                    <option value="{{ $type }}"
                                        {{ ($data['id_card_type'] ?? '') === $type ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                           
                            
                           
                            <div class="col-md-6 id-fields" >
                                <label id="id-card-number-label-0">
                                    ID Card Number <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="id_card_number" required
                                    class="form-control id-card-number-input @error('id_card_number') is-invalid @enderror"
                                    value="{{ old('id_card_number', $data['id_card_number'] ?? '') }}">
                                @error('id_card_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                          
                            
                          
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    function updateIdCardNumberLabel(idx) {
                                        const select = document.querySelector('.id-card-type-select[data-index="' + idx + '"]');
                                        const label = document.getElementById('id-card-number-label-' + idx);
                                        if (select && label) {
                                            if (select.value === 'Aadhaar Card') {
                                                label.innerHTML = 'ID Card Number (Last 4 digits) <span class="text-danger">*</span>';
                                            } else {
                                                label.innerHTML = 'ID Card Number <span class="text-danger">*</span>';
                                            }
                                        }
                                    }
                                    document.querySelectorAll('.id-card-type-select').forEach(function(select) {
                                        const idx = select.getAttribute('data-index');
                                        select.addEventListener('change', function() {
                                            updateIdCardNumberLabel(idx);
                                        });
                                        // Initial state
                                        updateIdCardNumberLabel(idx);
                                    });
                                });
                            </script>
                          
                          
                            <div class="col-md-12 id-fields">
                                <label>Upload Profile Picture <span class="important">*</span></label>
                                <input type="file" name="profile_picture"
                                    class="form-control profile-upload" required accept="image/*"
                                    onchange="validateProfilePicture(this)">
                                <small class="form-text text-muted">Max size: 1MB. Allowed formats: jpg, jpeg, png.</small>
                            </div>
                            <script>
                                function validateProfilePicture(input) {
                                    if (input.files && input.files[0]) {
                                        if (input.files[0].size > 1024 * 1024) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'File Too Large',
                                                text: 'File size must be less than or equal to 1MB.',
                                            });
                                            input.value = '';
                                        }
                                    }
                                }
                            </script>

                           
                          
                                <div class="mt-4 d-flex justify-content-center">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                         </div>
                    </div>
                </div>
               
            </form>

            <div class="alert alert-info mt-3" role="alert">
                <strong>Note:</strong> Kindly note that participation (in-person) in the Inaugural event is subject to final confirmation and will be informed separately from 3rd week of August onwards.
            </div>
             @endif
    </div>
        
    @endif
</div>

<!-- intl-tel-input JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<!-- Utils Script for intl-tel-input -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>

<script>
    $(document).ready(function () {
        var input = document.querySelector("#phone");

        var iti = window.intlTelInput(input, {
            initialCountry: "IN",
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            preferredCountries: ["in"],
            excludeCountries: ["af", "ir", "pk", "sd"]
        });

        function updatePhoneNumber() {
            var fullNumber = iti.getNumber();
            if (iti.isValidNumber()) {
                $("#fullPhoneNumber").val(fullNumber);
            } else {
                $("#fullPhoneNumber").val(""); // Reset if invalid
            }
        }

        $("#phone").on("change keyup", updatePhoneNumber);
        input.addEventListener("countrychange", updatePhoneNumber);

        // $("#addForm").on("submit", function (event) {
        //     event.preventDefault();
        //     updatePhoneNumber();

        //     var fullPhoneNumber = $("#fullPhoneNumber").val();
        //     if (!fullPhoneNumber) {
        //         Swal.fire('Error', 'Please enter a valid phone number.', 'error');
        //         return;
        //     }

        //     var csrfToken = $("input[name=_token]").val();
        //     if (!csrfToken) {
        //         Swal.fire('Error', 'CSRF token missing!', 'error');
        //         return;
        //     }

        //     var formData = {
        //         _token: csrfToken,
        //         name: $("#name").val(),
        //         email: $("#email").val(),
        //         phone: fullPhoneNumber,
        //         jobTitle: $("#jobTitle").val()
        //     };

        //     fetch("{{ route('exhibition.invitee.submit') }}", {
        //         method: 'POST',
        //         headers: {
        //             'Content-Type': 'application/json',
        //             'X-CSRF-TOKEN': csrfToken
        //         },
        //         body: JSON.stringify(formData)
        //     })
        //         .then(response => response.json())
        //         .then(data => {
        //             if (data.error) {
        //                 Swal.fire('Error', JSON.stringify(data.error), 'error');
        //             } else {
        //                 Swal.fire('Success', data.message, 'success');
        //             }
        //         })
        //         .catch(error => {
        //             console.error('Error:', error);
        //             Swal.fire('Error', 'Something went wrong! ' + error.message, 'error');
        //         });
        // });
    });
</script>



<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Custom Dropdown Functions
    function toggleCustomDropdown(element) {
        const dropdown = element.closest('.custom-dropdown');
        const options = dropdown.querySelector('.dropdown-options');
        const selected = dropdown.querySelector('.dropdown-selected');
        
        // Close other dropdowns
        document.querySelectorAll('.custom-dropdown').forEach(function(otherDropdown) {
            if (otherDropdown !== dropdown) {
                otherDropdown.querySelector('.dropdown-options').classList.remove('show');
                otherDropdown.querySelector('.dropdown-selected').classList.remove('active');
            }
        });
        
        // Toggle current dropdown
        options.classList.toggle('show');
        selected.classList.toggle('active');
    }

    function updateCustomDropdownText(checkbox) {
        const dropdown = checkbox.closest('.custom-dropdown');
        const selectedTags = dropdown.querySelector('.selected-tags');
        const selectedText = dropdown.querySelector('.selected-text');
        const checkboxes = dropdown.querySelectorAll('input[type="checkbox"]');
        
        // Clear existing tags
        selectedTags.innerHTML = '';
        
        // Get all checked items
        const checkedItems = Array.from(checkboxes).filter(cb => cb.checked);
        
        if (checkedItems.length > 0) {
            // Hide placeholder text
            selectedText.style.display = 'none';
            
            // Create tags for selected items
            checkedItems.forEach(function(cb) {
                const tag = document.createElement('span');
                tag.className = 'selected-tag';
                tag.innerHTML = `
                    <span>${cb.getAttribute('data-label')}</span>
                    <span class="remove" onclick="removeTag(this, '${cb.value}')">&times;</span>
                `;
                selectedTags.appendChild(tag);
            });
        } else {
            // Show placeholder text
            selectedText.style.display = 'block';
            selectedText.textContent = selectedText.getAttribute('data-placeholder');
        }
    }

    function removeTag(element, value) {
        const dropdown = element.closest('.custom-dropdown');
        const checkbox = dropdown.querySelector(`input[value="${value}"]`);
        
        if (checkbox) {
            checkbox.checked = false;
            updateCustomDropdownText(checkbox);
        }
    }

    

    // Initialize dropdowns on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize existing selections
        document.querySelectorAll('.custom-dropdown input[type="checkbox"]:checked').forEach(function(checkbox) {
            updateCustomDropdownText(checkbox);
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.custom-dropdown')) {
                document.querySelectorAll('.dropdown-options').forEach(function(options) {
                    options.classList.remove('show');
                });
                document.querySelectorAll('.dropdown-selected').forEach(function(selected) {
                    selected.classList.remove('active');
                });
            }
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
            console.log('Country ID:', countryId, 'Index:', idx, 'State dropdown found:', $stateDropdown.length);
            $stateDropdown.html('<option value="">Loading...</option>');
            if (countryId) {
                var token = $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val();
                console.log('CSRF Token:', token ? 'Found' : 'Not found');
                $.ajax({
                    url: "https://portal.semiconindia.org/get-states",
                    type: "POST",
                    data: {
                        country_id: countryId,
                        _token: token
                    },
                    success: function(states) {
                        console.log('States received:', states);
                        var options = '<option value="">--- Select ---</option>';
                        $.each(states, function(i, state) {
                            options += '<option value="' + state.id + '">' + state.name + '</option>';
                        });
                        $stateDropdown.html(options);
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error, xhr.responseText);
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
            var selectedState = "";
            if (countryId) {
                var $stateDropdown = $('.state-dropdown[data-index="' + idx + '"]');
                $.ajax({
                    url: "https://portal.semiconindia.org/get-states",
                    type: "POST",
                    data: {
                        country_id: countryId,
                        _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val()
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


</body>
</html>
