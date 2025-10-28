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

echo "Resetting standings for competition: " . $comp->name . PHP_EOL;

// Reset all standings to 0
Standing::where('competition_id', $comp->id)->update([
    'played' => 0,
    'won' => 0,
    'lost' => 0,
    'drawn' => 0,
    'points' => 0,
    'sets_won' => 0,
    'sets_lost' => 0,
    'points_won' => 0,
    'points_lost' => 0,
    'position' => 999,
]);

echo "Standings reset to 0" . PHP_EOL;

// Reset match results to null
CompetitionMatch::where('competition_id', $comp->id)
    ->whereNotNull('tournament_group_id')
    ->update([
        'status' => 'scheduled',
        'home_score' => null,
        'away_score' => null,
        'sets' => null,
        'played_at' => null,
    ]);

echo "Match results reset" . PHP_EOL;