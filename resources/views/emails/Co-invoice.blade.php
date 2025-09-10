<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Exhibitor Details</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; color: #333333; background-color: #f7f7f7;">
<!-- Main Table -->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; margin: 3px auto; background-color: #ffffff; border-collapse: collapse; box-shadow: 0 2px 5px rgba(0,0,0,0.1); style="margin-top: 5px;">
<!-- Header with Logo -->
<tr>
    <td align="center" style="padding: 10px 0; background-color: #ffffff; border-bottom: 1px solid #eeeeee;">
        <img src="https://portal.semiconindia.org/asset/img/logos/logo.png" alt="Logo" style="max-width: 180px;">
        <p> SEMICON India 2025 </p>
    </td>
</tr>

<!-- Title -->
<tr>
    <td align="center" style="padding: 15px 30px; background-color: #f8f9fa; border-bottom: 2px solid #dddddd;">
        <h1 style="margin: 0; font-size: 20px; font-weight: 600; color: #333333; ">Exhibitor Details</h1>
    </td>
</tr>
<tr>
    <td style="padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; word-wrap: break-all; font-size:14px;">
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Co-Exhibitor ID:</strong><span style="font-size:15px;"></span></td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;">{{$data['coExhibitorID']}}
                </td>
            </tr>
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"><strong>Date of Approval:</strong></td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;">{{$data['approval_date']}}</td>
            </tr>
        </table>
    </td>
</tr>

<!-- Order Details Header Row -->
<tr>
    <td style="padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
            <tr>
                <td colspan="2" align="left" style="padding: 12px 20px; background-color: #f2f2f2; border: 1px solid #dddddd; font-weight: 600;"> Co-Exhibitor Details</td>

            </tr>
        </table>
    </td>
</tr>

<!-- Order Details Content -->
<tr>
    <td style="padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Co-Exhibitor Name: </strong> </td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;"> {{$data['coExhibitorName']}} </td>
            </tr>
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Contact Person: </strong> </td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"> {{$data['coExhibitorPerson']}} / {{$data['stall_size']}} </td>
            </tr>
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Contact Person Email:</strong> </td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"> {{$data['coExhibitorEmail']}}</td>
            </tr>
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa; text-align: left;"><strong>Contact Person Mobile No: </strong> </td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"> {{$data['coExhibitorPhone']}} </td>
            </tr>
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Contact Person Job Title/Designation:</strong> </td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;"> {{$data['coExhibitorJobTitle']}}</td>
            </tr>
           {{--  <!-- <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Preferred Location:</strong> </td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"><span> {{$data['pref_location']}}</span></td>
            </tr> -->
            --}}
        </table>
    </td>
</tr>

<!-- Billing Information Section -->
<tr>
    <td align="center"  style="padding: 15px 30px; background-color: #f8f9fa; border-bottom: 2px solid #dddddd;">
        <h2 style="margin: 0; font-size: 20px; font-weight: 600; color: #333333;">Billing Details</h2>
    </td>
</tr>

<!-- Billing Information Content -->
<tr>
    <td style="padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; "><strong>Name:</strong> </td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; "> {{ $data['BillingName'] }} </td>

            </tr>
            <tr>
                <td width="40%"  style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Company Name: </strong> </td>
                <td  width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; " > {{ $data['BillingCompanyName'] }} </td>
            </tr>
            <!--
            <tr>
              <td style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Company Name:</strong> Interlinks</td>
            </tr> -->
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Email Address: </strong></td>
                <td  width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; "> {{$data['BillingEmail']}} </td>
            </tr>
            <tr>
                <td width="40%"  style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Mobile No:</strong></td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; " > {{$data['BillingPhone']}} </td>

            </tr>
            <tr>

            </tr>
            <tr>
                <td colspan="2" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Address:</strong> {{$data['BillingCompanyName']}} {{$data['BillingAddress']}}, <br> {{$data['BillingCity']}}, {{$data['BillingState']}}<br> {{$data['BillingCountry']}}<br>
                    GSTIN: {{$data['GST'] ?? 'N/A'}}</td>
            </tr>
        </table>
    </td>
</tr>

<!-- Amount Received Section -->

<tr>
    <td style="padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse; font-size: 15px;">
            <tr>
                <td colspan="2" style="padding: 12px 20px; background-color: #f2f2f2; border: 1px solid #dddddd; font-weight: 600; text-align: left;">
                    Payment Summary
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Price:</strong></td>
                <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd; color: #27ae60; font-weight: bold; text-align: right;">
                    INR {{ number_format($data['price'] ?? 0, 2) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>GST:</strong></td>
                <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd; color: #27ae60; font-weight: bold; text-align: right;">
                    INR {{ number_format($data['gst'] ?? 0, 2) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Total:</strong></td>
                <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd; color: #27ae60; font-weight: bold; background-color: #fafafa; text-align: right;">
                    INR {{ number_format($data['total_amount'] ?? 0, 2) }}
                </td>
            </tr>
            <tr>
                <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Payment Status:</strong></td>
                <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd; font-weight: bold; text-align: right; color: {{ (isset($data['paystatus']) && strtolower($data['paystatus']) == 'paid') ? '#27ae60' : '#e67e22' }};">
                    {{ ucfirst($data['paystatus'] ?? 'Pending') }}
                </td>
            </tr>
        </table>
    </td>
</tr>
<!-- Footer -->
<tr>
    <td style="padding: 20px; text-align: center; color: #666666; font-size: 14px; border-top: 1px solid #eeeeee;">
        <p style="margin: 0 0 10px 0;">If you have any questions, please contact us at <a href="mailto:semiconindia@semi.org">semiconindia@semi.org</a> .</p>
        <p style="margin: 0;">Thank you for co-exhibitor registration at SEMICON India 2025.</p>
    </td>
</tr>

<!-- Contact Information -->
</table>
</body>
</html>
