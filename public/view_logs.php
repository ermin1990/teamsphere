<?php
// Simple script to view recent logs
ini_set('display_errors', 1);
error_reporting(E_ALL);

$logFile = __DIR__ . '/../storage/logs/laravel.log';

echo "<h1>Recent Laravel Logs</h1>";
echo "<p>Log file: $logFile</p>";

if (!file_exists($logFile)) {
    echo "<p style='color:red'>Log file does not exist!</p>";
    echo "<p>Checking storage/logs directory...</p>";
    $logsDir = __DIR__ . '/../storage/logs';
    if (is_dir($logsDir)) {
        $files = scandir($logsDir);
        echo "<p>Files in storage/logs:</p><ul>";
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "<li>$file</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p style='color:red'>storage/logs directory does not exist!</p>";
    }
    exit;
}

$lines = file($logFile);
$recentLines = array_slice($lines, -200); // Last 200 lines

// Filter for AdminOrganizationController and OrganizationPolicy logs
$relevantLines = [];
foreach ($recentLines as $line) {
    if (stripos($line, 'AdminOrganizationController') !== false ||
        stripos($line, 'OrganizationPolicy') !== false ||
        stripos($line, 'authorization') !== false) {
        $relevantLines[] = $line;
    }
}

echo "<h2>Relevant Log Entries (Last 200 lines)</h2>";
if (count($relevantLines) > 0) {
    echo "<pre style='background: #f4f4f4; padding: 10px; overflow-x: auto; max-height: 600px;'>";
    echo htmlspecialchars(implode('', $relevantLines));
    echo "</pre>";
} else {
    echo "<p style='color:orange'>No relevant log entries found in last 200 lines</p>";
    echo "<h3>Last 50 log lines:</h3>";
    echo "<pre style='background: #f4f4f4; padding: 10px; overflow-x: auto; max-height: 400px;'>";
    echo htmlspecialchars(implode('', array_slice($lines, -50)));
    echo "</pre>";
}
?>
