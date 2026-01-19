<?php
/**
 * Quick OTP Mail Test Script
 * 
 * Run this from command line:
 * php test-otp-mail.php your-email@example.com
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$testEmail = $argv[1] ?? 'test@example.com';

echo "=== OTP Mail Configuration Test ===\n\n";

// 1. Check mail configuration
echo "1. Checking Mail Configuration...\n";
$mailDriver = config('mail.default');
$mailHost = config('mail.mailers.smtp.host');
$mailPort = config('mail.mailers.smtp.port');
$mailUsername = config('mail.mailers.smtp.username');
$mailEncryption = config('mail.mailers.smtp.encryption');
$mailFrom = config('mail.from.address');

echo "   Mail Driver: " . ($mailDriver ?: 'NOT SET') . "\n";
echo "   Mail Host: " . ($mailHost ?: 'NOT SET') . "\n";
echo "   Mail Port: " . ($mailPort ?: 'NOT SET') . "\n";
echo "   Mail Username: " . ($mailUsername ?: 'NOT SET') . "\n";
echo "   Mail Encryption: " . ($mailEncryption ?: 'NOT SET') . "\n";
echo "   Mail From: " . ($mailFrom ?: 'NOT SET') . "\n";
echo "   Mail Password: " . (config('mail.mailers.smtp.password') ? 'SET (hidden)' : 'NOT SET') . "\n\n";

if (empty($mailDriver) || empty($mailHost)) {
    echo "❌ ERROR: Mail configuration is incomplete!\n";
    echo "   Please check your .env file.\n\n";
    exit(1);
}

// 2. Check email template
echo "2. Checking Email Template...\n";
$templatePath = resource_path('views/emails/delegate-otp.blade.php');
if (file_exists($templatePath)) {
    echo "   ✓ Template exists: {$templatePath}\n\n";
} else {
    echo "   ❌ Template NOT found: {$templatePath}\n\n";
    exit(1);
}

// 3. Test sending email
echo "3. Testing Email Sending...\n";
echo "   Sending test email to: {$testEmail}\n\n";

try {
    $eventName = config('constants.EVENT_NAME', 'Test Event');
    $eventYear = config('constants.EVENT_YEAR', date('Y'));
    $supportEmail = config('constants.SUPPORT_EMAIL', 'support@example.com');
    $testOtp = '123456';

    \Illuminate\Support\Facades\Mail::send('emails.delegate-otp', [
        'otp' => $testOtp,
        'eventName' => $eventName,
        'eventYear' => $eventYear,
        'supportEmail' => $supportEmail,
    ], function ($message) use ($testEmail, $eventName, $eventYear) {
        $message->to($testEmail)
            ->subject("OTP Test - {$eventName} {$eventYear}");
    });

    echo "   ✓ Email sent successfully!\n";
    echo "   Check your inbox (and spam folder) for the test email.\n\n";
    echo "   If you don't receive it, check:\n";
    echo "   - Spam/Junk folder\n";
    echo "   - Mail server logs\n";
    echo "   - Laravel logs: storage/logs/laravel.log\n\n";
    
} catch (\Swift_TransportException $e) {
    echo "   ❌ SMTP Transport Error:\n";
    echo "   " . $e->getMessage() . "\n\n";
    echo "   Common fixes:\n";
    echo "   - Check MAIL_HOST and MAIL_PORT\n";
    echo "   - Verify MAIL_USERNAME and MAIL_PASSWORD\n";
    echo "   - For Gmail, use App Password (not regular password)\n";
    echo "   - Check firewall allows SMTP connections\n\n";
    exit(1);
    
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    echo "   Error Class: " . get_class($e) . "\n";
    echo "   Check storage/logs/laravel.log for details.\n\n";
    exit(1);
}

echo "=== Test Complete ===\n";
