<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Standing;
use Illuminate\Support\Facades\DB;

/**
 * Standings math for individual/team leagues (Competition type='league',
 * keyed by competition_id + player_id/team_id - not tournament-group
 * standings, which have their own service).
 *
 * Standings are recomputed from scratch from every completed match rather
 * than patched with incremental +/- deltas: every place that completes,
 * edits, resets, or deletes a league match just calls rebuildForCompetition()
 * afterwards, so there's no separate ledger that can drift out of sync with
 * the match data (the source of three separate "table wasn't updated"
 * bugs fixed the same day this was introduced).
 */
class LeagueStandingsService
{
    /**
     * Recompute this competition's entire standings table from its completed
     * matches. Safe to call after any match create/update/delete.
     */
    public function rebuildForCompetition(Competition $competition): void
    {
        DB::transaction(function () use ($competition) {
            Standing::where('competition_id', $competition->id)
                ->update(['played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'points' => 0]);

            $competition->matches()
                ->where('status', 'completed')
                ->get()
                ->each(fn (CompetitionMatch $match) => $this->applyMatch($competition, $match));
        });
    }

    private function applyMatch(Competition $competition, CompetitionMatch $match): void
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

        $homeStanding->increment('played');
        $awayStanding->increment('played');

        $pointsForWin = $competition->points_for_win ?? 2;
        $pointsForDraw = $competition->points_for_draw ?? 1;
        $pointsForLoss = $competition->points_for_loss ?? 0;

        if ($match->home_score > $match->away_score) {
            $homeStanding->increment('won');
            $homeStanding->increment('points', $pointsForWin);
            $awayStanding->increment('lost');
            $awayStanding->increment('points', $pointsForLoss);
        } elseif ($match->away_score > $match->home_score) {
            $awayStanding->increment('won');
            $awayStanding->increment('points', $pointsForWin);
            $homeStanding->increment('lost');
            $homeStanding->increment('points', $pointsForLoss);
        } else {
            $homeStanding->increment('drawn');
            $awayStanding->increment('drawn');
            $homeStanding->increment('points', $pointsForDraw);
            $awayStanding->increment('points', $pointsForDraw);
        }
    }
}
