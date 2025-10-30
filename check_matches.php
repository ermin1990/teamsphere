<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$matches = App\Models\CompetitionMatch::where('competition_id', 17)->with(['homePlayer', 'awayPlayer'])->get();
echo 'Ukupno mečeva: ' . $matches->count() . PHP_EOL;

foreach($matches as $match) {
    echo 'ID: ' . $match->id . ', Phase: ' . $match->phase . ', Round: ' . $match->round_number . ', Home: ' . ($match->homePlayer ? $match->homePlayer->name : 'N/A') . ', Away: ' . ($match->awayPlayer ? $match->awayPlayer->name : 'N/A') . ', Status: ' . $match->status . ', Home Score: ' . ($match->home_score ?? 'N/A') . ', Away Score: ' . ($match->away_score ?? 'N/A') . PHP_EOL;
}