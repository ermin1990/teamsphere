<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Testing JOOLA knockout generation..." . PHP_EOL;

// Reset knockout phase first
CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'knockout')
    ->delete();

$comp->update([
    'current_phase' => 'groups',
    'knockout_bracket' => null,
]);

// Generate knockout bracket
$comp->generateKnockoutBracket();

echo "Knockout bracket generated!" . PHP_EOL;

// Show the generated matches
$matches = CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'knockout')
    ->with('homePlayer', 'awayPlayer')
    ->orderBy('match_order')
    ->get();

echo PHP_EOL . "Generated matches:" . PHP_EOL;
foreach ($matches as $match) {
    $homeName = $match->homePlayer ? $match->homePlayer->name : 'BYE';
    $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'BYE';
    echo "Match {$match->match_order}: {$homeName} vs {$awayName}" . PHP_EOL;
}

echo PHP_EOL . "Bracket positions analysis:" . PHP_EOL;

// Get groups and standings to verify the logic
$groups = $comp->tournamentGroups()
    ->orderBy('name')
    ->get();

// Load players for display
$players = \App\Models\Player::whereIn('id', collect($groups)->pluck('player_ids')->flatten()->unique())->get()->keyBy('id');

foreach ($groups as $group) {
    echo "Group {$group->name}:" . PHP_EOL;
    $standings = collect($group->standings ?? [])->sortByDesc(function ($player) {
        return [
            $player['points'] ?? 0,
            ($player['sets_won'] ?? 0) - ($player['sets_lost'] ?? 0),
            $player['sets_won'] ?? 0,
        ];
    })->values();
    
    foreach ($standings as $index => $standing) {
        $position = $index + 1;
        $playerName = $players[$standing['player_id']]->name ?? 'Unknown';
        echo "  Position {$position}: {$playerName} ({$standing['points']} pts)" . PHP_EOL;
    }
    echo PHP_EOL;
}