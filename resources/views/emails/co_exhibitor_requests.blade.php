<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Co-Exhibitor Application</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px;">
    <div style="background: #fff; padding: 30px; border-radius: 6px; box-shadow: 0 0 10px rgba(0,0,0,0.05); max-width: 600px; margin: auto;">

    <div style="text-align: center; margin-bottom: 24px;">
        <img src="https://interlinx.in/logo.svg" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">
        <div style="font-size: 22px; font-weight: bold; color: #222;">{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}</div>
    </div>
        <h2 style="color: #333; margin-bottom: 20px;">New Co-Exhibitor Application Submitted</h2>
        <table style="width:100%; border-collapse:collapse;">

        <tr>
            <td style="padding:8px 0; color:#555;"><strong style="color:#111;">Applicant Name:</strong></td>
            <td style="padding:8px 0; color:#555;">{{ $coExhibitor['company_name'] }}</td>
        </tr>
            <tr>
                <td style="padding:8px 0; color:#555;"><strong style="color:#111;">Co-Exhibitor Company Name:</strong></td>
                <td style="padding:8px 0; color:#555;">{{ $coExhibitor['co_exhibitor_name'] }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#555;"><strong style="color:#111;">Contact Person:</strong></td>
                <td style="padding:8px 0; color:#555;">{{ $coExhibitor['contact_person'] }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#555;"><strong style="color:#111;">Job Title:</strong></td>
                <td style="padding:8px 0; color:#555;">{{ $coExhibitor['job_title'] ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#555;"><strong style="color:#111;">Email:</strong></td>
                <td style="padding:8px 0; color:#555;">{{ $coExhibitor['email'] }}</td>
            </tr>
            <tr>
                <td style="padding:8px 0; color:#555;"><strong style="color:#111;">Phone:</strong></td>
                <td style="padding:8px 0; color:#555;">{{ $coExhibitor['phone'] }}</td>
            </tr>
            @if(!empty($coExhibitor['proof_document']))
            <tr>
                <td style="padding:8px 0; color:#555;"><strong style="color:#111;">Proof Document:</strong></td>
                <td style="padding:8px 0; color:#555;">
                    <a href="{{ asset($coExhibitor['proof_document']) }}" target="_blank" style="color:#007bff; text-decoration:underline;">View Document</a>
                </td>
            </tr>
            @endif
            <tr>
                <td style="padding:8px 0; color:#555;"><strong style="color:#111;">Status:</strong></td>
                <td style="padding:8px 0; color:#555;">{{ ucfirst($coExhibitor['status'] ?? '') }}</td>
            </tr>
        </table>
        <!-- <a href="{{ config('app.url') }}" style="margin-top:20px; display:inline-block; background-color:#007bff; color:#fff !important; padding:10px 20px; border-radius:5px; text-decoration:none;">Go to Dashboard</a> -->
        <div style="margin-top:30px; font-size:12px; color:#aaa;">
            Thank you,<br>
            {{ config('app.name') }} Team
        </div>
    </div>
</body>
</html>
