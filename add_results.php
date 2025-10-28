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

// Define match results to add
$matchResults = [
    // Group A
    ['group' => 'A', 'home' => 'Panić Demijan', 'away' => 'Katana David', 'home_score' => 2, 'away_score' => 1],
    ['group' => 'A', 'home' => 'Pavle Lukić', 'away' => 'Mujkić Nisvet', 'home_score' => 2, 'away_score' => 0],
    ['group' => 'A', 'home' => 'Panić Demijan', 'away' => 'Mujkić Nisvet', 'home_score' => 3, 'away_score' => 0],
    ['group' => 'A', 'home' => 'Katana David', 'away' => 'Pavle Lukić', 'home_score' => 3, 'away_score' => 2],
    ['group' => 'A', 'home' => 'Panić Demijan', 'away' => 'Pavle Lukić', 'home_score' => 3, 'away_score' => 2],
    ['group' => 'A', 'home' => 'Mujkić Nisvet', 'away' => 'Katana David', 'home_score' => 3, 'away_score' => 2],

    // Group B
    ['group' => 'B', 'home' => 'Zlotrg Dino', 'away' => 'Zlatan Telalagić', 'home_score' => 3, 'away_score' => 0],
    ['group' => 'B', 'home' => 'Barišić Filip', 'away' => 'Nikša Vesović', 'home_score' => 3, 'away_score' => 0],
    ['group' => 'B', 'home' => 'Zlotrg Dino', 'away' => 'Nikša Vesović', 'home_score' => 3, 'away_score' => 2],
    ['group' => 'B', 'home' => 'Zlatan Telalagić', 'away' => 'Barišić Filip', 'home_score' => 3, 'away_score' => 0],
    ['group' => 'B', 'home' => 'Zlotrg Dino', 'away' => 'Barišić Filip', 'home_score' => 3, 'away_score' => 1],
    ['group' => 'B', 'home' => 'Nikša Vesović', 'away' => 'Zlatan Telalagić', 'home_score' => 2, 'away_score' => 0],
];

echo "Adding match results one by one and checking standings update..." . PHP_EOL;
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