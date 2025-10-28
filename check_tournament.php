<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Player;
use App\Models\Standing;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Organization: " . $org->name . PHP_EOL;
echo "Competition: " . $comp->name . PHP_EOL;
echo "Current Phase: " . $comp->current_phase . PHP_EOL;
echo "Groups: " . $comp->group_count . PHP_EOL;
echo "Players per group: " . $comp->players_per_group . PHP_EOL;
echo "Knockout matches count: " . $comp->knockout_matches_count . PHP_EOL;

// Get all matches in groups phase
$matches = CompetitionMatch::where('competition_id', $comp->id)
    ->whereNotNull('tournament_group_id')
    ->with(['homePlayer', 'awayPlayer', 'tournamentGroup'])
    ->orderBy('tournament_group_id')
    ->orderBy('id')
    ->get()
    ->groupBy('tournament_group_id');

echo PHP_EOL . "Group Phase Matches:" . PHP_EOL;
foreach ($matches as $groupId => $groupMatches) {
    $group = $groupMatches->first()->tournamentGroup;
    echo "Group {$group->name}:" . PHP_EOL;
    foreach ($groupMatches as $match) {
        echo "  " . ($match->homePlayer ? $match->homePlayer->name : 'TBD') . " vs ";
        echo ($match->awayPlayer ? $match->awayPlayer->name : 'TBD') . " - ";
        echo "Score: " . ($match->home_score ?? '-') . "-" . ($match->away_score ?? '-') . " (" . $match->status . ")";
        echo PHP_EOL;
    }
    echo PHP_EOL;
}

// Show current standings
echo PHP_EOL . "Current Standings:" . PHP_EOL;
foreach($comp->tournamentGroups as $group) {
    echo "Group {$group->name}:" . PHP_EOL;
    $standings = Standing::where('competition_id', $comp->id)
        ->where('tournament_group_id', $group->id)
        ->with('player')
        ->orderBy('position')
        ->get();

    foreach($standings as $standing) {
        echo "  {$standing->position}. {$standing->player->name} - {$standing->points} pts ({$standing->won}W {$standing->lost}L {$standing->sets_won}-{$standing->sets_lost})" . PHP_EOL;
    }
    echo PHP_EOL;
}