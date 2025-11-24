<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Competition;
use App\Models\Standing;
use App\Models\TournamentGroup;

// Find all competitions
echo "All competitions:\n";
$allComps = Competition::with('tournamentGroups')->get();
foreach ($allComps as $comp) {
    echo "  {$comp->id} - {$comp->name} (Type: {$comp->type}, Groups: {$comp->tournamentGroups->count()})\n";
}
echo "\n";

// Find competition - Asee liga Oktobar tournament
$competition = Competition::find(36);

if (!$competition) {
    echo "Competition not found!\n";
    echo "All competitions:\n";
    Competition::all(['id', 'name'])->each(function($c) {
        echo "  {$c->id} - {$c->name}\n";
    });
    exit;
}

echo "=== COMPETITION: {$competition->name} (ID: {$competition->id}) ===\n";
echo "Type: {$competition->type}\n";
echo "Status: {$competition->status}\n";
echo "Current phase: {$competition->current_phase}\n";
echo "Groups count: " . $competition->tournamentGroups->count() . "\n\n";

// Check each group
foreach ($competition->tournamentGroups as $group) {
    echo "--- GRUPA {$group->name} ---\n";
    
    // Get standings with proper sorting
    $standings = Standing::where('competition_id', $competition->id)
        ->where('tournament_group_id', $group->id)
        ->orderByDesc('points')
        ->orderByRaw('(sets_won - sets_lost) DESC')
        ->orderByRaw('(points_won - points_lost) DESC')
        ->orderByDesc('sets_won')
        ->orderByDesc('won')
        ->orderBy('id')
        ->get();
    
    echo "Sorted standings:\n";
    foreach ($standings as $index => $standing) {
        $actualPos = $index + 1;
        $dbPos = $standing->position ?? 'NULL';
        
        echo sprintf(
            "  %d. %s | DB pos: %s | Points: %d | Sets: %d-%d (diff: %+d) | Gems: %d-%d (diff: %+d)\n",
            $actualPos,
            $standing->player->name ?? 'N/A',
            $dbPos,
            $standing->points,
            $standing->sets_won,
            $standing->sets_lost,
            $standing->sets_won - $standing->sets_lost,
            $standing->points_won ?? 0,
            $standing->points_lost ?? 0,
            ($standing->points_won ?? 0) - ($standing->points_lost ?? 0)
        );
    }
    
    
    // Show matches for this group
    $groupMatches = \App\Models\CompetitionMatch::where('competition_id', $competition->id)
        ->where('tournament_group_id', $group->id)
        ->where('status', 'completed')
        ->orderBy('round_number')
        ->get();
    
    if ($groupMatches->count() > 0) {
        echo "Completed matches:\n";
        foreach ($groupMatches as $match) {
            $homeName = $match->homePlayer ? $match->homePlayer->name : 'N/A';
            $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'N/A';
            echo "  {$homeName} vs {$awayName}: {$match->home_score}-{$match->away_score}\n";
            if ($match->sets && is_array($match->sets)) {
                echo "    Sets: ";
                foreach ($match->sets as $set) {
                    echo "({$set['home']}-{$set['away']}) ";
                }
                echo "\n";
            } else {
                echo "    Sets: EMPTY or NULL\n";
            }
        }
    }
    
    echo "\n";
}

// Check knockout matches
$knockoutMatches = \App\Models\CompetitionMatch::where('competition_id', $competition->id)
    ->where('phase', 'knockout')
    ->orderBy('round_number')
    ->orderBy('match_order')
    ->get();

if ($knockoutMatches->count() > 0) {
    echo "--- KNOCKOUT MATCHES ---\n";
    foreach ($knockoutMatches as $match) {
        echo "Round {$match->round_number}, Match {$match->match_order}:\n";
        $homeName = $match->homePlayer ? $match->homePlayer->name : 'TBD';
        $awayName = $match->awayPlayer ? $match->awayPlayer->name : 'TBD';
        echo "  Home: {$homeName} (ID: {$match->home_player_id})\n";
        echo "    -> Saved: Group {$match->home_player_group}, Position {$match->home_player_position}\n";
        echo "  Away: {$awayName} (ID: {$match->away_player_id})\n";
        echo "    -> Saved: Group {$match->away_player_group}, Position {$match->away_player_position}\n";
        
        // Find their groups and positions
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
                    echo "    -> From Group {$group->name}, Position " . ($position + 1) . "\n";
                    break;
                }
            }
        }
        
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
                    echo "    -> From Group {$group->name}, Position " . ($position + 1) . "\n";
                    break;
                }
            }
        }
        
        echo "\n";
    }
}
