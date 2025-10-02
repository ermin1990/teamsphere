<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Player;
use App\Models\FriendlyMatch;

class TableTennisFriendly extends Component
{
    // Save match, sets, and set durations (for now, just log; adapt to DB as needed)
    public function saveMatch()
    {
        if (!$this->matchCompleted) {
            // Only allow saving if match is completed
            return;
        }
        $data = [
            'organization_id' => $this->organization->id,
            'sets' => $this->sets,
            'set_durations' => $this->setDurations,
            'winner_name' => $this->getMatchWinner(),
            'completed_at' => now(),
        ];
        if ($this->matchType === 'team') {
            $data['home_pair_ids'] = implode(',', [$this->homePlayer['id'], $this->homePlayer2['id']]);
            $data['away_pair_ids'] = implode(',', [$this->awayPlayer['id'], $this->awayPlayer2['id']]);
            $data['home_player_name'] = $this->homePlayer['name'] . ' / ' . $this->homePlayer2['name'];
            $data['away_player_name'] = $this->awayPlayer['name'] . ' / ' . $this->awayPlayer2['name'];
        } else {
            $data['home_player_id'] = $this->homePlayer['id'];
            $data['away_player_id'] = $this->awayPlayer['id'];
            $data['home_player_name'] = $this->homePlayer ? $this->homePlayer['name'] : 'Home Player';
            $data['away_player_name'] = $this->awayPlayer ? $this->awayPlayer['name'] : 'Away Player';
        }
        FriendlyMatch::create($data);

        session()->flash('message', 'Meč je uspješno snimljen!');
        return redirect()->route('organizations.friendly-matches.index', ['organization' => $this->organization->slug]);
    }

    // Helper to get match winner
    private function getMatchWinner()
    {
        $homeSets = count(array_filter($this->sets, fn($set) => $set['home_score'] > $set['away_score']));
        $awaySets = count(array_filter($this->sets, fn($set) => $set['away_score'] > $set['home_score']));
        if ($homeSets > $awaySets) {
            if ($this->matchType === 'team') {
                return $this->homePlayer['name'] . ' / ' . $this->homePlayer2['name'];
            } else {
                return $this->homePlayer ? $this->homePlayer['name'] : 'Home Player';
            }
        } elseif ($awaySets > $homeSets) {
            if ($this->matchType === 'team') {
                return $this->awayPlayer['name'] . ' / ' . $this->awayPlayer2['name'];
            } else {
                return $this->awayPlayer ? $this->awayPlayer['name'] : 'Away Player';
            }
        } else {
            return 'Draw';
        }
    }
    // Start timers for JS (copied from LiveScore)
    private function startTimers()
    {
        $this->dispatch('start-timers',
            playedAt: optional($this->matchStartTime)->toIso8601String(),
            setStartedAt: optional($this->setStartTime)->toIso8601String()
        );
    }

    // Livewire expects a public method for undo
    public function undoLastPoint()
    {
        $this->undoPoint();
    }
    public $organization;
    public $matchType; // 'individual' or 'team'

    // Player selection
    public $playersSelected = false;
    public $availablePlayers = [];
    public $setsToWin = 3; // Default to best of 3 sets

    // Match state - copied from LiveScore
    public $homeScore = 0;
    public $awayScore = 0;
    public $sets = [];
    public $setDurations = [];
    public $setsVersion = 0; // Used to force UI updates
    public $serveCount = 0;
    public $pointHistory = [];
    public $firstServer = null;
    public $currentServer = null;
    public $matchStartTime = null;
    public $setStartTime = null;
    public $matchPaused = false;
    public $currentSet = 1;

    // Players/Teams
    public $homePlayer = null;
    public $awayPlayer = null;
    // For doubles (multi-select)
    public $homePair = [];
    public $awayPair = [];
    public $homePlayer2 = null;
    public $awayPlayer2 = null;
    public $homeTeam = null;
    public $awayTeam = null;

    // Match control
    public $matchStarted = false;
    public $matchCompleted = false;

    protected $listeners = [
        'recordSetDuration' => 'recordSetDuration',
    ];

    public function mount($organization, $matchType = 'individual')
    {
        $this->organization = $organization;
        $this->matchType = $matchType;

        // Load available players for both types
        $this->availablePlayers = Player::where('organization_id', $organization->id)
            ->orderBy('name')
            ->get()
            ->toArray();
    }
    // For doubles
    public function selectHomePlayer2($playerId)
    {
        $player = collect($this->availablePlayers)->firstWhere('id', $playerId);
        if ($player) {
            $this->homePlayer2 = $player;
        }
    }
    public function selectAwayPlayer2($playerId)
    {
        $player = collect($this->availablePlayers)->firstWhere('id', $playerId);
        if ($player) {
            $this->awayPlayer2 = $player;
        }
    }

    public function selectHomePlayer($playerId)
    {
        $player = collect($this->availablePlayers)->firstWhere('id', $playerId);
        if ($player) {
            $this->homePlayer = $player;
        }
    }

