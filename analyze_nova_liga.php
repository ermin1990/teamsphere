<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'nova-liga-1761854390')->first();

if (!$competition) {
    echo "Competition not found\n";
    exit(1);
}

echo "=== NOVA LIGA - ANALYSIS ===\n\n";
echo "Name: " . $competition->name . "\n";
echo "Type: " . $competition->type . "\n";
echo "Status: " . $competition->status . "\n\n";

// Load matches
$competition->load([
    'matches' => function ($query) {
        $query->orderBy('round_number')
              ->orderBy('match_order')
              ->with(['homePlayer', 'awayPlayer', 'tournamentGroup']);
    }
]);

$allMatches = $competition->matches;

echo "Total matches: " . $allMatches->count() . "\n\n";

$knockoutMatches = $allMatches->where('phase', 'knockout')
    ->sortBy(['round_number', 'match_order'])
    ->groupBy('round_number');

echo "=== KNOCKOUT MATCHES ===\n\n";
foreach($knockoutMatches as $round => $matches) {
    echo "Round $round (" . $matches->count() . " matches):\n";
    foreach($matches as $match) {
        $homeName = $match->homePlayer ? $match->homePlayer->name : 'NULL';
        $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'NULL';
        echo sprintf(
            "  Match ID %d: %s vs %s\n",
            $match->id,
            $homeName,
            $awayName
        );
        echo "    Status: {$match->status}\n";
        echo "    home_score field: " . ($match->home_score ?? 'NULL') . "\n";
        echo "    away_score field: " . ($match->away_score ?? 'NULL') . "\n";
        echo "    sets field: " . (isset($match->sets) ? json_encode($match->sets) : 'NULL') . "\n";
        
        // Check what blade will see
        $homeSetsWon = 0;
        $awaySetsWon = 0;
        if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
            foreach($match->sets as $set) {
                if(($set['home_score'] ?? $set['home'] ?? 0) > ($set['away_score'] ?? $set['away'] ?? 0)) {
                    $homeSetsWon++;
                }
                if(($set['away_score'] ?? $set['away'] ?? 0) > ($set['home_score'] ?? $set['home'] ?? 0)) {
                    $awaySetsWon++;
                }
            }
        } elseif ($match->status === 'completed') {
            $homeSetsWon = $match->home_score ?? 0;
            $awaySetsWon = $match->away_score ?? 0;
        }
        echo "    Calculated homeSetsWon: $homeSetsWon\n";
        echo "    Calculated awaySetsWon: $awaySetsWon\n";
        echo "\n";
    }
    echo "\n";
}