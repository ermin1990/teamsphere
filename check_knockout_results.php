<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$matches = App\Models\CompetitionMatch::where('competition_id', 17)
    ->where('phase', 'knockout')
    ->where('status', 'completed')
    ->get();

echo 'Eliminacioni mečevi sa rezultatima:' . PHP_EOL;

foreach($matches as $match) {
    echo 'ID: ' . $match->id . ', Home: ' . ($match->homePlayer ? $match->homePlayer->name : 'N/A') . ' vs Away: ' . ($match->awayPlayer ? $match->awayPlayer->name : 'N/A') . PHP_EOL;
    echo '  Status: ' . $match->status . ', Home Score: ' . ($match->home_score ?? 'N/A') . ', Away Score: ' . ($match->away_score ?? 'N/A') . PHP_EOL;
    echo '  Sets: ' . json_encode($match->sets) . PHP_EOL;
    echo PHP_EOL;
}