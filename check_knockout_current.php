<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'joola-kup-2025-juniorke-1761344299')->first();

if (!$competition) {
    echo "Competition not found\n";
    exit(1);
}

$knockoutMatches = $competition->matches()->where('phase', 'knockout')->with('homePlayer', 'awayPlayer')->orderBy('round_number')->get();

echo "Current knockout matches:\n";
foreach($knockoutMatches as $match) {
    $homeName = $match->homePlayer ? $match->homePlayer->name : 'Unknown';
    $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'Unknown';
    echo $match->id . ': ' . $homeName . ' vs ' . $awayName . ' - Round: ' . $match->round_number . ' - Status: ' . $match->status . ' - Score: ' . ($match->home_score ?? 0) . '-' . ($match->away_score ?? 0) . "\n";
}

echo "\nPlayers in knockout:\n";
$players = [];
foreach($knockoutMatches as $match) {
    if ($match->homePlayer) {
        $players[$match->homePlayer->name] = $match->home_player_id;
    }
    if ($match->awayPlayer) {
        $players[$match->awayPlayer->name] = $match->away_player_id;
    }
}

foreach($players as $name => $id) {
    echo "$name (ID: $id)\n";
}