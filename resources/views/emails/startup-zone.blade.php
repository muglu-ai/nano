<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@if($isAdminNotification)New Startup Zone Application Submitted@elseif($isApprovalEmail){{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }} - Application Approved@elseThank You for Your Payment - {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}@endif</title>
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
                                    @if($isAdminNotification)
                                        New Startup Zone Application Submitted
                                    @elseif($isApprovalEmail)
                                        Application Approved - Payment Link
                                    @elseif($isPaymentThankYou)
                                        Payment Confirmation - Startup Exhibition
                                    @endif
                                </p>
                            </div>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            
                            <!-- Greeting -->
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                @if($isAdminNotification)
                                    Hello Admin,
                                @else
                                    Dear @if($contact && $contact->first_name){{ $contact->first_name }}@else{{ $application->company_name }}@endif,
                                @endif
                            </p>
                            
                            @if($isApprovalEmail)
                            <div style="background-color: #d4edda; border: 2px solid #28a745; padding: 20px; margin: 25px 0; border-radius: 4px; text-align: center;">
                                <p style="margin: 0 0 10px; font-size: 18px; color: #155724; font-weight: bold;">✓ Application Approved!</p>
                                <p style="margin: 0; font-size: 16px; color: #155724;">Your startup zone application has been approved. You can now proceed with payment.</p>
                            </div>
                            @endif
                            
                            @if($isPaymentThankYou)
                            <div style="background-color: #d4edda; border: 2px solid #28a745; padding: 20px; margin: 25px 0; border-radius: 4px; text-align: center;">
                                <p style="margin: 0 0 10px; font-size: 20px; color: #155724; font-weight: bold;">✓ Payment Confirmed!</p>
                                <p style="margin: 0; font-size: 18px; color: #155724; font-weight: bold;">Thank You for Making Payment</p>
                                <p style="margin: 10px 0 0; font-size: 16px; color: #155724;">Your payment has been successfully processed for the Startup Exhibition at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}.</p>
                            </div>
                            @endif
                            
                            <!-- Main Message -->
                            <p style="margin: 0 0 25px; font-size: 16px; color: #333333; line-height: 1.6;">
                                @if($isAdminNotification)
                                    A new <strong>Startup Zone</strong> application has been submitted and is awaiting your approval. Please review the details below and approve the application to enable payment.
                                @elseif($isApprovalEmail)
                                    Thank you for registering as an exhibitor for <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>. We are pleased to confirm that your application has been approved.
                                @elseif($isPaymentThankYou)
                                    We are delighted to confirm that your payment has been received and your registration is now complete. We look forward to your participation at <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>.
                                @endif
                            </p>

                            <!-- Payment Confirmation Section (Only for Payment Thank You) -->
                            @if($showPaymentConfirmation)
                            <div style="background-color: #f8f9fa; border-left: 4px solid #28a745; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Payment Confirmation</h2>
                                
                                @if($invoice && $invoice->pin_no)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">PIN Number:</td>
                                        <td style="color: #333333; padding: 5px 0;"><strong>{{ $invoice->pin_no }}</strong></td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice && $invoice->total_final_price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Amount Paid:</td>
                                        <td style="color: #28a745; padding: 5px 0; font-size: 18px; font-weight: bold;">{{ $invoice->currency }} {{ number_format($invoice->total_final_price, 2) }}</td>
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
                                    $paymentDate = $invoice->updated_at ?? now();
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

                            <!-- Application Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">
                                    @if($isAdminNotification)
                                        Application Details
                                    @else
                                        Registration Details
                                    @endif
                                </h2>
                                
                                @if($application->application_id)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">
                                            @if($isAdminNotification)
                                                TIN Number:
                                            @else
                                                TIN Number:
                                            @endif
                                        </td>
                                        <td style="color: #333333; padding: 5px 0;"><strong>{{ $application->application_id }}</strong></td>
                                    </tr>
                                </table>
                                @endif

                                @php
                                    if ($isAdminNotification) {
                                        $dateField = $application->submission_date ?? $application->created_at;
                                        $dateLabel = 'Submission Date:';
                                        $dateFormat = 'd M Y, h:i A';
                                    } else {
                                        $dateField = $application->submission_date ?? $application->created_at;
                                        $dateLabel = 'Registration Date:';
                                        $dateFormat = 'd M Y';
                                    }
                                    if ($dateField) {
                                        $formattedDate = \Carbon\Carbon::parse($dateField)->format($dateFormat);
                                    }
                                @endphp
                                @if(isset($formattedDate) && $formattedDate)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">{{ $dateLabel }}</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $formattedDate }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($isAdminNotification)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Status:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            <span style="padding: 4px 12px; border-radius: 4px; font-weight: bold; background-color: #fff3cd; color: #856404;">
                                                {{ ucfirst($application->submission_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                                @endif

                                @if($application->RegSource)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Association:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $application->RegSource }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if(!empty($sectorName))
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Sector:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $sectorName }}</td>
                                    </tr>
                                </table>
                                @endif
                            </div>
                            <!-- Booth Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Booth Details</h2>
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Booth Space:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $application->stall_category }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Booth Type:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $application->interested_sqm }}</td>
                                    </tr>
                            </div>

                            <!-- Billing Information -->
                            @if($billingDetail)
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Billing Information</h2>
                                
                                @if($billingDetail->billing_company)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Company Name:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $billingDetail->billing_company }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($billingDetail->contact_name)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Contact Name:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $billingDetail->contact_name }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($billingDetail->email)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Email:</td>
                                        <td style="color: #333333; padding: 5px 0;"><a href="mailto:{{ $billingDetail->email }}" style="color: #1a237e; text-decoration: none;">{{ $billingDetail->email }}</a></td>
                                    </tr>
                                </table>
                                @endif

                                @if($billingDetail->phone)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Phone:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $billingDetail->phone }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($billingDetail->address)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0; vertical-align: top;">Address:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            {{ $billingDetail->address }}
                                            @if($billingDetail->city_id)
                                                @php
                                                    $billingCity = '';
                                                    if (is_numeric($billingDetail->city_id)) {
                                                        $city = \App\Models\City::find($billingDetail->city_id);
                                                        $billingCity = $city ? $city->name : $billingDetail->city_id;
                                                    } else {
                                                        $billingCity = $billingDetail->city_id;
                                                    }
                                                @endphp
                                                , {{ $billingCity }}
                                            @endif
                                            @if($billingDetail->state_id), {{ \App\Models\State::find($billingDetail->state_id)->name ?? '' }}@endif
                                            @if($billingDetail->country_id), {{ \App\Models\Country::find($billingDetail->country_id)->name ?? '' }}@endif
                                            @if($billingDetail->postal_code) - {{ $billingDetail->postal_code }}@endif
                                        </td>
                                    </tr>
                                </table>
                                @endif
                            </div>
                            @endif

                            <!-- Exhibitor Information -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Exhibitor Information</h2>
                                
                                @if($exhibitorData['name'])
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Name of Exhibitor:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $exhibitorData['name'] }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($exhibitorData['email'])
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Company Email:</td>
                                        <td style="color: #333333; padding: 5px 0;"><a href="mailto:{{ $exhibitorData['email'] }}" style="color: #1a237e; text-decoration: none;">{{ $exhibitorData['email'] }}</a></td>
                                    </tr>
                                </table>
                                @endif

                                @if($exhibitorData['address'])
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0; vertical-align: top;">Address:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            {{ $exhibitorData['address'] }}
                                            @if($exhibitorData['city']), {{ $exhibitorData['city'] }}@endif
                                            @if($exhibitorData['state']), {{ $exhibitorData['state'] }}@endif
                                            @if($exhibitorData['country']), {{ $exhibitorData['country'] }}@endif
                                            @if($exhibitorData['postal_code']) - {{ $exhibitorData['postal_code'] }}@endif
                                        </td>
                                    </tr>
                                </table>
                                @endif

                                @if($exhibitorData['telephone'])
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Telephone:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $exhibitorData['telephone'] }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($exhibitorData['website'])
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Website:</td>
                                        <td style="color: #333333; padding: 5px 0;"><a href="{{ $exhibitorData['website'] }}" target="_blank" style="color: #1a237e; text-decoration: none;">{{ $exhibitorData['website'] }}</a></td>
                                    </tr>
                                </table>
                                @endif
                            </div>

                            <!-- Contact Person Details -->
                            @if($contact && ($contact->first_name || $contact->email || $contact->contact_number))
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Contact Person Details</h2>
                                
                                @if($contact->first_name || $contact->last_name)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Name:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            @if($contact->salutation){{ $contact->salutation }} @endif
                                            @if($contact->first_name){{ $contact->first_name }}@endif
                                            @if($contact->last_name) {{ $contact->last_name }}@endif
                                        </td>
                                    </tr>
                                </table>
                                @endif

                                @if($contact->job_title)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Designation:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $contact->job_title }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($contact->email)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Email:</td>
                                        <td style="color: #333333; padding: 5px 0;"><a href="mailto:{{ $contact->email }}" style="color: #1a237e; text-decoration: none;">{{ $contact->email }}</a></td>
                                    </tr>
                                </table>
                                @endif

                                @if($contact->contact_number)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Mobile:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $contact->contact_number }}</td>
                                    </tr>
                                </table>
                                @endif
                            </div>
                            @endif

                            <!-- Payment Details (For Approval Email) -->
                            @if($showPaymentDetails && !$isPaymentThankYou)
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Payment Details</h2>
                                
                                @if($invoice && $invoice->price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Base Amount:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->price, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice && $invoice->gst)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">GST @ 18%:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->gst, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice && $invoice->processing_charges)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Processing Charges:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->processing_charges, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice && $invoice->total_final_price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 15px; border-top: 2px solid #1a237e; padding-top: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #1a237e; padding: 5px 0; font-size: 16px;">Total Amount:</td>
                                        <td style="color: #1a237e; padding: 5px 0; font-size: 16px; font-weight: bold;">{{ $invoice->currency }} {{ number_format($invoice->total_final_price, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice && $invoice->payment_due_date)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Payment Due Date:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ \Carbon\Carbon::parse($invoice->payment_due_date)->format('d M Y') }}</td>
                                    </tr>
                                </table>
                                @endif
                            </div>
                            @endif

                            <!-- Payment Breakdown (For Payment Thank You) -->
                            @if($showPaymentBreakdown)
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Payment Breakdown</h2>
                                
                                @if($invoice && $invoice->price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Base Amount:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->price, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice && $invoice->gst)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">GST @ 18%:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->gst, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice && $invoice->processing_charges)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Processing Charges:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->processing_charges, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice && $invoice->total_final_price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 15px; border-top: 2px solid #1a237e; padding-top: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #1a237e; padding: 5px 0; font-size: 16px;">Total Amount Paid:</td>
                                        <td style="color: #28a745; padding: 5px 0; font-size: 16px; font-weight: bold;">{{ $invoice->currency }} {{ number_format($invoice->total_final_price, 2) }}</td>
                                    </tr>
                                </table>
                                @endif
                            </div>
                            @endif

                            <!-- Payment Link (For Approval Email) -->
                            @if($showPaymentLink && $invoice)
                            @php
                                $paymentUrl = route('startup-zone.payment', $application->application_id);
                            @endphp
                            <div style="background-color: #d4edda; border: 2px solid #28a745; padding: 20px; margin: 25px 0; border-radius: 4px; text-align: center;">
                                <p style="margin: 0 0 15px; font-size: 18px; color: #155724; font-weight: bold;">Complete Your Payment</p>
                                <p style="margin: 0 0 20px; font-size: 16px; color: #155724;">Your application has been approved. Please complete your payment to confirm your registration.</p>
                                <a href="{{ $paymentUrl }}" style="display: inline-block; padding: 12px 30px; background-color: #DAA520; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 16px; margin-bottom: 15px;">Pay Now</a>
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #28a745;">
                                    <p style="margin: 0 0 8px; font-size: 12px; color: #155724; font-weight: bold;">Or copy and paste this link:</p>
                                    <p style="margin: 0; font-size: 11px; color: #155724; word-break: break-all; background-color: #ffffff; padding: 8px; border-radius: 4px; border: 1px solid #28a745;">
                                        <a href="{{ $paymentUrl }}" style="color: #1a237e; text-decoration: underline;">{{ $paymentUrl }}</a>
                                    </p>
                                </div>
                            </div>
                            @endif

                            <!-- Action Required (For Admin Notification) -->
                            @if($showActionRequired)
                            <div style="background-color: #fff3cd; border: 2px solid #ffc107; padding: 20px; margin: 25px 0; border-radius: 4px; text-align: center;">
                                <p style="margin: 0 0 15px; font-size: 16px; color: #856404; font-weight: bold;">Action Required</p>
                                <p style="margin: 0 0 20px; font-size: 14px; color: #856404;">Please review and approve this application to enable payment for the user.</p>
                                <a href="{{ config('app.url') }}/application-list/submitted?type=startup-zone" style="display: inline-block; padding: 12px 30px; background-color: #1a237e; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 16px;">Review Application</a>
                            </div>
                            @endif

                            <!-- Important Note -->
                            @if($isApprovalEmail)
                            <div style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 25px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #721c24; font-weight: bold;">Important:</p>
                                <p style="margin: 5px 0 0; font-size: 14px; color: #721c24; line-height: 1.5;">
                                    Please complete your payment within the due date to confirm your participation. You will receive a payment confirmation email once your payment is processed.
                                </p>
                            </div>
                            @endif

                            @if($isPaymentThankYou)
                            <div style="background-color: #d1ecf1; border-left: 4px solid #17a2b8; padding: 15px; margin: 25px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #0c5460; font-weight: bold;">Important:</p>
                                <p style="margin: 5px 0 0; font-size: 14px; color: #0c5460; line-height: 1.5;">
                                    Please keep this email for your records. This serves as your payment confirmation and receipt for the Startup Exhibition at {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}.
                                </p>
                            </div>
                            @endif

                            <!-- Closing -->
                            <p style="margin: 25px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                @if($isAdminNotification)
                                    Best regards,<br>
                                    {{ config('constants.EVENT_NAME') }} Team
                                @elseif($isApprovalEmail)
                                    We look forward to your participation at <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>.
                                @elseif($isPaymentThankYou)
                                    Once again, thank you for your payment and registration. We are excited to have you as part of <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>.
                                @endif
                            </p>

                            @if(!$isAdminNotification)
                            <p style="margin: 20px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                @if($isPaymentThankYou)
                                    We look forward to seeing you at the event!<br><br>
                                @endif
                                Best regards,<br>
                                @php
                                    $organizer = config('constants.organizer', []);
                                @endphp
                                @if(!empty($organizer['name']))
                                <strong>{{ $organizer['name'] }}</strong><br>
                                @endif
                                {{ config('constants.EVENT_NAME') }} Team
                            </p>
                            @endif
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; border-radius: 0 0 8px 8px;">
                            @if($isAdminNotification)
                                <p style="margin: 0; font-size: 12px; color: #6c757d; text-align: center;">
                                    This is an automated notification. Please do not reply to this email.
                                </p>
                            @elseif($showOrganizerFooter)
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

