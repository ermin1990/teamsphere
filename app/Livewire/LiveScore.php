<?php

namespace App\Livewire;

use App\Models\LeagueMatch;
use App\Models\Standing;
use Livewire\Component;

class LiveScore extends Component 
{
    public $match;
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

    public function mount($match)
    {
        \Log::info('LiveScore mount called', [
            'match_id' => $match->id,
            'initial_status' => $match->status,
            'initial_first_server' => $match->first_server
        ]);

        // Refresh match data to get latest status from database
        $match->refresh();
        
        $this->match = $match;
        
        // If match is scheduled (not started), always reset to initial state
        if ($match && $match->status === 'scheduled') {
            \Log::info('Match is scheduled, resetting to initial state', ['match_id' => $match->id]);
            
            $this->firstServer = null;
            $this->currentServer = null;
            $this->homeScore = 0;
            $this->awayScore = 0;
            $this->sets = [];
            $this->setDurations = [];
            $this->currentSet = 1;
            $this->matchStartTime = null;
            $this->setStartTime = null;
            $this->matchPaused = false;
            
            // Also reset in database to ensure consistency
            $match->update([
                'first_server' => null,
                'current_server' => null,
                'home_score' => 0,
                'away_score' => 0,
                'sets' => null,
                'set_durations' => null,
                'played_at' => null,
                'current_set_started_at' => null,
            ]);
            
            \Log::info('Match reset complete', [
                'match_id' => $match->id,
                'firstServer' => $this->firstServer,
                'status' => $match->fresh()->status
            ]);
        } else {
            \Log::info('Match is not scheduled, loading existing data', [
                'match_id' => $match->id,
                'status' => $match->status,
                'first_server' => $match->first_server
            ]);
            
            // Load existing match data for in-progress or completed matches
            if ($match && $match->first_server) {
                $this->firstServer = $match->first_server;
                $this->currentServer = $match->current_server ?? $match->first_server;
            }
            
            // Load scores and sets data
            $this->homeScore = $match->home_score ?? 0;
            $this->awayScore = $match->away_score ?? 0;
            $this->sets = $match->sets ?? [];
            $this->setDurations = $match->set_durations ?? [];
            $this->currentSet = count($this->sets) + 1;
            
            // Load timing data
            if ($match->played_at) {
                $this->matchStartTime = $match->played_at;
            }
            if ($match->current_set_started_at) {
                $this->setStartTime = $match->current_set_started_at;
            }
            
            \Log::info('Existing data loaded', [
                'match_id' => $match->id,
                'firstServer' => $this->firstServer,
                'homeScore' => $this->homeScore,
                'awayScore' => $this->awayScore
            ]);
        }
    }

    public function recordSetDuration($seconds)
    {
        // Add the duration to the setDurations array
        $this->setDurations[] = (int)$seconds;
        $this->match->update([
            'set_durations' => $this->setDurations,
        ]);
        // Force UI update
        $this->setsVersion++;
    }

    public function selectHomeServer()
    {
        $this->selectFirstServer('home');
    }

    public function selectAwayServer()
    {
        $this->selectFirstServer('away');
    }

    public function selectFirstServer($player)
    {
        $this->firstServer = $player;
        $this->currentServer = $player;

        $now = now();
        $this->matchStartTime = $now;
        $this->setStartTime = $now;

        $this->match->update([
            'first_server' => $player,
            'current_server' => $player,
            'status' => 'in_progress',
            'played_at' => $now,
            'current_set_started_at' => $now,
        ]);

        // Refresh match to get latest data
        $this->match->refresh();

        $this->startTimers();
    }    public function selectRandomServer()
    {
        $randomPlayer = rand(0, 1) ? 'home' : 'away';
        $this->selectFirstServer($randomPlayer);
    }

