<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Semicon India 2025 - Invoice</title>
    <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f8f9fa;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8f9fa; padding: 20px;">
@php



@endphp
    <tr>
        <td align="center">
            <table width="600px" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; padding: 20px; border-radius: 10px; width: 600px; margin: 0 auto;">

            <tr>
                    <td align="center" style="padding-bottom: 20px;">
                        <img src="{{ asset('assets/images/logo.svg') }}" style="width: 100px; height: auto;" alt="Semicon India 2025">
                    </td>
                </tr>
                <tr>
                    <td align="center">
                        <h2 style="color: #333; margin: 0; font-size: 24px;">Proforma Invoice</h2>
                        <p style="color: #555; font-size: 14px; margin-top: 5px;">Invoice No: <strong>#{{$invoice->invoice_no}}</strong></p>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 20px;">
                        <table width="100%">
                            <tr>
                                <td width="50%" valign="top" style="font-size: 14px; color: #333; padding-right: 10px;">
                                    <strong>{{ config('constants.organizer.name') }}</strong><br>
                                    Email: <a href="mailto:{{ config('constants.organizer.email') }}" style="color: #007bff; text-decoration: none;">{{ config('constants.organizer.email') }}</a><br>
                                    Tel: {{ config('constants.organizer.phone') }}<br>
                                    Address: {{ config('constants.organizer.address') }}<br>
                                    Website: <a href="{{ config('constants.organizer.website') }}" style="color: #007bff; text-decoration: none;">{{ config('constants.organizer.website') }}</a>
                                    <br>
                                    GSTIN No: {{ config('constants.GSTIN') }}
                                </td>
                                <td width="50%" valign="top" style="font-size: 14px; color: #333; padding-left: 10px;">
                                    <strong>Billed to:</strong><br>
                                    {{$billing->contact_name}}<br>
                                    {{$billing->email}}<br>
                                    {{$billing->phone}}<br>
                                    {{$billing->address}}<br>
                                    {{$billing->city_id}}, {{$billing->state->name}}<br>
                                    {{$billing->country->name}}<br>
                                    @if(isset($applications->gst_no))
                                        GSTIN: {{$applications->gst_no}}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 20px;">
                        <table width="100%">
                            <tr>
                                <td width="50%"></td>
                                <td width="50%" align="right">
                                    <p style="margin: 0; font-size: 14px; color: #333;"><strong>Invoice Date:</strong> {{ $invoice->created_at->format('d/m/Y') }}</p>
                                    <p style="margin: 5px 0; font-size: 14px; color: #333;"><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($invoice->payment_due_date)->format('d/m/Y') }}</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 20px;">
                        <table width="100%" border="1" style="border-collapse: collapse; border-color: #ddd;">
                            <thead>
                            <tr>
                                <th style="width: 40%; padding: 8px; background-color: #f2f2f2;">Item</th>
                                <th style="width: 10%; padding: 8px; background-color: #f2f2f2;">Qty</th>
                                <th style="width: 25%; padding: 8px; background-color: #f2f2f2;">Rate</th>
                                <th style="width: 25%; padding: 8px; background-color: #f2f2f2;">Amount</th>

                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td style="padding: 8px;">{{$products['item']}}</td>
                                <td align="center" style="padding: 8px;">{{$products['quantity']}}</td>
                                <td align="right" style="padding: 8px;">{{$products['price']}}</td>
                                <td align="right" style="padding: 8px;">{{$products['price']}}</td>
                            </tr>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="2"></td>
                                <td align="right" style="padding: 8px;"><strong>GST</strong></td>
                                <td align="right" style="padding: 8px;">{{$applications->payment_currency}} {{$products['gst']}}</td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td align="right" style="padding: 8px;"><strong>Total</strong></td>
                                <td align="right" style="padding: 8px;">{{$applications->payment_currency}} {{$products['total']}}</td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <td align="right" style="padding: 8px;"><strong>Total Due</strong></td>
                                <td align="right" style="padding: 8px;">{{$applications->payment_currency}} {{$products['total']}}</td>
                            </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 20px; text-align: center;">
                        <h4 style="color: #333; font-size: 16px;">Thank you!</h4>
                        <p style="font-size: 14px; color: #555;">If you have any issues related to the invoice, contact us at:</p>
                        <p style="font-size: 14px; color: #007bff; margin: 5px 0;">
                            <a href="mailto:{{ config('constants.organizer.email') }}" style="color: #007bff; text-decoration: none;">{{ config('constants.organizer.email') }}</a>
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center" style="padding-top: 20px;">
            <button onclick="this.style.display='none'; window.print(); this.style.display='block';" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">Print Invoice</button>
        </td>
    </tr>
</table>
</body>
</html>
