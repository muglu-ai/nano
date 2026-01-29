<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>{{config('constants.EVENT_NAME') . ' ' . $application->event->event_year}} | Application Form</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      font-size: 14px;
      color: #333;
      background-color: #ffffff;
    }
    .logo {
      max-height: 70px;
    }
    .header-box {
      border-bottom: 2px solid #80cfc6;
      margin-bottom: 25px;
      padding-bottom: 10px;
    }
    .section-title {
      font-weight: 700;
      font-size: 18px;
      padding: 12px 16px;
      background-color: #E91E63;
      color: #FFFFFF;
      margin-top: 30px;
      margin-bottom: 15px;
      border-radius: 4px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-left: 4px solid #FF1744;
      border: 1px solid #C2185B;
    }
    .custom-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    .custom-table th,
    .custom-table td {
      border: 1px solid #dee2e6;
      padding: 10px 14px;
      vertical-align: top;
    }
    .custom-table th {
      width: 30%;
      background-color: #f0fdfd;
      font-weight: 600;
      color: #555;
    }
    .check {
      color: #28a745;
      font-weight: bold;
    }
    .cross {
      color: #dc3545;
      font-weight: bold;
    }
    .terms {
      font-size: 12px;
      color: #555;
      margin-top: 30px;
    }
    .footer {
      text-align: right;
      font-size: 12px;
      margin-top: 40px;
      color: #888;
    }
    a {
      color: #007b8a;
      text-decoration: none;
    }
  </style>
</head>
<body class="p-4">
@php
    $logoPath = config('constants.event_logo');
@endphp

{{-- @dd($logoPath) --}}
  <!-- Header -->
  <div class="header-box d-flex justify-content-between align-items-center">
<div class="d-flex align-items-center w-100">
    <img src="{{ $logoPath }}" class="logo me-3" alt="{{config('constants.EVENT_NAME')}}">
    <span class="fw-bold fs-5">{{config('constants.EVENT_NAME')}} {{config('constants.EVENT_YEAR')}}</span>
    <div class="ms-auto text-end">
        <h5 class="mb-0 fw-bold">Submitted Application</h5>
    </div>
</div>
  </div>

  <!-- Company Information -->
  <div class="section-title">Company Information</div>
  <table class="custom-table">
    <tr>
      <th>Company Name</th>
      <td>
        @if($application->company_name)
          {{$application->company_name}}
        @else
          <span class="text-danger">Not provided</span>
        @endif
      </td>
    </tr>
    <tr>
      <th>Address</th>
      <td>
        @if($application->address && $application->city_id && $application->state && $application->state->name && $application->postal_code && $application->country && $application->country->name)
          {{$application->address}}, {{$application->city_id}}, {{$application->state->name}} - {{$application->postal_code}}, {{$application->country->name}}
        @else
          <span class="text-danger">Incomplete address information</span>
        @endif
      </td>
    </tr>
    <tr>
      <th>Website</th>
      <td>
        @if($application->website)
          <a href="{{$application->website}}">{{$application->website}}</a>
        @else
          <span class="text-danger">Not provided</span>
        @endif
      </td>
    </tr>

    <tr><th>Type of Business</th>
        <td>
            {{ isset($application->type_of_business) ? $application->type_of_business : 'Not provided' }}
        </td>
    </tr>
    <tr><th>Sector</th>
        <td>
            {{-- @dump($application->sector_id)

            @dump($sectors) --}}
            @if(isset($application->sector_id) && isset($sectors) && is_iterable($sectors))
                @php
                    $sectorIds = is_array($application->sector_id) ? $application->sector_id : json_decode($application->sector_id, true);
                    $sectorNames = collect($sectors)
                        ->whereIn('id', $sectorIds)
                        ->pluck('name')
                        ->filter()
                        ->toArray();
                @endphp
                {{ count($sectorNames) ? implode(', ', $sectorNames) : 'Not provided' }}
            @else
                <span class="text-danger">Not provided</span>
            @endif

        </td>
    </tr>
    <tr><th>Main Product Category</th>
        <td>
            @if(isset($application->main_product_category) && isset($productCategories) && is_iterable($productCategories))
            @php
                $category = $productCategories->firstWhere('id', $application->main_product_category);
            @endphp
            {{ $category ? $category->name : 'Unknown Category' }}
            @else
            <span class="text-danger">Not provided</span>
            @endif

        </td>
    </tr>

  </table>

  <!-- Participation Details -->
