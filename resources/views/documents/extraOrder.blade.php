<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation - Semicon India 2025</title>
    <style>
        @page {
            size: A3;
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            color: #333;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }
        h1, h2, h4 {
            margin: 0 0 10px 0;
        }
        .section {
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>

    <table width="100%">
        <tr>
            <td align="center">
                <img src="https://portal.semiconindia.org/asset/img/logos/logo.png" width="120">
                <h4>{{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}</h4>
                <h1>Proforma Invoice</h1>
                <p>Thank you for ordering extra requirement items at {{ config('constants')['EVENT_NAME'] }} {{ config('constants')['EVENT_YEAR'] }}</p>
            </td>
        </tr>
    </table>

    <div class="section">
        <table class="table">
            <tr>
                <td><strong>Order Number:</strong> {{ $invoice_Id }}</td>
                <td class="text-right"><strong>Order Date:</strong> {{ $order_date }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <p><strong>Note:</strong></p>
        <p><em>Your items will be available for pickup at the registration desk on the first day of the event. Please bring your order confirmation.</em></p>
    </div>

    <div class="section">
        <h2>Exhibitor Information</h2>
        <table class="table">
            <tr><td class="bold">Exhibitor Name</td><td>{{ $exhibitor_name }}</td></tr>
            <tr><td class="bold">Booth No</td><td>{{ $booth_no }}</td></tr>
            <tr><td class="bold">Stall Size</td><td>{{ $stall_size }} SQM</td></tr>
        </table>
    </div>

    <div class="section">
        <h2>Billing Information</h2>
        <table class="table">
            <tr><td class="bold">Company</td><td>{{ $billingCompany }}</td></tr>
            <tr><td class="bold">Contact Name</td><td>{{ $billingContactName }}</td></tr>
            <tr><td class="bold">Email</td><td>{{ $billingEmail }}</td></tr>
            <tr><td class="bold">Contact Number</td><td>{{ $billingPhone }}</td></tr>
            <tr><td class="bold">Address</td><td>{{ $billingAddress }}</td></tr>
            <tr><td class="bold">GST Compliance</td><td>{{ $gst_applicable ? 'Yes' : 'No' }}</td></tr>
            @if ($gst_applicable)
                <tr><td class="bold">GST Number</td><td>{{ $gst_number ?? 'N/A' }}</td></tr>
            @endif
        </table>
    </div>

    <div class="section">
        <h2>Payment Status</h2>
        <p class="bold" style="color: {{ $paymentStatus == 'paid' ? '#28a745' : '#d9534f' }};">
            {{ strtoupper($paymentStatus ?? 'Pending') }}
        </p>
        @if(!empty($payment_method))
            <p><strong>Payment Method:</strong> {{ $payment_method }}</p>
        @endif
        @if(!empty($payment_reference))
            <p><strong>Reference:</strong> {{ $payment_reference }}</p>
        @endif
        @if(!empty($payment_Date))
            <p><strong>Payment Date:</strong> {{ \Carbon\Carbon::parse($payment_Date)->format('d M Y, h:i A') }}</p>
        @endif
    </div>

    <div class="section">
        <h2>Order Summary</h2>
        <table class="table">
            <thead>
                <tr style="background-color: #f5f5f5;">
                    <th>Item</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Units</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orderItems as $item)
                <tr>
                    <td>{{ $item['item_name'] }}</td>
                    <td class="text-right">Rs. {{ number_format($item['unit_price'], 2) }}</td>
                    <td class="text-right">{{ $item['quantity'] }}</td>
                    <td class="text-right">Rs. {{ number_format($item['total_price'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table width="100%">
            <tr>
                <td width="50%"></td>
                <td width="50%">
                    <table width="100%">
                        <tr><td>Subtotal:</td><td class="text-right">Rs. {{ number_format($subtotal, 2) }}</td></tr>
                        @if($surcharge > 0)
                        <tr><td>Surcharge ({{ $surcharge_percentage }}%):</td><td class="text-right">Rs. {{ number_format($surcharge, 2) }}</td></tr>
                        @endif
                        <tr><td>Processing Charge:</td><td class="text-right">Rs. {{ number_format($processingCharge, 2) }}</td></tr>
                        <tr><td>GST:</td><td class="text-right">Rs. {{ number_format($gst, 2) }}</td></tr>
                       
                        
                        
                       
                        <tr class="bold"><td>Order Total:</td><td class="text-right">Rs. {{ number_format($finalTotalPrice, 2) }}</td></tr>
                        @if ($currency == 'USD')
                        <tr class="bold"><td>Order Total (USD):</td><td class="text-right">$ {{ number_format($usdTotal, 2) }}</td></tr>
                        @endif
                        <tr class="bold"><td>Total Received:</td><td class="text-right">{{ $currency }} {{ number_format($total_received, 2) }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>For Extra Requirement Enquiries</h2>
        <p><strong>Nitin Chauhan</strong><br>
        Email: nitin.chauhan@mmactiv.com</p>
    </div>



    <div class="section" style="margin-bottom: 50px;">
        <table width="100%">
            <tr>
                <td width="30%">
                    <img src="https://www.mmactiv.in/images/MMA.jpg" width="100">
                </td>
                <td width="70%" style="font-size: 13px;">
                    <p><strong>Billing Office:</strong> MM Activ Sci-Tech Communications Pvt. Ltd.</p>
                    <p><strong>Billing Address:</strong> 103-104, Rohit House, 3, Tolstoy Marg, Connaught Place, New Delhi - 110 001</p>
                    <p><strong>Website:</strong> www.mmactiv.in</p>
                    <p><strong>Email:</strong> semiconindia@mmactiv.com</p>
                    <p><strong>GST No:</strong> 07AABCM2615H1ZS</p>
                </td>
            </tr>
        </table>
    </div>

    <br>
     <div class="section" style="border-top: 1px solid #ccc; padding-top: 10px; margin-top: 20px;">
        <h2 style="margin-top: 20px;">Bank Details for Wire Transfer</h2>
        <table width="100%">
            <tr>
                <td width="50%" valign="top" style="padding-right: 10px;">
                    <h4>INDIAN</h4>
                    <table class="table">
                        <tr><td class="bold">Account Name</td><td>MM ACTIV SCI TECH COMMUNICATIONS PVT LTD</td></tr>
                        <tr><td class="bold">Account Type</td><td>Current Account</td></tr>
                        <tr><td class="bold">Account Number</td><td>04272560002067</td></tr>
                        <tr><td class="bold">Bank Name</td><td>HDFC BANK LTD. PUNE - LAXMI ROAD</td></tr>
                        {{-- <tr><td class="bold">DP Code No.</td><td>????</td></tr> --}}
                        <tr><td class="bold">Bank Address</td>
                            <td>
                                SHOP NO 3,4,5 & 10, ADITI APARTMENTS, 344/1 NARAYAN PETH<br>
                                NEAR MODI GANAPATI, OFF LAXMI ROAD,<br>
                                PUNE-411030, MAHARASHTRA, INDIA
                            </td>
                        </tr>
                        <tr><td class="bold">Bank IFSC Code</td><td>HDFC0000427</td></tr>
                        <tr><td class="bold">Bank MICR Code</td><td>411240012</td></tr>
                    </table>
                </td>
                <td width="50%" valign="top" style="padding-left: 10px;">
                    <h4>INTERNATIONAL</h4>
                    <table class="table">
                        <tr><td class="bold">Account Name</td><td>MM ACTIV SCI-TECH COMMUNICATIONS PVT.LTD.</td></tr>
                        <tr><td class="bold">Account Type</td><td>Current Account</td></tr>
                        <tr><td class="bold">Account Number</td><td>04272560002067</td></tr>
                        <tr><td class="bold">Bank Name</td><td>HDFC BANK LTD. PUNE - LAXMI ROAD</td></tr>
                        {{-- <tr><td class="bold">DP Code No.</td><td>????</td></tr> --}}
                        <tr><td class="bold">Bank Address</td>
                            <td>
                                SHOP NO 3,4,5 & 10, ADITI APARTMENTS, 344/1 NARAYAN PETH<br>
                                NEAR MODI GANAPATI, OFF LAXMI ROAD,<br>
                                PUNE-411030, MAHARASHTRA, INDIA
                            </td>
                        </tr>
                        <tr><td class="bold">Bank SWIFT Code</td><td>HDFCINBB</td></tr>
                        <tr><td class="bold">Bank MICR Code</td><td>411240012</td></tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