    public function resetServerSelection()
    {
        \Log::info('resetServerSelection called', [
            'match_id' => $this->match->id,
            'current_first_server' => $this->firstServer,
            'current_status' => $this->match->status
        ]);

        // Reset server selection
        $this->firstServer = null;
        $this->currentServer = null;
        $this->matchStartTime = null;
        $this->setStartTime = null;
        $this->matchPaused = false;

        // Reset scores
        $this->homeScore = 0;
        $this->awayScore = 0;
        $this->sets = [];
        $this->setDurations = [];
        $this->currentSet = 1;
        $this->serveCount = 0;
        $this->pointHistory = [];

        // Update match in database
        $this->match->update([
            'status' => 'scheduled',
            'first_server' => null,
            'current_server' => null,
            'home_score' => 0,
            'away_score' => 0,
            'sets' => null,
            'set_durations' => null,
            'played_at' => null,
            'current_set_started_at' => null,
        ]);

        // Clear local storage
        $this->dispatch('clear-local-storage');

        \Log::info('resetServerSelection completed', [
            'new_first_server' => $this->firstServer,
            'new_status' => $this->match->fresh()->status
        ]);
    }

    public function startMatch()
    {
        \Log::info('startMatch called', [
            'match_id' => $this->match->id,
            'first_server' => $this->firstServer,
            'current_server' => $this->currentServer
        ]);

        $now = now();
        $this->matchStartTime = $now;
        $this->setStartTime = $now;

        $this->match->update([
            'status' => 'in_progress',
            'played_at' => $now,
            'current_set_started_at' => $now,
            'first_server' => $this->firstServer,
            'current_server' => $this->currentServer,
        ]);

        $this->startTimers();

    }

    public function startTimer()
    {
        $now = now();
        $this->matchStartTime = $now;
        $this->setStartTime = $now;
        // Persist times and status
        $this->match->update([
            'status' => 'in_progress',
            'played_at' => $now,
            'current_set_started_at' => $now,
        ]);
        $this->startTimers();
    }

    public function addPoint($player)
    {
        // Get the parent (league or competition) for organization check
        $parent = $this->match->league ?? $this->match->competition;
        
        // Debug logging
        \Log::info('addPoint called', [
            'player' => $player,
            'match_id' => $this->match->id,
            'match_status' => $this->match->status,
            'is_org_owner' => $parent->organization->user_id === auth()->id(),
            'user_id' => auth()->id(),
            'org_user_id' => $parent->organization->user_id
        ]);

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

        if ($player === 'home') {
            $this->homeScore++;
        } else {
            $this->awayScore++;
        }

        // Change server based on score
        if ($this->homeScore >= 10 && $this->awayScore >= 10) {
            // When both players are at 10+, change server every point
            $this->currentServer = $this->currentServer === 'home' ? 'away' : 'home';
        } else {
            // Normal rotation: change server every 2 serves
            $this->serveCount++;
            if ($this->serveCount % 2 === 0) {
                $this->currentServer = $this->currentServer === 'home' ? 'away' : 'home';
            }
        }

        $this->match->update(['current_server' => $this->currentServer]);

        $this->updateScore();
        $this->checkSetWin();
    }

