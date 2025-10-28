<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\CompetitionMatch;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

$match = CompetitionMatch::where('competition_id', $comp->id)
    ->where('phase', 'groups')
    ->first();

if ($match) {
    echo "Sample match:" . PHP_EOL;
    echo "- ID: {$match->id}" . PHP_EOL;
    echo "- Phase: {$match->phase}" . PHP_EOL;
    echo "- Tournament Group ID: " . ($match->tournament_group_id ?? 'NULL') . PHP_EOL;
    echo "- Status: {$match->status}" . PHP_EOL;
    echo "- Home Player: " . ($match->homePlayer ? $match->homePlayer->name : 'NULL') . PHP_EOL;
    echo "- Away Player: " . ($match->awayPlayer ? $match->awayPlayer->name : 'NULL') . PHP_EOL;
} else {
    echo "No matches found with phase 'groups'" . PHP_EOL;

    // Try with phase 'group'
    $match = CompetitionMatch::where('competition_id', $comp->id)
        ->where('phase', 'group')
        ->first();

    if ($match) {
        echo "Found match with phase 'group':" . PHP_EOL;
        echo "- ID: {$match->id}" . PHP_EOL;
        echo "- Phase: {$match->phase}" . PHP_EOL;
        echo "- Tournament Group ID: " . ($match->tournament_group_id ?? 'NULL') . PHP_EOL;
        echo "- Status: {$match->status}" . PHP_EOL;
    } else {
        echo "No matches found at all!" . PHP_EOL;
    }
}