<div class="section-title">Participation Details</div>
<table class="custom-table">
{{--    <tr>--}}
{{--        <th>SEMI Member</th>--}}
{{--        <td>--}}
{{--            @if(isset($application->semi_member))--}}
{{--                {{$application->semi_member == 1 ? 'Yes' : 'No'}}--}}
{{--            @else--}}
{{--                <span class="text-danger">Not provided</span>--}}
{{--            @endif--}}
{{--        </td>--}}
{{--    </tr>--}}
    <tr>
        <th>Region</th>
        <td>
            @if(isset($application->region))
                {{$application->region}}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Previous Participation</th>
        <td>
            @if(isset($application->participated_previous))
                @if($application->participated_previous == 1)
                    Yes
                @else
                    No
                @endif
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Stall Category</th>
        <td>
            @if(isset($application->stall_category))
                {{$application->stall_category}}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Preferred Location</th>
        <td>
            @if(isset($application->pref_location))
                {{$application->pref_location}}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    @if(isset($application->sponsor_only) && $application->sponsor_only == 1)
    
    
    <tr>
        <th>Applying for Sponsorship Only</th>
        <td>
            @if(isset($application->sponsor_only))
                {{$application->sponsor_only == '1' ?  'Yes' : 'No'}}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    @endif
    <tr>
        <th>Interested SQM</th>
        <td>
            @if(isset($application->interested_sqm))
                {{$application->interested_sqm}}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    @if(isset($application->submission_status) && $application->submission_status =='approved')
    <tr>
        <th>Allocated SQM</th>
        <td>
            @if(isset($application->allocated_sqm))
                {{$application->allocated_sqm}}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Allocated Booth Number</th>
        <td>
            @if(isset($application->stallNumber))
                {{$application->stallNumber}}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    @endif
</table>
</table>

  <!-- Primary Contact -->
  <div class="section-title">Primary Contact Person</div>
  <table class="custom-table">
    @if(isset($application->eventContact))
      <tr><th>Full Name</th><td>
        {{ $application->eventContact->salutation ?? '' }} {{ $application->eventContact->first_name ?? '' }} {{ $application->eventContact->last_name ?? '' }}
      </td></tr>
      <tr><th>Job Title</th><td>{{ $application->eventContact->job_title ?? '' }}</td></tr>
      <tr><th>Email</th><td>{{ $application->eventContact->email ?? '' }}</td></tr>
      <tr><th>Phone</th><td>{{ $application->eventContact->contact_number ?? '' }}</td></tr>
    @else
      <tr><td colspan="2" class="text-danger">Primary contact information not provided.</td></tr>
    @endif
  </table>
  @if(isset($application->eventContact) && isset($application->eventContact->contact_number) && isset($application->eventContact->mobile_number) && $application->eventContact->contact_number != $application->eventContact->mobile_number)
    <div class="section-title">Secondary Contact Person</div>
    <table class="custom-table">
      @if(isset($application->secondaryEventContact))
        <tr><th>Full Name</th><td>
          {{ $application->secondaryEventContact->salutation ?? '' }} {{ $application->secondaryEventContact->first_name ?? '' }} {{ $application->secondaryEventContact->last_name ?? '' }}
        </td></tr>
        <tr><th>Job Title</th><td>{{ $application->secondaryEventContact->job_title ?? '' }}</td></tr>
        <tr><th>Email</th><td>{{ $application->secondaryEventContact->email ?? '' }}</td></tr>
        <tr><th>Phone</th><td>{{ $application->secondaryEventContact->contact_number ?? '' }}</td></tr>
      @else
        <tr><td colspan="2" class="text-danger">Secondary contact information not provided.</td></tr>
      @endif
    </table>
  @endif

  <!-- Type of Business -->
  {{-- <div class="section-title">Type of Business</div>
  <table class="custom-table">
    <tr><th>Manufacturer</th><td><span class="check">✔️</span></td></tr>
    <tr><th>Other Types</th><td><span class="cross">❌</span></td></tr>
  </table> --}}

  <!-- Billing Details -->
  <div class="section-title">Billing Details</div>
