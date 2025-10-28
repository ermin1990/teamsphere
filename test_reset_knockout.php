<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Current competition state:" . PHP_EOL;
echo "Current phase: " . $comp->current_phase . PHP_EOL;
echo "Knockout bracket: " . ($comp->knockout_bracket ? 'true' : 'false') . PHP_EOL;
echo "Knockout matches count: " . ($comp->knockout_matches_count ?? 'null') . PHP_EOL;

$knockoutMatches = CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'knockout')
    ->count();

echo "Current knockout matches: " . $knockoutMatches . PHP_EOL;

if ($knockoutMatches > 0) {
    echo "Resetting knockout phase..." . PHP_EOL;

    // Delete all knockout matches
    CompetitionMatch::where('competition_id', $comp->id)
        ->where('phase', 'knockout')
        ->delete();

    // Reset competition knockout fields
    $comp->update([
        'knockout_bracket' => null,
        'knockout_completed_at' => null,
    ]);

    echo "Reset complete!" . PHP_EOL;

    $knockoutMatchesAfter = CompetitionMatch::where('competition_id', $comp->id)
        ->where('phase', 'knockout')
        ->count();

    echo "Knockout matches after reset: " . $knockoutMatchesAfter . PHP_EOL;
} else {
    echo "No knockout matches to reset." . PHP_EOL;
}