<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exhibitor Information -
        {{ is_object($application) && isset($application->company_name) ? $application->company_name : 'Exhibitor' }}
    </title>
    <style>
        @page {
            size: 100mm 240mm;
            /* Custom page size */
            margin: 8mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 0.56rem;
            margin: 0;
            padding: 0;
        }

        .content {
            height: 230mm;
            /* Available content height */
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin: 0 auto;
            padding: 6px;
        }

        .exhibitor {
            height: 50%;
            /* Each exhibitor takes half of the page */
            page-break-inside: avoid;
            /* Keep each exhibitor on one page */
            border-top: 1px solid #ddd;
            padding-top: 12px;
        }

        .exhibitor1 {
            height: 50%;
            /* Each exhibitor takes half of the page */
            page-break-inside: avoid;
            /* Keep each exhibitor on one page */
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
            align-items: left;
        }

        th {
            padding: 0;
            margin: 0;
            /* padding: 2px 0px; */
            text-align: left;
            font-weight: bold;
            /* vertical-align: top; */
            align-items: left;
        }

        .front-page,
        .back-page {
            width: 100%;
            page-break-after: always;
        }

        .front-page img,
        .back-page img {
            width: 100%;
            height: 135%;
        }

        .header {
            padding-bottom: 10px;
        }

        .th-colon {
            /* remove any padding or margin */
            padding: 0;
            margin: 0;
            text-align: left;
            align-items: left;
        }

        .header img,
        .footer img {
            width: 100%;

        }

        .profile {
            line-height: 1.5;
            text-align: justify;
        }

        .page-number11 {
            position: fixed;
            bottom: 5px;
            /* Distance from the bottom of the page */
            left: 50%;
            /* Center horizontally */
            transform: translateX(-50%);
            /* Adjust for perfect centering */
            z-index: 1000;
            /* Ensure it appears on top of other elements */
            text-align: center;
        }
        tr {
            align-items: left;
            margin-top: 8px;
        }

        .page-number1 {
            text-align: center;
            display: block;
            margin-top: 40px;
        }

        /* Print-specific styles */
        @media print {
            @page {
                size: 100mm 240mm;
                margin: 8mm;
            }
            
            body {
                font-size: 0.56rem;
                line-height: 1.2;
                color: #000;
                background: #fff;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .content {
                height: 230mm;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
                margin: 0 auto;
                padding: 6px;
            }
            
            .exhibitor, .exhibitor1 {
                /* height: 50%; */
                page-break-inside: avoid;
                border-top: 1px solid #000;
                padding-top: 12px;
            }
            
            .exhibitor1 {
                padding-top: 13px;
            }
            
            h1 {
                font-size: 10px;
                text-align: center;
                margin: 0 0 5px 0;
                font-weight: bold;
            }
            
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
            }
            
            td, th {
                padding: 1px 2px;
                margin: 0;
                word-break: break-word;
                text-align: left;
                vertical-align: top;
                font-size: 0.56rem;
            }
            
            th {
                font-weight: bold;
                width: 30%;
            }
            
            .th-colon {
                width: 5%;
                padding: 0;
                margin: 0;
                text-align: left;
            }
            
            .profile {
                line-height: 1.3;
                text-align: justify;
                font-size: 0.56rem;
            }
            
            .header img {
                width: 100%;
                height: auto;
                max-height: 40mm;
            }
            
            .front-page, .back-page {
                width: 100%;
                page-break-after: always;
            }
            
            .front-page img, .back-page img {
                width: 100%;
                height: auto;
            }
            
            .page-number11 {
                position: fixed;
                bottom: 5px;
                left: 50%;
                transform: translateX(-50%);
                z-index: 1000;
                text-align: center;
                font-size: 0.5rem;
            }
            
            .page-number1 {
                text-align: center;
                display: block;
                margin-top: 40px;
                font-size: 0.5rem;
            }
            
            tr {
                align-items: left;
                margin-top: 2px;
            }
            
            /* Ensure proper spacing and avoid page breaks */
            .exhibitor:last-child {
                page-break-after: avoid;
            }
            
            /* Hide any elements that shouldn't print */
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    @php
        $companyName =
            is_object($application) && isset($application->company_name)
                ? strtoupper($application->company_name)
                : 'N/A';
        $isStartup =
            is_object($application) &&
            isset($application->assoc_mem) &&
            $application->assoc_mem === 'Startup Exhibitor';
        $cpTitle = $salutation ?? '';
        $cpFname = $firstName ?? '';
        $cpLname = $lastName ?? '';
        $designation = $exhibitorInfo->designation ?? 'N/A';
        $mobile = $exhibitorInfo->phone ?? 'N/A';
        $email = $exhibitorInfo->email ?? 'N/A';
        $address = trim(
            ($exhibitorInfo->address ?? '') .
                ', ' .
                ($exhibitorInfo->city ?? '') .
                ', ' .
                ($exhibitorInfo->state ?? '') .
                ', ' .
                ($exhibitorInfo->country ?? '') .
                ' ' .
                ($exhibitorInfo->zip_code ?? ''),
        );
        $website = $exhibitorInfo->website ?? 'N/A';
        $profile = $exhibitorInfo->description ?? 'N/A';
    @endphp
    <div class="content">
        <div class="header">
            <img src="https://bengalurutechsummit.com/exhibitor_directory_logo.png" alt="Header Image">
        </div>

        <div class="exhibitor1">
            <h1>{{ $companyName }}</h1>
            @if ($isStartup)
                <p style="text-align:center;"><em>(Startup)</em></p>
            @endif

            <table width="80%" style="margin: 0 auto;">
                <tr style="line-height: 180%;">
                    <th width="31%"  align="right" valign="top">Contact Person </th>
                    <th width="2%" valign="top" class="th-colon">:</th>

                    <td width="77%" align="left" valign="top">{{ trim($cpTitle . ' ' . $cpFname . ' ' . $cpLname) }}</td>
      </tr>
                <tr style="line-height: 180%;">
                    <th  align="right" valign="top">Designation</th>
                    <th valign="top" class="th-colon">:</th>
                    <td align="left" valign="top">{{ $designation }}</td>
      </tr>
                <tr style="line-height: 180%;">
                    <th  align="right" valign="top">Mobile</th>
                    <th valign="top" class="th-colon">:</th>
                    <td align="left" valign="top">{{ $mobile }}</td>
      </tr>
                <tr style="line-height: 180%;">
                    <th  align="right" valign="top">E-mail</th>
                    <th valign="top" class="th-colon">:</th>
                    <td align="left" valign="top">{{ $email }}</td>
      </tr>
                <tr style="line-height: 180%;">
                    <th  align="right" valign="top">Address</th>
                    <th valign="top" class="th-colon">:</th>
                    <td align="left" valign="top">{{ $address }}</td>
      </tr>
                <tr style="line-height: 180%;">
                    <th  align="right" valign="top">Website</th>
                    <th valign="top" class="th-colon">:</th>
                    <td align="left" valign="top">{{ $website }}</td>
      </tr>

                <tr style="line-height: 180%;">
                    <th  align="right" valign="top"><br>Profile:</th>
                    <th align="left" valign="top"> </th>
      </tr>
                <tr style="line-height: 180%;">
                    <td colspan="3" class="profile">{{ $profile }}</td>
                </tr>
</table>
            
        </div>

        {{-- <span class="page-number1">1</span> --}}
    </div>
</body>

</html>
