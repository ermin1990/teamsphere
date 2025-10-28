<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Competition status:" . PHP_EOL;
echo "Current phase: " . $comp->current_phase . PHP_EOL;
echo "Knockout bracket: " . ($comp->knockout_bracket ? 'true' : 'false') . PHP_EOL;
echo "Knockout matches count: " . ($comp->knockout_matches_count ?? 'null') . PHP_EOL;

$knockoutMatches = CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'knockout')
    ->count();

echo "Current knockout matches: " . $knockoutMatches . PHP_EOL;

if ($knockoutMatches == 0) {
    echo "No knockout matches found - Reset button won't be shown!" . PHP_EOL;
    echo "Let's generate some knockout matches to test the reset button..." . PHP_EOL;

    // Generate knockout bracket
    $comp->generateKnockoutBracket();

    $knockoutMatchesAfter = CompetitionMatch::where('competition_id', $comp->id)
        ->where('phase', 'knockout')
        ->count();

    echo "Knockout matches after generation: " . $knockoutMatchesAfter . PHP_EOL;
    echo "Now the Reset button should be visible!" . PHP_EOL;
} else {
    echo "Knockout matches exist - Reset button should be visible." . PHP_EOL;
}