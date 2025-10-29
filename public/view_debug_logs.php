<?php
// View debug logs from public folder
ini_set('display_errors', 1);
error_reporting(E_ALL);

$logFile = __DIR__ . '/debug_organization.log';

echo "<h1>Organization Debug Logs</h1>";
echo "<p>Log file: $logFile</p>";
echo "<p><a href='?clear=1'>Clear logs</a> | <a href='?'>Refresh</a></p>";

// Handle clear request
if (isset($_GET['clear'])) {
    if (file_exists($logFile)) {
        unlink($logFile);
        echo "<p style='color:green'>Logs cleared!</p>";
        echo "<p><a href='?'>Refresh</a></p>";
        exit;
    }
}

if (!file_exists($logFile)) {
    echo "<p style='color:orange'>No log file yet. Try accessing an organization page first.</p>";
    exit;
}

$logs = file_get_contents($logFile);
$size = filesize($logFile);

echo "<p>File size: " . number_format($size) . " bytes</p>";
echo "<p>Last modified: " . date('Y-m-d H:i:s', filemtime($logFile)) . "</p>";

echo "<h2>Log Contents:</h2>";
echo "<pre style='background: #f4f4f4; padding: 10px; overflow-x: auto; max-height: 800px; border: 1px solid #ccc;'>";
echo htmlspecialchars($logs);
echo "</pre>";

echo "<p><a href='?clear=1'>Clear logs</a> | <a href='?'>Refresh</a></p>";
?>
