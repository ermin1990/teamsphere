<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Organization;
use App\Models\Competition;
use App\Models\Standing;
use App\Models\Player;

$org = Organization::where('slug', 'asee-doo')->first();
$comp = Competition::where('slug', 'joola-kup-2006-godiste-1761335055')->first();

// Get standings for this competition
$standings = Standing::where('competition_id', $comp->id)
    ->orderBy('group_number')
    ->orderBy('position')
    ->get();

echo "Current Standings:" . PHP_EOL;
echo str_repeat("=", 80) . PHP_EOL;

$currentGroup = null;
foreach ($standings as $standing) {
    if ($currentGroup !== $standing->group_number) {
        if ($currentGroup !== null) echo PHP_EOL;
        echo "Group {$standing->group_number}:" . PHP_EOL;
        echo str_repeat("-", 40) . PHP_EOL;
        $currentGroup = $standing->group_number;
    }

    $player = Player::find($standing->player_id);
    $playerName = $player ? $player->name : 'Unknown Player';

    echo "Position {$standing->position}: {$playerName}" . PHP_EOL;
    echo "  Points: {$standing->points}, Wins: {$standing->wins}, Losses: {$standing->losses}" . PHP_EOL;
    echo "  Sets Won: {$standing->sets_won}, Sets Lost: {$standing->sets_lost}" . PHP_EOL;
    echo "  Games Won: {$standing->games_won}, Games Lost: {$standing->games_lost}" . PHP_EOL;

    if ($standing->position <= $comp->players_advancing_per_group) {
        echo "  ✅ ADVANCES TO KNOCKOUT" . PHP_EOL;
    }
    echo PHP_EOL;
}

echo PHP_EOL . "Summary:" . PHP_EOL;
echo "- Competition: {$comp->name}" . PHP_EOL;
echo "- Current Phase: {$comp->current_phase}" . PHP_EOL;
echo "- Players advancing per group: {$comp->players_advancing_per_group}" . PHP_EOL;
echo "- Total groups: {$comp->group_count}" . PHP_EOL;
echo "- Expected knockout participants: " . ($comp->players_advancing_per_group * $comp->group_count) . PHP_EOL;