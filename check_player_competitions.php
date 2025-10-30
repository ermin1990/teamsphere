<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$playerNames = ['Milan Stefanović', 'Uroš Jovanović', 'Luka Pavlović', 'Andrija Kovačević'];

echo "Checking competitions that have these players:\n";
foreach($playerNames as $name) {
    $player = App\Models\Player::where('name', $name)->first();
    if ($player) {
        $competitions = $player->leagues;
        echo "$name is in competitions:\n";
        foreach($competitions as $comp) {
            echo "  - " . $comp->name . " (ID: " . $comp->id . ", Slug: " . $comp->slug . ")\n";
        }
    }
    echo "\n";
}