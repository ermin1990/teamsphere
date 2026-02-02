<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// EMERGENCY DEBUG - Log all requests to organizations
$logFile = __DIR__ . '/emergency_debug.log';
$timestamp = date('Y-m-d H:i:s');
if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'organizations') !== false) {
    $logData = [
        'timestamp' => $timestamp,
        'request_uri' => $_SERVER['REQUEST_URI'],
        'request_method' => $_SERVER['REQUEST_METHOD'],
        'script_name' => $_SERVER['SCRIPT_NAME'],
    ];
    file_put_contents($logFile, "=== ORGANIZATIONS REQUEST ===\n" . print_r($logData, true) . "\n", FILE_APPEND);
}

// Add error handler to catch authorization errors
set_exception_handler(function($e) use ($logFile, $timestamp) {
    $errorData = [
        'timestamp' => $timestamp,
        'exception' => get_class($e),
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString(),
    ];
    file_put_contents($logFile, "=== EXCEPTION CAUGHT ===\n" . print_r($errorData, true) . "\n", FILE_APPEND);
    
    // Let Laravel handle it normally
    throw $e;
});

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
