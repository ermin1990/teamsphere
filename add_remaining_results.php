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

// Define match results to add for remaining groups C-H
$matchResults = [
    // Group C
    ['group' => 'C', 'home' => 'Kanlić Aron', 'away' => 'Đorđe Ćelić', 'home_score' => 3, 'away_score' => 1],
    ['group' => 'C', 'home' => 'Vuk Stevanović', 'away' => 'Zijadić Benjamin', 'home_score' => 3, 'away_score' => 2],
    ['group' => 'C', 'home' => 'Kanlić Aron', 'away' => 'Zijadić Benjamin', 'home_score' => 3, 'away_score' => 0],
    ['group' => 'C', 'home' => 'Đorđe Ćelić', 'away' => 'Vuk Stevanović', 'home_score' => 2, 'away_score' => 3],
    ['group' => 'C', 'home' => 'Kanlić Aron', 'away' => 'Vuk Stevanović', 'home_score' => 3, 'away_score' => 1],
    ['group' => 'C', 'home' => 'Zijadić Benjamin', 'away' => 'Đorđe Ćelić', 'home_score' => 3, 'away_score' => 2],

    // Group D
    ['group' => 'D', 'home' => 'Vukašin Kecman', 'away' => 'Aleksa Pantelić', 'home_score' => 3, 'away_score' => 0],
    ['group' => 'D', 'home' => 'Karađuz Amin', 'away' => 'Irfan Hadžiabdić', 'home_score' => 2, 'away_score' => 3],
    ['group' => 'D', 'home' => 'Vukašin Kecman', 'away' => 'Irfan Hadžiabdić', 'home_score' => 3, 'away_score' => 1],
    ['group' => 'D', 'home' => 'Aleksa Pantelić', 'away' => 'Karađuz Amin', 'home_score' => 3, 'away_score' => 2],
    ['group' => 'D', 'home' => 'Vukašin Kecman', 'away' => 'Karađuz Amin', 'home_score' => 3, 'away_score' => 0],
    ['group' => 'D', 'home' => 'Irfan Hadžiabdić', 'away' => 'Aleksa Pantelić', 'home_score' => 3, 'away_score' => 1],

    // Group E
    ['group' => 'E', 'home' => 'Tvrtković Dario', 'away' => 'Čorić Emin', 'home_score' => 3, 'away_score' => 2],
    ['group' => 'E', 'home' => 'Škulj Bakir', 'away' => 'Čorić Emin', 'home_score' => 3, 'away_score' => 1],
    ['group' => 'E', 'home' => 'Škulj Bakir', 'away' => 'Tvrtković Dario', 'home_score' => 3, 'away_score' => 0],

    // Group F
    ['group' => 'F', 'home' => 'Mario Banjaš', 'away' => 'Nidal Bašić', 'home_score' => 3, 'away_score' => 1],
    ['group' => 'F', 'home' => 'Mičić Marko', 'away' => 'Nidal Bašić', 'home_score' => 3, 'away_score' => 2],
    ['group' => 'F', 'home' => 'Mičić Marko', 'away' => 'Mario Banjaš', 'home_score' => 3, 'away_score' => 0],

    // Group G
    ['group' => 'G', 'home' => 'Fakić Faruk', 'away' => 'Vuk Sukara', 'home_score' => 3, 'away_score' => 1],
    ['group' => 'G', 'home' => 'Fakić Raif', 'away' => 'Vuk Sukara', 'home_score' => 3, 'away_score' => 2],
    ['group' => 'G', 'home' => 'Fakić Raif', 'away' => 'Fakić Faruk', 'home_score' => 3, 'away_score' => 0],

    // Group H
    ['group' => 'H', 'home' => 'David Zrnić', 'away' => 'Maglić Kamer', 'home_score' => 3, 'away_score' => 2],
    ['group' => 'H', 'home' => 'Ognjen Davidović', 'away' => 'Maglić Kamer', 'home_score' => 3, 'away_score' => 1],
    ['group' => 'H', 'home' => 'Ognjen Davidović', 'away' => 'David Zrnić', 'home_score' => 3, 'away_score' => 0],
];

echo "Adding match results for remaining groups (C-H) one by one and checking standings update..." . PHP_EOL;
echo str_repeat("=", 80) . PHP_EOL;

foreach ($matchResults as $index => $result) {
    echo PHP_EOL . "Adding result " . ($index + 1) . ": {$result['home']} vs {$result['away']} ({$result['home_score']}-{$result['away_score']})" . PHP_EOL;

    // Find the match
    $group = $comp->tournamentGroups()->where('name', $result['group'])->first();
    $homePlayer = Player::where('name', $result['home'])->first();
    $awayPlayer = Player::where('name', $result['away'])->first();

    $match = CompetitionMatch::where('competition_id', $comp->id)
        ->where('tournament_group_id', $group->id)
        ->where('home_player_id', $homePlayer->id)
        ->where('away_player_id', $awayPlayer->id)
        ->first();

    if (!$match) {
        echo "ERROR: Match not found!" . PHP_EOL;
        continue;
    }

    // Update match result
    $match->update([
        'status' => 'completed',
        'home_score' => $result['home_score'],
        'away_score' => $result['away_score'],
        'played_at' => now(),
    ]);

    // Update standings using the same logic as LiveScore
    $tournamentGroup = $match->tournamentGroup;
    if ($tournamentGroup) {
        $tournamentGroup->updateStandings($match);
        // Also update Eloquent standings in database
        $match->refresh(); // Refresh to get updated sets data
        $groupService = app(\App\Services\TournamentGroupService::class);
        $groupService->recalculateGroupStandings($tournamentGroup);
    }

    echo "Match updated. Checking standings for Group {$result['group']}:" . PHP_EOL;

    // Show current standings for this group
    $standings = Standing::where('competition_id', $comp->id)
        ->where('tournament_group_id', $group->id)
        ->with('player')
        ->orderBy('position')
        ->get();

    foreach($standings as $standing) {
        echo "  {$standing->position}. {$standing->player->name} - {$standing->points} pts ({$standing->won}W {$standing->lost}L {$standing->sets_won}-{$standing->sets_lost})" . PHP_EOL;
    }

    echo str_repeat("-", 40) . PHP_EOL;
}

echo PHP_EOL . "Final standings for all groups:" . PHP_EOL;
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