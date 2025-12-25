<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Team;
use App\Models\TeamMatch;

$compId = 4;
$matches = TeamMatch::where('competition_id', $compId)->get();

$teamIds = collect();
foreach ($matches as $match) {
    $teamIds->push($match->home_team_id);
    $teamIds->push($match->away_team_id);
}

$uniqueTeamIds = $teamIds->unique()->filter();

echo "Found " . $uniqueTeamIds->count() . " unique teams in matches.\n";

foreach ($uniqueTeamIds as $teamId) {
    $team = Team::find($teamId);
    if ($team) {
        $team->competition_id = $compId;
        $team->save();
        echo "Updated team: {$team->name}\n";
    }
}
