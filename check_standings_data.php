<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

$group = $comp->tournamentGroups()->first();
echo "Group {$group->name} standings:" . PHP_EOL;
var_dump($group->standings);

echo PHP_EOL . "Group {$group->name} is_completed: " . ($group->is_completed ? 'true' : 'false') . PHP_EOL;