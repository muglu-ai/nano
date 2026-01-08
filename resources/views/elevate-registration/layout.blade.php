<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ELEVATE Registration Form')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Primary Colors - Customize based on ELEVATE branding */
            --primary-color: #6A1B9A; /* Purple - ELEVATE theme */
            --primary-color-dark: #4A0072;
            --primary-color-light: #9C4DCC;
            --accent-color: #E91E63;
            
            /* Background Colors */
            --bg-primary: #f5f5f5;
            --bg-secondary: #ffffff;
            --section-bg: #f8f9fa;
            
            /* Text Colors */
            --text-primary: #333333;
            --text-secondary: #666666;
            --text-light: #999999;
            
            /* Progress Bar Colors */
            --progress-active: #6A1B9A;
            --progress-inactive: #e0e0e0;
            --progress-bg: #f0f0f0;
        }

        body {
            background: var(--bg-primary);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .elevate-header {
            background: var(--bg-secondary);
            border-bottom: 1px solid #e0e0e0;
            padding: 1.5rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .header-logo img {
            max-height: 80px;
            width: auto;
            object-fit: contain;
        }

        /* Main Content */
        .elevate-main {
            flex: 1;
            padding: 2rem 0;
            background: var(--bg-primary);
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .form-card {
            background: var(--bg-secondary);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .form-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-dark) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .form-header h2 {
            margin: 0;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .form-header p {
            margin: 0.5rem 0 0;
            opacity: 0.95;
            font-size: 1rem;
        }

        .form-body {
            padding: 2.5rem;
        }

        /* Section Styles */
        .form-section {
            margin-bottom: 2rem;
        }

        .section-header {
            background: var(--section-bg);
            padding: 1rem 1.5rem;
            border-radius: 8px 8px 0 0;
            margin: -2.5rem -2.5rem 1.5rem -2.5rem;
            border-bottom: 2px solid var(--primary-color);
        }

        .section-header h5 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            display: block;
        }

        .required {
            color: #dc3545;
        }

        .form-control, .form-select {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(106, 27, 154, 0.1);
            outline: none;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Attendee Section */
        .attendee-block {
            background: var(--section-bg);
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border: 1px solid #e0e0e0;
        }

        .attendee-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .attendee-title {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1rem;
        }

        .btn-remove-attendee {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.875rem;
            cursor: pointer;
        }

        .btn-remove-attendee:hover {
            background: #c82333;
        }

        .btn-add-attendee {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            margin-top: 1rem;
        }

        .btn-add-attendee:hover {
            background: var(--primary-color-dark);
        }

        /* Radio Buttons */
        .radio-group {
            display: flex;
            gap: 2rem;
            margin-top: 0.5rem;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .radio-item input[type="radio"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }

        .radio-item label {
            margin: 0;
            cursor: pointer;
            font-weight: normal;
        }

        /* Checkbox Group */
        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 0.5rem;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .checkbox-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }

        .checkbox-item label {
            margin: 0;
            cursor: pointer;
            font-weight: normal;
        }

        /* intlTelInput Styling */
        .iti {
            width: 100%;
            display: block;
            position: relative;
        }

        .iti__flag-container {
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1;
            width: auto;
            min-width: 95px;
            max-width: 100px;
        }

        .attendee-phone-input {
            width: 100% !important;
            padding-left: 105px !important;
            padding-right: 0.75rem !important;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            box-sizing: border-box;
        }

        .attendee-phone-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(106, 27, 154, 0.1);
            outline: none;
        }

        .iti__selected-flag {
            padding: 0 10px 0 12px !important;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            border-right: 2px solid #e0e0e0;
            background-color: #f8f9fa;
            border-radius: 8px 0 0 8px;
            min-width: 95px;
            max-width: 100px;
            box-sizing: border-box;
        }

        .iti__selected-flag:hover {
            background-color: #e9ecef;
        }

        .iti__flag {
            margin-right: 8px !important;
            width: 20px !important;
            height: 15px !important;
        }

        /* Submit Button */
        .btn-submit {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-dark) 100%);
            color: white;
            border: none;
            padding: 1rem 3rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(106, 27, 154, 0.3);
            background: linear-gradient(135deg, var(--primary-color-dark) 0%, var(--primary-color) 100%);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Footer Styles */
        .elevate-footer {
            background: var(--bg-secondary);
            border-top: 1px solid #e0e0e0;
            padding: 2rem 0;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            text-align: center;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        @media (max-width: 768px) {
            .form-body {
                padding: 1.5rem;
            }

            .section-header {
                margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            }

            .radio-group {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="elevate-header">
        <div class="header-content">
            <div class="header-logo">
                <img src="{{ asset('images/logos/elevate-logo.jpg') }}" alt="ELEVATE">
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="elevate-main">
        <div class="form-container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="elevate-footer">
        <div class="footer-content">
            <p>&copy; Copyright {{ date('Y') }} - ELEVATE. All Rights Reserved.</p>
        </div>
    </footer>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"></script>
    @if(config('constants.RECAPTCHA_ENABLED', false))
    <script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @endif
    @stack('scripts')
</body>
</html>
