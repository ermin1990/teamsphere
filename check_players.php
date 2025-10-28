<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Players in competition:" . PHP_EOL;
$players = $comp->players()->orderBy('name')->get();
foreach ($players as $player) {
    echo "- {$player->name} (ID: {$player->id})" . PHP_EOL;
}

echo PHP_EOL . "Groups and their players:" . PHP_EOL;
$groups = $comp->tournamentGroups;
foreach ($groups as $group) {
    echo "Group {$group->name}:" . PHP_EOL;
    foreach ($group->player_ids as $playerId) {
        $player = $comp->players->find($playerId);
        if ($player) {
            echo "  - {$player->name}" . PHP_EOL;
        }
    }
    echo PHP_EOL;
}