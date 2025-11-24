<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Standing;

$competition = Competition::find(36);

if (!$competition) {
    echo "Competition not found!\n";
    exit;
}

echo "=== Updating Knockout Matches with Correct Positions ===\n\n";

$knockoutMatches = CompetitionMatch::where('competition_id', $competition->id)
    ->where('phase', 'knockout')
    ->get();

if ($knockoutMatches->count() == 0) {
    echo "No knockout matches found. Regenerating...\n";
    
    // Mark groups as completed
    foreach ($competition->tournamentGroups as $group) {
        $group->update(['is_completed' => true]);
    }
    
    // Generate knockout
    $competition->update(['current_phase' => 'groups']);
    $competition->generateKnockoutBracket();
    
    $knockoutMatches = CompetitionMatch::where('competition_id', $competition->id)
        ->where('phase', 'knockout')
        ->get();
}

echo "Found {$knockoutMatches->count()} knockout matches\n\n";

foreach ($knockoutMatches as $match) {
    $updates = [];
    
    // Find home player's group and position
    if ($match->home_player_id) {
        foreach ($competition->tournamentGroups as $group) {
            $standings = Standing::where('competition_id', $competition->id)
                ->where('tournament_group_id', $group->id)
                ->orderByDesc('points')
                ->orderByRaw('(sets_won - sets_lost) DESC')
                ->orderByRaw('(points_won - points_lost) DESC')
                ->orderByDesc('sets_won')
                ->orderByDesc('won')
                ->orderBy('id')
                ->get();
            
            $position = $standings->search(function($s) use ($match) {
                return $s->player_id == $match->home_player_id;
            });
            
            if ($position !== false) {
                $updates['home_player_group'] = $group->name;
                $updates['home_player_position'] = $position + 1;
                break;
            }
        }
    }
    
    // Find away player's group and position
    if ($match->away_player_id) {
        foreach ($competition->tournamentGroups as $group) {
            $standings = Standing::where('competition_id', $competition->id)
                ->where('tournament_group_id', $group->id)
                ->orderByDesc('points')
                ->orderByRaw('(sets_won - sets_lost) DESC')
                ->orderByRaw('(points_won - points_lost) DESC')
                ->orderByDesc('sets_won')
                ->orderByDesc('won')
                ->orderBy('id')
                ->get();
            
            $position = $standings->search(function($s) use ($match) {
                return $s->player_id == $match->away_player_id;
            });
            
            if ($position !== false) {
                $updates['away_player_group'] = $group->name;
                $updates['away_player_position'] = $position + 1;
                break;
            }
        }
    }
    
    if (!empty($updates)) {
        $match->update($updates);
        $homeName = $match->homePlayer ? $match->homePlayer->name : 'TBD';
        $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'TBD';
        echo "Updated Match {$match->match_order}: {$homeName} ({$updates['home_player_group']}-{$updates['home_player_position']}) vs {$awayName} ({$updates['away_player_group']}-{$updates['away_player_position']})\n";
    }
}

echo "\n✓ All matches updated!\n";
