<?php
// Check competition slugs
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Competition;

echo "<h1>Competition Slugs Check</h1>";

$competitions = Competition::all();
echo "Found {$competitions->count()} competitions:<br><br>";

foreach ($competitions as $comp) {
    $url = route('organizations.competitions.show', [
        'organization' => $comp->organization->slug,
        'competition' => $comp->slug
    ]);
    
    echo "<strong>ID:</strong> {$comp->id}<br>";
    echo "<strong>Name:</strong> {$comp->name}<br>";
    echo "<strong>Slug:</strong> {$comp->slug}<br>";
    echo "<strong>Organization:</strong> {$comp->organization->name} ({$comp->organization->slug})<br>";
    echo "<strong>URL:</strong> <a href='{$url}'>{$url}</a><br>";
    echo "<hr>";
}
