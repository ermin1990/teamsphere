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
    public $pointHistory = [];

    public $playedAt;
    public $venueId;
    public $venues = [];

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

        $this->playedAt = optional($this->match->played_at)->format('Y-m-d\TH:i') ?? now()->format('Y-m-d\TH:i');
        $this->venueId = $this->match->venue_id;

        $competition = $this->match->competition;
        $this->venues = $competition && $competition->city_id
            ? \App\Models\Venue::where('city_id', $competition->city_id)->orderBy('name')->get(['id', 'name'])->toArray()
            : [];

        // Resume in-progress game/set state if cached (kept only in memory per-session;
        // a page refresh mid-game restarts the current game at 0-0, which is an
        // acceptable trade-off given games/sets already won are persisted).
    }

    /**
     * True if the authenticated user is one of the two players in this
     * (individual, non-team) match - lets a player run live scoring on
     * their own match, same as an organizer/referee can.
     */
    private function isPlayerParticipant(): bool
    {
        $playerIds = array_filter([$this->match->home_player_id, $this->match->away_player_id]);

        if (empty($playerIds)) {
            return false;
        }

        return \App\Models\Player::whereIn('id', $playerIds)
            ->where('user_id', auth()->id())
            ->exists();
    }

    private function isOrganizationStaff(): bool
    {
        $organization = $this->match->league
            ? $this->match->league->organization
            : $this->match->competition->organization;

        $isOrganizationOwner = $organization->user_id === auth()->id();
        $isOrganizationReferee = auth()->user()->organizationUsers()
            ->where('organization_id', $organization->id)
            ->where('role', 'referee')
            ->exists();
        $isMatchReferee = $this->match->referee_user_id === auth()->id();

        return $isOrganizationOwner || $isOrganizationReferee || $isMatchReferee;
    }

    public function canManageLiveScore(): bool
    {
        return $this->isOrganizationStaff() || $this->isPlayerParticipant();
    }

    private function assertCanManageLiveScore(): void
    {
        abort_unless($this->canManageLiveScore(), 403, 'Nemaš dozvolu za upravljanje ovim mečom.');
    }

    public function selectFirstServer($side)
    {
        $this->assertCanManageLiveScore();

        $this->currentServer = $side;
        $this->needsServerSelection = false;
        $this->match->update([
            'current_server' => $side,
            'first_server' => $side,
            'status' => 'in_progress',
            'played_at' => $this->playedAt ?: now(),
            'venue_id' => $this->venueId ?: null,
        ]);
    }

    /**
     * Snapshot the full score state before a point is applied, so a
     * mis-tap (wrong player) can be undone via undoPoint() below.
     */
    private function pushHistory(): void
    {
        $this->pointHistory[] = [
            'homePoints' => $this->homePoints,
            'awayPoints' => $this->awayPoints,
            'homeGames' => $this->homeGames,
            'awayGames' => $this->awayGames,
            'homeSets' => $this->homeSets,
            'awaySets' => $this->awaySets,
            'completedSets' => $this->completedSets,
            'inTiebreak' => $this->inTiebreak,
            'tiebreakHome' => $this->tiebreakHome,
            'tiebreakAway' => $this->tiebreakAway,
            'currentServer' => $this->currentServer,
            'matchComplete' => $this->matchComplete,
        ];
    }

    public function undoPoint()
    {
        $this->assertCanManageLiveScore();

        if (empty($this->pointHistory)) {
            return;
        }

        $state = array_pop($this->pointHistory);

        $this->homePoints = $state['homePoints'];
        $this->awayPoints = $state['awayPoints'];
        $this->homeGames = $state['homeGames'];
        $this->awayGames = $state['awayGames'];
        $this->homeSets = $state['homeSets'];
        $this->awaySets = $state['awaySets'];
        $this->completedSets = $state['completedSets'];
        $this->inTiebreak = $state['inTiebreak'];
        $this->tiebreakHome = $state['tiebreakHome'];
        $this->tiebreakAway = $state['tiebreakAway'];
        $this->currentServer = $state['currentServer'];
        $this->matchComplete = $state['matchComplete'];

        $this->persist();
    }

    public function addPoint($side)
    {
        $this->assertCanManageLiveScore();

        if ($this->matchComplete || $this->needsServerSelection) {
            return;
        }

        $this->pushHistory();

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
        $this->assertCanManageLiveScore();

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
        $this->assertCanManageLiveScore();

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
        $this->pointHistory = [];

        $this->match->update([
            'home_score' => 0,
            'away_score' => 0,
            'sets' => [],
            'current_server' => null,
            'first_server' => null,
            'status' => 'scheduled',
            'played_at' => null,
        ]);

        // Recompute league standings so a reset match's contribution is gone.
        if ($this->match->competition && $this->match->competition->isLeague()) {
            app(\App\Services\LeagueStandingsService::class)->rebuildForCompetition($this->match->competition);
        }
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

        app(\App\Services\LeagueStandingsService::class)->rebuildForCompetition($competition);
    }

    public function render()
    {
        return view('livewire.tennis-live-score');
    }
}
