<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Competition;
use App\Models\CompetitionMatch;

$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();
$matches = CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'groups')
    ->take(10)
    ->get();

echo "Checking match group associations:" . PHP_EOL;
foreach($matches as $match) {
    echo 'Match ID: ' . $match->id . ', Group ID: ' . ($match->tournament_group_id ?? 'NULL') . ', Status: ' . $match->status . PHP_EOL;
}