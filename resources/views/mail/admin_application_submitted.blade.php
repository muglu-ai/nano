<!DOCTYPE html>
<html>
<head>
    <title>Application Submitted Successfully</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 150px;
        }
        .content {
            font-size: 16px;
            color: #333;
            line-height: 1.6;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
<div class="email-container">
    <div class="header" style="text-align: center;">
        <img src="https://www.mmactiv.in/images/semicon_logo.png" alt="SEMICON India 2025" style="max-width: 150px; display: block; margin: 0 auto 0px;">
        <span class="header-text" style="font-size: 15px; font-weight: normal; color:rgb(15, 15, 15); display: block;">SEMICON India 2025</span>
    </div>
    <div class="content">
        <p style="margin: 0 0 15px;">Hello Admin,</p>

        <p style="margin: 0 0 15px;">A new application has been submitted.</p>
        <p><strong>Company Name:</strong> <span style="color: #007bff;">{{ $application->billingDetail->billing_company }}</span></p>
        <p style="margin: 20px 0;">Please log in to the admin panel to review the details.</p>

    </div>
    <div class="footer">
        <p>Best Regards,</p>
        <p><strong>SEMICON India 2025</strong></p>
        <p><a href="https://www.semiconindia.org/" style="color: #007bff; text-decoration: none;">https://www.semiconindia.org/</a></p>
    </div>
</div>
</body>
</html>
