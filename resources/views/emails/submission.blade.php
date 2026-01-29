<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Application Confirmation - {{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}</title>
    <link href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Inter', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 700px;
            margin: 40px auto;
            background-color: #ffffff;
            border: 1px solid #e5e5e5;
            padding: 40px;
            border-radius: 8px;
            text-align: center;
        }
        .email-header img {
            max-width: 150px;
        }
        .email-body h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .email-body p {
            color: #555;
            font-size: 16px;
            margin: 10px 0;
        }
        .email-body strong {
            color: #333;
        }
        .cta-button {
            display: inline-block;
            background-color: #0073e6;
            color: #ffffff;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 20px;
        }
        .email-footer {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .email-footer a {
            color: #0073e6;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="email-container">
    <!-- Email Header -->
    <div class="email-header">
        <img src="https://interlinx.in/logo.svg" alt="{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}">
        <br>
        <span style="font-size:14px; color:#333;">{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}</span>
    </div>

    <!-- Email Body -->
    <div class="email-body">
        <h3>Dear {{$data['firstName']}} {{$data['lastName']}},</h3>
        <p>Thank you for submitting your application for <strong>{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}</strong>!</p>
        <p><strong>Application ID:</strong>{{$data['applicationID']}}</p>
        <p><strong>Submission Date:</strong> {{$data['submissionDate']}}</p>

        <p>Your application is currently under review. The review process will take a minimum of <strong>7 working days</strong> from the date of submission. We will notify you about the next steps once the review process is complete.</p>

        <!-- Call-to-Action Button -->
        <a href="#" class="cta-button">
            Track Your Application by login into your account
        </a>

        <p class="email-footer">If you have any questions, feel free to reach out to us at:</p>
        <p><a href="mailto:{{ config('constants')['organizer']['email'] }}">{{ config('constants')['organizer']['email'] }}</a></p>

        <p style="color:#333; font-size:14px;">Best regards,</p>
        <p style="color:#333; font-weight:600;">{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }} Team</p>
    </div>
</div>

</body>
</html>
