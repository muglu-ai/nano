<?php
// exit;
set_time_limit(0);
/**
 * Exhibitor Directory Reminder Email Script
 * 
 * This script sends reminder emails to exhibitors who haven't completed
 * their exhibitor directory form or updated their facia details.
 * 
 * Usage: php public/helpTool/exhibitorReminder.php
 */

// Include email function
require_once __DIR__ . '/emailFunction.php';

// Database configuration
$host = "95.216.2.164"; // Adjust if needed
$username = "btsblnl265_asd1d_bengaluruite"; // Adjust if needed
$password = "Disl#vhfj#Af#DhW65"; // Adjust if needed
$database = "btsblnl265_asd1d_portal"; // Adjust if needed

// Portal URLs - Update these with your actual URLs
$loginUrl = "https://www.bengalurutechsummit.com/portal/public/login"; // Adjust if needed
$forgotPasswordUrl = "https://www.bengalurutechsummit.com/portal/public/forgot-password"; // Adjust if needed

// Test mode: Set to true to send all emails to a test address instead of actual recipients
$testMode = false; // Set to true for testing
$testEmail = "test.interlinks@gmail.com"; // Test email address (only used if testMode is true)

// Connect to database
$link = mysqli_connect($host, $username, $password, $database);

if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($link, 'utf8mb4');

/**
 * Render email template with variables
 */
function renderEmailTemplate($loginUrl, $loginEmail, $loginPassword, $forgotUrl) {
    $html = <<<HTML
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
                Dear Participants,
              </p>

              <p style="margin:0 0 18px 0;font-size:15px;color:#333333;line-height:1.5;">
                Despite repeated emails and follow-ups, we are yet to receive your exhibitor and fascia details. We request you to kindly share the required information by <strong>November 10th, 6:00 PM</strong> to ensure smooth coordination and timely processing.
              </p>

              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:18px 0;">
                <tr>
                  <td style="padding:12px;border:1px solid #e6e6e9;border-radius:4px;background:#fafafa;">
                    <p style="margin:0 0 8px 0;font-size:14px;color:#333;"><strong>Deadline to update exhibitor and fascia details:</strong></p>
                    <p style="margin:0;font-size:16px;color:#d6336c;font-weight:700;">November 10th, 6:00 PM</p>
                  </td>
                </tr>
              </table>

              <!-- Credentials block -->
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;margin:12px 0 18px 0;">
                <tr>
                  <td style="padding:12px;border:1px dashed #dfe3e8;border-radius:4px;background:#ffffff;">
                    <p style="margin:0 0 8px 0;font-size:14px;color:#333;"><strong>Your exhibitor login credentials</strong></p>

                    <table role="presentation" cellpadding="4" cellspacing="0" style="border-collapse:collapse;">
                      <tr>
                        <td style="font-size:14px;color:#666;padding:6px 8px;width:140px;">Portal URL:</td>
                        <td style="font-size:14px;color:#333;padding:6px 8px;">
                          <a href="{$loginUrl}" style="color:#0b5ed7;text-decoration:underline;">{$loginUrl}</a>
                        </td>
                      </tr>
                      <tr>
                        <td style="font-size:14px;color:#666;padding:6px 8px;">Email / Username:</td>
                        <td style="font-size:14px;color:#333;padding:6px 8px;">{$loginEmail}</td>
                      </tr>
                      <tr>
                        <td style="font-size:14px;color:#666;padding:6px 8px;">Password:</td>
                        <td style="font-size:14px;color:#333;padding:6px 8px;">{$loginPassword}</td>
                      </tr>
                    </table>

                    <p style="margin:12px 0 0 0;font-size:13px;color:#666;">
                      If you have lost your password, please use the <a href="{$forgotUrl}" style="color:#0b5ed7;text-decoration:underline;"><em>Forgot Password</em></a> link on the login page to reset it.
                    </p>
                  </td>
                </tr>
              </table>

              <!-- CTA -->
              <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;">
                <tr>
                  <td align="center" style="padding:8px 0 24px 0;">
                    <a href="{$loginUrl}" style="display:inline-block;padding:12px 20px;border-radius:6px;background:#0b5ed7;color:#ffffff;text-decoration:none;font-weight:600;font-size:15px;">
                      Update Facia Details &amp; Complete Form
                    </a>
                  </td>
                </tr>
              </table>

              <p style="margin:0 0 14px 0;font-size:14px;color:#333;line-height:1.5;">
                We appreciate your prompt attention to this matter.
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
HTML;
    return $html;
}

/**
 * Get plain text version of email
 */
