@php
  $name = $name ?? 'Exhibitor';
  $loginEmail = $loginEmail ?? 'exhibitor@example.com';
  $loginUrl = $loginUrl ?? 'https://portal.semiconindia.org/login';
  $forgotUrl = $forgotUrl ?? 'https://portal.semiconindia.org/forgot-password';
  $supportEmail = $supportEmail ?? 'semiconindia@mmactiv.com';
  $year = now()->year;
@endphp
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>SEMICON India 2025 – Exhibitor Portal</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
</head>
<body style="margin:0;padding:0;background:#f2f4f6;">
  <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f2f4f6;">
    <tr>
      <td align="center" style="padding:24px 12px;">
        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e6e8ec;">
          <tr>
            <td style="padding:20px 24px;background:#0f172a;color:#ffffff;font-family:Arial,Helvetica,sans-serif;">
              <h2 style="margin:0;font-size:20px;line-height:1.4;">SEMICON India 2025 — Exhibitor Portal Access</h2>
            </td>
          </tr>

          <tr>
            <td style="padding:24px;font-family:Arial,Helvetica,sans-serif;color:#111827;font-size:15px;line-height:1.6;">
              <p style="margin:0 0 16px;">Dear {{ $name }},</p>

              <p style="margin:0 0 16px;">
                We noticed that you have not yet accessed the <strong>Exhibitor Portal</strong> for SEMICON India 2025.
                The portal is the central platform for <strong>ordering additional items</strong>, and <strong>managing Exhibitor and Inaugural Passes</strong>.
              </p>

              <p style="margin:0 0 16px;">
                To log in, please use your registered email ID:
                <a href="mailto:{{ $loginEmail }}" style="color:#2563eb;text-decoration:none;">{{ $loginEmail }}</a>
                at
                <a href="{{ $loginUrl }}" style="color:#2563eb;text-decoration:none;">{{ $loginUrl }}</a>.
              </p>

              <p style="margin:0 0 16px;">
                If you’ve forgotten your password, you can reset it here:
                <a href="{{ $forgotUrl }}" style="color:#2563eb;text-decoration:none;">Reset Password</a>
                (or use {{ $forgotUrl }}).
              </p>

              <table role="presentation" cellspacing="0" cellpadding="0" style="margin:16px 0 20px;">
                <tr>
                  <td>
                    <a href="{{ $loginUrl }}" style="display:inline-block;background:#2563eb;color:#ffffff;text-decoration:none;font-weight:bold;border-radius:6px;padding:10px 16px;font-size:14px;">Log in to Exhibitor Portal</a>
                  </td>
                  <td width="8"></td>
                  <td>
                    <a href="{{ $forgotUrl }}" style="display:inline-block;background:#0ea5e9;color:#ffffff;text-decoration:none;font-weight:bold;border-radius:6px;padding:10px 16px;font-size:14px;">Reset Password</a>
                  </td>
                </tr>
              </table>

              <p style="margin:0 0 10px;"><strong>Once logged in, you can:</strong></p>
              <ul style="margin:0 0 16px 20px;padding:0;">
                <li style="margin:0 0 8px;">Place orders for additional items and services.</li>
                <li style="margin:0 0 8px;">Access registration details — your exhibitor team has been allocated <strong>Inaugural Passes</strong> for smooth entry and participation.</li>
                <li style="margin:0 0 8px;">Order the <strong>Lead Generation Tool</strong> (Item No: <strong>MMA 40</strong>) under Extra Requirements.</li>
                <li style="margin:0 0 8px;">Request <strong>Hostesses/Additional Manpower</strong> (Item Nos: <strong>MMA 41</strong> &amp; <strong>MMA 42</strong>).</li>
              </ul>

              <p style="margin:0 0 16px;">
                If you have already logged in, submitted your design, or placed your orders, kindly ignore this message.
              </p>

              <p style="margin:0 0 2px;">Warm regards,</p>
              <p style="margin:0 0 16px;"><strong>Team SEMICON India 2025</strong></p>

              <hr style="border:none;border-top:1px solid #e6e8ec;margin:16px 0;">
              <p style="margin:0;color:#6b7280;font-size:12px;line-height:1.4;">
                Need help? Write to
                <a href="mailto:{{ $supportEmail }}" style="color:#2563eb;text-decoration:none;">{{ $supportEmail }}</a>.
              </p>
            </td>
          </tr>

        </table>

        <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;">
          <tr>
            <td style="padding:12px 8px;text-align:center;color:#6b7280;font-family:Arial,Helvetica,sans-serif;font-size:12px;">
              © {{ $year }} SEMICON India. All rights reserved.
            </td>
          </tr>
        </table>

      </td>
    </tr>
  </table>
</body>
</html>
