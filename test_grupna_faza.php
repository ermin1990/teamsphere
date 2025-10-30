<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'nova-liga-1761854390')->first();

echo "=== MILAN STEFANOVIĆ vs BRANKO SIMIĆ (GRUPNA FAZA) ===\n\n";

$match = $competition->matches()
    ->whereNotNull('tournament_group_id')
    ->whereHas('homePlayer', function($q) {
        $q->where('name', 'Milan Stefanović');
    })
    ->whereHas('awayPlayer', function($q) {
        $q->where('name', 'Branko Simić');
    })
    ->with('homePlayer', 'awayPlayer')
    ->first();

if ($match) {
    echo "Match found!\n";
    echo "Status: {$match->status}\n";
    echo "home_score: " . ($match->home_score ?? 'NULL') . "\n";
    echo "away_score: " . ($match->away_score ?? 'NULL') . "\n";
    echo "Sets: " . json_encode($match->sets) . "\n\n";
    
    // Calculate what blade will show
    $homeSetsWon = 0;
    $awaySetsWon = 0;
    
    if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
        foreach($match->sets as $set) {
            $homeScore = $set['home_score'] ?? $set['home'] ?? 0;
            $awayScore = $set['away_score'] ?? $set['away'] ?? 0;
            
            if($homeScore > $awayScore) {
                $homeSetsWon++;
            }
            if($awayScore > $homeScore) {
                $awaySetsWon++;
            }
            
            echo "Set: $homeScore - $awayScore\n";
        }
    } elseif ($match->status === 'completed') {
        $homeSetsWon = $match->home_score ?? 0;
        $awaySetsWon = $match->away_score ?? 0;
    }
    
    echo "\nŠTA BI BLADE TREBAO DA PRIKAŽE:\n";
    echo "Konačan rezultat: $homeSetsWon - $awaySetsWon\n";
    echo "Pojedinačni setovi: ";
    if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
        foreach($match->sets as $set) {
            $homeScore = $set['home_score'] ?? $set['home'] ?? 0;
            $awayScore = $set['away_score'] ?? $set['away'] ?? 0;
            echo "$homeScore-$awayScore  ";
        }
    } else {
        echo "NEMA";
    }
    echo "\n";
} else {
    echo "Match not found!\n";
}