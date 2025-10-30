<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'joola-kup-2025-juniorke-1761344299')->first();

if (!$competition) {
    echo "Competition not found\n";
    exit(1);
}

$maxRound = $competition->matches()->where('phase', 'knockout')->max('round_number');
echo "Max round: $maxRound\n";

// Check if final exists
$final = $competition->matches()->where('phase', 'knockout')->where('round_number', $maxRound)->first();

if (!$final) {
    echo "No final match found\n";
    exit(1);
}

echo "Final match ID: " . $final->id . "\n";

// Update final with winners
$uros = App\Models\Player::where('name', 'Uroš Jovanović')->first();
$luka = App\Models\Player::where('name', 'Luka Pavlović')->first();

if ($uros && $luka) {
    $final->update([
        'home_player_id' => $uros->id,
        'away_player_id' => $luka->id,
        'status' => 'completed',
        'home_score' => 3,
        'away_score' => 0,
    ]);
    echo "Updated final: Uroš vs Luka (Uroš wins 3-0)\n";
}