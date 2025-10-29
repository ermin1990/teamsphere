<?php
// View emergency debug logs
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Emergency Debug Logs</h1>";
echo "<p><a href='?clear=1'>Clear logs</a> | <a href='?'>Refresh</a></p>";

$logFiles = [
    'emergency_debug.log' => 'All Organizations Requests',
    'emergency_organizations.log' => 'Detailed Organizations Data',
    'debug_organization.log' => 'Controller/Policy Logs',
];

// Handle clear request
if (isset($_GET['clear'])) {
    foreach (array_keys($logFiles) as $file) {
        $path = __DIR__ . '/' . $file;
        if (file_exists($path)) {
            unlink($path);
        }
    }
    echo "<p style='color:green'>All logs cleared!</p>";
    echo "<p><a href='?'>Refresh</a></p>";
    exit;
}

foreach ($logFiles as $file => $description) {
    $path = __DIR__ . '/' . $file;
    
    echo "<h2>$description ($file)</h2>";
    
    if (!file_exists($path)) {
        echo "<p style='color:orange'>No log file yet.</p>";
        continue;
    }
    
    $logs = file_get_contents($path);
    $size = filesize($path);
    
    echo "<p>File size: " . number_format($size) . " bytes | Last modified: " . date('Y-m-d H:i:s', filemtime($path)) . "</p>";
    echo "<pre style='background: #f4f4f4; padding: 10px; overflow-x: auto; max-height: 400px; border: 1px solid #ccc;'>";
    echo htmlspecialchars($logs);
    echo "</pre>";
}

echo "<hr>";
echo "<p><a href='?clear=1'>Clear all logs</a> | <a href='?'>Refresh</a></p>";
?>
