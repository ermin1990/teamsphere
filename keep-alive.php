<?php
// Simple keep-alive script for TeamSphere
// This script can be called by cron to keep the server active

// Define the URL to ping (your homepage)
$url = 'https://teamsphere.infinitycreative.agency/';

// Use curl to ping the site
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10); // 10 second timeout
curl_setopt($ch, CURLOPT_USERAGENT, 'TeamSphere Keep-Alive Cron/1.0');

// Execute the request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

// Log the result (optional - you can remove this)
$logFile = __DIR__ . '/../storage/logs/keep-alive.log';
$logMessage = date('Y-m-d H:i:s') . " - Ping to $url - HTTP Code: $httpCode\n";

if (!file_exists(dirname($logFile))) {
    mkdir(dirname($logFile), 0755, true);
}

file_put_contents($logFile, $logMessage, FILE_APPEND);

// Exit with success
exit(0);