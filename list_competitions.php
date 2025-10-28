<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Competition;

$competitions = Competition::where('type', 'tournament')->get();
echo 'Svi turniri:' . PHP_EOL;
foreach ($competitions as $comp) {
    $groups = $comp->tournamentGroups()->count();
    $matches = $comp->matches()->count();
    echo $comp->name . ' - Grupe: ' . $groups . ', Mečevi: ' . $matches . PHP_EOL;
}