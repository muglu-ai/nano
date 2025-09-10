<!-- resources/views/mail/attendee_approval.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Inaugural Session Status</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333; background: #f8f9fa; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: white; border-radius: 8px; padding: 30px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
        <p>Dear {{ $data['name'] }},</p>

        <p>Your application for the Inaugural Session has been <strong> Approved  </strong>.</p>

        <p>Your Unique ID: <strong>{{ $data['unique_id'] }}</strong></p>

        
            <p>If you have any questions, please contact the event team.</p>

        <p>Best regards,<br>SEMICON India Team</p>
    </div>
</body>
</html>
