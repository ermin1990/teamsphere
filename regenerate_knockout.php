<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Deleting existing knockout matches..." . PHP_EOL;
CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'knockout')
    ->delete();

echo "Resetting competition knockout fields..." . PHP_EOL;
$comp->update([
    'current_phase' => 'groups',
    'knockout_bracket' => null,
    'knockout_completed_at' => null,
    'knockout_started_at' => null,
]);

echo "Regenerating knockout bracket..." . PHP_EOL;
$comp->generateKnockoutBracket();

echo "Knockout bracket regenerated successfully!" . PHP_EOL;