    public function subtractPoint($player)
    {
        // Prevent subtracting if score is already 0
        if (($player === 'home' && $this->homeScore <= 0) || ($player === 'away' && $this->awayScore <= 0)) {
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

        if ($player === 'home') {
            $this->homeScore = max(0, $this->homeScore - 1);
        } else {
            $this->awayScore = max(0, $this->awayScore - 1);
        }

        // Reverse server change logic
        if ($this->homeScore >= 10 && $this->awayScore >= 10) {
            // When both players are at 10+, change server every point (reverse)
            $this->currentServer = $this->currentServer === 'home' ? 'away' : 'home';
        } else {
            // Normal rotation: change server every 2 serves (reverse)
            $this->serveCount = max(0, $this->serveCount - 1);
            if ($this->serveCount % 2 === 0) {
                $this->currentServer = $this->currentServer === 'home' ? 'away' : 'home';
            }
        }

        $this->match->update(['current_server' => $this->currentServer]);

        $this->updateScore();
    }

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

            // Update database
            $this->match->update([
                'home_score' => $this->homeScore,
                'away_score' => $this->awayScore,
                'current_server' => $this->currentServer,
                'sets' => $this->sets,
                'set_durations' => $this->setDurations,
            ]);

            // Force UI update
            $this->setsVersion++;
        }
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
            // Automatically end the current set
            $this->endCurrentSet();
        }
    }

    public function endCurrentSet()
    {
        // Record set time
        if ($this->setStartTime) {
            $setDuration = $this->setStartTime->diffInSeconds(now());
            $this->setDurations[] = $setDuration; // Store as seconds

            \Log::info('Set duration recorded', [
                'duration_seconds' => $setDuration,
                'setStartTime' => $this->setStartTime,
                'now' => now(),
                'diffInSeconds' => $setDuration
            ]);
        }

        // Add set to history
        $this->sets[] = [
            'home_score' => $this->homeScore,
            'away_score' => $this->awayScore
        ];

        \Log::info('Set added to history', [
            'sets' => $this->sets,
            'setDurations' => $this->setDurations
        ]);

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

        \Log::info('Before database update', [
            'sets' => $this->sets,
            'set_durations' => $this->setDurations,
            'current_set_started_at' => $this->setStartTime
        ]);

        $result = $this->match->update([
            'sets' => $this->sets,
            'set_durations' => $this->setDurations,
            'current_set_started_at' => $this->setStartTime,
            'current_server' => $this->currentServer,
            'first_server' => $this->firstServer,
        ]);

        \Log::info('Database update result', [
            'result' => $result,
            'sets_to_save' => $this->sets,
            'durations_to_save' => $this->setDurations,
            'match_id' => $this->match->id
        ]);

        // Check if update was successful
        if (!$result) {
            \Log::error('Database update failed', [
                'match_id' => $this->match->id,
                'sets' => $this->sets,
                'set_durations' => $this->setDurations
            ]);
        }

        // Force refresh the match data and update component properties
        $this->match->refresh();
        $freshSets = $this->match->sets ?? [];
        $freshDurations = $this->match->set_durations ?? [];
        
        // Explicitly update properties to ensure Livewire reactivity
        $this->sets = $freshSets;
        $this->setDurations = $freshDurations;

        \Log::info('After refresh', [
            'sets' => $this->sets,
            'setDurations' => $this->setDurations,
            'freshSets' => $freshSets,
            'freshDurations' => $freshDurations
        ]);

        // Dispatch event to update JavaScript timers with fresh start time
        $this->dispatch('set-changed', [
            'setStartedAt' => $this->setStartTime->toIso8601String(),
        ]);

        // Check if match is won
        $this->checkMatchWin();
    }

    public function togglePause()
    {
        $this->matchPaused = !$this->matchPaused;

        if ($this->matchPaused) {
            // Stop timers on pause
            $this->dispatch('stop-timers');
        } else {
            // Resume timers - calculate correct start time accounting for pause duration
            $this->dispatch('start-timers', [
                'playedAt' => optional($this->match->played_at)->toIso8601String(),
                'setStartedAt' => optional($this->setStartTime)->toIso8601String(),
            ]);
        }
    }

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

        // Persist reset
        $this->match->update([
            'status' => 'scheduled',
            'home_score' => null,
            'away_score' => null,
            'sets' => null,
            'set_durations' => null,
            'played_at' => null,
            'current_set_started_at' => null,
            'first_server' => null,
            'current_server' => null,
        ]);

        // Stop any running timers and reset UI
        $this->dispatch('stop-timers');
        
        // Clear localStorage data for this match
        $this->dispatch('clear-local-storage', ['matchId' => $this->match->id]);
    }

    public function canEndMatch()
    {
        if ($this->match->status === 'completed') {
            return false;
        }

        $parent = $this->match->league ?? $this->match->competition;
        $setsToWin = $parent->settings['sets_to_win'] ?? 3;

        $homeSetsWon = 0;
        $awaySetsWon = 0;

        foreach ($this->sets as $set) {
            if ($set['home_score'] > $set['away_score']) {
                $homeSetsWon++;
            } elseif ($set['away_score'] > $set['home_score']) {
                $awaySetsWon++;
            }
        }

        return $homeSetsWon >= $setsToWin || $awaySetsWon >= $setsToWin;
    }

    public function endMatch()
    {
        // Record final set duration if not already recorded
        if ($this->setStartTime && empty($this->setDurations) || count($this->setDurations) < $this->currentSet) {
            $setDuration = now()->diffInSeconds($this->setStartTime);
            $minutes = floor($setDuration / 60);
            $seconds = $setDuration % 60;
            $this->setDurations[] = sprintf('%02d:%02d', $minutes, $seconds);
        }

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

        $parent = $this->match->league ?? $this->match->competition;
        $setsToWin = $parent->settings['sets_to_win'] ?? 3;

        // Only allow end match if someone has won enough sets
        if ($homeSetsWon < $setsToWin && $awaySetsWon < $setsToWin) {
            // Match not completed yet - show error message
            $this->dispatch('match-not-finished', [
                'message' => 'Match cannot be ended yet. A player must win ' . $setsToWin . ' sets first.',
                'homeSets' => $homeSetsWon,
                'awaySets' => $awaySetsWon,
                'setsToWin' => $setsToWin
            ]);
            return;
        }

        $this->match->update([
            'home_score' => $homeSetsWon,
            'away_score' => $awaySetsWon,
            'sets' => $this->sets,
            'set_durations' => $this->setDurations,
            'status' => 'completed',
            'played_at' => now(),
        ]);

        // Update standings if this is a league match
        if ($this->match->league) {
            $this->updateStandings($this->match->league);
        }

        // Stop timers and reset to 00:00
        $this->dispatch('stop-timers');
        $this->dispatch('reset-timer-display');

        // Redirect based on match type
        if ($this->match->league) {
            return redirect()->route('leagues.matches.show', [
                'league' => $this->match->league,
                'match' => $this->match
            ]);
        } else {
            return redirect()->route('organizations.competitions.show', [
                'organization' => $this->match->competition->organization,
                'competition' => $this->match->competition
            ]);
        }
    }

    private function checkMatchWin()
    {
        $parent = $this->match->league ?? $this->match->competition;
        $setsToWin = $parent->settings['sets_to_win'] ?? 3;
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
        return $this->endMatch();
    }

    public function confirmSetWin()
    {
        // Debug logging
        \Log::info('confirmSetWin called', [
            'match_id' => $this->match->id,
            'currentSet' => $this->currentSet,
            'homeScore' => $this->homeScore,
            'awayScore' => $this->awayScore,
            'setStartTime' => $this->setStartTime,
            'sets_before' => $this->sets,
            'durations_before' => $this->setDurations
        ]);

        $this->endCurrentSet();

        // After ending the set, ensure UI updates
        $this->match->refresh();
        $this->sets = $this->match->sets ?? [];
        $this->setDurations = $this->match->set_durations ?? [];

        \Log::info('confirmSetWin completed', [
            'sets_after' => $this->sets,
            'durations_after' => $this->setDurations,
            'match_sets' => $this->match->sets,
            'match_durations' => $this->match->set_durations
        ]);

        // Force UI update by incrementing version
        $this->setsVersion++;

        // Force UI update by dispatching a custom event
        $this->dispatch('sets-updated', [
            'sets' => $this->sets,
            'durations' => $this->setDurations,
            'version' => $this->setsVersion
        ]);

        // Force a full component refresh to ensure UI updates
        $this->dispatch('$refresh');

        // Also try to force reactivity by reassigning the arrays
        $tempSets = $this->sets;
        $tempDurations = $this->setDurations;
        $this->sets = [];
        $this->setDurations = [];
        $this->sets = $tempSets;
        $this->setDurations = $tempDurations;
    }

    public function pauseTimer()
    {
        // Stop the timer by dispatching stop event
        $this->dispatch('stop-timers');
    }

    private function startTimers()
    {
        // Timers handled in JS; send anchors to client
        $this->dispatch('start-timers',
            playedAt: optional($this->matchStartTime)->toIso8601String(),
            setStartedAt: optional($this->setStartTime)->toIso8601String()
        );
    }

    private function updateStandings($league)
    {
        // Reset all standings for this league
        Standing::where('competition_id', $league->id)->update([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
        ]);

        // Get all completed, forfeited matches and cancelled matches with scores
        $completedMatches = LeagueMatch::where('competition_id', $league->id)
            ->where(function($query) {
                $query->whereIn('status', ['completed', 'forfeited'])
                      ->orWhere(function($q) {
                          $q->where('status', 'cancelled')
                            ->where(function($sq) {
                                $sq->where('home_score', '>', 0)
                                   ->orWhere('away_score', '>', 0);
                            });
                      });
            })
            ->get();

        foreach ($completedMatches as $match) {
            $homeParticipantId = $league->is_team_based ? $match->home_team_id : $match->home_player_id;
            $awayParticipantId = $league->is_team_based ? $match->away_team_id : $match->away_player_id;

            $homeStanding = Standing::where('competition_id', $league->id)
                ->where($league->is_team_based ? 'team_id' : 'player_id', $homeParticipantId)
                ->first();

            $awayStanding = Standing::where('competition_id', $league->id)
                ->where($league->is_team_based ? 'team_id' : 'player_id', $awayParticipantId)
                ->first();

            if ($homeStanding && $awayStanding) {
                // Update played games
                $homeStanding->increment('played');
                $awayStanding->increment('played');

                // Handle forfeited matches
                if ($match->status === 'forfeited') {
                    if ($match->forfeited_by === 'home') {
                        // Away wins by forfeit
                        $awayStanding->increment('won');
                        $awayStanding->increment('points', $league->settings['points_win'] ?? 3);
                        $homeStanding->increment('lost');
                        $homeStanding->increment('points', $league->settings['points_loss'] ?? 1);
                    } elseif ($match->forfeited_by === 'away') {
                        // Home wins by forfeit
                        $homeStanding->increment('won');
                        $homeStanding->increment('points', $league->settings['points_win'] ?? 3);
                        $awayStanding->increment('lost');
                        $awayStanding->increment('points', $league->settings['points_loss'] ?? 1);
                    }
                } else {
                    // Regular match results based on sets won
                    $homeSets = $match->home_score ?? 0;
                    $awaySets = $match->away_score ?? 0;

                    if ($homeSets > $awaySets) {
                        // Home wins
                        $homeStanding->increment('won');
                        $awayStanding->increment('lost');
                        $homeStanding->increment('points', $league->settings['points_win'] ?? 3);
                        $awayStanding->increment('points', $league->settings['points_loss'] ?? 1);
                    } elseif ($awaySets > $homeSets) {
                        // Away wins
                        $awayStanding->increment('won');
                        $homeStanding->increment('lost');
                        $awayStanding->increment('points', $league->settings['points_win'] ?? 3);
                        $homeStanding->increment('points', $league->settings['points_loss'] ?? 1);
                    } else {
                        // Draw (shouldn't happen in table tennis, but just in case)
                        $homeStanding->increment('drawn');
                        $awayStanding->increment('drawn');
                        $homeStanding->increment('points', $league->settings['points_draw'] ?? 1);
                        $awayStanding->increment('points', $league->settings['points_draw'] ?? 1);
                    }

                    // Update goals (sets) for and against
                    $homeStanding->increment('goals_for', $homeSets);
                    $homeStanding->increment('goals_against', $awaySets);
                    $awayStanding->increment('goals_for', $awaySets);
                    $awayStanding->increment('goals_against', $homeSets);

                    // Update goal difference
                    $homeStanding->increment('goal_difference', $homeSets - $awaySets);
                    $awayStanding->increment('goal_difference', $awaySets - $homeSets);
                }
            }
        }

        // Update positions based on points, then goal difference, then goals for
        $standings = Standing::where('competition_id', $league->id)
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->get();

        $position = 1;
        foreach ($standings as $standing) {
            $standing->update(['position' => $position]);
            $position++;
        }
    }

    public function render()
    {
        // Check if match belongs to league or competition and get organization accordingly
        if ($this->match->league) {
            $isOrganizationOwner = $this->match->league->organization->user_id === auth()->id();
        } else {
            $isOrganizationOwner = $this->match->competition->organization->user_id === auth()->id();
        }
        
        return view('livewire.live-score', [
            'match' => $this->match,
            'firstServer' => $this->firstServer,
            'currentServer' => $this->currentServer,
            'homeScore' => $this->homeScore,
            'awayScore' => $this->awayScore,
            'currentSet' => $this->currentSet,
            'setDurations' => $this->setDurations,
            'sets' => $this->sets,
            'setsVersion' => $this->setsVersion,
            'setStartTime' => $this->setStartTime,
            'matchPaused' => $this->matchPaused,
            'isOrganizationOwner' => $isOrganizationOwner,
        ]);
    }
}
