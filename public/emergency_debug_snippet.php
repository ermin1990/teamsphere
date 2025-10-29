<?php
// Emergency debug script - place this at the TOP of public/index.php
$logFile = __DIR__ . '/emergency_debug.log';
$timestamp = date('Y-m-d H:i:s');

// Log EVERYTHING
$logData = [
    'timestamp' => $timestamp,
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown',
    'script_name' => $_SERVER['SCRIPT_NAME'] ?? 'unknown',
    'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
    'query_string' => $_SERVER['QUERY_STRING'] ?? 'none',
    'is_organizations' => strpos($_SERVER['REQUEST_URI'] ?? '', 'organizations') !== false,
];

file_put_contents($logFile, print_r($logData, true) . "\n\n", FILE_APPEND);

// If this is an organizations request, log even more
if (strpos($_SERVER['REQUEST_URI'] ?? '', 'organizations') !== false) {
    $detailedLog = [
        'ALL_SERVER_VARS' => $_SERVER,
        'SESSION' => $_SESSION ?? [],
        'COOKIES' => $_COOKIE ?? [],
    ];
    
    file_put_contents(__DIR__ . '/emergency_organizations.log', print_r($detailedLog, true) . "\n\n", FILE_APPEND);
}
?>
