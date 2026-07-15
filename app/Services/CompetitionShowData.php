<?php

namespace App\Services;

use App\Models\Competition;

/**
 * Shared eager-loading + tournament seeding-map logic behind the
 * competition "show" page, reused by both the anonymous spectator view
 * (PublicMatchController::showLeague) and the authenticated player view
 * (PlayerLeagueController::show) so the two don't drift.
 */
class CompetitionShowData
{
    public static function load(Competition $competition): array
    {
        $competition->load([
            'organization',
            'sport',
            'standings' => function ($query) {
                $query->orderBy('position', 'asc');
            },
            'standings.team',
            'standings.player',
            'tournamentGroups', // Load tournament groups for standings
            'tournamentGroups.standings.player',
        ]);

        // Create player seeding maps for tournaments
        $playerGroupSeeding = []; // For knockout phase: "A-1" format
        $playerPositionSeeding = []; // For group phase: just position number
        if ($competition->type === 'tournament') {
            foreach ($competition->tournamentGroups as $group) {
                foreach ($group->player_ids ?? [] as $index => $playerId) {
                    $playerGroupSeeding[$playerId] = $group->name . '-' . ($index + 1);
                    $playerPositionSeeding[$playerId] = $index + 1;
                }
            }
        }

        // Load matches based on competition type
        if ($competition->type === 'league') {
            $competition->load(['standings.team', 'standings.player']);
            if ($competition->is_team_based) {
                $competition->load([
                    'teamMatches' => function ($query) {
                        $query->orderBy('round')
                              ->orderBy('scheduled_at', 'desc')
                              ->with(['homeTeam', 'awayTeam']);
                    }
                ]);
            } else {
                $competition->load([
                    'leagueMatches' => function ($query) {
                        $query->orderBy('round')
                              ->orderBy('scheduled_at', 'desc')
                              ->with(['homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer']);
                    }
                ]);
            }
        } else {
            // For tournaments, matches are CompetitionMatch
            $competition->load([
                'matches' => function ($query) {
                    $query->orderBy('round_number')
                          ->orderBy('match_order')
                          ->with(['homePlayer', 'awayPlayer', 'tournamentGroup']);
                }
            ]);
        }

        return compact('playerGroupSeeding', 'playerPositionSeeding');
    }
}
