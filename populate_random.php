<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Populating all group matches with random results..." . PHP_EOL;

$matches = CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'groups')
    ->where('status', 'scheduled')
    ->get();

$updated = 0;
foreach ($matches as $match) {
    // Generate random scores (best of 5 sets, so max 3 sets to win)
    $homeScore = rand(0, 3);
    $awayScore = rand(0, 3);

    // Make sure someone wins (no draws in table tennis)
    if ($homeScore == $awayScore) {
        if ($homeScore < 3) {
            $homeScore = 3;
        } else {
            $awayScore = $homeScore - 1;
        }
    }

    // Generate sets data
    $sets = [];
    $homeSetsWon = 0;
    $awaySetsWon = 0;

    for ($i = 0; $i < 5; $i++) {
        if ($homeSetsWon == 3 || $awaySetsWon == 3) break;

        $homeSetScore = rand(0, 11);
        $awaySetScore = rand(0, 11);

        // Make sure set winner is correct
        if ($homeSetsWon < 3 && $awaySetsWon < 3) {
            if ($homeSetsWon < $homeScore) {
                // Home should win this set
                while ($homeSetScore <= $awaySetScore || ($homeSetScore < 11 && $awaySetScore < 11)) {
                    $homeSetScore = rand(11, 15);
                    $awaySetScore = rand(0, 10);
                }
                $homeSetsWon++;
            } else {
                // Away should win this set
                while ($awaySetScore <= $homeSetScore || ($awaySetScore < 11 && $homeSetScore < 11)) {
                    $awaySetScore = rand(11, 15);
                    $homeSetScore = rand(0, 10);
                }
                $awaySetsWon++;
            }
        }

        $sets[] = [
            'home' => $homeSetScore,
            'away' => $awaySetScore
        ];
    }

    $match->update([
        'home_score' => $homeScore,
        'away_score' => $awayScore,
        'sets' => json_encode($sets),
        'status' => 'completed',
        'completed_at' => now(),
    ]);

    echo "Updated: {$match->homePlayer->name} {$homeScore}-{$awayScore} {$match->awayPlayer->name}" . PHP_EOL;
    $updated++;
}

echo PHP_EOL . "Updated {$updated} matches with random results!" . PHP_EOL;