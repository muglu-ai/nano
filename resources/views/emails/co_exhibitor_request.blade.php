<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Co-Exhibitor Application</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; margin: 0;">
    <div style="background: #fff; padding: 30px; border-radius: 6px; box-shadow: 0 0 10px rgba(0,0,0,0.05); max-width: 600px; margin: auto;">
        <h2 style="color: #333; margin-bottom: 20px;">New Co-Exhibitor Application Submitted</h2>

        <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Company Name:</strong> {{ $coExhibitor->co_exhibitor_name }}</p>
        <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Contact Person:</strong> {{ $coExhibitor->contact_person }}</p>
        <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Job Title:</strong> {{ $coExhibitor->job_title ?? 'N/A' }}</p>
        <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Email:</strong> {{ $coExhibitor->email }}</p>
        <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Phone:</strong> {{ $coExhibitor->phone }}</p>

        @if($coExhibitor->proof_document)
        <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Proof Document:</strong>
            <a href="{{ asset($coExhibitor->proof_document) }}" target="_blank" style="color: #007bff; text-decoration: none;">View Document</a>
        </p>
        @endif

        <p style="margin: 6px 0; color: #555;"><strong style="color: #111;">Status:</strong> {{ ucfirst($coExhibitor->status) }}</p>

        <a href="{{ config('app.url') }}" style="margin-top: 20px; display: inline-block; background-color: #007bff; color: #fff; padding: 10px 20px; border-radius: 5px; text-decoration: none;">Go to Dashboard</a>

        <div style="margin-top: 30px; font-size: 12px; color: #aaa;">
            Thank you,<br>
            {{ config('app.name') }} Team
        </div>
    </div>
</body>
</html>
