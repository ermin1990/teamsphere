<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Manually marking groups as completed..." . PHP_EOL;

$groups = $comp->tournamentGroups;
foreach ($groups as $group) {
    $totalPlayers = count($group->player_ids ?? []);
    $requiredMatches = ($totalPlayers * ($totalPlayers - 1)) / 2;

    // Count completed matches for this group
    $completedMatches = CompetitionMatch::where('competition_id', $comp->id)
        ->where('tournament_group_id', $group->id)
        ->where('phase', 'group')
        ->where('status', 'completed')
        ->count();

    echo "Group {$group->name}: {$completedMatches}/{$requiredMatches} matches completed" . PHP_EOL;

    if ($completedMatches >= $requiredMatches && !$group->is_completed) {
        echo "  -> Marking as completed" . PHP_EOL;
        $group->update(['is_completed' => true, 'completed_at' => now()]);
    }
}

echo "Done!" . PHP_EOL;