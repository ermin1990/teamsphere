<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\CompetitionMatch;

$count = CompetitionMatch::where('competition_id', 36)
    ->where('phase', 'knockout')
    ->count();

echo "Knockout matches count: {$count}\n";

$matches = CompetitionMatch::where('competition_id', 36)
    ->where('phase', 'knockout')
    ->get();

foreach ($matches as $match) {
    echo "Match {$match->id}: Home group={$match->home_player_group}, pos={$match->home_player_position}, Away group={$match->away_player_group}, pos={$match->away_player_position}\n";
}
