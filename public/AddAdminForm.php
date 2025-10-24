<?php
require_once '../vendor/autoload.php';

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Mail\UserCredentialsMail;
use Illuminate\Support\Facades\Mail;

// Bootstrap Laravel
$app = require_once '../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        
        // Validate required fields
        if (empty($name) || empty($email)) {
            throw new Exception('Name and email are required.');
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Please enter a valid email address.');
        }
        
        // Check if user already exists
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            throw new Exception('A user with this email already exists.');
        }
        
        // Generate random password
        $password = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        
        // Create new admin user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'simplePass' => $password,
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        
        // Send credentials email
        $setupProfileUrl = config('app.url') . '/portal/public';
        Mail::to($email)->send(new UserCredentialsMail($name, $setupProfileUrl, $email, $password));
        
        $success = "Admin user created successfully! Credentials have been sent to {$email}";
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Admin User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            padding: 40px;
            margin-top: 50px;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-header h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: #666;
            font-size: 16px;
        }
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            font-weight: 500;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }
        
        .info-box {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .info-box h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .info-box ul {
            margin-bottom: 0;
            color: #6c757d;
        }
        
        .info-box li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="form-container">
                    <div class="form-header">
                        <h2><i class="fas fa-user-plus me-2"></i>Add Admin User</h2>
                        <p>Create a new admin user and send credentials via email</p>
                    </div>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="info-box">
                        <h6><i class="fas fa-info-circle me-2"></i>What happens when you create an admin user:</h6>
                        <ul>
                            <li>Password will be generated automatically</li>
                            <li>Role will be set to 'admin'</li>
                            <li>Email will be verified automatically</li>
                            <li>Credentials will be sent via email</li>
                            <li>User can login immediately</li>
                        </ul>
                    </div>
                    
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label for="name" class="form-label">
                                <i class="fas fa-user me-2"></i>Full Name
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                   required 
                                   placeholder="Enter full name">
                        </div>
                        
                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email Address
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   required 
                                   placeholder="Enter email address">
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Create Admin User
                            </button>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="../admin/users" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>