<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 40px;
            max-width: 800px;
            margin: 0 auto;
        }
        .form-title {
            color: #667eea;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        .required {
            color: #dc3545;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 12px;
            transition: all 0.3s;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .domain-checkbox {
            margin-bottom: 10px;
        }
        .domain-checkbox input[type="checkbox"] {
            margin-right: 8px;
        }
        .captcha-container {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .captcha-image {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 5px;
            background: #f8f9fa;
        }
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 40px;
            font-weight: 600;
            border-radius: 8px;
            color: white;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 5px;
        }
        .alert {
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
