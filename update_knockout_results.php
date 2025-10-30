<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'joola-kup-2025-juniorke-1761344299')->first();

if (!$competition) {
    echo "Competition not found\n";
    exit(1);
}

$playerNames = ['Milan Stefanović', 'Uroš Jovanović', 'Luka Pavlović', 'Andrija Kovačević'];

echo "Adding players to competition:\n";
foreach($playerNames as $name) {
    $player = App\Models\Player::where('name', $name)->first();
    if ($player) {
        // Check if player is already in competition
        $exists = $competition->players()->where('player_id', $player->id)->exists();
        if (!$exists) {
            $competition->players()->attach($player->id, ['joined_at' => now()]);
            echo "Added $name to competition\n";
        } else {
            echo "$name already in competition\n";
        }
    } else {
        echo "Player $name not found\n";
    }
}

// Now update knockout matches
// First, let's see current knockout matches again
$knockoutMatches = $competition->matches()->where('phase', 'knockout')->orderBy('round_number')->get();

echo "\nCurrent knockout matches:\n";
foreach($knockoutMatches as $match) {
    echo "Round " . $match->round_number . ": ID " . $match->id . "\n";
}

// For semifinals (round 2), we need 2 matches
// For final (round 3), we need 1 match

// Let's update semifinals
$semifinal1 = $knockoutMatches->where('round_number', 2)->first(); // First semifinal
$semifinal2 = $knockoutMatches->where('round_number', 2)->skip(1)->first(); // Second semifinal

$milan = App\Models\Player::where('name', 'Milan Stefanović')->first();
$uros = App\Models\Player::where('name', 'Uroš Jovanović')->first();
$luka = App\Models\Player::where('name', 'Luka Pavlović')->first();
$andrija = App\Models\Player::where('name', 'Andrija Kovačević')->first();

if ($semifinal1 && $milan && $uros) {
    $semifinal1->update([
        'home_player_id' => $milan->id,
        'away_player_id' => $uros->id,
        'status' => 'completed',
        'home_score' => 0,
        'away_score' => 3, // Uroš wins
    ]);
    echo "Updated semifinal 1: Milan vs Uroš (Uroš wins 3-0)\n";
}

if ($semifinal2 && $luka && $andrija) {
    $semifinal2->update([
        'home_player_id' => $luka->id,
        'away_player_id' => $andrija->id,
        'status' => 'completed',
        'home_score' => 3,
        'away_score' => 0, // Luka wins
    ]);
    echo "Updated semifinal 2: Luka vs Andrija (Luka wins 3-0)\n";
}

// Now update final
$final = $knockoutMatches->where('round_number', 3)->first();

if ($final && $uros && $luka) {
    $final->update([
        'home_player_id' => $uros->id,
        'away_player_id' => $luka->id,
        'status' => 'completed',
        'home_score' => 3,
        'away_score' => 0, // Uroš wins
    ]);
    echo "Updated final: Uroš vs Luka (Uroš wins 3-0)\n";
}

// Mark competition as completed
$competition->update(['status' => 'completed']);

echo "Competition marked as completed. Champion: Uroš Jovanović\n";