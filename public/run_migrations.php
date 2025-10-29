<?php
/**
 * Web-based Laravel Migration Runner for Shared Hosting
 *
 * Access this file via browser: https://yourdomain.com/run_migrations.php
 * This runs: php artisan migrate
 * 
 * ⚠️ DELETE THIS FILE AFTER USE FOR SECURITY!
 */

// Security check - basic authentication
$ADMIN_PASSWORD = 'admin123'; // CHANGE THIS!

if (!isset($_GET['password']) || $_GET['password'] !== $ADMIN_PASSWORD) {
    die('❌ Unauthorized. Access denied.');
}

// Increase limits
set_time_limit(300);
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

// Set proper headers
header('Content-Type: text/html; charset=utf-8');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Laravel Migrations</title>";
echo "<style>body{font-family:monospace;background:#1e1e1e;color:#d4d4d4;padding:20px;}pre{background:#252526;padding:15px;border-radius:5px;overflow-x:auto;}.success{color:#4ec9b0;}.error{color:#f48771;}.warning{color:#dcdcaa;}.info{color:#569cd6;}</style>";
echo "</head><body>";
echo "<h1>🚀 Laravel Migration Runner</h1>";
echo "<pre>";

// Navigate to project root
$publicDir = __DIR__;
$projectRoot = dirname($publicDir);

if (!is_dir($projectRoot)) {
    die("<span class='error'>❌ ERROR: Project root not found: $projectRoot</span></pre></body></html>");
}

echo "<span class='info'>📁 Project root: $projectRoot</span>\n";
echo "<span class='info'>🔧 Current directory: " . getcwd() . "</span>\n\n";

// Change to project root
chdir($projectRoot);
echo "<span class='success'>✅ Changed to project root</span>\n\n";

// Check if artisan exists
if (!file_exists('artisan')) {
    die("<span class='error'>❌ ERROR: artisan file not found in project root!</span></pre></body></html>");
}

echo "<span class='success'>✅ Found artisan file</span>\n\n";

// Fix migrations table AUTO_INCREMENT FIRST
echo "<span class='warning'>🔧 Pre-flight check: Fixing migrations table...</span>\n";

try {
    // Load environment
    $envFile = $projectRoot . '/.env';
    if (!file_exists($envFile)) {
        die("<span class='error'>❌ ERROR: .env file not found</span></pre></body></html>");
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

    $pdo = new PDO(
        "mysql:host={$env['DB_HOST']}:{$env['DB_PORT']};dbname={$env['DB_DATABASE']};charset=utf8mb4",
        $env['DB_USERNAME'],
        $env['DB_PASSWORD'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Check and fix migrations table
    $result = $pdo->query("SHOW COLUMNS FROM migrations WHERE Field = 'id'");
    $column = $result->fetch(PDO::FETCH_ASSOC);
    
    if ($column && strpos($column['Extra'], 'auto_increment') === false) {
        echo "<span class='warning'>   ⚠️  Fixing migrations table AUTO_INCREMENT...</span>\n";
        $pdo->exec("ALTER TABLE migrations MODIFY id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY");
        echo "<span class='success'>   ✅ Fixed migrations table!</span>\n\n";
    } else {
        echo "<span class='success'>   ✅ Migrations table OK</span>\n\n";
    }

} catch (PDOException $e) {
    echo "<span class='error'>   ⚠️  Could not fix migrations table: " . $e->getMessage() . "</span>\n\n";
    echo "<span class='warning'>   Continuing anyway...</span>\n\n";
}

// Find PHP executable
$phpPath = 'php'; // Default

// Common PHP paths on shared hosting
$possiblePaths = [
    '/usr/local/bin/php',
    '/usr/bin/php',
    '/opt/php/bin/php',
    '/usr/local/php/bin/php',
    'php', // System PATH
];

foreach ($possiblePaths as $path) {
    $testOutput = [];
    $testReturn = 0;
    @exec("$path -v 2>&1", $testOutput, $testReturn);
    
    if ($testReturn === 0) {
        $phpPath = $path;
        echo "<span class='success'>✅ Found PHP at: $phpPath</span>\n";
        echo "<span class='info'>   Version: " . implode(' ', $testOutput) . "</span>\n\n";
        break;
    }
}

// Run php artisan migrate
echo "<span class='warning'>🔄 Running migrations...</span>\n";
echo str_repeat('-', 80) . "\n\n";

$command = "$phpPath artisan migrate --force 2>&1";
echo "<span class='info'>Command: $command</span>\n\n";

// Execute and capture output
$output = [];
$returnCode = 0;
exec($command, $output, $returnCode);

// Display output
foreach ($output as $line) {
    // Colorize output
    if (strpos($line, 'Migrating:') !== false) {
        echo "<span class='warning'>$line</span>\n";
    } elseif (strpos($line, 'Migrated:') !== false || strpos($line, 'DONE') !== false) {
        echo "<span class='success'>$line</span>\n";
    } elseif (strpos($line, 'ERROR') !== false || strpos($line, 'SQLSTATE') !== false || strpos($line, 'Exception') !== false) {
        echo "<span class='error'>$line</span>\n";
    } elseif (strpos($line, 'Nothing to migrate') !== false) {
        echo "<span class='info'>$line</span>\n";
    } else {
        echo "$line\n";
    }
}

echo "\n" . str_repeat('-', 80) . "\n";

// Check return code
if ($returnCode === 0) {
    echo "\n<span class='success'>✅ Migration completed successfully!</span>\n";
} else {
    echo "\n<span class='error'>❌ Migration failed with exit code: $returnCode</span>\n";
}

// Security reminder
echo "\n" . str_repeat('=', 80) . "\n";
echo "<span class='error'>⚠️  SECURITY WARNING: Delete this file immediately!</span>\n";
echo "<span class='error'>   rm run_migrations.php</span>\n";
echo str_repeat('=', 80) . "\n";

echo "</pre>";

// Show quick stats
try {
    // Try to show migrations table
    require_once 'vendor/autoload.php';
    
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $db = $app->make('db');
    
    echo "<h2>📊 Migration History</h2>";
    echo "<pre>";
    
    $migrations = $db->table('migrations')->orderBy('batch', 'desc')->get();
    
    if ($migrations->count() > 0) {
        echo sprintf("%-60s | %-6s\n", "Migration", "Batch");
        echo str_repeat('-', 80) . "\n";
        
        foreach ($migrations as $migration) {
            echo sprintf("%-60s | %-6s\n", $migration->migration, $migration->batch);
        }
    } else {
        echo "No migrations found in database.\n";
    }
    
    echo "</pre>";
} catch (Exception $e) {
    echo "<pre><span class='error'>⚠️  Could not load migration history: " . $e->getMessage() . "</span></pre>";
}

echo "</body></html>";
?>
