<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Competition;
use App\Models\CompetitionMatch;

echo "Tournaments: " . Competition::where('type', 'tournament')->count() . "\n";

$tournamentMatches = CompetitionMatch::whereHas('competition', function($q) {
    $q->where('type', 'tournament');
})->count();

echo "Tournament matches: " . $tournamentMatches . "\n";

// Test one tournament match ordering
$tournament = Competition::where('type', 'tournament')->first();
if ($tournament) {
    echo "Testing tournament: " . $tournament->name . "\n";

    $matches = CompetitionMatch::where('competition_id', $tournament->id)
        ->orderBy('round_number')
        ->orderBy('match_order')
        ->take(5)
        ->with(['homePlayer', 'awayPlayer'])
        ->get();

    echo "First 5 matches ordered by round_number, match_order:\n";
    foreach ($matches as $match) {
        $homePlayerName = $match->homePlayer ? $match->homePlayer->name : 'N/A';
        $awayPlayerName = $match->awayPlayer ? $match->awayPlayer->name : 'N/A';
        echo "Round {$match->round_number}, Match {$match->match_order}: {$homePlayerName} vs {$awayPlayerName} (Status: {$match->status})\n";
    }
}