<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'nova-liga-1761854390')->first();

if (!$competition) {
    echo "Competition not found\n";
    exit(1);
}

echo "=== CHECKING FOR MATCHES WITH SET DETAILS ===\n\n";

$allMatches = $competition->matches()->with('homePlayer', 'awayPlayer')->get();

foreach($allMatches as $match) {
    if(isset($match->sets) && is_array($match->sets) && count($match->sets) > 0) {
        echo "Match ID {$match->id}: {$match->homePlayer->name} vs {$match->awayPlayer->name}\n";
        echo "  Status: {$match->status}\n";
        echo "  Phase: {$match->phase}\n";
        echo "  Sets: " . json_encode($match->sets) . "\n\n";
    }
}