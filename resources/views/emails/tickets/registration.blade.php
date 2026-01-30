<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Payment Confirmation - {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</title>
    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, Helvetica, sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.5; color: #333333;">
@php
    $isInternational = ($order->registration->nationality === 'International' || $order->registration->nationality === 'international');
    $currencySymbol = $isInternational ? '$' : '₹';
    $priceFormat = $isInternational ? 2 : 0;
    $primaryColor = '#0066cc';
    $successColor = '#28a745';
    $warningColor = '#ffc107';
    $warningBg = '#fff3cd';
    $successBg = '#d4edda';
@endphp

<!-- Wrapper Table -->
<table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f4f4f4;">
    <tr>
        <td align="center" style="padding: 20px 10px;">
            
            <!-- Main Container -->
            <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                
                <!-- Header with Logo -->
                <tr>
                    <td style="padding: 0; background-color: #ffffff;">
                        @if(config('constants.EMAILER_HEADER_LOGO'))
                        <img src="{{ config('constants.EMAILER_HEADER_LOGO') }}" alt="{{ config('constants.EVENT_NAME') }}" style="width: 100%; max-width: 600px; height: auto; display: block;">
                        @endif
                    </td>
                </tr>

                <!-- Receipt Type Badge -->
                <tr>
                    <td style="padding: 15px 20px; background-color: {{ $order->status === 'paid' ? $successBg : $warningBg }}; border-bottom: 3px solid {{ $order->status === 'paid' ? $successColor : $warningColor }};">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                            <tr>
                                <td>
                                    <span style="display: inline-block; padding: 8px 16px; background-color: {{ $order->status === 'paid' ? $successColor : '#856404' }}; color: #ffffff; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; border-radius: 4px;">
                                        @if($order->status === 'paid')
                                            ✓ CONFIRMATION RECEIPT
                                        @else
                                            ⏳ PROVISIONAL RECEIPT
                                        @endif
                                    </span>
                                </td>
                                @if($order->status !== 'paid')
                                <td style="text-align: right;">
                                    <a href="{{ route('tickets.payment.by-tin', ['eventSlug' => $event->slug ?? $event->id, 'tin' => $order->order_no]) }}" style="display: inline-block; padding: 10px 20px; background-color: #ffc107; color: #333333; text-decoration: none; font-size: 13px; font-weight: 700; border-radius: 5px;">
                                        💳 PAY NOW
                                    </a>
                                </td>
                                @endif
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Greeting -->
                <tr>
                    <td style="padding: 25px 20px 15px 20px;">
                        <p style="margin: 0 0 10px 0; font-size: 16px; color: #333333;">
                            Dear <strong>{{ $order->registration->contact->name ?? 'Valued Customer' }}</strong>,
                        </p>
                        <p style="margin: 0; font-size: 14px; color: #555555;">
                            Thank you for registering for <strong style="color: {{ $primaryColor }};">{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>.
                        </p>
                    </td>
                </tr>

                <!-- Alert Box -->
                <tr>
                    <td style="padding: 0 20px 20px 20px;">
                        @if($order->status !== 'paid')
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: {{ $warningBg }}; border-left: 4px solid {{ $warningColor }}; border-radius: 4px;">
                            <tr>
                                <td style="padding: 12px 15px;">
                                    <p style="margin: 0; font-size: 13px; color: #856404;">
                                        <strong>⚠️ Action Required:</strong> Please complete the payment to confirm your registration.
                                    </p>
                                </td>
                            </tr>
                        </table>
                        @else
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: {{ $successBg }}; border-left: 4px solid {{ $successColor }}; border-radius: 4px;">
                            <tr>
                                <td style="padding: 12px 15px;">
                                    <p style="margin: 0; font-size: 13px; color: #155724;">
                                        <strong>✓ Payment Confirmed:</strong> Your registration has been confirmed. Thank you!
                                    </p>
                                </td>
                            </tr>
                        </table>
                        @endif
                    </td>
                </tr>

                <!-- Registration Information Section -->
                <tr>
                    <td style="padding: 0 20px 20px 20px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; overflow: hidden;">
                            <!-- Section Header -->
                            <tr>
                                <td colspan="2" style="padding: 12px 15px; background-color: {{ $primaryColor }}; color: #ffffff; font-size: 14px; font-weight: 700;">
                                    📋 Registration Information
                                </td>
                            </tr>
                            @php
                                $invoice = \App\Models\Invoice::where('invoice_no', $order->order_no)
                                    ->where('type', 'ticket_registration')
                                    ->first();
                                $pinNo = $invoice->pin_no ?? null;
                            @endphp
                            <!-- Info Rows -->
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666; width: 40%;">Date</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; font-weight: 600;">{{ $order->created_at->format('d-m-Y') }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">TIN No.</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: {{ $primaryColor }}; font-weight: 700;">{{ $order->order_no }}</td>
                            </tr>
                            @if($order->status === 'paid' && $pinNo)
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">PIN No.</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: {{ $primaryColor }}; font-weight: 700;">{{ $pinNo }}</td>
                            </tr>
                            @endif
                            <tr style="background-color: {{ $order->status === 'paid' ? $successBg : $warningBg }};">
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: {{ $order->status === 'paid' ? '#155724' : '#856404' }};">Payment Status</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef;">
                                    <span style="display: inline-block; padding: 4px 10px; background-color: {{ $order->status === 'paid' ? $successColor : $warningColor }}; color: {{ $order->status === 'paid' ? '#ffffff' : '#333333' }}; font-size: 11px; font-weight: 700; text-transform: uppercase; border-radius: 3px;">
                                        {{ $order->status === 'paid' ? '✓ PAID' : '⏳ PENDING' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">Category</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; font-weight: 600;">{{ $order->registration->registrationCategory->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">Ticket Type</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; font-weight: 600;">{{ $order->items->first()->ticketType->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">No. of Delegates</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; font-weight: 600;">{{ $order->items->sum('quantity') }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; font-size: 13px; color: #666666;">Currency</td>
                                <td style="padding: 10px 15px; font-size: 13px; color: #333333; font-weight: 600;">{{ $isInternational ? 'USD ($)' : 'INR (₹)' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Organisation/Individual Information -->
                <tr>
                    <td style="padding: 0 20px 20px 20px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td colspan="2" style="padding: 12px 15px; background-color: {{ $primaryColor }}; color: #ffffff; font-size: 14px; font-weight: 700;">
                                    {{ ($order->registration->registration_type ?? 'Organisation') === 'Individual' ? '👤 Individual' : '🏢 Organisation' }} Information
                                </td>
                            </tr>
                            @if(($order->registration->registration_type ?? 'Organisation') === 'Organisation')
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666; width: 40%;">Organisation</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; font-weight: 600;">{{ $order->registration->company_name ?? 'N/A' }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">Industry Sector</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333;">{{ $order->registration->industry_sector ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">Country</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333;">{{ $order->registration->company_country }}</td>
                            </tr>
                            @if($order->registration->company_state)
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">State</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333;">{{ $order->registration->company_state }}</td>
                            </tr>
                            @endif
                            @if($order->registration->company_city)
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">City</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333;">{{ $order->registration->company_city }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding: 10px 15px; font-size: 13px; color: #666666;">Phone</td>
                                <td style="padding: 10px 15px; font-size: 13px; color: #333333;">{{ $order->registration->company_phone }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- GST Information (if required) -->
                @if($order->registration->gst_required)
                <tr>
                    <td style="padding: 0 20px 20px 20px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td colspan="2" style="padding: 12px 15px; background-color: {{ $primaryColor }}; color: #ffffff; font-size: 14px; font-weight: 700;">
                                    🧾 GST / Invoice Details
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666; width: 40%;">Legal Name</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; font-weight: 600;">{{ $order->registration->gst_legal_name ?? $order->registration->company_name }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">GSTIN</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; font-weight: 600;">{{ $order->registration->gstin ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; font-size: 13px; color: #666666;">Address</td>
                                <td style="padding: 10px 15px; font-size: 13px; color: #333333;">{{ $order->registration->gst_address ?? '-' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif

                <!-- Delegate Details - Card Style for Mobile -->
                @if($order->registration->delegates && $order->registration->delegates->count() > 0)
                @php 
                    $ticketTypeObj = $order->items->first()->ticketType ?? null;
                    $ticketTypeName = $ticketTypeObj->name ?? 'N/A';
                    $categoryName = $ticketTypeObj->category->name ?? null;
                    $subcategoryName = $ticketTypeObj->subcategory->name ?? null;
                @endphp
                <tr>
                    <td style="padding: 0 20px 20px 20px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td style="padding: 12px 15px; background-color: {{ $primaryColor }}; color: #ffffff; font-size: 14px; font-weight: 700;">
                                    👥 Delegate Details
                                </td>
                            </tr>
                            @foreach($order->registration->delegates as $delegate)
                            <tr>
                                <td style="padding: 15px; {{ !$loop->last ? 'border-bottom: 2px solid #e9ecef;' : '' }}">
                                    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%">
                                        <tr>
                                            <td style="padding-bottom: 8px;">
                                                <span style="display: inline-block; padding: 3px 10px; background-color: {{ $primaryColor }}; color: #ffffff; font-size: 11px; font-weight: 700; border-radius: 3px; margin-right: 8px;">
                                                    #{{ $loop->iteration }}
                                                </span>
                                                <span style="font-size: 15px; font-weight: 700; color: #333333;">
                                                    {{ $delegate->salutation }} {{ $delegate->first_name }} {{ $delegate->last_name }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 4px 0; font-size: 13px; color: #555555;">
                                                📧 {{ $delegate->email }}
                                            </td>
                                        </tr>
                                        @if($delegate->phone)
                                        <tr>
                                            <td style="padding: 4px 0; font-size: 13px; color: #555555;">
                                                📱 {{ $delegate->phone }}
                                            </td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td style="padding-top: 8px;">
                                                @if($categoryName)
                                                <span style="display: inline-block; padding: 3px 8px; background-color: #e9ecef; color: #495057; font-size: 11px; border-radius: 3px; margin-right: 5px;">
                                                    {{ $categoryName }}
                                                </span>
                                                @endif
                                                @if($subcategoryName)
                                                <span style="display: inline-block; padding: 3px 8px; background-color: #e9ecef; color: #495057; font-size: 11px; border-radius: 3px; margin-right: 5px;">
                                                    {{ $subcategoryName }}
                                                </span>
                                                @endif
                                                <span style="display: inline-block; padding: 3px 8px; background-color: {{ $primaryColor }}; color: #ffffff; font-size: 11px; border-radius: 3px;">
                                                    {{ $ticketTypeName }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                    </td>
                </tr>
                @endif

                <!-- Price Breakdown -->
                <tr>
                    <td style="padding: 0 20px 20px 20px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td colspan="2" style="padding: 12px 15px; background-color: {{ $primaryColor }}; color: #ffffff; font-size: 14px; font-weight: 700;">
                                    💰 Price Breakdown
                                </td>
                            </tr>
                            @foreach($order->items as $item)
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">
                                    Ticket Price ({{ $item->quantity }} × {{ $currencySymbol }}{{ number_format($item->unit_price, $priceFormat) }})
                                </td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; font-weight: 600; text-align: right;">
                                    {{ $currencySymbol }}{{ number_format($item->subtotal, $priceFormat) }}
                                </td>
                            </tr>
                            @if($order->discount_amount > 0 && $order->promoCode)
                            <tr style="background-color: {{ $successBg }};">
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #155724;">
                                    🏷️ Promocode Discount
                                    @if($order->promoCode->type === 'percentage')
                                    <br><span style="font-size: 11px;">({{ number_format($order->promoCode->value, 0) }}% off)</span>
                                    @endif
                                </td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #155724; font-weight: 600; text-align: right;">
                                    -{{ $currencySymbol }}{{ number_format($order->discount_amount, $priceFormat) }}
                                </td>
                            </tr>
                            @endif
                            @if($item->gst_type === 'cgst_sgst')
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">CGST ({{ number_format($item->cgst_rate ?? 0, 0) }}%)</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; text-align: right;">{{ $currencySymbol }}{{ number_format($item->cgst_amount ?? 0, $priceFormat) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">SGST ({{ number_format($item->sgst_rate ?? 0, 0) }}%)</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; text-align: right;">{{ $currencySymbol }}{{ number_format($item->sgst_amount ?? 0, $priceFormat) }}</td>
                            </tr>
                            @else
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">IGST ({{ number_format($item->igst_rate ?? 0, 0) }}%)</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; text-align: right;">{{ $currencySymbol }}{{ number_format($item->igst_amount ?? 0, $priceFormat) }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">Processing Charge ({{ $item->processing_charge_rate }}%)</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333; text-align: right;">{{ $currencySymbol }}{{ number_format($item->processing_charge_amount, $priceFormat) }}</td>
                            </tr>
                            @endforeach
                            <!-- Total Row -->
                            <tr>
                                <td style="padding: 15px; background-color: {{ $primaryColor }}; font-size: 16px; color: #ffffff; font-weight: 700;">
                                    Total Amount
                                </td>
                                <td style="padding: 15px; background-color: {{ $primaryColor }}; font-size: 16px; color: #ffffff; font-weight: 700; text-align: right;">
                                    {{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Pay Now Button (for pending) -->
                @if($order->status !== 'paid')
                <tr>
                    <td style="padding: 0 20px 25px 20px; text-align: center;">
                        <a href="{{ route('tickets.payment.by-tin', ['eventSlug' => $event->slug ?? $event->id, 'tin' => $order->order_no]) }}" style="display: inline-block; padding: 15px 40px; background-color: #ffc107; color: #333333; text-decoration: none; font-size: 16px; font-weight: 700; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                            💳 Pay Now - {{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}
                        </a>
                        <p style="margin: 12px 0 0 0; font-size: 12px; color: #888888;">
                            Click the button above to complete your payment securely.
                        </p>
                    </td>
                </tr>
                @else
                <!-- Payment Transaction Details (for paid) -->
                @php
                    $payment = \App\Models\Payment::where('order_id', $order->order_no)
                        ->where('status', 'successful')
                        ->latest()
                        ->first();
                @endphp
                @if($payment)
                <tr>
                    <td style="padding: 0 20px 20px 20px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td colspan="2" style="padding: 12px 15px; background-color: {{ $successColor }}; color: #ffffff; font-size: 14px; font-weight: 700;">
                                    🧾 Payment Transaction Details
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666; width: 40%;">Payment Method</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #333333;">{{ $payment->payment_method ?? 'Online' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">Transaction ID</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: {{ $primaryColor }}; font-weight: 700;">{{ $payment->transaction_id ?? $payment->track_id ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #666666;">Amount Paid</td>
                                <td style="padding: 10px 15px; border-bottom: 1px solid #e9ecef; font-size: 13px; color: #155724; font-weight: 700;">{{ $currencySymbol }}{{ number_format($payment->amount_paid ?? $payment->amount ?? $order->total, $priceFormat) }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 10px 15px; font-size: 13px; color: #666666;">Payment Date</td>
                                <td style="padding: 10px 15px; font-size: 13px; color: #333333;">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y, h:i A') : 'N/A' }}</td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif

                <!-- Success Message -->
                <tr>
                    <td style="padding: 0 20px 25px 20px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: {{ $successBg }}; border-radius: 8px; text-align: center;">
                            <tr>
                                <td style="padding: 20px;">
                                    <p style="margin: 0 0 5px 0; font-size: 18px; font-weight: 700; color: #155724;">
                                        @if($order->payment_status === 'complimentary')
                                            🎁 Complimentary Registration Confirmed
                                        @else
                                            ✓ Payment Completed Successfully
                                        @endif
                                    </p>
                                    <p style="margin: 0; font-size: 13px; color: #155724;">
                                        Your registration is confirmed. You will receive further communication regarding the event.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif

                <!-- Secretariat Information -->
                <tr>
                    <td style="padding: 0 20px 20px 20px;">
                        <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="background-color: #f8f9fa; border-radius: 8px; overflow: hidden;">
                            <tr>
                                @if(config('constants.organizer_logo'))
                                <td style="padding: 15px; width: 100px; text-align: center; vertical-align: middle;">
                                    <img src="{{ config('constants.organizer_logo') }}" alt="{{ config('constants.organizer.name') }}" style="width: 80px; height: 80px; object-fit: contain;">
                                </td>
                                @endif
                                <td style="padding: 15px; vertical-align: middle;">
                                    <p style="margin: 0 0 5px 0; font-size: 13px; font-weight: 700; color: #333333;">{{ config('constants.EVENT_NAME') }} Secretariat</p>
                                    <p style="margin: 0 0 3px 0; font-size: 12px; color: #666666;"><strong>{{ config('constants.organizer.name') }}</strong></p>
                                    <p style="margin: 0 0 3px 0; font-size: 12px; color: #666666;">📞 {{ config('constants.organizer.phone') }}</p>
                                    <p style="margin: 0; font-size: 12px; color: #666666;">✉️ <a href="mailto:{{ config('constants.organizer.email') }}" style="color: {{ $primaryColor }}; text-decoration: none;">{{ config('constants.organizer.email') }}</a></p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="padding: 20px; background-color: #333333; text-align: center;">
                        <p style="margin: 0 0 5px 0; font-size: 11px; color: #999999;">
                            This is an automated email. Please do not reply to this message.
                        </p>
                        <p style="margin: 0; font-size: 11px; color: #999999;">
                            &copy; {{ date('Y') }} {{ config('constants.organizer.name') }}. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
            <!-- End Main Container -->

        </td>
    </tr>
</table>
<!-- End Wrapper -->

</body>
</html>
