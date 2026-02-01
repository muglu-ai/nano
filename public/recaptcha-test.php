<?php
/**
 * reCAPTCHA Enterprise Test Page
 * 
 * This page tests the Google reCAPTCHA Enterprise integration.
 * Access via: /recaptcha-test.php
 */

// Load Laravel configuration
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$siteKey = config('services.recaptcha.site_key');
$secretKey = config('services.recaptcha.secret_key');
$recaptchaEnabled = config('constants.RECAPTCHA_ENABLED', false);

$testResult = null;
$testToken = null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['g-recaptcha-response'])) {
    $testToken = $_POST['g-recaptcha-response'];
    
    if (!empty($testToken) && !empty($secretKey)) {
        // Verify the token with Google reCAPTCHA Enterprise API
        $url = 'https://recaptchaenterprise.googleapis.com/v1/projects/' . 
               config('services.recaptcha.project_id', 'your-project-id') . 
               '/assessments?key=' . $secretKey;
        
        $data = [
            'event' => [
                'token' => $testToken,
                'siteKey' => $siteKey,
                'expectedAction' => 'test_submit'
            ]
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $testResult = [
            'http_code' => $httpCode,
            'response' => json_decode($response, true),
            'token_preview' => substr($testToken, 0, 50) . '...'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>reCAPTCHA Enterprise Test</title>
    <script src="https://www.google.com/recaptcha/enterprise.js?render=<?= htmlspecialchars($siteKey) ?>"></script>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 { color: #333; margin-bottom: 24px; }
        h2 { color: #555; font-size: 18px; margin-bottom: 16px; }
        .status { padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; }
        .status.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status.warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { font-weight: 600; width: 150px; color: #666; }
        .info-value { color: #333; word-break: break-all; }
        button {
            background: #4285f4;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.2s;
        }
        button:hover { background: #3367d6; }
        button:disabled { background: #ccc; cursor: not-allowed; }
        pre {
            background: #f8f9fa;
            padding: 16px;
            border-radius: 6px;
            overflow-x: auto;
            font-size: 13px;
            border: 1px solid #e9ecef;
        }
        .token-display {
            background: #e9ecef;
            padding: 8px 12px;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <h1>🔐 reCAPTCHA Enterprise Test</h1>
    
    <div class="card">
        <h2>Configuration Status</h2>
        
        <div class="status <?= $recaptchaEnabled ? 'success' : 'warning' ?>">
            reCAPTCHA is <strong><?= $recaptchaEnabled ? 'ENABLED' : 'DISABLED' ?></strong> in constants.php
        </div>
        
        <div class="info-row">
            <span class="info-label">Site Key:</span>
            <span class="info-value"><?= $siteKey ? substr($siteKey, 0, 20) . '...' : '<em>Not configured</em>' ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Secret Key:</span>
            <span class="info-value"><?= $secretKey ? '***configured***' : '<em>Not configured</em>' ?></span>
        </div>
    </div>
    
    <div class="card">
        <h2>Test reCAPTCHA Token Generation</h2>
        <p style="color: #666; margin-bottom: 16px;">
            Click the button below to generate a reCAPTCHA token and verify it with Google's API.
        </p>
        
        <form method="POST" id="testForm">
            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
            <button type="submit" id="testBtn">
                🧪 Test reCAPTCHA
            </button>
        </form>
        
        <div id="tokenStatus" style="margin-top: 16px;"></div>
    </div>
    
    <?php if ($testResult): ?>
    <div class="card">
        <h2>Test Results</h2>
        
        <div class="status <?= $testResult['http_code'] === 200 ? 'success' : 'error' ?>">
            HTTP Status: <?= $testResult['http_code'] ?>
        </div>
        
        <h3 style="font-size: 14px; margin-bottom: 8px;">Token Preview:</h3>
        <div class="token-display"><?= htmlspecialchars($testResult['token_preview']) ?></div>
        
        <h3 style="font-size: 14px; margin: 16px 0 8px;">API Response:</h3>
        <pre><?= htmlspecialchars(json_encode($testResult['response'], JSON_PRETTY_PRINT)) ?></pre>
        
        <?php if (isset($testResult['response']['tokenProperties']['valid'])): ?>
        <div class="status <?= $testResult['response']['tokenProperties']['valid'] ? 'success' : 'error' ?>" style="margin-top: 16px;">
            Token is <strong><?= $testResult['response']['tokenProperties']['valid'] ? 'VALID' : 'INVALID' ?></strong>
        </div>
        <?php endif; ?>
        
        <?php if (isset($testResult['response']['riskAnalysis']['score'])): ?>
        <div class="info-row" style="margin-top: 8px;">
            <span class="info-label">Risk Score:</span>
            <span class="info-value"><?= $testResult['response']['riskAnalysis']['score'] ?> (1.0 = likely human, 0.0 = likely bot)</span>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <script>
        document.getElementById('testForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('testBtn');
            const status = document.getElementById('tokenStatus');
            
            btn.disabled = true;
            btn.textContent = '⏳ Generating token...';
            status.innerHTML = '<div class="status warning">Requesting token from Google...</div>';
            
            grecaptcha.enterprise.ready(function() {
                grecaptcha.enterprise.execute('<?= $siteKey ?>', {action: 'test_submit'})
                    .then(function(token) {
                        document.getElementById('g-recaptcha-response').value = token;
                        status.innerHTML = '<div class="status success">✅ Token generated! Submitting for verification...</div>';
                        document.getElementById('testForm').submit();
                    })
                    .catch(function(error) {
                        status.innerHTML = '<div class="status error">❌ Error: ' + error.message + '</div>';
                        btn.disabled = false;
                        btn.textContent = '🧪 Test reCAPTCHA';
                    });
            });
        });
    </script>
</body>
</html>
