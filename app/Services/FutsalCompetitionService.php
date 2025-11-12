<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\FutsalTeam;
use App\Models\FutsalMatch;
use App\Models\FutsalStanding;
use App\Models\TournamentGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FutsalCompetitionService
{
    /**
     * Generate league matches using Berger algorithm for futsal teams.
     * 
     * @param Competition $competition
     * @param bool $doubleRoundRobin Whether to generate home and away matches
     * @return void
     */
    public function generateLeagueMatches(Competition $competition, bool $doubleRoundRobin = false)
    {
        $teams = $competition->futsalTeams()->where('is_active', true)->get();
        $teamCount = $teams->count();

        if ($teamCount < 2) {
            throw new \Exception('Potrebna su najmanje 2 tima za ligu.');
        }

        // Initialize standings
        foreach ($teams as $team) {
            FutsalStanding::create([
                'competition_id' => $competition->id,
                'futsal_team_id' => $team->id,
                'position' => 0,
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_difference' => 0,
                'points' => 0,
            ]);
        }

        // Generate Berger schedule
        $schedule = $this->generateBergerSchedule($teams->pluck('id')->toArray());

        // Create matches for first round robin
        $round = 1;
        foreach ($schedule as $roundMatches) {
            foreach ($roundMatches as $match) {
                FutsalMatch::create([
                    'competition_id' => $competition->id,
                    'home_team_id' => $match['home'],
                    'away_team_id' => $match['away'],
                    'status' => 'scheduled',
                    'round' => $round,
                    'phase' => 'league',
                ]);
            }
            $round++;
        }

        // If double round robin, create return matches with reversed home/away
        if ($doubleRoundRobin) {
            foreach ($schedule as $roundMatches) {
                foreach ($roundMatches as $match) {
                    FutsalMatch::create([
                        'competition_id' => $competition->id,
                        'home_team_id' => $match['away'], // Reversed
                        'away_team_id' => $match['home'],  // Reversed
                        'status' => 'scheduled',
                        'round' => $round,
                        'phase' => 'league',
                    ]);
                }
                $round++;
            }
        }
    }

    /**
     * Generate Berger algorithm schedule (round-robin).
     * 
     * @param array $teamIds
     * @return array
     */
    private function generateBergerSchedule(array $teamIds): array
    {
        $teamCount = count($teamIds);
        $isOdd = $teamCount % 2 !== 0;

        // If odd number of teams, add a dummy (bye)
        if ($isOdd) {
            $teamIds[] = null;
            $teamCount++;
        }

        $rounds = $teamCount - 1;
        $matchesPerRound = $teamCount / 2;
        $schedule = [];

        // Fixed position for team at index 0
        for ($round = 0; $round < $rounds; $round++) {
            $roundMatches = [];
            
            for ($match = 0; $match < $matchesPerRound; $match++) {
                $home = ($round + $match) % ($teamCount - 1);
                $away = ($teamCount - 1 - $match + $round) % ($teamCount - 1);

                // Last team (index n-1) is paired with the team calculated above
                if ($match == 0) {
                    $away = $teamCount - 1;
                }

                // Alternate home/away for fairness
                if ($round % 2 == 1 && $match != 0) {
                    $temp = $home;
                    $home = $away;
                    $away = $temp;
                }

                $homeTeamId = $teamIds[$home];
                $awayTeamId = $teamIds[$away];

                // Skip matches with dummy (bye)
                if ($homeTeamId !== null && $awayTeamId !== null) {
                    $roundMatches[] = [
                        'home' => $homeTeamId,
                        'away' => $awayTeamId,
                    ];
                }
            }

            $schedule[] = $roundMatches;
        }

        return $schedule;
    }

    /**
     * Generate tournament groups for futsal competition.
     * 
     * @param Competition $competition
     * @param int $groupCount Number of groups
     * @param bool $doubleRoundRobin Whether to play double round robin in groups
     * @return void
     */
    public function generateTournamentGroups(Competition $competition, int $groupCount, bool $doubleRoundRobin = false)
    {
        $teams = $competition->futsalTeams()->where('is_active', true)->get();
        $teamCount = $teams->count();

        if ($teamCount < $groupCount * 2) {
            throw new \Exception("Potrebno je najmanje " . ($groupCount * 2) . " timova za {$groupCount} grupe.");
        }

        // Shuffle teams for random distribution
        $shuffledTeams = $teams->shuffle();
        $teamsPerGroup = ceil($teamCount / $groupCount);

        // Create groups
        for ($i = 0; $i < $groupCount; $i++) {
            $groupName = chr(65 + $i); // A, B, C, etc.
            
            $group = TournamentGroup::create([
                'competition_id' => $competition->id,
                'name' => $groupName,
                'group_number' => $i + 1,
            ]);

            // Assign teams to this group
            $groupTeams = $shuffledTeams->slice($i * $teamsPerGroup, $teamsPerGroup);

            foreach ($groupTeams as $team) {
                // Create standing for this team in this group
                FutsalStanding::create([
                    'competition_id' => $competition->id,
                    'futsal_team_id' => $team->id,
                    'tournament_group_id' => $group->id,
                    'position' => 0,
                    'played' => 0,
                    'won' => 0,
                    'drawn' => 0,
                    'lost' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                    'goal_difference' => 0,
                    'points' => 0,
                ]);
            }

            // Generate matches for this group using Berger
            $this->generateGroupMatches($competition, $group, $groupTeams, $doubleRoundRobin);
        }
    }

    /**
     * Generate matches for a tournament group using Berger algorithm.
     *
     * @param TournamentGroup $group
     * @param bool $doubleRoundRobin
     * @return void
     */
    public function generateGroupMatches(TournamentGroup $group, bool $doubleRoundRobin = false)
    {
        $competition = $group->competition;
        $teams = $group->futsalTeams;

        if ($teams->count() < 2) {
            return;
        }

        $schedule = $this->generateBergerSchedule($teams->pluck('id')->toArray());

        // Create matches for first round robin
        $round = 1;
        foreach ($schedule as $roundMatches) {
            foreach ($roundMatches as $match) {
                FutsalMatch::create([
                    'competition_id' => $competition->id,
                    'tournament_group_id' => $group->id,
                    'home_team_id' => $match['home'],
                    'away_team_id' => $match['away'],
                    'status' => 'scheduled',
                    'round' => $round,
                    'phase' => 'group',
                ]);
            }
            $round++;
        }

        // If double round robin, create return matches
        if ($doubleRoundRobin) {
            foreach ($schedule as $roundMatches) {
                foreach ($roundMatches as $match) {
                    FutsalMatch::create([
                        'competition_id' => $competition->id,
                        'tournament_group_id' => $group->id,
                        'home_team_id' => $match['away'], // Reversed
                        'away_team_id' => $match['home'],  // Reversed
                        'status' => 'scheduled',
                        'round' => $round,
                        'phase' => 'group',
                    ]);
                }
                $round++;
            }
        }
    }

    /**
     * Update standings after a match is completed.
     * 
     * @param FutsalMatch $match
     * @return void
     */
    public function updateStandings(FutsalMatch $match)
    {
        if ($match->status !== 'completed' || $match->home_score === null || $match->away_score === null) {
            return;
        }

        DB::transaction(function () use ($match) {
            // Get standings for both teams
            $homeStanding = FutsalStanding::where('competition_id', $match->competition_id)
                ->where('futsal_team_id', $match->home_team_id)
                ->where(function ($query) use ($match) {
                    $query->whereNull('tournament_group_id')
                        ->orWhere('tournament_group_id', $match->tournament_group_id);
                })
                ->first();

            $awayStanding = FutsalStanding::where('competition_id', $match->competition_id)
                ->where('futsal_team_id', $match->away_team_id)
                ->where(function ($query) use ($match) {
                    $query->whereNull('tournament_group_id')
                        ->orWhere('tournament_group_id', $match->tournament_group_id);
                })
                ->first();

            if (!$homeStanding || !$awayStanding) {
                Log::error('Standings not found for match', [
                    'match_id' => $match->id,
                    'home_team_id' => $match->home_team_id,
                    'away_team_id' => $match->away_team_id,
                ]);
                return;
            }

            // Calculate match result
            if ($match->home_score > $match->away_score) {
                // Home win
                $homeStanding->increment('won');
                $homeStanding->increment('points', 3);
                $awayStanding->increment('lost');
            } elseif ($match->home_score < $match->away_score) {
                // Away win
                $awayStanding->increment('won');
                $awayStanding->increment('points', 3);
                $homeStanding->increment('lost');
            } else {
                // Draw
                $homeStanding->increment('drawn');
                $homeStanding->increment('points', 1);
                $awayStanding->increment('drawn');
                $awayStanding->increment('points', 1);
            }

            // Update goals and played
            $homeStanding->increment('played');
            $homeStanding->increment('goals_for', $match->home_score);
            $homeStanding->increment('goals_against', $match->away_score);

            $awayStanding->increment('played');
            $awayStanding->increment('goals_for', $match->away_score);
            $awayStanding->increment('goals_against', $match->home_score);

            // Update goal difference
            $homeStanding->update([
                'goal_difference' => $homeStanding->goals_for - $homeStanding->goals_against
            ]);
            $awayStanding->update([
                'goal_difference' => $awayStanding->goals_for - $awayStanding->goals_against
            ]);

            // Recalculate positions
            $this->updateStandingsPositions($match->competition_id, $match->tournament_group_id);
        });
    }

    /**
     * Update position numbers in standings table.
     * 
     * @param int $competitionId
     * @param int|null $groupId
     * @return void
     */
    private function updateStandingsPositions(int $competitionId, ?int $groupId = null)
    {
        $query = FutsalStanding::where('competition_id', $competitionId);

        if ($groupId) {
            $query->where('tournament_group_id', $groupId);
        } else {
            $query->whereNull('tournament_group_id');
        }

        $standings = $query->orderByStandings()->get();

        $position = 1;
        foreach ($standings as $standing) {
            $standing->update(['position' => $position++]);
        }
    }

    /**
     * Remove team from competition and void all their results.
     * 
     * @param Competition $competition
     * @param FutsalTeam $team
     * @return void
     */
    public function removeTeamFromCompetition(Competition $competition, FutsalTeam $team)
    {
        DB::transaction(function () use ($competition, $team) {
            // Get all matches involving this team
            $matches = FutsalMatch::where('competition_id', $competition->id)
                ->where(function ($query) use ($team) {
                    $query->where('home_team_id', $team->id)
                        ->orWhere('away_team_id', $team->id);
                })
                ->get();

            // Reverse standings updates for completed matches
            foreach ($matches as $match) {
                if ($match->status === 'completed') {
                    $this->reverseMatchStandings($match);
                }
            }

            // Delete all matches involving this team
            FutsalMatch::where('competition_id', $competition->id)
                ->where(function ($query) use ($team) {
                    $query->where('home_team_id', $team->id)
                        ->orWhere('away_team_id', $team->id);
                })
                ->delete();

            // Delete standings for this team
            FutsalStanding::where('competition_id', $competition->id)
                ->where('futsal_team_id', $team->id)
                ->delete();

            // Mark team as inactive
            $team->update(['is_active' => false]);

            // Recalculate all positions
            $this->updateStandingsPositions($competition->id);
        });
    }

    /**
     * Reverse standings updates for a match (used when voiding results).
     * 
     * @param FutsalMatch $match
     * @return void
     */
    private function reverseMatchStandings(FutsalMatch $match)
    {
        if ($match->status !== 'completed' || $match->home_score === null || $match->away_score === null) {
            return;
        }

        $homeStanding = FutsalStanding::where('competition_id', $match->competition_id)
            ->where('futsal_team_id', $match->home_team_id)
            ->where(function ($query) use ($match) {
                $query->whereNull('tournament_group_id')
                    ->orWhere('tournament_group_id', $match->tournament_group_id);
            })
            ->first();

        $awayStanding = FutsalStanding::where('competition_id', $match->competition_id)
            ->where('futsal_team_id', $match->away_team_id)
            ->where(function ($query) use ($match) {
                $query->whereNull('tournament_group_id')
                    ->orWhere('tournament_group_id', $match->tournament_group_id);
            })
            ->first();

        if (!$homeStanding || !$awayStanding) {
            return;
        }

        // Reverse points
        if ($match->home_score > $match->away_score) {
            $homeStanding->decrement('won');
            $homeStanding->decrement('points', 3);
            $awayStanding->decrement('lost');
        } elseif ($match->home_score < $match->away_score) {
            $awayStanding->decrement('won');
            $awayStanding->decrement('points', 3);
            $homeStanding->decrement('lost');
        } else {
            $homeStanding->decrement('drawn');
            $homeStanding->decrement('points', 1);
            $awayStanding->decrement('drawn');
            $awayStanding->decrement('points', 1);
        }

        // Reverse goals and played
        $homeStanding->decrement('played');
        $homeStanding->decrement('goals_for', $match->home_score);
        $homeStanding->decrement('goals_against', $match->away_score);

        $awayStanding->decrement('played');
        $awayStanding->decrement('goals_for', $match->away_score);
        $awayStanding->decrement('goals_against', $match->home_score);

        // Update goal difference
        $homeStanding->update([
            'goal_difference' => $homeStanding->goals_for - $homeStanding->goals_against
        ]);
        $awayStanding->update([
            'goal_difference' => $awayStanding->goals_for - $awayStanding->goals_against
        ]);
    }

    /**
     * Award walkover (3-0 default) to a team.
     * 
     * @param FutsalMatch $match
     * @param string $winner 'home' or 'away'
     * @return void
     */
    public function awardWalkover(FutsalMatch $match, string $winner)
    {
        DB::transaction(function () use ($match, $winner) {
            if ($winner === 'home') {
                $match->update([
                    'home_score' => 3,
                    'away_score' => 0,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            } else {
                $match->update([
                    'home_score' => 0,
                    'away_score' => 3,
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);
            }

            $this->updateStandings($match);
        });
    }

    /**
     * Generate knockout bracket from group stage results.
     * 
     * @param Competition $competition
     * @param int $teamsPerGroup Number of teams advancing from each group (usually 2)
     * @return void
     */
    public function generateKnockoutBracket(Competition $competition, int $teamsPerGroup = 2)
    {
        $groups = TournamentGroup::where('competition_id', $competition->id)
            ->orderBy('group_number')
            ->get();

        if ($groups->isEmpty()) {
            throw new \Exception('Nema grupa za generisanje knockout faze.');
        }

        // Collect top teams from each group
        $qualifiedTeams = [];
        foreach ($groups as $group) {
            $topTeams = FutsalStanding::where('competition_id', $competition->id)
                ->where('tournament_group_id', $group->id)
                ->orderByStandings()
                ->take($teamsPerGroup)
                ->get();

            foreach ($topTeams as $standing) {
                $qualifiedTeams[] = [
                    'team_id' => $standing->futsal_team_id,
                    'group' => $group->name,
                    'position' => $standing->position,
                ];
            }
        }

        $teamCount = count($qualifiedTeams);

        // Seed teams for knockout (group winners vs runners-up)
        $this->createKnockoutMatches($competition, $qualifiedTeams);
    }

    /**
     * Create knockout phase matches with proper seeding.
     * 
     * @param Competition $competition
     * @param array $teams
     * @return void
     */
    private function createKnockoutMatches(Competition $competition, array $teams)
    {
        $teamCount = count($teams);

        // Separate winners (position 1) and runners-up (position 2+)
        $winners = array_filter($teams, fn($t) => $t['position'] === 1);
        $runnersUp = array_filter($teams, fn($t) => $t['position'] > 1);

        // Simple pairing: winner of group A vs runner-up of group B, etc.
        $pairings = [];
        $winners = array_values($winners);
        $runnersUp = array_values($runnersUp);

        for ($i = 0; $i < min(count($winners), count($runnersUp)); $i++) {
            $pairings[] = [
                'home' => $winners[$i]['team_id'],
                'away' => $runnersUp[$i]['team_id'],
            ];
        }

        // Create semi-final matches
        $matchOrder = 1;
        foreach ($pairings as $pairing) {
            FutsalMatch::create([
                'competition_id' => $competition->id,
                'home_team_id' => $pairing['home'],
                'away_team_id' => $pairing['away'],
                'status' => 'scheduled',
                'phase' => 'knockout',
                'round' => 1, // Semi-finals
                'match_order' => $matchOrder++,
            ]);
        }
    }

    /**
     * Advance to next knockout round.
     * 
     * @param Competition $competition
     * @param int $currentRound
     * @return void
     */
    public function advanceKnockoutRound(Competition $competition, int $currentRound)
    {
        $matches = FutsalMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->where('round', $currentRound)
            ->where('status', 'completed')
            ->get();

        if ($matches->isEmpty()) {
            throw new \Exception('Nema završenih utakmica u trenutnoj rundi.');
        }

        // Get winners
        $winners = [];
        foreach ($matches as $match) {
            $winner = $match->winner;
            if ($winner) {
                $winners[] = $winner->id;
            }
        }

        if (count($winners) === 1) {
            // Tournament completed - we have a champion
            $competition->update([
                'current_phase' => 'completed',
                'status' => 'completed',
            ]);
            return;
        }

        // Create next round matches
        $nextRound = $currentRound + 1;
        $matchOrder = 1;

        for ($i = 0; $i < count($winners); $i += 2) {
            if (isset($winners[$i + 1])) {
                FutsalMatch::create([
                    'competition_id' => $competition->id,
                    'home_team_id' => $winners[$i],
                    'away_team_id' => $winners[$i + 1],
                    'status' => 'scheduled',
                    'phase' => 'knockout',
                    'round' => $nextRound,
                    'match_order' => $matchOrder++,
                ]);
            }
        }
    }
}
