<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Application Confirmation - {{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}</title>
</head>
<body style="background-color: #f5f5f5; font-family: 'Inter', Arial, sans-serif; margin: 0; padding: 0;">

<div style="max-width: 700px; margin: 40px auto; background-color: #ffffff; border: 1px solid #e5e5e5; padding: 40px; border-radius: 8px; text-align: center;">
    <!-- Email Header -->
    <div style="margin-bottom: 20px;">
        <img src="https://interlinx.in/logo.svg" alt="{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}" style="max-width: 150px;">
        <br>
        <span style="font-size:14px; color:#333;">{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}</span>
    </div>

    <!-- Email Body -->
    <div>
        <h3 style="color: #333; margin-bottom: 10px;">Dear {{$data['firstName']}} {{$data['lastName']}},</h3>
        <p style="color: #555; font-size: 16px; margin: 10px 0;">Thank you for submitting your application for <strong style="color: #333;">{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}</strong>!</p>
        <p style="color: #555; font-size: 16px; margin: 10px 0;"><strong style="color: #333;">Application ID:</strong>{{$data['applicationID']}}</p>
        <p style="color: #555; font-size: 16px; margin: 10px 0;"><strong style="color: #333;">Submission Date:</strong> {{$data['submissionDate']}}</p>

        <p style="color: #555; font-size: 16px; margin: 10px 0;">Your application is currently under review. The review process will take a minimum of <strong style="color: #333;">7 working days</strong> from the date of submission. We will notify you about the next steps once the review process is complete.</p>

        <!-- Call-to-Action Button -->
        <a href="#" style="display: inline-block; background-color: #0073e6; color: #ffffff; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: 600; font-size: 14px; margin-top: 20px;">
            Track Your Application by login into your account
        </a>

        <p style="margin-top: 20px; font-size: 14px; color: #555;">If you have any questions, feel free to reach out to us at:</p>
        <p style="color: #555; font-size: 16px; margin: 10px 0;"><a href="mailto:{{ config('constants')['organizer']['email'] }}" style="color: #0073e6; text-decoration: none;">{{ config('constants')['organizer']['email'] }}</a></p>

        <p style="color:#333; font-size:14px; margin-top: 20px;">Best regards,</p>
        <p style="color:#333; font-weight:600; margin: 5px 0;">{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }} Team</p>
    </div>
</div>

</body>
</html>
