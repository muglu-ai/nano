<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            max-width: 450px;
            width: 100%;
        }
        .login-header {
            background: #004aad;
            color: white;
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <div class="login-header">
                        <h3><i class="fas fa-key me-2"></i>Reset Password</h3>
                        <p class="mb-0">{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</p>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('delegate.password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">
                            
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-check me-2"></i>Reset Password
                            </button>
                            <div class="text-center">
                                <a href="{{ route('delegate.login') }}" class="text-decoration-none">Back to Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
