<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meeting Room Booking Confirmation - SEMICON India 2025</title>
</head>
<body style="background: #f8fdfc; font-family: 'Inter', Arial, sans-serif; color: #2d3a4a; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 16px rgba(44, 180, 166, 0.08); padding: 32px 32px 24px 32px;">
        <div style="text-align: center; margin-bottom: 32px;">
            <div style="text-align: center; margin: 5px 0; padding: 10px; background-color: #fff;">
                <img src="https://portal.semiconindia.org/asset/img/logos/logo.png" alt="SEMICON India Logo" style="max-width: 180px; display: block; margin: 0 auto 8px auto;">
                <div style="font-size: 1.1rem; font-weight: 600; margin-top: 4px;">SEMICON India 2025</div>
            </div>
            <h1 style="color: #2cb4a6; font-size: 2rem; margin: 0 0 8px 0; font-weight: 600;">Meeting Room Booking Confirmation</h1>
        </div>

        <div style="font-size: 1.1rem; font-weight: 600; color: #2cb4a6; margin-top: 24px; margin-bottom: 8px;">Booking Details</div>
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 24px;">
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Booking ID:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400;">#{{ $data['booking_id'] }}</td>
            </tr>
            @if(!empty($data['confirmation_date']))
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Confirmation Date:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400;">{{ $data['confirmation_date'] }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Exhibitor Name:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400;">{{ $data['exhibitor_name'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Booking Confirmation:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400;">{{ ucfirst($data['confirmation_status']) }}</td>
            </tr>
        </table>

        <div style="font-size: 1.1rem; font-weight: 600; color: #2cb4a6; margin-top: 24px; margin-bottom: 8px;">Room Details</div>
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 24px;">
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Room Type:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['room_type'] }}, {{ $data['room_location'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Booking Date:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['booking_date'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Time Slot:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['time_slot'] }} ({{ $data['duration'] }} hours)</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Capacity:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['capacity'] }} persons</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Room Features:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">• {{ $data['room_features'] }}</td>
            </tr>
        </table>

        <div style="font-size: 1.1rem; font-weight: 600; color: #2cb4a6; margin-top: 24px; margin-bottom: 8px;">Payment Information</div>
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 24px;">
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Payment Status:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #52f600; font-weight: 600; line-height: 1.5;">{{ ucfirst($data['payment_status']) }}</td>
            </tr>
            @if(!empty($data['transaction_id']))
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Transaction ID:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['transaction_id'] }}</td>
            </tr>
            @endif
            @if(!empty($data['payment_date']))
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Payment Date:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['payment_date'] }}</td>
            </tr>
            @endif
        </table>

        <div style="font-size: 1.1rem; font-weight: 600; color: #2cb4a6; margin-top: 24px; margin-bottom: 8px;">Company Billing Information</div>
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 24px;">
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Company Name:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['company_name'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Billing Address:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['billing_address'] }}<br>{{ $data['billing_address_line2'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">City:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['city'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">State:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['state'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Country:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['country'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Postal Code:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['postal_code'] }}</td>
            </tr>
            @if(!empty($data['gst_number']))
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">GST Number:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['gst_number'] }}</td>
            </tr>
            @endif
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Contact Person:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['contact_person'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Contact Email:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['contact_email'] }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #7a8b9c; font-weight: 600; width: 160px;">Contact Phone:</td>
                <td style="padding: 8px 0; vertical-align: top; font-size: 1rem; color: #2d3a4a; font-weight: 400; line-height: 1.5;">{{ $data['contact_phone'] }}</td>
            </tr>
        </table>

        <div style="font-size: 1.1rem; font-weight: 600; color: #2cb4a6; margin-top: 24px; margin-bottom: 8px;">Billing Details</div>
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold; color: #2cb4a6; border-bottom: 2px solid #e0f3f1; text-align: left;">Description</th>
                <th style="padding: 10px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold; color: #2cb4a6; border-bottom: 2px solid #e0f3f1; text-align: right;">Duration</th>
                <th style="padding: 10px; border: 1px solid #ddd; background-color: #f5f5f5; font-weight: bold; color: #2cb4a6; border-bottom: 2px solid #e0f3f1; text-align: right;">Amount (INR)</th>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; color: #2d3a4a; text-align: left;">{{ $data['room_type'] }} Meeting Room Rental</td>
                <td style="padding: 10px; border: 1px solid #ddd; color: #2d3a4a; text-align: right;">{{ $data['duration'] }}</td>
                <td style="padding: 10px; border: 1px solid #ddd; color: #2d3a4a; text-align: right;">{{ $data['final_price'] }}</td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px; border: 1px solid #ddd; color: #2d3a4a; text-align: right;">Subtotal:</td>
                <td style="padding: 10px; border: 1px solid #ddd; color: #2d3a4a; text-align: right;">{{ number_format($data['subtotal'], 2) ?? 0 }}</td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px; border: 1px solid #ddd; color: #2d3a4a; text-align: right;">GST (18%):</td>
                <td style="padding: 10px; border: 1px solid #ddd; color: #2d3a4a; text-align: right;">{{ number_format($data['gst'], 2) ?? 0 }}</td>
            </tr>
            <tr style="background-color: #f9f9f9;">
                <td colspan="2" style="padding: 10px; border: 1px solid #ddd; color: #2cb4a6; font-weight: 600; border-top: 2px solid #e0f3f1; text-align: right;"><strong>Total Amount:</strong></td>
                <td style="padding: 10px; border: 1px solid #ddd; color: #2cb4a6; font-weight: 600; border-top: 2px solid #e0f3f1; text-align: right;"><strong>{{ number_format($data['total_amount'], 2) ?? 0 }}</strong></td>
            </tr>
        </table>

        <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border-radius: 4px;">
            <p style="margin: 0 0 10px 0;"><strong>Payment Terms:</strong></p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Full payment must be made within 7 days of booking confirmation.</li>
                <li>Payment can be made via bank transfer to the following account:</li>
            </ul>
            <table cellpadding="0" cellspacing="0" style="margin-top: 10px; margin-left: 20px;">
                <tr>
                    <td style="padding: 5px; font-weight: bold; padding-right: 15px;">Bank Name:</td>
                    <td style="padding: 5px;">State Bank of India</td>
                </tr>
                <tr>
                    <td style="padding: 5px; font-weight: bold; padding-right: 15px;">Account Name:</td>
                    <td style="padding: 5px;">SEMICON India</td>
                </tr>
                <tr>
                    <td style="padding: 5px; font-weight: bold; padding-right: 15px;">Account Number:</td>
                    <td style="padding: 5px;">1234567890</td>
                </tr>
                <tr>
                    <td style="padding: 5px; font-weight: bold; padding-right: 15px;">IFSC Code:</td>
                    <td style="padding: 5px;">SBIN0001234</td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; color: #7a8b9c; font-size: 0.95rem; margin-top: 32px;">
            Thank you for booking with us.<br>
            <span style="color: #2cb4a6; font-weight: 600; font-size: 1.05rem;">{{ $data['organizer_team'] }}</span>
        </div>
    </div>
</body>
</html>
