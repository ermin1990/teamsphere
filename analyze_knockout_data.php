<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'joola-kup-2025-juniorke-1761344299')->first();

if (!$competition) {
    echo "Competition not found\n";
    exit(1);
}

echo "=== COMPETITION INFO ===\n";
echo "Name: " . $competition->name . "\n";
echo "Type: " . $competition->type . "\n";
echo "Status: " . $competition->status . "\n";
echo "Is public: " . ($competition->is_public ? 'Yes' : 'No') . "\n\n";

echo "=== ALL MATCHES ===\n";
$allMatches = $competition->matches()->with('homePlayer', 'awayPlayer')->orderBy('phase')->orderBy('round_number')->get();

foreach($allMatches as $match) {
    $homeName = $match->homePlayer ? $match->homePlayer->name : 'NULL';
    $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'NULL';
    echo sprintf(
        "ID: %d | Phase: %s | Round: %d | %s vs %s | Status: %s | Score: %d-%d\n",
        $match->id,
        $match->phase,
        $match->round_number,
        $homeName,
        $awayName,
        $match->status,
        $match->home_score ?? 0,
        $match->away_score ?? 0
    );
}

echo "\n=== KNOCKOUT MATCHES ONLY ===\n";
$knockoutMatches = $competition->matches()->where('phase', 'knockout')->with('homePlayer', 'awayPlayer')->orderBy('round_number')->get();

echo "Total knockout matches: " . $knockoutMatches->count() . "\n\n";

foreach($knockoutMatches as $match) {
    $homeName = $match->homePlayer ? $match->homePlayer->name : 'NULL';
    $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'NULL';
    echo sprintf(
        "Round %d: %s vs %s | Status: %s | Score: %d-%d\n",
        $match->round_number,
        $homeName,
        $awayName,
        $match->status,
        $match->home_score ?? 0,
        $match->away_score ?? 0
    );
}

echo "\n=== GROUPED BY ROUND ===\n";
$grouped = $knockoutMatches->groupBy('round_number');
foreach($grouped as $round => $matches) {
    echo "Round $round (" . $matches->count() . " matches):\n";
    foreach($matches as $match) {
        $homeName = $match->homePlayer ? $match->homePlayer->name : 'NULL';
        $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'NULL';
        echo "  - $homeName vs $awayName (Score: " . ($match->home_score ?? 0) . "-" . ($match->away_score ?? 0) . ")\n";
    }
    echo "\n";
}

echo "\n=== CHECKING WHAT BLADE TEMPLATE WILL SEE ===\n";
$knockoutMatches = $competition->matches()
    ->where('phase', 'knockout')
    ->with('homePlayer', 'awayPlayer')
    ->orderBy('round_number')
    ->get();

$groupedKnockout = $knockoutMatches->groupBy('round_number');

echo "Grouped knockout has " . $groupedKnockout->count() . " rounds\n";
foreach($groupedKnockout as $roundNumber => $roundMatches) {
    echo "\nRound $roundNumber:\n";
    echo "  Match count: " . $roundMatches->count() . "\n";
    
    foreach($roundMatches as $match) {
        echo "  Match ID " . $match->id . ":\n";
        echo "    Home player ID: " . ($match->home_player_id ?? 'NULL') . "\n";
        echo "    Away player ID: " . ($match->away_player_id ?? 'NULL') . "\n";
        echo "    Home player object: " . ($match->homePlayer ? $match->homePlayer->name : 'NULL') . "\n";
        echo "    Away player object: " . ($match->awayPlayer ? $match->awayPlayer->name : 'NULL') . "\n";
        echo "    Status: " . $match->status . "\n";
        echo "    Home score: " . ($match->home_score ?? 'NULL') . "\n";
        echo "    Away score: " . ($match->away_score ?? 'NULL') . "\n";
    }
}