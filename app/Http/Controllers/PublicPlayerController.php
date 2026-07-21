<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\Standing;

class PublicPlayerController extends Controller
{
    /**
     * Public player profile: leagues they play in, recent results, and a
     * few headline stats. Only public leagues are shown - a player's
     * private/organizer-only competitions stay invisible here.
     */
    public function show(Player $player)
    {
        $player->load('organization');

        $leagues = $player->leagues()
            ->where('is_public', true)
            ->with(['season', 'organization'])
            ->get();

        $leagueIds = $leagues->pluck('id');

        $standings = Standing::where('player_id', $player->id)
            ->whereIn('competition_id', $leagueIds)
            ->get()
            ->keyBy('competition_id');

        $matches = $player->homeMatches()
            ->whereIn('competition_id', $leagueIds)
            ->where('status', 'completed')
            ->with(['homePlayer', 'awayPlayer', 'competition'])
            ->get()
            ->merge(
                $player->awayMatches()
                    ->whereIn('competition_id', $leagueIds)
                    ->where('status', 'completed')
                    ->with(['homePlayer', 'awayPlayer', 'competition'])
                    ->get()
            )
            // No played_at is reliably set on historical/imported matches, so
            // round number (then id) is the best available proxy for "most
            // recently played" ordering.
            ->sortByDesc(fn ($m) => [$m->played_at?->timestamp ?? 0, $m->round ?? 0, $m->id])
            ->values();

        $stats = $this->computeStats($player, $matches);

        return view('public.players.show', [
            'player' => $player,
            'leagues' => $leagues,
            'standings' => $standings,
            'recentMatches' => $matches->take(15),
            'stats' => $stats,
        ]);
    }

    private function computeStats(Player $player, $matches): array
    {
        $played = 0;
        $won = 0;
        $setsWon = 0;
        $setsLost = 0;
        $streakType = null;
        $streakCount = 0;
        $streakBroken = false;

        foreach ($matches as $match) {
            $isHome = $match->home_player_id === $player->id;
            $forfeitedSide = $isHome ? 'home' : 'away';

            // A walkover the player forfeited doesn't count as a played
            // match for them at all - matches the league table convention
            // used across the app (LeagueStandingsService).
            if ($match->forfeited_by === $forfeitedSide) {
                continue;
            }

            $playerScore = $isHome ? $match->home_score : $match->away_score;
            $opponentScore = $isHome ? $match->away_score : $match->home_score;
            $isWin = $playerScore > $opponentScore;

            $played++;
            if ($isWin) {
                $won++;
            }

            if (!$match->forfeited_by) {
                foreach ($match->sets ?? [] as $set) {
                    $h = (int) ($set['home'] ?? 0);
                    $a = (int) ($set['away'] ?? 0);
                    $playerSets = $isHome ? $h : $a;
                    $opponentSets = $isHome ? $a : $h;
                    if ($playerSets > $opponentSets) {
                        $setsWon++;
                    } elseif ($opponentSets > $playerSets) {
                        $setsLost++;
                    }
                }
            }

            if (!$streakBroken) {
                if ($streakType === null) {
                    $streakType = $isWin ? 'W' : 'L';
                    $streakCount = 1;
                } elseif ($streakType === ($isWin ? 'W' : 'L')) {
                    $streakCount++;
                } else {
                    $streakBroken = true;
                }
            }
        }

        return [
            'played' => $played,
            'won' => $won,
            'lost' => $played - $won,
            'winRate' => $played > 0 ? round($won / $played * 100) : 0,
            'setsWon' => $setsWon,
            'setsLost' => $setsLost,
            'streakType' => $streakType,
            'streakCount' => $streakCount,
        ];
    }
}
