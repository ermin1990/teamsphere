<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\Standing;
use App\Models\CompetitionMatch;

class TournamentGroupService
{
    /**
     * Generate tournament groups for a competition.
     */
    public function generateGroups(Competition $competition): array
    {
        $players = $competition->players()->get();
        $groupCount = $competition->group_count;
        $playersPerGroup = $competition->players_per_group;

        $groups = [];
        for ($i = 0; $i < $groupCount; $i++) {
            $groups[] = [
                'name' => 'Grupa ' . chr(65 + $i), // A, B, C, etc.
                'player_ids' => [],
                'standings' => [],
            ];
        }

        // Assign players to groups in alternating order to balance groups
        $playerIndex = 0;
        foreach ($players as $player) {
            $groupIndex = $playerIndex % $groupCount;
            $groups[$groupIndex]['player_ids'][] = $player->id;
            $playerIndex++;
        }

        // Initialize standings for each group, preserving the order (seeding)
        foreach ($groups as &$group) {
            $group['standings'] = $this->initializeGroupStandings($group['player_ids']);
        }

        return $groups;
    }

    /**
     * Initialize standings for a group.
     */
    public function initializeGroupStandings(array $playerIds): array
    {
        $standings = [];
        foreach ($playerIds as $playerId) {
            $standings[] = [
                'player_id' => $playerId,
                'played' => 0,
                'won' => 0,
                'lost' => 0,
                'points' => 0,
                'sets_won' => 0,
                'sets_lost' => 0,
                'points_scored' => 0,
                'points_conceded' => 0,
            ];
        }
        return $standings;
    }

    /**
     * Generate matches for all tournament groups.
     */
    public function generateGroupMatches(Competition $competition): void
    {
        if (!$competition->isTournament()) {
            return;
        }

        $groups = $competition->tournamentGroups;

        foreach ($groups as $group) {
            $this->generateMatchesForGroup($competition, $group);
        }
    }

