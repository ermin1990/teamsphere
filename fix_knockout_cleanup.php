<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$competition = App\Models\Competition::where('slug', 'joola-kup-2025-juniorke-1761344299')->first();

echo "=== Deleting incorrect male player matches from women's tournament ===\n\n";

// Delete matches with male players
$malePlayers = ['Uroš Jovanović', 'Luka Pavlović', 'Milan Stefanović', 'Andrija Kovačević'];

$matchesToDelete = App\Models\CompetitionMatch::where('competition_id', $competition->id)
    ->where('phase', 'knockout')
    ->where(function($q) use ($malePlayers) {
        $q->whereHas('homePlayer', function($q2) use ($malePlayers) {
            $q2->whereIn('name', $malePlayers);
        })->orWhereHas('awayPlayer', function($q2) use ($malePlayers) {
            $q2->whereIn('name', $malePlayers);
        });
    })->get();

echo "Found " . $matchesToDelete->count() . " matches to delete:\n";
foreach($matchesToDelete as $match) {
    echo "  - Match ID {$match->id}: " . ($match->homePlayer ? $match->homePlayer->name : 'NULL') . " vs " . ($match->awayPlayer ? $match->awayPlayer->name : 'NULL') . "\n";
    $match->delete();
}

echo "\nDeleted incorrect matches.\n\n";

// Remove male players from competition
echo "=== Removing male players from women's tournament ===\n\n";
foreach($malePlayers as $name) {
    $player = App\Models\Player::where('name', $name)->first();
    if ($player) {
        $competition->players()->detach($player->id);
        echo "Removed $name from competition\n";
    }
}

echo "\n=== Remaining knockout matches ===\n";
$remainingMatches = $competition->matches()->where('phase', 'knockout')->with('homePlayer', 'awayPlayer')->orderBy('round_number')->get();
foreach($remainingMatches as $match) {
    $homeName = $match->homePlayer ? $match->homePlayer->name : 'NULL';
    $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'NULL';
    echo "Round {$match->round_number}: $homeName vs $awayName (Score: " . ($match->home_score ?? 0) . "-" . ($match->away_score ?? 0) . ")\n";
}