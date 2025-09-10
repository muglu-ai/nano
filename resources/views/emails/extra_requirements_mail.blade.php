<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Order Confirmation - Semicon India 2025</title>
</head>

<body
    style="
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            color: #333333;
            background-color: #f5f5f5;
        ">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding: 20px 0">
                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="
                            background-color: #ffffff;
                            border-radius: 5px;
                            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                        ">
                    <!-- Logo -->
                    <tr>
                        <td align="center" style="padding: 30px 0 5px 0">
                            <img src="https://portal.semiconindia.org/asset/img/logos/logo.png" alt="Semicon India Logo"
                                width="150" style="display: block; border: 0;">

                        </td>

                    </tr>
                    <tr>
                        <td align="center">
                            <h4
                                style="
                                        margin: 0 0 10px 0;
                                        color: #333333;
                                    ">
                                SEMICON India 2025
                            </h4>


                        </td>
                    </tr>

                    <!-- Thank you message -->
                    <tr>
                        <td align="center" style="padding: 0 30px 30px 30px">
                            <h1
                                style="
                                        font-size: 22px;
                                        margin: 0 0 10px 0;
                                        color: #333333;
                                    ">
                                Thank You for Your Orders
                            </h1>
                            <p
                                style="
                                        margin: 0;
                                        color: #666666;
                                        font-size: 16px;
                                    ">
                                Thank you for ordering extra requirements
                                items at SEMICON India 2025
                            </p>
                        </td>
                    </tr>

                    <!-- Order details -->
                    <tr>
                        <td style="padding: 0 30px 20px 30px">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="50%" valign="top">
                                        <p
                                            style="
                                                    font-weight: bold;
                                                    margin: 0 0 5px 0;
                                                ">
                                            Order Number:
                                        </p>
                                        <p style="margin: 0 0 15px 0">
                                            {{ $invoice_Id }}
                                        </p>
                                    </td>
                                    <td width="50%" valign="top" align="right">
                                        <p
                                            style="
                                                    font-weight: bold;
                                                    margin: 0 0 5px 0;
                                                ">
                                            Order Date:
                                        </p>
                                        <p style="margin: 0 0 15px 0">
                                            {{ $order_date }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Note section -->
                    <tr>
                        <td style="padding: 0 30px 20px 30px">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="
                                        background-color: #f9f9f9;
                                        border-radius: 4px;
                                    ">
                                <tr>
                                    <td style="padding: 15px">
                                        <p
                                            style="
                                                    font-weight: bold;
                                                    margin: 0 0 10px 0;
                                                ">
                                            Note:
                                        </p>
                                        <p
                                            style="
                                                    margin: 0;
                                                    color: #666666;
                                                    font-style: italic;
                                                ">
                                            Your items will be available for
                                            pickup at the registration desk
                                            on the first day of the event.
                                            Please bring your order
                                            confirmation.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Exhibitor Information -->
                    <tr>
                        <td style="padding: 0 30px 20px 30px">
                            <h2
                                style="
                                    font-size: 18px;
                                    margin: 0 0 15px 0;
                                    padding-bottom: 10px;
                                    border-bottom: 1px solid #eeeeee;
                                ">
                                Exhibitor Information
                            </h2>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 14px; color: #333;">
                                <tr>
                                    <td style="padding: 5px 0; width: 160px;"><strong>Exhibitor Name</strong></td>
                                    <td style="padding: 5px 0; width: 10px; text-align: center;">:</td>
                                    <td style="padding: 5px 0;">{{ $exhibitor_name }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Booth No</strong></td>
                                    <td style="padding: 5px 0; text-align: center;">:</td>
                                    <td style="padding: 5px 0;">{{ $booth_no }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Stall Size</strong></td>
                                    <td style="padding: 5px 0; text-align: center;">:</td>
                                    <td style="padding: 5px 0;">{{ $stall_size }} SQM</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Billing information -->
                    <tr>
                        <td
                            style="padding: 30px; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); font-family: Arial, sans-serif;">
                            <h2
                                style="font-size: 20px; margin: 0 0 15px 0; color: #004aad; border-bottom: 2px solid #eeeeee; padding-bottom: 10px;">
                                Billing Information
                            </h2>

                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; color: #333;">
                                <tr>
                                    <td style="padding: 5px 0; width: 160px;"><strong>Company</strong></td>
                                    <td style="padding: 5px 0; width: 10px; text-align: center;">:</td>
                                    <td style="padding: 5px 0;">{{ $billingCompany }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Contact Name</strong></td>
                                    <td style="padding: 5px 0; text-align: center;">:</td>
                                    <td style="padding: 5px 0;">{{ $billingContactName }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Email</strong></td>
                                    <td style="padding: 5px 0; text-align: center;">:</td>
                                    <td style="padding: 5px 0;">{{ $billingEmail }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Contact Number</strong></td>
                                    <td style="padding: 5px 0; text-align: center;">:</td>
                                    <td style="padding: 5px 0;">{{ $billingPhone }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>Address</strong></td>
                                    <td style="padding: 5px 0; text-align: center;">:</td>
                                    <td style="padding: 5px 0;">{{ $billingAddress }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 5px 0;"><strong>GST Compliance</strong></td>
                                    <td style="padding: 5px 0; text-align: center;">:</td>
                                    <td style="padding: 5px 0;">{{ $gst_applicable ? 'Yes' : 'No' }}</td>
                                </tr>
                                @if ($gst_applicable == true)
                                    <tr>
                                        <td style="padding: 5px 0;"><strong>GST Number</strong></td>
                                        <td style="padding: 5px 0; text-align: center;">:</td>
                                        <td style="padding: 5px 0;">{{ $gst_number ?? 'N/A' }}</td>
                                    </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- Payment information -->
                    <tr>
                        <td style="padding: 30px; background-color: #f9f9f9; border-radius: 8px; font-family: Arial, sans-serif;">
                            
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 50%; vertical-align: middle;">
                                        <h2 style="font-size: 18px; margin: 0; color: #004aad; border-bottom: 1px solid #eeeeee; padding-bottom: 8px; display: inline-block;">
                                            Payment Status
                                        </h2>
                                    </td>
                                    <td style="width: 50%; text-align: right; vertical-align: middle;">
                                        <p style="font-size: 16px; margin: 0; color: {{ $paymentStatus == 'paid' ? '#28a745' : '#d9534f' }}; border-bottom: 1px solid #eeeeee; padding-bottom: 8px; display: inline-block; font-weight: bold; display: inline-block;">
                                            {{ strtoupper($paymentStatus ?? 'Pending') }}
                                        </p>
                                    </td>
                                </tr>
                            </table>
                            @if(!empty($payment_method))
                                <p style="font-size: 14px; margin: 5px 0 0 0; color: #333;">
                                    <strong>Payment Method:</strong> {{ $payment_method }}
                                </p>
                            @endif
                            @if(!empty($payment_reference))
                                <p style="font-size: 14px; margin: 5px 0 0 0; color: #333;">
                                    <strong>Reference:</strong> {{ $payment_reference }}
                                </p>
                            @endif
                            @if(!empty($payment_Date))
                                <p style="font-size: 14px; margin: 5px 0 0 0; color: #333;">
                                    <strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($payment_Date)->format('d M Y, h:i A') }}
                                </p>
                            @endif
                        </td>
                    </tr>

                    <!-- Order summary -->
                    <tr>
                        <td style="padding: 0 30px 20px 30px">
                            <h2
                                style="
                                            font-size: 18px;
                                            margin: 0 0 15px 0;
                                        padding-bottom: 10px;
                                        border-bottom: 1px solid #eeeeee;
                                    ">
                                Order Summary
                            </h2>
                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="
                                        border-collapse: collapse;
                                        margin-bottom: 15px;
                                    ">
                                <thead>
                                    <tr style="background-color: #f5f5f5">
                                        <th
                                            style="
                                                    text-align: left;
                                                    padding: 10px;
                                                    border-bottom: 1px solid
                                                        #eeeeee;
                                                ">
                                            Item
                                        </th>
                                        <th
                                            style="
                                                    text-align: right;
                                                    padding: 10px;
                                                    border-bottom: 1px solid
                                                        #eeeeee;
                                                ">
                                            Price
                                        </th>
                                        <th
                                            style="
                                                    text-align: right;
                                                    padding: 10px;
                                                    border-bottom: 1px solid
                                                        #eeeeee;
                                                ">
                                            Units
                                        </th>
                                        <th
                                            style="
                                                    text-align: right;
                                                    padding: 10px;
                                                    border-bottom: 1px solid
                                                        #eeeeee;
                                                ">
                                            Total
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- //@dd($orderItems)     --}}
                                    @foreach ($orderItems as $item)
                                        <tr>
                                            <td
                                                style="
                                                                                            padding: 10px;
                                                                                            border-bottom: 1px solid
                                                                                                #eeeeee;
                                                                                        ">
                                                {{ $item['item_name'] }}
                                            </td>
                                            <td
                                                style="
                                                                                            text-align: right;
                                                                                            padding: 10px;
                                                                                            border-bottom: 1px solid
                                                                                                #eeeeee;
                                                                                        ">
                                                ₹ {{ number_format($item['unit_price'], 2) }}
                                            </td>
                                            <td
                                                style="
                                                                                            text-align: right;
                                                                                            padding: 10px;
                                                                                            border-bottom: 1px solid
                                                                                                #eeeeee;
                                                                                        ">
                                                {{ $item['quantity'] }}
                                            </td>
                                            <td
                                                style="
                                                                                            text-align: right;
                                                                                            padding: 10px;
                                                                                            border-bottom: 1px solid
                                                                                                #eeeeee;
                                                                                        ">
                                                ₹ {{ number_format($item['total_price'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach

                                    {{-- @php
                                            die;
                                        @endphp --}}
                                </tbody>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="50%"></td>
                                    <td width="50%">
                                        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="padding: 5px 0">
                                                    Subtotal:
                                                </td>
                                                <td
                                                    style="
                                                            text-align: right;
                                                            padding: 5px 0;
                                                        ">
                                                    ₹ {{ number_format($subtotal, 2) }}
                                                </td>
                                            </tr>

                                             @if($surcharge > 0)
                                            <tr>
                                                <td style="padding: 5px 0">
                                                    Surcharge ({{ $surcharge_percentage }}%):
                                                </td>
                                                <td
                                                    style="
                                                            text-align: right;
                                                            padding: 5px 0;
                                                        ">
                                                    ₹ {{ number_format($surcharge, 2) }}
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 5px 0">
                                                    Processing Charge:
                                                </td>
                                                <td
                                                    style="
                                                            text-align: right;
                                                            padding: 5px 0;
                                                        ">
                                                    ₹ {{ number_format($processingCharge, 2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 5px 0">
                                                    GST :
                                            
                                                </td>
                                                <td
                                                    style="
                                                            text-align: right;
                                                            padding: 5px 0;
                                                        ">
                                                    ₹ {{ number_format($gst, 2) }}
                                                </td>
                                            </tr>
                                            
                                            
                                           
                                            <tr>
                                                <td
                                                    style="
                                                            padding: 5px 0;
                                                            font-weight: bold;
                                                        ">
                                                    Order Total:
                                                </td>
                                                <td
                                                    style="
                                                            text-align: right;
                                                            padding: 5px 0;
                                                            font-weight: bold;
                                                        ">
                                                    ₹ {{ number_format($finalTotalPrice, 2) }}
                                                </td>
                                            </tr>
                                            @if ($currency == 'USD')
                                                <tr>
                                                    <td
                                                        style="
                                                            padding: 5px 0;
                                                            font-weight: bold;
                                                        ">
                                                        Order Total (In USD):
                                                    </td>
                                                    <td
                                                        style="
                                                            text-align: right;
                                                            padding: 5px 0;
                                                            font-weight: bold;
                                                        ">
                                                        $ {{ number_format($usdTotal, 2) }}
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td
                                                    style="
                                                            padding: 5px 0;
                                                            font-weight: bold;
                                                        ">
                                                    Total Received :
                                                </td>
                                                <td
                                                    style="
                                                            text-align: right;
                                                            padding: 5px 0;
                                                            font-weight: bold;
                                                        ">
                                                    {{ number_format($total_received, 2) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Contact information -->
                    <tr>
                        <td
                            style="
                                    padding: 20px 30px 0 30px;
                                    border-top: 1px solid #eeeeee;
                                ">
                            <h2 style="font-size: 18px; margin: 0 0 15px 0">
                                For Extra Requirement Enquiries
                            </h2>
                            <p style="margin: 0 0 5px 0">Please contact:</p>
                            <p style="margin: 10px 0 0 0">
                                <span
                                    style="
                                            font-weight: 500;
                                            display: block;
                                            margin-bottom: 5px;
                                        ">Nitin
                                    Chauhan</span>
                                <span
                                    style="
                                            font-weight: 500;
                                            display: block;
                                            margin-bottom: 5px;
                                        ">Email:
                                    nitin.chauhan@mmactiv.com</span>
                                {{-- <span
                                    style="
                                            font-weight: 500;
                                            display: block;
                                            margin-bottom: 5px;
                                        ">Phone:
                                    +91 98917 22025</span> --}}
                            </p>
                        </td>
                    </tr>


                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px; border-top: 1px solid #eeeeee;">
                            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <!-- Logo Left -->
                                    <td width="30%" valign="top" align="left">
                                        <img src="https://www.mmactiv.in/images/MMA.jpg" alt="MM Activ Logo"
                                            width="120" style="display: block; border: 0;">
                                    </td>
                                    <!-- Billing Info Right -->
                                    <td width="70%" valign="top" align="left"
                                        style="padding-left: 20px; font-size: 14px; color: #666666;">
                                        <p style="margin: 0 0 5px 0;"><strong>Billing Office:</strong> MM Activ
                                            Sci-Tech Communications Pvt. Ltd.</p>
                                        <p style="margin: 0 0 5px 0;"><strong>Billing Address:</strong> 103-104, Rohit
                                            House, 3,<br>
                                            Tolstoy Marg, Connaught Place,<br>
                                            New Delhi - 110 001</p>
                                        <p style="margin: 0 0 5px 0;"><strong>Website:</strong> <a
                                                href="https://www.mmactiv.in/"
                                                style="color: #0066cc; text-decoration: none;">www.mmactiv.in</a></p>
                                        <p style="margin: 0 0 5px 0;"><strong>Email:</strong> <a
                                                href="mailto:semiconindia@mmactiv.com"
                                                style="color: #0066cc; text-decoration: none;">semiconindia@mmactiv.com</a>
                                        </p>
                                        <p style="margin: 0;"><strong>GST No:</strong> 07AABCM2615H1ZS </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
