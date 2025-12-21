<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Startup Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))</title>

  <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />

  <!-- Fonts and Icons -->
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />

  <!-- Stylesheets -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.0.0/mdb.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css" />
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Custom CSS -->
  <style>
    :root {
      --primary-color: #1B3783; /* Dark Blue - Bengaluru Tech Summit */
      --accent-color: #FFC03D; /* Bright Yellow - Bengaluru Tech Summit */
      --secondary-color: #6c757d;
      --success-color: #28a745;
      --danger-color: #dc3545;
      --warning-color: #ffc107;
      --info-color: #17a2b8;
      --light-bg: #FFFFFF;
      --dark-text: #464646;
      --header-bg: #1B3783;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background-color: var(--light-bg);
      color: var(--dark-text);
    }

    .startup-zone-header {
      background: var(--header-bg);
      color: white;
      padding: 20px 0;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .startup-zone-header .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .startup-zone-header .logo-section {
      display: flex;
      align-items: center;
      gap: 20px;
      flex: 1;
    }

    .startup-zone-header .event-logo {
      max-height: 70px;
      height: auto;
      width: auto;
    }

    .startup-zone-header .association-logo-container {
      display: flex;
      align-items: center;
      justify-content: flex-end;
    }

    .startup-zone-header .association-logo {
      max-height: 45px;
      height: auto;
      width: auto;
      background: white;
      padding: 8px 12px;
      border-radius: 5px;
      margin-left: 15px;
    }

    .startup-zone-header h1 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 500;
      color: white;
    }

    .startup-zone-header .event-name {
      font-size: 0.9rem;
      opacity: 0.9;
      color: rgba(255, 255, 255, 0.9);
    }

    .startup-zone-footer {
      background-color: var(--primary-color);
      color: white;
      padding: 40px 0 20px;
      margin-top: 50px;
    }

    .startup-zone-footer a {
      color: #adb5bd;
      text-decoration: none;
    }

    .startup-zone-footer a:hover {
      color: white;
    }

    .startup-zone-footer .footer-section h5 {
      color: white;
      margin-bottom: 15px;
    }

    .startup-zone-footer .footer-section ul {
      list-style: none;
      padding: 0;
    }

    .startup-zone-footer .footer-section ul li {
      margin-bottom: 8px;
    }

    /* Form Styles */
    .form-control:focus, .form-select:focus {
      border-color: var(--primary-color);
      box-shadow: 0 0 0 0.2rem rgba(27, 55, 131, 0.25);
    }

    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
    }

    .btn-primary:hover {
      background-color: #152a6b;
      border-color: #152a6b;
    }

    .btn-accent {
      background-color: var(--accent-color);
      border-color: var(--accent-color);
      color: var(--dark-text);
    }

    .btn-accent:hover {
      background-color: #e6a835;
      border-color: #e6a835;
      color: var(--dark-text);
    }

    .progress-bar {
      background-color: var(--primary-color);
    }

    .nav-pills .nav-link.active {
      background-color: var(--primary-color);
    }

    .card {
      border: none;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      border-radius: 10px;
    }

    .card-header {
      border-radius: 10px 10px 0 0 !important;
    }

    /* Auto-save Indicator */
    #autoSaveIndicator {
      min-width: 150px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-radius: 8px;
    }

    /* Step Content Animation */
    .step-content {
      animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
      from { 
        opacity: 0; 
        transform: translateY(10px); 
      }
      to { 
        opacity: 1; 
        transform: translateY(0); 
      }
    }

    /* Validation Styles */
    .form-control.is-invalid, .form-select.is-invalid {
      border-color: var(--danger-color);
      padding-right: calc(1.5em + 0.75rem);
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right calc(0.375em + 0.1875rem) center;
      background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .form-control.is-valid, .form-select.is-valid {
      border-color: var(--success-color);
      padding-right: calc(1.5em + 0.75rem);
      background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='m2.3 6.73.98-.98-.98-.98-.98.98.98.98zm3.98-3.98L6.27 4.3l-.98-.98-.98.98.98.98z'/%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right calc(0.375em + 0.1875rem) center;
      background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    /* Responsive */
    @media (max-width: 768px) {
      .startup-zone-header h1 {
        font-size: 1.2rem;
      }
      
      .startup-zone-header .event-name {
        font-size: 0.8rem;
      }

      .startup-zone-header .event-logo {
        max-height: 50px;
      }

      .startup-zone-header .association-logo {
        max-height: 35px;
        padding: 5px 8px;
      }

      .startup-zone-header .logo-section {
        gap: 10px;
      }
    }

    /* Custom utility classes */
    .text-primary-custom {
      color: var(--primary-color) !important;
    }

    .bg-primary-custom {
      background-color: var(--primary-color) !important;
    }

    .border-primary-custom {
      border-color: var(--primary-color) !important;
    }
  </style>

  @stack('styles')
</head>
<body>
  {{-- Header --}}
  <header class="startup-zone-header">
    <div class="container">
      <div class="logo-section">
        <img src="https://bengalurutechsummit.com/img/logo-BTS-26-N.png" alt="{{ config('constants.EVENT_NAME') }} Logo" class="event-logo">
        <div>
          <h1>Startup Registration</h1>
          <div class="event-name">{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</div>
        </div>
      </div>
      <div class="association-logo-container">
        @if(isset($associationLogo) && $associationLogo)
          <img src="{{ $associationLogo }}" alt="Association Logo" class="association-logo">
        @endif
        <!-- <div class="d-none d-md-block ms-3">
          <a href="{{ url('/') }}" class="btn btn-light btn-sm">
            <i class="fas fa-home"></i> Home
          </a>
        </div> -->
      </div>
    </div>
  </header>

  {{-- Main Content --}}
  <main class="py-4">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="startup-zone-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-4 footer-section mb-4 mb-md-0">
          <h5>Contact Us</h5>
          <ul>
            <li><i class="fas fa-envelope"></i> {{ config('constants.ORGANIZER_EMAIL') }}</li>
            <li><i class="fas fa-phone"></i> {{ config('constants.ORGANIZER_PHONE') }}</li>
            <li><i class="fas fa-globe"></i> <a href="{{ config('constants.ORGANIZER_WEBSITE') }}" target="_blank">{{ config('constants.ORGANIZER_WEBSITE') }}</a></li>
          </ul>
        </div>
        <div class="col-md-4 footer-section mb-4 mb-md-0">
          <h5>Quick Links</h5>
          <ul>
            <li><a href="{{ url('/') }}">Home</a></li>
            <li><a href="{{ route('startup-zone.register') }}">Register</a></li>
            <li><a href="{{ config('constants.ORGANIZER_WEBSITE') }}" target="_blank">Event Website</a></li>
          </ul>
        </div>
        <div class="col-md-4 footer-section">
          <h5>Event Information</h5>
          <ul>
            <li><strong>Event:</strong> {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</li>
            <li><strong>Organizer:</strong> {{ config('constants.ORGANIZER_NAME') }}</li>
          </ul>
        </div>
      </div>
      <hr class="bg-light my-4">
      <div class="row">
        <div class="col-12 text-center">
          <p class="mb-0">&copy; {{ date('Y') }} {{ config('constants.ORGANIZER_NAME') }}. All rights reserved.</p>
        </div>
      </div>
    </div>
  </footer>

  {{-- Core JS Libraries --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>

  {{-- Custom Scripts --}}
  @stack('scripts')
</body>
</html>
