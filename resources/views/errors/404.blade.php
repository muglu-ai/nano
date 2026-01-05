<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | {{ config('constants.EVENT_NAME', 'Event') }}</title>
    
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />
    
    <!-- Fonts and Icons -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <style>
        :root {
            --primary-color: #1B3783;
            --accent-color: #FFC03D;
            --secondary-color: #6c757d;
            --light-bg: #f8f9fa;
            --dark-text: #464646;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--dark-text);
        }

        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 4rem 3rem;
            text-align: center;
            max-width: 600px;
            width: 100%;
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .error-icon {
            width: 150px;
            height: 150px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #2a4a9e 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            color: white;
            font-size: 4rem;
            box-shadow: 0 10px 30px rgba(27, 55, 131, 0.3);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: var(--primary-color);
            line-height: 1;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .error-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 1rem;
        }

        .error-message {
            font-size: 1.1rem;
            color: var(--secondary-color);
            line-height: 1.6;
            margin-bottom: 2.5rem;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-home {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2a4a9e 100%);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(27, 55, 131, 0.3);
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(27, 55, 131, 0.4);
            color: white;
        }

        .btn-back {
            background: white;
            color: var(--primary-color);
            padding: 12px 30px;
            border: 2px solid var(--primary-color);
            border-radius: 50px;
            font-size: 1rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .suggestions {
            margin-top: 2.5rem;
            padding-top: 2rem;
            border-top: 1px solid #e0e0e0;
        }

        .suggestions h4 {
            font-size: 1.1rem;
            color: var(--dark-text);
            margin-bottom: 1rem;
        }

        .suggestions ul {
            list-style: none;
            padding: 0;
        }

        .suggestions li {
            margin-bottom: 0.5rem;
        }

        .suggestions a {
            color: var(--primary-color);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .suggestions a:hover {
            color: #2a4a9e;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .error-container {
                padding: 3rem 2rem;
            }

            .error-code {
                font-size: 4rem;
            }

            .error-title {
                font-size: 1.5rem;
            }

            .error-icon {
                width: 120px;
                height: 120px;
                font-size: 3rem;
            }

            .error-actions {
                flex-direction: column;
            }

            .btn-home,
            .btn-back {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        
        <div class="error-code">404</div>
        
        <h1 class="error-title">Page Not Found</h1>
        
        <p class="error-message">
            Oops! The page you're looking for doesn't exist. It might have been moved, deleted, or the URL might be incorrect.
        </p>
        
		{{--
        <div class="error-actions">
            <a href="{{ url('/') }}" class="btn-home">
                <i class="fas fa-home"></i>
                Go to Homepage
            </a>
            <a href="javascript:history.back()" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Go Back
            </a>
        </div>
        
        <div class="suggestions">
            <h4>You might be looking for:</h4>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                @if(route('exhibitor-registration.register', [], false))
                <li><a href="{{ route('exhibitor-registration.register') }}">Exhibitor Registration</a></li>
                @endif
                @if(route('startup-zone.register', [], false))
                <li><a href="{{ route('startup-zone.register') }}">Startup Zone Registration</a></li>
                @endif
                @if(config('constants.ORGANIZER_WEBSITE'))
                <li><a href="{{ config('constants.ORGANIZER_WEBSITE') }}" target="_blank">Event Website</a></li>
                @endif
            </ul>
        </div>
		--}}

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

