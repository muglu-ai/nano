<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> SEMICON® India 2025: Registration</title>

  <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Custom Styles for a more professional look -->
  <style>
    body {
      background-color: #f0f2f5;
      /* A light, professional grey */
      background-image: linear-gradient(145deg, #e2e8f0, #f8fafc);
      /* Subtle gradient */
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .thank-you-card {
      max-width: 650px;
      border: none;
      border-radius: 15px;
      /* A soft shadow for depth */
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
      animation: fadeIn 0.8s ease-in-out;
    }

    .card-header {
      background-color: #ffffff;
      border-bottom: none;
      padding: 2rem 2rem 1rem;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
    }

    .card-header img {
      max-width: 170px;
      height: auto;
    }

    .card-body {
      padding: 1rem 2rem;
    }

    .card-footer {
      background-color: #f8f9fa;
      border-top: 1px solid #dee2e6;
      padding: 1.5rem 2rem;
      border-bottom-left-radius: 15px;
      border-bottom-right-radius: 15px;
    }

    .registration-id-wrapper {
      background-color: #e9ecef;
      padding: 1rem;
      border-radius: 8px;
      font-family: 'Courier New', Courier, monospace;
      font-size: 1.25rem;
      letter-spacing: 2px;
      font-weight: bold;
      color: #0056b3;
    }

    .social-icons a {
      color: #6c757d;
      font-size: 1.5rem;
      margin: 0 10px;
      transition: color 0.3s ease;
    }

    .social-icons a:hover {
      color: #0d6efd;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body>

  <div class="card thank-you-card text-center">
    <!-- Card Header: Logo and Main Title -->
    <div class="card-header">
      <div class="d-flex justify-content-center align-items-center mb-4 gap-3">
        
        <img src="{{ asset('asset/img/logos/meity-logo.png') }}" width="120" alt="Ministry of Electronics & IT Logo" class="logo2">
        <img src="{{ asset('asset/img/logos/ism_logo.png') }}" width="120" alt="ISM Logo" class="logo3">
        <img src="{{ asset('asset/img/logos/DIC_Logo.webp') }}" width="120" alt="DIC Logo" class="logo4">
         <img src="{{ asset('asset/img/logos/SEMI_IESA_logo.png') }}" width="120" alt="SEMI IESA Logo" class="logo1">
       
      </div>
      <h1 class="h2">Thank You, {{ ucfirst($attendee->first_name) }}!</h1>
      <p class="lead text-muted">Your Inaugural registration for SEMICON® India 2025  is received.</p>
    </div>

    <!-- Card Body: Key Information and QR Code -->
    <div class="card-body">
      
      <div class="alert alert-info d-flex align-items-center mt-3" role="alert">
        <i class="fa-solid fa-circle-info me-2"></i>
        <span>
          <strong>Note:</strong> Kindly note that participation (in-person) in the Inaugural event is subject to final confirmation  and will be informed separately from 1st week of August onwards.
        </span>
      </div>


  <!-- QR Code Section -->

  <!-- Action Buttons -->
  <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
    <a href="https://www.semiconindia.org/" target="_blank" class="btn btn-primary btn-lg px-4 gap-3"><i class="fa-solid fa-globe"></i> Visit Website</a>
    <!-- **UPDATED** Google Calendar Link with correct details -->
    <a href="https://www.google.com/calendar/render?action=TEMPLATE&text=SEMICON%C2%AE+India+2025&dates=20250902T100000/20250904T180000&details=My+registration+for+SEMICON%C2%AE+India+2025.+More+details+at+https://www.semiconindia.org/&location=Yashobhoomi+(IICC),+New+Delhi" target="_blank" class="btn btn-outline-secondary btn-lg px-4"><i class="fa-regular fa-calendar-plus"></i> Add to Calendar</a>
  </div>
  </div>

  <!-- Card Footer: Social Sharing -->
  <div class="card-footer text-center">
    <h6 class="text-muted fw-light mb-3">Share the excitement with your network!</h6>
    <div class="social-icons">
      <!-- **UPDATED** Twitter (X) -->
      <a href="https://twitter.com/intent/tweet?text={{ urlencode('I\'ve just registered for SEMICON® India 2025, happening Sep 2-4 at Yashobhoomi (IICC), New Delhi! Excited to connect with industry leaders. Join me!') }}&url={{ urlencode('https://www.semiconindia.org/') }}&hashtags=SEMICONIndia,Semiconductors,TechEvent" target="_blank" title="Share on Twitter"><i class="fab fa-x-twitter"></i></a>
      <!-- **UPDATED** LinkedIn -->
      <a href="https://www.linkedin.com/feed/?shareActive=true&text=I've+just+registered+for+SEMICON%C2%AE+India+2025,+happening+Sep+2-4+at+Yashobhoomi+(IICC),+New+Delhi!+Excited+to+connect+with+industry+leaders.+Join+me:+https://www.semiconindia.org/+%23SEMICONIndia+%23Semiconductors+%23SEMI" target="_blank" title="Share on LinkedIn"><i class="fab fa-linkedin-in"></i></a>
      <!-- Facebook -->
      <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.semiconindia.org/" target="_blank" title="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
    </div>
  </div>
  </div>



</body>

</html>