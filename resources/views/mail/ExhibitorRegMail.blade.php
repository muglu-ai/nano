{{--@dd($data)--}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="icon" type="image/png" href="{{ config('constants.FAVICON') }}"/>
    <title>Registration  - {{config('constants.EVENT_NAME')}}</title>
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
                    <td rowspan="3"><img
                                src="{{config('constants.event_logo')}}"
                                alt="{{config('constants.EVENT_NAME')}}"
                                width="300"/></td>
                </tr>
                </tbody>
            </table>
            <h2 style="color: #096dec; margin-bottom: 20px; text-align: center;">Registration Information Receipt</h2>
            {{--            <p style="font-size: 14px; line-height: 1.6; text-align: left;">--}}
            {{--                Dear <strong>{{ $exhibitor->exhibitor_name ?? 'Exhibitor' }}</strong>,--}}
            {{--            </p>--}}
            <p style="font-size: 14px; line-height: 1.6; text-align: left;">
                Thank you for registering for {{config('constants.EVENT_NAME') . ' '. config('constants.EVENT_YEAR')}}.
            </p>
           <table style="width: 100%; font-size: 13px; color: #333; margin-bottom: 24px; border-radius: 6px; border: 1px solid #e0e0e0; overflow: hidden;">

                <tbody>
                    @if(!empty($data['unique_id']))
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;"><strong>TIN No</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">{{ $data['unique_id'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['pinNo']) && $data['pinNo'] !== 'N/A')
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;"><strong>PIN No</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">{{ $data['pinNo'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['ticket_type']))
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;"><strong>Ticket Category</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">{{ $data['ticket_type'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['company_name']))
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;"><strong>Company Name</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">{{ $data['company_name'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['address']))
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;"><strong>Address</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">{{ $data['address'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['country']))
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;"><strong>Country</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">{{ $data['country'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['state']))
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;"><strong>State</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">{{ $data['state'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['city']))
                        <tr>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;"><strong>City</strong></td>
                            <td style="padding: 8px 0; border-bottom: 1px solid #f0f0f0;">{{ $data['city'] }}</td>
                        </tr>
                    @endif
                    @if(!empty($data['postalCode']))
                        <tr>
                            <td style="padding: 8px 0;"><strong>ZIP Code</strong></td>
                            <td style="padding: 8px 0;">{{ $data['postalCode'] }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <h3 style="margin: 20px 0 10px 0; font-size: 15px; color: #333; border-bottom: 1px solid #e0e0e0; text-align: start;"><h3 style="margin: 20px 0 10px 0; font-size: 15px; color: #333; border-bottom: 1px solid #e0e0e0; text-align: start;">
                Details</h3>
            <table
                    style="width: 100%; font-size: 12px; color: #666; border-collapse: collapse; margin-bottom: 20px;"
                    border="1" cellpadding="6">
                <thead>
                <tr style="background: #f8f9fa;">
                    <th>#</th>
                    <th>Name</th>
                    <th>Designation</th>
                    <th>Mobile</th>
                    <th>Email</th>
                    <th>Ticket Category</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ $data['fullName'] ?? '-' }}</td>
                    <td>{{ $data['designation'] ?? '-' }}</td>
                    <td>{{ $data['mobile'] ?? '-' }}</td>
                    <td>{{ $data['email'] ?? '-' }}</td>
                    <td> {{$data['ticket_type']}}</td>
                </tr>
                </tbody>
            </table>
            <p style="font-size: 14px; line-height: 1.6; text-align: left;">
                Best regards,<br>
                <strong>{{config('constants.EVENT_NAME') . ' ' .config('constants.EVENT_YEAR')}}</strong>
            </p>
            <!-- Footer -->
            <table width="100%" border="0">
                <tbody>
                <tr>
                    <td width="50%" align="left" valign="middle" style="padding:10px 0px"><img
                                src="https://www.quantumindiabengaluru.com/assets/img/logo/MMA.jpg" width="150">
                    </td>
                    <td width="50%" align="right" valign="middle" style="padding:10px 0px">
                        <strong>{{config('constants.EVENT_NAME')}} Secretariat</strong><br>
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
