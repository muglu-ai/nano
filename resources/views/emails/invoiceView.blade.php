<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png"
          type="image/vnd.microsoft.icon"/>
    <title>Exhibitor Details</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; color: #333333; background-color: #f7f7f7;">
<!-- Main Table -->
<table border="0" cellpadding="0" cellspacing="0" width="100%"
       style="max-width: 600px; margin: 3px auto; background-color: #ffffff; border-collapse: collapse; box-shadow: 0 2px 5px rgba(0,0,0,0.1); style="
       margin-top: 5px;
">
<!-- Header with Logo -->
<tr>
    <td align="center" style="padding: 10px 0; background-color: #ffffff; border-bottom: 1px solid #eeeeee;">
        <table border="0" cellpadding="0" cellspacing="0" style="margin: 0 auto;">
            <tr>
                <td colspan="5" style="text-align: center;">
                    <img src="{{config('constants.event_logo')}}" alt="{{config('constants.EVENT_NAME')}}" style="max-width: 300px;">
                    <p style="margin: 5px 0 10px 0;">{{ config('constants.EVENT_NAME') }}</p>
                </td>
            </tr>
            <tr>
                <td style="padding: 0 5px;">
                    <a href="{{ config('constants.SOCIAL_LINKS.facebook') }}" target="_blank">
                        <img src="https://cdn-icons-png.flaticon.com/24/733/733547.png" alt="Facebook" style="width:24px; height:24px; vertical-align: middle;">
                    </a>
                </td>
                <td style="padding: 0 5px;">
                    <a href="{{ config('constants.SOCIAL_LINKS.twitter') }}" target="_blank">
                        <img src="{{ asset('assets/images/socials/twitter.png') }}" alt="X" style="width:24px; height:24px; vertical-align: middle;">
                    </a>
                </td>
                <td style="padding: 0 5px;">
                    <a href="{{ config('constants.SOCIAL_LINKS.instagram') }}" target="_blank">
                        <img src="https://cdn-icons-png.flaticon.com/24/733/733558.png" alt="Instagram" style="width:24px; height:24px; vertical-align: middle;">
                    </a>
                </td>
                <td style="padding: 0 5px;">
                    <a href="{{ config('constants.SOCIAL_LINKS.linkedin') }}" target="_blank">
                        <img src="https://cdn-icons-png.flaticon.com/24/733/733561.png" alt="LinkedIn" style="width:24px; height:24px; vertical-align: middle;">
                    </a>
                </td>
                <td style="padding: 0 5px;">
                    <a href="{{ config('constants.SOCIAL_LINKS.youtube') }}" target="_blank">
                        <img src="https://cdn-icons-png.flaticon.com/24/1384/1384060.png" alt="YouTube" style="width:24px; height:24px; vertical-align: middle;">
                    </a>
                </td>
            </tr>
        </table>
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
        <table border="0" cellpadding="0" cellspacing="0" width="100%"
               style="border-collapse: collapse; word-wrap: break-all; font-size:14px;">
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"><strong>Date of
                        Registration:</strong></td>
                <td width="60%"
                    style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;">{{$data['approval_date']}}</td>
            </tr>
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>TIN No:</strong><span style="font-size:15px;"></span></td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;">{{$data['applicationID']}}
                </td>
            </tr>
            @if(!empty($data['pinNo']) && $data['pinNo'] != 'N/A')
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>PIN No:</strong><span style="font-size:15px;"></span></td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;">{{$data['pinNo']}}
                </td>
            </tr>
            @endif

        </table>
    </td>
</tr>

<!-- Order Details Header Row -->
<tr>
    <td style="padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
            <tr>
                <td colspan="2" align="left"
                    style="padding: 12px 20px; background-color: #f2f2f2; border: 1px solid #dddddd; font-weight: 600;"> {{$data['exhibitor_name']}}</td>

            </tr>
        </table>
    </td>
</tr>

