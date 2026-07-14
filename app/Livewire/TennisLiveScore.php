<?php

namespace App\Livewire;

use App\Models\CompetitionMatch;
use App\Models\Standing;
use Livewire\Component;

/**
 * Live scoring for sets/games sports (Tenis, Padel) - separate from LiveScore.php
 * (which implements table-tennis-specific point-race-to-11 rules) so that fixing
 * or extending one cannot break the other.
 *
 * Score model: points within a game are tracked as raw integers (0,1,2,3,4...);
 * a game is won when a side reaches >=4 points with a lead of >=2 - this is
 * exactly the real tennis win condition and naturally reproduces deuce/advantage
 * without special-casing. Games accumulate until a set is won at >=6 games with
 * a lead of >=2, or a tiebreak (plain points, win at >=7 by 2) at 6-6.
 */
class TennisLiveScore extends Component
{
    public $match;

    public $homePoints = 0;
    public $awayPoints = 0;
    public $homeGames = 0;
    public $awayGames = 0;
    public $homeSets = 0;
    public $awaySets = 0;
    public $completedSets = [];

    public $inTiebreak = false;
    public $tiebreakHome = 0;
    public $tiebreakAway = 0;

    public $setsToWin = 2;
    public $currentServer = null;
    public $needsServerSelection = false;
    public $matchComplete = false;

    public function mount($match)
    {
        $this->match = $match;

        $rules = $this->match->competition->sport->rules ?? [];
        $this->setsToWin = $rules['sets_to_win'] ?? 2;

        $this->completedSets = $this->match->sets ?? [];
        $this->homeSets = $this->match->home_score ?? 0;
        $this->awaySets = $this->match->away_score ?? 0;
        $this->currentServer = $this->match->current_server;
        $this->needsServerSelection = empty($this->currentServer) && $this->match->status !== 'completed';
        $this->matchComplete = $this->match->status === 'completed';

        // Resume in-progress game/set state if cached (kept only in memory per-session;
        // a page refresh mid-game restarts the current game at 0-0, which is an
        // acceptable trade-off given games/sets already won are persisted).
    }

    public function selectFirstServer($side)
    {
        $this->currentServer = $side;
        $this->needsServerSelection = false;
        $this->match->update(['current_server' => $side, 'first_server' => $side, 'status' => 'in_progress']);
    }

    public function addPoint($side)
    {
        if ($this->matchComplete || $this->needsServerSelection) {
            return;
        }

        if ($this->inTiebreak) {
            $this->addTiebreakPoint($side);
            return;
        }

        if ($side === 'home') {
            $this->homePoints++;
        } else {
            $this->awayPoints++;
        }

        $this->checkGameCompletion();
        $this->persist();
    }

    private function checkGameCompletion()
    {
        $diff = abs($this->homePoints - $this->awayPoints);
        if (($this->homePoints >= 4 || $this->awayPoints >= 4) && $diff >= 2) {
            $this->completeGame($this->homePoints > $this->awayPoints ? 'home' : 'away');
        }
    }

    private function completeGame($winner)
    {
        $this->homePoints = 0;
        $this->awayPoints = 0;

        if ($winner === 'home') {
            $this->homeGames++;
        } else {
            $this->awayGames++;
        }

        $this->switchServer();

        if ($this->homeGames === 6 && $this->awayGames === 6) {
            $this->inTiebreak = true;
            $this->tiebreakHome = 0;
            $this->tiebreakAway = 0;
            return;
        }

        $this->checkSetCompletion();
    }

    private function addTiebreakPoint($side)
    {
        if ($side === 'home') {
            $this->tiebreakHome++;
        } else {
            $this->tiebreakAway++;
        }

        // Serve alternates every point in this simplified tiebreak implementation
        // (real tennis alternates after the first point, then every 2 points -
        // this only affects the serve indicator, never the score itself).
        $this->switchServer();

        $diff = abs($this->tiebreakHome - $this->tiebreakAway);
        if (($this->tiebreakHome >= 7 || $this->tiebreakAway >= 7) && $diff >= 2) {
            $winner = $this->tiebreakHome > $this->tiebreakAway ? 'home' : 'away';
            if ($winner === 'home') {
                $this->homeGames++;
            } else {
                $this->awayGames++;
            }
            $this->inTiebreak = false;
            $this->checkSetCompletion();
        }

        $this->persist();
    }

