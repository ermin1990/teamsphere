<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Competition status: " . $comp->status . PHP_EOL;
echo "Current phase: " . $comp->current_phase . PHP_EOL;
echo "Groups count: " . $comp->tournamentGroups()->count() . PHP_EOL;
echo "Group matches count: " . CompetitionMatch::where('competition_id', $comp->id)->where('phase', 'groups')->count() . PHP_EOL;