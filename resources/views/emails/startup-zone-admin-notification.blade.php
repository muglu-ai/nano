<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Startup Zone Application Submitted</title>
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
                                <p style="margin: 10px 0 0; color: #666666; font-size: 16px; font-weight: 600;">New Startup Zone Application Submitted</p>
                            </div>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            
                            <p style="margin: 0 0 20px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Hello Admin,
                            </p>
                            
                            <p style="margin: 0 0 25px; font-size: 16px; color: #333333; line-height: 1.6;">
                                A new <strong>Startup Zone</strong> application has been submitted and is awaiting your approval. Please review the details below and approve the application to enable payment.
                            </p>

                            <!-- Application Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Application Details</h2>
                                
                                @if($application->application_id)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Application ID (TIN):</td>
                                        <td style="color: #333333; padding: 5px 0;"><strong>{{ $application->application_id }}</strong></td>
                                    </tr>
                                </table>
                                @endif

                                @php
                                    $submissionDate = $application->submission_date ?? $application->created_at;
                                    if ($submissionDate) {
                                        $submissionDate = \Carbon\Carbon::parse($submissionDate)->format('d M Y, h:i A');
                                    }
                                @endphp
                                @if($submissionDate)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Submission Date:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $submissionDate }}</td>
                                    </tr>
                                </table>
                                @endif

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
                                
                                @if($exhibitorInfo->company_name)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Name of Exhibitor:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $exhibitorInfo->company_name ?? 'N/A' }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($exhibitorInfo->email)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Company Email:</td>
                                        <td style="color: #333333; padding: 5px 0;"><a href="mailto:{{ $exhibitorInfo->email }}" style="color: #1a237e; text-decoration: none;">{{ $exhibitorInfo->email }}</a></td>
                                    </tr>
                                </table>
                                @endif

                                @if($exhibitorInfo->address)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0; vertical-align: top;">Address:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            {{ $exhibitorInfo->address }}
                                            @if($exhibitorInfo->city), {{ $exhibitorInfo->city }}@endif
                                            @if($exhibitorInfo->state), {{ $exhibitorInfo->state }}@endif
                                            @if($exhibitorInfo->country), {{ $exhibitorInfo->country }}@endif
                                            @if($exhibitorInfo->postal_code) - {{ $exhibitorInfo->postal_code }}@endif
                                        </td>
                                    </tr>
                                </table>
                                @endif

                                @if($exhibitorInfo->telPhone)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Telephone:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $exhibitorInfo->telPhone }}</td>
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
                                </table>
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


                            <!-- Invoice Details -->
                          
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Invoice Details</h2>
                                
                                @if($invoice->price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Base Price:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->price, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice->gst)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">GST({{ $invoice->gst_rate ?? 18 }}%):</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->gst, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice->processing_chargesRate && $invoice->processing_chargesRate > 0)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Processing Charges({{ $invoice->processing_chargesRate }}%):</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->processing_charges ?? 0, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice->total_final_price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 15px; border-top: 2px solid #1a237e; padding-top: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #1a237e; padding: 5px 0; font-size: 16px;">Total Amount:</td>
                                        <td style="color: #1a237e; padding: 5px 0; font-size: 16px; font-weight: bold;">{{ $invoice->currency }} {{ number_format($invoice->total_final_price, 2) }}</td>
                                    </tr>
                                </table>
                                @endif
                            </div>
                           

                            <!-- Action Required -->
                            <div style="background-color: #fff3cd; border: 2px solid #ffc107; padding: 20px; margin: 25px 0; border-radius: 4px; text-align: center;">
                                <p style="margin: 0 0 15px; font-size: 16px; color: #856404; font-weight: bold;">Action Required</p>
                                <p style="margin: 0 0 20px; font-size: 14px; color: #856404;">Please review and approve this application to enable payment for the user.</p>
                                <a href="{{ config('app.url') }}/application-list/submitted?type=startup-zone" style="display: inline-block; padding: 12px 30px; background-color: #1a237e; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 16px;">Review Application</a>
                            </div>

                            <!-- Closing -->
                            <p style="margin: 25px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                Best regards,<br>
                                {{ config('constants.EVENT_NAME') }} Team
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 30px; background-color: #f8f9fa; border-top: 1px solid #e9ecef; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; font-size: 12px; color: #6c757d; text-align: center;">
                                This is an automated notification. Please do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

