<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo "Testing resetKnockout functionality..." . PHP_EOL;

// Simulate the resetKnockout method
try {
    echo "Deleting knockout matches..." . PHP_EOL;
    $deleted = CompetitionMatch::where('competition_id', $comp->id)
        ->where('phase', 'knockout')
        ->delete();
    echo "Deleted $deleted knockout matches" . PHP_EOL;

    echo "Resetting competition fields..." . PHP_EOL;
    $comp->update([
        'current_phase' => 'groups',
        'knockout_bracket' => null,
        'knockout_completed_at' => null,
        'knockout_started_at' => null,
    ]);

    echo "Reset successful!" . PHP_EOL;

    // Check final state
    $comp->refresh();
    $remainingMatches = CompetitionMatch::where('competition_id', $comp->id)
        ->where('phase', 'knockout')
        ->count();

    echo "Final state:" . PHP_EOL;
    echo "Current phase: " . $comp->current_phase . PHP_EOL;
    echo "Knockout bracket: " . ($comp->knockout_bracket ? 'true' : 'false') . PHP_EOL;
    echo "Remaining knockout matches: " . $remainingMatches . PHP_EOL;

} catch (\Exception $e) {
    echo "Error during reset: " . $e->getMessage() . PHP_EOL;
}