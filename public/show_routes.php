<?php
// Show all routes for debugging

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

echo "<h1>All Routes Matching 'organizations'</h1>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Method</th><th>URI</th><th>Name</th><th>Action</th><th>Middleware</th></tr>";

$routes = Route::getRoutes();

foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'organizations') !== false && strpos($uri, '{organization}') !== false) {
        $methods = implode('|', $route->methods());
        $name = $route->getName() ?? '-';
        $action = $route->getActionName();
        $middleware = implode(', ', $route->gatherMiddleware());
        
        // Highlight the specific route
        $style = '';
        if ($uri === 'organizations/{organization}' && in_array('GET', $route->methods())) {
            $style = 'background: yellow; font-weight: bold;';
        }
        if ($uri === 'admin/organizations/{organization}' && in_array('GET', $route->methods())) {
            $style = 'background: lightgreen; font-weight: bold;';
        }
        
        echo "<tr style='$style'>";
        echo "<td>$methods</td>";
        echo "<td>$uri</td>";
        echo "<td>$name</td>";
        echo "<td>$action</td>";
        echo "<td>$middleware</td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<hr>";
echo "<h2>Test URLs:</h2>";
echo "<a href='/organizations/asee-doo' target='_blank'>GET /organizations/asee-doo</a> (yellow route)<br>";
echo "<a href='/admin/organizations/asee-doo' target='_blank'>GET /admin/organizations/asee-doo</a> (green route)<br>";
