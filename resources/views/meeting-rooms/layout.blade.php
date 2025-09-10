<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/meeting-rooms.css') }}">
    <title>{{ config('app.name', 'SEMICON India') }} - Meeting Room Booking</title>
 <link rel="icon" href="https://www.semiconindia.org/themes/custom/semicon/favicon.ico" type="image/vnd.microsoft.icon" />
<!-- SweetAlert2 CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom styles for cursor and transitions */
        .cursor-pointer { cursor: pointer; }
        .slot-card { transition: all 0.2s ease-in-out; }
        .slot-card.available:hover { transform: translateY(-5px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="main-container">
    <div id="app">
        
        @include('meeting-rooms._partials.header')

        <main class="py-4">
            <div class="container">
                @yield('content')
            </div>
        </main>
        
        @include('meeting-rooms._partials.footer')

    </div>

    <!-- Bootstrap JS Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    
    <!-- Page-specific scripts -->
    @stack('scripts')
</body>
</html>