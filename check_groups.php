<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Group completion status:" . PHP_EOL;
$groups = $comp->tournamentGroups;
foreach ($groups as $group) {
    $totalMatches = CompetitionMatch::where('competition_id', $comp->id)
        ->where('tournament_group_id', $group->id)
        ->count();

    $completedMatches = CompetitionMatch::where('competition_id', $comp->id)
        ->where('tournament_group_id', $group->id)
        ->where('status', 'completed')
        ->count();

    echo "Group {$group->name}: {$completedMatches}/{$totalMatches} matches completed" . PHP_EOL;
}