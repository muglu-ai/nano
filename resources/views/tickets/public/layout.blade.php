<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ticket Registration') - {{ $event->event_name ?? config('constants.EVENT_NAME') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --pink-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --blue-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        body {
            background: #0a0a0a;
            color: #fff;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        /* Header Styles - Matching Bengaluru Tech Summit Website */
        .event-header {
            background: #0a0a0a;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .event-branding {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex: 1;
        }

        .karnataka-logo {
            height: 70px;
            width: auto;
            object-fit: contain;
        }

        .event-logo-section {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .event-logo-img {
            height: 60px;
            width: auto;
            object-fit: contain;
            flex-shrink: 0;
        }

        .event-title {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .event-title-wrapper {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .event-title-wrapper {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .event-title .bengaluru {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .event-title .tech-summit {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 1.75rem;
            font-weight: 800;
            line-height: 1.1;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }

        .event-dates {
            color: #fff;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            font-weight: 400;
        }

        .nav-bar {
            background: rgba(30, 30, 46, 0.95);
            border: 2px solid rgba(102, 126, 234, 0.6);
            border-radius: 50px;
            padding: 0.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.2);
        }

        .nav-link-custom {
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0.875rem;
            border-radius: 20px;
            transition: all 0.3s;
            position: relative;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .nav-link-custom:hover {
            background: rgba(102, 126, 234, 0.25);
            color: #fff;
        }

        .nav-link-custom.active {
            background: var(--primary-gradient);
            color: #fff;
        }

        .nav-link-custom i {
            font-size: 0.7rem;
            margin-left: 0.25rem;
        }

        .btn-enquiry {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            transition: all 0.3s;
            font-size: 0.9rem;
            white-space: nowrap;
        }

        .btn-enquiry:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
            color: #fff;
        }

        /* Main Content */
        .ticket-page-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 3rem 1rem;
        }

        /* Mobile Responsive */
        @media (max-width: 992px) {
            .nav-bar {
                flex-wrap: wrap;
                gap: 0.5rem;
                padding: 0.5rem 1rem;
            }

            .nav-link-custom {
                font-size: 0.8rem;
                padding: 0.4rem 0.75rem;
            }

            .btn-enquiry {
                font-size: 0.8rem;
                padding: 0.4rem 1rem;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .event-branding {
                width: 100%;
                justify-content: flex-start;
            }

            .nav-bar {
                width: 100%;
                justify-content: center;
                border-radius: 25px;
            }

            .event-title .bengaluru,
            .event-title .tech-summit {
                font-size: 1.5rem;
            }

            .karnataka-logo {
                height: 50px;
            }

            .event-logo-img {
                height: 50px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Header -->
    <header class="event-header">
        <div class="container">
            <div class="header-content">
                <div class="event-branding">
                    {{-- <img src="https://bengalurutechsummit.com/img/karnataka-logo.png" alt="Government of Karnataka" class="karnataka-logo"> --}}
                    <div class="event-logo-section">
                        <div class="event-title">
                            <div class="event-title-wrapper">
                                                    <img src="https://bengalurutechsummit.com/web/it_forms/images/logo2.png" alt="Bengaluru Tech Summit Logo" class="event-logo-img">

                                {{-- <div class="bengaluru">BENGALURU</div>
                                <div class="tech-summit">TECH SUMMIT</div> --}}
                            </div>
                            <div class="event-dates">
                                @if(isset($event) && $event->start_date && $event->end_date)
                                    {{ \Carbon\Carbon::parse($event->start_date)->format('jS') }} - {{ \Carbon\Carbon::parse($event->end_date)->format('jS. M Y') }}
                                @elseif(isset($event) && $event->event_date)
                                    {{ \Carbon\Carbon::parse($event->event_date)->format('jS') }} - {{ \Carbon\Carbon::parse($event->event_date)->format('jS. M Y') }}
                                @endif
                                @if(isset($event) && $event->event_location)
                                    • {{ $event->event_location }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <nav class="nav-bar">
                    <a href="#" class="nav-link-custom active">Home</a>
                    <a href="#" class="nav-link-custom">About <i class="fas fa-chevron-down"></i></a>
                    <a href="#" class="nav-link-custom">Attend <i class="fas fa-chevron-down"></i></a>
                    <a href="#" class="nav-link-custom">Conference <i class="fas fa-chevron-down"></i></a>
                    <a href="#" class="nav-link-custom">Special Programmes <i class="fas fa-chevron-down"></i></a>
                    <a href="#" class="nav-link-custom">Exhibition & Sponsorship <i class="fas fa-chevron-down"></i></a>
                    <a href="#" class="nav-link-custom">Startup Springboard <i class="fas fa-chevron-down"></i></a>
                    <button class="btn btn-enquiry">Enquiry</button>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer (if needed) -->
    @hasSection('footer')
        <footer>
            @yield('footer')
        </footer>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

