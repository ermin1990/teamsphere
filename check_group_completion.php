<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

echo 'Group completion status:' . PHP_EOL;
foreach($comp->tournamentGroups as $group) {
    $completedMatches = $group->matches()->where('status', 'completed')->count();
    $totalMatches = $group->matches()->count();
    echo "Group {$group->name}: {$completedMatches}/{$totalMatches} matches completed, is_completed: " . ($group->is_completed ? 'YES' : 'NO') . PHP_EOL;
}