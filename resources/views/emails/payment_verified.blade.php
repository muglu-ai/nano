<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Payment Has Been Verified</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333333;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-collapse: collapse;">
        <!-- Logo -->
        <tr>
            <td style="padding: 20px; text-align: center; background-color: #ffffff;">
                <img src="https://portal.semiconindia.org/asset/img/logos/logo.png" alt="SEMICON India Logo" style="max-width: 150px;">
                <h2 style="margin: 10px 0 0 0; font-size: 20px; color: #333333;">SEMICON India 2025</h2>
            </td>
        </tr>
        
        <!-- Header -->
        <tr>
            <td style="padding: 30px 30px 20px 30px; text-align: center; background-color: #0056b3;">
                <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: bold;">Your Payment Has Been Verified</h1>
            </td>
        </tr>
        
        <!-- Success Icon -->
        <tr>
            <td style="padding: 30px 30px 0 30px; text-align: center;">
                <div style="display: inline-block; width: 60px; height: 60px; background-color: #4CAF50; border-radius: 50%; margin-bottom: 20px;">
                    <div style="font-size: 40px; color: white; line-height: 60px;">✓</div>
                </div>
            </td>
        </tr>
        
        <!-- Content -->
        <tr>
            <td style="padding: 20px 30px 30px 30px;">
                <p style="margin: 0 0 20px 0; font-size: 16px; line-height: 24px; color: #333333;">
                    We are pleased to inform you that your payment for <strong>Stall Booking</strong> has been successfully verified.
                </p>
                <p style="margin: 0 0 30px 0; font-size: 16px; line-height: 24px; color: #333333;">
                    Please log in to your exhibitor portal to manage your stall.
                </p>
                <p style="margin: 0; text-align: center;">
                    <a href="https://portal.semiconindia.org/login" style="display: inline-block; padding: 12px 24px; background-color: #0056b3; color: #ffffff; text-decoration: none; font-weight: bold; border-radius: 4px;">Log in to Exhibitor Portal</a>
                </p>
            </td>
        </tr>
        
        <!-- Footer -->
        <tr>
            <td style="padding: 20px 30px; text-align: center; background-color: #f8f8f8; font-size: 14px; color: #666666; border-top: 1px solid #dddddd;">
                <p style="margin: 0 0 10px 0;">If you have any questions, please email to <a href="mailto:{{ config('constants')['organizer']['email'] }}">{{ config('constants')['organizer']['email'] }}</a>. </p>
            </td>
        </tr>
    </table>
</body>
</html>
