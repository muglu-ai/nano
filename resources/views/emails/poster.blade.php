<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Payment - {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }} Poster Registration</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f6f8;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f6f8; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 30px 20px; background-color: #ffffff; border-radius: 8px 8px 0 0; border-bottom: 2px solid #1a237e;">
                            <div style="text-align: left; margin-bottom: 20px;">
                                @if(config('constants.event_logo'))
                                <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.EVENT_NAME') }}" style="max-width: 200px; height: auto; display: block;">
                                @endif
                            </div>
                            
                            <div style="text-align: center; padding-top: 15px; border-top: 1px solid #e9ecef;">
                                <h1 style="margin: 0; color: #1a237e; font-size: 24px; font-weight: bold;">{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</h1>
                                <p style="margin: 10px 0 0; color: #666666; font-size: 16px; font-weight: 600;">
                                    Payment Confirmation - Poster Registration
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Dear {{ $poster->lead_name }},
                            </p>
                            
                            @if($isPaymentThankYou)
                            <div style="background-color: #d4edda; border: 2px solid #28a745; padding: 20px; margin: 25px 0; border-radius: 4px; text-align: center;">
                                <p style="margin: 0 0 10px; font-size: 20px; color: #155724; font-weight: bold;">✓ Payment Confirmed!</p>
                                <p style="margin: 0; font-size: 18px; color: #155724; font-weight: bold;">Thank You for Making Payment</p>
                                <p style="margin: 10px 0 0; font-size: 16px; color: #155724;">Your payment has been successfully processed for your Poster Registration at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}.</p>
                            </div>
                            @endif
                            
                            <!-- Main Message -->
                            <p style="margin: 0 0 25px; font-size: 16px; color: #333333; line-height: 1.6;">
                                We are delighted to confirm that your payment has been received and your poster registration is now complete. We look forward to your participation at <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>.
                            </p>

                            <!-- Payment Confirmation Section -->
                            @if($showPaymentConfirmation)
                            <div style="background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Payment Confirmation</h2>
                                
                                @if($poster->tin_no)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">TIN Number:</td>
                                        <td style="color: #333333; padding: 5px 0;"><strong>{{ $poster->tin_no }}</strong></td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice && $invoice->total_final_price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Amount Paid:</td>
                                        <td style="color: #28a745; padding: 5px 0; font-size: 18px; font-weight: bold;">{{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->total_final_price, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if(isset($paymentDetails['transaction_id']) && $paymentDetails['transaction_id'])
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Transaction ID:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $paymentDetails['transaction_id'] }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if(isset($paymentDetails['payment_method']) && $paymentDetails['payment_method'])
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Payment Method:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $paymentDetails['payment_method'] }}</td>
                                    </tr>
                                </table>
                                @endif

                                @php
                                    $paymentDate = $invoice->updated_at ?? $poster->updated_at ?? now();
                                    if ($paymentDate) {
                                        $paymentDate = \Carbon\Carbon::parse($paymentDate)->format('d M Y, h:i A');
                                    }
                                @endphp
                                @if($paymentDate)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Payment Date:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $paymentDate }}</td>
                                    </tr>
                                </table>
                                @endif

                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Payment Status:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            <span style="padding: 4px 12px; border-radius: 4px; font-weight: bold; background-color: #d4edda; color: #155724;">
                                                Paid
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            @endif

                            <!-- Poster Registration Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Poster Registration Details</h2>
                                
                                @if($poster->title)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Poster Title:</td>
                                        <td style="color: #333333; padding: 5px 0;"><strong>{{ $poster->title }}</strong></td>
                                    </tr>
                                </table>
                                @endif

                                @if($poster->theme)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Theme:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $poster->theme }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($poster->sector)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Sector:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $poster->sector }}</td>
                                    </tr>
                                </table>
                                @endif

                                @php
                                    $registrationDate = $poster->created_at ?? now();
                                    if ($registrationDate) {
                                        $formattedDate = \Carbon\Carbon::parse($registrationDate)->format('d M Y');
                                    }
                                @endphp
                                @if(isset($formattedDate) && $formattedDate)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Registration Date:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $formattedDate }}</td>
                                    </tr>
                                </table>
                                @endif
                            </div>

                            <!-- Lead Author Information -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Lead Author Information</h2>
                                
                                @if($poster->lead_name)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Name:</td>
                                        <td style="color: #333333; padding: 5px 0;"><strong>{{ $poster->lead_name }}</strong></td>
                                    </tr>
                                </table>
                                @endif

                                @if($poster->lead_email)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Email:</td>
                                        <td style="color: #333333; padding: 5px 0;"><a href="mailto:{{ $poster->lead_email }}" style="color: #1a237e; text-decoration: none;">{{ $poster->lead_email }}</a></td>
                                    </tr>
                                </table>
                                @endif

                                @if($poster->lead_org)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Organization:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $poster->lead_org }}</td>
                                    </tr>
                                </table>
                                @endif
                            </div>

                            <!-- Payment Breakdown -->
                            @if($showPaymentBreakdown && $invoice)
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Payment Breakdown</h2>
                                
                                @if($invoice->price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Base Amount:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->price, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice->gst)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">GST:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->gst, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice->processing_charges)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Processing Charges:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->processing_charges, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice->total_final_price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 15px; border-top: 2px solid #1a237e; padding-top: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #1a237e; padding: 5px 0; font-size: 16px;">Total Amount Paid:</td>
                                        <td style="color: #28a745; padding: 5px 0; font-size: 16px; font-weight: bold;">{{ $invoice->currency ?? 'INR' }} {{ number_format($invoice->total_final_price, 2) }}</td>
                                    </tr>
                                </table>
                                @endif
                            </div>
                            @endif

                            <!-- Important Note -->
                            <div style="background-color: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 25px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #0c5460; font-weight: bold;">Important:</p>
                                <p style="margin: 5px 0 0; font-size: 14px; color: #0c5460; line-height: 1.5;">
                                    Please keep this email for your records. This serves as your payment confirmation and receipt for your Poster Registration at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}.
                                </p>
                            </div>

                            <!-- Closing -->
                            <p style="margin: 25px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Once again, thank you for your payment and registration. We are excited to have you as part of <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>.
                            </p>

                            <p style="margin: 20px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                We look forward to seeing you at the event!<br><br>
                                Best regards,<br>
                                @php
                                    $organizer = config('constants.organizer', []);
                                @endphp
                                @if(!empty($organizer['name']))
                                <strong>{{ $organizer['name'] }}</strong><br>
                                @endif
                                {{ config('constants.EVENT_NAME') }} Team
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; border-radius: 0 0 8px 8px;">
                            @if($showOrganizerFooter)
                                @php
                                    $organizer = config('constants.organizer', []);
                                @endphp
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                    <tr>
                                        <td style="width: 30%; vertical-align: top; padding-right: 20px;">
                                            @if(config('constants.organizer_logo'))
                                            <img src="{{ config('constants.organizer_logo') }}" alt="{{ $organizer['name'] ?? '' }}" style="max-width: 150px; height: auto; display: block;">
                                            @endif
                                        </td>
                                        
                                        <td style="width: 70%; vertical-align: top; font-size: 12px; color: #6c757d; line-height: 1.6;">
                                            @if(!empty($organizer['name']))
                                            <p style="margin: 0 0 10px; font-weight: bold; color: #333333; font-size: 14px;">{{ $organizer['name'] }}</p>
                                            @endif
                                            
                                            @if(!empty($organizer['address']))
                                            <p style="margin: 0 0 12px; color: #555555; line-height: 1.5;">{!! $organizer['address'] !!}</p>
                                            @endif
                                            
                                            <table role="presentation" width="100%" cellpadding="3" cellspacing="0" style="margin: 0;">
                                                @if(!empty($organizer['phone']))
                                                <tr>
                                                    <td style="width: 80px; font-weight: bold; color: #333333; padding: 3px 0; vertical-align: top;">Tel:</td>
                                                    <td style="color: #555555; padding: 3px 0;"><a href="tel:{{ str_replace([' ', '-'], '', $organizer['phone']) }}" style="color: #1a237e; text-decoration: none;">{{ $organizer['phone'] }}</a></td>
                                                </tr>
                                                @endif
                                                
                                                @if(!empty($organizer['email']))
                                                <tr>
                                                    <td style="width: 80px; font-weight: bold; color: #333333; padding: 3px 0; vertical-align: top;">Email:</td>
                                                    <td style="color: #555555; padding: 3px 0;"><a href="mailto:{{ $organizer['email'] }}" style="color: #1a237e; text-decoration: none;">{{ $organizer['email'] }}</a></td>
                                                </tr>
                                                @endif
                                                
                                                @if(!empty($organizer['website']))
                                                <tr>
                                                    <td style="width: 80px; font-weight: bold; color: #333333; padding: 3px 0; vertical-align: top;">Website:</td>
                                                    <td style="color: #555555; padding: 3px 0;"><a href="{{ $organizer['website'] }}" target="_blank" style="color: #1a237e; text-decoration: none;">{{ $organizer['website'] }}</a></td>
                                                </tr>
                                                @endif
                                                
                                                @if(config('constants.EVENT_WEBSITE'))
                                                <tr>
                                                    <td style="width: 80px; font-weight: bold; color: #333333; padding: 3px 0; vertical-align: top;">Event:</td>
                                                    <td style="color: #555555; padding: 3px 0;"><a href="{{ config('constants.EVENT_WEBSITE') }}" target="_blank" style="color: #1a237e; text-decoration: none;">{{ config('constants.EVENT_WEBSITE') }}</a></td>
                                                </tr>
                                                @endif
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
