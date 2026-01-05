<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }} - Registration Confirmation</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f6f8;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f4f6f8; padding: 20px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 30px 30px 20px; background-color: #ffffff; border-radius: 8px 8px 0 0; border-bottom: 2px solid #1a237e;">
                            <!-- Event Logo on Left -->
                            <div style="text-align: left; margin-bottom: 20px;">
                                @if(config('constants.event_logo'))
                                <img src="{{ config('constants.event_logo') }}" alt="{{ config('constants.EVENT_NAME') }}" style="max-width: 200px; height: auto; display: block;">
                                @endif
                            </div>
                            
                            <!-- Event Name and Year -->
                            <div style="text-align: center; padding-top: 15px; border-top: 1px solid #e9ecef;">
                                <h1 style="margin: 0; color: #1a237e; font-size: 24px; font-weight: bold;">{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</h1>
                                <p style="margin: 10px 0 0; color: #666666; font-size: 16px; font-weight: 600;">
                                    @if($application->application_type === 'exhibitor-registration')
                                        Exhibitor Registration - Provisional Receipt
                                    @else
                                        Startup Exhibitor Registration - Provisional Receipt
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
                                Dear @if($contact && $contact->first_name){{ $contact->first_name }}@else{{ $application->company_name }}@endif,
                            </p>
                            
                            <p style="margin: 0 0 25px; font-size: 16px; color: #333333; line-height: 1.6;">
                                Thank you for registering as an exhibitor for <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>. We are pleased to confirm your registration.
                            </p>

                            <!-- Registration Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Registration Details</h2>
                                
                                @if($application->application_id)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">TIN Number:</td>
                                        <td style="color: #333333; padding: 5px 0;"><strong>{{ $application->application_id }}</strong></td>
                                    </tr>
                                </table>
                                @endif

                                @php
                                    $registrationDate = $application->submission_date ?? $application->created_at;
                                    if ($registrationDate) {
                                        $registrationDate = \Carbon\Carbon::parse($registrationDate)->format('d M Y');
                                    }
                                @endphp
                                @if($registrationDate)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Registration Date:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $registrationDate }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice->invoice_nos)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Invoice Number:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->invoice_no }}</td>
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

                                @if($application->subSector)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Subsector:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $application->subSector }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($application->stall_category)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Stall Category:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $application->stall_category }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($application->interested_sqm)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Booth Size:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $application->interested_sqm }}</td>
                                    </tr>
                                </table>
                                @endif
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
                                                        $billingCity = $billingDetail->city_id; // It's already a city name
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

                                @if($billingDetail->gst_id)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">GST Number:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $billingDetail->gst_id }}</td>
                                    </tr>
                                </table>
                                @endif
                            </div>
                            @endif

                            <!-- Exhibitor Information -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Exhibitor Information</h2>
                                
                                @if($application->company_name)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Name of Exhibitor:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $application->company_name }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($application->address)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0; vertical-align: top;">Address:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            {{ $application->address }}
                                            @if($application->city_id)
                                                @php
                                                    $exhibitorCity = '';
                                                    if (is_numeric($application->city_id)) {
                                                        $city = \App\Models\City::find($application->city_id);
                                                        $exhibitorCity = $city ? $city->name : $application->city_id;
                                                    } else {
                                                        $exhibitorCity = $application->city_id; // It's already a city name
                                                    }
                                                @endphp
                                                , {{ $exhibitorCity }}
                                            @endif
                                            @if($application->state && $application->state->name), {{ $application->state->name }}@endif
                                            @if($application->country && $application->country->name), {{ $application->country->name }}@endif
                                            @if($application->postal_code) - {{ $application->postal_code }}@endif
                                        </td>
                                    </tr>
                                </table>
                                @endif

                                @if($application->company_email)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Company Email:</td>
                                        <td style="color: #333333; padding: 5px 0;"><a href="mailto:{{ $application->company_email }}" style="color: #1a237e; text-decoration: none;">{{ $application->company_email }}</a></td>
                                    </tr>
                                </table>
                                @endif

                                @if($application->landline)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Telephone:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $application->landline }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($application->website)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Website:</td>
                                        <td style="color: #333333; padding: 5px 0;"><a href="{{ $application->website }}" target="_blank" style="color: #1a237e; text-decoration: none;">{{ $application->website }}</a></td>
                                    </tr>
                                </table>
                                @endif

                                @if($application->gst_no)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">GST Number:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $application->gst_no }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($application->pan_no)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">PAN Number:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $application->pan_no }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($application->how_old_startup || $application->companyYears)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Company Age:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            @php
                                                $companyAge = $application->companyYears ?? $application->how_old_startup;
                                            @endphp
                                            {{ $companyAge }} Year{{ $companyAge > 1 ? 's' : '' }}
                                        </td>
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

                            <!-- Payment Details -->
                            <div style="background-color: #f8f9fa; border-left: 4px solid #1a237e; padding: 20px; margin: 25px 0; border-radius: 4px;">
                                <h2 style="margin: 0 0 15px; font-size: 18px; color: #1a237e; font-weight: bold;">Payment Details</h2>
                                
                                @if($invoice->price)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Base Amount:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->price, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice->gst)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">GST @ 18%:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->gst, 2) }}</td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice->processing_charges)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Processing Charges:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ $invoice->currency }} {{ number_format($invoice->processing_charges, 2) }}</td>
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

                                @if($invoice->payment_status)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Payment Status:</td>
                                        <td style="color: #333333; padding: 5px 0;">
                                            <span style="padding: 4px 12px; border-radius: 4px; font-weight: bold; 
                                                @if($invoice->payment_status === 'paid') background-color: #d4edda; color: #155724;
                                                @elseif($invoice->payment_status === 'partial') background-color: #fff3cd; color: #856404;
                                                @else background-color: #f8d7da; color: #721c24;
                                                @endif">
                                                {{ ucfirst($invoice->payment_status) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                                @endif

                                @if($invoice->payment_due_date)
                                <table role="presentation" width="100%" cellpadding="5" cellspacing="0" style="margin-bottom: 10px;">
                                    <tr>
                                        <td style="width: 40%; font-weight: bold; color: #555555; padding: 5px 0;">Payment Due Date:</td>
                                        <td style="color: #333333; padding: 5px 0;">{{ \Carbon\Carbon::parse($invoice->payment_due_date)->format('d M Y') }}</td>
                                    </tr>
                                </table>
                                @endif
                            </div>

                            <!-- Payment Link (if unpaid) -->
                            @if($invoice->payment_status !== 'paid')
                            <div style="background-color: #fff3cd; border: 2px solid #ffc107; padding: 20px; margin: 25px 0; border-radius: 4px; text-align: center;">
                                <p style="margin: 0 0 15px; font-size: 16px; color: #856404; font-weight: bold;">Complete Your Payment</p>
                                <p style="margin: 0 0 20px; font-size: 14px; color: #856404;">Please complete your payment to confirm your registration.</p>
                                <a href="{{ $paymentUrl }}" style="display: inline-block; padding: 12px 30px; background-color: #1a237e; color: #ffffff; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 16px; margin-bottom: 15px;">Pay Now</a>
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ffc107;">
                                    <p style="margin: 0 0 8px; font-size: 12px; color: #856404; font-weight: bold;">Or copy and paste this link:</p>
                                    <p style="margin: 0; font-size: 11px; color: #856404; word-break: break-all; background-color: #ffffff; padding: 8px; border-radius: 4px; border: 1px solid #ffc107;">
                                        <a href="{{ $paymentUrl }}" style="color: #1a237e; text-decoration: underline;">{{ $paymentUrl }}</a>
                                    </p>
                                </div>
                            </div>
                            @endif

                            <!-- Important Note -->
                            <div style="background-color: #f8d7da; border-left: 4px solid #dc3545; padding: 15px; margin: 25px 0; border-radius: 4px;">
                                <p style="margin: 0; font-size: 14px; color: #721c24; font-weight: bold;">Important:</p>
                                <p style="margin: 5px 0 0; font-size: 14px; color: #721c24; line-height: 1.5;">
                                    Please keep this email for your records. You will receive a payment confirmation email once your payment is processed.
                                </p>
                            </div>

                            <!-- Closing -->
                            <p style="margin: 25px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
                                We look forward to your participation at <strong>{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</strong>.
                            </p>

                            <p style="margin: 20px 0 0; font-size: 16px; color: #333333; line-height: 1.6;">
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
                            @php
                                $organizer = config('constants.organizer', []);
                            @endphp
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <!-- Left Side: Organizer Logo -->
                                    <td style="width: 30%; vertical-align: top; padding-right: 20px;">
                                        @if(config('constants.organizer_logo'))
                                        <img src="{{ config('constants.organizer_logo') }}" alt="{{ $organizer['name'] ?? '' }}" style="max-width: 150px; height: auto; display: block;">
                                        @endif
                                    </td>
                                    
                                    <!-- Right Side: Organizer Details -->
                                    <td style="width: 70%; vertical-align: top; font-size: 12px; color: #6c757d; line-height: 1.6;">
                                        <!-- Organizer Name -->
                                        @if(!empty($organizer['name']))
                                        <p style="margin: 0 0 10px; font-weight: bold; color: #333333; font-size: 14px;">{{ $organizer['name'] }}</p>
                                        @endif
                                        
                                        <!-- Organizer Address -->
                                        @if(!empty($organizer['address']))
                                        <p style="margin: 0 0 12px; color: #555555; line-height: 1.5;">{!! $organizer['address'] !!}</p>
                                        @endif
                                        
                                        <!-- Contact Details -->
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
                                            
                                            @if(config('constants.GSTIN'))
                                            <tr>
                                                <td style="width: 80px; font-weight: bold; color: #333333; padding: 3px 0; vertical-align: top;">GSTIN:</td>
                                                <td style="color: #555555; padding: 3px 0;">{{ config('constants.GSTIN') }}</td>
                                            </tr>
                                            @endif
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
