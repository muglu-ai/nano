<?php
/**
 * Database Configuration
 * Loads database credentials from .env file
 */

function loadEnv($path) {
    if (!file_exists($path)) {
        die("Error: .env file not found");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            $value = trim($value, '"\'');
            $env[$key] = $value;
        }
    }

    return $env;
}

// Get .env file path
$envPath = __DIR__ . '/../../.env';
if (!file_exists($envPath)) {
    $envPath = __DIR__ . '/../../../.env';
}

$env = loadEnv($envPath);

// Database configuration
define('DB_HOST', $env['DB_HOST'] ?? '127.0.0.1');
define('DB_PORT', $env['DB_PORT'] ?? '3306');
define('DB_DATABASE', $env['DB_DATABASE'] ?? '');
define('DB_USERNAME', $env['DB_USERNAME'] ?? 'root');
define('DB_PASSWORD', $env['DB_PASSWORD'] ?? '');

// Database connection
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_DATABASE . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Create table if it doesn't exist
function createTableIfNotExists($pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS domain_registrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        org VARCHAR(255) DEFAULT NULL,
        designation VARCHAR(255) DEFAULT NULL,
        country VARCHAR(100) NOT NULL,
        domains TEXT NOT NULL,
        user_ip VARCHAR(45) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_email (email),
        INDEX idx_created_at (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
}

// Initialize database
try {
    $pdo = getDBConnection();
    createTableIfNotExists($pdo);
} catch (Exception $e) {
    // Table creation is optional, continue if it fails
}