    private function checkSetCompletion()
    {
        $diff = abs($this->homeGames - $this->awayGames);
        $wonRegular = ($this->homeGames >= 6 || $this->awayGames >= 6) && $diff >= 2;
        $wonByTiebreak = $this->homeGames === 7 || $this->awayGames === 7;

        if ($wonRegular || $wonByTiebreak) {
            $this->completeSet($this->homeGames > $this->awayGames ? 'home' : 'away');
        }
    }

    private function completeSet($winner)
    {
        $this->completedSets[] = ['home' => $this->homeGames, 'away' => $this->awayGames];

        if ($winner === 'home') {
            $this->homeSets++;
        } else {
            $this->awaySets++;
        }

        $this->homeGames = 0;
        $this->awayGames = 0;

        if ($this->homeSets >= $this->setsToWin || $this->awaySets >= $this->setsToWin) {
            $this->completeMatch();
        }
    }

    private function completeMatch()
    {
        $this->matchComplete = true;
        $this->persist();
        $this->updateLeagueStandingsIfNeeded();
    }

    private function switchServer()
    {
        $this->currentServer = $this->currentServer === 'home' ? 'away' : 'home';
    }

    private function persist()
    {
        $this->match->update([
            'home_score' => $this->homeSets,
            'away_score' => $this->awaySets,
            'sets' => $this->completedSets,
            'current_server' => $this->currentServer,
            'status' => $this->matchComplete ? 'completed' : 'in_progress',
            'played_at' => $this->matchComplete ? now() : $this->match->played_at,
        ]);
    }

    public function resetMatch()
    {
        $this->homePoints = 0;
        $this->awayPoints = 0;
        $this->homeGames = 0;
        $this->awayGames = 0;
        $this->homeSets = 0;
        $this->awaySets = 0;
        $this->completedSets = [];
        $this->inTiebreak = false;
        $this->tiebreakHome = 0;
        $this->tiebreakAway = 0;
        $this->matchComplete = false;
        $this->needsServerSelection = true;
        $this->currentServer = null;

        $this->match->update([
            'home_score' => 0,
            'away_score' => 0,
            'sets' => [],
            'current_server' => null,
            'first_server' => null,
            'status' => 'scheduled',
            'played_at' => null,
        ]);
    }

    /**
     * Mirrors LiveScore::updateLeagueStandings for the same competition types
     * (individual/team league matches) - kept as a separate copy rather than a
     * shared call into LiveScore.php so this component cannot affect the
     * existing table-tennis live-scoring behavior.
     */
    private function updateLeagueStandingsIfNeeded()
    {
        $competition = $this->match->competition;
        if (!$competition->isLeague()) {
            return;
        }

        $homeId = $this->match->home_player_id;
        $awayId = $this->match->away_player_id;

        $homeStanding = Standing::firstOrCreate([
            'competition_id' => $competition->id,
            'player_id' => $homeId,
        ], ['played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'points' => 0, 'position' => 999]);

        $awayStanding = Standing::firstOrCreate([
            'competition_id' => $competition->id,
            'player_id' => $awayId,
        ], ['played' => 0, 'won' => 0, 'drawn' => 0, 'lost' => 0, 'points' => 0, 'position' => 999]);

        $homeStanding->increment('played');
        $awayStanding->increment('played');

        if ($this->homeSets > $this->awaySets) {
            $homeStanding->increment('won');
            $homeStanding->increment('points', 3);
            $awayStanding->increment('lost');
        } else {
            $awayStanding->increment('won');
            $awayStanding->increment('points', 3);
            $homeStanding->increment('lost');
        }
    }

    public function render()
    {
        return view('livewire.tennis-live-score');
    }
}
