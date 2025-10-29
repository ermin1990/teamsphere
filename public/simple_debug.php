<?php
// Simple debug script without Laravel
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Simple Debug Test</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";
echo "<p>Script Path: " . __FILE__ . "</p>";

// Check if files exist
$files = [
    '../vendor/autoload.php',
    '../bootstrap/app.php',
    '../config/database.php',
    '../app/Policies/OrganizationPolicy.php'
];

echo "<h2>File Check:</h2>";
foreach ($files as $file) {
    $fullPath = __DIR__ . '/' . $file;
    $exists = file_exists($fullPath);
    echo "<p>$file: " . ($exists ? '<span style="color:green">EXISTS</span>' : '<span style="color:red">MISSING</span>') . "</p>";
}

// Try to include autoloader
echo "<h2>Loading Autoloader:</h2>";
try {
    require __DIR__.'/../vendor/autoload.php';
    echo "<p style='color:green'>Autoloader loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>Autoloader error: " . $e->getMessage() . "</p>";
}

// Check database connection
echo "<h2>Database Test:</h2>";
try {
    // Try production database first
    $pdo = new PDO('mysql:host=localhost;dbname=infinit4_testteamsphere', 'infinit4_teamspherelaravel', 'Norrmejeriererko1990');
    echo "<p style='color:green'>Production database connection successful</p>";
    $pdo = null;
} catch (Exception $e) {
    echo "<p style='color:red'>Production database error: " . $e->getMessage() . "</p>";
    try {
        // Try local database as fallback
        $pdo = new PDO('mysql:host=127.0.0.1;dbname=teamsphere', 'root', '');
        echo "<p style='color:orange'>Local database connection successful (fallback)</p>";
        $pdo = null;
    } catch (Exception $e2) {
        echo "<p style='color:red'>Local database error: " . $e2->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='debug_organization_access.php'>Go to full debug script</a></p>";
