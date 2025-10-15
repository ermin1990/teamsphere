<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Standing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefereeController extends Controller
{
    /**
     * Display referee dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get organizations where user is referee
        $organizationUsers = $user->organizationUsers()->where('role', 'referee')->with('organization')->get();

        // Get organizations
        $organizations = $organizationUsers->pluck('organization');

        // Get leagues from those organizations
        $leagues = collect();
        foreach ($organizationUsers as $orgUser) {
            $orgLeagues = $orgUser->organization->leagues()->with(['sport', 'matches'])->get();
            $leagues = $leagues->merge($orgLeagues);
        }

        // Get recent matches that need referee attention
        $recentMatches = collect();
        foreach ($leagues as $league) {
            $leagueMatches = $league->matches()
                ->with(['homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])
                ->where('scheduled_at', '>=', now()->subDays(7))
                ->orderBy('scheduled_at', 'desc')
                ->limit(10)
                ->get();
            $recentMatches = $recentMatches->merge($leagueMatches);
        }

        // Calculate statistics
        $totalMatches = $leagues->sum(function($league) {
            return $league->matches->count();
        });

        $completedMatches = $leagues->sum(function($league) {
            return $league->matches->where('status', 'completed')->count();
        });

        $inProgressMatches = $leagues->sum(function($league) {
            return $league->matches->where('status', 'in_progress')->count();
        });

        $scheduledMatches = $leagues->sum(function($league) {
            return $league->matches->where('status', 'scheduled')->count();
        });

        return view('referee.dashboard', compact('organizations', 'leagues', 'recentMatches', 'totalMatches', 'completedMatches', 'inProgressMatches', 'scheduledMatches'));
    }

    /**
     * Display referee's leagues.
     */
    public function leagues()
    {
        $user = Auth::user();

        // Get organizations where user is referee
        $organizationUsers = $user->organizationUsers()->where('role', 'referee')->with('organization')->get();

        // Get leagues from those organizations
        $leagues = collect();
        foreach ($organizationUsers as $orgUser) {
            $orgLeagues = $orgUser->organization->leagues()->with(['sport', 'matches'])->get();
            $leagues = $leagues->merge($orgLeagues);
        }

        return view('referee.leagues', compact('leagues'));
    }

    /**
     * Display matches for a specific league.
     */
    public function leagueMatches(League $league)
    {
        $user = Auth::user();

        // Check if user is referee for this league's organization
        $isReferee = $user->organizationUsers()
            ->where('organization_id', $league->organization_id)
            ->where('role', 'referee')
            ->exists();

        if (!$isReferee) {
            abort(403, 'You are not authorized to view this league.');
        }

        $matches = $league->matches()
            ->with(['homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])
            ->orderBy('round', 'asc')
            ->orderBy('scheduled_at', 'asc')
            ->get()
            ->groupBy('round');

        return view('referee.league-matches', compact('league', 'matches'));
    }

    /**
     * Show match details for referee.
     */
    public function showMatch(League $league, LeagueMatch $match)
    {
        $user = Auth::user();

        // Check if user is referee for this league's organization
        $isReferee = $user->organizationUsers()
            ->where('organization_id', $league->organization_id)
            ->where('role', 'referee')
            ->exists();

        if (!$isReferee) {
            abort(403, 'You are not authorized to view this match.');
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404, 'Match not found in this league.');
        }

        $organization = $league->organization;

        // Load teams with players for team-based leagues
        if ($league->is_team_based) {
            $match->load(['homeTeam.players', 'awayTeam.players']);
        }

        return view('organizations.leagues.matches.show', compact('organization', 'league', 'match'));
    }

    /**
     * Show form to edit match result.
     */
    public function editMatch(League $league, LeagueMatch $match)
    {
        $user = Auth::user();

        // Check if user is referee for this league's organization
        $isReferee = $user->organizationUsers()
            ->where('organization_id', $league->organization_id)
            ->where('role', 'referee')
            ->exists();

        if (!$isReferee) {
            abort(403, 'You are not authorized to edit this match.');
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404, 'Match not found in this league.');
        }

        $organization = $league->organization;

        return view('organizations.leagues.matches.edit', compact('organization', 'league', 'match'));
    }

    /**
     * Show live scoring interface for match.
     */
    public function liveScore(League $league, LeagueMatch $match)
    {
        $user = Auth::user();

        // Check if user is referee for this league's organization
        $isReferee = $user->organizationUsers()
            ->where('organization_id', $league->organization_id)
            ->where('role', 'referee')
            ->exists();

        if (!$isReferee) {
            abort(403, 'You are not authorized to manage this match.');
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404, 'Match not found in this league.');
        }

        $organization = $league->organization;

        // Load teams with players for team-based leagues
        if ($league->is_team_based) {
            $match->load(['homeTeam.players', 'awayTeam.players']);
        }

        // Start the match if not already started
        if ($match->status === 'scheduled') {
            $match->update([
                'status' => 'in_progress',
                'played_at' => now(),
            ]);
        }

        return view('organizations.leagues.matches.live', compact('organization', 'league', 'match'));
    }

    /**
     * Update match result.
     */
    public function updateMatch(Request $request, League $league, LeagueMatch $match)
    {
        $user = Auth::user();

        // Check if user is referee for this league's organization
        $isReferee = $user->organizationUsers()
            ->where('organization_id', $league->organization_id)
            ->where('role', 'referee')
            ->exists();

        if (!$isReferee) {
            abort(403, 'You are not authorized to update this match.');
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404, 'Match not found in this league.');
        }

        $request->validate([
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'forfeited_by' => 'nullable|in:home,away',
        ]);

        $updateData = $request->only(['home_score', 'away_score', 'status', 'forfeited_by']);

        // Set played_at when match is completed or cancelled
        if (in_array($request->status, ['completed', 'cancelled']) && !$match->played_at) {
            $updateData['played_at'] = now();
        }

        $match->update($updateData);

        return redirect()->route('referee.match.show', [$league, $match])
            ->with('success', 'Match result updated successfully.');
    }

    /**
     * Reset match to initial state.
     */
    public function resetMatch(Request $request, League $league, LeagueMatch $match)
    {
        $user = Auth::user();

        // Check if user is referee for this league's organization
        $isReferee = $user->organizationUsers()
            ->where('organization_id', $league->organization_id)
            ->where('role', 'referee')
            ->exists();

        if (!$isReferee) {
            abort(403, 'You are not authorized to reset this match.');
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404, 'Match not found in this league.');
        }

        // Check if match was previously completed or forfeited
        $wasCompleted = in_array($match->status, ['completed', 'forfeited']);

        // Reset match to initial state
        $match->update([
            'status' => 'scheduled',
            'home_score' => 0,
            'away_score' => 0,
            'sets' => [],
            'played_at' => null,
            'forfeited_by' => null,
        ]);

        // Update standings if match was previously completed/forfeited
        if ($wasCompleted) {
            $this->updateStandings($league);
        }

        return redirect()->route('referee.match.show', [$league, $match])
            ->with('success', 'Match has been reset to initial state.');
    }

    /**
     * Update league standings after match changes.
     */
    private function updateStandings(League $league)
    {
        // Reset all standings for this league
        Standing::where('league_id', $league->id)->update([
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
        $completedMatches = LeagueMatch::where('league_id', $league->id)
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

            $homeStanding = Standing::where('league_id', $league->id)
                ->where($league->is_team_based ? 'team_id' : 'player_id', $homeParticipantId)
                ->first();

            $awayStanding = Standing::where('league_id', $league->id)
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
                    // Regular completed match
                    $homeStanding->increment('goals_for', $match->home_score);
                    $homeStanding->increment('goals_against', $match->away_score);
                    $awayStanding->increment('goals_for', $match->away_score);
                    $awayStanding->increment('goals_against', $match->home_score);

                    if ($match->home_score > $match->away_score) {
                        $homeStanding->increment('won');
                        $awayStanding->increment('lost');
                        $homeStanding->increment('points', $league->settings['points_win'] ?? 3);
                        $awayStanding->increment('points', $league->settings['points_loss'] ?? 1);
                    } elseif ($match->away_score > $match->home_score) {
                        $awayStanding->increment('won');
                        $homeStanding->increment('lost');
                        $awayStanding->increment('points', $league->settings['points_win'] ?? 3);
                        $homeStanding->increment('points', $league->settings['points_loss'] ?? 1);
                    } else {
                        $homeStanding->increment('drawn');
                        $awayStanding->increment('drawn');
                        $homeStanding->increment('points', $league->settings['points_draw'] ?? 0);
                        $awayStanding->increment('points', $league->settings['points_draw'] ?? 0);
                    }
                }

                // Update goal difference
                $homeStanding->goal_difference = $homeStanding->goals_for - $homeStanding->goals_against;
                $awayStanding->goal_difference = $awayStanding->goals_for - $awayStanding->goals_against;

                $homeStanding->save();
                $awayStanding->save();
            }
        }

        // Update positions based on points, goal difference, goals for
        $standings = Standing::where('league_id', $league->id)
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->get();

        $position = 1;
        foreach ($standings as $standing) {
            $standing->update(['position' => $position++]);
        }
    }
}
