<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Updating match phases from 'groups' to 'group'..." . PHP_EOL;

$updated = CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'groups')
    ->update(['phase' => 'group']);

echo "Updated {$updated} matches" . PHP_EOL;