<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\TournamentGroup;

/**
 * Berger Schedule Service
 * 
 * Generates round-robin matches using the Berger system (circle method)
 * where each player plays every other player exactly once.
 */
class BergerScheduleService
{
    /**
     * Generate Berger schedule for a group
     * 
     * @param Competition $competition
     * @param TournamentGroup $group
     * @return array Array of rounds with matches
     */
    public function generateSchedule(Competition $competition, TournamentGroup $group): array
    {
        $playerIds = $group->player_ids ?? [];
        $playerCount = count($playerIds);
        
        if ($playerCount < 2) {
            return [];
        }
        
        // Add a bye player if odd number of players
        $players = $playerIds;
        $hasBye = ($playerCount % 2 !== 0);
        
        if ($hasBye) {
            $players[] = null; // null represents BYE
        }
        
        $totalPlayers = count($players);
        $rounds = $totalPlayers - 1;
        $matchesPerRound = $totalPlayers / 2;
        
        $schedule = [];
        
        // Generate rounds using Berger algorithm
        for ($round = 0; $round < $rounds; $round++) {
            $roundMatches = [];
            
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $home = ($match === 0) ? 0 : ($round + $match) % ($totalPlayers - 1) + 1;
                $away = ($totalPlayers - 1) - $match + $round;
                
                if ($away >= $totalPlayers - 1) {
                    $away -= $totalPlayers - 1;
                }
                $away += 1;
                if ($away >= $totalPlayers) {
                    $away -= $totalPlayers - 1;
                }
                
                // Skip if this is a bye match (one player is null)
                if ($players[$home] === null || $players[$away] === null) {
                    continue;
                }
                
                $roundMatches[] = [
                    'home_player_id' => $players[$home],
                    'away_player_id' => $players[$away],
                    'round' => $round + 1,
                ];
            }
            
            if (!empty($roundMatches)) {
                $schedule[$round + 1] = $roundMatches;
            }
        }
        
        return $schedule;
    }
    
    /**
     * Generate matches for a tournament group using Berger system
     * 
     * @param Competition $competition
     * @param TournamentGroup $group
     * @return void
     */
    public function generateGroupMatches(Competition $competition, TournamentGroup $group): void
    {
        $schedule = $this->generateSchedule($competition, $group);
        
        foreach ($schedule as $round => $matches) {
            foreach ($matches as $matchData) {
                CompetitionMatch::create([
                    'competition_id' => $competition->id,
                    'tournament_group_id' => $group->id,
                    'home_player_id' => $matchData['home_player_id'],
                    'away_player_id' => $matchData['away_player_id'],
                    'round_number' => $matchData['round'],
                    'status' => 'scheduled',
                    'phase' => 'groups',
                ]);
            }
        }
    }
}
