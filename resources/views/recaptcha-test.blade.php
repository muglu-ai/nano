<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>reCAPTCHA Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .card {
            background: #fff;
            padding: 24px 28px;
            border-radius: 8px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            max-width: 420px;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
        }
        h1 {
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 12px;
        }
        p {
            font-size: 14px;
            color: #555;
            margin-top: 0;
            margin-bottom: 16px;
        }
        button {
            margin-top: 16px;
            padding: 10px 20px;
            background: #4f46e5;
            border: none;
            border-radius: 4px;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
        }
        button:disabled {
            background: #a5b4fc;
            cursor: not-allowed;
        }
        .token-output {
            margin-top: 16px;
            font-size: 12px;
            word-break: break-all;
            color: #111827;
            background: #f3f4f6;
            padding: 8px;
            border-radius: 4px;
        }
        .note {
            font-size: 12px;
            color: #6b7280;
            margin-top: 12px;
        }
        code {
            background: #e5e7eb;
            padding: 2px 4px;
            border-radius: 3px;
        }
    </style>
    {{-- reCAPTCHA v3 / Enterprise style script, site key from .env via services.recaptcha.site_key --}}
    <script src="https://www.google.com/recaptcha/enterprise.js?render={{ config('services.recaptcha.site_key') }}"></script>
</head>
<body>
    <div class="card">
        <h1>Google reCAPTCHA Test</h1>
        <p>
            This page uses the site key from <code>.env</code> via <code>config('services.recaptcha.site_key')</code>
            and the reCAPTCHA v3 / Enterprise-style button integration.
        </p>

        <form id="demo-form" action="#" method="POST" onsubmit="return false;">
            @csrf
            <button
                id="recaptchaButton"
                class="g-recaptcha"
                data-sitekey="{{ config('services.recaptcha.site_key') }}"
                data-callback="onSubmit"
                data-action="submit">
                Run reCAPTCHA v3
            </button>
        </form>

        <div id="tokenOutput" class="token-output" style="display:none;"></div>

        <div class="note">
            Open the browser console to see detailed messages.
        </div>
    </div>

    <script>
        // Callback used by the button (see data-callback="onSubmit")
        function onSubmit(token) {
            var output = document.getElementById('tokenOutput');

            console.log('reCAPTCHA token:', token);
            output.style.display = 'block';
            output.textContent =
                'Received token from reCAPTCHA v3 / Enterprise-style button:\\n\\n'
                + token +
                '\\n\\nThis confirms the client-side integration works with the site key from .env.';
        }
    </script>
</body>
</html>