<table class="custom-table">
    <tr>
        <th>Billing Company</th>
        <td>
            @if(isset($application->billingDetail) && $application->billingDetail->billing_company)
                {{$application->billingDetail->billing_company}}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Contact Name</th>
        <td>
            @if(isset($application->billingDetail) && $application->billingDetail->contact_name)
                {{$application->billingDetail->contact_name}}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Billing Address</th>
        <td>
            @if(isset($application->billingDetail) && $application->billingDetail->address && $application->billingDetail->city_id && $application->billingDetail->state && $application->billingDetail->state->name && $application->billingDetail->postal_code && $application->billingDetail->country && $application->billingDetail->country->name)
                {{$application->billingDetail->address}}, {{$application->billingDetail->city_id}}, {{$application->billingDetail->state->name}} – {{$application->billingDetail->postal_code}}, {{$application->billingDetail->country->name}}
            @else
                <span class="text-danger">Incomplete billing address information</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>Email</th>
        <td>
            @if(isset($application->billingDetail) && $application->billingDetail->email)
                {{$application->billingDetail->email}}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    <tr>
        <th>GST Compliance</th>
        <td>
            @if(isset($application->gst_compliance))
                {{ $application->gst_compliance == 1 ? 'Yes' : 'No' }}
            @else
                <span class="text-danger">Not provided</span>
            @endif
        </td>
    </tr>
    @if(isset($application->gst_compliance) && $application->gst_compliance == 1)
        <tr>
            <th>GST Number</th>
            <td>
                {{ isset($application->gst_no) && $application->gst_no ? $application->gst_no :  '<span class="text-danger">Not provided</span>' }}
            </td>
        </tr>
        <tr>
            <th>PAN Number</th>
            <td>
                @if(isset($application->pan_no) && $application->pan_no)
                    {{$application->pan_no}}
                @else
                    <span class="text-danger">Not provided</span>
                @endif
            </td>
        </tr>
    @endif
</table>
  </table>

  <!-- Terms -->
  <div class="section-title">Terms and Conditions</div>
