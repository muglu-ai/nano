<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Delegate Login - {{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</title>
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
        .login-tabs {
            border-bottom: 2px solid #e9ecef;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #666;
        }
        .nav-tabs .nav-link.active {
            color: #004aad;
            border-bottom: 2px solid #004aad;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="login-card">
                    <div class="login-header">
                        <h3><i class="fas fa-user-circle me-2"></i>Delegate Login</h3>
                        <p class="mb-0">{{ config('constants.EVENT_NAME') }} {{ config('constants.EVENT_YEAR') }}</p>
                    </div>
                    <div class="card-body p-4">
                        <ul class="nav nav-tabs login-tabs mb-4" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#password-tab" type="button">Password</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#otp-tab" type="button">OTP</button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- Password Login Tab -->
                            <div class="tab-pane fade show active" id="password-tab">
                                <form method="POST" action="{{ route('delegate.login') }}">
                                    @csrf
                                    @if($errors->has('email') || $errors->has('password'))
                                        <div class="alert alert-danger">
                                            {{ $errors->first('email') ?: $errors->first('password') }}
                                        </div>
                                    @endif
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Password</label>
                                        <input type="password" name="password" class="form-control" required>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mb-3">
                                        <i class="fas fa-sign-in-alt me-2"></i>Login
                                    </button>
                                    <div class="text-center">
                                        <a href="{{ route('delegate.password.forgot') }}" class="text-decoration-none">Forgot Password?</a>
                                    </div>
                                </form>
                            </div>

                            <!-- OTP Login Tab -->
                            <div class="tab-pane fade" id="otp-tab">
                                <form id="otp-send-form">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" id="otp-email" class="form-control" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 mb-3">
                                        <i class="fas fa-paper-plane me-2"></i>Send OTP
                                    </button>
                                </form>

                                <form id="otp-verify-form" style="display: none;">
                                    @csrf
                                    <input type="hidden" name="email" id="verify-email">
                                    <div class="mb-3">
                                        <label class="form-label">Enter OTP</label>
                                        <input type="text" name="otp" class="form-control text-center" maxlength="6" pattern="[0-9]{6}" required>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check me-2"></i>Verify & Login
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('otp-send-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('otp-email').value;
            const formData = new FormData();
            formData.append('email', email);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("delegate.otp.send") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.errors?.email?.[0] || data.message || 'Failed to send OTP');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    document.getElementById('otp-send-form').style.display = 'none';
                    document.getElementById('otp-verify-form').style.display = 'block';
                    document.getElementById('verify-email').value = email;
                    alert('OTP sent to your email! Please check your inbox.');
                } else {
                    const errorMsg = data.errors?.email?.[0] || data.message || 'Failed to send OTP. Please try again.';
                    alert(errorMsg);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Failed to send OTP. Please try again.');
            });
        });

        document.getElementById('otp-verify-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("delegate.otp.verify") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.json();
                }
            })
            .then(data => {
                if (data && data.errors) {
                    alert(Object.values(data.errors)[0]);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>
