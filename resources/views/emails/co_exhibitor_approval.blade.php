<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Co-Exhibitor Account Approved</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
<table cellpadding="0" cellspacing="0" width="100%" style="width: 100%; max-width: 600px; margin: 20px auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
    <tr>
        <td align="center" style="text-align: center; padding-bottom: 20px; border-bottom: 2px solid #eeeeee;">
            <img src="https://www.mmactiv.in/images/semicon_logo.png" alt="Event Logo" style="max-width: 150px;">
        </td>
    </tr>
    <tr>
        <td style="padding: 20px 0; font-size: 16px; color: #333333;">
            <p style="margin: 6px 0; color: #555;">Dear {{ $coExhibitor->contact_person }},</p>
            <p style="margin: 6px 0; color: #555;">We are pleased to inform you that your Co-Exhibitor account has been successfully approved! You are invited to exhibit under {{ $coExhibitor->application->company_name }}.</p>
            <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Login Details:</strong></p>
            <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Website:</strong> <a href="{{ url('/login') }}" target="_blank" style="color: #007bff; text-decoration: none;">Login Here</a></p>
            <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Email:</strong> {{ $coExhibitor->email }}</p>
            <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Password:</strong> {{ $password }}</p>
            <p style="margin: 6px 0; color: #555;"><em>For security reasons, please log in and change your password immediately.</em></p>
        </td>
    </tr>
    <tr>
        <td align="center" style="text-align: center; font-size: 14px; color: #777777; padding-top: 20px; border-top: 2px solid #eeeeee;">
            <p style="margin: 6px 0;">Best Regards,<br><strong>{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }} Team</strong></p>
        </td>
    </tr>
    <tr>
        <td align="left" style="text-align: left; padding-top: 5px; font-size: 14px; color: #555555;">
            <table cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="vertical-align: top; padding-right: 20px;">
                        <img src="https://www.mmactiv.in/images/mma.jpg" alt="Organizer Logo" style="max-width: 150px; margin-bottom: 10px;">
                    </td>
                    <td style="vertical-align: top;">
                        <p style="margin: 6px 0; color: #555;">
                            <strong style="color: #111;">Organizer Details:</strong><br>
                            MM Activ Sci-Tech Communications Pvt. Ltd.<br>
                            103-104, Rohit House, 3, Tolstoy Marg, Connaught Place,<br>
                            New Delhi - 110 001<br>
                            Tel: 011-4354 2737 / 011-2331 9387<br>
                            Fax: +91-11-2331 9388
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
