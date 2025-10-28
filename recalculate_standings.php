<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Services\TournamentGroupService;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

$groupService = app(TournamentGroupService::class);

echo "Recalculating standings for all groups..." . PHP_EOL;

$groups = $comp->tournamentGroups;
foreach ($groups as $group) {
    echo "Recalculating standings for Group {$group->name}..." . PHP_EOL;
    $groupService->recalculateGroupStandings($group);
}

echo "All group standings recalculated!" . PHP_EOL;