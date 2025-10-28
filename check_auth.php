<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo 'Organization user_id: ' . $org->user_id . PHP_EOL;
echo 'Competition current_phase: ' . $comp->current_phase . PHP_EOL;
echo 'Competition knockout_bracket: ' . ($comp->knockout_bracket ? 'true' : 'false') . PHP_EOL;

// Check if user is authenticated (this will be null in CLI)
echo 'Auth check: Not available in CLI context' . PHP_EOL;

// But we can check if the reset would work by simulating the owner check
echo 'Simulated is_owner check: YES (assuming user is owner)' . PHP_EOL;