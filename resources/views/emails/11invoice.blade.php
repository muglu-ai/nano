<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> Order confirmation </title>
<meta name="robots" content="noindex,nofollow" />
<meta name="viewport" content="width=device-width; initial-scale=1.0;" />
<style type="text/css">
    @import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700);
    body { margin: 0; padding: 0; background: #e1e1e1; }
    div, p, a, li, td { -webkit-text-size-adjust: none; }
    .ReadMsgBody { width: 100%; background-color: #ffffff; }
    .ExternalClass { width: 100%; background-color: #ffffff; }
    body { width: 100%; height: 100%; background-color: #e1e1e1; margin: 0; padding: 0; -webkit-font-smoothing: antialiased; }
    html { width: 100%; }
    p { padding: 0 !important; margin-top: 0 !important; margin-right: 0 !important; margin-bottom: 0 !important; margin-left: 0 !important; }
    .visibleMobile { display: none; }
    .hiddenMobile { display: block; }

    @media only screen and (max-width: 600px) {
        body { width: auto !important; }
        table[class=fullTable] { width: 96% !important; clear: both; }
        table[class=fullPadding] { width: 85% !important; clear: both; }
        table[class=col] { width: 45% !important; }
        .erase { display: none; }
    }

    @media only screen and (max-width: 420px) {
        table[class=fullTable] { width: 100% !important; clear: both; }
        table[class=fullPadding] { width: 85% !important; clear: both; }
        table[class=col] { width: 100% !important; clear: both; }
        table[class=col] td { text-align: left !important; }
        .erase { display: none; font-size: 0; max-height: 0; line-height: 0; padding: 0; }
        .visibleMobile { display: block !important; }
        .hiddenMobile { display: none !important; }
    }
</style>

<body>
<!-- Header -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
    <tr>
        <td height="20"></td>
    </tr>
    <tr>
        <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="border-radius: 10px 10px 0 0;">
                <tr class="hiddenMobile">
                    <td height="40"></td>
                </tr>
                <tr class="visibleMobile">
                    <td height="30"></td>
                </tr>

                <tr>
                    <td>
                        <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                            <tbody>
                            <tr>
                                <td>
                                    <table width="220" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                                        <tbody>
                                        <tr>
                                            <td align="left"> <img src="https://www.mmactiv.in/images/semicon_logo.png" width="120" height="50" alt="logo" border="0" /></td>
                                        </tr>
                                        <tr class="hiddenMobile">
                                            <td height="40"></td>
                                        </tr>
                                        <tr class="visibleMobile">
                                            <td height="20"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 12px; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: left;">
                                                Hello, {{ $data['BillingName'] }}<br>
                                                <br> Thank you for {{ $data['registrationType'] }} registration at Semicon India 2025.
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <table width="220" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                                        <tbody>
                                        <tr class="visibleMobile">
                                            <td height="20"></td>
                                        </tr>
                                        <tr>
                                            <td height="5"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 21px; color: #ff0000; letter-spacing: -1px; font-family: 'Open Sans', sans-serif; line-height: 1; vertical-align: top; text-align: right;">
                                                Application Confirmation
                                            </td>
                                        </tr>
                                        <tr>
                                        <tr class="hiddenMobile">
                                            <td height="60"></td>
                                        </tr>
                                        <tr class="visibleMobile">
                                            <td height="20"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 12px; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: right;">
                                                <small>ORDER </small>{{ $data['invoiceID'] }}<br />
                                                <small>Billing Date </small>{{  $data['billingDate']  }}<br />
                                                <small>Due Date </small>{{ $data['DueDate']  }}<br />
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<!-- /Header -->
<!-- Order Details -->
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
    <tbody>
    <tr>
        <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
                <tbody>
                <tr>
                <tr class="hiddenMobile">
                    <td height="60"></td>
                </tr>
                <tr class="visibleMobile">
                    <td height="40"></td>
                </tr>
                <tr>
                    <td>
                        <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                            <tbody>
                            <tr>
                                <td>
                                    <table width="220" border="0" cellpadding="0" cellspacing="0" align="left" class="col">

                                        <tbody>
                                        <tr>
                                            <td style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                                <strong>BILLING INFORMATION</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="0"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                                <br> {{$data['BillingCompanyName']}}<br> {{$data['BillingAddress']}},<br> {{$data['BillingCity']}}, {{$data['BillingState']}}<br>M: {{$data['BillingPhone']}}<br>
                                                GSTIN: {{$data['GST']}}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>


                                    <table width="220" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                                        <tbody>
                                        <tr class="visibleMobile">
                                            <td height="20"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 1; vertical-align: top; ">
                                                <strong>Application Details</strong>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td width="100%" height="10"></td>
                                        </tr>
                                        <tr>
                                            <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 20px; vertical-align: top; ">
                                                Type of Business: {{$data['business_type']}}<br>
                                                Sector(s): {{$data['sectors']}}<br>Main Product Category: {{$data['products']}}  <a href="#" style="color: #ff0000; text-decoration:underline;"></a><br>
                                                <a href="#" style="color:#b0b0b0;"></a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr class="hiddenMobile">
                    <td height="60"></td>
                </tr>
                <tr>
                    <td>
                        <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                            <tbody>
                            <tr>
                                <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 10px 7px 0;" width="52%" align="left">
                                    Stall Type / Size
                                </th>
                                <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="left">
                                    <small>Booth No</small>
                                </th>

                                <th style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #1e2b33; font-weight: normal; line-height: 1; vertical-align: top; padding: 0 0 7px;" align="right">
                                    Subtotal
                                </th>
                            </tr>
                            <tr>
                                <td height="1" style="background: #bebebe;" colspan="4"></td>
                            </tr>
                            <tr>
                                <td height="10" colspan="4"></td>
                            </tr>
                            <tr>
                                <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #1e2b33;  line-height: 18px;  vertical-align: top; padding:10px 0;" class="article">
                                    {{$data['stall_type']}} / {{$data['stall_size']}}
                                </td>
                                <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #1e2b33;  line-height: 18px;  vertical-align: top; padding:10px 0;"><small>{{$data['booth_no']}}</small></td>
                                <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #1e2b33;  line-height: 18px;  vertical-align: top; padding:10px 0;" align="right">{{$data['price']}}</td>
                            </tr>
                            <tr>
                                <td height="1" colspan="4" style="border-bottom:1px solid #e4e4e4"></td>
                            </tr>

                            <tr>
                                <td height="1" colspan="4" style="border-bottom:1px solid #e4e4e4"></td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td height="20"></td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<!-- /Order Details -->
<!-- Total -->

<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
    <tbody>
    <tr>
        <td>
            <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
                <tbody>
                <tr>
                    <td>

                        <!-- Table Total -->
                        <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                            <tbody>
                            <tr>
                                <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; ">
                                    Subtotal
                                </td>
                                <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #646a6e; line-height: 22px; vertical-align: top; text-align:right; white-space:nowrap;" width="80">
                                    {{$data['price']}}
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #b0b0b0; line-height: 22px; vertical-align: top; text-align:right; "><small>TAX</small></td>
                                <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #b0b0b0; line-height: 22px; vertical-align: top; text-align:right; ">
                                    <small>{{$data['gst']}}</small>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                    <strong>Grand Total (Incl.Tax) </strong>
                                </td>
                                <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #000; line-height: 22px; vertical-align: top; text-align:right; ">
                                    <strong> {{$data['total_amount']}}</strong>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                        <!-- /Table Total -->

                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    </tbody>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#e1e1e1">
<table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff">
    <tr class="hiddenMobile">
        <td height="40"></td>
    </tr>
    <tr class="visibleMobile">
        <td height="30"></td>
    </tr>

    <tr>
        <td>
            <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                <tbody>
                <tr>
                    <td>
                        <table width="120" border="0" cellpadding="0" cellspacing="0" align="left" class="col">
                            <tbody>
                            <tr>
                                <td align="left"> <img src="https://www.mmactiv.in/images/mma.jpg" width="120" height="50" alt="logo" border="0" /></td>
                            </tr>
                            <tr class="hiddenMobile">
                                <td height="40"></td>
                            </tr>
                            <tr class="visibleMobile">
                                <td height="20"></td>
                            </tr>

                            </tbody>
                        </table>
                        <table width="320" border="0" cellpadding="0" cellspacing="0" align="right" class="col">
                            <tbody>
                            <tr class="visibleMobile">
                                <td height="20"></td>
                            </tr>
                            <tr>
                                <td height="1"></td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: ; vertical-align: top; ">
                                    <strong>Office: Semicon India 2025 Secretariat</strong>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 12px; font-family: 'Open Sans', sans-serif; color: #5b5b5b; line-height: 18px; vertical-align: top; ">
                                    MM Activ Sci-Tech Communications Pvt. Ltd.<br> 103-104, Rohit House, 3,<br> Tolstoy Marg, Connaught Place<br> New Delhi - 110 001<br> T:  011-4354 2737 / 011-2331 9387<br>
                                    GSTIN: 29AABCM2615H1ZM
                                </td>
                            </tr>
                            <tr>
                            <tr class="hiddenMobile">
                                <td height="60"></td>
                            </tr>
                            <tr class="visibleMobile">
                                <td height="20"></td>
                            </tr>

                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
</table>
</table>
<!-- /Total -->
<!-- Information -->


<tr>
    <td>
        <table width="600" border="0" cellpadding="0" cellspacing="0" align="center" class="fullTable" bgcolor="#ffffff" style="border-radius: 0 0 10px 10px;">
            <tr>
                <td>
                    <table width="480" border="0" cellpadding="0" cellspacing="0" align="center" class="fullPadding">
                        <tbody>
                        <tr>
                            <td style="font-size: 12px; color: #5b5b5b; font-family: 'Open Sans', sans-serif; line-height: 18px; vertical-align: top; text-align: left;">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr class="spacer">
                <td height="50"></td>
            </tr>

        </table>
    </td>
</tr>
<tr>
    <td height="20"></td>
</tr>
</table>
</body>
</html>
