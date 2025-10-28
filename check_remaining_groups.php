<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Groups in tournament: ";
foreach($comp->tournamentGroups as $group) {
    echo $group->name . " ";
}
echo PHP_EOL;

echo "Checking matches for remaining groups (C-H):" . PHP_EOL;
foreach($comp->tournamentGroups as $group) {
    if (in_array($group->name, ['C', 'D', 'E', 'F', 'G', 'H'])) {
        echo "Group {$group->name}:" . PHP_EOL;
        $matches = $group->matches()->with('homePlayer', 'awayPlayer')->get();
        foreach($matches as $match) {
            echo "  {$match->homePlayer->name} vs {$match->awayPlayer->name}" . PHP_EOL;
        }
        echo PHP_EOL;
    }
}