<!-- Order Details Content -->
<tr>
    <td style="padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
            @if(!empty($data['business_type']) && $data['business_type'] != 'N/A')
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Type of
                            Business: </strong></td>
                    <td width="60%"
                        style="padding: 12px 20px; border: 1px solid #dddddd;"> {{$data['business_type']}} </td>
                </tr>
            @endif

            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;">
                    <strong>Stall Type / Size: </strong></td>
                <td width="60%"
                    style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"> {{$data['stall_type']}}
                    / {{$data['stall_size']}} </td>
            </tr>
            @if(!empty($data['products'] && $data['products']!= 'N/A'))
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;">
                        <strong>Product Category:</strong></td>
                    <td width="60%"
                        style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"> {{$data['products']}}</td>
                </tr>
            @endif
            @if(!empty($data['booth_no']) && $data['booth_no'] != 'N/A')
                <tr>
                    <td width="40%"
                        style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa; text-align: left;">
                        <strong>Booth No: </strong></td>
                    <td width="60%"
                        style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;"> {{$data['booth_no']}} </td>
                </tr>
            @endif
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Sector:</strong></td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;"> {{$data['sectors']}}</td>
            </tr>
                <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Sub-Sector:</strong></td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd;"> {{$data['subSector']}}</td>
            </tr>
            @if(!empty($data['pref_location']) && $data['pref_location'] != 'N/A')
                <tr>
                    <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;">
                        <strong>Preferred Location:</strong></td>
                    <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; text-align: left;">
                        <span> {{$data['pref_location']}}</span></td>
                </tr>
            @endif
        </table>
    </td>
</tr>

<!-- Billing Information Section -->
<tr>
    <td align="center" style="padding: 15px 30px; background-color: #f8f9fa; border-bottom: 2px solid #dddddd;">
        <h2 style="margin: 0; font-size: 20px; font-weight: 600; color: #333333;">Billing Details</h2>
    </td>
</tr>

<!-- Billing Information Content -->
<tr>
    <td style="padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; "><strong>Name:</strong></td>
                <td width="60%"
                    style="padding: 12px 20px; border: 1px solid #dddddd; "> {{ $data['BillingName'] }} </td>

            </tr>
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;">
                    <strong>Company Name: </strong></td>
                <td width="60%"
                    style="padding: 12px 20px; border: 1px solid #dddddd; "> {{ $data['BillingCompanyName'] }} </td>
            </tr>
            <!--
            <tr>
              <td style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;"><strong>Company Name:</strong> Interlinks</td>
            </tr> -->
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Email Address: </strong>
                </td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; "> {{$data['BillingEmail']}} </td>
            </tr>
            <tr>
                <td width="40%" style="padding: 12px 20px; border: 1px solid #dddddd; background-color: #fafafa;">
                    <strong>Mobile No:</strong></td>
                <td width="60%" style="padding: 12px 20px; border: 1px solid #dddddd; "> {{$data['BillingPhone']}} </td>

            </tr>
            <tr>

            </tr>
            <tr>
                <td colspan="2" style="padding: 12px 20px; border: 1px solid #dddddd;">
                    <strong>Address:</strong> {{$data['BillingCompanyName']}} {{$data['BillingAddress']}},
                    <br> {{$data['BillingCity']}}, {{$data['BillingState']}}<br> {{$data['BillingCountry']}}<br>
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
                <td style="padding: 12px 20px; border: 1px solid #dddddd;">
                    <strong>Price:</strong>
                </td>
                <td style="padding: 12px 20px; border: 1px solid #dddddd;">
                    INR {{$data['price']}}
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 20px; border: 1px solid #dddddd;">
                    <strong>GST @ 18%:</strong>
                </td>
                <td style="padding: 12px 20px; border: 1px solid #dddddd;">
                    INR {{$data['gst']}}
                </td>
            </tr>
            <tr>
                <td style="padding: 12px 20px; border: 1px solid #dddddd;">
                    <strong>Processing Charge:</strong>
                </td>
                <td style="padding: 12px 20px; border: 1px solid #dddddd;">
                    INR {{$data['processingCharge']}}
                </td>
            </tr>
            <tr>
                <td style="padding: 15px 20px; border: 1px solid #dddddd; background-color: #f2f7ff; font-size: 18px;">
                    <strong>Total:</strong>
                </td>
                <td style="padding: 15px 20px; border: 1px solid #dddddd; background-color: #f2f7ff; font-size: 18px; color: #2980b9; font-weight: bold;">
                    INR {{$data['total_amount']}}
                </td>
            </tr>
        </table>
    </td>
