<?php
//make it dynamic by passing the data from the controller
// makee the registrationType as Exhibitor
$RegistrationType = 'Complimentary Exhibitor';

?>

        <!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{$RegistrationType}} - {{config('constants.EVENT_NAME')}}</title>
</head>

<body style="margin: 0; padding: 20px; font-family: Arial, sans-serif; background-color: #f5f5f5;">
<table width="630" border="0" align="center"
       style="background-color: #ffffff; font-family: Arial, sans-serif; padding:10px; font-size:12px; text-align: left;">
    <tbody>
    <tr>
        <td align="right" valign="top">
            <!-- Header -->
            <table width="100%" border="0" style="margin:10px 0px;">
                <tbody>
                <tr>
                    <td rowspan="3">
                        <img
                                src="{{config('constants.event_logo')}}" alt="{{config('constants.EVENT_NAME')}}"
                                width="300"/></td>
                </tr>
                </tbody>
            </table>
            <h2 style="color: #096dec; margin-bottom: 20px; text-align: center;">Exhibitor Booth Application
                Received</h2>
            <p style="font-size: 14px; line-height: 1.6; text-align: left;">
                Dear <strong>{{ $exhibitor->exhibitor_name ?? 'Exhibitor' }}</strong>,
            </p>
            <p style="font-size: 14px; line-height: 1.6; text-align: left;">
                Thank you for submitting your Exhibitor Booth Application for Quantum India Bengaluru 2025. We
                have received your application and our team will review it shortly.
            </p>
            <table style="width: 100%; font-size: 12px; color: #666; margin-bottom: 20px; ">
                <tr>
                    <td style="padding: 4px 0"><strong>Application ID</strong></td>
                    <td style="padding: 4px 0">: {{ $exhibitor->exhibitor_id ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0"><strong>Category</strong></td>
                    <td style="padding: 4px 0">: {{ $exhibitor->category ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0"><strong>Booth Space</strong></td>
                    <td style="padding: 4px 0">: {{ $exhibitor->booth_space ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0"><strong>Area (SQM)</strong></td>
                    <td style="padding: 4px 0">: {{ $exhibitor->area_sqm ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0"><strong>Address</strong></td>
                    <td style="padding: 4px 0">: {{ $exhibitor->address ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0"><strong>Country</strong></td>
                    <td style="padding: 4px 0">: {{ $exhibitor->country ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0"><strong>State</strong></td>
                    <td style="padding: 4px 0">: {{ $exhibitor->state ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0"><strong>City</strong></td>
                    <td style="padding: 4px 0">: {{ $exhibitor->city ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0"><strong>ZIP Code</strong></td>
                    <td style="padding: 4px 0">: {{ $exhibitor->zip ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0"><strong>Contact Number</strong></td>
                    <td style="padding: 4px 0">: {{ $exhibitor->contact_number ?? '-' }}</td>
                </tr>
            </table>
            <!-- Stall Manning Table -->
            <h3 style="margin: 20px 0 10px 0; font-size: 15px; color: #333; border-bottom: 1px solid #e0e0e0;">
                Stall Manning Details</h3>
            <table
                    style="width: 100%; font-size: 12px; color: #666; border-collapse: collapse; margin-bottom: 20px;"
                    border="1" cellpadding="6">
                <thead>
                <tr style="background: #f8f9fa;">
                    <th>#</th>
                    <th>Title</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Designation</th>
                    <th>Mobile</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                @php $smIndex = 1; @endphp
                @foreach($persons as $person)
                    @if(isset($person->ticket_category) && strtolower($person->ticket_category) === 'stall manning')
                        <tr>
                            <td>{{ $smIndex++ }}</td>
                            <td>{{ $person->title }}</td>
                            <td>{{ $person->first_name }}</td>
                            <td>{{ $person->last_name }}</td>
                            <td>{{ $person->designation }}</td>
                            <td>{{ $person->mobile }}</td>
                            <td>{{ $person->email }}</td>
                        </tr>
                    @endif
                @endforeach
                @if($smIndex === 1)
                    <tr>
                        <td colspan="7" style="text-align:center;">No Stall Manning data</td>
                    </tr>
                @endif
                </tbody>
            </table>
            <!-- Delegate Table -->
            <h3 style="margin: 20px 0 10px 0; font-size: 15px; color: #333; border-bottom: 1px solid #e0e0e0;">
                Delegate Details</h3>
            <table
                    style="width: 100%; font-size: 12px; color: #666; border-collapse: collapse; margin-bottom: 20px;"
                    border="1" cellpadding="6">
                <thead>
                <tr style="background: #f8f9fa;">
                    <th>#</th>
                    <th>Title</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Designation</th>
                    <th>Mobile</th>
                    <th>Email</th>
                </tr>
                </thead>
                <tbody>
                @php $dIndex = 1; @endphp
                @foreach($persons as $person)
                    @if(isset($person->ticket_category) && strtolower($person->ticket_category) === 'delegate')
                        <tr>
                            <td>{{ $dIndex++ }}</td>
                            <td>{{ $person->title }}</td>
                            <td>{{ $person->first_name }}</td>
                            <td>{{ $person->last_name }}</td>
                            <td>{{ $person->designation }}</td>
                            <td>{{ $person->mobile }}</td>
                            <td>{{ $person->email }}</td>
                        </tr>
                    @endif
                @endforeach
                @if($dIndex === 1)
                    <tr>
                        <td colspan="7" style="text-align:center;">No Delegate data</td>
                    </tr>
                @endif
                </tbody>
            </table>
            <div
                    style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745; text-align: left;">
                <h4 style="margin: 0 0 10px 0; color: #28a745; font-size: 14px; text-align: left;">Next Steps
                </h4>
                <ul style="margin: 0; padding-left: 20px; font-size: 12px; color: #666; text-align: left;">
                    <li>Our team will review your application and contact you if any additional information is
                        required.
                    </li>
                    <li>You will receive a confirmation email once your application is approved.</li>
                    <li>For any queries, please contact our support team.</li>
                </ul>
            </div>
            <p style="font-size: 14px; line-height: 1.6; text-align: left;">
                If you have any questions about your application or need assistance, please don't hesitate to
                contact us.
            </p>
            <p style="font-size: 14px; line-height: 1.6; text-align: left;">
                Best regards,<br>
                <strong>Quantum India Bengaluru Team</strong>
            </p>
            <!-- Footer -->
            <table width="100%" border="0">
                <tbody>
                <tr>
                    <td width="50%" align="left" valign="middle" style="padding:10px 0px"><img
                                src="https://www.quantumindiabengaluru.com/assets/img/logo/MMA.jpg" width="150">
                    </td>
                    <td width="50%" align="right" valign="middle" style="padding:10px 0px"><strong>Quantum
                            India Bengaluru Secretariat</strong><br>
                        MM Activ Sci-Tech Communications<br>
                        No.11/6, NITON, Block "C"<br>
                        Second Floor, Palace Road<br>
                        Bengaluru - 560001, Karnataka, India<br><br>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
</body>

</html>
