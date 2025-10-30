<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Find which competition should have the male players
$competitions = App\Models\Competition::whereHas('players', function($q) {
    $q->whereIn('name', ['Uroš Jovanović', 'Luka Pavlović', 'Milan Stefanović', 'Andrija Kovačević']);
})->get();

echo "=== Competitions with male players ===\n";
foreach($competitions as $comp) {
    echo $comp->id . ": " . $comp->name . " (Slug: " . $comp->slug . ")\n";
}

// Check competition_player pivot
echo "\n=== Checking competition_player for these players ===\n";
$playerNames = ['Uroš Jovanović', 'Luka Pavlović', 'Milan Stefanović', 'Andrija Kovačević'];
foreach($playerNames as $name) {
    $player = App\Models\Player::where('name', $name)->first();
    if ($player) {
        $comps = DB::table('competition_player')->where('player_id', $player->id)->get();
        echo "$name (ID: {$player->id}):\n";
        foreach($comps as $cp) {
            $comp = App\Models\Competition::find($cp->competition_id);
            echo "  - Competition ID {$cp->competition_id}: " . ($comp ? $comp->name : 'NOT FOUND') . "\n";
        }
    }
}

// Check matches for these players
echo "\n=== Checking matches with these players ===\n";
$matches = App\Models\CompetitionMatch::whereHas('homePlayer', function($q) {
    $q->whereIn('name', ['Uroš Jovanović', 'Luka Pavlović', 'Milan Stefanović', 'Andrija Kovačević']);
})->orWhereHas('awayPlayer', function($q) {
    $q->whereIn('name', ['Uroš Jovanović', 'Luka Pavlović', 'Milan Stefanović', 'Andrija Kovačević']);
})->with('competition', 'homePlayer', 'awayPlayer')->get();

foreach($matches as $match) {
    echo "Match ID {$match->id}: Competition '{$match->competition->name}' (ID: {$match->competition_id})\n";
    echo "  {$match->homePlayer->name} vs {$match->awayPlayer->name}\n";
}