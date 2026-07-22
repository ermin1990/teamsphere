<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Standing;
use Illuminate\Support\Collection;
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
 *
 * Ranking (the `position` column) breaks ties in this order:
 *   1. points
 *   2. head-to-head result, when exactly two entries are tied
 *   3. set difference (sets_won - sets_lost)
 *   4. game/point difference (points_won - points_lost)
 *   5. sets_won
 * Views should sort by `position`, not `points`, to see the tiebreak applied.
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
            $this->ensureStandingRowsForParticipants($competition);

            Standing::where('competition_id', $competition->id)
                ->whereNull('tournament_group_id')
                ->update([
                    'played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'points' => 0,
                    'sets_won' => 0, 'sets_lost' => 0, 'points_won' => 0, 'points_lost' => 0,
                ]);

            $matches = $competition->matches()
                ->where('status', 'completed')
                ->get();

            $matches->each(fn (CompetitionMatch $match) => $this->applyMatch($competition, $match));

            $this->assignPositions($competition, $matches);
        });
    }

    /**
     * A registered participant who never plays a match otherwise never gets
     * a Standing row (applyMatch only creates one on demand), so they'd be
     * missing from the table entirely instead of showing up with all-zero
     * stats - this seeds a zeroed row for every current participant up
     * front so the table always lists everyone registered.
     */
    private function ensureStandingRowsForParticipants(Competition $competition): void
    {
        $isTeamBased = $competition->is_team_based;
        $participantIds = $isTeamBased
            ? $competition->teams()->pluck('id')
            : $competition->players()->pluck('players.id');

        foreach ($participantIds as $id) {
            Standing::firstOrCreate([
                'competition_id' => $competition->id,
                'tournament_group_id' => null,
                'player_id' => $isTeamBased ? null : $id,
                'team_id' => $isTeamBased ? $id : null,
            ], ['played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'points' => 0, 'position' => 999]);
        }
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

        $pointsForWin = $competition->points_for_win ?? 2;
        $pointsForDraw = $competition->points_for_draw ?? 1;
        $pointsForLoss = $competition->points_for_loss ?? 0;

        // Forfeit points/played-credit are organizer-configurable per
        // competition (see Competition::forfeitWinnerPoints()/
        // forfeitLoserPoints() and the forfeit_*_counts_as_played flags) -
        // there's no fixed convention, since different leagues want
        // different treatment of a no-show.
        if ($match->forfeited_by === 'home' || $match->forfeited_by === 'away') {
            $winnerStanding = $match->forfeited_by === 'home' ? $awayStanding : $homeStanding;
            $loserStanding = $match->forfeited_by === 'home' ? $homeStanding : $awayStanding;

            if ($competition->forfeit_winner_counts_as_played) {
                $winnerStanding->increment('played');
                $winnerStanding->increment('won');
            }
            $winnerStanding->increment('points', $competition->forfeitWinnerPoints());

            // The forfeiting side only gets points if the organizer explicitly
            // configured some (forfeit_loser_points set), or if the match is
            // being counted as a played/lost match for them (in which case
            // it falls back to the normal loss points) - by default (not
            // counted as played, no explicit override) they get nothing,
            // matching how this was always handled before it was
            // configurable.
            if ($competition->forfeit_loser_counts_as_played) {
                $loserStanding->increment('played');
                $loserStanding->increment('lost');
                $loserStanding->increment('points', $competition->forfeitLoserPoints());
            } elseif (!is_null($competition->forfeit_loser_points)) {
                $loserStanding->increment('points', $competition->forfeit_loser_points);
            }
        } else {
            $homeStanding->increment('played');
            $awayStanding->increment('played');

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

        [$homeSets, $awaySets, $homeGames, $awayGames] = $this->tallySets($match->sets);

        $homeStanding->increment('sets_won', $homeSets);
        $homeStanding->increment('sets_lost', $awaySets);
        $homeStanding->increment('points_won', $homeGames);
        $homeStanding->increment('points_lost', $awayGames);

        $awayStanding->increment('sets_won', $awaySets);
        $awayStanding->increment('sets_lost', $homeSets);
        $awayStanding->increment('points_won', $awayGames);
        $awayStanding->increment('points_lost', $homeGames);
    }

    /**
     * Sum sets won/lost and games/points won/lost from a match's `sets` JSON.
     * Supports both the indexed [[home, away], ...] shape and the
     * associative [{home, away}, ...] / [{home_score, away_score}, ...] shapes
     * used elsewhere in the app.
     */
    private function tallySets(?array $sets): array
    {
        if (empty($sets)) {
            return [0, 0, 0, 0];
        }

        $homeSets = 0;
        $awaySets = 0;
        $homeGames = 0;
        $awayGames = 0;

        foreach ($sets as $set) {
            $home = $set['home'] ?? $set['home_score'] ?? $set['p1'] ?? $set[0] ?? null;
            $away = $set['away'] ?? $set['away_score'] ?? $set['p2'] ?? $set[1] ?? null;

            if ($home === null || $away === null) {
                continue;
            }

            $home = (int) $home;
            $away = (int) $away;

            $homeGames += $home;
            $awayGames += $away;

            if ($home > $away) {
                $homeSets++;
            } elseif ($away > $home) {
                $awaySets++;
            }
        }

        return [$homeSets, $awaySets, $homeGames, $awayGames];
    }

    /**
     * Rank standings and persist `position`. An organizer-set `manual_order`
     * always wins and is placed first (in that order); everything without
     * one is ranked after it using the automatic rules: points, then
     * head-to-head result for a straight two-way tie (a three-way tie can be
     * non-transitive, so it falls through to the statistical tiebreakers
     * below), then set difference, game/point difference, and sets won.
     */
    private function assignPositions(Competition $competition, Collection $matches): void
    {
        $headToHead = $this->buildHeadToHeadMap($competition, $matches);

        $standings = Standing::where('competition_id', $competition->id)
            ->whereNull('tournament_group_id')
            ->get()
            ->all();

        usort($standings, function (Standing $a, Standing $b) use ($headToHead) {
            if ($a->manual_order !== null || $b->manual_order !== null) {
                if ($a->manual_order === null) {
                    return 1;
                }
                if ($b->manual_order === null) {
                    return -1;
                }
                return $a->manual_order <=> $b->manual_order;
            }

            if ($a->points !== $b->points) {
                return $b->points <=> $a->points;
            }

            $idA = $a->team_id ?? $a->player_id;
            $idB = $b->team_id ?? $b->player_id;

            if ($idA !== null && $idB !== null) {
                $winner = $headToHead[$this->pairKey($idA, $idB)] ?? null;
                if ($winner === $idA) {
                    return -1;
                }
                if ($winner === $idB) {
                    return 1;
                }
            }

            $setDiffA = $a->sets_won - $a->sets_lost;
            $setDiffB = $b->sets_won - $b->sets_lost;
            if ($setDiffA !== $setDiffB) {
                return $setDiffB <=> $setDiffA;
            }

            $gameDiffA = $a->points_won - $a->points_lost;
            $gameDiffB = $b->points_won - $b->points_lost;
            if ($gameDiffA !== $gameDiffB) {
                return $gameDiffB <=> $gameDiffA;
            }

            return $b->sets_won <=> $a->sets_won;
        });

        foreach ($standings as $index => $standing) {
            $standing->position = $index + 1;
            $standing->save();
        }
    }

    /**
     * Map "smallerId|largerId" => winning participant id, from completed
     * matches between two participants. Only decisive (non-drawn) results
     * count; if the same pair met more than once, the later match wins.
     */
    private function buildHeadToHeadMap(Competition $competition, Collection $matches): array
    {
        $isTeamBased = $competition->is_team_based;
        $map = [];

        foreach ($matches as $match) {
            $homeId = $isTeamBased ? $match->home_team_id : $match->home_player_id;
            $awayId = $isTeamBased ? $match->away_team_id : $match->away_player_id;

            if (!$homeId || !$awayId || $match->home_score === $match->away_score) {
                continue;
            }

            $winner = $match->home_score > $match->away_score ? $homeId : $awayId;
            $map[$this->pairKey($homeId, $awayId)] = $winner;
        }

        return $map;
    }

    private function pairKey(int $a, int $b): string
    {
        return $a < $b ? "{$a}|{$b}" : "{$b}|{$a}";
    }
}
