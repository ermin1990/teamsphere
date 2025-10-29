<?php
// Test if CompetitionController exists and loads
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<h1>CompetitionController Test</h1>";

// Check if controller file exists
$controllerPath = base_path('app/Http/Controllers/CompetitionController.php');
echo "<h2>1. File Check</h2>";
echo "Path: {$controllerPath}<br>";
echo "Exists: " . (file_exists($controllerPath) ? 'YES ✅' : 'NO ❌') . "<br>";

if (file_exists($controllerPath)) {
    echo "Size: " . filesize($controllerPath) . " bytes<br>";
    echo "Last modified: " . date('Y-m-d H:i:s', filemtime($controllerPath)) . "<br>";
}

// Check if class can be loaded
echo "<h2>2. Class Loading</h2>";
try {
    $controller = app()->make(\App\Http\Controllers\CompetitionController::class);
    echo "Controller loaded: YES ✅<br>";
} catch (\Exception $e) {
    echo "Controller loaded: NO ❌<br>";
    echo "Error: {$e->getMessage()}<br>";
    echo "<pre>{$e->getTraceAsString()}</pre>";
}

// Check route
echo "<h2>3. Route Check</h2>";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$found = false;

foreach ($routes as $route) {
    if ($route->getName() === 'organizations.competitions.show') {
        $found = true;
        echo "Route name: {$route->getName()}<br>";
        echo "URI: {$route->uri()}<br>";
        echo "Action: {$route->getActionName()}<br>";
        echo "Methods: " . implode(', ', $route->methods()) . "<br>";
        echo "Middleware: " . implode(', ', $route->gatherMiddleware()) . "<br>";
        break;
    }
}

if (!$found) {
    echo "<p style='color: red;'>❌ Route 'organizations.competitions.show' NOT FOUND!</p>";
}

// Test actual access
echo "<h2>4. Simulated Request</h2>";
try {
    $organization = \App\Models\Organization::where('slug', 'asee-doo')->first();
    $competition = \App\Models\Competition::where('slug', 'joola-kup-2025-juniorke-1761344299')->first();
    
    if (!$organization) {
        echo "❌ Organization not found<br>";
    } else {
        echo "✅ Organization found: {$organization->name}<br>";
    }
    
    if (!$competition) {
        echo "❌ Competition not found<br>";
    } else {
        echo "✅ Competition found: {$competition->name}<br>";
    }
    
    if ($organization && $competition) {
        // Check if controller method exists
        $controller = new \App\Http\Controllers\CompetitionController();
        if (method_exists($controller, 'show')) {
            echo "✅ Controller method 'show' exists<br>";
        } else {
            echo "❌ Controller method 'show' does NOT exist<br>";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Error: {$e->getMessage()}<br>";
    echo "<pre>{$e->getTraceAsString()}</pre>";
}

echo "<hr>";
echo "<h2>5. Try accessing the URL:</h2>";
echo "<a href='https://teamsphere.infinitycreative.agency/organizations/asee-doo/competitions/joola-kup-2025-juniorke-1761344299' target='_blank'>
    Click here to test
</a>";
