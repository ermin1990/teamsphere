<?php

namespace App\Livewire;

use App\Models\CompetitionMatch;
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

    /**
     * Rekreativna liga: zavrsi mec odmah, uzimajuci trenutni rezultat gemova
     * u tekucem setu kao konacan rezultat tog seta (npr. 3-1 gemova racuna se
     * kao osvojen set), bez cekanja da neko dodje do standardnih 6 gemova ili
     * tiebreak-a, i bez cekanja da bude odigran ceo broj setova za pobjedu.
     */
    public function finishMatchNow()
    {
        if ($this->matchComplete) {
            return;
        }

        if ($this->homeGames > 0 || $this->awayGames > 0) {
            $this->completedSets[] = ['home' => $this->homeGames, 'away' => $this->awayGames];
            if ($this->homeGames > $this->awayGames) {
                $this->homeSets++;
            } elseif ($this->awayGames > $this->homeGames) {
                $this->awaySets++;
            }
        }

        $this->homeGames = 0;
        $this->awayGames = 0;
        $this->homePoints = 0;
        $this->awayPoints = 0;
        $this->inTiebreak = false;
        $this->matchComplete = true;

        $this->persist();
        $this->updateLeagueStandingsIfNeeded();
    }

    public function resetMatch()
    {
        // Reverse this match's league standings contribution before wiping
        // its score, otherwise a reset match leaves stale points/wins behind.
        if ($this->match->status === 'completed' && $this->match->competition && $this->match->competition->isLeague()) {
            app(\App\Services\LeagueStandingsService::class)
                ->reverseForMatch($this->match->competition, $this->match, $this->match->home_score, $this->match->away_score);
        }

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
     * Delegates to the shared LeagueStandingsService (same one used by
     * CompetitionController::quickResult and LiveScore::forceFinishMatch) so
     * ties are handled correctly (draw, not a silent win for "away") and the
     * points math can't drift between the three places that complete a
     * league match. $this->match already has home_score/away_score/status
     * persisted by persist() before this is called.
     */
    private function updateLeagueStandingsIfNeeded()
    {
        $competition = $this->match->competition;
        if (!$competition->isLeague()) {
            return;
        }

        app(\App\Services\LeagueStandingsService::class)->applyForMatch($competition, $this->match);
    }

    public function render()
    {
        return view('livewire.tennis-live-score');
    }
}
