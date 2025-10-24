<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Player;
use App\Models\Standing;
use App\Models\TournamentGroup;
use Illuminate\Support\Collection;

/**
 * JOOLA Bracket Service
 * 
 * Implements the JOOLA Cup knockout bracket system:
 * - Winners of groups 1 and 2 are seeded (positions A and B)
 * - Other group winners are drawn randomly (positions C, D, E...)
 * - Second-place finishers are drawn randomly but cannot face their group winner in round 1
 * - Uses bye positions for non-power-of-2 player counts
 */
class JOOLABracketService
{
    /**
     * Get advancing players from all groups
     * 
     * @param Competition $competition
     * @return Collection Collection of player IDs grouped by placement
     */
    public function getAdvancingPlayers(Competition $competition): array
    {
        $groupWinners = [];
        $groupRunners = [];
        
        foreach ($competition->tournamentGroups()->orderBy('group_number')->get() as $group) {
            $standings = Standing::where('competition_id', $competition->id)
                ->where('tournament_group_id', $group->id)
                ->orderBy('points', 'desc')
                ->orderByRaw('(sets_won - sets_lost) desc')
                ->orderByRaw('(points_won - points_lost) desc')
                ->limit($competition->players_advancing_per_group ?? 2)
                ->pluck('player_id')
                ->toArray();
            
            if (count($standings) >= 1) {
                $groupWinners[] = [
                    'player_id' => $standings[0],
                    'group_number' => $group->group_number,
                ];
            }
            
            if (count($standings) >= 2) {
                $groupRunners[] = [
                    'player_id' => $standings[1],
                    'group_number' => $group->group_number,
                ];
            }
        }
        
        return [
            'winners' => $groupWinners,
            'runners' => $groupRunners,
        ];
    }
    
    /**
     * Generate JOOLA bracket
     * 
     * @param Competition $competition
     * @return array Bracket structure
     */
    public function generateBracket(Competition $competition): array
    {
        $advancing = $this->getAdvancingPlayers($competition);
        $winners = $advancing['winners'];
        $runners = $advancing['runners'];
        
        $totalPlayers = count($winners) + count($runners);
        
        if ($totalPlayers < 2) {
            return [];
        }
        
        // Calculate bracket size (next power of 2)
        $bracketSize = $this->getNextPowerOfTwo($totalPlayers);
        $byeCount = $bracketSize - $totalPlayers;
        
        // Initialize bracket positions
        $positions = array_fill(0, $bracketSize, null);
        
        // Step 1: Seed Group 1 and 2 winners (if they exist)
        if (count($winners) >= 1) {
            $positions[0] = $winners[0]['player_id']; // Position A (top)
        }
        
        if (count($winners) >= 2) {
            $positions[$bracketSize - 1] = $winners[1]['player_id']; // Position B (bottom)
        }
        
        // Step 2: Randomly distribute other group winners
        $remainingWinners = array_slice($winners, 2);
        shuffle($remainingWinners);
        
        $availablePositions = [];
        for ($i = 1; $i < $bracketSize - 1; $i++) {
            if ($positions[$i] === null) {
                $availablePositions[] = $i;
            }
        }
        
        foreach ($remainingWinners as $winner) {
            if (empty($availablePositions)) break;
            $pos = array_shift($availablePositions);
            $positions[$pos] = $winner['player_id'];
        }
        
        // Step 3: Distribute runners (cannot face their group winner in round 1)
        shuffle($runners);
        
        foreach ($runners as $runner) {
            // Find group winner position
            $groupWinnerPos = null;
            foreach ($winners as $idx => $winner) {
                if ($winner['group_number'] === $runner['group_number']) {
                    $groupWinnerPos = array_search($winner['player_id'], $positions);
                    break;
                }
            }
            
            // Find opponent position in first round
            if ($groupWinnerPos !== null) {
                $opponentPos = $this->getFirstRoundOpponent($groupWinnerPos, $bracketSize);
            }
            
            // Place runner in a position that doesn't face their group winner
            $placed = false;
            foreach ($availablePositions as $key => $pos) {
                if ($groupWinnerPos === null || $pos !== $opponentPos) {
                    $positions[$pos] = $runner['player_id'];
                    unset($availablePositions[$key]);
                    $placed = true;
                    break;
                }
            }
            
            // If couldn't place with restriction, place anywhere available
            if (!$placed && !empty($availablePositions)) {
                $pos = array_shift($availablePositions);
                $positions[$pos] = $runner['player_id'];
            }
        }
        
        // Step 4: Assign bye positions to seeds
        if ($byeCount > 0) {
            $byesAssigned = 0;
            
            // Give byes to top seeds first
            for ($i = 0; $i < $bracketSize && $byesAssigned < $byeCount; $i++) {
                if ($positions[$i] === null) {
                    $positions[$i] = 'BYE';
                    $byesAssigned++;
                }
            }
        }
        
        return [
            'positions' => $positions,
            'bracket_size' => $bracketSize,
            'bye_count' => $byeCount,
        ];
    }
    
    /**
     * Generate matches from bracket
     * 
     * @param Competition $competition
     * @param array $bracket
     * @return void
     */
    public function generateMatchesFromBracket(Competition $competition, array $bracket): void
    {
        $positions = $bracket['positions'];
        $bracketSize = $bracket['bracket_size'];
        
        // Generate first round matches
        $matchOrder = 1;
        for ($i = 0; $i < $bracketSize / 2; $i++) {
            $homePos = $i;
            $awayPos = $bracketSize - 1 - $i;
            
            $homePlayer = $positions[$homePos] !== 'BYE' ? $positions[$homePos] : null;
            $awayPlayer = $positions[$awayPos] !== 'BYE' ? $positions[$awayPos] : null;
            
            // Skip if both are bye
            if ($homePlayer === null && $awayPlayer === null) {
                continue;
            }
            
            $isBye = ($homePlayer === null || $awayPlayer === null);
            
            CompetitionMatch::create([
                'competition_id' => $competition->id,
                'home_player_id' => $homePlayer,
                'away_player_id' => $awayPlayer,
                'phase' => 'knockout',
                'round_number' => 1,
                'match_order' => $matchOrder++,
                'status' => $isBye ? 'completed' : 'scheduled',
                'is_bye' => $isBye,
                'home_score' => $isBye ? 1 : 0,
                'away_score' => 0,
                'played_at' => $isBye ? now() : null,
            ]);
        }
    }
    
    /**
     * Get next power of 2
     * 
     * @param int $n
     * @return int
     */
    private function getNextPowerOfTwo(int $n): int
    {
        $power = 1;
        while ($power < $n) {
            $power *= 2;
        }
        return $power;
    }
    
    /**
     * Get first round opponent position
     * 
     * @param int $position
     * @param int $bracketSize
     * @return int
     */
    private function getFirstRoundOpponent(int $position, int $bracketSize): int
    {
        return $bracketSize - 1 - $position;
    }
}
