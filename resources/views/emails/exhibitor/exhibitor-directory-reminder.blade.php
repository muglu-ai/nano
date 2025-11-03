<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Bengaluru Tech Summit — Action Required</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f6;font-family:Arial, sans-serif;">
  <!-- Wrapper table -->
  <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">
    <tr>
      <td align="center" style="padding:20px 10px;">
        <!-- Center column -->
        <table role="presentation" cellpadding="0" cellspacing="0" width="680" style="max-width:680px;border-collapse:collapse;background:#ffffff;border-radius:6px;overflow:hidden;">
          <!-- Header -->
          <tr>
            <td style="padding:20px 24px;background:#0b5ed7;color:#ffffff;">
              <h1 style="margin:0;font-size:20px;font-weight:700;">Bengaluru Tech Summit — Exhibitor Portal</h1>
            </td>
          </tr>

          <!-- Body -->
          <tr>
            <td style="padding:24px;">
              <p style="margin:0 0 14px 0;font-size:15px;color:#333333;line-height:1.5;">
                Dear Participant,
              </p>

              <p style="margin:0 0 14px 0;font-size:15px;color:#333333;line-height:1.5;">
                Greetings from the Bengaluru Tech Summit team!
              </p>

              <p style="margin:0 0 18px 0;font-size:15px;color:#333333;line-height:1.5;">
                Thank you for registering as an exhibitor. We previously sent your exhibitor portal login credentials to your registered email address. Our records show that you have not yet completed the exhibitor directory form or updated your <strong>facia details</strong>.
              </p>

              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:18px 0;">
                <tr>
                  <td style="padding:12px;border:1px solid #e6e6e9;border-radius:4px;background:#fafafa;">
                    <p style="margin:0 0 8px 0;font-size:14px;color:#333;"><strong>Deadline to update facia details:</strong></p>
                    <p style="margin:0;font-size:16px;color:#d6336c;font-weight:700;">7 November 2025</p>
                  </td>
                </tr>
              </table>

              <!-- Credentials block (replace before sending) -->
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:12px 0 18px 0;">
                <tr>
                  <td style="padding:12px;border:1px dashed #dfe3e8;border-radius:4px;background:#ffffff;">
                    <p style="margin:0 0 8px 0;font-size:14px;color:#333;"><strong>Your exhibitor login credentials</strong></p>

                    <!-- Replace these placeholders with real credentials -->
                    <table role="presentation" cellpadding="4" cellspacing="0" style="border-collapse:collapse;">
                      <tr>
                        <td style="font-size:14px;color:#666;padding:6px 8px;width:140px;">Portal URL:</td>
                        <td style="font-size:14px;color:#333;padding:6px 8px;">
                          <a href="{{ $loginUrl }}" style="color:#0b5ed7;text-decoration:underline;">{{ $loginUrl }}</a>
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size:14px;color:#666;padding:6px 8px;">Email / Username:</td>
                        <td style="font-size:14px;color:#333;padding:6px 8px;">{{ $loginEmail }}</td>
                      </tr>
                      <tr>
                        <td style="font-size:14px;color:#666;padding:6px 8px;">Password:</td>
                        <td style="font-size:14px;color:#333;padding:6px 8px;">{{ $loginPassword }}</td>
                      </tr>
                    </table>

                    <p style="margin:12px 0 0 0;font-size:13px;color:#666;">
                      If you have lost your password, please use the <a href="{{ $forgotUrl }}" style="color:#0b5ed7;text-decoration:underline;"><em>Forgot Password</em></a> link on the login page to reset it.
                    </p>
                  </td>
                </tr>
              </table>

              <!-- CTA -->
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">
                <tr>
                  <td align="center" style="padding:8px 0 24px 0;">
                    <a href="{{ $loginUrl }}" style="display:inline-block;padding:12px 20px;border-radius:6px;background:#0b5ed7;color:#ffffff;text-decoration:none;font-weight:600;font-size:15px;">
                      Update Facia Details &amp; Complete Form
                    </a>
                  </td>
                </tr>
              </table>

              <p style="margin:0 0 14px 0;font-size:14px;color:#333;line-height:1.5;">
                We appreciate your prompt attention to this important step and look forward to your active participation at the Bengaluru Tech Summit.
              </p>

              <p style="margin:18px 0 0 0;font-size:14px;color:#333;line-height:1.5;">
                Warm regards,<br>
                <strong>Bengaluru Tech Summit Team</strong>
              </p>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td style="padding:14px 24px;background:#f0f2f5;color:#666;font-size:13px;">
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">
                <tr>
                  <td align="left" style="vertical-align:middle;">
                    <div style="line-height:1.4;">
                      Bengaluru Tech Summit<br>
                      <span style="color:#999;font-size:12px;">For support, reply to this email or contact your event coordinator.</span>
                    </div>
                  </td>
                  <td align="right" style="vertical-align:middle;">
                    <a href="https://www.bengalurutechsummit.com" style="font-size:13px;color:#0b5ed7;text-decoration:none;">bengalurutechsummit.com</a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

        </table><!-- End center column -->
      </td>
    </tr>
  </table>
</body>
</html>
