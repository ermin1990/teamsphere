<?php
/**
 * Emergency Fix for migrations table AUTO_INCREMENT
 * 
 * Run this BEFORE run_migrations.php
 * Access: https://yourdomain.com/fix_migrations_table.php?password=YOUR_PASSWORD
 * 
 * ⚠️ DELETE THIS FILE AFTER USE!
 */

// Security check
$ADMIN_PASSWORD = 'your-secure-password-here'; // CHANGE THIS!

if (!isset($_GET['password']) || $_GET['password'] !== $ADMIN_PASSWORD) {
    die('❌ Unauthorized. Access denied.');
}

header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Fix Migrations Table</title>";
echo "<style>body{font-family:monospace;background:#1e1e1e;color:#d4d4d4;padding:20px;}pre{background:#252526;padding:15px;border-radius:5px;}.success{color:#4ec9b0;}.error{color:#f48771;}.warning{color:#dcdcaa;}.info{color:#569cd6;}</style>";
echo "</head><body>";
echo "<h1>🔧 Fix migrations Table AUTO_INCREMENT</h1>";
echo "<pre>";

// Load environment
$projectRoot = dirname(__DIR__);
$envFile = $projectRoot . '/.env';

if (!file_exists($envFile)) {
    die("<span class='error'>❌ ERROR: .env file not found at: $envFile</span></pre></body></html>");
}

$envContent = file_get_contents($envFile);
$lines = explode("\n", $envContent);
$env = [];

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line) || str_starts_with($line, '#')) continue;
    
    if (strpos($line, '=') !== false) {
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            
            if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
                $value = substr($value, 1, -1);
            }
            
            $env[$key] = $value;
        }
    }
}

echo "<span class='success'>✅ Loaded environment configuration</span>\n\n";

// Database connection
$host = $env['DB_HOST'] ?? 'localhost';
$port = $env['DB_PORT'] ?? '3306';
$database = $env['DB_DATABASE'];
$username = $env['DB_USERNAME'];
$password = $env['DB_PASSWORD'];

echo "<span class='info'>📋 Database: $host:$port/$database</span>\n\n";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<span class='success'>✅ Connected to MySQL</span>\n\n";
    
    // Check migrations table structure
    echo "<span class='info'>🔍 Checking migrations table...</span>\n";
    
    $result = $pdo->query("SHOW COLUMNS FROM migrations WHERE Field = 'id'");
    $column = $result->fetch(PDO::FETCH_ASSOC);
    
    if ($column) {
        echo "<span class='info'>Current id column: {$column['Type']} {$column['Extra']}</span>\n";
        
        if (strpos($column['Extra'], 'auto_increment') === false) {
            echo "<span class='warning'>⚠️  AUTO_INCREMENT is missing!</span>\n\n";
            
            // Fix it
            echo "<span class='warning'>🔧 Applying fix...</span>\n";
            $pdo->exec("ALTER TABLE migrations MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY");
            echo "<span class='success'>✅ Fixed migrations table AUTO_INCREMENT!</span>\n\n";
            
            // Verify
            $result = $pdo->query("SHOW COLUMNS FROM migrations WHERE Field = 'id'");
            $column = $result->fetch(PDO::FETCH_ASSOC);
            echo "<span class='success'>✅ Verified: {$column['Type']} {$column['Extra']}</span>\n";
        } else {
            echo "<span class='success'>✅ AUTO_INCREMENT already set correctly!</span>\n";
        }
    } else {
        echo "<span class='error'>❌ migrations table 'id' column not found!</span>\n";
    }
    
    echo "\n" . str_repeat('=', 80) . "\n";
    echo "<span class='success'>✅ Fix completed! You can now run run_migrations.php</span>\n";
    echo str_repeat('=', 80) . "\n\n";
    
    echo "<span class='error'>⚠️  SECURITY: Delete this file after use!</span>\n";
    
} catch (PDOException $e) {
    echo "<span class='error'>❌ Database error: " . $e->getMessage() . "</span>\n";
}

echo "</pre></body></html>";
?>
