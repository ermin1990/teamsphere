<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'joola-kup-2025-juniorke-1761344299')->first();

if (!$competition) {
    echo "Competition not found\n";
    exit(1);
}

echo "Testing what public page will see:\n\n";

// Load data exactly as controller does
$competition->load([
    'organization',
    'sport',
    'standings.team',
    'standings.player',
    'tournamentGroups',
    'matches' => function ($query) {
        $query->orderBy('round_number')
              ->orderBy('match_order')
              ->with(['homePlayer', 'awayPlayer', 'tournamentGroup']);
    }
]);

echo "Total matches loaded: " . $competition->matches->count() . "\n\n";

// Process as blade template does
$allMatches = $competition->matches ?? collect();

$knockoutMatches = $allMatches->where('phase', 'knockout')
    ->sortBy(['round_number', 'match_order'])
    ->groupBy('round_number');

echo "Knockout matches grouped by round:\n";
foreach($knockoutMatches as $round => $matches) {
    echo "\nRound $round (" . $matches->count() . " matches):\n";
    foreach($matches as $match) {
        $homeName = $match->homePlayer ? $match->homePlayer->name : 'NULL';
        $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'NULL';
        echo "  - $homeName vs $awayName (Score: " . ($match->home_score ?? 0) . "-" . ($match->away_score ?? 0) . ", Status: {$match->status})\n";
    }
}

echo "\n\n=== CHECKING BLADE TEMPLATE LOGIC ===\n";
$totalRounds = $knockoutMatches->count();
echo "Total rounds: $totalRounds\n";

$firstRoundMatches = $knockoutMatches->get(1) ?? collect();
echo "First round matches count: " . $firstRoundMatches->count() . "\n";

$numPlayers = $firstRoundMatches->count() * 2;
echo "Number of players (first round * 2): $numPlayers\n";

$expectedRounds = $numPlayers > 1 ? ceil(log($numPlayers, 2)) : 1;
echo "Expected rounds: $expectedRounds\n";

// Check if tournament is completed and get winner
$finalMatch = $knockoutMatches->get($totalRounds)?->first();
echo "\nFinal match (round $totalRounds):\n";
if ($finalMatch) {
    echo "  Home: " . ($finalMatch->homePlayer ? $finalMatch->homePlayer->name : 'NULL') . "\n";
    echo "  Away: " . ($finalMatch->awayPlayer ? $finalMatch->awayPlayer->name : 'NULL') . "\n";
    echo "  Status: " . $finalMatch->status . "\n";
    echo "  Score: " . ($finalMatch->home_score ?? 0) . "-" . ($finalMatch->away_score ?? 0) . "\n";
    
    $winner = null;
    if ($finalMatch->status === 'completed' && $totalRounds == $expectedRounds) {
        $winner = $finalMatch->home_score > $finalMatch->away_score
            ? $finalMatch->homePlayer
            : $finalMatch->awayPlayer;
        echo "  Winner: " . ($winner ? $winner->name : 'NULL') . "\n";
    } else {
        echo "  Winner: Not determined (status={$finalMatch->status}, totalRounds=$totalRounds, expectedRounds=$expectedRounds)\n";
    }
} else {
    echo "  No final match found\n";
}