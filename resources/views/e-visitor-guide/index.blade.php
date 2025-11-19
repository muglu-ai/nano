<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('constants.EVENT_NAME') }} - Exhibitor Directory</title>

    <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />

    <!-- Bootstrap 5 -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">

    <!-- Flipbook StyleSheets -->
    <link href="{{ asset('public/dflip/css/dflip.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('public/dflip/css/themify-icons.min.css') }}" rel="stylesheet" type="text/css">

    <style>
        body {
            background: radial-gradient(circle at top, #f8fbff 0%, #e8f0ff 50%, #fdfdff 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2a44;
        }

        .bts-header {
            padding: 3rem 1rem 1rem;
            text-align: center;
        }

        .bts-header img {
            max-height: 90px;
        }

        .bts-header h1 {
            margin-top: 1rem;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .flipbook-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem 4rem;
        }

        .flipbook-card {
            background: #ffffffcc;
            border-radius: 20px;
            box-shadow: 0 20px 45px rgba(31, 42, 68, 0.12);
            padding: 1.5rem;
            border: 1px solid rgba(117, 135, 183, 0.2);
        }

        ._df_book {
            min-height: 700px;
        }

        footer {
            text-align: center;
            padding: 1rem 0 2rem;
            color: #6c7a99;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="bts-header">
        <img src="https://bengalurutechsummit.com/img/logo-BTS-25-N.png" alt="{{ config('constants.EVENT_NAME') }} Logo" class="img-fluid">
        <h1>{{ config('constants.EVENT_NAME') }} Exhibitor Directory</h1>
        <p class="text-muted mb-0">Immerse yourself in the Exhibitor Directory and explore the full exhibitors list.</p>
    </div>

    <div class="flipbook-wrapper">
        <div class="flipbook-card">
            <div class="_df_book" id="flipbok_example" source="{{ asset('assets/docs/BTS-2025_Exhibitor-Directory.pdf') }}"></div>
        </div>
    </div>

    <footer>
        &copy; {{ date('Y') }} {{ config('constants.EVENT_NAME') }}. All rights reserved.
    </footer>

    <!-- Scripts -->
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="{{ asset('public/dflip/js/libs/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('public/dflip/js/dflip.min.js') }}" type="text/javascript"></script>
    <script>
        $(document).ready(function () {
            $('#flipbok_example').flipBook({
                maxVisiblePages: 2
            });
        });
    </script>
</body>
</html>