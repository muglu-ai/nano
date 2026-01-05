<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Enquiry Form') - {{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }} {{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css">
    
    <style>
        :root {
            /* 
             * COLOR CUSTOMIZATION GUIDE:
             * To match different logo colors, simply change the values below:
             * 
             * Examples:
             * - Teal (default): #20b2aa
             * - Blue: #3b82f6
             * - Purple: #8b5cf6
             * - Green: #10b981
             * - Orange: #f59e0b
             * 
             * Just replace the hex codes below with your brand colors
             */
            
            /* Primary Colors - Can be customized based on logo */
            --primary-color: #20b2aa; /* Teal - default, change this to match logo */
            --primary-color-dark: #1a9b94; /* Darker shade of primary */
            --primary-color-light: #4dd0c7; /* Lighter shade of primary */
            --accent-color: #20b2aa; /* Accent color (usually same as primary) */
            
            /* Background Colors */
            --bg-primary: #f5f5f5; /* Grey background */
            --bg-secondary: #ffffff; /* White for form container */
            
            /* Text Colors */
            --text-primary: #333333;
            --text-secondary: #666666;
            --text-light: #999999;
            
            /* Progress Bar Colors */
            --progress-active: #20b2aa; /* Teal */
            --progress-inactive: #e0e0e0;
            --progress-bg: #f0f0f0;

            --primary-color: #6A1B9A;        
            --primary-color-dark: #4A0072;   
            --primary-color-light: #9C4DCC;  
            --accent-color: #E91E63;         
            --progress-active: #6A1B9A;
        }

        body {
            background: var(--bg-primary);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .enquiry-header {
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

        header-logo img {
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
        .enquiry-main {
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

        /* Progress Bar Styles - Teal */
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
            background: var(--progress-inactive);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s;
        }

        .step-item.active .step-number {
            background: var(--progress-active);
            color: white;
            box-shadow: 0 4px 12px rgba(32, 178, 170, 0.3);
        }

        .step-label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        .step-item.active .step-label {
            color: var(--progress-active);
            font-weight: 600;
        }

        .step-connector {
            width: 100px;
            height: 3px;
            background: var(--progress-inactive);
            margin: 0 1rem;
            margin-top: -25px;
            transition: all 0.3s;
        }

        .step-item.active ~ .step-item .step-connector,
        .step-item.active .step-connector {
            background: var(--progress-active);
        }

        .progress-bar-custom {
            height: 8px;
            background: var(--progress-bg);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 1rem;
        }

        .progress-fill {
            height: 100%;
            background: var(--progress-active);
            width: 50%;
            transition: width 0.3s;
            border-radius: 10px;
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
            box-shadow: 0 0 0 3px rgba(32, 178, 170, 0.1);
            outline: none;
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
            box-shadow: 0 5px 20px rgba(32, 178, 170, 0.3);
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

        /* Footer Styles */
        .enquiry-footer {
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
    </style>

    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="enquiry-header">
        <div class="header-content">
            <div class="header-logo">
                @if(config('constants.event_logo'))
                    <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.EVENT_NAME', 'Event') }}">
                @endif
                {{--
                <div class="header-title">
                    <h1>{{ $event->event_name ?? config('constants.EVENT_NAME', 'Event') }}</h1>
                    <p>{{ $event->event_year ?? config('constants.EVENT_YEAR', date('Y')) }}</p>
                </div>
                --}}
            </div>
            <div class="header-actions">
                <!-- Add any header actions here if needed -->
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="enquiry-main">
        <div class="form-container">
            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="enquiry-footer">
        <div class="footer-content">
            <div>
                <p>&copy; Copyright {{ date('Y') }} - {{ config('constants.EVENT_NAME', 'Event') }}. All Rights Reserved.</p>
            </div>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms & Conditions</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </footer>

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

