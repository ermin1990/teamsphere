<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Competition details:" . PHP_EOL;
echo "- Status: " . $comp->status . PHP_EOL;
echo "- Type: " . $comp->type . PHP_EOL;
echo "- Players count: " . $comp->players()->count() . PHP_EOL;
echo "- Groups count: " . $comp->tournamentGroups()->count() . PHP_EOL;

$groups = $comp->tournamentGroups;
foreach ($groups as $group) {
    echo PHP_EOL . "Group " . $group->name . ":" . PHP_EOL;
    echo "- Players: " . count($group->player_ids) . PHP_EOL;
    echo "- Player IDs: " . implode(', ', $group->player_ids) . PHP_EOL;
}