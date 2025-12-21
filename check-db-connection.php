<?php
/**
 * Database Connection Check Script
 * 
 * Supports both .env file and MySQL connection string
 * Includes TiDB Cloud SSL support
 * 
 * Usage:
 *   php check-db-connection.php
 *   php check-db-connection.php "mysql://user:pass@host:port/database"
 * 
 * For TiDB Cloud:
 *   - Set DB_URL or DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env
 *   - Set MYSQL_ATTR_SSL_CA to TiDB Cloud CA certificate URL or path
 *   - Ensure your IP is whitelisted in TiDB Cloud console
 */

// Parse MySQL connection string
function parseConnectionString($connectionString)
{
    // Remove mysql:// prefix if present
    $connectionString = preg_replace('/^mysql:\/\//', '', $connectionString);
    
    // Pattern: user:password@host:port/database
    if (preg_match('/^([^:]+):([^@]+)@([^:]+):(\d+)\/(.+)$/', $connectionString, $matches)) {
        return [
            'username' => $matches[1],
            'password' => $matches[2],
            'host' => $matches[3],
            'port' => $matches[4],
            'database' => $matches[5],
        ];
    }
    
    // Pattern: user:password@host/database (default port 3306)
    if (preg_match('/^([^:]+):([^@]+)@([^\/]+)\/(.+)$/', $connectionString, $matches)) {
        return [
            'username' => $matches[1],
            'password' => $matches[2],
            'host' => $matches[3],
            'port' => '3306',
            'database' => $matches[4],
        ];
    }
    
    // Pattern: user@host:port/database (no password)
    if (preg_match('/^([^@]+)@([^:]+):(\d+)\/(.+)$/', $connectionString, $matches)) {
        return [
            'username' => $matches[1],
            'password' => '',
            'host' => $matches[2],
            'port' => $matches[3],
            'database' => $matches[4],
        ];
    }
    
    // Pattern: user@host/database (no password, default port)
    if (preg_match('/^([^@]+)@([^\/]+)\/(.+)$/', $connectionString, $matches)) {
        return [
            'username' => $matches[1],
            'password' => '',
            'host' => $matches[2],
            'port' => '3306',
            'database' => $matches[3],
        ];
    }
    
    return null;
}

