<?php
// Test Laravel bootstrap
echo "<h1>Laravel Bootstrap Test</h1>";
echo "<pre>";

try {
    require __DIR__.'/../vendor/autoload.php';
    echo "✓ Autoload successful\n\n";
    
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "✓ Bootstrap successful\n\n";
    
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    echo "✓ Kernel created\n\n";
    
    echo "Environment Variables:\n";
    echo "APP_ENV: " . (getenv('APP_ENV') ?: 'not set') . "\n";
    echo "APP_KEY: " . (getenv('APP_KEY') ? 'SET (length: ' . strlen(getenv('APP_KEY')) . ')' : 'NOT SET') . "\n";
    echo "DB_CONNECTION: " . (getenv('DB_CONNECTION') ?: 'not set') . "\n";
    echo "DB_HOST: " . (getenv('DB_HOST') ?: 'not set') . "\n";
    
    echo "\n✓ Laravel is working!\n";
    
} catch (Exception $e) {
    echo "✗ ERROR: " . $e->getMessage() . "\n\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString();
}

echo "</pre>";
