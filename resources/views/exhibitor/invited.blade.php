<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            <p>Invalid or expired invitation link.</p>
        </div>
        </div>
    @elseif(isset($token) && !empty($token) && $token == 'success')
        <div class="col-md-6 d-flex flex-column justify-content-center mt-3">
        <div class="alert alert-success" role="alert">
            <h4 class="alert-heading">Success!</h4>
            <p>Thank you for filling out the form.</p>
        </div>
        </div>
    @else

    <div class="col-md-4 d-flex flex-column justify-content-center mt-3">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
            <h3>You are Invited as an Exhibitor under {{$companyName}}</h3>
            <p>Please fill the below details.</p>

            <form class="mt-3" id="addForm" action="{{ route('exhibition.invitee.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                {{-- <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div> --}}

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="tel" class="form-control" id="phone" required>
                    <input type="hidden" id="fullPhoneNumber" name="fullPhoneNumber">
                </div>

                <div class="mb-3">
                    <label for="jobTitle" class="form-label">Job Title</label>
                    <input type="text" class="form-control" id="jobTitle" name="jobTitle" required>
                </div>
                <div class="mb-3">
                    <label for="organisationName" class="form-label ">Organisation Name</label>
                    <input type="text" class="form-control" id="organisationName" name="organisationName" value="{{ $companyName }}">
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
</div>
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
            initialCountry: "auto",
            geoIpLookup: function (callback) {
                $.get("https://ipinfo.io/json?token=15e3c2489c20af", function (resp) {
                    var countryCode = (resp && resp.country) ? resp.country : "US";
                    callback(countryCode);
                }, "jsonp").fail(function () {
                    callback("IN"); // Fallback to "US" if the request fails
                });
            },
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"
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

        $("#addForms").on("submit", function (event) {
            event.preventDefault();
            updatePhoneNumber();

            var fullPhoneNumber = $("#fullPhoneNumber").val();
            if (!fullPhoneNumber) {
                Swal.fire('Error', 'Please enter a valid phone number.', 'error');
                return;
            }

            var csrfToken = $("input[name=_token]").val();
            if (!csrfToken) {
                Swal.fire('Error', 'CSRF token missing!', 'error');
                return;
            }

            var formData = {
                _token: csrfToken,
                name: $("#name").val(),
                email: $("#email").val(),
                phone: fullPhoneNumber,
                jobTitle: $("#jobTitle").val()
            };

            fetch("{{ route('exhibition.invitee.submit') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        Swal.fire('Error', JSON.stringify(data.error), 'error');
                    } else {
                        Swal.fire('Success', data.message, 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Something went wrong! ' + error.message, 'error');
                });
        });
    });
</script>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