    public function selectAwayPlayer($playerId)
    {
        $player = collect($this->availablePlayers)->firstWhere('id', $playerId);
        if ($player) {
            $this->awayPlayer = $player;
        }
    }

    public function setSetsToWin($sets)
    {
        $this->setsToWin = $sets;
    }

    public function confirmPlayerSelection()
    {
        if ($this->matchType === 'team') {
            // Validate exactly 2 unique players per team
            if (count($this->homePair) !== 2 || count($this->awayPair) !== 2) {
                $this->addError('players', 'Odaberite po 2 igrača za svaki tim.');
                return;
            }
            $ids = array_merge($this->homePair, $this->awayPair);
            if (count($ids) !== count(array_unique($ids))) {
                $this->addError('players', 'Svi igrači moraju biti različiti.');
                return;
            }
            // Set homePlayer/homePlayer2 and awayPlayer/awayPlayer2 from selected IDs
            $playersById = collect($this->availablePlayers)->keyBy('id');
            $this->homePlayer = $playersById[$this->homePair[0]] ?? null;
            $this->homePlayer2 = $playersById[$this->homePair[1]] ?? null;
            $this->awayPlayer = $playersById[$this->awayPair[0]] ?? null;
            $this->awayPlayer2 = $playersById[$this->awayPair[1]] ?? null;
        } else {
            if (!$this->homePlayer || !$this->awayPlayer) {
                $this->addError('players', 'Please select both players.');
                return;
            }
            if ($this->homePlayer['id'] === $this->awayPlayer['id']) {
                $this->addError('players', 'Please select different players.');
                return;
            }
        }
        $this->playersSelected = true;
    }

    // Adapted from LiveScore selectFirstServer
    public function selectFirstServer($player)
    {
        $this->firstServer = $player;
        $this->currentServer = $player;

        // Auto-start the match when first server is selected
        $this->startMatch();
    }

    // Adapted from LiveScore selectRandomServer
    public function selectRandomServer()
    {
        $randomPlayer = rand(0, 1) ? 'home' : 'away';
        $this->selectFirstServer($randomPlayer);
    }

    // Adapted from LiveScore startMatch
    public function startMatch()
    {
        $now = now();
        $this->matchStartTime = $now;
        $this->setStartTime = $now;
        $this->matchStarted = true;

        $this->startTimers();
    }

    // Adapted from LiveScore addPoint
    public function addPoint($player)
    {
        \Log::debug('addPoint called', [
            'player' => $player,
            'homeScore_before' => $this->homeScore,
            'awayScore_before' => $this->awayScore,
            'sets' => $this->sets,
            'matchCompleted' => $this->matchCompleted,
        ]);
        if (!$this->matchStarted || $this->matchPaused || $this->matchCompleted) {
            \Log::debug('addPoint: not started or paused or completed', [
                'matchStarted' => $this->matchStarted,
                'matchPaused' => $this->matchPaused,
                'matchCompleted' => $this->matchCompleted,
            ]);
            return;
        }

        // Save current state for undo functionality
        $this->pointHistory[] = [
            'homeScore' => $this->homeScore,
            'awayScore' => $this->awayScore,
            'serveCount' => $this->serveCount,
            'currentServer' => $this->currentServer,
            'sets' => $this->sets,
            'setDurations' => $this->setDurations,
            'currentSet' => $this->currentSet
        ];

        // Deuce logic: check if both are at least 10 BEFORE increment
        $deuce = ($this->homeScore >= 10 && $this->awayScore >= 10);

        if ($player === 'home') {
            $this->homeScore++;
        } else {
            $this->awayScore++;
        }

        \Log::debug('addPoint after increment', [
            'homeScore' => $this->homeScore,
            'awayScore' => $this->awayScore,
        ]);

        // Change server based on score
        if ($deuce) {
            // After 10-10, change server every point
            $this->currentServer = $this->currentServer === 'home' ? 'away' : 'home';
        } else {
            // Normal rotation: change server every 2 serves
            $this->serveCount++;
            if ($this->serveCount % 2 === 0) {
                $this->currentServer = $this->currentServer === 'home' ? 'away' : 'home';
            }
        }

        $this->updateScore();
        $this->checkSetWin();
        \Log::debug('addPoint end', [
            'homeScore' => $this->homeScore,
            'awayScore' => $this->awayScore,
            'sets' => $this->sets,
            'matchCompleted' => $this->matchCompleted,
        ]);
    }

    // Adapted from LiveScore undoPoint
    public function undoPoint()
    {
        if (count($this->pointHistory) > 0) {
            $lastState = array_pop($this->pointHistory);

            $this->homeScore = $lastState['homeScore'];
            $this->awayScore = $lastState['awayScore'];
            $this->serveCount = $lastState['serveCount'];
            $this->currentServer = $lastState['currentServer'];
            $this->sets = $lastState['sets'];
            $this->setDurations = $lastState['setDurations'];
            $this->currentSet = $lastState['currentSet'];

            // Force UI update
            $this->setsVersion++;
        }
    }

