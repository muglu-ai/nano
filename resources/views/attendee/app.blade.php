<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Visitor Registration')</title>

  <link rel="icon" href="https://www.semiconindia.org/themes/custom/semicon/favicon.ico" type="image/vnd.microsoft.icon" />

  <!-- Fonts and Icons -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

  <!-- Stylesheets -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.0.0/mdb.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">

<link rel="stylesheet" href="/images/style.css" />

  <!-- Custom CSS -->
  <style>
    .important { color: red; }
    .multiple-checkboxes { padding: 10px; }
    .multiselect-container.dropdown-menu {
      padding-left: 10px !important;
      padding-right: 10px !important;
      min-width: 120px !important; 
    }
    .iti { width: 100%; }
    .iti--allow-dropdown .iti__flag-container { height: 100%; }
    
    input[type="tel"].phone-input.form-control {
      padding-left: 58px;
    }
    .intl-tel-input {
  width: 100%;
}

.intl-tel-input .iti__flag-container {
  height: 100%;
  border-right: 1px solid #ccc;
  background-color: #f8f9fa;
}

.phone-input {
  padding-left: 80px !important; /* adjusted for flag width */
  height: 45px !important;
  border-radius: 6px;
  border: 1px solid #ced4da;
}

.intl-tel-input input[type="tel"] {
  border-radius: 0 6px 6px 0;
  height: 45px;
  padding-left: 12px;
  font-size: 1rem;
  background-color: #fff;
}

  </style>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
 <header class="main-header">
        <div class="main-header-topBar">
            <div class="container">
                <div class="row">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex">
                            <div class="date-time" id="date"></div>
                            <div class="date-time" id="time"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap">
                <div class="ism-header__main_box text-center">
                    <a href="https://www.meity.gov.in/" target="_blank" aria-label="MIETY logo">
                        <img src="/images/logos/meity_header_logo_new.png" alt="MIETY logo" class=" logo-img">
                    </a>
                </div>
                <div class="ism-header__main_box text-center">
                    <a href="https://ism.gov.in/" target="_blank" aria-label="ISM logo">
                        <img src="/images/logos/ism_header_logo.png" alt="ISM logo" class=" logo-img">
                    </a>
                </div>
                <div class="ism-header__main_box text-center">
                    <a href="https://dic.gov.in/" target="_blank" aria-label="DIC logo">
                        <img src="/images/logos/digi_india_header_logo.png" alt="DIC logo" class=" logo-img">
                    </a>
                </div>
                <div class="ism-header__main_box text-center">
                    <a href="https://www.semiconindia.org/" target="_blank" aria-label="SEMI logo">
                        <img src="/images/logos/SEMI_IESA_logo.png" alt="SEMI logo" class=" logo-img">
                    </a>
                </div>
            </div>
        </div>
    </header>
  @yield('content')


  <!-- Footer -->
</div>
  <footer class="ism-footer pt-3 pb-5">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-lg-6">
                    <h5>Live Streaming Links</h5>
                    <ul>
                        <li class="d-flex">
                            <div class="me-3">
                                <img src="/images/icons/webcast.jpg" alt="Webcast Icon" class="footer-list-icon">
                            </div>
                            <div>
                                <h4>Webcast</h4>
                                <a href="https://webcast.gov.in/meity/semiconindia">
                                    https://webcast.gov.in/meity/semiconindia</a>
                            </div>
                        </li>
                        <li class="d-flex">
                            <div class="me-3">
                                <img src="/images/icons/youtube.png" alt="Youtube Icon" class="footer-list-icon">
                            </div>
                            <div>
                                <h4>Youtube</h4>
                                <a href="https://www.youtube.com/@IndiaSemiconductorMission/streams">
                                    https://www.youtube.com/@IndiaSemiconductorMission/streams</a>
                            </div>
                        </li>
                        <li>
                            <h5 class="custom-font mt-5">Scan the QR code for Registration</h5>
                            <div>
                                <img src="/images/reg_qr.png" alt="QR Code Image" class="scanner-image">
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-12 col-lg-6">
                    <h5>Location</h5>
                    <ul>
                        <li class="d-flex">
                            <div class="me-3">
                                <img src="/images/icons/location.png" alt="Location Icon" class="footer-list-icon">
                            </div>
                            <div>
                                <a href="https://maps.app.goo.gl/mBy3j6XUcLZdgezUA"> Yashobhoomi (IICC), Sector 25
                                    Dwarka, New Delhi, 110061</a>
                            </div>
                        </li>
                    </ul>
                    <div class="footer-iframe p-2">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3609.485005285528!2d77.04614029999999!3d28.553795899999994!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390d1a509578bb01%3A0xc4f9b6f167e3d164!2sYashoBhoomi%20Dwarka%20Sector-25!5e1!3m2!1sen!2sin!4v1753345616265!5m2!1sen!2sin"
                            frameborder="0" height="100%" width="100%" title="embeded maps">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </footer>


  <!-- Core JS Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

  <!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>


  <!-- Project Specific Scripts -->
  <script src="/assets/js/intl-tel.js"></script>

  <script src="/assets/js/visitor-form.js"></script>

  <!-- Optional Local JS -->
  <script src="/assets/js/jquery.min.js"></script>
  <script src="/assets/js/popper.js"></script>
  <script src="/assets/js/bootstrap.min.js"></script>
  <script src="/assets/js/bootstrap-multiselect.js"></script>
  <script src="/assets/js/main.js"></script>

<!-- Initialize Tom Select -->
<script>
  new TomSelect("#business_nature_0", {
    plugins: ['remove_button'],
    persist: false,
    create: false,
    maxItems: null,
    placeholder: 'Select one or more Nature of Buisness'
  });
  new TomSelect("#purpose_0_visit", {
    plugins: ['remove_button'],
    persist: false,
    create: false,
    maxItems: null,
    placeholder: 'Select one or more Purpose of Visit'
  });
  new TomSelect("#product_categories_0", {
    plugins: ['remove_button'],
    persist: false,
    create: false,
    maxItems: null,
    placeholder: 'Select one or more Product Categories'
  });
  new TomSelect("#event_days_0", {
    plugins: ['remove_button'],
    persist: false,
    create: false,
    maxItems: null,
    placeholder: 'Select one or more Event Days'
  });
</script>


  <script>
    function removePhonePlaceholder() {
      var phoneInputs = document.querySelectorAll('#phone');
      phoneInputs.forEach(function(input) {
        if (input.hasAttribute('placeholder')) {
          input.removeAttribute('placeholder');
        }
      });
    }

    window.addEventListener('DOMContentLoaded', function() {
      removePhonePlaceholder();
      setInterval(removePhonePlaceholder, 200); // Keep checking every 200ms
    });

    // JS for show dynamic date & time in Header topbar
        function UpdateDateTime() {
            const now = new Date();
            const options = {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            const formattedDate = now.toLocaleDateString('en-GB', options);
            const formattedTime = now.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            }).replace(/:/g, ' : ');
            document.getElementById('date').textContent = formattedDate + ' |';
            document.getElementById('time').innerHTML = '&nbsp;' + formattedTime;
            if (!this.intervalId) {
                this.intervalId = setInterval(this.UpdateDateTime.bind(this), 1000);
            }
        };
        UpdateDateTime();
  </script>

<script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.4.0/dist/js/coreui.bundle.min.js"></script>

    {{-- <script src="/assets/js/geo.js"></script> --}}

</body>
</html>
