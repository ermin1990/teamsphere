<?php
/**
 * EMERGENCY CACHE CLEAR SCRIPT
 * 
 * Koristi ovaj fajl kada nemaš terminal pristup!
 * 
 * UPOTREBA:
 * 1. Upload u: teamsphere.infinitycreative.agency/clear-cache.php
 * 2. Otvori: https://teamsphere.infinitycreative.agency/clear-cache.php
 * 3. OBRIŠI OVAJ FAJL nakon korišćenja!
 */

// Adjust this path if needed (currently assumes structure from docs)
$appPath = __DIR__.'/../teamsphere_app';

echo "<!DOCTYPE html><html><head><title>Cache Clear</title>";
echo "<style>body{font-family:Arial;padding:20px;background:#f5f5f5;}";
echo ".box{background:white;padding:20px;border-radius:8px;max-width:600px;margin:0 auto;}";
echo ".success{color:green;} .error{color:red;} .warning{color:orange;}</style></head><body>";
echo "<div class='box'><h1>🔧 Laravel Cache Clear</h1><hr>";

// Check if vendor exists (validate path)
if (!file_exists($appPath.'/vendor/autoload.php')) {
    echo "<p class='error'>❌ ERROR: Cannot find Laravel application!</p>";
    echo "<p>Current path: <code>$appPath</code></p>";
    echo "<p>Please adjust the \$appPath variable in this script.</p>";
    echo "</div></body></html>";
    exit;
}

echo "<p>Application found at: <code>$appPath</code></p><hr>";

// Clear config cache
$configCache = $appPath.'/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    if (unlink($configCache)) {
        echo "<p class='success'>✅ Config cache cleared</p>";
    } else {
        echo "<p class='error'>❌ Failed to delete config cache</p>";
    }
} else {
    echo "<p class='warning'>⚠️ Config cache doesn't exist (already cleared)</p>";
}

// Clear routes cache
$routesCache = $appPath.'/bootstrap/cache/routes-v7.php';
if (file_exists($routesCache)) {
    if (unlink($routesCache)) {
        echo "<p class='success'>✅ Routes cache cleared</p>";
    } else {
        echo "<p class='error'>❌ Failed to delete routes cache</p>";
    }
} else {
    echo "<p class='warning'>⚠️ Routes cache doesn't exist (already cleared)</p>";
}

// Clear compiled views (optional)
$viewsPath = $appPath.'/storage/framework/views';
if (is_dir($viewsPath)) {
    $files = glob($viewsPath.'/*.php');
    $count = 0;
    foreach ($files as $file) {
        if (basename($file) !== '.gitignore' && is_file($file)) {
            if (unlink($file)) {
                $count++;
            }
        }
    }
    if ($count > 0) {
        echo "<p class='success'>✅ Cleared $count compiled views</p>";
    } else {
        echo "<p class='warning'>⚠️ No compiled views to clear</p>";
    }
}

echo "<hr><h3 class='success'>🎉 Cache clearing completed!</h3>";
echo "<p><strong>IMPORTANT:</strong> Now <span style='color:red;font-weight:bold;'>DELETE THIS FILE</span> for security!</p>";
echo "<p>File to delete: <code>clear-cache.php</code></p>";
echo "<hr><p><a href='/'>← Go to homepage</a></p>";
echo "</div></body></html>";
