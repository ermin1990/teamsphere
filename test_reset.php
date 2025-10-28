<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo 'Before reset:' . PHP_EOL;
echo 'Current phase: ' . $comp->current_phase . PHP_EOL;
echo 'Knockout bracket: ' . ($comp->knockout_bracket ? 'true' : 'false') . PHP_EOL;

// Simulate reset
$comp->update([
    'current_phase' => 'groups',
    'knockout_bracket' => null,
    'knockout_completed_at' => null,
    'knockout_started_at' => null,
]);

echo 'After reset:' . PHP_EOL;
$comp->refresh();
echo 'Current phase: ' . $comp->current_phase . PHP_EOL;
echo 'Knockout bracket: ' . ($comp->knockout_bracket ? 'true' : 'false') . PHP_EOL;