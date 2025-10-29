<?php
// Ultra simple test - just create a log file
$logFile = __DIR__ . '/debug_organization.log';
$timestamp = date('Y-m-d H:i:s');

file_put_contents($logFile, "[$timestamp] TEST - This file was created by test_log.php\n", FILE_APPEND);

echo "<h1>Test Complete</h1>";
echo "<p>Log file should now exist at: $logFile</p>";
echo "<p><a href='view_debug_logs.php'>View debug logs</a></p>";

// Also test if we can write to public folder
if (is_writable(__DIR__)) {
    echo "<p style='color:green'>✓ Public folder is writable</p>";
} else {
    echo "<p style='color:red'>✗ Public folder is NOT writable</p>";
}

// Show current user
if (function_exists('posix_getpwuid')) {
    $processUser = posix_getpwuid(posix_geteuid());
    echo "<p>Process running as: " . $processUser['name'] . "</p>";
}
?>
