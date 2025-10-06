<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Standing;
use App\Models\Team;
use App\Services\LeagueScheduler;

class LeagueController extends Controller
{
    /**
     * Show the form for creating a new league.
     */
    public function create(Organization $organization)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        $sports = Sport::all();

        return view('organizations.leagues.create', compact('organization', 'sports'));
    }

    /**
     * Display the specified league.
     */
    public function show(Organization $organization, League $league)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        $league->load('sport', 'teams.players');
        $organization->load('players');

        return view('organizations.leagues.show', compact('organization', 'league'));
    }

    /**
     * Store a newly created league.
     */
    public function store(Request $request, Organization $organization)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if organization can create more leagues
        if (!$organization->canCreateMoreLeagues()) {
            return back()->withErrors(['general' => __('You have reached the maximum number of leagues for this organization.')]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sport_id' => ['required', 'exists:sports,id'],
            'is_team_based' => ['required', 'boolean'],
            'start_date' => ['nullable', 'date', 'after:today'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
        ]);

        $sport = Sport::findOrFail($request->sport_id);

        $league = League::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name . '-' . time()),
            'description' => $request->description,
            'organization_id' => $organization->id,
            'sport_id' => $request->sport_id,
            'is_team_based' => $request->is_team_based,
            'status' => 'draft',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_teams' => null, // Will be configured later
            'settings' => [], // Empty settings - will be configured later
            'is_active' => true,
        ]);

        return redirect()->route('organizations.show', $organization)->with('success', __('League created successfully!'));
    }

    /**
     * Update the specified league rules.
     */
    public function update(Request $request, Organization $organization, League $league)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure league is in draft status
        if ($league->status !== 'draft') {
            return back()->withErrors(['general' => __('Cannot modify rules for a league that has already started.')]);
        }

        $settings = $league->settings ?? [];

        if ($request->has('format')) {
            $request->validate([
                'format' => ['required', 'string', 'in:round_robin,dual_robin,dual_robin_knockout,knockout'],
            ]);

            $settings['format'] = $request->format;
        }

        if ($request->has(['points_win', 'points_draw', 'points_loss'])) {
            $request->validate([
                'points_win' => ['required', 'integer', 'min:1', 'max:10'],
                'points_draw' => ['nullable', 'integer', 'min:0', 'max:5'],
                'points_loss' => ['required', 'integer', 'min:0', 'max:5'],
            ]);

            $settings['points_win'] = $request->points_win;
            $settings['points_draw'] = $request->points_draw ?? 1;
            $settings['points_loss'] = $request->points_loss;
        }

        if ($request->has('sets_to_win') && (!$league->is_team_based || $league->sport->slug === 'stoni-tenis')) {
            $request->validate([
                'sets_to_win' => ['required', 'integer', 'min:2', 'max:4'],
            ]);

            $settings['sets_to_win'] = $request->sets_to_win;
        }

        $league->update(['settings' => $settings]);

        return back()->with('success', __('League rules updated successfully!'));
    }

    /**
     * Remove the specified league.
     */
    public function destroy(Organization $organization, League $league)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Delete the league
        $league->delete();

        return redirect()->route('organizations.show', $organization)->with('success', __('League deleted successfully!'));
    }

    /**
     * Get league settings form based on sport and team/individual setting.
     */
    public function getSettingsForm(Request $request)
    {
        $sport = Sport::findOrFail($request->sport_id);
        $hasDates = $request->boolean('has_dates');
        $isTeamBased = $request->boolean('is_team_based');

        return view('organizations.leagues.partials.settings-form', compact('sport', 'hasDates', 'isTeamBased'));
    }

    /**
     * Add a team to the league.
     */
    public function addTeam(Request $request, Organization $organization, League $league)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure league is team-based
        if (!$league->is_team_based) {
            return back()->withErrors(['general' => __('This league does not support teams.')]);
        }

        // Check if league can accept more teams
        if (!$league->canAcceptMoreTeams()) {
            return back()->withErrors(['general' => __('This league has reached the maximum number of teams.')]);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $team = Team::create([
            'name' => $request->name,
            'description' => $request->description,
            'league_id' => $league->id,
            'status' => 'active',
        ]);

        return back()->with('success', __('Team added successfully!'));
    }

    /**
     * Add multiple players to the league.
     */
    public function addPlayers(Request $request, Organization $organization, League $league)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure league is in draft status
        if ($league->status !== 'draft') {
            return back()->withErrors(['general' => __('Cannot add players to a league that has already started.')]);
        }

        $request->validate([
            'player_ids' => ['required', 'array', 'min:1'],
            'player_ids.*' => ['required', 'exists:players,id'],
        ]);

        $playerIds = $request->player_ids;

        // Get players and validate they belong to the organization
        $players = \App\Models\Player::whereIn('id', $playerIds)
            ->where('organization_id', $organization->id)
            ->get();

        if ($players->count() !== count($playerIds)) {
            return back()->withErrors(['general' => __('One or more selected players do not belong to this organization.')]);
        }

        // Prepare sync data with joined_at timestamp
        $syncData = [];
        foreach ($playerIds as $playerId) {
            $syncData[$playerId] = ['joined_at' => now()];
        }

        // Add players to the league (syncWithoutDetaching won't remove existing ones)
        $league->players()->syncWithoutDetaching($syncData);

        $addedCount = count($playerIds);
        return back()->with('success', __('Players added to league successfully! :count players added.', ['count' => $addedCount]));
    }

    /**
     * Start the league.
     */
    public function startLeague(Request $request, Organization $organization, League $league)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure league is in draft status
        if ($league->status !== 'draft') {
            return back()->withErrors(['general' => __('League has already been started.')]);
        }

        // Validate that league has participants
        $participantCount = $league->is_team_based ? $league->teams->count() : $league->players->count();
        if ($participantCount < 2) {
            return back()->withErrors(['general' => __('League must have at least 2 participants to start.')]);
        }

        try {
            // Generate matches based on format
            $this->generateMatches($league);

            // Generate standings table
            $this->generateStandings($league);

            // Start the league
            $league->update(['status' => 'active']);

            return redirect()->route('organizations.leagues.show', [$organization, $league])
                           ->with('success', __('League started successfully!'));
        } catch (\Exception $e) {
            \Log::error('Error starting league: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Error starting league: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate matches for the league based on format.
     */
    private function generateMatches(League $league)
    {
        $format = $league->settings['format'] ?? 'round_robin';

        switch ($format) {
            case 'round_robin':
            case 'dual_robin':
            case 'dual_robin_knockout':
                // Use the LeagueScheduler service for round-robin formats
                LeagueScheduler::generateRoundRobinSchedule($league);
                break;
            case 'knockout':
                // Generate knockout bracket
                $this->generateKnockoutMatches($league);
                break;
        }
    }

    /**
     * Generate round-robin matches.
     */
    private function generateRoundRobinMatches(array $participantIds, bool $doubleRound = false): array
    {
        $matches = [];
        $numParticipants = count($participantIds);

        if ($numParticipants < 2) {
            return $matches; // Not enough participants
        }

        // Simple round-robin: each participant plays every other participant exactly once
        $participants = $participantIds;

        // For each participant, pair them with all others they haven't played yet
        $playedPairs = [];
        $round = 0;

        while (true) {
            $roundMatches = [];
            $usedParticipants = [];

            // Find pairs for this round
            for ($i = 0; $i < $numParticipants; $i++) {
                if (in_array($participants[$i], $usedParticipants)) continue;

                for ($j = $i + 1; $j < $numParticipants; $j++) {
                    if (in_array($participants[$j], $usedParticipants)) continue;

                    $pairKey = min($participants[$i], $participants[$j]) . '-' . max($participants[$i], $participants[$j]);

                    if (!in_array($pairKey, $playedPairs)) {
                        $roundMatches[] = ['home' => $participants[$i], 'away' => $participants[$j]];
                        $playedPairs[] = $pairKey;
                        $usedParticipants[] = $participants[$i];
                        $usedParticipants[] = $participants[$j];
                        break;
                    }
                }
            }

            if (empty($roundMatches)) {
                break; // No more matches to schedule
            }

            $matches[] = $roundMatches;
            $round++;
        }

        // If double round, add reverse fixtures
        if ($doubleRound) {
            $reverseMatches = [];
            foreach ($matches as $roundMatches) {
                $reverseRound = [];
                foreach ($roundMatches as $match) {
                    $reverseRound[] = ['home' => $match['away'], 'away' => $match['home']];
                }
                $reverseMatches[] = $reverseRound;
            }
            $matches = array_merge($matches, $reverseMatches);
        }

        return $matches;
    }

    /**
     * Generate knockout matches.
     */
    private function generateKnockoutMatches(League $league)
    {
        $participants = $league->is_team_based ? $league->teams : $league->players;
        $participantIds = $participants->pluck('id')->toArray();

        shuffle($participantIds); // Randomize bracket
        $matches = [];

        // First round
        $firstRoundMatches = [];
        for ($i = 0; $i < count($participantIds); $i += 2) {
            if (isset($participantIds[$i + 1])) {
                LeagueMatch::create([
                    'league_id' => $league->id,
                    'home_team_id' => $league->is_team_based ? $participantIds[$i] : null,
                    'away_team_id' => $league->is_team_based ? $participantIds[$i + 1] : null,
                    'home_player_id' => !$league->is_team_based ? $participantIds[$i] : null,
                    'away_player_id' => !$league->is_team_based ? $participantIds[$i + 1] : null,
                    'round' => 1,
                    'status' => 'scheduled',
                ]);
            }
        }
    }

    /**
     * Generate standings table for the league.
     */
    private function generateStandings(League $league)
    {
        $participants = $league->is_team_based ? $league->teams : $league->players;

        $position = 1;
        foreach ($participants as $participant) {
            Standing::create([
                'league_id' => $league->id,
                'team_id' => $league->is_team_based ? $participant->id : null,
                'player_id' => !$league->is_team_based ? $participant->id : null,
                'position' => $position++,
            ]);
        }
    }

    /**
     * Update standings table based on completed matches.
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
     * Show a specific match.
     */
    public function showMatch(Request $request, Organization $organization, League $league, LeagueMatch $match)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404);
        }

        return view('organizations.leagues.matches.show', compact('organization', 'league', 'match'));
    }

    /**
     * Show the form for editing match results.
     */
    public function editMatch(Request $request, Organization $organization, League $league, LeagueMatch $match)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404);
        }

        return view('organizations.leagues.matches.edit', compact('organization', 'league', 'match'));
    }

    /**
     * Update match results.
     */
    public function updateMatch(Request $request, Organization $organization, League $league, LeagueMatch $match)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404);
        }

        // Validation
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,forfeited,cancelled',
            'home_score' => 'nullable|numeric|min:0',
            'away_score' => 'nullable|numeric|min:0',
            'forfeited_by' => 'nullable|in:home,away',
        ]);

        // Prepare update data
        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'scheduled') {
            // Reset everything
            $updateData['home_score'] = 0;
            $updateData['away_score'] = 0;
            $updateData['forfeited_by'] = null;
            $updateData['played_at'] = null;
        } else {
            // Update scores if provided
            if (isset($validated['home_score'])) {
                $updateData['home_score'] = $validated['home_score'];
            }
            if (isset($validated['away_score'])) {
                $updateData['away_score'] = $validated['away_score'];
            }
            if (isset($validated['forfeited_by'])) {
                $updateData['forfeited_by'] = $validated['forfeited_by'];
            }
            
            // Set played_at for completed or forfeited matches
            if (in_array($validated['status'], ['completed', 'forfeited'])) {
                $updateData['played_at'] = now();
            }
        }

        // Update match
        $match->update($updateData);

        // Update standings if needed
        if (in_array($validated['status'], ['completed', 'forfeited']) ||
            ($validated['status'] === 'cancelled' && ($validated['home_score'] > 0 || $validated['away_score'] > 0))) {
            $this->updateStandings($league);
        }

        return redirect()->route('organizations.leagues.matches.show', [$organization, $league, $match])
            ->with('success', 'Match updated successfully!');
    }

    /**
     * Show live scoring interface for a match.
     */
    public function liveScore(Request $request, Organization $organization, League $league, LeagueMatch $match)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404);
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
     * Update live match score.
     */
    public function updateLiveScore(Request $request, Organization $organization, League $league, LeagueMatch $match)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404);
        }

        $validated = $request->validate([
            'home_score' => 'required_unless:action,forfeit_match,start_match|integer|min:0',
            'away_score' => 'required_unless:action,forfeit_match,start_match|integer|min:0',
            'sets' => 'nullable|array',
            'sets.*.home' => 'required|integer|min:0',
            'sets.*.away' => 'required|integer|min:0',
            'forfeited_by' => 'required_if:action,forfeit_match|in:home,away',
            'first_server' => 'required_if:action,start_match|in:home,away',
            'action' => 'required|in:update_score,complete_match,pause_match,forfeit_match,start_match',
        ]);

        if ($validated['action'] === 'start_match') {
            $match->update([
                'status' => 'in_progress',
                'played_at' => now(),
                'first_server' => $validated['first_server'],
                'current_server' => $validated['first_server'],
            ]);

            return response()->json(['status' => 'started', 'message' => 'Match started successfully.']);
        } elseif ($validated['action'] === 'complete_match') {
            $match->update([
                'home_score' => $validated['home_score'],
                'away_score' => $validated['away_score'],
                'sets' => $validated['sets'] ?? [],
                'status' => 'completed',
            ]);

            // Update standings
            $this->updateStandings($league);

            return response()->json(['status' => 'completed', 'message' => 'Match completed successfully.']);
        } elseif ($validated['action'] === 'forfeit_match') {
            $match->update([
                'home_score' => $validated['forfeited_by'] === 'away' ? 1 : 0,
                'away_score' => $validated['forfeited_by'] === 'home' ? 1 : 0,
                'sets' => [],
                'status' => 'forfeited',
                'forfeited_by' => $validated['forfeited_by'],
            ]);

            // Update standings
            $this->updateStandings($league);

            return response()->json(['status' => 'forfeited', 'message' => 'Match forfeited successfully.']);
        } elseif ($validated['action'] === 'pause_match') {
            $match->update(['status' => 'scheduled']);
            return response()->json(['status' => 'paused', 'message' => 'Match paused.']);
        } else {
            // Update current score
            $match->update([
                'home_score' => $validated['home_score'],
                'away_score' => $validated['away_score'],
                'sets' => $validated['sets'] ?? [],
            ]);

            return response()->json(['status' => 'updated', 'message' => 'Score updated.']);
        }
    }

    /**
     * Reset match to initial state.
     */
    public function resetMatch(Request $request, Organization $organization, League $league, LeagueMatch $match)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure match belongs to league
        if ($match->league_id !== $league->id) {
            abort(404);
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

        return redirect()->route('organizations.leagues.matches.show', [$organization, $league, $match])
            ->with('success', 'Match has been reset to initial state.');
    }

    /**
     * Reset league to draft status and regenerate matches.
     */
    public function resetLeague(Request $request, Organization $organization, League $league)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league belongs to organization
        if ($league->organization_id !== $organization->id) {
            abort(404);
        }

        try {
            // Delete all existing matches
            $league->matches()->delete();

            // Delete all standings
            $league->standings()->delete();

            // Reset league to draft status
            $league->update(['status' => 'draft']);

            return redirect()->route('organizations.leagues.show', [$organization, $league])
                           ->with('success', __('League has been reset to draft status. You can now restart it with correct match pairings.'));
        } catch (\Exception $e) {
            \Log::error('Error resetting league: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Error resetting league: ' . $e->getMessage()]);
        }
    }
}