    /**
     * Generate round-robin matches for a specific group.
     */
    private function generateMatchesForGroup(Competition $competition, $group): void
    {
        $playerIds = $group->player_ids;
        $playerCount = count($playerIds);
        $rounds = $competition->group_rounds ?? 1;

        // Generate matches for each round
        for ($round = 1; $round <= $rounds; $round++) {
            if ($round == 1) {
                // First round: normal round-robin
                for ($i = 0; $i < $playerCount; $i++) {
                    for ($j = $i + 1; $j < $playerCount; $j++) {
                        CompetitionMatch::create([
                            'competition_id' => $competition->id,
                            'tournament_group_id' => $group->id,
                            'home_player_id' => $playerIds[$i],
                            'away_player_id' => $playerIds[$j],
                            'status' => 'scheduled',
                            'scheduled_at' => now(),
                            'phase' => 'group',
                            'round_number' => $round,
                        ]);
                    }
                }
            } else {
                // Subsequent rounds: reverse home/away
                for ($i = 0; $i < $playerCount; $i++) {
                    for ($j = $i + 1; $j < $playerCount; $j++) {
                        CompetitionMatch::create([
                            'competition_id' => $competition->id,
                            'tournament_group_id' => $group->id,
                            'home_player_id' => $playerIds[$j], // Swapped
                            'away_player_id' => $playerIds[$i], // Swapped
                            'status' => 'scheduled',
                            'scheduled_at' => now(),
                            'phase' => 'group',
                            'round_number' => $round,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Recalculate standings for a group after a match.
     */
    public function recalculateGroupStandings($group): void
    {
        $competition = $group->competition;

        \Log::info('Recalculating standings for group', [
            'group_id' => $group->id,
            'competition_id' => $competition->id,
        ]);

        // Get all completed matches for this group (do not rely on TournamentGroup::matches() which filters by phase)
        $completedMatches = CompetitionMatch::where('tournament_group_id', $group->id)
            ->where('competition_id', $competition->id)
            ->whereIn('status', ['completed', 'forfeited'])
            ->get();

        \Log::info('Found completed matches', [
            'count' => $completedMatches->count(),
        ]);

        // Calculate standings from scratch
        $standingsData = $this->calculateStandingsFromMatches($group, $completedMatches, $competition);

        \Log::info('Calculated standings data', [
            'data' => $standingsData,
        ]);

        // Remove any existing standings for this group and recreate from scratch
        $deleted = Standing::where('competition_id', $competition->id)
            ->where('tournament_group_id', $group->id)
            ->delete();

        \Log::info('Deleted old standings', [
            'count' => $deleted,
        ]);

        // Create fresh Standing records
        foreach ($standingsData as $playerId => $data) {
            try {
                $standing = Standing::create(array_merge([
                    'competition_id' => $competition->id,
                    'tournament_group_id' => $group->id,
                    'player_id' => $playerId,
                ], $data));
                \Log::info('Created standing', [
                    'player_id' => $playerId,
                    'standing_id' => $standing->id,
                    'data' => $data,
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // Ignore duplicate entry errors (race condition)
                if ($e->getCode() !== '23000') {
                    throw $e;
                }
                \Log::warning('Duplicate standing entry', ['player_id' => $playerId]);
            }
        }

        // Recalculate positions
        $ordered = Standing::where('competition_id', $competition->id)
            ->where('tournament_group_id', $group->id)
            ->orderByDesc('points')
            ->orderByRaw('(sets_won - sets_lost) DESC')
            ->orderByRaw('(points_won - points_lost) DESC')
            ->orderByDesc('sets_won')
            ->orderByDesc('won')
            ->orderBy('id')
            ->get();

        \Log::info('Ordered standings before position update', [
            'standings' => $ordered->map(fn($s) => [
                'id' => $s->id,
                'player_id' => $s->player_id,
                'played' => $s->played,
                'points' => $s->points,
            ])->toArray(),
        ]);

        foreach ($ordered as $index => $standing) {
            $standing->position = $index + 1;
            $standing->save();
        }

        \Log::info('Finished recalculating - checking final standings in DB');
        
        $final = Standing::where('competition_id', $competition->id)
            ->where('tournament_group_id', $group->id)
            ->get(['id', 'player_id', 'played', 'points']);
        
        \Log::info('Final standings after recalculation', [
            'standings' => $final->toArray(),
        ]);

        // Check if group is completed after recalculating standings
        $group->checkGroupCompletion();
    }

    /**
     * Calculate standings data from matches.
     */
    private function calculateStandingsFromMatches($group, $matches, $competition): array
    {
        $standings = [];

        // Initialize standings for all players in the group
        foreach ($group->player_ids ?? [] as $playerId) {
            $standings[$playerId] = [
                'played' => 0,
                'won' => 0,
                'lost' => 0,
                'points' => 0,
                'sets_won' => 0,
                'sets_lost' => 0,
                'points_won' => 0,
                'points_lost' => 0,
            ];
        }

        // Calculate standings based on all completed matches
        foreach ($matches as $match) {
            $homeId = $match->home_player_id;
            $awayId = $match->away_player_id;

            if (!isset($standings[$homeId]) || !isset($standings[$awayId])) {
                continue; // Skip if player not in group
            }

            // Update played games
            $standings[$homeId]['played']++;
            $standings[$awayId]['played']++;

            // Determine winner and update stats
            if ($match->home_score > $match->away_score) {
                $standings[$homeId]['won']++;
                $standings[$awayId]['lost']++;
                $standings[$homeId]['points'] += $competition->points_for_win ?? 3;
            } elseif ($match->away_score > $match->home_score) {
                $standings[$awayId]['won']++;
                $standings[$homeId]['lost']++;
                $standings[$awayId]['points'] += $competition->points_for_win ?? 3;
            } else {
                // Draw
                $standings[$homeId]['points'] += $competition->points_for_draw ?? 1;
                $standings[$awayId]['points'] += $competition->points_for_draw ?? 1;
            }

            // Update sets
            $standings[$homeId]['sets_won'] += $match->home_score;
            $standings[$homeId]['sets_lost'] += $match->away_score;
            $standings[$awayId]['sets_won'] += $match->away_score;
            $standings[$awayId]['sets_lost'] += $match->home_score;

            // Calculate points from sets (sum of all points won/lost in each set)
            $homePointsTotal = 0;
            $awayPointsTotal = 0;
            
            \Log::info('Processing match sets', [
                'match_id' => $match->id,
                'sets' => $match->sets,
                'sets_type' => gettype($match->sets),
                'is_array' => is_array($match->sets),
            ]);
            
            if (!empty($match->sets) && is_array($match->sets)) {
                foreach ($match->sets as $set) {
                    // Support both formats: home/away and home_score/away_score
                    $homePointsTotal += $set['home'] ?? $set['home_score'] ?? 0;
                    $awayPointsTotal += $set['away'] ?? $set['away_score'] ?? 0;
                }
            }
            
            \Log::info('Calculated points from sets', [
                'match_id' => $match->id,
                'home_points' => $homePointsTotal,
                'away_points' => $awayPointsTotal,
            ]);
            
            $standings[$homeId]['points_won'] += $homePointsTotal;
            $standings[$homeId]['points_lost'] += $awayPointsTotal;
            $standings[$awayId]['points_won'] += $awayPointsTotal;
            $standings[$awayId]['points_lost'] += $homePointsTotal;
        }

        return $standings;
    }



    /**
     * Check if all groups are completed.
     */
    public function areAllGroupsCompleted(Competition $competition): bool
    {
        return $competition->tournamentGroups()->where('is_completed', false)->count() === 0;
    }
}