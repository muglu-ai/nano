<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meeting Room Booking Confirmation - SEMICON India 2025</title>
    <link href="https://fonts.googleapis.com/css?family=Inter:400,600&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f8fdfc;
            font-family: 'Inter', Arial, sans-serif;
            color: #2d3a4a;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(44, 180, 166, 0.08);
            padding: 32px 32px 24px 32px;
        }
        .header {
            text-align: center;
            margin-bottom: 32px;
        }
        .header h1 {
            color: #2cb4a6;
            font-size: 2rem;
            margin: 0 0 8px 0;
            font-weight: 600;
        }
        .header p {
            color: #7a8b9c;
            font-size: 1rem;
            margin: 0;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2cb4a6;
            margin-top: 24px;
            margin-bottom: 8px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .details-table td {
            padding: 8px 0;
            vertical-align: top;
            font-size: 1rem;
        }
        .details-table .label {
            color: #7a8b9c;
            font-weight: 600;
            width: 160px;
        }
        .details-table .value {
            color: #2d3a4a;
            font-weight: 400;
        }
        .billing-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .billing-table th,
        .billing-table td {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .billing-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }

        .billing-table th:not(:first-child),
        .billing-table td:not(:first-child) {
            text-align: right;
        }
        .billing-table td[colspan] {
            text-align: left;
        }
        .details-table .value {
            line-height: 1.5;
        }
        .billing-table th {
            color: #2cb4a6;
            font-weight: 600;
            border-bottom: 2px solid #e0f3f1;
        }
        .billing-table td {
            color: #2d3a4a;
        }
        .billing-table .total-row td {
            font-weight: 600;
            color: #2cb4a6;
            border-top: 2px solid #e0f3f1;
        }
        .footer {
            text-align: center;
            color: #7a8b9c;
            font-size: 0.95rem;
            margin-top: 32px;
        }
        .org {
            color: #2cb4a6;
            font-weight: 600;
            font-size: 1.05rem;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            background-color: #f9f9f9;
        }
        .payment-note {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }

        .payment-note ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .bank-details {
            margin-top: 10px;
            margin-left: 20px;
        }

        .bank-details td {
            padding: 5px;
        }

        .bank-details .label {
            font-weight: bold;
            padding-right: 15px;
        }
        .logo-container {
            text-align: center;
            margin: 5px 0;
            padding: 10px;
            background-color: #fff;
        }
        .navbar-brand-img {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>


    <div class="container">
        <div class="header">
            <div class="logo-container">
    <img src="https://portal.semiconindia.org/asset/img/logos/logo.png" alt="SEMICON India Logo" class="navbar-brand-img" style="max-width:180px; display:block; margin:0 auto 8px auto;">
    <div style="font-size:1.1rem;  font-weight:600; margin-top:4px;">SEMICON India 2025</div>
    </div>
            <h1>Meeting Room Booking Confirmation</h1>
            {{-- <p>SEMICON India 2025</p> --}}
        </div>

        <div class="section-title">Booking Details</div>
        <table class="details-table">
            <tr>
                <td class="label">Booking ID:</td>
                <td class="value">#{{ $data['booking_id'] }}</td>
            </tr>
            @if(!empty($data['confirmation_date']))
            <tr>
                <td class="label">Confirmation Date:</td>
                <td class="value">{{ $data['confirmation_date'] }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Exhibitor Name:</td>
                <td class="value">{{ $data['exhibitor_name'] }}</td>
            </tr>
            <tr>
                <td class="label">Booking Confirmation:</td>
                <td class="value">{{ ucfirst($data['confirmation_status']) }}</td>
            </tr>
        </table>

        <div class="section-title">Room Details</div>
        <table class="details-table">
            <tr>
                <td class="label">Room Type:</td>
                <td class="value">{{ $data['room_type'] }}, {{ $data['room_location'] }}</td>
            </tr>
            <tr>
                <td class="label">Booking Date:</td>
                <td class="value">{{ $data['booking_date'] }}</td>
            </tr>
            <tr>
                <td class="label">Time Slot:</td>
                <td class="value">{{ $data['time_slot'] }} ({{ $data['duration'] }} hours)</td>
            </tr>
            <tr>
                <td class="label">Capacity:</td>
                <td class="value">{{ $data['capacity'] }} persons</td>
            </tr>
            <tr>
                <td class="label">Room Features:</td>
                <td class="value">
                    
                    • {{ $data['room_features'] }}<br>
                    
                </td>
            </tr>
        </table>

        <div class="section-title">Payment Information</div>
        <table class="details-table">
            <tr>
                <td class="label">Payment Status:</td>
                <td class="value" style="color: #52f600; font-weight: 600;">{{ ucfirst($data['payment_status']) }}</td>
            </tr>
            @if(!empty($data['transaction_id']))
            <tr>
                <td class="label">Transaction ID:</td>
                <td class="value">{{ $data['transaction_id'] }}</td>
            </tr>
            @endif
            @if(!empty($data['payment_date']))
            <tr>
                <td class="label">Payment Date:</td>
                <td class="value">{{ $data['payment_date'] }}</td>
            </tr>
            @endif
        </table>

        <div class="section-title">Company Billing Information</div>
        <table class="details-table">
            <tr>
                <td class="label">Company Name:</td>
                <td class="value">{{ $data['company_name'] }}</td>
            </tr>
            <tr>
                <td class="label">Billing Address:</td>
                <td class="value">{{ $data['billing_address'] }}<br>{{ $data['billing_address_line2'] }}</td>
            </tr>
            <tr>
                <td class="label">City:</td>
                <td class="value">{{ $data['city'] }}</td>
            </tr>
            <tr>
                <td class="label">State:</td>
                <td class="value">{{ $data['state'] }}</td>
            </tr>
            <tr>
                <td class="label">Country:</td>
                <td class="value">{{ $data['country'] }}</td>
            </tr>
            <tr>
                <td class="label">Postal Code:</td>
                <td class="value">{{ $data['postal_code'] }}</td>
            </tr>
            @if(!empty($data['gst_number']))
            <tr>
                <td class="label">GST Number:</td>
                <td class="value">{{ $data['gst_number'] }}</td>
            </tr>
            @endif
            <tr>
                <td class="label">Contact Person:</td>
                <td class="value">{{ $data['contact_person'] }}</td>
            </tr>
            <tr>
                <td class="label">Contact Email:</td>
                <td class="value">{{ $data['contact_email'] }}</td>
            </tr>
            <tr>
                <td class="label">Contact Phone:</td>
                <td class="value">{{ $data['contact_phone'] }}</td>
            </tr>
        </table>

        <div class="section-title">Billing Details</div>
        <table class="billing-table">
            <tr>
                <th>Description</th>
                <th>Duration</th>
                {{-- <th>Rate</th> --}}
                <th>Amount (INR)</th>
            </tr>
            <tr>
                <td>{{ $data['room_type'] }} Meeting Room Rental</td>
                <td>{{ $data['duration'] }} </td>
                <td>{{ $data['final_price'] }}</td>
            </tr>
            {{-- @if($data['additional_services_amount'] > 0) --}}
            {{-- <tr>
                <td>Additional Services</td>
                <td>-</td>
                <td>-</td>
                <td>{{ number_format($data['additional_services_amount'], 2) }}</td>
            </tr>
            @endif --}}
            <tr>
                <td colspan="2" class="text-right">Subtotal:</td>
                <td>{{ number_format($data['subtotal'], 2) ?? 0 }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-right">GST (18%):</td>
                <td>{{ number_format($data['gst'], 2) ?? 0 }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="2" class="text-right"><strong>Total Amount:</strong></td>
                <td><strong>{{ number_format($data['total_amount'], 2) ?? 0 }}</strong></td>
            </tr>
            {{-- Debugging info --}}
            {{-- <tr>
                <td colspan="3">
                    <pre>{{ print_r($data, true) }}</pre>
                </td>
            </tr> --}}
        </table>

        <div class="payment-note">
            <p><strong>Payment Terms:</strong></p>
            <ul>
            <li>Full payment must be made within 7 days of booking confirmation.</li>
            <li>Payment can be made via bank transfer to the following account:</li>
            </ul>
            <table class="bank-details">
            <tr>
                <td class="label">Bank Name:</td>
                <td class="value">State Bank of India</td>
            </tr>
            <tr>
                <td class="label">Account Name:</td>
                <td class="value">SEMICON India </td>
            </tr>
            <tr>
                <td class="label">Account Number:</td>
                <td class="value">1234567890</td>
            </tr>
            <tr>
                <td class="label">IFSC Code:</td>
                <td class="value">SBIN0001234</td>
            </tr>
            </table>
        </div>

        <div class="footer">
            Thank you for booking with us.<br>
            <span class="org">{{ $data['organizer_team'] }}</span>
        </div>
    </div>
</body>
</html>
