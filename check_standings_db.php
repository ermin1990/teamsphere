<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$comp = \App\Models\Competition::where('slug', 'premijer-liga-muskarci-20252026-1766657518')->first();
if ($comp) {
    $standings = \App\Models\Standing::where('competition_id', $comp->id)->get();
    foreach ($standings as $standing) {
        // In my previous script I might have set participant_id which doesn't exist in DB
        // but Eloquent might have ignored it or failed.
        // Let's check what's actually in the DB.
        echo "Standing ID: " . $standing->id . " Team ID: " . $standing->team_id . " Player ID: " . $standing->player_id . "\n";
    }
} else {
    echo "Competition not found\n";
}
