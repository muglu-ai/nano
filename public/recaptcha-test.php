<?php
/**
 * Simple reCAPTCHA Test Page
 * Access this at: /recaptcha-test.php
 */

// Load Laravel's environment
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$siteKey = $_ENV['RECAPTCHA_SITE_KEY'] ?? '';
$secretKey = $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';
$projectId = $_ENV['RECAPTCHA_PROJECT_ID'] ?? '';

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['g-recaptcha-response'] ?? '';
    
    if (empty($token)) {
        $message = 'No reCAPTCHA token received. The reCAPTCHA script may not have loaded.';
        $messageType = 'error';
    } elseif (empty($secretKey)) {
        $message = 'RECAPTCHA_SECRET_KEY is not configured in .env file. Token received: ' . substr($token, 0, 50) . '...';
        $messageType = 'warning';
    } else {
        // Verify the token with Google
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = [
            'secret' => $secretKey,
            'response' => $token,
            'remoteip' => $_SERVER['REMOTE_ADDR']
        ];
        
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];
        
        $context = stream_context_create($options);
        $result = @file_get_contents($url, false, $context);
        
        if ($result === false) {
            $message = 'Failed to connect to Google reCAPTCHA verification server.';
            $messageType = 'error';
        } else {
            $response = json_decode($result, true);
            if ($response['success']) {
                $score = $response['score'] ?? 'N/A';
                $message = "✅ reCAPTCHA verification SUCCESSFUL! Score: {$score}";
                $messageType = 'success';
            } else {
                $errors = implode(', ', $response['error-codes'] ?? ['Unknown error']);
                $message = "❌ reCAPTCHA verification FAILED. Errors: {$errors}";
                $messageType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reCAPTCHA Test</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1 { color: #333; margin-top: 0; }
        h2 { color: #666; font-size: 1.2rem; margin-top: 25px; }
        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .config-item:last-child { border-bottom: none; }
        .label { font-weight: 600; color: #555; }
        .value { font-family: monospace; }
        .value.set { color: #28a745; }
        .value.not-set { color: #dc3545; }
        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .message.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message.warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        button {
            background: #4285f4;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
        button:hover { background: #357abd; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        #recaptcha-status { margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔐 reCAPTCHA Configuration Test</h1>
        
        <h2>Environment Variables</h2>
        <div class="config-item">
            <span class="label">RECAPTCHA_SITE_KEY</span>
            <span class="value <?= $siteKey ? 'set' : 'not-set' ?>">
                <?= $siteKey ? '✓ Set (' . substr($siteKey, 0, 10) . '...)' : '✗ Not Set' ?>
            </span>
        </div>
        <div class="config-item">
            <span class="label">RECAPTCHA_SECRET_KEY</span>
            <span class="value <?= $secretKey ? 'set' : 'not-set' ?>">
                <?= $secretKey ? '✓ Set (' . substr($secretKey, 0, 10) . '...)' : '✗ Not Set' ?>
            </span>
        </div>
        <div class="config-item">
            <span class="label">RECAPTCHA_PROJECT_ID</span>
            <span class="value <?= $projectId ? 'set' : 'not-set' ?>">
                <?= $projectId ? '✓ Set (' . $projectId . ')' : '✗ Not Set (optional)' ?>
            </span>
        </div>
        
        <div id="recaptcha-status">
            <strong>Script Status:</strong> <span id="script-status">Loading...</span>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="message <?= $messageType ?>">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <div class="card">
        <h2>Test reCAPTCHA Verification</h2>
        
        <?php if (empty($siteKey)): ?>
        <div class="message error">
            Cannot test: RECAPTCHA_SITE_KEY is not configured in your .env file.
        </div>
        <?php else: ?>
        <form method="POST" id="test-form">
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            <button type="submit" id="submit-btn" disabled>
                Loading reCAPTCHA...
            </button>
        </form>
        <?php endif; ?>
    </div>

    <div class="card">
        <h2>Setup Instructions</h2>
        <p>Add these to your <code>.env</code> file:</p>
        <pre style="background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto;">
RECAPTCHA_SITE_KEY=your_site_key_here
RECAPTCHA_SECRET_KEY=your_secret_key_here</pre>
        <p>Get your keys from: <a href="https://www.google.com/recaptcha/admin" target="_blank">Google reCAPTCHA Admin Console</a></p>
        <p><strong>Note:</strong> Use reCAPTCHA v3 or Enterprise keys.</p>
    </div>

    <?php if (!empty($siteKey)): ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?= htmlspecialchars($siteKey) ?>"></script>
    <script>
        const statusEl = document.getElementById('script-status');
        const submitBtn = document.getElementById('submit-btn');
        const form = document.getElementById('test-form');
        
        // Check if grecaptcha loads
        let checkCount = 0;
        const checkRecaptcha = setInterval(function() {
            checkCount++;
            if (typeof grecaptcha !== 'undefined') {
                clearInterval(checkRecaptcha);
                statusEl.innerHTML = '<span class="badge badge-success">✓ Loaded Successfully</span>';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Test reCAPTCHA Verification';
            } else if (checkCount > 50) {
                clearInterval(checkRecaptcha);
                statusEl.innerHTML = '<span class="badge badge-danger">✗ Failed to Load</span> (Check browser console for errors)';
            }
        }, 100);
        
        // Handle form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBtn.disabled = true;
            submitBtn.textContent = 'Verifying...';
            
            if (typeof grecaptcha === 'undefined') {
                alert('reCAPTCHA not loaded!');
                return;
            }
            
            grecaptcha.ready(function() {
                grecaptcha.execute('<?= htmlspecialchars($siteKey) ?>', {action: 'test'})
                    .then(function(token) {
                        document.getElementById('g-recaptcha-response').value = token;
                        form.submit();
                    })
                    .catch(function(error) {
                        console.error('reCAPTCHA error:', error);
                        alert('reCAPTCHA error: ' + error);
                        submitBtn.disabled = false;
                        submitBtn.textContent = 'Test reCAPTCHA Verification';
                    });
            });
        });
    </script>
    <?php else: ?>
    <script>
        document.getElementById('script-status').innerHTML = '<span class="badge badge-danger">✗ Cannot load - No Site Key</span>';
    </script>
    <?php endif; ?>
</body>
</html>