// Load environment variables from .env file
function loadEnv($path)
{
    if (!file_exists($path)) {
        return [];
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

// Get configuration
$config = null;
$connectionString = null;
$sslCa = null;

// Try to load from .env file first
$envPath = __DIR__ . '/.env';
if (!file_exists($envPath)) {
    $envPath = __DIR__ . '/../.env';
}

if (file_exists($envPath)) {
    $env = loadEnv($envPath);
    
    // Check for DB_URL connection string
    if (!empty($env['DB_URL'])) {
        $connectionString = $env['DB_URL'];
        $config = parseConnectionString($connectionString);
        
        if (!$config) {
            die("❌ Error: Invalid DB_URL format in .env file.\n" .
                "Expected format: mysql://username:password@host:port/database\n" .
                "   or: mysql://username:password@host/database\n" .
                "   or: mysql://username@host:port/database\n" .
                "   or: mysql://username@host/database\n");
        }
        
        echo "📝 Using DB_URL from .env file\n\n";
    } else {
        // Fallback to individual DB_* variables
        $config = [
            'host' => $env['DB_HOST'] ?? '127.0.0.1',
            'port' => $env['DB_PORT'] ?? '3306',
            'database' => $env['DB_DATABASE'] ?? '',
            'username' => $env['DB_USERNAME'] ?? 'root',
            'password' => $env['DB_PASSWORD'] ?? '',
        ];
        
        echo "📝 Using DB_* variables from .env file\n\n";
    }
    
    // Get SSL CA certificate URL if provided
    if (!empty($env['MYSQL_ATTR_SSL_CA'])) {
        $sslCa = $env['MYSQL_ATTR_SSL_CA'];
        echo "🔒 SSL CA certificate URL found in .env\n\n";
    }
} else {
    // Check if connection string is provided as command line argument
    if (isset($argv[1]) && !empty($argv[1])) {
        $connectionString = $argv[1];
        $config = parseConnectionString($connectionString);
        
        if (!$config) {
            die("❌ Error: Invalid connection string format.\n" .
                "Expected format: mysql://username:password@host:port/database\n" .
                "   or: mysql://username:password@host/database\n" .
                "   or: mysql://username@host:port/database\n" .
                "   or: mysql://username@host/database\n");
        }
        
        echo "📝 Using connection string provided as argument\n\n";
    } else {
        die("❌ Error: No connection string provided and .env file not found.\n\n" .
            "Usage:\n" .
            "  php check-db-connection.php\n" .
            "  php check-db-connection.php \"mysql://user:pass@host:port/database\"\n\n");
    }
}

// Extract database configuration
$dbHost = $config['host'];
$dbPort = $config['port'];
$dbDatabase = $config['database'];
$dbUsername = $config['username'];
$dbPassword = $config['password'];

// Display configuration (mask password)
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "🔍 Database Connection Check\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo "📋 Configuration:\n";
echo "   Host:     {$dbHost}\n";
echo "   Port:     {$dbPort}\n";
echo "   Database: {$dbDatabase}\n";
echo "   Username: {$dbUsername}\n";
echo "   Password: " . (empty($dbPassword) ? '(empty)' : str_repeat('*', min(strlen($dbPassword), 10))) . "\n";

if ($connectionString) {
    echo "   Connection String: mysql://{$dbUsername}:" . str_repeat('*', min(strlen($dbPassword), 10)) . "@{$dbHost}:{$dbPort}/{$dbDatabase}\n";
}
echo "\n";

// Validate required fields
if (empty($dbDatabase)) {
    die("❌ Error: Database name is required\n");
}

if (empty($dbUsername)) {
    die("❌ Error: Username is required\n");
}

// Attempt database connection
echo "🔌 Attempting to connect to database...\n\n";

try {
    // Detect TiDB Cloud connection
    $isTiDBCloud = (strpos($dbHost, 'tidbcloud.com') !== false || strpos($dbHost, 'tidb') !== false);
    
    // Create PDO connection
    // TiDB Cloud requires SSL - add ssl-mode to DSN
    if ($isTiDBCloud) {
        // For TiDB Cloud, we need to ensure SSL is used
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase};charset=utf8mb4";
    } else {
        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbDatabase};charset=utf8mb4";
    }
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 30, // Increased timeout for TiDB Cloud
    ];
    
    // Add SSL configuration if SSL CA is provided or if it's TiDB Cloud
    
    if (!empty($sslCa) || $isTiDBCloud) {
        echo "🔒 Configuring SSL connection...\n";
        
        if (!empty($sslCa)) {
            // Download SSL certificate if it's a URL
            $sslCaPath = $sslCa;
            if (filter_var($sslCa, FILTER_VALIDATE_URL)) {
                echo "   Downloading SSL certificate from: {$sslCa}\n";
                $sslCaPath = sys_get_temp_dir() . '/mysql_ssl_ca_' . md5($sslCa) . '.pem';
                
                $sslContent = @file_get_contents($sslCa);
                if ($sslContent === false) {
                    throw new Exception("Failed to download SSL certificate from: {$sslCa}");
                }
                
                file_put_contents($sslCaPath, $sslContent);
                echo "   SSL certificate saved to: {$sslCaPath}\n";
            }
            
            // Verify SSL certificate file exists
            if (!file_exists($sslCaPath)) {
                throw new Exception("SSL certificate file not found: {$sslCaPath}");
            }
            
            $options[PDO::MYSQL_ATTR_SSL_CA] = $sslCaPath;
        } else if ($isTiDBCloud) {
            // For TiDB Cloud, try to use system CA bundle or disable verification
            echo "   Detected TiDB Cloud connection - configuring SSL...\n";
            
            // Try to find system CA bundle
            $caPaths = [
                '/etc/ssl/certs/ca-certificates.crt',  // Debian/Ubuntu
                '/etc/pki/tls/certs/ca-bundle.crt',     // CentOS/RHEL
                '/usr/local/etc/openssl/cert.pem',      // macOS Homebrew
                '/opt/homebrew/etc/openssl@3/cert.pem', // macOS Homebrew (Apple Silicon)
                '/System/Library/OpenSSL/certs/cert.pem', // macOS System
            ];
            
            $foundCa = false;
            foreach ($caPaths as $caPath) {
                if (file_exists($caPath)) {
                    $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
                    echo "   Using system CA bundle: {$caPath}\n";
                    $foundCa = true;
                    break;
                }
            }
            
            if (!$foundCa) {
                echo "   ⚠️  System CA bundle not found, using SSL without CA verification\n";
                echo "   (For production, download TiDB Cloud CA certificate)\n";
            }
        }
        
        // TiDB Cloud SSL options
        if ($isTiDBCloud) {
            // For TiDB Cloud, we need SSL but can skip verification if no CA cert
            if (empty($sslCa) || !isset($options[PDO::MYSQL_ATTR_SSL_CA])) {
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
                echo "   ⚠️  SSL verification disabled (no CA cert provided)\n";
                echo "   💡 For production, download TiDB Cloud CA certificate\n";
            } else {
                $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
            }
            $options[PDO::MYSQL_ATTR_SSL_CIPHER] = 'DEFAULT';
        } else {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = true;
        }
        
        echo "   ✅ SSL configuration added\n\n";
    } elseif ($isTiDBCloud) {
        // TiDB Cloud detected but no SSL config - add minimal SSL
        echo "🔒 TiDB Cloud detected - configuring SSL (minimal)...\n";
        
        // TiDB Cloud requires SSL - enable it even without CA cert
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        $options[PDO::MYSQL_ATTR_SSL_CIPHER] = 'DEFAULT';
        
        // Try to use system CA bundle if available
        $caPaths = [
            '/etc/ssl/certs/ca-certificates.crt',  // Debian/Ubuntu
            '/etc/pki/tls/certs/ca-bundle.crt',     // CentOS/RHEL
            '/usr/local/etc/openssl/cert.pem',      // macOS Homebrew
            '/opt/homebrew/etc/openssl@3/cert.pem', // macOS Homebrew (Apple Silicon)
            '/System/Library/OpenSSL/certs/cert.pem', // macOS System
        ];
        
        foreach ($caPaths as $caPath) {
            if (file_exists($caPath)) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $caPath;
                echo "   Using system CA bundle: {$caPath}\n";
                break;
            }
        }
        
        echo "   ⚠️  Using SSL without strict verification (recommended: set MYSQL_ATTR_SSL_CA in .env)\n";
        echo "   💡 Download TiDB Cloud CA: https://docs.pingcap.com/tidbcloud/secure-connections-to-serverless-tier\n\n";
    }

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

    // Collation
    $stmt = $pdo->query("SELECT @@collation_database as collation");
    $collation = $stmt->fetch();
    echo "   Collation: {$collation['collation']}\n";

    // Table count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM information_schema.tables WHERE table_schema = '{$dbDatabase}'");
    $tableCount = $stmt->fetch();
    echo "   Total Tables: {$tableCount['count']}\n\n";

    // Test a simple query
    echo "🧪 Testing query execution...\n";
    $stmt = $pdo->query("SELECT 1 as test, NOW() as current_time");
    $result = $stmt->fetch();
    
    if ($result && $result['test'] == 1) {
        echo "   ✅ Query execution successful\n";
        echo "   Current Server Time: {$result['current_time']}\n\n";
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
        'startup_zone_drafts',
        'gst_lookups',
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

    // Get table sizes for existing tables
    if (!empty($existingTables)) {
        echo "\n📈 Table Statistics:\n";
        foreach ($existingTables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM `{$table}`");
                $count = $stmt->fetch();
                echo "   {$table}: {$count['count']} rows\n";
            } catch (PDOException $e) {
                echo "   {$table}: Unable to count rows\n";
            }
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
    
    // Additional diagnostics for specific error codes
    if ($e->getCode() == 2002) {
        echo "🔍 Error 2002 Analysis:\n";
        echo "   This usually means the server refused the connection.\n";
        echo "   Possible causes:\n";
        echo "   - Firewall blocking port {$dbPort}\n";
        echo "   - IP not whitelisted (even if you think it is, double-check)\n";
        echo "   - Server is down or unreachable\n";
        echo "   - Wrong host/port combination\n";
        echo "   - Network connectivity issues\n\n";
        
        // Test network connectivity
        echo "🧪 Testing network connectivity...\n";
        $testHost = $dbHost;
        $testPort = $dbPort;
        
        // Try to connect with fsockopen
        $connection = @fsockopen($testHost, $testPort, $errno, $errstr, 5);
        if ($connection) {
            echo "   ✅ Port {$testPort} is reachable on {$testHost}\n";
            fclose($connection);
        } else {
            echo "   ❌ Cannot reach {$testHost}:{$testPort}\n";
            echo "   Error: {$errstr} (Code: {$errno})\n";
            echo "\n";
            echo "   🔍 Additional Diagnostics:\n";
            
            // Get current IP
            $currentIp = @file_get_contents('https://ifconfig.me');
            if (!$currentIp) {
                $currentIp = @file_get_contents('https://ipinfo.io/ip');
            }
            if ($currentIp) {
                echo "   Your current public IP: " . trim($currentIp) . "\n";
                echo "   ⚠️  Verify this IP is whitelisted in TiDB Cloud console\n";
            }
            
            echo "   - Check TiDB Cloud console → Security → IP Access List\n";
            echo "   - Ensure cluster is running (not paused)\n";
            echo "   - Try disabling VPN/Proxy if active\n";
            echo "   - Wait a few minutes after adding IP to whitelist (propagation delay)\n";
        }
        echo "\n";
    }
    
    $isTiDBCloud = (strpos($dbHost, 'tidbcloud.com') !== false || strpos($dbHost, 'tidb') !== false);
    
    echo "💡 Troubleshooting tips:\n";
    echo "   1. Verify connection details are correct\n";
    echo "   2. Ensure MySQL/TiDB server is running\n";
    echo "   3. Check if the database exists: CREATE DATABASE IF NOT EXISTS `{$dbDatabase}`;\n";
    echo "   4. Verify user has proper permissions\n";
    echo "   5. Check firewall settings if connecting to remote host\n";
    echo "   6. Verify network connectivity to {$dbHost}:{$dbPort}\n";
    
    if ($isTiDBCloud) {
        echo "   7. ⚠️  TiDB Cloud requires SSL connection - ensure MYSQL_ATTR_SSL_CA is set in .env\n";
        echo "   8. Verify IP whitelist in TiDB Cloud console (you mentioned it's whitelisted)\n";
        echo "   9. Check if port {$dbPort} is accessible: telnet {$dbHost} {$dbPort}\n";
        echo "  10. Test connection with mysql client:\n";
        echo "      mysql -h {$dbHost} -P {$dbPort} -u {$dbUsername} -p --ssl-mode=REQUIRED\n";
        echo "  11. Verify your current IP matches whitelist: curl ifconfig.me\n";
        echo "  12. Check TiDB Cloud cluster status in console (ensure it's running)\n";
        echo "  13. Try increasing timeout if connection is slow\n";
    } else {
        echo "   7. Test connection: mysql -h {$dbHost} -P {$dbPort} -u {$dbUsername} -p{$dbDatabase}\n";
    }
    
    if (!empty($sslCa)) {
        echo "   " . ($isTiDBCloud ? "11" : "8") . ". Verify SSL certificate URL is accessible: {$sslCa}\n";
        echo "   " . ($isTiDBCloud ? "12" : "9") . ". Check SSL certificate file permissions\n";
    }
    echo "\n";
    
    exit(1);
} catch (Exception $e) {
    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "❌ Error occurred!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    
    echo "Error Code: {$e->getCode()}\n";
    echo "Error Message: {$e->getMessage()}\n\n";
    
    if (strpos($e->getMessage(), 'SSL') !== false || strpos($e->getMessage(), 'certificate') !== false) {
        echo "💡 SSL Certificate Error:\n";
        echo "   1. Verify MYSQL_ATTR_SSL_CA URL is accessible\n";
        echo "   2. Check if the SSL certificate URL is valid\n";
        echo "   3. Ensure the certificate file can be downloaded\n";
        if (!empty($sslCa)) {
            echo "   4. SSL CA URL: {$sslCa}\n";
        }
        echo "\n";
    }
    
    exit(1);
}