    // Copied from LiveScore
    private function updateScore()
    {
        // No database update for friendly matches
    }

    // Copied from LiveScore checkSetWin
    private function checkSetWin()
    {
        $winScore = 11;
        $scoreDiff = abs($this->homeScore - $this->awayScore);
        \Log::debug('checkSetWin', [
            'homeScore' => $this->homeScore,
            'awayScore' => $this->awayScore,
            'scoreDiff' => $scoreDiff,
            'sets' => $this->sets,
        ]);
        // Set is won if a player has at least 11 AND at least 2 more than opponent (or >10 and 2 more)
        if ((($this->homeScore >= $winScore || $this->awayScore >= $winScore) && $scoreDiff >= 2)) {
            \Log::debug('checkSetWin: set completed', [
                'homeScore' => $this->homeScore,
                'awayScore' => $this->awayScore,
            ]);
            // Automatically end the current set
            $this->endCurrentSet();
        }
    }



    // Adapted from LiveScore togglePause
    public function togglePause()
    {
        $this->matchPaused = !$this->matchPaused;

        if ($this->matchPaused) {
            // Stop timers on pause
            $this->dispatch('stop-timers');
        } else {
            // Resume timers - calculate correct start time accounting for pause duration
            $this->dispatch('start-timers', [
                'playedAt' => optional($this->matchStartTime)->toIso8601String(),
                'setStartedAt' => optional($this->setStartTime)->toIso8601String(),
            ]);
        }
    }

    // Adapted from LiveScore resetMatch
    public function resetMatch()
    {
        // Reset local state
        $this->homeScore = 0;
        $this->awayScore = 0;
        $this->sets = [];
        $this->setDurations = [];
        $this->currentSet = 1;
        $this->serveCount = 0;
        $this->firstServer = null;
        $this->currentServer = null;
        $this->matchPaused = false;
        $this->matchStartTime = null;
        $this->setStartTime = null;
        $this->matchStarted = false;
        $this->matchCompleted = false;
        $this->pointHistory = [];
        $this->playersSelected = false;
        $this->homePair = [];
        $this->awayPair = [];
        $this->homePlayer = null;
        $this->homePlayer2 = null;
        $this->awayPlayer = null;
        $this->awayPlayer2 = null;

        // Stop any running timers and reset UI
        $this->dispatch('stop-timers');
    }

    // Adapted from LiveScore checkMatchWin
    private function checkMatchWin()
    {
        $homeSetsWon = 0;
        $awaySetsWon = 0;

        foreach ($this->sets as $set) {
            if ($set['home_score'] > $set['away_score']) {
                $homeSetsWon++;
            } elseif ($set['away_score'] > $set['home_score']) {
                $awaySetsWon++;
            }
        }

        $setsNeededToWin = (int) ceil($this->setsToWin / 2);
        \Log::debug('checkMatchWin', [
            'homeSetsWon' => $homeSetsWon,
            'awaySetsWon' => $awaySetsWon,
            'setsNeededToWin' => $setsNeededToWin,
            'sets' => $this->sets,
        ]);
        if ($homeSetsWon >= $setsNeededToWin || $awaySetsWon >= $setsNeededToWin) {
            \Log::debug('checkMatchWin: match completed', [
                'winner' => $homeSetsWon >= $setsNeededToWin ? 'home' : 'away',
                'homeSets' => $homeSetsWon,
                'awaySets' => $awaySetsWon,
            ]);
            // Match is won, show confirmation and end match
            $this->matchCompleted = true;
            $this->dispatch('match-won', [
                'winner' => $homeSetsWon >= $setsNeededToWin ? 'home' : 'away',
                'homeSets' => $homeSetsWon,
                'awaySets' => $awaySetsWon,
                'setsToWin' => $this->setsToWin,
                'finalSets' => $this->sets
            ]);
        }
    }

    // Copied from LiveScore
    public function recordSetDuration($seconds)
    {
        // Add the duration to the setDurations array
        $this->setDurations[] = (int)$seconds;
        // Force UI update
        $this->setsVersion++;
    }

    // Copied from LiveScore pauseTimer
    public function endCurrentSet()
    {
        // Record set time
        if ($this->setStartTime) {
            $setDuration = $this->setStartTime->diffInSeconds(now());
            $this->setDurations[] = $setDuration; // Store as seconds
        }

        // Add set to history
        $this->sets[] = [
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore
        ];

        // Check if match is won BEFORE resetting for next set
        $this->checkMatchWin();
        if ($this->matchCompleted) {
            return;
        }

        // Reset scores for next set
        $this->homeScore = 0;
        $this->awayScore = 0;
        $this->currentSet++;

        // Reset serve count for new set
        $this->serveCount = 0;

        // Switch first server for next set (opposite of previous set's first server)
        $this->currentServer = $this->firstServer === 'home' ? 'away' : 'home';
        $this->firstServer = $this->currentServer; // Update first server for next set
        $this->setStartTime = now();
    }
}
