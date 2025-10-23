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
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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

        $sports = Sport::active()->get();

        return view('organizations.leagues.create', compact('organization', 'sports'));
    }

    /**
     * Display the specified league.
     */
    public function show(League $league)
    {
        $organization = $league->organization;

        // Allow access if user owns the organization OR is registered as a player in it OR is a referee
        $isOwner = $organization->user_id === auth()->id();
        $isPlayer = $organization->players()->where('user_id', auth()->id())->exists();
        $isReferee = auth()->user()->organizationUsers()
            ->where('organization_id', $organization->id)
            ->where('role', 'referee')
            ->exists();

        if (!$isOwner && !$isPlayer && !$isReferee) {
            abort(403);
        }

        $league->load('sport', 'teams.players', 'matches.homeTeam', 'matches.awayTeam', 'matches.homePlayer', 'matches.awayPlayer', 'players', 'standings');
        $organization->load('players');

        return view('organizations.leagues.show', compact('organization', 'league', 'isOwner', 'isPlayer', 'isReferee'));
    }

    /**
     * Update team information.
     */
    public function updateTeam(Request $request, League $league, Team $team)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure team belongs to league
        if ($team->competition_id !== $league->id) {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $team->update($request->only(['name', 'description']));

        return back()->with('success', __('Team updated successfully!'));
    }

    /**
     * Delete a team from the league.
     */
    public function deleteTeam(Request $request, League $league, Team $team)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure team belongs to league
        if ($team->competition_id !== $league->id) {
            abort(404);
        }

        // Ensure league is in draft status
        if ($league->status !== 'draft') {
            return back()->withErrors(['general' => __('Cannot delete teams from a league that has already started.')]);
        }

        $team->delete();

        return back()->with('success', __('Team deleted successfully!'));
    }

    /**
     * Add a player to a specific team.
     */
    public function addPlayerToTeam(Request $request, League $league, Team $team)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure team belongs to league
        if ($team->competition_id !== $league->id) {
            abort(404);
        }

        // Ensure league is in draft status
        if ($league->status !== 'draft') {
            return back()->withErrors(['general' => __('Cannot modify teams in a league that has already started.')]);
        }

        $request->validate([
            'player_id' => ['required', 'exists:players,id'],
        ]);

        $player = Player::findOrFail($request->player_id);

        // Ensure player belongs to the organization
        if ($player->organization_id !== $organization->id) {
            return back()->withErrors(['general' => __('Player does not belong to this organization.')]);
        }

        // Ensure player is in the league
        if (!$league->players()->where('player_id', $player->id)->exists()) {
            return back()->withErrors(['general' => __('Player must be added to the league first.')]);
        }

        // Remove player from any existing team in this league
        $league->players()->updateExistingPivot($player->id, ['team_id' => $team->id]);

        return back()->with('success', __('Player added to team successfully!'));
    }

    /**
     * Remove a player from a team.
     */
    public function removePlayerFromTeam(Request $request, League $league, Team $team, Player $player)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure team belongs to league
        if ($team->competition_id !== $league->id) {
            abort(404);
        }

        // Ensure league is in draft status
        if ($league->status !== 'draft') {
            return back()->withErrors(['general' => __('Cannot modify teams in a league that has already started.')]);
        }

        // Ensure player belongs to the organization
        if ($player->organization_id !== $organization->id) {
            return back()->withErrors(['general' => __('Player does not belong to this organization.')]);
        }

        // Remove player from team (set team_id to null)
        $league->players()->updateExistingPivot($player->id, ['team_id' => null]);

        return back()->with('success', __('Player removed from team successfully!'));
    }

    /**
     * Add a single player to the league (for individual leagues).
     */
    public function addPlayer(Request $request, League $league)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league is individual-based
        if ($league->is_team_based) {
            return back()->withErrors(['general' => __('This league is team-based. Use team management instead.')]);
        }

        // Ensure league is in draft status
        if ($league->status !== 'draft') {
            return back()->withErrors(['general' => __('Cannot add players to a league that has already started.')]);
        }

        $request->validate([
            'player_id' => ['required', 'exists:players,id'],
        ]);

        $player = Player::findOrFail($request->player_id);

        // Ensure player belongs to the organization
        if ($player->organization_id !== $organization->id) {
            return back()->withErrors(['general' => __('Player does not belong to this organization.')]);
        }

        // Check if player is already in the league
        if ($league->players()->where('player_id', $player->id)->exists()) {
            return back()->withErrors(['general' => __('Player is already in this league.')]);
        }

        // Add player to the league
        $league->players()->attach($player->id, ['joined_at' => now()]);

        return back()->with('success', __('Player added to league successfully!'));
    }

    /**
     * Show team management interface for team-based leagues.
     */
    public function teamManagement(League $league)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure league is team-based
        if (!$league->is_team_based) {
            return redirect()->route('leagues.show', $league)
                           ->withErrors(['general' => __('This league is not team-based.')]);
        }

        $league->load('sport', 'teams.players', 'players');
        $organization->load('players');

        return view('organizations.leagues.team-management', compact('organization', 'league'));
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
            'type' => 'league',
            'is_team_based' => $request->is_team_based,
            'status' => 'draft',
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            // 'max_teams' => null, // Will be configured later - using database default
            'settings' => [], // Empty settings - will be configured later
            'is_active' => true,
        ]);

        // Clear league cache
        League::clearLeagueCache();

        return redirect()->route('organizations.show', $organization)->with('success', __('League created successfully!'));
    }

    /**
     * Update the specified league rules.
     */
    public function update(Request $request, League $league)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Handle public visibility toggle (can be changed anytime)
        if ($request->has('is_public')) {
            $isPublic = $request->boolean('is_public');
            $league->update(['is_public' => $isPublic]);
            
            $message = $isPublic 
                ? __('league_now_public')
                : __('league_now_private');
                
            return back()->with('success', $message);
        }

        // Ensure league is in draft status for other settings
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
    public function destroy(League $league)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
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
    public function addTeam(Request $request, League $league)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
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
            'competition_id' => $league->id,
            'status' => 'active',
        ]);

        // Check if league should auto-start (minimum 2 teams for team-based leagues)
        if ($league->is_team_based && $league->teams()->count() >= 2) {
            $this->autoStartLeague($league);
        }

        return back()->with('success', __('Team added successfully!'));
    }

    /**
     * Add multiple players to the league.
     */
    public function addPlayers(Request $request, League $league)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
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

        // Check if league should auto-start (minimum 2 players for individual leagues)
        if (!$league->is_team_based && $league->players()->count() >= 2) {
            $this->autoStartLeague($league);
        }

        $addedCount = count($playerIds);
        return back()->with('success', __('Players added to league successfully! :count players added.', ['count' => $addedCount]));
    }

    /**
     * Start the league.
     */
    public function startLeague(Request $request, League $league)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
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

            return redirect()->route('leagues.show', $league)
                           ->with('success', __('League started successfully!'));
        } catch (\Exception $e) {
            \Log::error('Error starting league: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Error starting league: ' . $e->getMessage()]);
        }
    }

    /**
     * Auto-start league when minimum participants are reached.
     */
    private function autoStartLeague(League $league)
    {
        try {
            // Set default settings if not set
            $settings = $league->settings ?? [];
            if (!isset($settings['format'])) {
                $settings['format'] = 'round_robin';
            }
            if (!isset($settings['points_win'])) {
                $settings['points_win'] = 3;
            }
            if (!isset($settings['points_draw'])) {
                $settings['points_draw'] = 1;
            }
            if (!isset($settings['points_loss'])) {
                $settings['points_loss'] = 0;
            }
            if (!isset($settings['sets_to_win'])) {
                $settings['sets_to_win'] = 2;
            }
            $league->update(['settings' => $settings]);

            // Generate matches based on format
            $this->generateMatches($league);

            // Generate standings table
            $this->generateStandings($league);

            // Start the league
            $league->update(['status' => 'active']);

        } catch (\Exception $e) {
            \Log::error('Error auto-starting league: ' . $e->getMessage());
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
                    'competition_id' => $league->id,
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
                'competition_id' => $league->id,
                'team_id' => $league->is_team_based ? $participant->id : null,
                'player_id' => !$league->is_team_based ? $participant->id : null,
                'position' => $position++,
            ]);
        }
    }

    /**
     * Update standings table based on completed matches.
     */
    public function updateStandings(League $league)
    {
        // Reset all standings for this league
        Standing::where('competition_id', $league->id)->update([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
            'sets_won' => 0,
            'sets_lost' => 0,
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
                    // Regular completed match
                    $homeStanding->increment('goals_for', $match->home_score);
                    $homeStanding->increment('goals_against', $match->away_score);
                    $awayStanding->increment('goals_for', $match->away_score);
                    $awayStanding->increment('goals_against', $match->home_score);

                    // Update sets won/lost
                    $homeStanding->increment('sets_won', $match->home_score);
                    $homeStanding->increment('sets_lost', $match->away_score);
                    $awayStanding->increment('sets_won', $match->away_score);
                    $awayStanding->increment('sets_lost', $match->home_score);

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
        $standings = Standing::where('competition_id', $league->id)
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
    public function showMatch(Request $request, League $league, LeagueMatch $match)
    {
        $organization = $league->organization;

        // Allow access if user owns the organization OR is registered as a player in it OR is a referee
        $isOwner = $organization->user_id === auth()->id();
        $isPlayer = $organization->players()->where('user_id', auth()->id())->exists();
        $isReferee = auth()->user()->organizationUsers()
            ->where('organization_id', $organization->id)
            ->where('role', 'referee')
            ->exists();

        if (!$isOwner && !$isPlayer && !$isReferee) {
            abort(403);
        }

        // Ensure match belongs to league
        if ($match->competition_id !== $league->id) {
            abort(404);
        }

        // Load teams with players for team-based leagues
        if ($league->is_team_based) {
            $match->load(['homeTeam.players', 'awayTeam.players']);
        }

        // Load audit relationships and match officials
        $match->load(['moderator', 'referee', 'table', 'editedBy', 'completedBy']);

        return view('organizations.leagues.matches.show', compact('organization', 'league', 'match', 'isOwner', 'isPlayer', 'isReferee'));
    }

    /**
     * Show the form for editing match results.
     */
    public function editMatch(Request $request, League $league, LeagueMatch $match)
    {
        $organization = $league->organization;

        // Ensure user can manage matches for this organization (owner or referee)
        if (!$this->canManageMatches($organization)) {
            abort(403);
        }

        // Ensure match belongs to league
        if ($match->competition_id !== $league->id) {
            abort(404);
        }

        $isOwner = $organization->user_id === auth()->id();

        return view('organizations.leagues.matches.edit', compact('organization', 'league', 'match', 'isOwner'));
    }

    /**
     * Update match results.
     */
    public function updateMatch(Request $request, League $league, LeagueMatch $match)
    {
        $organization = $league->organization;

        // Ensure user can manage matches for this organization (owner or referee)
        if (!$this->canManageMatches($organization)) {
            abort(403);
        }

        // Ensure match belongs to league
        if ($match->competition_id !== $league->id) {
            abort(404);
        }

        // Validation
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,forfeited,cancelled',
            'home_score' => 'nullable|numeric|min:0',
            'away_score' => 'nullable|numeric|min:0',
            'forfeited_by' => 'nullable|in:home,away',
            'moderator_id' => 'nullable|exists:users,id',
            'referee_user_id' => 'nullable|exists:users,id',
            'table_id' => 'nullable|exists:tables,id',
        ]);

        // Check if user is owner (only owners can assign moderators)
        $isOwner = $organization->user_id === auth()->id();
        if ($request->filled('moderator_id') && !$isOwner) {
            abort(403, 'Only organization owners can assign moderators.');
        }

        // If moderator_id is provided, ensure they are a referee for this organization
        if ($request->filled('moderator_id')) {
            $isReferee = $organization->organizationUsers()
                ->where('user_id', $request->moderator_id)
                ->where('role', 'referee')
                ->exists();
            if (!$isReferee) {
                return back()->withErrors(['moderator_id' => 'Selected user is not a referee for this organization.']);
            }
        }

        // Check if table belongs to this organization
        if ($request->filled('table_id')) {
            $isValidTable = \App\Models\Table::where('id', $request->table_id)
                ->where('organization_id', $organization->id)
                ->exists();
            if (!$isValidTable) {
                return back()->withErrors(['table_id' => 'Selected table does not belong to this organization.']);
            }
        }

        // Prepare update data
        $updateData = ['status' => $validated['status']];

        // Add moderator if provided
        if (isset($validated['moderator_id'])) {
            $updateData['moderator_id'] = $validated['moderator_id'] ?: null;
        }

        // Add referee if provided
        if (isset($validated['referee_user_id'])) {
            $updateData['referee_user_id'] = $validated['referee_user_id'] ?: null;
        }

        // Add table if provided
        if (isset($validated['table_id'])) {
            $updateData['table_id'] = $validated['table_id'] ?: null;
        }

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

        return redirect()->route('leagues.matches.show', [$league, $match])
            ->with('success', 'Match updated successfully!');
    }

    /**
     * Show live scoring interface for a match.
     */
    public function liveScore(Request $request, League $league, LeagueMatch $match)
    {
        $organization = $league->organization;

        // Ensure user can manage matches for this organization (owner or referee)
        if (!$this->canManageMatches($organization)) {
            abort(403);
        }

        // Ensure match belongs to league
        if ($match->competition_id !== $league->id) {
            abort(404);
        }

        // Load teams with players for team-based leagues
        if ($league->is_team_based) {
            $match->load(['homeTeam.players', 'awayTeam.players']);
        }

        // Don't auto-start the match here - let the user choose server first
        // Match will be started when first server is selected in LiveScore component

        return view('organizations.leagues.matches.live', compact('organization', 'league', 'match'));
    }

    /**
     * Update live match score.
     */
    public function updateLiveScore(Request $request, League $league, LeagueMatch $match)
    {
        $organization = $league->organization;

        // Ensure user can manage matches for this organization (owner or referee)
        if (!$this->canManageMatches($organization)) {
            abort(403);
        }

        // Ensure match belongs to league
        if ($match->competition_id !== $league->id) {
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
    public function resetMatch(Request $request, League $league, LeagueMatch $match)
    {
        $organization = $league->organization;

        // Ensure user can manage matches for this organization (owner or referee)
        if (!$this->canManageMatches($organization)) {
            abort(403);
        }

        // Ensure match belongs to league
        if ($match->competition_id !== $league->id) {
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

        return redirect()->route('leagues.matches.show', [$league, $match])
            ->with('success', 'Match has been reset to initial state.');
    }

    /**
     * Reset league to draft status and regenerate matches.
     */
    public function resetLeague(Request $request, League $league)
    {
        $organization = $league->organization;

        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        try {
            // Delete all existing matches
            $league->matches()->delete();

            // Delete all standings
            $league->standings()->delete();

            // Reset league to draft status
            $league->update(['status' => 'draft']);

            return redirect()->route('leagues.show', $league)
                           ->with('success', __('League has been reset to draft status. You can now restart it with correct match pairings.'));
        } catch (\Exception $e) {
            \Log::error('Error resetting league: ' . $e->getMessage());
            return back()->withErrors(['general' => 'Error resetting league: ' . $e->getMessage()]);
        }
    }

    /**
     * Quick result entry for a match.
     */
    public function quickResult(Request $request, $matchId)
    {
        $match = LeagueMatch::findOrFail($matchId);
        
        // Get the league through the match
        $league = $match->league;
        
        // Ensure user can manage matches for this organization
        if (!$this->canManageMatches($league->organization)) {
            abort(403);
        }

        $request->validate([
            'home_score' => 'required|integer|min:0|max:5',
            'away_score' => 'required|integer|min:0|max:5',
            'sets' => 'nullable|array',
            'sets.*.home' => 'required_with:sets|integer|min:0',
            'sets.*.away' => 'required_with:sets|integer|min:0',
        ]);

        // Update match with results
        $match->update([
            'home_score' => $request->home_score,
            'away_score' => $request->away_score,
            'sets' => $request->sets ?? [],
            'status' => 'completed',
            'played_at' => now(),
        ]);

        // Update league standings
        $this->updateStandings($league);

        return back()->with('success', 'Match result saved successfully!');
    }

    /**
     * Check if the current user can manage matches for the organization.
     */
    private function canManageMatches(Organization $organization): bool
    {
        $user = auth()->user();

        // Owner can always manage matches
        if ($organization->user_id === $user->id) {
            return true;
        }

        // Check if user is a referee for this organization
        return $organization->organizationUsers()
            ->where('user_id', $user->id)
            ->where('role', 'referee')
            ->exists();
    }
}
