<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Feedback - Bengaluru Tech Summit</title>
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
            padding: 2rem 1rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
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
        }

        .feedback-wrapper {
            max-width: 1000px;
            margin: 0 auto;
        }

        /* Header Section */
        .header-section {
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 20px 20px 0 0;
            padding: 2.5rem 2rem;
            text-align: center;
            border-bottom: 3px solid rgba(255, 255, 255, 0.1);
        }

        .logo-container {
            margin-bottom: 1.5rem;
            background-color: #000;
            padding: 1.2rem 2.5rem;
            border-radius: 12px;
            display: inline-block;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .logo-container img {
            max-height: 75px;
            width: auto;
        }

        .event-name {
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.5rem;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header-title {
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            margin: 1rem 0 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }

        .header-title i {
            color: #60a5fa;
        }

        .header-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1rem;
            margin: 0;
        }

        /* Main Form Container */
        .feedback-container {
            background: #ffffff;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }

        .feedback-body {
            padding: 3rem 2.5rem;
        }

        .form-section {
            margin-bottom: 3rem;
            padding-bottom: 2.5rem;
            border-bottom: 2px solid #f0f0f0;
            position: relative;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .form-section::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #3b82f6 0%, #8b5cf6 100%);
            border-radius: 2px;
        }

        .form-section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding-left: 1rem;
        }

        .form-section-title i {
            font-size: 1.8rem;
            color: #3b82f6;
        }

        /* Star Rating Styles */
        .star-rating {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin: 2rem 0;
            flex-wrap: wrap;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star-rating label {
            font-size: 2.8rem;
            color: #e2e8f0;
            cursor: pointer;
            transition: all 0.3s ease;
            line-height: 1;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label {
            color: #fbbf24;
            transform: scale(1.1);
        }

        .star-rating input[type="radio"]:checked ~ label,
        .star-rating input[type="radio"]:checked + label {
            color: #f59e0b;
        }

        .rating-label {
            text-align: center;
            font-weight: 600;
            color: #64748b;
            margin-top: 0.75rem;
            font-size: 1rem;
        }

        /* Form Controls */
        .form-label {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
            margin-left: 12px;
        }

        .form-control,
        .form-select {
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.875rem 1.25rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            background: #fff;
            margin-left: 12px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
            outline: none;
            
        }

        textarea.form-control {
            min-height: 130px;
            resize: vertical;
            margin-left: 12px;

        }

        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 4px solid #3b82f6;
            padding: 1.25rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
        }

        .info-box i {
            color: #3b82f6;
            margin-right: 0.5rem;
        }

        /* Recommendation Buttons */
        .recommendation-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 1.5rem;
        }

        .recommendation-btn {
            padding: 1rem 2.5rem;
            border: 2px solid #e2e8f0;
            background: #fff;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            font-size: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .recommendation-btn:hover {
            border-color: #3b82f6;
            color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .recommendation-btn.active {
            background: #10b981;
            border-color: #10b981;
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .recommendation-btn.no.active {
            background: #ef4444;
            border-color: #ef4444;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .recommendation-btn.maybe.active {
            background: #f59e0b;
            border-color: #f59e0b;
            color: white;
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        /* CAPTCHA Section */
        .captcha-container {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.5rem;
        }

        .captcha-img {
            display: inline-block;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.75rem;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        #reload-captcha {
            border-radius: 10px;
            padding: 0.75rem 1.25rem;
            border: 2px solid #e2e8f0;
            background: #fff;
            transition: all 0.3s ease;
        }

        #reload-captcha:hover {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
            transform: rotate(180deg);
        }

        /* Submit Button */
        .btn-submit {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            color: white;
            border: none;
            padding: 1.25rem 4rem;
            font-size: 1.15rem;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(59, 130, 246, 0.5);
            color: white;
        }

        .btn-submit:active {
            transform: translateY(-1px);
        }

        /* Alerts */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .alert-danger {
            background: #fef2f2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .alert-info {
            background: #eff6ff;
            color: #1e40af;
            border-left: 4px solid #3b82f6;
        }

        /* Footer */
        .feedback-footer {
            background: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(10px);
            border-radius: 0 0 20px 20px;
            padding: 1.5rem 2rem;
            text-align: center;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 2rem;
        }

        .feedback-footer p {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
        }

        .feedback-footer a {
            color: #60a5fa;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .feedback-footer a:hover {
            color: #93c5fd;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 1rem 0.5rem;
            }

            .header-section {
                padding: 2rem 1.5rem;
                border-radius: 15px 15px 0 0;
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

            .header-title {
                font-size: 1.8rem;
                flex-direction: column;
                gap: 0.5rem;
            }

            .feedback-body {
                padding: 2rem 1.5rem;
            }

            .form-section-title {
                font-size: 1.3rem;
            }

            .star-rating label {
                font-size: 2.2rem;
            }

            .btn-submit {
                padding: 1rem 3rem;
                font-size: 1rem;
                width: 100%;
            }

            .feedback-footer {
                padding: 1.25rem 1.5rem;
                border-radius: 0 0 15px 15px;
            }

            .feedback-footer p {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="feedback-wrapper">
            <!-- Header Section -->
            <div class="header-section">
                <div class="logo-container">
                    <img src="https://bengalurutechsummit.com/img/logo-BTS-25-N.png" alt="Bengaluru Tech Summit Logo" class="img-fluid">
                </div>
                <div class="event-name">Bengaluru Tech Summit</div>
                <h1 class="header-title">
                    <i class="bi bi-chat-heart-fill"></i>
                    <span>Share Your Feedback</span>
                </h1>
                <p class="header-subtitle">Your opinion matters! Help us improve by sharing your experience</p>
            </div>

            <!-- Main Form Container -->
            <div class="feedback-container">
                <div class="feedback-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                        </div>
                    @endif

                    @if(session('info'))
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle-fill"></i> {{ session('info') }}
                        </div>
                    @endif

                    @if($user)
                        <div class="info-box">
                            <i class="bi bi-person-circle"></i> <strong>{{ $user->name }}</strong> | 
                            <i class="bi bi-envelope"></i> {{ $user->email }}
                            @if($companyName)
                                | <i class="bi bi-building"></i> {{ $companyName }}
                            @endif
                        </div>
                    @endif

                    <form action="{{ route('feedback.store') }}" method="POST" id="feedbackForm">
                        @csrf

                        <!-- Basic Information -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-person-badge-fill"></i>
                                <span>Basic Information</span>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ old('name', $user ? $user->name : '') }}" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="{{ old('email', $user ? $user->email : '') }}" required>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="company_name" name="company_name" 
                                           value="{{ old('company_name', $companyName) }}">
                                    @error('company_name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="{{ old('phone', $user ? $user->phone : '') }}">
                                    @error('phone')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Event Rating -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-calendar-event-fill"></i>
                                <span>Event Rating</span>
                            </div>
                            <div class="mb-4">
                                <label class="form-label d-block text-center">How would you rate the overall event? <span class="text-danger">*</span></label>
                                <div class="star-rating" id="eventRating">
                                    <input type="radio" name="event_rating" value="5" id="event5" {{ old('event_rating') == '5' ? 'checked' : '' }} required>
                                    <label for="event5"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="event_rating" value="4" id="event4" {{ old('event_rating') == '4' ? 'checked' : '' }}>
                                    <label for="event4"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="event_rating" value="3" id="event3" {{ old('event_rating') == '3' ? 'checked' : '' }}>
                                    <label for="event3"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="event_rating" value="2" id="event2" {{ old('event_rating') == '2' ? 'checked' : '' }}>
                                    <label for="event2"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="event_rating" value="1" id="event1" {{ old('event_rating') == '1' ? 'checked' : '' }}>
                                    <label for="event1"><i class="bi bi-star-fill"></i></label>
                                </div>
                                <div class="rating-label" id="eventRatingLabel">Select a rating</div>
                                @error('event_rating')
                                    <div class="text-danger text-center small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-center d-block">Event Organization</label>
                                    <div class="star-rating" style="font-size: 2rem;">
                                        <input type="radio" name="event_organization_rating" value="5" id="org5" {{ old('event_organization_rating') == '5' ? 'checked' : '' }}>
                                        <label for="org5"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="event_organization_rating" value="4" id="org4" {{ old('event_organization_rating') == '4' ? 'checked' : '' }}>
                                        <label for="org4"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="event_organization_rating" value="3" id="org3" {{ old('event_organization_rating') == '3' ? 'checked' : '' }}>
                                        <label for="org3"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="event_organization_rating" value="2" id="org2" {{ old('event_organization_rating') == '2' ? 'checked' : '' }}>
                                        <label for="org2"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="event_organization_rating" value="1" id="org1" {{ old('event_organization_rating') == '1' ? 'checked' : '' }}>
                                        <label for="org1"><i class="bi bi-star-fill"></i></label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-center d-block">Venue</label>
                                    <div class="star-rating" style="font-size: 2rem;">
                                        <input type="radio" name="venue_rating" value="5" id="venue5" {{ old('venue_rating') == '5' ? 'checked' : '' }}>
                                        <label for="venue5"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="venue_rating" value="4" id="venue4" {{ old('venue_rating') == '4' ? 'checked' : '' }}>
                                        <label for="venue4"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="venue_rating" value="3" id="venue3" {{ old('venue_rating') == '3' ? 'checked' : '' }}>
                                        <label for="venue3"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="venue_rating" value="2" id="venue2" {{ old('venue_rating') == '2' ? 'checked' : '' }}>
                                        <label for="venue2"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="venue_rating" value="1" id="venue1" {{ old('venue_rating') == '1' ? 'checked' : '' }}>
                                        <label for="venue1"><i class="bi bi-star-fill"></i></label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label text-center d-block">Networking Opportunities - B2B Interlinx Platform</label>
                                    <div class="star-rating" style="font-size: 2rem;">
                                        <input type="radio" name="networking_opportunities_rating" value="5" id="net5" {{ old('networking_opportunities_rating') == '5' ? 'checked' : '' }}>
                                        <label for="net5"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="networking_opportunities_rating" value="4" id="net4" {{ old('networking_opportunities_rating') == '4' ? 'checked' : '' }}>
                                        <label for="net4"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="networking_opportunities_rating" value="3" id="net3" {{ old('networking_opportunities_rating') == '3' ? 'checked' : '' }}>
                                        <label for="net3"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="networking_opportunities_rating" value="2" id="net2" {{ old('networking_opportunities_rating') == '2' ? 'checked' : '' }}>
                                        <label for="net2"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" name="networking_opportunities_rating" value="1" id="net1" {{ old('networking_opportunities_rating') == '1' ? 'checked' : '' }}>
                                        <label for="net1"><i class="bi bi-star-fill"></i></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Portal Rating -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-laptop-fill"></i>
                                <span>Portal Access Rating</span>
                            </div>
                            <div class="mb-4">
                                <label class="form-label d-block text-center">How would you rate the exhibitor portal experience? <span class="text-danger">*</span></label>
                                <div class="star-rating" id="portalRating">
                                    <input type="radio" name="portal_rating" value="5" id="portal5" {{ old('portal_rating') == '5' ? 'checked' : '' }} required>
                                    <label for="portal5"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="portal_rating" value="4" id="portal4" {{ old('portal_rating') == '4' ? 'checked' : '' }}>
                                    <label for="portal4"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="portal_rating" value="3" id="portal3" {{ old('portal_rating') == '3' ? 'checked' : '' }}>
                                    <label for="portal3"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="portal_rating" value="2" id="portal2" {{ old('portal_rating') == '2' ? 'checked' : '' }}>
                                    <label for="portal2"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="portal_rating" value="1" id="portal1" {{ old('portal_rating') == '1' ? 'checked' : '' }}>
                                    <label for="portal1"><i class="bi bi-star-fill"></i></label>
                                </div>
                                <div class="rating-label" id="portalRatingLabel">Select a rating</div>
                                @error('portal_rating')
                                    <div class="text-danger text-center small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Overall Experience -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-emoji-smile-fill"></i>
                                <span>Overall Experience</span>
                            </div>
                            <div class="mb-4">
                                <label class="form-label d-block text-center">Overall Experience Rating</label>
                                <div class="star-rating" id="overallRating">
                                    <input type="radio" name="overall_experience_rating" value="5" id="overall5" {{ old('overall_experience_rating') == '5' ? 'checked' : '' }}>
                                    <label for="overall5"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="overall_experience_rating" value="4" id="overall4" {{ old('overall_experience_rating') == '4' ? 'checked' : '' }}>
                                    <label for="overall4"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="overall_experience_rating" value="3" id="overall3" {{ old('overall_experience_rating') == '3' ? 'checked' : '' }}>
                                    <label for="overall3"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="overall_experience_rating" value="2" id="overall2" {{ old('overall_experience_rating') == '2' ? 'checked' : '' }}>
                                    <label for="overall2"><i class="bi bi-star-fill"></i></label>
                                    <input type="radio" name="overall_experience_rating" value="1" id="overall1" {{ old('overall_experience_rating') == '1' ? 'checked' : '' }}>
                                    <label for="overall1"><i class="bi bi-star-fill"></i></label>
                                </div>
                                <div class="rating-label" id="overallRatingLabel">Select a rating</div>
                            </div>
                        </div>

                        <!-- Feedback Text -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-chat-left-text-fill"></i>
                                <span>Your Feedback</span>
                            </div>
                            <div class="mb-3">
                                <label for="what_liked_most" class="form-label">What did you like most about the event? <i class="bi bi-heart-fill text-danger"></i></label>
                                <textarea class="form-control" id="what_liked_most" name="what_liked_most" 
                                          rows="4" placeholder="Share what impressed you the most...">{{ old('what_liked_most') }}</textarea>
                                @error('what_liked_most')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="what_could_be_improved" class="form-label">What could be improved? <i class="bi bi-lightbulb-fill text-warning"></i></label>
                                <textarea class="form-control" id="what_could_be_improved" name="what_could_be_improved" 
                                          rows="4" placeholder="Your suggestions help us improve...">{{ old('what_could_be_improved') }}</textarea>
                                @error('what_could_be_improved')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="additional_comments" class="form-label">Additional Comments or Suggestions</label>
                                <textarea class="form-control" id="additional_comments" name="additional_comments" 
                                          rows="4" placeholder="Any other thoughts or feedback...">{{ old('additional_comments') }}</textarea>
                                @error('additional_comments')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Recommendation -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-share-fill"></i>
                                <span>Recommendation</span>
                            </div>
                            <label class="form-label d-block text-center mb-3">Would you recommend this event to others?</label>
                            <div class="recommendation-buttons">
                                <input type="radio" name="would_recommend" value="yes" id="recommend_yes" 
                                       class="d-none" {{ old('would_recommend') == 'yes' ? 'checked' : '' }}>
                                <label for="recommend_yes" class="recommendation-btn yes">
                                    <i class="bi bi-hand-thumbs-up-fill"></i> Yes
                                </label>
                                <input type="radio" name="would_recommend" value="maybe" id="recommend_maybe" 
                                       class="d-none" {{ old('would_recommend') == 'maybe' ? 'checked' : '' }}>
                                <label for="recommend_maybe" class="recommendation-btn maybe">
                                    <i class="bi bi-question-circle-fill"></i> Maybe
                                </label>
                                <input type="radio" name="would_recommend" value="no" id="recommend_no" 
                                       class="d-none" {{ old('would_recommend') == 'no' ? 'checked' : '' }}>
                                <label for="recommend_no" class="recommendation-btn no">
                                    <i class="bi bi-hand-thumbs-down-fill"></i> No
                                </label>
                            </div>
                            @error('would_recommend')
                                <div class="text-danger text-center small mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- CAPTCHA -->
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-shield-check-fill"></i>
                                <span>Security Verification</span>
                            </div>
                            <div class="captcha-container">
                                <div class="row align-items-center">
                                    <div class="col-md-6 mb-3">
                                        <label for="captcha" class="form-label">Enter CAPTCHA <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="captcha" name="captcha" 
                                               maxlength="6" placeholder="Enter CAPTCHA" required autocomplete="off">
                                        @error('captcha')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label d-block">CAPTCHA Image</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <div id="captcha-img" style="flex: 1;">
                                                {!! session('captchaSvg') ?? $captchaSvg ?? '' !!}
                                            </div>
                                            <button type="button" id="reload-captcha" class="btn btn-outline-secondary" 
                                                    title="Reload CAPTCHA">
                                                <i class="bi bi-arrow-clockwise"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center mt-5">
                            <button type="submit" class="btn btn-submit">
                                <i class="bi bi-send-fill"></i> Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="feedback-footer">
                <p>
                    Designed and developed by 
                    <a href="https://interlinks.in/" target="_blank" rel="noopener noreferrer">
                        SCI Knowledge Interlinks PVT. LTD.
                    </a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Star rating labels
        const ratingLabels = {
            event: {
                5: 'Excellent',
                4: 'Very Good',
                3: 'Good',
                2: 'Fair',
                1: 'Poor'
            },
            portal: {
                5: 'Excellent',
                4: 'Very Good',
                3: 'Good',
                2: 'Fair',
                1: 'Poor'
            },
            overall: {
                5: 'Excellent',
                4: 'Very Good',
                3: 'Good',
                2: 'Fair',
                1: 'Poor'
            }
        };

        // Update rating labels
        function updateRatingLabel(type, value) {
            const labelElement = document.getElementById(type + 'RatingLabel');
            if (labelElement && value) {
                labelElement.textContent = ratingLabels[type][value] || 'Select a rating';
            }
        }

        // Event rating
        document.querySelectorAll('#eventRating input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateRatingLabel('event', this.value);
            });
            if (radio.checked) {
                updateRatingLabel('event', radio.value);
            }
        });

        // Portal rating
        document.querySelectorAll('#portalRating input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateRatingLabel('portal', this.value);
            });
            if (radio.checked) {
                updateRatingLabel('portal', radio.value);
            }
        });

        // Overall rating
        document.querySelectorAll('#overallRating input[type="radio"]').forEach(radio => {
            radio.addEventListener('change', function() {
                updateRatingLabel('overall', this.value);
            });
            if (radio.checked) {
                updateRatingLabel('overall', radio.value);
            }
        });

        // Recommendation button styling
        document.querySelectorAll('.recommendation-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                document.querySelectorAll('.recommendation-btn').forEach(b => b.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
            });
        });

        // Set initial active state for recommendation buttons
        document.querySelectorAll('input[name="would_recommend"]:checked').forEach(radio => {
            const label = document.querySelector(`label[for="${radio.id}"]`);
            if (label) {
                label.classList.add('active');
            }
        });

        // CAPTCHA reload functionality
        document.getElementById('reload-captcha')?.addEventListener('click', function() {
            fetch('{{ route("feedback.reload.captcha") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('captcha-img').innerHTML = data.captcha;
                document.getElementById('captcha').value = ''; // Clear the input
            })
            .catch(error => {
                console.error('Error reloading CAPTCHA:', error);
            });
        });
    </script>
</body>
</html>
