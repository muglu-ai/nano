<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Co-Exhibitor Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }
        .email-container {
            background: #fff;
            padding: 30px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            max-width: 600px;
            margin: auto;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        p {
            margin: 6px 0;
            color: #555;
        }
        strong {
            color: #111;
        }
        .button {
            margin-top: 20px;
            display: inline-block;
            background-color: #007bff;
            color: #fff !important;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <h2>New Co-Exhibitor Application Submitted</h2>

        <p><strong>Company Name:</strong> {{ $coExhibitor->co_exhibitor_name }}</p>
        <p><strong>Contact Person:</strong> {{ $coExhibitor->contact_person }}</p>
        <p><strong>Job Title:</strong> {{ $coExhibitor->job_title ?? 'N/A' }}</p>
        <p><strong>Email:</strong> {{ $coExhibitor->email }}</p>
        <p><strong>Phone:</strong> {{ $coExhibitor->phone }}</p>

        @if($coExhibitor->proof_document)
        <p><strong>Proof Document:</strong>
            <a href="{{ asset($coExhibitor->proof_document) }}" target="_blank">View Document</a>
        </p>
        @endif

        <p><strong>Status:</strong> {{ ucfirst($coExhibitor->status) }}</p>

        <a href="{{ config('app.url') }}" class="button">Go to Dashboard</a>

        <div class="footer">
            Thank you,<br>
            {{ config('app.name') }} Team
        </div>
    </div>
</body>
</html>
