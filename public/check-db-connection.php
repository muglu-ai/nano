<?php
/**
 * Database Connection Check Script
 * 
 * This script verifies database connectivity by reading configuration from .env file
 * Usage: php check-db-connection.php
 */

// Load environment variables from .env file
function loadEnv($path)
{
    if (!file_exists($path)) {
        die("❌ Error: .env file not found at: {$path}\n");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];

    foreach ($lines as $line) {
        // Skip comments
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Parse KEY=VALUE format
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            $value = trim($value, '"\'');
            
            $env[$key] = $value;
        }
    }

    return $env;
}

// Get .env file path (check in current directory and parent directory)
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    $envPath = __DIR__ . '/../.env';
}

$env = loadEnv($envPath);

// Extract database configuration
$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbDatabase = $env['DB_DATABASE'] ?? '';
$dbUsername = $env['DB_USERNAME'] ?? 'root';
$dbPassword = $env['DB_PASSWORD'] ?? '';

// Display configuration (mask password)
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 Database Connection Check\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📋 Configuration from .env:\n";
echo "   Host:     {$dbHost}\n";
echo "   Port:     {$dbPort}\n";
echo "   Database: {$dbDatabase}\n";
echo "   Username: {$dbUsername}\n";
echo "   Password: " . (empty($dbPassword) ? '(empty)' : str_repeat('*', min(strlen($dbPassword), 10))) . "\n\n";

// Validate required fields
if (empty($dbDatabase)) {
    die("❌ Error: DB_DATABASE is not set in .env file\n");
}

if (empty($dbUsername)) {
    die("❌ Error: DB_USERNAME is not set in .env file\n");
}

// Attempt database connection
echo "🔌 Attempting to connect to database...\n\n";

try {
    // Create PDO connection
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ];

    $startTime = microtime(true);
    $pdo = new PDO($dsn, $dbUsername, $dbPassword, $options);
    $connectionTime = round((microtime(true) - $startTime) * 1000, 2);

    echo "✅ Connection successful!\n";
    echo "   Connection time: {$connectionTime}ms\n\n";

    // Get database information
    echo "📊 Database Information:\n";
    
    // MySQL version
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "   MySQL Version: {$version['version']}\n";

    // Database name
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $dbName = $stmt->fetch();
    echo "   Current Database: {$dbName['db_name']}\n";

    // Character set
    $stmt = $pdo->query("SELECT @@character_set_database as charset");
    $charset = $stmt->fetch();
    echo "   Character Set: {$charset['charset']}\n";

    // Table count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '{$dbDatabase}'");
    $tableCount = $stmt->fetch();
    echo "   Total Tables: {$tableCount['count']}\n\n";

    // Test a simple query
    echo "🧪 Testing query execution...\n";
    $stmt = $pdo->query("SELECT 1 as test");
    $result = $stmt->fetch();
    
    if ($result && $result['test'] == 1) {
        echo "   ✅ Query execution successful\n\n";
    } else {
        echo "   ⚠️  Query returned unexpected result\n\n";
    }

    // Check for key tables
    echo "🔍 Checking for key application tables...\n";
    $keyTables = [
        'users',
        'applications',
        'invoices',
        'payments',
        'payment_gateway_response',
        'events',
        'countries',
        'states',
    ];

    $existingTables = [];
    $missingTables = [];

    foreach ($keyTables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
        if ($stmt->rowCount() > 0) {
            $existingTables[] = $table;
            echo "   ✅ {$table}\n";
        } else {
            $missingTables[] = $table;
            echo "   ⚠️  {$table} (not found)\n";
        }
    }

    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✅ Database connection check completed successfully!\n";
    echo "   Found: " . count($existingTables) . " key tables\n";
    if (!empty($missingTables)) {
        echo "   Missing: " . count($missingTables) . " key tables\n";
    }
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

} catch (PDOException $e) {
    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ Connection failed!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "Error Code: {$e->getCode()}\n";
    echo "Error Message: {$e->getMessage()}\n\n";
    
    echo "💡 Troubleshooting tips:\n";
    echo "   1. Verify DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, and DB_PASSWORD in .env\n";
    echo "   2. Ensure MySQL/MariaDB server is running\n";
    echo "   3. Check if the database exists: CREATE DATABASE IF NOT EXISTS `{$dbDatabase}`;\n";
    echo "   4. Verify user has proper permissions\n";
    echo "   5. Check firewall settings if connecting to remote host\n";
    echo "   6. Verify network connectivity to {$dbHost}:{$dbPort}\n\n";
    
    exit(1);
}
