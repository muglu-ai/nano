<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Co-Exhibitor Account Approved</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 2px solid #eeeeee;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            padding: 20px 0;
            font-size: 16px;
            color: #333333;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #777777;
            padding-top: 20px;
            border-top: 2px solid #eeeeee;
        }
        .organiser {
            text-align: left;
            padding-top: 5px;
            font-size: 14px;
            color: #555555;
        }
        .organiser img {
            max-width: 150px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<table class="container" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="header" align="center">
            <img src="https://www.mmactiv.in/images/semicon_logo.png" alt="Event Logo">
        </td>
    </tr>
    <tr>
        <td class="content">
            <p>Dear {{ $coExhibitor->contact_person }},</p>
            <p>We are pleased to inform you that your Co-Exhibitor account has been successfully approved! You are invited to exhibit under {{ $coExhibitor->application->company_name }}.</p>
            <p><strong>Login Details:</strong></p>
            <p><strong>Website:</strong> <a href="{{ url('/login') }}" target="_blank">Login Here</a></p>
            <p><strong>Email:</strong> {{ $coExhibitor->email }}</p>
            <p><strong>Password:</strong> {{ $password }}</p>
            <p><em>For security reasons, please log in and change your password immediately.</em></p>
        </td>
    </tr>
    <tr>
        <td class="footer" align="center">
            <p>Best Regards,<br><strong>{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }} Team</strong></p>
        </td>
    </tr>
    <tr>
        <td class="organiser" align="left" style="display: flex; align-items: center;">
            <img src="https://www.mmactiv.in/images/mma.jpg" alt="Organizer Logo" style="margin-right: 20px;">
            <p>
                <strong>Organizer Details:</strong><br>
                MM Activ Sci-Tech Communications Pvt. Ltd.<br>
                103-104, Rohit House, 3, Tolstoy Marg, Connaught Place,<br>
                New Delhi - 110 001<br>
                Tel: 011-4354 2737 / 011-2331 9387<br>
                Fax: +91-11-2331 9388
            </p>
        </td>
    </tr>
</table>
</body>
</html>
