<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Group matches:" . PHP_EOL;
$matches = CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'groups')
    ->with('homePlayer', 'awayPlayer')
    ->get();

foreach ($matches as $match) {
    echo "- {$match->homePlayer->name} vs {$match->awayPlayer->name}" . PHP_EOL;
}