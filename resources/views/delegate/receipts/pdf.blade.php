<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $receipt->receipt_no ?? 'N/A' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        .receipt-header {
            text-align: center;
            border-bottom: 2px solid #004aad;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .receipt-details {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #004aad;
            color: white;
        }
        .total-row {
            font-weight: bold;
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <h2>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</h2>
        <h3>Receipt</h3>
        <p>Receipt No: {{ $receipt->receipt_no ?? 'N/A' }}</p>
    </div>

    <div class="receipt-details">
        <h4>Order Information</h4>
        <table>
            <tr>
                <th>Order No:</th>
                <td>{{ $receipt->order->order_no ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Date:</th>
                <td>{{ $receipt->created_at->format('M d, Y h:i A') }}</td>
            </tr>
            <tr>
                <th>Company:</th>
                <td>{{ $receipt->registration->company_name ?? 'N/A' }}</td>
            </tr>
        </table>

        <h4>Order Items</h4>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipt->order->items ?? [] as $item)
                    <tr>
                        <td>{{ $item->ticketType->name ?? 'N/A' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unit_price, 2) }} {{ $receipt->order->registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                        <td>{{ number_format($item->subtotal, 2) }} {{ $receipt->order->registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">Total:</td>
                    <td>{{ number_format($receipt->order->total ?? 0, 2) }} {{ $receipt->order->registration->nationality === 'International' ? 'USD' : 'INR' }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</body>
</html>
