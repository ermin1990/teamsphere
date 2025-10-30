<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$playerNames = ['Milan Stefanović', 'Uroš Jovanović', 'Luka Pavlović', 'Andrija Kovačević'];

echo "Searching for players:\n";
foreach($playerNames as $name) {
    $player = App\Models\Player::where('name', $name)->first();
    if ($player) {
        echo "$name found - ID: " . $player->id . "\n";
    } else {
        echo "$name NOT found\n";
    }
}

// Check all players in the competition
$competition = App\Models\Competition::where('slug', 'joola-kup-2025-juniorke-1761344299')->first();
$allPlayers = $competition->players;

echo "\nAll players in competition:\n";
foreach($allPlayers as $player) {
    echo $player->id . ': ' . $player->name . "\n";
}