<?php

namespace App\Livewire;

use App\Models\LeagueMatch;
use Livewire\Component;

class LiveScore extends Component
{
    public LeagueMatch $match;
    public $homeScore = 0;
    public $awayScore = 0;
    public $currentSet = 1;
    public $sets = [];
    public $firstServer = null;
    public $currentServer = null;
    public $matchStartTime = null;
    public $setStartTime = null;
    public $matchPaused = false;
    public $setTimes = [];
    public $serveCount = 0;

    public function mount(LeagueMatch $match)
    {
        $this->match = $match;
        $this->homeScore = $match->home_score ?? 0;
        $this->awayScore = $match->away_score ?? 0;
        $this->sets = $match->sets ?? [];
        $this->firstServer = $match->first_server;
        $this->currentServer = $match->current_server;

        if ($match->status === 'in_progress') {
            $this->startTimers();
        }
    }

    public function selectFirstServer($player)
    {
        $this->firstServer = $player;
        $this->currentServer = $player;
        $this->match->update([
            'first_server' => $player,
            'current_server' => $player,
        ]);

        // Auto-start the match when first server is selected
        $this->startMatch();
    }

    public function selectRandomServer()
    {
        $randomPlayer = rand(0, 1) ? 'home' : 'away';
        $this->selectFirstServer($randomPlayer);
    }

    public function startMatch()
    {
        $this->matchStartTime = now();
        $this->setStartTime = now();

        $this->match->update([
            'status' => 'in_progress',
            'played_at' => now(),
            'first_server' => $this->firstServer,
            'current_server' => $this->currentServer,
        ]);

        $this->startTimers();
    }

    public function startTimer()
    {
        $this->matchStartTime = now();
        $this->setStartTime = now();
        $this->startTimers();
    }

    public function addPoint($player)
    {
        if ($player === 'home') {
            $this->homeScore++;
        } else {
            $this->awayScore++;
        }

        // Change server every 2 serves
        $this->serveCount++;
        if ($this->serveCount % 2 === 0) {
            $this->currentServer = $this->currentServer === 'home' ? 'away' : 'home';
            $this->match->update(['current_server' => $this->currentServer]);
        }

        $this->updateScore();
        $this->checkSetWin();
    }

    public function subtractPoint($player)
    {
        if ($player === 'home' && $this->homeScore > 0) {
            $this->homeScore--;
        } elseif ($player === 'away' && $this->awayScore > 0) {
            $this->awayScore--;
        }

        $this->updateScore();
    }

    private function updateScore()
    {
        $this->match->update([
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore,
        ]);
    }

    private function checkSetWin()
    {
        $winScore = 11;
        $scoreDiff = abs($this->homeScore - $this->awayScore);

        if (($this->homeScore >= $winScore || $this->awayScore >= $winScore) && $scoreDiff >= 2) {
            $this->endCurrentSet();
        }
    }

    public function endCurrentSet()
    {
        // Record set time
        if ($this->setStartTime) {
            $setDuration = now()->diffInSeconds($this->setStartTime);
            $minutes = floor($setDuration / 60);
            $seconds = $setDuration % 60;
            $this->setTimes[] = sprintf('%02d:%02d', $minutes, $seconds);
        }

        // Add set to history
        $this->sets[] = [
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore
        ];

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

        $this->match->update([
            'sets' => $this->sets,
            'current_server' => $this->currentServer,
            'first_server' => $this->firstServer,
        ]);

        // Dispatch event to update JavaScript timers
        $this->dispatch('set-changed');

        // Check if match is won
        $this->checkMatchWin();
    }

    public function togglePause()
    {
        $this->matchPaused = !$this->matchPaused;

        if ($this->matchPaused) {
            $this->match->update(['status' => 'scheduled']);
        } else {
            $this->match->update(['status' => 'in_progress']);
        }
    }

    public function endMatch()
    {
        // Calculate final match scores from sets
        $homeSetsWon = 0;
        $awaySetsWon = 0;

        foreach ($this->sets as $set) {
            if ($set['home_score'] > $set['away_score']) {
                $homeSetsWon++;
            } elseif ($set['away_score'] > $set['home_score']) {
                $awaySetsWon++;
            }
        }

        $this->match->update([
            'home_score' => $homeSetsWon,
            'away_score' => $awaySetsWon,
            'sets' => $this->sets,
            'status' => 'completed',
        ]);

        // Don't redirect, just mark as completed and disable further editing
        $this->match->refresh();
    }

    private function checkMatchWin()
    {
        $setsToWin = $this->match->league->settings['sets_to_win'] ?? 3;
        $homeSetsWon = 0;
        $awaySetsWon = 0;

        foreach ($this->sets as $set) {
            if ($set['home_score'] > $set['away_score']) {
                $homeSetsWon++;
            } elseif ($set['away_score'] > $set['home_score']) {
                $awaySetsWon++;
            }
        }

        if ($homeSetsWon >= $setsToWin || $awaySetsWon >= $setsToWin) {
            // Match is won, show confirmation and end match
            $this->dispatch('match-won', [
                'winner' => $homeSetsWon >= $setsToWin ? 'home' : 'away',
                'homeSets' => $homeSetsWon,
                'awaySets' => $awaySetsWon,
                'setsToWin' => $setsToWin,
                'finalSets' => $this->sets
            ]);
        }
    }

    public function confirmMatchEnd()
    {
        $this->endMatch();
    }

    private function startTimers()
    {
        // Timers will be handled by Livewire's reactivity
        // We can use JavaScript for real-time timer display
        $this->dispatch('start-timers');
    }

    public function render()
    {
        return view('livewire.live-score');
    }
}
