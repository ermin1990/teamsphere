<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Player;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

// Define realistic table tennis results for each group
// Format: [home_player_name, away_player_name, home_score, away_score, sets_data]
$results = [
    // Group 1
    ['Zijadić Benjamin', 'Mujkić Nisvet', 3, 0, [['home' => 11, 'away' => 8], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9]]],
    ['Ognjen Davidović', 'Vuk Stevanović', 3, 1, [['home' => 11, 'away' => 7], ['home' => 9, 'away' => 11], ['home' => 11, 'away' => 8], ['home' => 11, 'away' => 6]]],
    ['Zijadić Benjamin', 'Ognjen Davidović', 2, 3, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 9, 'away' => 11], ['home' => 7, 'away' => 11]]],
    ['Vuk Stevanović', 'Mujkić Nisvet', 3, 2, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Zijadić Benjamin', 'Vuk Stevanović', 3, 0, [['home' => 11, 'away' => 6], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 7]]],
    ['Mujkić Nisvet', 'Ognjen Davidović', 1, 3, [['home' => 11, 'away' => 9], ['home' => 7, 'away' => 11], ['home' => 8, 'away' => 11], ['home' => 6, 'away' => 11]]],

    // Group 2
    ['Barišić Filip', 'Panić Demijan', 3, 1, [['home' => 11, 'away' => 8], ['home' => 9, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 6]]],
    ['Fakić Raif', 'Aleksa Pantelić', 2, 3, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 9, 'away' => 11], ['home' => 7, 'away' => 11]]],
    ['Barišić Filip', 'Fakić Raif', 3, 0, [['home' => 11, 'away' => 6], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Aleksa Pantelić', 'Panić Demijan', 3, 2, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Barišić Filip', 'Aleksa Pantelić', 3, 1, [['home' => 11, 'away' => 8], ['home' => 9, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 6]]],
    ['Panić Demijan', 'Fakić Raif', 2, 3, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 9, 'away' => 11], ['home' => 7, 'away' => 11]]],

    // Group 3
    ['Irfan Hadžiabdić', 'Nidal Bašić', 3, 0, [['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Vukašin Kecman', 'Mario Banjaš', 3, 2, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Irfan Hadžiabdić', 'Vukašin Kecman', 3, 1, [['home' => 11, 'away' => 8], ['home' => 9, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 6]]],
    ['Mario Banjaš', 'Nidal Bašić', 3, 1, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 6]]],
    ['Irfan Hadžiabdić', 'Mario Banjaš', 3, 0, [['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Nidal Bašić', 'Vukašin Kecman', 1, 3, [['home' => 11, 'away' => 9], ['home' => 7, 'away' => 11], ['home' => 8, 'away' => 11], ['home' => 6, 'away' => 11]]],

    // Group 4
    ['Kanlić Aron', 'Karađuz Amin', 3, 0, [['home' => 11, 'away' => 8], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9]]],
    ['Čorić Emin', 'Tvrtković Dario', 2, 3, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 9, 'away' => 11], ['home' => 7, 'away' => 11]]],
    ['Kanlić Aron', 'Čorić Emin', 3, 1, [['home' => 11, 'away' => 8], ['home' => 9, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 6]]],
    ['Tvrtković Dario', 'Karađuz Amin', 3, 2, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Kanlić Aron', 'Tvrtković Dario', 3, 0, [['home' => 11, 'away' => 6], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Karađuz Amin', 'Čorić Emin', 1, 3, [['home' => 11, 'away' => 9], ['home' => 7, 'away' => 11], ['home' => 8, 'away' => 11], ['home' => 6, 'away' => 11]]],

    // Group 5 (3 players)
    ['Zlotrg Dino', 'Pavle Lukić', 3, 0, [['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Zlotrg Dino', 'Mičić Marko', 3, 1, [['home' => 11, 'away' => 8], ['home' => 9, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 6]]],
    ['Pavle Lukić', 'Mičić Marko', 2, 3, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 9, 'away' => 11], ['home' => 7, 'away' => 11]]],

    // Group 6 (3 players)
    ['Nikša Vesović', 'David Zrnić', 3, 1, [['home' => 11, 'away' => 8], ['home' => 9, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 6]]],
    ['Nikša Vesović', 'Vuk Sukara', 3, 0, [['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['David Zrnić', 'Vuk Sukara', 3, 2, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],

    // Group 7 (3 players)
    ['Maglić Kamer', 'Katana David', 3, 0, [['home' => 11, 'away' => 6], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Maglić Kamer', 'Škulj Bakir', 3, 1, [['home' => 11, 'away' => 8], ['home' => 9, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 6]]],
    ['Katana David', 'Škulj Bakir', 2, 3, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 9, 'away' => 11], ['home' => 7, 'away' => 11]]],

    // Group 8 (3 players)
    ['Zlatan Telalagić', 'Đorđe Ćelić', 3, 0, [['home' => 11, 'away' => 7], ['home' => 11, 'away' => 9], ['home' => 11, 'away' => 8]]],
    ['Zlatan Telalagić', 'Fakić Faruk', 3, 1, [['home' => 11, 'away' => 8], ['home' => 9, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 11, 'away' => 6]]],
    ['Đorđe Ćelić', 'Fakić Faruk', 2, 3, [['home' => 11, 'away' => 9], ['home' => 8, 'away' => 11], ['home' => 11, 'away' => 7], ['home' => 9, 'away' => 11], ['home' => 7, 'away' => 11]]],
];

echo "Updating match results..." . PHP_EOL;

foreach ($results as $result) {
    list($homeName, $awayName, $homeScore, $awayScore, $sets) = $result;

    // Find players
    $homePlayer = Player::where('name', $homeName)->first();
    $awayPlayer = Player::where('name', $awayName)->first();

    if (!$homePlayer || !$awayPlayer) {
        echo "ERROR: Could not find players: {$homeName} or {$awayName}" . PHP_EOL;
        continue;
    }

    // Find the match - check both home/away combinations
    $match = CompetitionMatch::where('competition_id', $comp->id)
        ->where('phase', 'groups')
        ->where(function($query) use ($homePlayer, $awayPlayer) {
            $query->where(function($q) use ($homePlayer, $awayPlayer) {
                $q->where('home_player_id', $homePlayer->id)
                  ->where('away_player_id', $awayPlayer->id);
            })->orWhere(function($q) use ($homePlayer, $awayPlayer) {
                $q->where('home_player_id', $awayPlayer->id)
                  ->where('away_player_id', $homePlayer->id);
            });
        })
        ->first();

    if (!$match) {
        echo "ERROR: Could not find match between {$homeName} and {$awayName}" . PHP_EOL;
        continue;
    }

    // Update match with results
    $match->update([
        'home_score' => $homeScore,
        'away_score' => $awayScore,
        'sets' => json_encode($sets),
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    echo "Updated: {$homeName} {$homeScore}-{$awayScore} {$awayName}" . PHP_EOL;
}

echo PHP_EOL . "All group phase results have been entered!" . PHP_EOL;