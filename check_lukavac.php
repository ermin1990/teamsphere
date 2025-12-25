<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TeamMatch;
use App\Models\Team;

$compId = 4;
$teamId = 10; // STK Lukavac

$matches = TeamMatch::where('competition_id', $compId)
    ->where(function($q) use ($teamId) {
        $q->where('home_team_id', $teamId)
          ->orWhere('away_team_id', $teamId);
    })->get();

echo "Matches for STK Lukavac (ID: $teamId): " . $matches->count() . "\n";
foreach ($matches as $m) {
    echo "Round {$m->round}: {$m->homeTeam->name} vs {$m->awayTeam->name} ({$m->status})\n";
}
