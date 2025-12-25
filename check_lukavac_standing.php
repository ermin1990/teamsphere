<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Team;
use App\Models\Standing;

$team = Team::find(10);
echo "Checking team 10...\n";
if ($team) {
    echo "Team 10: {$team->name}\n";
    try {
        $standing = Standing::where('competition_id', 4)->where('team_id', 10)->first();
        if ($standing) {
            echo "Standing found: Pos {$standing->position}\n";
        } else {
            echo "Standing NOT found for competition 4\n";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Team 10 NOT found in database\n";
}
