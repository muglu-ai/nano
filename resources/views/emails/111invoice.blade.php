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
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                <tr>
                    <td style="padding: 12px 20px; border: 1px solid #dddddd; width: 50%;"><strong>Application No:</strong><br><span style="font-size:15px;">{{$data['applicationID']}}</span></td>
                    <td style="padding: 12px 20px; border: 1px solid #dddddd; width: 50%; text-align: left;"><strong>Date of Approval:</strong><br> {{$data['approval_date']}}</td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Order Details Header Row -->
    <tr>
        <td style="padding: 0;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                <tr>
                    <th width="50%" align="left" style="padding: 12px 20px; background-color: #f2f2f2; border: 1px solid #dddddd; font-weight: 600; width: 50%;">{{$data['BillingCompanyName']}}</th>
                    <th  width="50%" align="left" style="padding: 12px 20px; background-color: #f2f2f2; border: 1px solid #dddddd; font-weight: 600; width: 50%;">Order Details</th>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Order Details Content -->
    <tr>
        <td style="padding: 0;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                <tr>
                    <td style="padding: 12px 20px; border: 1px solid #dddddd; width: 50%;"><strong>Type of Business: </strong> {{$data['business_type']}}</td>
                    <td style="padding: 12px 20px; border: 1px solid #dddddd; width: 50%; text-align: left;"><strong>Stall Type / Size: </strong> {{$data['stall_type']}} / {{$data['stall_size']}} </td>
                </tr>
                <tr>
                    <td style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Product Category:</strong> {{$data['products']}}</td>
                    <td style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa; text-align: left;"><strong>Booth No: </strong> {{$data['stallNumber']}}</td>
                </tr>
                <tr>
                    <td style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Sector(s):</strong> {{$data['sectors']}}</td>
                    <td style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"><strong>Preferred Location:</strong> <span> {{$data['pref_location']}}</span></td>
                </tr>
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
                    <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd; "><strong>Name:</strong> {{ $data['BillingName'] }}</td>
                    <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Company Name: </strong> {{$data['BillingCompanyName']}} </td>

                </tr>
                <!--
                <tr>
                  <td style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Company Name:</strong> Interlinks</td>
                </tr> -->
                <tr>
                    <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Email Address: </strong> {{$data['BillingEmail']}}</td>
                    <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Mobile No:</strong> {{$data['BillingPhone']}}</td>
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

    <!-- Order Summary Section -->
    <tr>
        <td align="center" style="padding: 15px 30px; background-color: #f8f9fa; border-bottom: 2px solid #dddddd;">
            <h2 style="margin: 0; font-size: 18px; color: #333333;">Order Summary</h2>
        </td>
    </tr>

    <!-- Order Summary Content -->
    <tr>
        <td style="padding: 0;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
                <tr>
                    <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Product Price:</strong> INR {{$data['price']}}</td>
                    <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Taxes:</strong> INR {{$data['gst']}}</td>
                </tr>
                <!-- <tr>
                  <td style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Processing Charge:</strong> INR 0</td>
                </tr> -->
                <tr>

                </tr>
                <tr>
                    <td align="right" colspan="2" style="padding: 15px 20px; border: 1px solid #dddddd; background-color: #f2f7ff; font-size: 18px;">
                        <strong>Total:</strong> <span style="color: #2980b9; font-weight: bold;">INR {{$data['total_amount']}}</span>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="padding: 20px; text-align: center; color: #666666; font-size: 14px; border-top: 1px solid #eeeeee;">
            <p style="margin: 0 0 10px 0;">If you have any questions, please contact us at <a href="mailto:{{ config('constants')['organizer']['email'] }}">{{ config('constants')['organizer']['email'] }}</a> .</p>
            <p style="margin: 0;">Thank you for exhibitor registration at SEMICON India 2025.</p>
        </td>
    </tr>

    <!-- Contact Information -->
</table>
</body>
</html>
