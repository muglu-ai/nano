@php
$eventName = config('constants.EVENT_NAME', 'Event');
$eventYear = config('constants.EVENT_YEAR', date('Y'));
$supportEmail = config('constants.SUPPORT_EMAIL', 'support@example.com');
$typeColors = [
    'info' => '#004aad',
    'warning' => '#ffc107',
    'important' => '#dc3545',
];
$typeColor = $typeColors[$notification->type] ?? '#004aad';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $notification->title }} - {{ $eventName }} {{ $eventYear }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: #e9ecef;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
            border: 1px solid #e0e0e0;
            overflow: hidden;
        }
        .header {
            padding: 24px 32px;
            background: {{ $typeColor }};
            color: #fff;
        }
        .content {
            padding: 32px 32px 24px 32px;
        }
        .footer {
            padding: 24px 32px;
            background: #f8f9fa;
            text-align: center;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2 style="margin: 0; color: #fff;">{{ $notification->title }}</h2>
        </div>
        <div class="content">
            @if($recipientName)
            <p style="font-size: 16px; color: #333; margin-bottom: 10px;">
                Hello {{ $recipientName }},
            </p>
            @endif
            <div style="font-size: 16px; color: #333; line-height: 1.6;">
                {!! nl2br(e($notification->message)) !!}
            </div>
        </div>
        <div class="footer">
            Warm Regards,<br>
            <strong>{{ $eventName }} {{ $eventYear }} Team</strong><br>
            <a href="mailto:{{ $supportEmail }}" style="color: #004aad;">{{ $supportEmail }}</a>
        </div>
    </div>
</body>
</html>