function getPlainTextEmail($loginUrl, $loginEmail, $loginPassword, $forgotUrl) {
    return <<<TEXT
Dear Participants,

Despite repeated emails and follow-ups, we are yet to receive your exhibitor and fascia details. We request you to kindly share the required information by November 10th, 6:00 PM to ensure smooth coordination and timely processing.

Deadline to update exhibitor and fascia details: November 10th, 6:00 PM

Your exhibitor login credentials:
Portal URL: {$loginUrl}
Email / Username: {$loginEmail}
Password: {$loginPassword}

If you have lost your password, please use the Forgot Password link on the login page to reset it: {$forgotUrl}

Update Facia Details & Complete Form: {$loginUrl}

We appreciate your prompt attention to this matter.

Warm regards,
Bengaluru Tech Summit Team

Website: https://www.bengalurutechsummit.com
TEXT;
}

// Query to get exhibitors who haven't completed their directory
// Get approved applications where ExhibitorInfo is missing or submission_status = 0
$query = "
    SELECT 
        a.id as application_id,
        a.company_name,
        u.id as user_id,
        u.email,
        u.name as user_name,
        u.simplePass,
        ei.id as exhibitor_info_id,
        ei.submission_status
    FROM applications a
    INNER JOIN users u ON a.user_id = u.id
    LEFT JOIN exhibitors_info ei ON a.id = ei.application_id
    WHERE a.submission_status = 'approved'
        AND u.role = 'exhibitor'
        AND (ei.id IS NULL OR ei.submission_status = 0 OR ei.submission_status IS NULL)
    ORDER BY a.id ASC
";

$result = mysqli_query($link, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($link));
}

$totalRecords = mysqli_num_rows($result);
if ($testMode) {
    echo "[" . date('Y-m-d H:i:s') . "] [TEST MODE ENABLED] Found {$totalRecords} exhibitors (emails will be sent to: {$testEmail})\n\n";
} else {
    echo "[" . date('Y-m-d H:i:s') . "] Found {$totalRecords} exhibitors to send reminders to\n\n";
}

if ($totalRecords == 0) {
    echo "[" . date('Y-m-d H:i:s') . "] No exhibitors found that need reminders\n";
    mysqli_close($link);
    exit;
}

$sentCount = 0;
$failedCount = 0;
$skippedCount = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $email = $row['email'];
    $userName = $row['email'];
    $companyName = $row['company_name'];
    $loginPassword = !empty($row['simplePass']) ? $row['simplePass'] : 'Password not available. Please use Forgot Password.';
    
    // Skip if no email
    if (empty($email)) {
        echo "[" . date('Y-m-d H:i:s') . "] Skipping: No email for user ID {$row['user_id']} (Company: {$companyName})\n";
        $skippedCount++;
        continue;
    }
    
    // Render email template
    $htmlMessage = renderEmailTemplate($loginUrl, $email, $loginPassword, $forgotPasswordUrl);
    $plainText = getPlainTextEmail($loginUrl, $email, $loginPassword, $forgotPasswordUrl);
    
    // Email subject
    $subject = "Reminder: Update Your Exhibitor & Fascia Details by November 10th - Bengaluru Tech Summit";
    
    // Determine recipient email (use test email if in test mode)
    $recipientEmail = $testMode ? $testEmail : $email;
    
    // Send email
    if ($testMode) {
        echo "[" . date('Y-m-d H:i:s') . "] [TEST MODE] Sending email to: {$recipientEmail} (Original: {$email}, Company: {$companyName})\n";
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] Sending email to: {$recipientEmail} (Company: {$companyName})\n";
    }

    // echo $htmlMessage;
    // exit;
    // $recipientEmail = 'manishksharma9801@gmail.com';
    
    $emailSent = elastic_mail($subject, $htmlMessage, array($recipientEmail), $plainText);
    
    if ($emailSent) {
        echo "[" . date('Y-m-d H:i:s') . "] ✓ Email sent successfully to: {$recipientEmail}\n";
        $sentCount++;
    } else {
        echo "[" . date('Y-m-d H:i:s') . "] ✗ Failed to send email to: {$recipientEmail}\n";
        $failedCount++;
    }

    // exit;
    
    // Small delay to avoid overwhelming the email service
    usleep(500000); // 0.5 second delay
}

mysqli_close($link);

// Summary
echo "\n";
echo "[" . date('Y-m-d H:i:s') . "] ========== Summary ==========\n";
echo "[" . date('Y-m-d H:i:s') . "] Total records processed: {$totalRecords}\n";
echo "[" . date('Y-m-d H:i:s') . "] Emails sent successfully: {$sentCount}\n";
echo "[" . date('Y-m-d H:i:s') . "] Emails failed: {$failedCount}\n";
echo "[" . date('Y-m-d H:i:s') . "] Skipped: {$skippedCount}\n";
echo "[" . date('Y-m-d H:i:s') . "] =============================\n";

