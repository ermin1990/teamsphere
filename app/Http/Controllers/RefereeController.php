<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Standing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RefereeController extends Controller
{
    /**
     * Check if user has referee rights for a match.
     * Returns true if user is:
     * 1. Organization referee
     * 2. Match moderator (for league matches)
     * 3. Match referee (referee_user_id)
     */
    protected function hasRefereeRights($user, $competition, $match = null)
    {
        // Check if user is organization referee
        $isOrgReferee = $user->organizationUsers()
            ->where('organization_id', $competition->organization_id)
            ->where('role', 'referee')
            ->exists();

        // Check if user is match moderator (only for league matches)
        $isMatchModerator = $match && method_exists($match, 'moderator_id') && $match->moderator_id === $user->id;

        // Check if user is assigned as match referee
        $isMatchReferee = $match && $match->referee_user_id === $user->id;

        return $isOrgReferee || $isMatchModerator || $isMatchReferee;
    }

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

        // Get moderator/referee matches - show all matches where user is moderator or assigned referee
        $allModeratorMatches = LeagueMatch::where(function($query) use ($user) {
                $query->where('moderator_id', $user->id)
                      ->orWhere('referee_user_id', $user->id);
            })
            ->with(['league.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])
            ->orderByRaw("CASE 
                WHEN status = 'in_progress' THEN 1 
                WHEN status = 'scheduled' THEN 2 
                WHEN status = 'completed' THEN 3 
                WHEN status = 'forfeited' THEN 4 
                ELSE 5 END")
            ->orderBy('scheduled_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Get competition matches where user is assigned referee
        $competitionRefereeMatches = CompetitionMatch::where('referee_user_id', $user->id)
            ->with(['competition.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])
            ->orderByRaw("CASE 
                WHEN status = 'in_progress' THEN 1 
                WHEN status = 'scheduled' THEN 2 
                WHEN status = 'completed' THEN 3 
                WHEN status = 'forfeited' THEN 4 
                ELSE 5 END")
            ->orderBy('scheduled_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Combine all referee matches
        $allRefereeMatches = $allModeratorMatches->concat($competitionRefereeMatches);

        // Split into recent (completed/forfeited) and upcoming (scheduled/in_progress)
        $moderatorRecentMatches = $allRefereeMatches->filter(function($match) {
            return in_array($match->status, ['completed', 'forfeited']);
        })->take(5);

        $moderatorUpcomingMatches = $allRefereeMatches->filter(function($match) {
            return in_array($match->status, ['scheduled', 'in_progress']);
        })->take(5);

        return view('referee.dashboard', compact('organizations', 'leagues', 'recentMatches', 'totalMatches', 'completedMatches', 'inProgressMatches', 'scheduledMatches', 'moderatorRecentMatches', 'moderatorUpcomingMatches'));
    }

    /**
     * Display moderator dashboard with assigned matches.
     */
    public function moderatorDashboard()
    {
        $user = Auth::user();

        // Get all matches where user is assigned as moderator or referee
        $allModeratorMatches = LeagueMatch::where(function($query) use ($user) {
                $query->where('moderator_id', $user->id)
                      ->orWhere('referee_user_id', $user->id);
            })
            ->with(['league.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])
            ->orderByRaw("CASE 
                WHEN status = 'in_progress' THEN 1 
                WHEN status = 'scheduled' THEN 2 
                WHEN status = 'completed' THEN 3 
                WHEN status = 'forfeited' THEN 4 
                ELSE 5 END")
            ->orderBy('scheduled_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Get competition matches where user is assigned referee
        $competitionRefereeMatches = CompetitionMatch::where('referee_user_id', $user->id)
            ->with(['competition.organization', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])
            ->orderByRaw("CASE 
                WHEN status = 'in_progress' THEN 1 
                WHEN status = 'scheduled' THEN 2 
                WHEN status = 'completed' THEN 3 
                WHEN status = 'forfeited' THEN 4 
                ELSE 5 END")
            ->orderBy('scheduled_at', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Combine all referee matches
        $allRefereeMatches = $allModeratorMatches->concat($competitionRefereeMatches);

        // Separate into moderated (completed/forfeited) and assigned (scheduled/in_progress)
        $moderatedMatches = $allRefereeMatches->filter(function($match) {
            return in_array($match->status, ['completed', 'forfeited']);
        });

        $assignedMatches = $allRefereeMatches->filter(function($match) {
            return in_array($match->status, ['scheduled', 'in_progress']);
        });

        return view('referee.moderator-dashboard', compact('moderatedMatches', 'assignedMatches'));
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

        // Check if user has referee rights for this match
        if (!$this->hasRefereeRights($user, $league, $match)) {
            abort(403, 'You are not authorized to view this match.');
        }

        $isReferee = true;

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404, 'Match not found in this league.');
        }

        $organization = $league->organization;
        $isOwner = $organization->user_id === $user->id;

        // Load teams with players for team-based leagues
        if ($league->is_team_based) {
            $match->load(['homeTeam.players', 'awayTeam.players']);
        }

        // Load audit relationships and match officials
        $match->load(['moderator', 'referee', 'table', 'editedBy', 'completedBy']);

        return view('organizations.leagues.matches.show', compact('organization', 'league', 'match', 'isOwner', 'isReferee'));
    }

    /**
     * Show form to edit match result.
     */
    public function editMatch(League $league, LeagueMatch $match)
    {
        $user = Auth::user();

        // Check if user has referee rights for this match
        if (!$this->hasRefereeRights($user, $league, $match)) {
            abort(403, 'You are not authorized to edit this match.');
        }

        $isReferee = true;

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404, 'Match not found in this league.');
        }

        $organization = $league->organization;
        $isOwner = $organization->user_id === $user->id;

        return view('organizations.leagues.matches.edit', compact('organization', 'league', 'match', 'isOwner', 'isReferee'));
    }

    /**
     * Show live scoring interface for match.
     */
    public function liveScore(League $league, LeagueMatch $match)
    {
        $user = Auth::user();

        // Check if user has referee rights for this match
        if (!$this->hasRefereeRights($user, $league, $match)) {
            abort(403, 'You are not authorized to manage this match.');
        }

        $isReferee = true;

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404, 'Match not found in this league.');
        }

        $organization = $league->organization;
        $isOwner = $organization->user_id === $user->id;

        // Load teams with players for team-based leagues
        if ($league->is_team_based) {
            $match->load(['homeTeam.players', 'awayTeam.players']);
        }

        // Don't auto-start the match here - let the user choose server first
        // Match will be started when first server is selected in LiveScore component

        return view('organizations.leagues.matches.live', compact('organization', 'league', 'match', 'isOwner', 'isReferee'));
    }

    /**
     * Update match result.
     */
    public function updateMatch(Request $request, League $league, LeagueMatch $match)
    {
        $user = Auth::user();

        // Check if user has referee rights for this match
        if (!$this->hasRefereeRights($user, $league, $match)) {
            abort(403, 'You are not authorized to update this match.');
        }

        $isReferee = true;

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404, 'Match not found in this league.');
        }

        // Check if user is owner (for moderator assignment)
        $isOwner = $league->organization->user_id === $user->id;

        $request->validate([
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
            'forfeited_by' => 'nullable|in:home,away',
            'moderator_id' => 'nullable|exists:users,id',
            'referee_user_id' => 'nullable|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
        ]);

        // Check moderator assignment permissions
        if ($request->filled('moderator_id') && !$isOwner) {
            abort(403, 'Only organization owners can assign moderators.');
        }

        // If moderator_id is provided, ensure they are a referee for this organization
        if ($request->filled('moderator_id')) {
            $isValidReferee = $league->organization->organizationUsers()
                ->where('user_id', $request->moderator_id)
                ->where('role', 'referee')
                ->exists();
            if (!$isValidReferee) {
                return back()->withErrors(['moderator_id' => 'Selected user is not a referee for this organization.']);
            }
        }

        // Check if table belongs to this organization
        if ($request->filled('table_id')) {
            $isValidTable = \App\Models\Table::where('id', $request->table_id)
                ->where('organization_id', $league->organization_id)
                ->exists();
            if (!$isValidTable) {
                return back()->withErrors(['table_id' => 'Selected table does not belong to this organization.']);
            }
        }

        $updateData = $request->only(['home_score', 'away_score', 'status', 'forfeited_by', 'moderator_id', 'referee_user_id', 'table_id']);

        // Set played_at when match is completed or cancelled
        if (in_array($request->status, ['completed', 'cancelled']) && !$match->played_at) {
            $updateData['played_at'] = now();
        }

        // Set audit fields
        $updateData['edited_by'] = $user->id;
        $updateData['edited_at'] = now();

        // Set completed_by and completed_at if status is being changed to completed
        if ($request->status === 'completed' && $match->status !== 'completed') {
            $updateData['completed_by'] = $user->id;
            $updateData['completed_at'] = now();
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

        // Check if user has referee rights for this match
        if (!$this->hasRefereeRights($user, $league, $match)) {
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
            'edited_by' => $user->id,
            'edited_at' => now(),
            'completed_by' => null,
            'completed_at' => null,
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

    /**
     * Show competition match details for referee.
     */
    public function showCompetitionMatch(Competition $competition, CompetitionMatch $match)
    {
        $user = Auth::user();

        // Check if user has referee rights for this match
        if (!$this->hasRefereeRights($user, $competition, $match)) {
            abort(403, 'You are not authorized to view this match.');
        }

        $isReferee = true;

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404, 'Match not found in this competition.');
        }

        $organization = $competition->organization;
        $isOwner = $organization->user_id === $user->id;

        // Load teams with players for team-based competitions
        if ($competition->is_team_based) {
            $match->load(['homeTeam.players', 'awayTeam.players']);
        }

        // Load audit relationships and match officials
        $match->load(['referee', 'table']);

        return view('organizations.competitions.matches.show', compact('organization', 'competition', 'match', 'isOwner', 'isReferee'));
    }

    /**
     * Show form to edit competition match result.
     */
    public function editCompetitionMatch(Competition $competition, CompetitionMatch $match)
    {
        $user = Auth::user();

        // Check if user has referee rights for this match
        if (!$this->hasRefereeRights($user, $competition, $match)) {
            abort(403, 'You are not authorized to edit this match.');
        }

        $isReferee = true;

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404, 'Match not found in this competition.');
        }

        $organization = $competition->organization;
        $isOwner = $organization->user_id === $user->id;

        return view('organizations.competitions.matches.edit', compact('organization', 'competition', 'match', 'isOwner', 'isReferee'));
    }

    /**
     * Show live scoring interface for competition match.
     */
    public function liveCompetitionScore(Competition $competition, CompetitionMatch $match)
    {
        $user = Auth::user();

        // Check if user has referee rights for this match
        if (!$this->hasRefereeRights($user, $competition, $match)) {
            abort(403, 'You are not authorized to manage this match.');
        }

        $isReferee = true;

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404, 'Match not found in this competition.');
        }

        $organization = $competition->organization;
        $isOwner = $organization->user_id === $user->id;

        // Load teams with players for team-based competitions
        if ($competition->is_team_based) {
            $match->load(['homeTeam.players', 'awayTeam.players']);
        }

        return view('organizations.competitions.matches.live', compact('organization', 'competition', 'match', 'isOwner', 'isReferee'));
    }

    /**
     * Update competition match result.
     */
    public function updateCompetitionMatch(Request $request, Competition $competition, CompetitionMatch $match)
    {
        $user = Auth::user();

        // Check if user has referee rights for this match
        if (!$this->hasRefereeRights($user, $competition, $match)) {
            abort(403, 'You are not authorized to update this match.');
        }

        $isReferee = true;

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404, 'Match not found in this competition.');
        }

        $request->validate([
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled,forfeited',
            'forfeited_by' => 'nullable|in:home,away',
            'referee_user_id' => 'nullable|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
        ]);

        // Check if table belongs to this organization
        if ($request->filled('table_id')) {
            $isValidTable = \App\Models\Table::where('id', $request->table_id)
                ->where('organization_id', $competition->organization_id)
                ->exists();
            if (!$isValidTable) {
                return back()->withErrors(['table_id' => 'Selected table does not belong to this organization.']);
            }
        }

        $updateData = $request->only(['home_score', 'away_score', 'status', 'forfeited_by', 'referee_user_id', 'table_id']);

        // Set played_at when match is completed or forfeited
        if (in_array($request->status, ['completed', 'forfeited']) && !$match->played_at) {
            $updateData['played_at'] = now();
        }

        $match->update($updateData);

        return redirect()->route('referee.competition.match.show', [$competition, $match])
            ->with('success', 'Match result updated successfully.');
    }

    /**
     * Reset competition match to initial state.
     */
    public function resetCompetitionMatch(Request $request, Competition $competition, CompetitionMatch $match)
    {
        $user = Auth::user();

        // Check if user has referee rights for this match
        if (!$this->hasRefereeRights($user, $competition, $match)) {
            abort(403, 'You are not authorized to reset this match.');
        }

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404, 'Match not found in this competition.');
        }

        // Reset match to initial state
        $match->update([
            'status' => 'scheduled',
            'home_score' => 0,
            'away_score' => 0,
            'sets' => [],
            'played_at' => null,
            'forfeited_by' => null,
        ]);

        return redirect()->route('referee.competition.match.show', [$competition, $match])
            ->with('success', 'Match has been reset to initial state.');
    }
}
