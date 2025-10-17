<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$org = App\Models\Organization::first();
$league = App\Models\League::first();
$player1 = App\Models\Player::first();
$player2 = App\Models\Player::skip(1)->first();

if (!$org || !$league || !$player1 || !$player2) {
    echo "Missing required data. Please seed the database first.\n";
    exit(1);
}

$match = App\Models\LeagueMatch::create([
    'league_id' => $league->id,
    'home_player_id' => $player1->id,
    'away_player_id' => $player2->id,
    'home_score' => 2,
    'away_score' => 1,
    'status' => 'in_progress',
    'sets' => [
        ['home' => 11, 'away' => 8],
        ['home' => 9, 'away' => 11],
        ['home' => 11, 'away' => 7]
    ]
]);

echo "Match created with ID: " . $match->id . "\n";