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
        
        // Generate Berger rounds using circle rotation
        $bergerRounds = $this->generateBergerRounds($playerCount);
        
        $schedule = [];
        
        // Map positions to actual player IDs
        foreach ($bergerRounds as $roundNumber => $matches) {
            $roundMatches = [];
            
            foreach ($matches as $match) {
                $homePosition = $match['home'];
                $awayPosition = $match['away'];
                
                // Map positions to player IDs
                if (isset($playerIds[$homePosition]) && isset($playerIds[$awayPosition])) {
                    $roundMatches[] = [
                        'home_player_id' => $playerIds[$homePosition],
                        'away_player_id' => $playerIds[$awayPosition],
                        'round' => $roundNumber,
                    ];
                }
            }
            
            if (!empty($roundMatches)) {
                $schedule[$roundNumber] = $roundMatches;
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
        // If competition requests multiple rounds (e.g., double round-robin),
        // duplicate the schedule with swapped home/away for the additional legs.
        $originalRoundCount = count($schedule);
        if (($competition->group_rounds ?? 1) > 1 && $originalRoundCount > 0) {
            // Build second-leg rounds by swapping home/away and offsetting round numbers
            $secondLeg = [];
            foreach ($schedule as $round => $matches) {
                $newRound = $round + $originalRoundCount;
                $secondLeg[$newRound] = [];
                foreach ($matches as $m) {
                    $secondLeg[$newRound][] = [
                        'home_player_id' => $m['away_player_id'] ?? null,
                        'away_player_id' => $m['home_player_id'] ?? null,
                        'round' => $newRound,
                    ];
                }
            }

            // Merge second leg into schedule preserving round order
            $schedule = $schedule + $secondLeg;
        }
        
        foreach ($schedule as $round => $matches) {
            foreach ($matches as $matchData) {
                try {
                    CompetitionMatch::create([
                        'competition_id' => $competition->id,
                        'tournament_group_id' => $group->id,
                        'home_player_id' => $matchData['home_player_id'],
                        'away_player_id' => $matchData['away_player_id'],
                        'round' => $matchData['round'],
                        'round_number' => $matchData['round'],
                        'status' => 'scheduled',
                        'phase' => 'group',
                        'scheduled_at' => now(),
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // Ignore duplicate entry errors (already exists from previous generation)
                    if ($e->getCode() !== '23000') {
                        throw $e;
                    }
                }
            }
        }
    }
    
    /**
     * Generate Berger schedule using circle rotation method
     * First player stays fixed, others rotate
     */
    private function generateBergerRounds(int $playerCount): array
    {
        if ($playerCount < 2) {
            return [];
        }
        
        // Make even number of players
        $n = $playerCount;
        if ($n % 2 !== 0) {
            $n++;
        }
        
        // Initialize players 0 to n-1
        $players = array_values(range(0, $n - 1));
        $rounds = [];
        
        // Generate n-1 rounds
        for ($round = 0; $round < $n - 1; $round++) {
            $roundMatches = [];
            
            // First player plays with last player
            $home = $players[0];
            $away = $players[$n - 1];
            
            // Only add if both players are in range (not bye)
            if ($home < $playerCount && $away < $playerCount) {
                $roundMatches[] = ['home' => $home, 'away' => $away];
            }
            
            // Middle matches: pair from outside inward
            for ($i = 1; $i < $n / 2; $i++) {
                $home = $players[$i];
                $away = $players[$n - 1 - $i];
                
                // Only add if both players are in range
                if ($home < $playerCount && $away < $playerCount) {
                    $roundMatches[] = ['home' => $home, 'away' => $away];
                }
            }
            
            $rounds[$round + 1] = $roundMatches;
            
            // Rotate: keep first player fixed, rotate others
            if ($round < $n - 2) {
                $temp = $players[$n - 1];
                for ($i = $n - 1; $i > 1; $i--) {
                    $players[$i] = $players[$i - 1];
                }
                $players[1] = $temp;
            }
        }
        
        return $rounds;
    }

    /**
     * Get Berger tournament table templates for different participant counts
     */
    private function getBergerTables(): array
    {
        return [
            4 => [ // 3 or 4 participants
                [1, 4, 2, 3],
                [4, 3, 1, 2],
                [2, 4, 3, 1],
            ],
            6 => [ // 5 or 6 participants
                [1, 6, 2, 5, 3, 4],
                [6, 4, 5, 3, 1, 2],
                [2, 6, 3, 1, 4, 5],
                [6, 5, 1, 4, 2, 3],
                [3, 6, 4, 2, 5, 1],
            ],
            8 => [ // 7 or 8 participants
                [1, 8, 2, 7, 3, 6, 4, 5],
                [8, 5, 6, 4, 7, 3, 1, 2],
                [2, 8, 3, 1, 4, 7, 5, 6],
                [8, 6, 7, 5, 1, 4, 2, 3],
                [3, 8, 4, 2, 5, 1, 6, 7],
                [8, 7, 1, 6, 2, 5, 3, 4],
                [4, 8, 5, 3, 6, 2, 7, 1],
            ],
            10 => [ // 9 or 10 participants
                [1, 10, 2, 9, 3, 8, 4, 7, 5, 6],
                [10, 6, 7, 5, 8, 4, 9, 3, 1, 2],
                [2, 10, 3, 1, 4, 9, 5, 8, 6, 7],
                [10, 7, 8, 6, 9, 5, 1, 4, 2, 3],
                [3, 10, 4, 2, 5, 1, 6, 9, 7, 8],
                [10, 8, 9, 7, 1, 6, 2, 5, 3, 4],
                [4, 10, 5, 3, 6, 2, 7, 1, 8, 9],
                [10, 9, 1, 8, 2, 7, 3, 6, 4, 5],
                [5, 10, 6, 4, 7, 3, 8, 2, 9, 1],
            ],
            12 => [ // 11 or 12 participants
                [1, 12, 2, 11, 3, 10, 4, 9, 5, 8, 6, 7],
                [12, 7, 8, 6, 9, 5, 10, 4, 11, 3, 1, 2],
                [2, 12, 3, 1, 4, 11, 5, 10, 6, 9, 7, 8],
                [12, 8, 9, 7, 10, 6, 11, 5, 1, 4, 2, 3],
                [3, 12, 4, 2, 5, 1, 6, 11, 7, 10, 8, 9],
                [12, 9, 10, 8, 11, 7, 1, 6, 2, 5, 3, 4],
                [4, 12, 5, 3, 6, 2, 7, 1, 8, 11, 9, 10],
                [12, 10, 11, 9, 1, 8, 2, 7, 3, 6, 4, 5],
                [5, 12, 6, 4, 7, 3, 8, 2, 9, 1, 10, 11],
                [12, 11, 1, 10, 2, 9, 3, 8, 4, 7, 5, 6],
                [6, 12, 7, 5, 8, 4, 9, 3, 10, 2, 11, 1],
            ],
            14 => [ // 13 or 14 participants
                [1, 14, 2, 13, 3, 12, 4, 11, 5, 10, 6, 9, 7, 8],
                [14, 8, 9, 7, 10, 6, 11, 5, 12, 4, 13, 3, 1, 2],
                [2, 14, 3, 1, 4, 13, 5, 12, 6, 11, 7, 10, 8, 9],
                [14, 9, 10, 8, 11, 7, 12, 6, 13, 5, 1, 4, 2, 3],
                [3, 14, 4, 2, 5, 1, 6, 13, 7, 12, 8, 11, 9, 10],
                [14, 10, 11, 9, 12, 8, 13, 7, 1, 6, 2, 5, 3, 4],
                [4, 14, 5, 3, 6, 2, 7, 1, 8, 13, 9, 12, 10, 11],
                [14, 11, 12, 10, 13, 9, 1, 8, 2, 7, 3, 6, 4, 5],
                [5, 14, 6, 4, 7, 3, 8, 2, 9, 1, 10, 13, 11, 12],
                [14, 12, 13, 11, 1, 10, 2, 9, 3, 8, 4, 7, 5, 6],
                [6, 14, 7, 5, 8, 4, 9, 3, 10, 2, 11, 1, 12, 13],
                [14, 13, 1, 12, 2, 11, 3, 10, 4, 9, 5, 8, 6, 7],
                [7, 14, 8, 6, 9, 5, 10, 4, 11, 3, 12, 2, 13, 1],
            ],
            16 => [ // 15 or 16 participants
                [1, 16, 2, 15, 3, 14, 4, 13, 5, 12, 6, 11, 7, 10, 8, 9],
                [16, 9, 10, 8, 11, 7, 12, 6, 13, 5, 14, 4, 15, 3, 1, 2],
                [2, 16, 3, 1, 4, 15, 5, 14, 6, 13, 7, 12, 8, 11, 9, 10],
                [16, 10, 11, 9, 12, 8, 13, 7, 14, 6, 15, 5, 1, 4, 2, 3],
                [3, 16, 4, 2, 5, 1, 6, 15, 7, 14, 8, 13, 9, 12, 10, 11],
                [16, 11, 12, 10, 13, 9, 14, 8, 15, 7, 1, 6, 2, 5, 3, 4],
                [4, 16, 5, 3, 6, 2, 7, 1, 8, 15, 9, 14, 10, 13, 11, 12],
                [16, 12, 13, 11, 14, 10, 15, 9, 1, 8, 2, 7, 3, 6, 4, 5],
                [5, 16, 6, 4, 7, 3, 8, 2, 9, 1, 10, 15, 11, 14, 12, 13],
                [16, 13, 14, 12, 15, 11, 1, 10, 2, 9, 3, 8, 4, 7, 5, 6],
                [6, 16, 7, 5, 8, 4, 9, 3, 10, 2, 11, 1, 12, 15, 13, 14],
                [16, 14, 15, 13, 1, 12, 2, 11, 3, 10, 4, 9, 5, 8, 6, 7],
                [7, 16, 8, 6, 9, 5, 10, 4, 11, 3, 12, 2, 13, 1, 14, 15],
                [16, 15, 1, 14, 2, 13, 3, 12, 4, 11, 5, 10, 6, 9, 7, 8],
                [8, 16, 9, 7, 10, 6, 11, 5, 12, 4, 13, 3, 14, 2, 15, 1],
            ],
            18 => [ // 17 or 18 participants
                [1, 18, 2, 17, 3, 16, 4, 15, 5, 14, 6, 13, 7, 12, 8, 11, 9, 10],
                [18, 10, 11, 9, 12, 8, 13, 7, 14, 6, 15, 5, 16, 4, 17, 3, 1, 2],
                [2, 18, 3, 1, 4, 17, 5, 16, 6, 15, 7, 14, 8, 13, 9, 12, 10, 11],
                [18, 11, 12, 10, 13, 9, 14, 8, 15, 7, 16, 6, 17, 5, 1, 4, 2, 3],
                [3, 18, 4, 2, 5, 1, 6, 17, 7, 16, 8, 15, 9, 14, 10, 13, 11, 12],
                [18, 12, 13, 11, 14, 10, 15, 9, 16, 8, 17, 7, 1, 6, 2, 5, 3, 4],
                [4, 18, 5, 3, 6, 2, 7, 1, 8, 17, 9, 16, 10, 15, 11, 14, 12, 13],
                [18, 13, 14, 12, 15, 11, 16, 10, 17, 9, 1, 8, 2, 7, 3, 6, 4, 5],
                [5, 18, 6, 4, 7, 3, 8, 2, 9, 1, 10, 17, 11, 16, 12, 15, 13, 14],
                [18, 14, 15, 13, 16, 12, 17, 11, 1, 10, 2, 9, 3, 8, 4, 7, 5, 6],
                [6, 18, 7, 5, 8, 4, 9, 3, 10, 2, 11, 1, 12, 17, 13, 16, 14, 15],
                [18, 15, 16, 14, 17, 13, 1, 12, 2, 11, 3, 10, 4, 9, 5, 8, 6, 7],
                [7, 18, 8, 6, 9, 5, 10, 4, 11, 3, 12, 2, 13, 1, 14, 17, 15, 16],
                [18, 16, 17, 15, 1, 14, 2, 13, 3, 12, 4, 11, 5, 10, 6, 9, 7, 8],
                [8, 18, 9, 7, 10, 6, 11, 5, 12, 4, 13, 3, 14, 2, 15, 1, 16, 17],
                [18, 17, 1, 16, 2, 15, 3, 14, 4, 13, 5, 12, 6, 11, 7, 10, 8, 9],
                [9, 18, 10, 8, 11, 7, 12, 6, 13, 5, 14, 4, 15, 3, 16, 2, 17, 1],
            ],
            20 => [ // 19 or 20 participants
                [1, 20, 2, 19, 3, 18, 4, 17, 5, 16, 6, 15, 7, 14, 8, 13, 9, 12, 10, 11],
                [20, 11, 12, 10, 13, 9, 14, 8, 15, 7, 16, 6, 17, 5, 18, 4, 19, 3, 1, 2],
                [2, 20, 3, 1, 4, 19, 5, 18, 6, 17, 7, 16, 8, 15, 9, 14, 10, 13, 11, 12],
                [20, 12, 13, 11, 14, 10, 15, 9, 16, 8, 17, 7, 18, 6, 19, 5, 1, 4, 2, 3],
                [3, 20, 4, 2, 5, 1, 6, 19, 7, 18, 8, 17, 9, 16, 10, 15, 11, 14, 12, 13],
                [20, 13, 14, 12, 15, 11, 16, 10, 17, 9, 18, 8, 19, 7, 1, 6, 2, 5, 3, 4],
                [4, 20, 5, 3, 6, 2, 7, 1, 8, 19, 9, 18, 10, 17, 11, 16, 12, 15, 13, 14],
                [20, 14, 15, 13, 16, 12, 17, 11, 18, 10, 19, 9, 1, 8, 2, 7, 3, 6, 4, 5],
                [5, 20, 6, 4, 7, 3, 8, 2, 9, 1, 10, 19, 11, 18, 12, 17, 13, 16, 14, 15],
                [20, 15, 16, 14, 17, 13, 18, 12, 19, 11, 1, 10, 2, 9, 3, 8, 4, 7, 5, 6],
                [6, 20, 7, 5, 8, 4, 9, 3, 10, 2, 11, 1, 12, 19, 13, 18, 14, 17, 15, 16],
                [20, 16, 17, 15, 18, 14, 19, 13, 1, 12, 2, 11, 3, 10, 4, 9, 5, 8, 6, 7],
                [7, 20, 8, 6, 9, 5, 10, 4, 11, 3, 12, 2, 13, 1, 14, 19, 15, 18, 16, 17],
                [20, 17, 18, 16, 19, 15, 1, 14, 2, 13, 3, 12, 4, 11, 5, 10, 6, 9, 7, 8],
                [8, 20, 9, 7, 10, 6, 11, 5, 12, 4, 13, 3, 14, 2, 15, 1, 16, 19, 17, 18],
                [20, 18, 19, 17, 1, 16, 2, 15, 3, 14, 4, 13, 5, 12, 6, 11, 7, 10, 8, 9],
                [9, 20, 10, 8, 11, 7, 12, 6, 13, 5, 14, 4, 15, 3, 16, 2, 17, 1, 18, 19],
                [20, 19, 1, 18, 2, 17, 3, 16, 4, 15, 5, 14, 6, 13, 7, 12, 8, 11, 9, 10],
                [10, 20, 11, 9, 12, 8, 13, 7, 14, 6, 15, 5, 16, 4, 17, 3, 18, 2, 19, 1],
            ],
        ];
    }
    
    /**
     * Generate Berger schedule for a list of players (for leagues)
     * Uses circle rotation method where first player stays fixed
     * 
     * @param array $playerIds Array of player IDs
     * @return array Array of rounds with matches
     */
    public function generateLeagueSchedule(array $playerIds): array
    {
        $playerCount = count($playerIds);
        
        if ($playerCount < 2) {
            return [];
        }
        
        // Generate Berger rounds using circle rotation
        $bergerRounds = $this->generateBergerRounds($playerCount);
        
        $schedule = [];
        
        // Map positions to actual player IDs
        foreach ($bergerRounds as $roundNumber => $matches) {
            $roundMatches = [];
            
            foreach ($matches as $match) {
                $homePosition = $match['home'];
                $awayPosition = $match['away'];
                
                // Map positions to player IDs
                if (isset($playerIds[$homePosition]) && isset($playerIds[$awayPosition])) {
                    $roundMatches[] = [
                        'home_player_id' => $playerIds[$homePosition],
                        'away_player_id' => $playerIds[$awayPosition],
                        'round' => $roundNumber,
                    ];
                }
            }
            
            if (!empty($roundMatches)) {
                $schedule[$roundNumber] = $roundMatches;
            }
        }
        
        return $schedule;
    }
    
    /**
     * Generate matches for a league competition using Berger system
     * 
     * @param Competition $competition
     * @param array $playerIds Array of player IDs
     * @return void
     */
    public function generateLeagueMatches(Competition $competition, array $playerIds): void
    {
        $schedule = $this->generateLeagueSchedule($playerIds);
        
        foreach ($schedule as $round => $matches) {
            foreach ($matches as $matchData) {
                CompetitionMatch::create([
                    'competition_id' => $competition->id,
                    'home_player_id' => $matchData['home_player_id'],
                    'away_player_id' => $matchData['away_player_id'],
                    // Oba polja - 'round' je ono sto league-content.blade.php grupise/prikazuje,
                    // 'round_number' je ono sto tournament view-ovi koriste. Bez ovoga 'round'
                    // ostaje na default vrijednosti (1) za sve mecceve iz cijele lige.
                    'round' => $matchData['round'],
                    'round_number' => $matchData['round'],
                    'status' => 'scheduled',
                    'phase' => 'groups', // Leagues don't have phases, but we'll use 'groups' for consistency
                    'scheduled_at' => $competition->start_date?->addDays($round - 1),
                ]);
            }
        }
    }
}
