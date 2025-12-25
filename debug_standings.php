<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Standing;

$standings = Standing::where('competition_id', 4)->orderBy('position')->get();

echo "Standings for Competition 4 (Total: " . $standings->count() . "):\n";
foreach ($standings as $s) {
    $participantName = $s->participant ? $s->participant->name : 'NULL';
    echo "Pos: {$s->position}, Team ID: " . ($s->team_id ?? 'NULL') . ", Name: {$participantName}\n";
}
