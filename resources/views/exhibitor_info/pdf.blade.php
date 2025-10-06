<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exhibitor Information - {{ is_object($application) && isset($application->company_name) ? $application->company_name : 'Exhibitor' }}</title>
    <style>
        @page {
            size: 100mm 240mm; /* Custom page size */
            margin: 8mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 0.56rem;
            margin: 0;
            padding: 0;
        }
        
        .content {
            height: 230mm; /* Available content height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin: 0 auto;
            padding: 6px;
        }
        .exhibitor {
            height: 50%; /* Each exhibitor takes half of the page */
            page-break-inside: avoid; /* Keep each exhibitor on one page */
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }
        .exhibitor1 {
            height: 50%; /* Each exhibitor takes half of the page */
            page-break-inside: avoid; /* Keep each exhibitor on one page */
            padding-top: 13px;
        }
        h1 {
            font-size: 10px;
            text-align: center;
            margin: 0 0 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 0;
        margin: 0;
            /* padding: 2px 4px; */
            word-break: break-word;
            /* vertical-align: top; */
            text-align: left;
        }
        th {
            padding: 0;
        margin: 0;
            /* padding: 2px 0px; */
            text-align: left;
            font-weight: bold;
            /* vertical-align: top; */
            text-align: left;
        }
        .front-page, .back-page {
        width: 100%;
        page-break-after: always;
    }
    .front-page img, .back-page img {
        width: 100%;
        height: 135%;
    }
    .header{
        padding-bottom: 10px;
    }
    .th-colon{
        /* remove any padding or margin */
        padding: 0;
        margin: 0;
        text-align: center;
    }
    .header img, .footer img {
        width: 100%;
        
    }
    .profile{
    line-height: 1.5;
    text-align: justify;
    }
    .page-number11 {
            position: fixed;
            bottom: 5px; /* Distance from the bottom of the page */
            left: 50%; /* Center horizontally */
            transform: translateX(-50%); /* Adjust for perfect centering */
            z-index: 1000; /* Ensure it appears on top of other elements */
            text-align: center;
        }
        .page-number1{
        text-align:center;
        display:block;
        margin-top:40px;


        }
    </style>
</head>
<body>
    @php
        $companyName = is_object($application) && isset($application->company_name) ? strtoupper($application->company_name) : 'N/A';
        $isStartup = is_object($application) && isset($application->assoc_mem) && $application->assoc_mem === 'Startup Exhibitor';
        $cpTitle = $salutation ?? '';
        $cpFname = $firstName ?? '';
        $cpLname = $lastName ?? '';
        $designation = $exhibitorInfo->designation ?? 'N/A';
        $mobile = $exhibitorInfo->phone ?? 'N/A';
        $email = $exhibitorInfo->email ?? 'N/A';
        $address = trim(($exhibitorInfo->address ?? '') . ', ' . ($exhibitorInfo->city ?? '') . ', ' . ($exhibitorInfo->state ?? '') . ', ' . ($exhibitorInfo->country ?? '') . ' ' . ($exhibitorInfo->zip_code ?? ''));
        $website = $exhibitorInfo->website ?? 'N/A';
        $profile = $exhibitorInfo->description ?? 'N/A';
    @endphp
    <div class="content">
        <div class="header">
            <img src="https://bengalurutechsummit.com/exhibitor_directory_logo.png" alt="Header Image">
        </div>

        <div class="exhibitor1">
            <h1>{{ $companyName }}</h1>
            @if($isStartup)
                <p style="text-align:center;"><em>(Startup)</em></p>
            @endif

            <table>
                <tr><th>Contact </th>
                    <th class="th-colon">:</th>
                    
                    <td>{{ trim($cpTitle . ' ' . $cpFname . ' ' . $cpLname) }}</td></tr>
                <tr><th>Designation</th>
                    <th class="th-colon">:</th>
                    <td>{{ $designation }}</td></tr>
                <tr><th>Mobile</th>
                    <th class="th-colon">:</th>
                    <td>{{ $mobile }}</td></tr>
                <tr><th>E-mail</th>
                    <th class="th-colon">:</th>
                    <td>{{ $email }}</td></tr>
                <tr><th>Address</th>
                    <th class="th-colon">:</th>
                    <td>{{ $address }}</td></tr>
                <tr><th>Website</th>
                    <th class="th-colon">:</th>
                    <td>{{ $website }}</td></tr>
                
                <tr><th><br>Profile:</th>
                    <th> </th>
                </tr>
                <tr><td colspan="3" class="profile">{{ $profile }}</td></tr>
            </table>
        </div>

        {{-- <span class="page-number1">1</span> --}}
    </div>
</body>
</html>
