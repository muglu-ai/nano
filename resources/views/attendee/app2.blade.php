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

  @yield('content')

  <!-- Core JS Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>

  <!-- Tom Select JS -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>


  <!-- Project Specific Scripts -->
  <script src="/assets/js/intl-tel.js"></script>

  <script src="/assets/js/visitor-forms.js"></script>

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
  </script>

<script src="https://cdn.jsdelivr.net/npm/@coreui/coreui@5.4.0/dist/js/coreui.bundle.min.js"></script>

    {{-- <script src="/assets/js/geo.js"></script> --}}

</body>
</html>
