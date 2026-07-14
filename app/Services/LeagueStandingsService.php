<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Standing;

/**
 * Standings math for individual/team leagues (Competition type='league',
 * keyed by competition_id + player_id/team_id - not tournament-group
 * standings, which have their own service). Extracted so the three places
 * that complete a league match (organizer quick-result, live-score forced
 * finish, player self-entry) share one implementation instead of drifting.
 */
class LeagueStandingsService
{
    /**
     * Apply a completed match's result to standings. Idempotent per match is
     * NOT enforced here (matches quick-result's existing "mark complete and
     * increment" behaviour) - re-submitting a result will double count unless
     * the caller reverses the previous result first (see reverseForMatch()).
     */
    public function applyForMatch(Competition $competition, CompetitionMatch $match): void
    {
        $this->applyDelta($competition, $match, $match->home_score, $match->away_score, 1);
    }

    /**
     * Undoes a previous applyForMatch() call - used when a completed match's
     * result is edited, so re-saving a corrected score doesn't double-count
     * the match's original result.
     */
    public function reverseForMatch(Competition $competition, CompetitionMatch $match, int $previousHomeScore, int $previousAwayScore): void
    {
        $this->applyDelta($competition, $match, $previousHomeScore, $previousAwayScore, -1);
    }

    private function applyDelta(Competition $competition, CompetitionMatch $match, int $homeScore, int $awayScore, int $direction): void
    {
        $isTeamBased = $competition->is_team_based;
        $homeId = $isTeamBased ? $match->home_team_id : $match->home_player_id;
        $awayId = $isTeamBased ? $match->away_team_id : $match->away_player_id;

        $homeStanding = Standing::firstOrCreate([
            'competition_id' => $competition->id,
            'player_id' => $isTeamBased ? null : $homeId,
            'team_id' => $isTeamBased ? $homeId : null,
        ], ['played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'points' => 0, 'position' => 999]);

        $awayStanding = Standing::firstOrCreate([
            'competition_id' => $competition->id,
            'player_id' => $isTeamBased ? null : $awayId,
            'team_id' => $isTeamBased ? $awayId : null,
        ], ['played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'points' => 0, 'position' => 999]);

        $homeStanding->increment('played', $direction);
        $awayStanding->increment('played', $direction);

        $pointsForWin = $competition->points_for_win ?? 2;
        $pointsForDraw = $competition->points_for_draw ?? 1;
        $pointsForLoss = $competition->points_for_loss ?? 0;

        if ($homeScore > $awayScore) {
            $homeStanding->increment('won', $direction);
            $homeStanding->increment('points', $pointsForWin * $direction);
            $awayStanding->increment('lost', $direction);
            $awayStanding->increment('points', $pointsForLoss * $direction);
        } elseif ($awayScore > $homeScore) {
            $awayStanding->increment('won', $direction);
            $awayStanding->increment('points', $pointsForWin * $direction);
            $homeStanding->increment('lost', $direction);
            $homeStanding->increment('points', $pointsForLoss * $direction);
        } else {
            $homeStanding->increment('drawn', $direction);
            $awayStanding->increment('drawn', $direction);
            $homeStanding->increment('points', $pointsForDraw * $direction);
            $awayStanding->increment('points', $pointsForDraw * $direction);
        }
    }
}
