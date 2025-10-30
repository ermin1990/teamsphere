<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$groups = App\Models\Competition::find(17)->tournamentGroups;
foreach($groups as $group) {
    echo $group->name . ' ' . $group->id . ':' . PHP_EOL;
    $players = App\Models\Player::whereIn('id', $group->player_ids)->get();
    foreach($players as $player) {
        echo '  ' . $player->id . ' - ' . $player->name . PHP_EOL;
    }
    echo PHP_EOL;
}