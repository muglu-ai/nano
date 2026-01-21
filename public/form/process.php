<?php
/**
 * Form Processing Script
 */
session_start();
require_once 'config.php';

// Get user IP address
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

// Validation errors
$errors = [];

// Validate and sanitize input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form.php');
    exit;
}

// Validate domains
if (!isset($_POST['domains']) || empty($_POST['domains'])) {
    $errors[] = 'Please select at least one domain';
} else {
    $allowedDomains = ['small satellite', 'fly-by-wireless', 'reliability', 'devices', 'materials', 'energy'];
    $domains = array_filter($_POST['domains'], function($domain) use ($allowedDomains) {
        return in_array($domain, $allowedDomains);
    });
    if (empty($domains)) {
        $errors[] = 'Invalid domain selection';
    }
}

// Validate mandatory fields
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');
$country = trim($_POST['country'] ?? '');

if (empty($name)) {
    $errors[] = 'Name is required';
} elseif (strlen($name) > 255) {
    $errors[] = 'Name must be less than 255 characters';
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
} elseif (strlen($email) > 255) {
    $errors[] = 'Email must be less than 255 characters';
}

if (empty($mobile)) {
    $errors[] = 'Mobile is required';
} elseif (!preg_match('/^[0-9]{10,15}$/', $mobile)) {
    $errors[] = 'Mobile must be 10-15 digits';
}

if (empty($country)) {
    $errors[] = 'Country is required';
}

// Validate optional fields
$org = trim($_POST['org'] ?? '');
$designation = trim($_POST['designation'] ?? '');

if (strlen($org) > 255) {
    $errors[] = 'Organization must be less than 255 characters';
}

if (strlen($designation) > 255) {
    $errors[] = 'Designation must be less than 255 characters';
}

// Validate CAPTCHA
$captcha = trim($_POST['captcha'] ?? '');
if (empty($captcha)) {
    $errors[] = 'CAPTCHA is required';
} elseif (!isset($_SESSION['captcha_code']) || strtoupper($captcha) !== strtoupper($_SESSION['captcha_code'])) {
    $errors[] = 'Invalid CAPTCHA. Please try again';
    // Regenerate CAPTCHA for next attempt
    unset($_SESSION['captcha_code']);
}

// Get user IP
$userIP = getUserIP();

// If there are errors, redirect back to form with error message
if (!empty($errors)) {
    $_SESSION['form_errors'] = $errors;
    $_SESSION['form_data'] = $_POST;
    header('Location: form.php?error=' . urlencode(implode(', ', $errors)));
    exit;
}

// Clear CAPTCHA session after successful validation
unset($_SESSION['captcha_code']);

// Save to database
try {
    $pdo = getDBConnection();
    
    // Prepare domains as comma-separated string
    $domainsString = implode(', ', $domains);
    
    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO domain_registrations 
        (name, email, mobile, org, designation, country, domains, user_ip) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $name,
        $email,
        $mobile,
        !empty($org) ? $org : null,
        !empty($designation) ? $designation : null,
        $country,
        $domainsString,
        $userIP
    ]);
    
    // Get the inserted ID
    $registrationId = $pdo->lastInsertId();
    
    // Store success data in session
    $_SESSION['registration_id'] = $registrationId;
    $_SESSION['registration_data'] = [
        'name' => $name,
        'email' => $email,
        'mobile' => $mobile,
        'org' => $org,
        'designation' => $designation,
        'country' => $country,
        'domains' => $domainsString,
        'user_ip' => $userIP
    ];
    
    // Redirect to thank you page
    header('Location: thankyou.php');
    exit;
    
} catch (PDOException $e) {
    // Log error (in production, use proper logging)
    error_log("Database error: " . $e->getMessage());
    
    $_SESSION['form_errors'] = ['An error occurred while processing your registration. Please try again.'];
    $_SESSION['form_data'] = $_POST;
    header('Location: form.php?error=' . urlencode('Database error. Please try again.'));
    exit;
}
