<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Standing;
use App\Models\Player;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

// Get all tournament groups for this competition
$groups = $comp->tournamentGroups;

echo "Updating standings for competition: {$comp->name}" . PHP_EOL;
echo "Number of groups: " . $groups->count() . PHP_EOL . PHP_EOL;

foreach ($groups as $group) {
    echo "Processing Group {$group->group_number}:" . PHP_EOL;

    // Get all players in this group
    $playerIds = $group->player_ids ?? [];

    // Initialize standings array
    $standingsData = [];
    foreach ($playerIds as $playerId) {
        $standingsData[$playerId] = [
            'played' => 0,
            'won' => 0,
            'lost' => 0,
            'drawn' => 0,
            'points' => 0,
            'sets_won' => 0,
            'sets_lost' => 0,
            'games_won' => 0,
            'games_lost' => 0,
        ];
    }

    // Get all completed matches in this group
    $matches = CompetitionMatch::where('competition_id', $comp->id)
        ->where('tournament_group_id', $group->id)
        ->where('phase', 'groups')
        ->where('is_completed', true)
        ->get();

    echo "  Found {$matches->count()} completed matches" . PHP_EOL;

    // Calculate standings from matches
    foreach ($matches as $match) {
        $homePlayerId = $match->home_player_id;
        $awayPlayerId = $match->away_player_id;
        $homeScore = $match->home_score ?? 0;
        $awayScore = $match->away_score ?? 0;

        // Update home player stats
        if (isset($standingsData[$homePlayerId])) {
            $standingsData[$homePlayerId]['played']++;
            $standingsData[$homePlayerId]['sets_won'] += $homeScore;
            $standingsData[$homePlayerId]['sets_lost'] += $awayScore;

            if ($homeScore > $awayScore) {
                $standingsData[$homePlayerId]['won']++;
                $standingsData[$homePlayerId]['points'] += 3; // 3 points for win
            } elseif ($homeScore < $awayScore) {
                $standingsData[$homePlayerId]['lost']++;
                // 0 points for loss
            } else {
                $standingsData[$homePlayerId]['drawn']++;
                $standingsData[$homePlayerId]['points'] += 1; // 1 point for draw
            }
        }

        // Update away player stats
        if (isset($standingsData[$awayPlayerId])) {
            $standingsData[$awayPlayerId]['played']++;
            $standingsData[$awayPlayerId]['sets_won'] += $awayScore;
            $standingsData[$awayPlayerId]['sets_lost'] += $homeScore;

            if ($awayScore > $homeScore) {
                $standingsData[$awayPlayerId]['won']++;
                $standingsData[$awayPlayerId]['points'] += 3;
            } elseif ($awayScore < $homeScore) {
                $standingsData[$awayPlayerId]['lost']++;
                // 0 points for loss
            } else {
                $standingsData[$awayPlayerId]['drawn']++;
                $standingsData[$awayPlayerId]['points'] += 1;
            }
        }
    }

    // Sort standings by points, then by set difference, then by sets won
    $sortedStandings = collect($standingsData)->map(function ($stats, $playerId) {
        return array_merge($stats, [
            'player_id' => $playerId,
            'set_difference' => $stats['sets_won'] - $stats['sets_lost']
        ]);
    })->sortByDesc(function ($player) {
        return [
            $player['points'],
            $player['set_difference'],
            $player['sets_won']
        ];
    })->values();

    // Update or create Standing records
    foreach ($sortedStandings as $position => $playerStats) {
        $playerId = $playerStats['player_id'];

        Standing::updateOrCreate(
            [
                'competition_id' => $comp->id,
                'player_id' => $playerId,
            ],
            [
                'group_number' => $group->group_number,
                'played' => $playerStats['played'],
                'won' => $playerStats['won'],
                'drawn' => $playerStats['drawn'],
                'lost' => $playerStats['lost'],
                'points' => $playerStats['points'],
                'sets_won' => $playerStats['sets_won'],
                'sets_lost' => $playerStats['sets_lost'],
                'position' => $position + 1,
            ]
        );

        $player = Player::find($playerId);
        $playerName = $player ? $player->name : 'Unknown';

        echo "  Position " . ($position + 1) . ": {$playerName} - {$playerStats['points']} pts, {$playerStats['won']}W {$playerStats['lost']}L" . PHP_EOL;
    }

    echo PHP_EOL;
}

echo "Standings update completed!" . PHP_EOL;