<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'nova-liga-1761854390')->first();

echo "=== CHECKING MATCHES WITH SETS ===\n\n";

$allMatches = $competition->matches()->with('homePlayer', 'awayPlayer')->get();

echo "GRUPA FAZA (ako postoji):\n";
$groupMatches = $allMatches->whereNotNull('tournament_group_id');
foreach($groupMatches as $match) {
    if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
        echo "Match: {$match->homePlayer->name} vs {$match->awayPlayer->name}\n";
        echo "  Status: {$match->status}\n";
        echo "  home_score: " . ($match->home_score ?? 'NULL') . "\n";
        echo "  away_score: " . ($match->away_score ?? 'NULL') . "\n";
        echo "  Sets: " . json_encode($match->sets) . "\n\n";
    }
}

echo "\nKNOCKOUT FAZA:\n";
$knockoutMatches = $allMatches->where('phase', 'knockout');
foreach($knockoutMatches as $match) {
    if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
        echo "Match: {$match->homePlayer->name} vs {$match->awayPlayer->name}\n";
        echo "  Status: {$match->status}\n";
        echo "  home_score: " . ($match->home_score ?? 'NULL') . "\n";
        echo "  away_score: " . ($match->away_score ?? 'NULL') . "\n";
        echo "  Sets: " . json_encode($match->sets) . "\n\n";
    }
}