</tr>
<!-- Amount Received Section -->
<tr>
    <td align="center" style="padding: 15px 30px; background-color: #f8f9fa; border-bottom: 2px solid #dddddd;">
        <h2 style="margin: 0; font-size: 18px; color: #333333;">Amount Received</h2>
    </td>
</tr>
<tr>
    <td style="padding: 0;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse: collapse;">
            <tr>
                <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Amount Received:</strong>
                </td>
                <td width="50%"
                    style="padding: 12px 20px; border: 1px solid #dddddd; color: #27ae60; font-weight: bold;">
                    INR {{$data['amount_paid'] ?? '0.00'}}
                </td>
            </tr>
            @if(!empty($data['transactionId']) && $data['transactionId'] != 'N/A')

            <tr>
                <td width="50%" style="padding: 12px 20px; border: 1px solid #dddddd;"><strong>Reference Number:</strong>
                </td>
                <td width="50%"
                    style="padding: 12px 20px; border: 1px solid #dddddd; font-weight: bold;">
                    {{$data['transactionId'] ?? 'N/A'}}
                </td>
            </tr>
            @endif

        </table>
</td>
</tr>
<!-- Footer -->
<tr bgcolor='#FFFFFF'>
    <td style='font-size: 11px; font-family: Verdana, Arial, Helvetica, sans-serif;'>
        <table width='100%' border='0' cellspacing='0' cellpadding='0'>
            <tr>
                <td width='4%' height='2'></td>
                <td width='34%' bgcolor='#D0CAB0'></td>
                <td width='59%' bgcolor='#D0CAB0'></td>
                <td width='3%'></td>
            </tr>
            <tr>
                <td height='10' colspan='4' align='center' valign='middle'></td>
            </tr>
            <tr>
                <td colspan='2' align='center' valign='middle' style="padding: 10px 0;">
                    <div style="display: inline-block; max-width: 220px; width: 100%; text-align: center;">
                        <img src="{{ config('constants.organizer_logo') }}" style="max-width: 200px; height: auto; display: block; margin: 0 auto;" alt="{{ config('constants.organizer.name') }}"/>
                    </div>
                </td>
                <td>
                    <table width='100%' border='0' cellspacing='0' cellpadding='0'>
                        <tr>
                            <td style='font-family: Verdana, Arial, Helvetica, sans-serif; color: #666666; font-size: 11px; font-weight: bold;'>
                                Office : {{ config('constants.organizer.name') }}
                            </td>
                        </tr>
                        <tr>
                            <td style='font-family: Verdana, Arial, Helvetica, sans-serif; color: #666666; font-size: 11px; font-weight: bold;'>
                                Address : {!! config('constants.organizer.address') !!}
                            </td>
                        </tr>
                        {{-- Add Tel: --}}
                        <tr>
                            <td style='font-family: Verdana, Arial, Helvetica, sans-serif; color: #666666; font-size: 11px; font-weight: bold;'>
                                Tel: {{ config('constants.organizer.phone') }}
                            </td>
                        </tr>
                        {{-- Website --}}
                        <tr>
                            <td style='font-family: Verdana, Arial, Helvetica, sans-serif; color: #666666; font-size: 11px; font-weight: bold;'>
                                Website: <a href='{{ config('constants.EVENT_WEBSITE') }}' target='_blank'>{{ config('constants.EVENT_WEBSITE') }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td style='font-family: Verdana, Arial, Helvetica, sans-serif; color: #666666; font-size: 11px; font-weight: bold;'>
                                Karnataka GST No.: {{ config('constants.GSTIN') }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan='2' align='center' valign='middle'>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        </table>
    </td>
</tr>

<tr>
    <td style="padding: 20px; text-align: center; color: #666666; font-size: 14px; border-top: 1px solid #eeeeee;">
        <p style="margin: 0 0 10px 0;">If you have any questions, please contact us at <a
                    href="mailto:{{ config('constants.organizer.email') }}">{{ config('constants.organizer.email') }}</a>.
        </p>
        <p style="margin: 0;">Thank you for exhibitor registration at {{config('constants.EVENT_NAME')}}.</p>
    </td>
</tr>

<!-- Contact Information -->

</body>
</html>
