<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::find(17);
$matches = App\Models\CompetitionMatch::where('competition_id', 17)->with('tournamentGroup')->get()->groupBy('tournament_group_id');

foreach($matches as $groupId => $groupMatches) {
    $group = $competition->tournamentGroups->firstWhere('id', $groupId);
    $standings = App\Models\Standing::where('competition_id', 17)->where('tournament_group_id', $groupId)->with('player')->get();
    echo 'Group ' . ($group ? $group->name : 'Unknown') . ': ' . $standings->count() . ' standings' . PHP_EOL;
}