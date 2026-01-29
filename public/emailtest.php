<?php

// Simple email send test script
// Usage: /emailtest.php?to=user@example.com&subject=Test

declare(strict_types=1);

header('Content-Type: application/json');

try {
    // Bootstrap Laravel
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';

    // Bootstrap the console kernel to initialize the framework and config
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    /** @var \Illuminate\Mail\Mailer $mailer */
    $mailer = $app->make('mailer');

    // Parameters
    $envDefaultTo = getenv('MAIL_TO_TEST') ?: getenv('MAIL_USERNAME');
    $to = isset($_GET['to']) && filter_var($_GET['to'], FILTER_VALIDATE_EMAIL)
        ? $_GET['to']
        : ($envDefaultTo ?: (config('mail.from.address') ?: null));

    if (!$to) {
        echo json_encode([
            'success' => false,
            'message' => 'Provide a valid recipient via ?to=user@example.com or configure mail.from.address',
        ]);
        exit;
    }

    $subject = isset($_GET['subject']) && is_string($_GET['subject']) && $_GET['subject'] !== ''
        ? $_GET['subject']
        : 'Mail transport test';

    $body = "This is a test email from emailtest.php on " . date('Y-m-d H:i:s') . 
            "\nApp: " . (config('app.name') ?: 'Laravel App') . 
            "\nEnvironment: " . (config('app.env') ?: 'production');

    // Diagnostics
    $driver = (string) config('mail.default');
    $mailerConfig = (array) config('mail.mailers.' . $driver, []);
    $fromAddress = (string) config('mail.from.address');
    $fromName = (string) config('mail.from.name');
    $nonDelivery = in_array($driver, ['log', 'array'], true);

    // SMTP reachability test
    $smtpTest = null;
    if ($driver === 'smtp') {
        $host = (string) ($mailerConfig['host'] ?? '');
        $port = (int) ($mailerConfig['port'] ?? 0);
        $timeout = 10;
        $errno = 0;
        $errstr = '';
        $start = microtime(true);
        $conn = @fsockopen($host, $port, $errno, $errstr, $timeout);
        $latencyMs = (int) round((microtime(true) - $start) * 1000);
        if ($conn) {
            fclose($conn);
            $smtpTest = [
                'reachable' => true,
                'host' => $host,
                'port' => $port,
                'latency_ms' => $latencyMs,
            ];
        } else {
            $smtpTest = [
                'reachable' => false,
                'host' => $host,
                'port' => $port,
                'error' => $errstr,
                'errno' => $errno,
                'latency_ms' => $latencyMs,
            ];
        }
    }

    // Send raw text email (synchronous)
    $mailer->raw($body, function ($message) use ($to, $subject, $fromAddress, $fromName) {
        if (!empty($fromAddress)) {
            $message->from($fromAddress, $fromName ?: null);
        }
        $message->to($to)->subject($subject);
    });

    echo json_encode([
        'success' => true,
        'message' => $nonDelivery ? 'Processed with non-delivery driver (log/array).' : 'Dispatched to mail transport. Check inbox/spam.',
        'to' => $to,
        'subject' => $subject,
        'driver' => $driver,
        'defaults' => [
            'MAIL_TO_TEST' => getenv('MAIL_TO_TEST') ?: null,
            'MAIL_USERNAME' => getenv('MAIL_USERNAME') ?: null,
            'mail.from.address' => $fromAddress,
        ],
        'from' => [
            'address' => $fromAddress,
            'name' => $fromName,
        ],
        'smtp_test' => $smtpTest,
        'mailer_config' => [
            'transport' => $mailerConfig['transport'] ?? null,
            'host' => $mailerConfig['host'] ?? null,
            'port' => $mailerConfig['port'] ?? null,
            'encryption' => $mailerConfig['encryption'] ?? null,
            'username_present' => !empty($mailerConfig['username']),
            'timeout' => $mailerConfig['timeout'] ?? null,
        ],
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send test email',
        'error' => $e->getMessage(),
    ]);
}


