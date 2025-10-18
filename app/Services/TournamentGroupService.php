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
        $playerIndex = 0;

        for ($i = 0; $i < $groupCount; $i++) {
            $groupPlayers = [];

            for ($j = 0; $j < $playersPerGroup; $j++) {
                if ($playerIndex < $players->count()) {
                    $groupPlayers[] = $players[$playerIndex]->id;
                    $playerIndex++;
                }
            }

            $groups[] = [
                'name' => 'Grupa ' . chr(65 + $i), // A, B, C, etc.
                'player_ids' => $groupPlayers,
                'standings' => $this->initializeGroupStandings($groupPlayers),
            ];
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

        for ($i = 0; $i < $playerCount; $i++) {
            for ($j = $i + 1; $j < $playerCount; $j++) {
                CompetitionMatch::create([
                    'competition_id' => $competition->id,
                    'tournament_group_id' => $group->id,
                    'home_player_id' => $playerIds[$i],
                    'away_player_id' => $playerIds[$j],
                    'status' => 'scheduled',
                    'scheduled_at' => now(),
                ]);
            }
        }
    }

    /**
     * Recalculate standings for a group after a match.
     */
    public function recalculateGroupStandings($group): void
    {
        $competition = $group->competition;

        // Get all completed matches for this group (do not rely on TournamentGroup::matches() which filters by phase)
        $completedMatches = CompetitionMatch::where('tournament_group_id', $group->id)
            ->where('competition_id', $competition->id)
            ->whereIn('status', ['completed', 'forfeited'])
            ->get();

        // Calculate standings from scratch
        $standingsData = $this->calculateStandingsFromMatches($group, $completedMatches, $competition);

        // Remove any existing standings for this group and recreate from scratch
        Standing::where('competition_id', $competition->id)
            ->where('tournament_group_id', $group->id)
            ->delete();

        // Create fresh Standing records
        foreach ($standingsData as $playerId => $data) {
            Standing::create(array_merge([
                'competition_id' => $competition->id,
                'tournament_group_id' => $group->id,
                'player_id' => $playerId,
            ], $data));
        }

        // Recalculate positions
        $ordered = Standing::where('competition_id', $competition->id)
            ->where('tournament_group_id', $group->id)
            ->get()
            ->sortByDesc(function ($s) {
                return [$s->points ?? 0, ($s->sets_won - $s->sets_lost) ?? 0, $s->points_won ?? 0];
            })->values();

        foreach ($ordered as $index => $standing) {
            $standing->position = $index + 1;
            $standing->save();
        }
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
                $standings[$homeId]['points'] += $competition->points_for_win ?? 2;
            } elseif ($match->away_score > $match->home_score) {
                $standings[$awayId]['won']++;
                $standings[$homeId]['lost']++;
                $standings[$awayId]['points'] += $competition->points_for_win ?? 2;
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

            // Update points if available
            if ($match->home_points !== null) {
                $standings[$homeId]['points_won'] += $match->home_points;
                $standings[$homeId]['points_lost'] += $match->away_points ?? 0;
                $standings[$awayId]['points_won'] += $match->away_points ?? 0;
                $standings[$awayId]['points_lost'] += $match->home_points;
            }
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