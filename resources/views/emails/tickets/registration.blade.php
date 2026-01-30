<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation - {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</title>
</head>
<body style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.5; color: #333333; max-width: 650px; margin: 0 auto; padding: 10px; background-color: #f5f5f5; font-size: 14px;">
    @php
        $isInternational = ($order->registration->nationality === 'International' || $order->registration->nationality === 'international');
        $currencySymbol = $isInternational ? '$' : '₹';
        $priceFormat = $isInternational ? 2 : 0;
    @endphp

    <div style="background: #ffffff; border-radius: 0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #ffffff; border-bottom: 2px solid #e0e0e0;">
            <tr>
                <td style="padding: 2px 2px; width: 100%;">
                    @if(config('constants.EMAILER_HEADER_LOGO'))
                    <img src="{{ config('constants.EMAILER_HEADER_LOGO') }}" alt="{{ config('constants.EVENT_NAME') }}" style="max-width: 100%; height: auto; max-height: 100px;">
                    @endif
                </td>
            </tr>
        </table>

        <!-- Receipt Header -->
        <table width="100%" cellpadding="0" cellspacing="0" style="background: #f5f5f5; border-bottom: 1px solid #e0e0e0;">
            <tr>
                <td style="padding: 10px 15px;">
                    <span style="background: #ffffff; color: #333333; padding: 5px 12px; display: inline-block; font-weight: 700; font-size: 12px; border: 1px solid #d0d0d0; text-transform: uppercase; letter-spacing: 0.5px;">
                    @if($order->status === 'paid')
                        ✓ CONFIRMATION RECEIPT
                    @else
                        ⏳ PROVISIONAL RECEIPT
                    @endif
                    </span>
                </td>
                <td style="padding: 5px 10px; text-align: right; font-size: 10px; color: #666666;">
                    @if($order->status !== 'paid')
                    <div style="text-align: center; margin: 7px 0;">
                        <a href="{{ route('tickets.payment.lookup', ['eventSlug' => $event->slug ?? $event->id, 'tin' => $order->order_no]) }}" style="display: inline-block; background: #DAA520; color: #ffffff; padding: 10px; text-decoration: none; border-radius: 5px; font-weight: 700; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px;">
                            💳 Pay Now - {{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}
                        </a>
                    </div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Content -->
        <div style="padding: 15px 18px;">
            <p style="font-size: 14px; margin-bottom: 10px;">Dear <strong>{{ $order->registration->contact->name ?? 'Valued Customer' }}</strong>,</p>
            
            <p style="font-size: 14px; margin-bottom: 12px;">Thank you for registering for <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>.</p>

            <!-- Alert -->
            @if($order->status !== 'paid')
            <div style="background: #fff3cd; border: 1px solid #ffc107; border-left: 4px solid #ffc107; padding: 12px 15px; margin: 10px 0; font-size: 13px; color: #856404;">
                <strong>⚠️ Action Required:</strong> Please complete the payment to confirm your registration.
            </div>
            @else
            <div style="background: #d4edda; border: 1px solid #28a745; border-left: 4px solid #28a745; padding: 12px 15px; margin: 10px 0; font-size: 13px; color: #155724;">
                <strong>✓ Payment Confirmed:</strong> Your registration has been confirmed. Thank you for your payment!
            </div>
            @endif

            <!-- Registration Information -->
            <div style="color: #333333; font-size: 15px; font-weight: 700; margin: 15px 0 8px 0; padding-bottom: 6px; border-bottom: 2px solid #0066cc;">📋 Registration Information</div>
            @php
                $invoice = \App\Models\Invoice::where('invoice_no', $order->order_no)
                    ->where('type', 'ticket_registration')
                    ->first();
                $pinNo = $invoice->pin_no ?? null;
            @endphp
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 10px;">
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Date:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->created_at->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">TIN NO:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->order_no }}</td>
                </tr>
                @if($order->status === 'paid' && $pinNo)
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">PIN NO:</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%; font-weight: 700; color: #0066cc;">{{ $pinNo }}</td>
                </tr>
                @endif
                <tr style="background: {{ $order->status === 'paid' ? '#d4edda' : '#fff3cd' }};">
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; font-weight: 600; color: {{ $order->status === 'paid' ? '#155724' : '#856404' }}; width: 40%;">Payment Status</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; width: 60%;">
                        <span style="display: inline-block; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; background: {{ $order->status === 'paid' ? '#28a745' : '#ffc107' }}; color: {{ $order->status === 'paid' ? '#ffffff' : '#333333' }};">
                            {{ $order->status === 'paid' ? '✓ PAID' : '⏳ PENDING' }}
                        </span>
                    </td>
                </tr>

                {{-- <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Registration Category</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->registrationCategory->name ?? 'N/A' }}</td>
                </tr> --}}
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Ticket Type</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->items->first()->ticketType->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Day Access</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">
                        @php
                            $firstItem = $order->items->first();
                            $selectedDay = $firstItem && $firstItem->selected_event_day_id ? $firstItem->selectedDay : null;
                            $ticketType = $firstItem ? $firstItem->ticketType : null;
                        @endphp
                        @if($selectedDay)
                            {{ $selectedDay->label }} ({{ \Carbon\Carbon::parse($selectedDay->date)->format('M d, Y') }})
                        @elseif($ticketType && ($ticketType->all_days_access || ($ticketType->enable_day_selection && $ticketType->include_all_days_option && !$firstItem->selected_event_day_id)))
                            All 3 Days
                        @elseif($ticketType)
                            @php $accessibleDays = $ticketType->getAllAccessibleDays(); @endphp
                            @if($accessibleDays->count() > 0)
                                {{ $accessibleDays->pluck('label')->implode(', ') }}
                            @else
                                All 3 Days
                            @endif
                        @else
                            All 3 Days
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Number of Delegates</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->items->sum('quantity') }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Currency</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->nationality === 'International' ? 'USD ($)' : 'INR (₹)' }}</td>
                </tr>
            </table>

            <!-- Organisation/Individual Information -->
            <div style="color: #333333; font-size: 15px; font-weight: 700; margin: 15px 0 8px 0; padding-bottom: 6px; border-bottom: 2px solid #0066cc;">{{ ($order->registration->registration_type ?? 'Organisation') === 'Individual' ? '👤' : '🏢' }} {{ ($order->registration->registration_type ?? 'Organisation') === 'Individual' ? 'Individual' : 'Organisation' }} Information</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 10px;">
                @if(($order->registration->registration_type ?? 'Organisation') === 'Organisation')
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Organisation Name</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->company_name ?? 'N/A' }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Industry Sector</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->industry_sector ?? 'N/A' }}</td>
                </tr>
                @if(($order->registration->registration_type ?? 'Organisation') === 'Organisation')
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Organisation Type</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->organisation_type ?? 'N/A' }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Country</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->company_country }}</td>
                </tr>
                @if($order->registration->company_state)
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">State</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->company_state }}</td>
                </tr>
                @endif
                @if($order->registration->company_city)
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">City</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->company_city }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Phone</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->company_phone }}</td>
                </tr>
            </table>

            <!-- GST Information (if required) -->
            @if($order->registration->gst_required)
            <div style="color: #333333; font-size: 15px; font-weight: 700; margin: 15px 0 8px 0; padding-bottom: 6px; border-bottom: 2px solid #0066cc;">🧾 GST / Invoice Details</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 10px;">
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Legal Name (For Invoice)</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->gst_legal_name ?? $order->registration->company_name }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">GSTIN</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->gstin ?? '-' }}</td>
                </tr>
                @php $panNo = $order->registration->gstin ? substr($order->registration->gstin, 2, 10) : null; @endphp
                @if($panNo)
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">PAN No.</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $panNo }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Invoice Address</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->gst_address ?? '-' }}</td>
                </tr>
                @if($order->registration->gst_state)
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">State</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $order->registration->gst_state }}</td>
                </tr>
                @endif
                @php
                    $contactName = $order->registration->contact->name ?? null;
                    $contactPhone = $order->registration->contact->phone ?? $order->registration->company_phone ?? null;
                @endphp
                @if($contactName)
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Contact Person</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $contactName }}</td>
                </tr>
                @endif
                @if($contactPhone)
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Contact Phone</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $contactPhone }}</td>
                </tr>
                @endif
            </table>
            @endif

            <!-- Delegate Details -->
            @if($order->registration->delegates && $order->registration->delegates->count() > 0)
            @php 
                $ticketTypeName = $order->items->first()->ticketType->name ?? 'N/A';
                $hasLinkedIn = $order->registration->delegates->contains(function($delegate) {
                    return !empty($delegate->linkedin_profile);
                });
            @endphp
            <div style="color: #333333; font-size: 15px; font-weight: 700; margin: 15px 0 8px 0; padding-bottom: 6px; border-bottom: 2px solid #0066cc;">👥 Delegate Details</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; font-size: 12px; margin: 8px 0;">
                <thead>
                    <tr>
                        <th style="background: #0066cc; color: #ffffff; padding: 8px 6px; text-align: left; font-weight: 600; font-size: 11px; border: 1px solid #0066cc;">#</th>
                        <th style="background: #0066cc; color: #ffffff; padding: 8px 6px; text-align: left; font-weight: 600; font-size: 11px; border: 1px solid #0066cc;">Delegate Name</th>
                        <th style="background: #0066cc; color: #ffffff; padding: 8px 6px; text-align: left; font-weight: 600; font-size: 11px; border: 1px solid #0066cc;">Email</th>
                        <th style="background: #0066cc; color: #ffffff; padding: 8px 6px; text-align: left; font-weight: 600; font-size: 11px; border: 1px solid #0066cc;">Phone</th>
                        <th style="background: #0066cc; color: #ffffff; padding: 8px 6px; text-align: left; font-weight: 600; font-size: 11px; border: 1px solid #0066cc;">Ticket Type</th>
                        @if($hasLinkedIn)
                        <th style="background: #0066cc; color: #ffffff; padding: 8px 6px; text-align: left; font-weight: 600; font-size: 11px; border: 1px solid #0066cc;">LinkedIn</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->registration->delegates as $delegate)
                    <tr style="background: {{ $loop->even ? '#f8f9fa' : '#ffffff' }};">
                        <td style="padding: 6px; border: 1px solid #e0e0e0; font-size: 11px; vertical-align: top;">{{ $loop->iteration }}</td>
                        <td style="padding: 6px; border: 1px solid #e0e0e0; font-size: 11px; vertical-align: top; word-wrap: break-word;">{{ $delegate->salutation }} {{ $delegate->first_name }} {{ $delegate->last_name }}</td>
                        <td style="padding: 6px; border: 1px solid #e0e0e0; font-size: 11px; vertical-align: top; word-wrap: break-word; word-break: break-all;">{{ $delegate->email }}</td>
                        <td style="padding: 6px; border: 1px solid #e0e0e0; font-size: 11px; vertical-align: top; word-wrap: break-word;">{{ $delegate->phone ?? '-' }}</td>
                        <td style="padding: 6px; border: 1px solid #e0e0e0; font-size: 11px; vertical-align: top; word-wrap: break-word;">{{ $ticketTypeName }}</td>
                        @if($hasLinkedIn)
                        <td style="padding: 6px; border: 1px solid #e0e0e0; font-size: 11px; vertical-align: top;">
                            @if(!empty($delegate->linkedin_profile))
                                <a href="{{ $delegate->linkedin_profile }}" target="_blank" rel="noopener noreferrer" style="color: #0077b5; text-decoration: none;">View</a>
                            @else
                                <span style="color: #999;">-</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif

            <!-- Price Breakdown -->
            <div style="color: #333333; font-size: 15px; font-weight: 700; margin: 15px 0 8px 0; padding-bottom: 6px; border-bottom: 2px solid #0066cc;">💰 Price Breakdown</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 8px 0;">
                @foreach($order->items as $item)
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #f8f9fa; font-weight: 500; width: 70%;">Ticket Price ({{ $item->quantity }} × {{ $currencySymbol }}{{ number_format($item->unit_price, $priceFormat) }})</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%;">{{ $currencySymbol }}{{ number_format($item->subtotal, $priceFormat) }}</td>
                </tr>
                @if($order->discount_amount > 0 && $order->promoCode)
                <tr style="background-color: #d4edda;">
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #d4edda; font-weight: 500; width: 70%; color: #155724;">
                        🏷️ Promocode Discount
                        @if($order->promoCode->type === 'percentage')
                            <div style="font-size: 11px; font-weight: normal; margin-top: 3px;">
                                ({{ number_format($order->promoCode->value, 0) }}% off base amount)
                            </div>
                        @endif
                    </td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%; color: #155724;">
                        -{{ $currencySymbol }}{{ number_format($order->discount_amount, $priceFormat) }}
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #f8f9fa; font-weight: 500; width: 70%;">Price After Discount</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%;">
                        {{ $currencySymbol }}{{ number_format($item->subtotal - $order->discount_amount, $priceFormat) }}
                    </td>
                </tr>
                @endif
                @if($item->gst_type === 'cgst_sgst')
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #f8f9fa; font-weight: 500; width: 70%;">CGST ({{ number_format($item->cgst_rate ?? 0, 0) }}%)</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%;">{{ $currencySymbol }}{{ number_format($item->cgst_amount ?? 0, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #f8f9fa; font-weight: 500; width: 70%;">SGST ({{ number_format($item->sgst_rate ?? 0, 0) }}%)</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%;">{{ $currencySymbol }}{{ number_format($item->sgst_amount ?? 0, $priceFormat) }}</td>
                </tr>
                @else
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #f8f9fa; font-weight: 500; width: 70%;">IGST ({{ number_format($item->igst_rate ?? 0, 0) }}%)</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%;">{{ $currencySymbol }}{{ number_format($item->igst_amount ?? 0, $priceFormat) }}</td>
                </tr>
                @endif
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; background: #f8f9fa; font-weight: 500; width: 70%;">Processing Charge ({{ $item->processing_charge_rate }}%)</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; text-align: right; font-weight: 600; width: 30%;">{{ $currencySymbol }}{{ number_format($item->processing_charge_amount, $priceFormat) }}</td>
                </tr>
                @endforeach
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 15px; font-weight: 700; background: #0066cc; color: #ffffff; width: 70%;">Total Amount</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 15px; font-weight: 700; text-align: right; background: #0066cc; color: #ffffff; width: 30%;">{{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}</td>
                </tr>
            </table>

            <!-- Pay Now Button -->
            @if($order->status !== 'paid')
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('tickets.payment.lookup', ['eventSlug' => $event->slug ?? $event->id, 'tin' => $order->order_no]) }}" style="display: inline-block; background: #DAA520; color: #ffffff; padding: 14px 35px; text-decoration: none; border-radius: 5px; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;">
                    💳 Pay Now - {{ $currencySymbol }}{{ number_format($order->total, $priceFormat) }}
                </a>
            </div>
            <p style="text-align: center; color: #666666; font-size: 12px; margin-top: 8px;">
                Click the button above to complete your payment securely.
            </p>
            <p style="text-align: center; color: #888888; font-size: 11px; font-style: italic;">
                <strong>Note:</strong> After payment, a final acknowledgement receipt will be provided.
            </p>
            @else
            <!-- Payment Transaction Details (shown only when paid) -->
            @php
                $payment = \App\Models\Payment::where('order_id', $order->order_no)
                    ->where('status', 'successful')
                    ->latest()
                    ->first();
            @endphp
            @if($payment)
            <div style="color: #333333; font-size: 15px; font-weight: 700; margin: 20px 0 8px 0; padding-bottom: 6px; border-bottom: 2px solid #0066cc;">🧾 Payment Transaction Details</div>
            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-bottom: 10px;">
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Payment Method</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $payment->payment_method ?? 'Online' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Transaction ID</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; font-weight: 700; color: #0066cc; width: 60%;">{{ $payment->transaction_id ?? $payment->track_id ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Amount Paid</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; font-weight: 700; color: #155724; width: 60%;">{{ $currencySymbol }}{{ number_format($payment->amount_paid ?? $payment->amount ?? $order->total, $priceFormat) }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Payment Date</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; color: #333333; width: 60%;">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y, h:i A') : 'N/A' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; background: #f8f9fa; font-weight: 600; color: #555555; width: 40%;">Payment Status</td>
                    <td style="padding: 8px 10px; border: 1px solid #e0e0e0; font-size: 13px; vertical-align: top; width: 60%;"><span style="background: #28a745; color: #fff; padding: 3px 10px; border-radius: 3px; font-weight: 600;">✓ CONFIRMED</span></td>
                </tr>
            </table>
            @endif
            <div style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 8px 0; text-align: center; border-radius: 4px;">
                <p style="margin: 0; color: #155724; font-size: 15px; font-weight: 700;">
                    @if($order->payment_status === 'complimentary')
                        🎁 Complimentary Registration Confirmed
                    @else
                        ✓ Payment Completed Successfully
                    @endif
                </p>
                <p style="margin: 5px 0 0 0; color: #155724; font-size: 13px;">
                    Your registration is confirmed. You will receive further communication regarding the event.
                </p>
            </div>
            @endif
        </div>

        <!-- Secretariat Information -->
        <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin-top: 10px; border-top: 1px solid #e0e0e0; padding-top: 10px; background: #f8f9fa;">
            <tr>
                <td style="width: 35%; text-align: center; border-right: 1px solid #e0e0e0; padding: 10px; vertical-align: top;">
                @if(config('constants.organizer_logo'))
                    <img src="{{ config('constants.organizer_logo') }}" alt="{{ config('constants.organizer.name') }}" style="width: 120px; height: 120px; object-fit: contain; display: block; margin: 0 auto;">
                @endif
                </td>
                <td style="width: 65%; padding: 10px 10px 10px 15px; vertical-align: top;">
                    <div style="font-size: 13px; font-weight: 700; color: #333333; margin-bottom: 5px;">{{ config('constants.EVENT_NAME') }} Secretariat</div>
                    <div style="font-size: 12px; color: #666666; line-height: 1.5;">
                        <p style="margin: 2px 0;"><strong>{{ config('constants.organizer.name') }}</strong></p>
                        <p style="margin: 2px 0;">{!! config('constants.organizer.address') !!}</p>
                        <p style="margin: 2px 0;"><strong>Tel:</strong> {{ config('constants.organizer.phone') }}</p>
                        <p style="margin: 2px 0;"><strong>Email:</strong> <a href="mailto:{{ config('constants.organizer.email') }}" style="color: #0066cc;">{{ config('constants.organizer.email') }}</a></p>
                        <p style="margin: 2px 0;"><strong>Website:</strong> <a href="{{ config('constants.EVENT_WEBSITE') }}" style="color: #0066cc;">{{ config('constants.EVENT_WEBSITE') }}</a></p>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div style="background: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #666666; border-top: 2px solid #e0e0e0;">
            <p style="margin: 5px 0; font-size: 11px; color: #999999;">This is an automated email. Please do not reply to this message.</p>
            <p style="margin: 5px 0; font-size: 11px; color: #999999;">&copy; {{ date('Y') }} {{ config('constants.organizer.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
