<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Competition;
use App\Models\CompetitionMatch;

$competition = Competition::find(36);

if (!$competition) {
    echo "Competition not found!\n";
    exit;
}

echo "=== Regenerating Knockout for: {$competition->name} ===\n\n";

// Delete existing knockout matches
$deleted = CompetitionMatch::where('competition_id', $competition->id)
    ->where('phase', 'knockout')
    ->delete();

echo "Deleted {$deleted} existing knockout matches\n";

// Reset phase to groups
$competition->update(['current_phase' => 'groups']);

echo "Reset phase to 'groups'\n";

// Mark all groups as completed
foreach ($competition->tournamentGroups as $group) {
    $group->update(['is_completed' => true]);
    echo "Marked group {$group->name} as completed\n";
}

// Regenerate knockout
try {
    echo "\nGenerating knockout bracket...\n";
    $competition->generateKnockoutBracket();
    echo "✓ Knockout bracket regenerated successfully!\n\n";
    
    // Show new matches
    $newMatches = CompetitionMatch::where('competition_id', $competition->id)
        ->where('phase', 'knockout')
        ->orderBy('round_number')
        ->orderBy('match_order')
        ->get();
    
    echo "New knockout matches:\n";
    foreach ($newMatches as $match) {
        $homeName = $match->homePlayer ? $match->homePlayer->name : 'TBD';
        $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'TBD';
        echo "  Match {$match->match_order}: {$homeName} ({$match->home_player_group}-{$match->home_player_position}) vs {$awayName} ({$match->away_player_group}-{$match->away_player_position})\n";
    }
} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
}