<div class="terms">
  <h4 class="mb-3">PRIVACY POLICY, TERMS AND CONDITIONS, CANCELLATION AND REFUND POLICY</h4>

  <h5 class="mt-4">PRIVACY POLICY</h5>
  <p>
    Bengaluru Tech Summit SECRETARIAT ("us", "we", "BTS", or "our") operates <a href="https://www.bengalurutechsummit.com/" target="_blank">https://www.bengalurutechsummit.com/</a>. The user data collected is through voluntary submission by the user. This data may be transmitted within the organization to individuals or departments to ensure usage in a manner and restricted to the purpose for which the data has been submitted by the user. By submitting data on the website, the user is providing explicit consent to the above, towards the fulfilment of their explicit request. We do not sell our data to third parties. If you choose to voluntarily participate in any survey, we reserve the right to publish results or comments derived from this survey. We use your Personal Information for providing and improving your Site experience. By using the Site, you agree to the collection and use of information in accordance with this policy.
  </p>
  <h6>Information Collection and Use</h6>
  <p>
    While using our site, we may ask you to provide us with certain personally identifiable information that can be used to contact or identify you. Personally identifiable information may include, but is not limited to your name ("Personal Information"). By supplying your contact information &amp; other information asked in the forms, you agree that we may share your contact information &amp; other required information with our partners for marketing purposes.
  </p>
  <h6>Log Data</h6>
  <p>
    Like many site operators, we collect information that your browser sends whenever you visit our Site ("Log Data"). This Log Data may include information such as your computer's Internet Protocol ("IP") address, browser type, browser version, the pages of our Site that you visit, the time and date of your visit, the time spent on those pages and other statistics. We may use third party services such as Google Analytics that collect, monitor and analyse. We collect only personally identifiable information that is specifically and voluntarily provided by the user to our website. Typically, identifying information is collected to register for certain areas of the site, enquire for further information, distribute requests, or to send various updates and promotional content. In case of any third party links appearing on our website, we advise visitors to review each site's privacy policy before disclosing any personally identifiable information.
  </p>
  <h6>Communications</h6>
  <p>
    We may use your Personal Information to contact you with event updates, registration updates, newsletters, marketing or promotional materials and other information.
  </p>
  <h6>Cookies</h6>
  <p>
    Cookies are files with small amounts of data, which may include an anonymous unique identifier. Cookies are sent to your browser from a website and stored on your computer's hard drive. Like many sites, we use "cookies" to collect information. You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our Site. The security of your Personal Information is important to us, but remember that no method of transmission over the Internet, or method of electronic storage, is 100% secure. While we strive to use commercially acceptable means to protect your Personal Information, we cannot guarantee its absolute security.
  </p>
  <h6>Compliance</h6>
  <ol>
    <li>All data derived from data records included in the direct marketing list including inter alia duplicates, copies and other versions of the direct marketing list shall be appropriately marked so the source of the data is identifiable, and shall have an expiry date for usage of the data records.</li>
    <li>Where data elements of a data record are combined with data elements from other sources to form a composite record, the licensee shall ensure each data element can be identified and removed or changed as required.</li>
    <li>We shall not be responsible for data records or data elements where the data elements or data records are sourced in whole or part from other sources.</li>
  </ol>
  <h6>Changes To This Privacy</h6>
  <p>
    This Privacy Policy is effective as of 24th November 2019 and will remain in effect except with respect to any changes in its provisions in the future, which will be in effect immediately after being posted on this page. We reserve the right to update or change our Privacy Policy at any time and you should check this page periodically. Your continued use of the service after we post any modifications to the Privacy Policy on this page will constitute your acknowledgment of the modifications and your consent to abide and be bound by the modified Privacy Policy. Changes in the Policies will be notified to the user prominently on the website. The Secretariat reserves the right to modify or amend these policies at any time.
  </p>

  <h5 class="mt-4">REGISTRATION AND PAYMENT PROCESS FLOW</h5>
  <ol>
    <li>Go to website (<a href="https://www.bengalurutechsummit.com/" target="_blank">Click Here</a>) to fill the registration form.</li>
    <li>After completely filling registration form click on 'Pay Now' button to connect with Payment Gateway and complete your payment.</li>
    <li>Once your payment is credited to our bank account, then your registration will be considered completed.</li>
  </ol>

  <h5 class="mt-4">Terms and Conditions for Delegate Registration</h5>
  <ul>
    <li>As seats are limited, confirmation of delegates will be on a first come first serve basis only.</li>
    <li>The Conference Program is subject to alterations at the discretion of the organizers.</li>
    <li>No Credit Facility would be extended under any circumstances and NO REFUND / ADJUSTMENT for "NO SHOW" or "Absence" of a delegate would be accommodated.</li>
    <li>"Delegate Confirmation Letter" will be issued only after receipt and realization of appropriate fees including applicable taxes along with the relevant details on the registration form.</li>
    <li>Delegate Badges/logins issued are "non-transferable". The organizers reserve the right to disallow a confirmed delegate from transferring their badge/login to any other person.</li>
    <li>Registrations will be online. All Delegates, Exhibitors or any other category registrations, are requested to have their digital device ready with good internet connection (minimum speed of 5MBPS) for smooth experience of event.</li>
    <li>By registering for event you agree not to sell, trade, transfer, or share your paid/complimentary/any type of access link and/or code/login details.</li>
    <li>Participants should not record or broadcast images, audios or videos of event without written consent from organisers.</li>
    <li>We reserve the right to deny/remove you from the event if, in our sole discretion, your participation or behaviour creates a disruption or hinders the event or you violate any policy or the enjoyment of the event by other attendees.</li>
    <li>If your participation is denied/cancelled/removed from event then we retain all payments made by you and no payments will be refunded.</li>
    <li>Any unlawful activity will be reported to law enforcement authorities, and participant(s) involved will be banned in this event as well as for future participation.</li>
  </ul>

  <h5 class="mt-4">Cancellation And Refund Policy For Delegate Registration</h5>
  <ul>
    <li>Refunds against cancellations will be provided as per the below conditions:</li>
    <li>For Cancellation requests received by the event secretariat on email On or Before 30th July 2025, 75% of the amount paid will be refunded.</li>
    <li>For Cancellation requests received by the event secretariat on email On or Before 15th August 2025, 50% of the amount paid will be refunded.</li>
    <li>Cancellation requests received by the event secretariat On or after 16th August 2025 will not be entitled to any refund.</li>
    <li>Registration transfer/substitution requests received by the event secretariat on email on or before 25th August 2025 only will be taken into consideration.</li>
    <li>Registration transfer/substitution requests received by the event secretariat after 25th August 2025 will not be taken into consideration.</li>
    <li>Requests for registration transfer/substitution must be emailed to <a href="mailto:enquiry@bengalurutechsummit.com">enquiry@bengalurutechsummit.com</a></li>
    <li>Any kind of participation will not be refunded if user is not able to attend event due to any reasons.</li>
  </ul>

  <h5 class="mt-4">Exhibition Terms And Conditions &amp; Exhibition Cancellation And Refund Policy</h5>
  <ul>
    <li>Bengaluru Tech Summit 2025 (BTS 2025) will, hereinafter be referred to as BTS 2025 and Participant, or in the alternative, party or parties, who book(s) space in "BTS 2025" Exhibition, will hereinafter be referred to as Exhibitor(s).</li>
    <li>BTS 2025 Secretariat will allot exhibition space/stalls to exhibitor(s) on a first come first served basis.</li>
    <li>BTS 2025 Secretariat will allot space/stalls to Exhibitor(s) only on receipt of confirmation of space/stall booking in writing along with the 50% Advance payment by Bank Transfer (NEFT or RTGS) by the duly filled Application form.</li>
    <li>The balance payment of space/stall charges shall be made full payment 1 month before the event date November 19th, 2025. In event of default of payment as aforesaid, BTS 2025 reserves the right to cancel the booking, and the advance paid will stand forfeited.</li>
    <li>All Bank transfer to be made in favour of "MM Activ Sci-Tech Communications Pvt. Ltd."</li>
    <li>BTS 2025 reserves its right to change the floor plan and the location of the Exhibitor(s) space/stall. Exhibitor(s) will be stationed as per their original booking or equivalent thereof.</li>
    <li>BTS 2025 will make appropriate security arrangements. However, BTS 2025 shall not be liable for any loss and/or damage to Exhibitor's equipment, display materials, samples, etc. due to any reason whatsoever.</li>
    <li>BTS 2025 reserves the right to repossess or reallot space/stall which is not occupied prior to and at the time of inauguration.</li>
    <li>The schedule of the space/stall possession shall be given to the exhibitor at the time of confirmation and the same is also mentioned in the Exhibitor Manual. Exhibitor(s) shall strictly adhere to it.</li>
    <li>Exhibitor(s) shall not assign this contract or a part thereof without prior written consent from Bengaluru Tech Summit 2025 (BTS 2025).</li>
    <li>Exhibitor(s) will care for the flooring, walls and all other structures during the space/stall decoration and also while bringing and removing materials. On these matters follow such instructions as BTS 2025 provides from time to time. This is the essence of the contract.</li>
    <li>Exhibitor(s) shall ensure that no hazardous or inflammable materials are kept in the space/stall and shall also ensure to adopt adequate safety measures in this regard.</li>
    <li>Exhibitor(s) shall make good to BTS 2025 all costs, damages and expenses resulting from fire or any damages caused to the structure, etc, due to Exhibitor's negligence.</li>
    <li>Exhibitor(s) shall ensure that all demonstrations, interviews, advertising, promotion and other sales and marketing activities are conducted within the space/stall. Aisle space must always be kept clear for the visitors.</li>
    <li>Audio Visual displays may be conducted within the space/stall at low volumes.</li>
    <li>BTS 2025 does not accept any responsibility for any misquotations, omissions or other errors that may occur in the compilation of any publication related to the exhibition.</li>
    <li>
      <ul>
        <li>In the event of the cancellations of space/stall booking, advance payments made towards the same will be forfeited.</li>
        <li>Reduction in booked space/stall area will also be treated as cancellations of entire space/stall area booked, and advance payment made towards the same will be forfeited.</li>
      </ul>
    </li>
    <li>Exhibitor(s) must ensure that the interior decoration of the space/stall is completed before the inauguration as per the time indicated in the schedule which will be given to the Exhibitor upon confirmation of Participation.</li>
    <li>Exhibitor(s) should take approval from the organizers for their stall designs before constructing their stalls.</li>
    <li>Exhibitor(s) shall ensure that the space/stall is vacated as per the time indicated in the schedule, which will be given to the Exhibitor upon confirmation of participation. BTS 2025 will not in any way be responsible and/or liable for the general security after the close of the Exhibition on the last day.</li>
    <li>Every Exhibitor(s) shall ensure that their space is open to view and is staffed by competent representatives during Exhibition hours.</li>
    <li>Organizers will not be responsible for the provision of stabilized power at BTS 2025.</li>
    <li>The organizers reserve the right to cancel and/or change the booth number/location at any time notwithstanding any previous allotment made.</li>
    <li>If the Exhibitor(s) commits any breach of this contract and fails to remedy it promptly on receiving written notice from BTS 2025 then BTS 2025 may, by a written notice, terminate this contract. Upon termination under this clause or otherwise, Exhibitor will forthwith vacate possession of the same. Exercise of rights under this clause will prejudice any rights or remedies of Bengaluru Tech Summit 2025 (BTS 2025).</li>
    <li>The foregoing terms and conditions shall prevail notwithstanding any variations contained in the terms and conditions or other documents, submitted by the Exhibitor(s).</li>
    <li>BTS 2025 will not be liable for any delays or failure in the performance of any of its obligations under, or arising out of the contract if the delay or the failure results from any of the following: Force majeure, acts of God, fire, flood, earthquake, storm, explosion, accidents, riots, strikes, lockouts, bandh, closures, war, civil unrest, industrial disputes, embargoes of any state of emergency, or any other Govt. Act, law, statute ordinance whatsoever which renders it impossible or impractical for BTS 2025 and Exhibitor(s) to perform and all previous negotiation, commitments, and agreements between parties pertaining to this transaction stands cancelled.</li>
    <li>The parties agree that competent courts at Karnataka, India only shall have exclusive jurisdiction over all disputes arising out of this contract.</li>
  </ul>

  <h5 class="mt-4">Change in Policies / Dispute Resolution</h5>
  <p>
    We may, at any time, and at our sole discretion, modify these PRIVACY POLICY, TERMS AND CONDITIONS, CANCELLATION AND REFUND POLICY, including our other terms of use, with or without notice to the User. Any such modification will be effective immediately upon public posting. Your continued use of our Service and this Site following any such modification constitutes your acceptance of these modified Terms.
  </p>
  <p>
    In case of dispute Bengaluru Tech Summit SECRETARIAT words will be considered as final.
  </p>

  <h5 class="mt-4">Contact</h5>
  <p>
    If you have any questions about this Privacy Policy, please contact us:<br>
    <strong>Bengaluru Tech Summit 2025 SECRETARIAT</strong><br>
    Email: <a href="mailto:enquiry@bengalurutechsummit.com">enquiry@bengalurutechsummit.com</a><br>
    No.11/6, NITON, Block "C"<br>
    Second Floor, Palace Road<br>
    Bengaluru - 560001, Karnataka, India
  </p>
</div>

  <!-- Footer -->
  <div class="footer">
    Submitted on: {{ \Carbon\Carbon::parse($application->submission_date)->format('d M Y') }}
    <br>
  </div>

</body>
</html>
