<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Debug Organization Access - Step by Step</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>Current Directory: " . __DIR__ . "</p>";

// Step 1: Load autoloader
echo "<h2>Step 1: Loading Autoloader</h2>";
try {
    require __DIR__.'/../vendor/autoload.php';
    echo "<p style='color:green'>✓ Autoloader loaded successfully</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Autoloader error: " . $e->getMessage() . "</p>";
    exit;
}

// Step 2: Load app
echo "<h2>Step 2: Loading Laravel App</h2>";
try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    echo "<p style='color:green'>✓ App loaded successfully</p>";
    echo "<p>App type: " . gettype($app) . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ App loading error: " . $e->getMessage() . "</p>";
    exit;
}

// Step 3: Bootstrap kernel
echo "<h2>Step 3: Bootstrapping Kernel</h2>";
try {
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<p style='color:green'>✓ Kernel created successfully</p>";
    echo "<p>Kernel type: " . gettype($kernel) . "</p>";
    
    $kernel->bootstrap();
    echo "<p style='color:green'>✓ Kernel bootstrapped successfully</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Kernel bootstrap error: " . $e->getMessage() . "</p>";
    echo "<p>Error file: " . $e->getFile() . " line " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    exit;
}

// Step 4: Test models
echo "<h2>Step 4: Testing Models</h2>";
try {
    $userCount = \App\Models\User::count();
    echo "<p style='color:green'>✓ Users model works - Count: $userCount</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Users model error: " . $e->getMessage() . "</p>";
}

try {
    $orgCount = \App\Models\Organization::count();
    echo "<p style='color:green'>✓ Organizations model works - Count: $orgCount</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Organizations model error: " . $e->getMessage() . "</p>";
}

// Step 5: Show parameters
echo "<h2>Step 5: Request Parameters</h2>";
$userId = $_GET['user_id'] ?? null;
$orgId = $_GET['org_id'] ?? null;
echo "<p>User ID: " . ($userId ?: 'not provided') . "</p>";
echo "<p>Organization ID: " . ($orgId ?: 'not provided') . "</p>";
echo "<p>Full GET array: " . print_r($_GET, true) . "</p>";
echo "<p>Full REQUEST array: " . print_r($_REQUEST, true) . "</p>";

if (!$userId || !$orgId) {
    echo "<h3>Available Users:</h3>";
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Email</th></tr>";
    foreach (\App\Models\User::all() as $user) {
        echo "<tr><td>{$user->id}</td><td>{$user->name}</td><td>{$user->email}</td></tr>";
    }
    echo "</table>";
    
    echo "<h3>Available Organizations:</h3>";
    echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Owner ID</th></tr>";
    foreach (\App\Models\Organization::all() as $org) {
        echo "<tr><td>{$org->id}</td><td>{$org->name}</td><td>{$org->user_id}</td></tr>";
    }
    echo "</table>";
    exit;
}

// Continue with the rest of the logic...
echo "<h2>Step 6: Testing Access Logic</h2>";
$user = \App\Models\User::find($userId);
$organization = \App\Models\Organization::find($orgId);

if (!$user) {
    echo "<p style='color:red'>User not found</p>";
    exit;
}

if (!$organization) {
    echo "<p style='color:red'>Organization not found</p>";
    exit;
}

echo "<p style='color:green'>✓ Both user and organization found</p>";

// Test policy
echo "<h3>Testing Policy:</h3>";
try {
    // Try to login user
    echo "<p>Trying to login user...</p>";
    \Auth::login($user);
    echo "<p>✓ User logged in</p>";
    
    // Test policy
    echo "<p>Testing policy method...</p>";
    $canView = $user->can('view', $organization);
    echo "<p>Policy result: " . ($canView ? 'YES' : 'NO') . "</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>✗ Policy error: " . $e->getMessage() . "</p>";
    echo "<p>Error file: " . $e->getFile() . " line " . $e->getLine() . "</p>";
    
    // Try direct policy call
    echo "<p>Trying direct policy call...</p>";
    try {
        $policy = new \App\Policies\OrganizationPolicy();
        $canViewDirect = $policy->view($user, $organization);
        echo "<p>Direct policy result: " . ($canViewDirect ? 'YES' : 'NO') . "</p>";
    } catch (Exception $e2) {
        echo "<p style='color:red'>✗ Direct policy error: " . $e2->getMessage() . "</p>";
        echo "<p>Error file: " . $e2->getFile() . " line " . $e2->getLine() . "</p>";
        
        // Debug the relationship
        echo "<p>Debugging organization users relationship...</p>";
        try {
            $orgUsers = $organization->organizationUsers;
            echo "<p>Organization users count: " . $orgUsers->count() . "</p>";
            foreach ($orgUsers as $orgUser) {
                echo "<p>User ID in org: {$orgUser->user_id}, Role: {$orgUser->role}</p>";
            }
        } catch (Exception $e3) {
            echo "<p style='color:red'>✗ Relationship error: " . $e3->getMessage() . "</p>";
        }
    }
}

// Show recent logs
echo "<h3>Recent Policy Logs:</h3>";
$logFile = storage_path('logs/laravel.log');
echo "<p>Log file path: $logFile</p>";
echo "<p>Log file exists: " . (file_exists($logFile) ? 'YES' : 'NO') . "</p>";

if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    echo "<p>Log file size: " . strlen($logs) . " bytes</p>";
    
    $lines = explode("\n", $logs);
    $recentLines = array_slice(array_reverse($lines), 0, 50);
    $policyLogs = array_filter($recentLines, function($line) {
        return strpos($line, 'OrganizationPolicy') !== false;
    });
    
    if (count($policyLogs) > 0) {
        echo "<p>Found " . count($policyLogs) . " policy log entries</p>";
        echo "<pre style='background: #f4f4f4; padding: 10px; overflow-x: auto;'>";
        echo implode("\n", array_reverse($policyLogs));
        echo "</pre>";
    } else {
        echo "<p style='color:orange'>No recent policy logs found</p>";
        echo "<p>Last 10 log lines:</p>";
        echo "<pre style='background: #f4f4f4; padding: 10px; overflow-x: auto;'>";
        echo implode("\n", array_slice(array_reverse($lines), 0, 10));
        echo "</pre>";
    }
} else {
    echo "<p style='color:red'>Log file not found</p>";
    $storageLogsDir = storage_path('logs');
    echo "<p>Logs directory exists: " . (is_dir($storageLogsDir) ? 'YES' : 'NO') . "</p>";
    if (is_dir($storageLogsDir)) {
        $files = scandir($storageLogsDir);
        echo "<p>Files in logs directory: " . implode(', ', array_diff($files, ['.', '..'])) . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='?'>Back to lists</a></p>";
