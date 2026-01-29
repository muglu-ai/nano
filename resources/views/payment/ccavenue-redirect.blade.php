<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting to CCAvenue...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: #f5f5f5;
        }
        .redirect-container {
            text-align: center;
            padding: 20px;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="redirect-container">
        <h2>Redirecting to Payment Gateway...</h2>
        <div class="spinner"></div>
        <p>Please wait while we redirect you to the secure payment page.</p>
        <p>If you are not redirected automatically, please click the button below.</p>
    </div>
    <form method="post" name="ccavenueForm" id="ccavenueForm" action="{{ $hosted_payment_url }}" style="display: none;">
        <input type="hidden" name="encRequest" value="{{ $encrypted_data }}">
        <input type="hidden" name="access_code" value="{{ $access_code }}">
        <button type="submit" id="submitBtn">Click here if not redirected</button>
    </form>
    <script>
        // Multiple fallback methods to ensure form submission
        (function() {
            function submitForm() {
                var form = document.getElementById('ccavenueForm');
                if (form) {
                    form.submit();
                }
            }
            
            // Try on page load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', submitForm);
            } else {
                submitForm();
            }
            
            // Fallback after 1 second
            setTimeout(submitForm, 1000);
            
            // Fallback after 2 seconds
            setTimeout(submitForm, 2000);
        })();
    </script>
</body>
</html>
