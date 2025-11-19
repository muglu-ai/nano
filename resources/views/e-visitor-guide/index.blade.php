<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
 <link rel="apple-touch-icon" sizes="76x76" href="../../assets/img/apple-icon.png">
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png"
        type="image/vnd.microsoft.icon" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ config('constants.EVENT_NAME') }} - Exhibitor Directory</title>

<!-- Flipbook StyleSheets -->
<link href="public/dflip/css/dflip.min.css" rel="stylesheet" type="text/css">
<!-- themify-icons.min.css is not required in version 2.0 and above -->
<link href="public/dflip/css/themify-icons.min.css" rel="stylesheet" type="text/css">

</head>
<body>
<div class="_df_book" id="flipbok_example" source="{{ asset('public/assets/docs/BTS-2025_Exhibitor-Directory.pdf') }}"></div>

<!-- Scripts -->
<script src="dflip/js/libs/jquery.min.js" type="text/javascript"></script>
<script src="dflip/js/dflip.min.js" type="text/javascript"></script>
<script>
    $(document).ready(function() {
        $('#flipbok_example').flipBook({
            maxVisiblePages: 2
        });
    });
</script>
</body>
</html>