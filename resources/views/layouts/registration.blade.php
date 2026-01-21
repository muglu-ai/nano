<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Registration') - {{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @if(config('constants.FAVICON'))
        <link rel="icon" href="{{ config('constants.FAVICON') }}" type="image/x-icon">
    @endif

    @if(config('constants.FAVICON_APPLE'))
        <link rel="apple-touch-icon" href="{{ config('constants.FAVICON_APPLE') }}">
    @endif

    @if(config('constants.FAVICON_16'))
        <link rel="icon" href="{{ config('constants.FAVICON_16') }}" type="image/x-icon">
    @endif

    @if(config('constants.FAVICON_32'))
        <link rel="icon" href="{{ config('constants.FAVICON_32') }}" type="image/x-icon">
    @endif

    @if(config('constants.FAVICON_64'))
        <link rel="icon" href="{{ config('constants.FAVICON_64') }}" type="image/x-icon">
    @endif







    @stack('head-links')


    <link rel="stylesheet" href="{{ asset('asset/css/custom.css') }}">
    
    <style>
        :root {
            /* Primary Color Palette */
            --primary-color: #0B5ED7;
            --primary-color-dark: #084298;
            --primary-color-light: #6EA8FE;
            --accent-color: #20C997;
            --progress-active: #0B5ED7;
            
            /* Background Colors */
            --bg-primary: #f5f5f5; /* Grey background */
            --bg-secondary: #ffffff; /* White for form container */
            
            /* Text Colors */
            --text-primary: #333333;
            --text-secondary: #666666;
            --text-light: #999999;
            
            /* Progress Bar Colors */
            --progress-inactive: #e0e0e0;
            --progress-bg: #f0f0f0;

            /* Gradients based on primary colors */
            --primary-gradient: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-color-dark) 100%);
            --primary-light-gradient: linear-gradient(135deg, var(--primary-color-light) 0%, var(--primary-color) 100%);
            --accent-gradient: linear-gradient(135deg, var(--accent-color) 0%, #1aa87a 100%);
        }

        body {
            background: var(--bg-primary);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .registration-header {
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
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-logo img {
            max-height: 80px;
            width: auto;
            object-fit: contain;
        }

        .header-title {
            display: flex;
            flex-direction: column;
        }

        .header-title h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .header-title p {
            margin: 0.25rem 0 0;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        /* Main Content */
        .registration-main {
            flex: 1;
            padding: 2rem 0;
            background: var(--bg-primary);
        }

        .form-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Responsive form container widths */
        @media (max-width: 1400px) {
            .form-container {
                max-width: 1200px;
            }
        }
        
        @media (max-width: 1200px) {
            .form-container {
                max-width: 100%;
                padding: 0 1.5rem;
            }
        }
        
        @media (max-width: 992px) {
            .form-container {
                padding: 0 1rem;
            }
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 0 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .form-container {
                padding: 0 0.5rem;
            }
        }

        .form-card {
            background: var(--bg-secondary);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .form-header {
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

        /* Progress Bar Styles */
        .progress-container {
            margin-bottom: 2rem;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1rem;
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid var(--progress-inactive);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .step-item:not(.active):not(.completed):hover .step-number {
            border-color: var(--primary-color-light);
            background: #f8f9ff;
            transform: scale(1.05);
        }

        .step-item.active .step-number {
            background: var(--progress-active);
            border-color: var(--progress-active);
            color: white;
            box-shadow: 0 4px 15px rgba(11, 94, 215, 0.4);
            transform: scale(1.05);
        }

        .step-item.completed .step-number {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: white;
            font-size: 0;
        }

        .step-item.completed .step-number::before {
            content: '✓';
            position: absolute;
            font-size: 1.4rem;
            font-weight: bold;
            color: white;
        }

        .step-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
            transition: all 0.3s;
        }

        .step-item.active .step-label {
            color: var(--progress-active);
            font-weight: 600;
        }

        .step-item.completed .step-label {
            color: var(--accent-color);
            font-weight: 600;
        }

        .step-connector {
            width: 100px;
            height: 3px;
            background: var(--progress-inactive);
            margin: 0 1rem;
            margin-top: -25px;
            transition: all 0.3s;
            position: relative;
            z-index: 0;
        }

        .step-item.active ~ .step-item .step-connector,
        .step-item.active .step-connector,
        .step-item.completed ~ .step-item .step-connector,
        .step-item.completed .step-connector {
            background: var(--progress-active);
        }

        .progress-bar-custom {
            height: 10px;
            background: var(--progress-bg);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 1rem;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }

        .progress-fill {
            height: 100%;
            background: var(--primary-gradient);
            width: 50%;
            transition: width 0.3s ease;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        /* Form Styles */
        .form-section {
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: var(--primary-color);
        }

        .form-label {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
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
            box-shadow: 0 0 0 3px rgba(11, 94, 215, 0.1);
            outline: none;
        }

        /* Fix intl-tel-input alignment to match other form fields */
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

        .phone-input {
            width: 100% !important;
            padding-left: 105px !important;
            padding-right: 0.75rem !important;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding-top: 0.75rem;
            padding-bottom: 0.75rem;
            box-sizing: border-box;
        }

        .phone-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(11, 94, 215, 0.1);
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
            overflow: visible;
        }

        .iti__selected-flag:hover {
            background-color: #e9ecef;
        }

        .iti__flag {
            margin-right: 8px !important;
            width: 20px !important;
            height: 15px !important;
            flex-shrink: 0;
        }

        .iti__selected-dial-code {
            margin-left: 0 !important;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .iti__arrow {
            margin-left: 6px;
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 4px solid #555;
            margin-top: 2px;
        }

        /* Ensure the wrapper doesn't break layout */
        .form-section .iti {
            margin-bottom: 0;
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            .phone-input {
                padding-left: 100px !important;
            }

            .iti__flag-container {
                min-width: 90px;
                max-width: 95px;
            }

            .iti__selected-flag {
                padding: 0 8px 0 10px !important;
                min-width: 90px;
                max-width: 95px;
            }

            .iti__flag {
                margin-right: 6px !important;
            }

            .iti__selected-dial-code {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .phone-input {
                padding-left: 95px !important;
            }

            .iti__flag-container {
                min-width: 85px;
                max-width: 90px;
            }

            .iti__selected-flag {
                padding: 0 6px 0 8px !important;
                min-width: 85px;
                max-width: 90px;
            }

            .iti__flag {
                margin-right: 5px !important;
                width: 18px !important;
                height: 13px !important;
            }

            .iti__selected-dial-code {
                font-size: 0.85rem;
            }
        }

        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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

        .char-counter {
            font-size: 0.85rem;
            color: var(--text-secondary);
            text-align: right;
            margin-top: 0.25rem;
        }

        .char-counter.warning {
            color: #ff9800;
        }

        .char-counter.danger {
            color: #dc3545;
        }

        .btn-submit {
            background: var(--primary-gradient);
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
            box-shadow: 0 5px 20px rgba(11, 94, 215, 0.3);
            background: linear-gradient(135deg, var(--primary-color-dark) 0%, var(--primary-color) 100%);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .success-message {
            color: var(--accent-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Footer Styles */
        .registration-footer {
            background: var(--bg-secondary);
            border-top: 1px solid #e0e0e0;
            padding: 2rem 0;
            margin-top: auto;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .footer-content p {
            margin: 0;
        }

        .footer-links {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: var(--primary-color);
        }

        /* Responsive form body padding */
        @media (max-width: 992px) {
            .form-body {
                padding: 2rem;
            }
        }
        
        @media (max-width: 768px) {
            .form-body {
                padding: 1.5rem;
            }

            .checkbox-group {
                grid-template-columns: 1fr;
            }

            .step-connector {
                width: 50px;
            }

            .header-content {
                flex-direction: column;
                text-align: center;
            }

            .footer-content {
                flex-direction: column;
                text-align: center;
            }
        }
        
        @media (max-width: 576px) {
            .form-body {
                padding: 1rem;
            }
            
            .form-header {
                padding: 1.5rem 1rem;
            }
            
            .form-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="registration-header">
        <div class="header-content">
            <div class="header-logo">
                @if(config('constants.event_logo'))
                    <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.EVENT_NAME', 'Event') }}">
                @endif
                @hasSection('header-title')
                    <div class="header-title">
                        @yield('header-title')
                    </div>
                @endif
            </div>
            <div class="header-actions">
                @hasSection('header-actions')
                    @yield('header-actions')
                @endif
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="registration-main">
        <div class="form-container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="registration-footer">
        <div class="footer-content">
            <div>
                <p>&copy; Copyright {{ date('Y') }} - {{ config('constants.EVENT_NAME', 'Event') }}. All Rights Reserved.</p>
            </div>
            <div class="footer-links">
                @hasSection('footer-links')
                    @yield('footer-links')
                @else
                    <a href="https://www.bengalurutechsummit.com/privacy-policy.php">Privacy Policy</a>
                    <a href="https://www.bengalurutechsummit.com/contact.php">Contact Us</a>
                @endif
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
    
    @if(config('constants.RECAPTCHA_ENABLED', false))
    <script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @endif
</body>
</html>
