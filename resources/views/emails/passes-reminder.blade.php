<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title> {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</title>
</head>
<body>
    {!! nl2br(e($emailBody)) !!}
</body>
</html>
