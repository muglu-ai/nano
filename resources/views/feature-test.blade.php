{{-- Create a view to test out the blade-flags --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0
">
    <title>Feature Test</title>
</head>
<body>
@php
    $country = new stdClass();
$country->code = 'in'; // Example country code
 @endphp
    <h1>Feature Test</h1>
    <img src="{{ asset('vendor/blade-flags/country-'.$country->code.'.svg') }}" width="32" height="32" alt="Country Flag"/>

    <img src="{{ asset('vendor/blade-flags/country-ca.svg') }}" width="32" height="32"/>
</body>
</html>