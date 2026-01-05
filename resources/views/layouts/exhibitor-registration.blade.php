<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Exhibitor Registration - ' . config('constants.EVENT_NAME') . ' ' . config('constants.EVENT_YEAR'))</title>

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
  
  <!-- Google reCAPTCHA -->
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>

  <!-- Custom CSS -->
  <style>
    :root {
      --primary-color: #1B3783;
      --accent-color: #FFC03D;
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

    .exhibitor-registration-header {
      background: var(--header-bg);
      color: white;
      padding: 20px 0;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .exhibitor-registration-header .container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .exhibitor-registration-header .logo-section {
      display: flex;
      align-items: center;
      gap: 20px;
      flex: 1;
    }

    .exhibitor-registration-header .event-logo {
      max-height: 70px;
      height: auto;
      width: auto;
    }

    .exhibitor-registration-header h1 {
      margin: 0;
      font-size: 1.5rem;
      font-weight: 500;
      color: white;
    }

    .exhibitor-registration-header .event-name {
      font-size: 0.9rem;
      opacity: 0.9;
      color: rgba(255, 255, 255, 0.9);
    }

    .exhibitor-registration-footer {
      background-color: var(--primary-color);
      color: white;
      padding: 40px 0 20px;
      margin-top: 50px;
    }

    .exhibitor-registration-footer a {
      color: #adb5bd;
      text-decoration: none;
    }

    .exhibitor-registration-footer a:hover {
      color: white;
    }

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

    .progress-bar {
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

    #autoSaveIndicator {
      min-width: 150px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      border-radius: 8px;
    }

    .step-indicator {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 30px;
    }

    .step-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }

    .step-number {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #e9ecef;
      color: #6c757d;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      margin-bottom: 8px;
    }

    .step-item.active .step-number {
      background-color: var(--primary-color);
      color: white;
    }

    .step-item.completed .step-number {
      background-color: var(--success-color);
      color: white;
    }

    .step-label {
      font-size: 0.875rem;
      color: #6c757d;
      text-align: center;
    }

    .step-item.active .step-label {
      color: var(--primary-color);
      font-weight: 500;
    }

    .step-connector {
      width: 100px;
      height: 2px;
      background-color: #e9ecef;
      margin: 0 10px;
      margin-top: -20px;
    }

    .step-item.completed ~ .step-connector {
      background-color: var(--success-color);
    }

    @media (max-width: 768px) {
      .exhibitor-registration-header h1 {
        font-size: 1.2rem;
      }
      
      .exhibitor-registration-header .event-logo {
        max-height: 50px;
      }
    }
  </style>

  @stack('styles')
</head>
<body>
  {{-- Header --}}
  <header class="exhibitor-registration-header">
    <div class="container">
      <div class="logo-section">
        <img src="https://bengalurutechsummit.com/img/logo-BTS-26-N.png" alt="{{ config('constants.EVENT_NAME') }} Logo" class="event-logo">
        <div>
          <h1>Exhibitor Registration</h1>
          <div class="event-name">{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</div>
        </div>
      </div>
    </div>
  </header>

  {{-- Main Content --}}
  <main class="py-4">
    @yield('content')
  </main>

  {{-- Footer --}}
  <footer class="exhibitor-registration-footer">
    <div class="container">
      <div class="row">
        <div class="col-md-4 mb-4 mb-md-0">
          <h5>Contact Us</h5>
          <ul style="list-style: none; padding: 0;">
            <li><i class="fas fa-envelope"></i> {{ config('constants.ORGANIZER_EMAIL') }}</li>
            <li><i class="fas fa-phone"></i> {{ config('constants.ORGANIZER_PHONE') }}</li>
            <li><i class="fas fa-globe"></i> <a href="{{ config('constants.ORGANIZER_WEBSITE') }}" target="_blank">{{ config('constants.ORGANIZER_WEBSITE') }}</a></li>
          </ul>
        </div>
        <div class="col-md-4 mb-4 mb-md-0">
          <h5>Quick Links</h5>
          <ul style="list-style: none; padding: 0;">
            <li><a href="{{ url('/') }}">Home</a></li>
            <li><a href="{{ route('exhibitor-registration.register') }}">Register</a></li>
            <li><a href="{{ config('constants.ORGANIZER_WEBSITE') }}" target="_blank">Event Website</a></li>
          </ul>
        </div>
        <div class="col-md-4">
          <h5>Event Information</h5>
          <ul style="list-style: none; padding: 0;">
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

