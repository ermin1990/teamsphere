<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$standings = App\Models\Standing::where('competition_id', 17)->get();
echo 'Broj Standing zapisa: ' . $standings->count() . PHP_EOL;

if ($standings->count() > 0) {
    foreach($standings as $standing) {
        echo 'ID: ' . $standing->id . ', Player: ' . ($standing->player ? $standing->player->name : 'N/A') . ', Position: ' . $standing->position . PHP_EOL;
    }
} else {
    echo 'Nema Standing zapisa u bazi.' . PHP_EOL;
}