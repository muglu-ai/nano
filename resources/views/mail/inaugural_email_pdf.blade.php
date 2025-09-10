<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SEMICON India 2025 Visitor Registration</title>
    <style>
        @page {
        margin-top: 0px;
        margin-bottom: 0px;
    }

    body {
        margin-top: 0px;
        margin-bottom: 0px;
    }
        /* Using @font-face is tricky in dompdf without local font files, so we stick to web-safe fonts */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 8pt;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 740px;
            /* A4 width is ~794px at 96dpi, leaving room for margins */
            margin: 20px auto;
        }

        .header-table {
            width: 100%;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: middle;
            text-align: center;
        }

        .header-table .logo-left {
            text-align: left;
        }

        .header-table .logo-right {
            text-align: right;
        }

        .logo1 {
            height: 40px;
        }

        .logo2 {
            height: 40px;
        }

        .logo3 {
            height: 40px;
        }
        .logo4 {
            height: 40px;
        }

        .title-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .title-section h2 {
            font-weight: bold;
            font-size: 13pt;
            margin: 0;
        }

        .title-section p {
            font-size: 12pt;
            margin: 5px 0;
        }

        .main-paragraph {
            text-align: justify;
            margin-bottom: 20px;
        }

        .event-details {
            border: 1px solid #333;
            padding: 5px;
            margin: 10px 0;
        }

        .event-details-title {
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .event-table {
            width: 100%;
        }

        .event-table td {
            vertical-align: top;
            padding: 5px 0;
            font-size: 9pt;
        }

        .event-table .icon {
            font-family: 'DejaVu Sans', sans-serif;
            /* Recommended for better unicode support in dompdf */
            font-size: 20px;
            width: 40px;
            text-align: center;
            line-height: 1;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .details-table th,
        .details-table td {
            border: 1px solid black;
            padding: 6px 8px;
            text-align: left;
            font-size: 8pt;
            word-wrap: break-word;
        }

        .details-table th {
            background-color: #002060;
            color: white;
            font-weight: bold;
        }

        .details-table td {
            height: 20px;
        }

        .section-heading {
            font-weight: bold;
            font-size: 12pt;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .live-streaming-table {
            width: 100%;
        }

        .live-streaming-table td {
            vertical-align: middle;
            padding: 2px 0;
        }

        .checkbox-box {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            text-align: right;
            padding-right: 10px;
        }

        .note-box {
            padding: 10px;
            margin-top: 8px;
            color: white;
            background-color: #002060;
            font-size: 7pt;
            text-align: justify;
        }

        .note-box.grey {
            background-color: #f2f2f2;
            color: black;
            border: 1px solid #e0e0e0;
        }

        a {
            color: #008080;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header with Logos -->
        <table class="header-table">
            <tr>
                
                <td  style="width: 25%;">
                    <img class="logo1"
                         src="{{ asset('/asset/img/logos/meity-logo.png') }}"
                         alt="Ministry of Electronics & IT Logo">
                </td>
                <td style="width: 25%;">
                    <img class="logo2"
                         src="{{ asset('/asset/img/logos/ism_logo.png') }}"
                         alt="ISM Logo">
                </td>
                
                <td style="width: 25%;">
                    <img class="logo4"
                         src="{{ asset('/asset/img/logos/DIC_Logo.webp') }}"
                         alt="SEMICON India Logo">
                </td>
                <td class="logo-left" style="width: 25%;">
                    <img class="logo3"
                         src="{{ asset('/asset/img/logos/SEMI_IESA_logo.png') }}"
                         alt="SEMI IESA Logo">
                </td>
                
            </tr>
        </table>

        <!-- Title Section -->
        <div class="title-section">
            <h2>SEMICON India 2025: Exhibitor Registration Slip</h2>
            <p>ID: {{ $data['unique_id'] }}</p>
        </div>

        <!-- Thank you message -->
        <p class="main-paragraph">
           Thank you for filling out the exhibitor badge form.
        </p>
        <p class="main-paragraph">
           This registration acknowledgement copy can be attached as a supporting document while applying for VISA.
        </p>

        <!-- Event Details -->
         {{--
        <div class="event-details">
            <table class="event-table">
                <tr>
                    <td class="icon"><img src="{{ asset('/mails/calendar-icon.png')}}" width="20"
                            height="20"></td> <!-- Calendar Icon -->
                    <td> 2<sup>nd</sup> September - 4<sup>th</sup> September 2025</td>
                </tr>
                <tr>
                    <td class="icon"><img src="{{ asset('/mails/clock-icon.png')}}" width="20" height="20">
                    
                    </td> <!-- Clock Icon -->

                    <td>
                        Visiting hours: 
                        <table style="width:100%; border:none;">
                            <tr>
                                <td colspan="2" style="padding:0;">
                                    <table style="width:100%; border-collapse:collapse; font-size:9pt;">
                                        <tr>
                                            <th style="border:1px solid #bbb; background:#f6f6f6; text-align:center; font-weight:bold; padding:4px 2px;"></th>
                                            <th style="border:1px solid #bbb; background:#f6f6f6; text-align:center; font-weight:bold; padding:4px 8px;">
                                                2<sup>nd</sup> Sep
                                            </th>
                                            <th style="border:1px solid #bbb; background:#f6f6f6; text-align:center; font-weight:bold; padding:4px 8px;">
                                                3<sup>rd</sup> Sep
                                            </th>
                                            <th style="border:1px solid #bbb; background:#f6f6f6; text-align:center; font-weight:bold; padding:4px 8px;">
                                                4<sup>th</sup> Sep
                                            </th>
                                        </tr>
                                        <tr>
                                            <td style="border:1px solid #bbb; font-weight:500; padding:4px 8px;">Exhibition</td>
                                            <td style="border:1px solid #bbb; text-align:center; padding:4px 8px;">10:00 AM – 6:00 PM</td>
                                            <td style="border:1px solid #bbb; text-align:center; padding:4px 8px;">10:00 AM – 6:00 PM</td>
                                            <td style="border:1px solid #bbb; text-align:center; padding:4px 8px;">10:00 AM – 5:00 PM</td>
                                        </tr>
                                        <tr>
                                            <td style="border:1px solid #bbb; font-weight:500; padding:4px 8px;">Conference</td>
                                            <td style="border:1px solid #bbb; text-align:center; padding:4px 8px;">9:00 AM – 6:00 PM</td>
                                            <td style="border:1px solid #bbb; text-align:center; padding:4px 8px;">9:00 AM – 6:00 PM</td>
                                            <td style="border:1px solid #bbb; text-align:center; padding:4px 8px;">9:00 AM – 6:00 PM</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="icon"><img src="https://portal.semiconindia.org/mails/location-icon.png" width="20"
                        height="20"></td> <!-- Location Pin Icon -->
                    <td>Yashobhoomi (IICC), New Delhi</td>
                </tr>
                <tr>
                    <td class="icon"><img src="{{ asset('/mails/globe-icon.png')}}" width="20"
                            height="20"></td> <!-- Link Icon -->
                    <td><a href="http://www.semiconindia.org">www.semiconindia.org</a></td>
                </tr>
            </table>
        </div>
--}}
        <!-- Participant and Registration Details Table -->
        <table class="details-table">
            <thead>
                <tr>
                    <th colspan="2" style="text-align: center;">Participant Details:</th>
                    <th colspan="2" style="text-align: center;">Registration Details:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 25%;">Name</td>
                    <td style="width: 35%;">{{ $data['name'] ?? '-' }}</td>
                   <td>Registration No:</td>
                    <td>{{ $data['unique_id'] }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $data['email'] ?? '-' }}</td>
                    <td>ID Type</td>
                    <td>{{ $data['id_card_type'] ?? '-' }}</td>
                </tr>
                @php 
                // if data['dates'] is all then 2nd september - 4th september 2025
                if (($data['dates'] ?? '') === 'All') {
                    $dates = "2<sup>nd</sup> September 2025,<br> 3<sup>rd</sup> September 2025,<br> 4<sup>th</sup> September 2025";
                } elseif (!empty($data['dates'])) {
                    $dates = $data['dates'];
                } else {
                    $dates = '-';
                }
                @endphp
                <tr>
                    <td>Mobile</td>
                    <td>{{ $data['mobile'] ?? '-' }}</td>
                     <td style="width: 15%;">ID Number</td>
                    <td style="width: 25%;">{{ $data['id_card_number'] }}</td>
                </tr>
                <tr>
                    <td>Organization/Company</td>
                    <td>{{ $data['company_name'] ?? '-' }}</td>
                       <td rowspan="2" style="vertical-align: top;">Date(s)</td>
                    <td rowspan="2" style="vertical-align: top;">{!! $dates !!}</td>
                </tr>
                <tr>
                    <td>Designation/Position</td>
                    <td>{{ $data['designation'] ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Access Information -->
        <div>
            <h3 class="section-heading">Exhibition Access</h3>
            <p>
                Please collect your Access badge from the Registration Counter from 30th August 2025 onwards at the venue.
            </p>

           
           
        </div>

        <!-- Closing Remarks and Notes -->
       

    
        @if (($data['type'] ?? '') === 'Inaugural Passes')
        <div class="note-box">
            Kindly note that participation (in-person) in the Inaugural event is subject to final confirmation and will be informed separately from 3rd week of August onwards.
        </div>
        @endif
        <br>
       

        <p class="contact-info" style="margin-top: 20px;">
            If you have any query, please feel free to contact us at <strong>visitsemiconindia@semi.org</strong>.
        </p>

    </div>
</body>

</html>