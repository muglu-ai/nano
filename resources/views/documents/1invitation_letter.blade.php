<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Participation Letter - {{$data['companyName']}}</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 40px;
      line-height: 1.6;
      color: #000;
    }

    .letter-header {
      margin-bottom: 10px;
    }

    .logo-left {
      width: 250px;
      height: auto;
    }

    .logo-right {
      width: 150px;
      height: auto;
    }

    .letter-date {
      text-align: left;
      margin-bottom: 10px;
    }

    .recipient-address {
      margin-bottom: 10px;
    }

    .subject {
      font-weight: bold;
      text-decoration: underline;
      margin-bottom: 10px;
    }

    .body-text {
      margin-bottom: 20px;
    }

    .signature {
        text-align: right;
      margin-top: 0px;
    }

    .signature-name {
      font-weight: bold;
    }

    .footer {
      margin-top: 20px;
      font-size: 14px;
      border-top: 1px solid #ccc;
      padding-top: 20px;
    }

    .seal {
      margin-top: 10px;
      width: 100px;
    }

    .seal-container {
      text-align: right;
    }
  </style>
</head>
<body id="body">
<div id="letter-print">
  <table class="letter-header" style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
    <tr>
      <td style="vertical-align: middle; width: 250px;">
        <img src="{{ public_path('asset/img/logos/SEMI_IESA_logo.jpg') }}" alt="Logo Left" class="logo-left">
      </td>
      <td style="vertical-align: middle; text-align: right;">
        <span style="margin-right: 10px; font-size: 16px; font-weight: bold;">Organized by:</span>
        <img src="{{ public_path('asset/img/logos/logo.png') }}" alt="Logo Center" style="width: 100px; height: auto; margin-right: 10px;">
        <img src="{{ public_path('asset/img/logos/mmactiv_logo.jpg') }}" alt="Logo Right" class="logo-right">
      </td>
    </tr>
  </table>

  <div class="letter-date">
    <p>Date: {{$data['date']}}</p>
  </div>

  <div class="recipient-address">
    <p>{{$data['contactPerson']}}<br>
    {{$data['companyName']}}<br>
    {{$data['Address']}}<br>
    {{$data['City']}}, {{$data['State']}} - {{$data['Pincode']}}<br>
    {{$data['Country']}}

    </p>
    <p><strong>Customer No:  {{$data['application_id']}}</strong> </p>
  </div>

  <div class="subject" style="text-align: center;">
    TO WHOM-SO-EVER IT MAY CONCERN
  </div>

  <div class="body-text">
    <p style="text-align: center;"><strong>Ref: Letter of Participation in SEMICON India 2025</strong></p>

    <p>
      This is to certify that <strong>{{$data['companyName']}}</strong> is participating as an exhibitor in <strong>SEMICON India 2025</strong>, scheduled to be held at <strong>Yashobhoomi-IICC, Convention Centre, Dwarka, New Delhi, India</strong>, from <strong>September 2nd to 4th, 2025</strong>.
    </p>

    <p>
      The organisation has been allotted booth number <strong>{{$data['boothNumber']}}</strong> in <strong>Hall No. 1</strong> for the duration of the exhibition.
    </p>

    <p>
      We kindly request all concerned authorities to extend their full support and assistance to <strong>{{$data['companyName']}}</strong> to facilitate their participation in the event.
    </p>
  </div>
<div class="seal-container">
    <img src="{{ public_path('asset/img/logos/seal.png') }}" alt="Official Seal/Stamp" class="seal">
  </div>
  <div class="signature">
    <p class="signature-name">(Nitin Chauhan)</p>
    <p>Authorized Signatory</p>
  </div>

  

  <div class="footer">
    
    <h3 style="font-weight: bold; text-align: center;">MM Activ Sci-Tech Communications Pvt. Ltd.</h3>

    <div style="text-align: center;">
    <p >
    103-104, Rohit House, 3,
Tolstoy Marg, Connaught Place
New Delhi - 110 001</p> 

    <p>Tel.:  011-4354 2737 / 011-2331 9387 | Fax: +91-11-2331 9388 | E-mail: <a href="mailto:semiconindia@mmactiv.com">semiconindia@mmactiv.com</a> | GST No. - 07AABCM2615H1ZS</p>
  </div>
</div>
</div>



</body>
</html>
