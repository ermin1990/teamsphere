<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

$knockoutMatches = CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'knockout')
    ->with('homePlayer', 'awayPlayer')
    ->orderBy('round_number')
    ->orderBy('match_order')
    ->get();

echo "Knockout matches generated: " . $knockoutMatches->count() . PHP_EOL;
echo PHP_EOL;

foreach ($knockoutMatches as $match) {
    $homeName = $match->homePlayer ? $match->homePlayer->name : 'BYE';
    $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'BYE';
    echo "Round {$match->round_number}, Match {$match->match_order}: {$homeName} vs {$awayName}" . PHP_EOL;
}