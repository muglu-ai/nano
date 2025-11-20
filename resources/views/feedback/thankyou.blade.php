<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - Bengaluru Tech Summit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 25%, #7e22ce 75%, #9333ea 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
            padding: 2rem 1rem;
        }

        /* Animated background elements */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 700px;
        }

        .thankyou-wrapper {
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            text-align: center;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .logo-container {
            margin-bottom: 2rem;
            background-color: #000;
            padding: 1.2rem 2.5rem;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .logo-container img {
            max-height: 70px;
            width: auto;
        }

        .event-name {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 2rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .success-icon {
            font-size: 5.5rem;
            color: #10b981;
            margin-bottom: 1.5rem;
            animation: scaleIn 0.6s ease-out;
            filter: drop-shadow(0 4px 12px rgba(16, 185, 129, 0.4));
        }

        @keyframes scaleIn {
            from {
                transform: scale(0) rotate(-180deg);
                opacity: 0;
            }
            to {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        h1 {
            color: #fff;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }

        .message-text {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.15rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .sub-message {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            margin-bottom: 2.5rem;
        }

        .btn-home {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            border: none;
            padding: 1rem 3rem;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(59, 130, 246, 0.5);
            color: white;
        }

        .btn-home:active {
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .thankyou-wrapper {
                padding: 2.5rem 2rem;
                border-radius: 15px;
            }

            .logo-container {
                padding: 1rem 2rem;
            }

            .logo-container img {
                max-height: 60px;
            }

            .event-name {
                font-size: 1.3rem;
            }

            h1 {
                font-size: 2rem;
            }

            .success-icon {
                font-size: 4.5rem;
            }

            .message-text {
                font-size: 1rem;
            }

            .btn-home {
                padding: 0.875rem 2.5rem;
                font-size: 1rem;
                width: 100%;
            }
        }

        /* Footer */
        .thankyou-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .thankyou-footer p {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .thankyou-footer a {
            color: #60a5fa;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .thankyou-footer a:hover {
            color: #93c5fd;
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .thankyou-footer p {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="thankyou-wrapper">
            <div class="logo-container">
                <img src="https://bengalurutechsummit.com/img/logo-BTS-25-N.png" alt="Bengaluru Tech Summit Logo" class="img-fluid">
            </div>
            <div class="event-name">Bengaluru Tech Summit</div>
            <div class="success-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <h1>Thank You!</h1>
            <p class="message-text">
                @if(session('success'))
                    {{ session('success') }}
                @elseif(session('info'))
                    {{ session('info') }}
                @else
                    Your feedback has been submitted successfully. We truly appreciate you taking the time to share your thoughts with us!
                @endif
            </p>
            <p class="sub-message">
                Your feedback helps us improve and deliver better experiences in the future.
            </p>
            @auth
                <a href="{{ route('user.dashboard') }}" class="btn btn-home">
                    <i class="bi bi-house-fill"></i> Back to Dashboard
                </a>
            @else
                <a href="{{ route('feedback.show') }}" class="btn btn-home">
                    <i class="bi bi-arrow-left"></i> Submit Another Feedback
                </a>
            @endauth

            <!-- Footer -->
            <div class="thankyou-footer">
                <p>
                    Designed and developed by 
                    <a href="https://interlinks.in/" target="_blank" rel="noopener noreferrer">
                        SCI Knowledge Interlinks PVT. LTD.
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
