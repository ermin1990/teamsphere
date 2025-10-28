<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Standing;
use App\Models\Team;
use App\Models\TournamentGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class CompetitionController extends Controller
{
    /**
     * Show the form for creating a new competition.
     */
    public function create(Organization $organization)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        $sports = Sport::active()->get();

        return view('organizations.competitions.create', compact('organization', 'sports'));
    }

    /**
     * Store a newly created competition.
     */
    public function store(Request $request, Organization $organization)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if user can create more competitions
        if (!auth()->user()->canCreateMoreCompetitions($organization->id)) {
            return back()->withErrors(['limit' => __('You have reached the maximum number of competitions allowed for this organization.')]);
        }

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'sport_id' => ['required', 'exists:sports,id'],
                'type' => ['required', 'in:tournament'],
                'start_date' => ['required', 'date'],
                // Tournament specific validation
                'players_advancing_per_group' => ['required_if:type,tournament', 'integer', 'min:1', 'max:4'],
                'advancement_method' => ['required_if:type,tournament', 'in:automatic,manual'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log validation errors for debugging
            \Log::error('Competition validation failed', [
                'errors' => $e->errors(),
                'input' => $request->all()
            ]);
            throw $e;
        }

        $competition = Competition::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name . '-' . time()),
            'description' => $request->description,
            'organization_id' => $organization->id,
            'sport_id' => $request->sport_id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_teams' => $request->max_teams ?: 8,
            'is_team_based' => (bool) ($request->input('is_team_based', 0)),
            'status' => 'draft',
            'is_active' => true,
            // Tournament fields - use defaults if not provided, but keep flexible
            'max_participants' => $request->max_participants, // Optional - can be null
            'group_count' => $request->group_count ?: 4,
            'players_per_group' => $request->players_per_group ?: 4,
            'players_advancing_per_group' => $request->players_advancing_per_group ?: 2,
            'advancement_method' => 'automatic',
            'current_phase' => 'groups',
            // Match settings defaults
            'sets_to_win' => 3,
            'points_per_set' => 11,
            'deuce_at' => 10,
            'must_win_by_two' => true,
            'points_for_win' => 2,
            'points_for_draw' => 1,
            'points_for_loss' => 0,
            'has_tiebreak' => false,
            'tiebreak_points' => 7,
            'manual_knockout_selection' => true,
        ]);

        // Clear organization cache
        Organization::clearOrganizationCache();

        return redirect()
            ->route('organizations.competitions.show', [$organization, $competition])
            ->with('success', 'Takmičenje uspješno kreirano!');
    }

    /**
     * Display the specified competition.
     */
    public function show(Organization $organization, Competition $competition)
    {
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

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Generate group matches if tournament is active but no matches exist
        if ($competition->isTournament() && $competition->status === 'active' && $competition->tournamentGroups()->count() > 0) {
            $existingMatches = \App\Models\CompetitionMatch::where('competition_id', $competition->id)
                ->whereNotNull('tournament_group_id')
                ->count();
            if ($existingMatches === 0) {
                $competition->generateGroupMatches();
            }
        }

        // Ensure standings exist for tournament groups
        if ($competition->isTournament() && $competition->tournamentGroups()->count() > 0) {
            foreach ($competition->tournamentGroups as $group) {
                $existingStandings = \App\Models\Standing::where('competition_id', $competition->id)
                    ->where('tournament_group_id', $group->id)
                    ->count();
                if ($existingStandings === 0) {
                    foreach ($group->player_ids as $playerId) {
                        \App\Models\Standing::create([
                            'competition_id' => $competition->id,
                            'tournament_group_id' => $group->id,
                            'player_id' => $playerId,
                            'played' => 0,
                            'won' => 0,
                            'drawn' => 0,
                            'lost' => 0,
                            'points' => 0,
                            'sets_won' => 0,
                            'sets_lost' => 0,
                            'points_won' => 0,
                            'points_lost' => 0,
                            'goals_for' => 0,
                            'goals_against' => 0,
                            'goal_difference' => 0,
                        ]);
                    }
                }
            }
        }

        $competition->load([
            'sport',
            'teams.players',
            'matches.homeTeam',
            'matches.awayTeam',
            'matches.homePlayer',
            'matches.awayPlayer',
            'players',
            'standings',
            'tournamentGroups.standings.player',
            'tournamentGroups.standings.team'
        ]);

        $organization->load('players');

        // Load knockout matches
        $knockoutMatches = \App\Models\CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->with('homePlayer', 'awayPlayer')
            ->orderBy('round_number')
            ->orderBy('match_order')
            ->get();

        // Debug
        \Log::info('Knockout matches loaded', [
            'competition_id' => $competition->id,
            'count' => $knockoutMatches->count(),
            'first_match' => $knockoutMatches->first() ? [
                'id' => $knockoutMatches->first()->id,
                'phase' => $knockoutMatches->first()->phase,
                'status' => $knockoutMatches->first()->status,
            ] : null
        ]);

        return view('organizations.competitions.show', compact('organization', 'competition', 'isOwner', 'isPlayer', 'isReferee', 'knockoutMatches'));
    }

    /**
     * Update the specified competition.
     */
    public function update(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Handle public visibility toggle (can be changed anytime)
        if ($request->has('is_public')) {
            $isPublic = $request->boolean('is_public');
            $competition->update(['is_public' => $isPublic]);
            $message = $isPublic
                ? 'Takmičenje je sada javno i vidljivo na web stranici!'
                : 'Takmičenje je sada privatno i skriveno od javnog pogleda!';
            return back()->with('success', $message);
        }

        // For other updates, ensure competition is in draft status
        if ($competition->status !== 'draft') {
            return back()->withErrors(['general' => __('Cannot modify settings for a competition that has already started.')]);
        }

        // Handle other competition updates here if needed in the future
        return back()->withErrors(['general' => __('No valid updates provided.')]);
    }

    /**
     * Show the manage players page.
     */
    public function managePlayers(Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure competition is in draft status
        if ($competition->status !== 'draft') {
            return redirect()
                ->route('organizations.competitions.show', [$organization, $competition])
                ->with('error', __('Can only manage players for draft competitions.'));
        }

        $competition->load(['sport', 'players']);
        $organization->load('players');

        return view('organizations.competitions.manage-players', compact('organization', 'competition'));
    }

    /**
     * Add a player to the competition.
     */
    public function addPlayer(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        $request->validate([
            'player_id' => ['required', 'exists:players,id'],
        ]);

        $player = Player::findOrFail($request->player_id);

        // Check if player belongs to the organization
        if ($player->organization_id !== $organization->id) {
            return back()->with('error', 'Player does not belong to this organization.');
        }

        // Check if player is already registered
        if ($competition->players->contains($player->id)) {
            return back()->with('error', 'Player is already registered for this competition.');
        }

        // Check max participants limit for tournaments
        if ($competition->isTournament() && $competition->max_participants) {
            $currentCount = $competition->players()->count();
            if ($currentCount >= $competition->max_participants) {
                return back()->with('error', 'Maximum number of participants reached.');
            }
        }

        // Add player to competition
        $competition->players()->attach($player->id);

        return back()->with('success', 'Player added successfully!');
    }

    /**
     * Remove a player from the competition.
     */
    public function removePlayer(Request $request, Organization $organization, Competition $competition, Player $player)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Check if player belongs to the organization
        if ($player->organization_id !== $organization->id) {
            return back()->with('error', 'Player does not belong to this organization.');
        }

        // Check if player is registered
        if (!$competition->players->contains($player->id)) {
            return back()->with('error', 'Player is not registered for this competition.');
        }

        // Remove player from competition
        $competition->players()->detach($player->id);

        return back()->with('success', 'Player removed successfully!');
    }

    /**
     * Show the manual group setup page.
     */
    public function setupGroups(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        if ($competition->status !== 'draft') {
            return back()->with('error', 'Competition has already started.');
        }

        if ($competition->players->count() < 4) {
            return back()->with('error', 'You need at least 4 players to setup groups.');
        }

        // Load players and any existing groups
        $competition->load(['players', 'tournamentGroups']);
        
        // Prepare groups structure - use existing groups or create one default group
        $groups = [];
        $assignedPlayerIds = [];
        
        if ($competition->tournamentGroups->count() > 0) {
            // Use existing groups
            foreach ($competition->tournamentGroups as $group) {
                $groups[] = [
                    'name' => $group->name,
                    'number' => $group->group_number,
                    'players' => $competition->players
                        ->whereIn('id', $group->player_ids)
                        ->values()
                        ->toArray(),
                ];
                // Collect assigned player IDs
                $assignedPlayerIds = array_merge($assignedPlayerIds, $group->player_ids);
            }
        } else {
            // Create one default group
            $groups[] = [
                'name' => 'A',
                'number' => 1,
                'players' => []
            ];
        }

        // Get available players (not assigned to any group)
        $availablePlayers = $competition->players->whereNotIn('id', $assignedPlayerIds);

        return view('organizations.competitions.setup-groups', compact('organization', 'competition', 'groups', 'availablePlayers'));
    }

    /**
     * Save manually configured groups.
     */
    public function saveGroups(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        $request->validate([
            'groups' => ['required', 'array', 'min:1'],
            'groups.*' => ['required', 'array'],
            'groups.*.players' => ['required', 'array', 'min:2', 'max:' . $competition->players_per_group],
            'groups.*.players.*' => ['exists:players,id'],
        ]);

        // Get existing groups
        $existingGroups = $competition->tournamentGroups->keyBy('group_number');
        
        // Process each group from the request
        $processedGroupNumbers = [];
        $groupService = app(\App\Services\TournamentGroupService::class);
        
        foreach ($request->groups as $index => $groupData) {
            $groupNumber = $index + 1;
            $processedGroupNumbers[] = $groupNumber;
            
            if (isset($existingGroups[$groupNumber])) {
                // Update existing group
                $group = $existingGroups[$groupNumber];
                $group->update([
                    'player_ids' => $groupData['players'],
                    'standings' => $groupService->initializeGroupStandings($groupData['players']),
                ]);
                
                // Update or create standings for this group
                Standing::where('competition_id', $competition->id)
                    ->where('tournament_group_id', $group->id)
                    ->delete(); // Delete old standings
                
                // Create new standings
                foreach ($groupData['players'] as $playerId) {
                    Standing::create([
                        'competition_id' => $competition->id,
                        'tournament_group_id' => $group->id,
                        'player_id' => $playerId,
                        'played' => 0,
                        'won' => 0,
                        'drawn' => 0,
                        'lost' => 0,
                        'points' => 0,
                        'sets_won' => 0,
                        'sets_lost' => 0,
                        'points_won' => 0,
                        'points_lost' => 0,
                        'goals_for' => 0,
                        'goals_against' => 0,
                        'goal_difference' => 0,
                    ]);
                }
            } else {
                // Create new group
                $group = TournamentGroup::create([
                    'competition_id' => $competition->id,
                    'name' => chr(65 + $index),
                    'group_number' => $groupNumber,
                    'player_ids' => $groupData['players'],
                    'standings' => $groupService->initializeGroupStandings($groupData['players']),
                ]);

                // Create Standing records for each player in the group
                foreach ($groupData['players'] as $playerId) {
                    Standing::create([
                        'competition_id' => $competition->id,
                        'tournament_group_id' => $group->id,
                        'player_id' => $playerId,
                        'played' => 0,
                        'won' => 0,
                        'drawn' => 0,
                        'lost' => 0,
                        'points' => 0,
                        'sets_won' => 0,
                        'sets_lost' => 0,
                        'points_won' => 0,
                        'points_lost' => 0,
                        'goals_for' => 0,
                        'goals_against' => 0,
                        'goal_difference' => 0,
                    ]);
                }
            }
        }
        
        // Delete groups that are no longer in the request
        $competition->tournamentGroups()
            ->whereNotIn('group_number', $processedGroupNumbers)
            ->delete();

        return redirect()
            ->route('organizations.competitions.show', [$organization, $competition])
            ->with('success', 'Groups saved successfully! You can now start the competition.');
    }

    /**
     * Show competition settings page.
     */
    public function showSettings(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        return view('organizations.competitions.settings', compact('organization', 'competition'));
    }

    /**
     * Update competition settings.
     */
    public function updateSettings(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if ($competition->status !== 'draft') {
            return back()->with('error', 'Cannot change settings after competition has started.');
        }

        $request->validate([
            'sets_to_win' => ['required', 'integer', 'min:1', 'max:7'],
            'points_per_set' => ['required', 'integer', 'min:7', 'max:21'],
            'deuce_at' => ['nullable', 'integer', 'min:5'],
            'must_win_by_two' => ['boolean'],
            'points_for_win' => ['required', 'integer', 'min:0', 'max:10'],
            'points_for_draw' => ['required', 'integer', 'min:0', 'max:10'],
            'points_for_loss' => ['required', 'integer', 'min:0', 'max:10'],
            'has_tiebreak' => ['boolean'],
            'tiebreak_points' => ['nullable', 'integer', 'min:5', 'max:15'],
            // Tournament-specific validation
            'players_advancing_per_group' => ['nullable', 'integer', 'min:1', 'max:4'],
        ]);

        $competition->update([
            'sets_to_win' => $request->sets_to_win,
            'points_per_set' => $request->points_per_set,
            'deuce_at' => $request->deuce_at,
            'must_win_by_two' => $request->boolean('must_win_by_two'),
            'points_for_win' => $request->points_for_win,
            'points_for_draw' => $request->points_for_draw,
            'points_for_loss' => $request->points_for_loss,
            'has_tiebreak' => $request->boolean('has_tiebreak'),
            'tiebreak_points' => $request->tiebreak_points,
            // Tournament-specific updates
            'players_advancing_per_group' => $request->players_advancing_per_group,
        ]);

        return redirect()
            ->route('organizations.competitions.show', [$organization, $competition])
            ->with('success', 'Competition settings updated successfully!');
    }

    /**
     * Start the competition.
     */
    public function startCompetition(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if ($competition->status !== 'draft') {
            return back()->with('error', 'Competition has already started.');
        }

        if ($competition->isTournament() && $competition->tournamentGroups()->count() === 0) {
            return back()->with('error', 'Please setup groups before starting the tournament.');
        }

        // Update competition status and current phase
        $currentPhase = null;
        if ($competition->isTournament()) {
            $currentPhase = 'groups';
        }

        $competition->update([
            'status' => 'active',
            'current_phase' => $currentPhase,
        ]);

        // Generate matches and standings based on competition type
        if ($competition->isTournament()) {
            $competition->generateGroupMatches();
        } elseif ($competition->isLeague()) {
            $competition->generateLeagueMatches();
            $competition->generateLeagueStandings();
        }

        return back()->with('success', 'Competition started successfully! Matches have been generated.');
    }

    /**
     * Generate tournament groups.
     */
    public function generateGroups(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        if ($competition->tournamentGroups()->exists()) {
            return back()->with('error', 'Groups have already been generated.');
        }

        $competition->generateGroups();

        return redirect()
            ->route('organizations.competitions.show', [$organization, $competition])
            ->with('success', 'Tournament groups generated successfully!');
    }

    /**
     * Advance players from groups to knockout phase.
     */
    public function advanceFromGroups(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        // Check if all groups are completed
        $incompleteGroups = $competition->tournamentGroups()->where('is_completed', false)->count();
        if ($incompleteGroups > 0) {
            return back()->with('error', "Cannot advance. {$incompleteGroups} group(s) are not completed yet.");
        }

        $competition->advanceFromGroups();

        return back()->with('success', 'Players advanced to knockout phase successfully!');
    }

    /**
     * Generate knockout bracket automatically.
     */
    public function generateKnockoutBracket(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        if ($competition->current_phase !== 'knockout') {
            return back()->with('error', 'Competition is not in knockout phase.');
        }

        // Check if knockout bracket already exists
        $existingKnockoutMatches = CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->count();

        if ($existingKnockoutMatches > 0) {
            return back()->with('error', 'Knockout bracket has already been generated.');
        }

        $competition->generateKnockoutBracket();

        return back()->with('success', 'Knockout bracket generated successfully!');
    }

    /**
     * Manually advance players from a specific group.
     */
    public function advanceGroupPlayers(Request $request, Organization $organization, Competition $competition, TournamentGroup $group)
    {
        $request->validate([
            'advancing_players' => ['required', 'array', 'min:1', 'max:' . $competition->players_advancing_per_group],
            'advancing_players.*' => ['exists:players,id'],
        ]);

        if ($competition->advancement_method !== 'manual') {
            return back()->with('error', 'This tournament uses automatic advancement.');
        }

        // Update the group's standings to mark selected players as advancing
        $standings = $group->standings ?? [];
        foreach ($standings as &$standing) {
            $standing['manually_advanced'] = in_array($standing['player_id'], $request->advancing_players);
        }

        $group->update(['standings' => $standings]);

        return back()->with('success', 'Players advanced from group successfully!');
    }

    /**
     * Complete the tournament.
     */
    public function completeTournament(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        $competition->completeTournament();

        return back()->with('success', 'Tournament completed successfully!');
    }

    /**
     * Reset competition back to draft status.
     */
    public function reset(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Delete all matches
        $competition->matches()->delete();

        // Delete all groups
        $competition->tournamentGroups()->delete();

        // Delete all standings
        $competition->standings()->delete();

        // Reset competition status
        $competition->update([
            'status' => 'draft',
            // set to initial phase (must respect NOT NULL constraint in DB)
            'current_phase' => 'groups',
            'groups_completed_at' => null,
        ]);

        return back()->with('success', 'Competition has been reset to draft status!');
    }

    /**
     * Quick result entry for a match.
     */
    public function quickResult(Request $request, $matchId)
    {
        $match = CompetitionMatch::findOrFail($matchId);

        // Get the competition through the match
        $competition = $match->competition;

        // Check if user can access this specific match
        if (!$this->canAccessCompetitionMatch($match, $competition->organization)) {
            abort(403);
        }

        $request->validate([
            'home_score' => 'required|integer|min:0|max:7',
            'away_score' => 'required|integer|min:0|max:7',
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

        // Update standings if tournament
        if ($competition->type === 'tournament' && $match->tournamentGroup) {
            // Refresh match to get updated values (like LiveScore does)
            $match->refresh();
            // First update the in-model group standings (same as LiveScore)
            $match->tournamentGroup->updateStandings($match);
            // Then update Eloquent Standing records from scratch
            $groupService = app(\App\Services\TournamentGroupService::class);
            $groupService->recalculateGroupStandings($match->tournamentGroup);
        }

        return back()->with('success', 'Match result saved successfully!');
    }

    /**
     * Check if user can access a competition match.
     */
    private function canAccessCompetitionMatch(CompetitionMatch $match, Organization $organization)
    {
        $user = auth()->user();

        // Owner can access
        if ($organization->user_id === $user->id) {
            return true;
        }

        // Player in the organization can access
        if ($organization->players()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Referee can access
        if ($user->organizationUsers()
            ->where('organization_id', $organization->id)
            ->where('role', 'referee')
            ->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Show match details.
     */
    public function showMatch(Organization $organization, Competition $competition, CompetitionMatch $match)
    {
        // Check if user can access this specific match
        if (!$this->canAccessCompetitionMatch($match, $organization)) {
            abort(403);
        }

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404);
        }

        $isOwner = $organization->user_id === auth()->id();
        
        // Load relationships
        $match->load(['homePlayer', 'awayPlayer', 'homeTeam', 'awayTeam', 'tournamentGroup']);

        return view('organizations.competitions.matches.show', compact('organization', 'competition', 'match', 'isOwner'));
    }

    /**
     * Show edit form for a match.
     */
    public function editMatch(Organization $organization, Competition $competition, CompetitionMatch $match)
    {
        // Check if user can access this specific match
        if (!$this->canAccessCompetitionMatch($match, $organization)) {
            abort(403);
        }

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404);
        }

        $isOwner = $organization->user_id === auth()->id();

        return view('organizations.competitions.matches.edit', compact('organization', 'competition', 'match', 'isOwner'));
    }

    /**
     * Update match results.
     */
    public function updateMatch(Request $request, Organization $organization, Competition $competition, CompetitionMatch $match)
    {
        // Check if user can access this specific match
        if (!$this->canAccessCompetitionMatch($match, $organization)) {
            abort(403);
        }

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404);
        }

        // Validation
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,forfeited,cancelled',
            'home_score' => 'nullable|integer|min:0|max:7',
            'away_score' => 'nullable|integer|min:0|max:7',
            'sets' => 'nullable|array',
            'sets.*.home' => 'required_with:sets|integer|min:0',
            'sets.*.away' => 'required_with:sets|integer|min:0',
            'forfeited_by' => 'nullable|in:home,away',
        ]);

        // Prepare update data
        $updateData = ['status' => $validated['status']];

        if ($validated['status'] === 'scheduled') {
            // Reset everything
            $updateData['home_score'] = 0;
            $updateData['away_score'] = 0;
            $updateData['sets'] = [];
            $updateData['forfeited_by'] = null;
            $updateData['played_at'] = null;
        } elseif ($validated['status'] === 'completed') {
            $updateData['home_score'] = $validated['home_score'] ?? 0;
            $updateData['away_score'] = $validated['away_score'] ?? 0;
            $updateData['sets'] = $validated['sets'] ?? [];
            $updateData['played_at'] = $match->played_at ?? now();
        } elseif ($validated['status'] === 'forfeited') {
            $updateData['forfeited_by'] = $validated['forfeited_by'];
            $updateData['played_at'] = now();
        }

        $match->update($updateData);

        // Update standings if tournament
        if ($competition->type === 'tournament' && $match->tournamentGroup && $validated['status'] === 'completed') {
            $match->refresh();
            $match->tournamentGroup->updateStandings($match);
            $groupService = app(\App\Services\TournamentGroupService::class);
            $groupService->recalculateGroupStandings($match->tournamentGroup);
        }

        return redirect()
            ->route('organizations.competitions.show', [$organization, $competition])
            ->with('success', 'Match updated successfully!');
    }

    /**
     * Delete the competition.
     */
    public function destroy(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        $competitionName = $competition->name;

        // Delete all related data (cascade will handle most of it)
        $competition->delete();

        return redirect()
            ->route('organizations.show', $organization)
            ->with('success', "Competition '{$competitionName}' has been deleted successfully!");
    }

    /**
     * Update match players via AJAX (for drag and drop).
     */
    public function updateMatchPlayers(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $request->validate([
            'match_id' => 'required|exists:matches,id',
            'player_type' => 'required|in:home,away',
            'player_id' => 'required|exists:players,id',
        ]);

        $match = CompetitionMatch::findOrFail($request->match_id);

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            return response()->json(['success' => false, 'message' => 'Match not found']);
        }

        // Update the player
        if ($request->player_type === 'home') {
            $match->update(['home_player_id' => $request->player_id]);
        } else {
            $match->update(['away_player_id' => $request->player_id]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show the bulk import players form.
     */
    public function showBulkImport(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure competition is in draft status
        if ($competition->status !== 'draft') {
            return redirect()
                ->route('organizations.competitions.show', [$organization, $competition])
                ->with('error', __('Can only manage players for draft competitions.'));
        }

        return view('organizations.competitions.bulk-import', compact('organization', 'competition'));
    }

    /**
     * Process bulk import of players from text input.
     */
    public function bulkImportPlayers(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure competition is in draft status
        if ($competition->status !== 'draft') {
            return back()->with('error', 'Can only manage players for draft competitions.');
        }

        $request->validate([
            'players_text' => ['required', 'string'],
        ]);

        $playersText = trim($request->players_text);
        if (empty($playersText)) {
            return back()->with('error', 'Please provide player names.');
        }

        // Parse players from text (format: "Name, Club;")
        $playersData = [];
        $lines = explode("\n", $playersText);
        
        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Each line should end with semicolon
            if (!str_ends_with($line, ';')) {
                $errors[] = "Line " . ($lineNumber + 1) . ": Must end with ';'";
                continue;
            }
            
            // Remove trailing semicolon
            $line = rtrim($line, ';');
            
            // Split by comma to get name and club
            $parts = explode(',', $line, 2);
            
            if (count($parts) !== 2) {
                $errors[] = "Line " . ($lineNumber + 1) . ": Format must be 'Name, Club'";
                continue;
            }
            
            $name = trim($parts[0]);
            $club = trim($parts[1]);
            
            if (empty($name)) {
                $errors[] = "Line " . ($lineNumber + 1) . ": Player name is required";
                continue;
            }
            
            $playersData[] = [
                'name' => $name,
                'club' => $club,
            ];
        }

        if (empty($playersData)) {
            return back()->with('error', 'No valid player data found.');
        }

        // Check max participants limit for tournaments
        if ($competition->isTournament() && $competition->max_participants) {
            $currentCount = $competition->players()->count();
            $newCount = $currentCount + count($playersData);
            if ($newCount > $competition->max_participants) {
                return back()->with('error', "Adding these players would exceed the maximum participants limit of {$competition->max_participants}. Current: {$currentCount}, Adding: " . count($playersData) . ", Total: {$newCount}");
            }
        }

        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($playersData as $playerData) {
            $playerName = $playerData['name'];
            $playerClub = $playerData['club'];
            
            try {
                // Check if player already exists in organization
                $existingPlayer = Player::where('organization_id', $organization->id)
                    ->where('name', $playerName)
                    ->first();

                if ($existingPlayer) {
                    // Check if already in competition
                    if ($competition->players->contains($existingPlayer->id)) {
                        $skipped++;
                        continue;
                    }
                    
                    // Update club/position if provided
                    if (!empty($playerClub) && $existingPlayer->position !== $playerClub) {
                        $existingPlayer->update(['position' => $playerClub]);
                    }
                    
                    // Add existing player to competition
                    $competition->players()->attach($existingPlayer->id);
                    $imported++;
                } else {
                    // Create new player with club
                    $player = Player::create([
                        'organization_id' => $organization->id,
                        'name' => $playerName,
                        'position' => $playerClub, // Club name stored in position field
                        'is_active' => true,
                    ]);
                    
                    // Add to competition
                    $competition->players()->attach($player->id);
                    $imported++;
                }
            } catch (\Exception $e) {
                $errors[] = "Error importing '{$playerName}': " . $e->getMessage();
            }
        }

        $message = "Import completed. Imported: {$imported}";
        if ($skipped > 0) {
            $message .= ", Skipped (already in competition): {$skipped}";
        }
        if (!empty($errors)) {
            $message .= ". Errors: " . implode('; ', $errors);
        }

        return back()->with('success', $message);
    }

    /**
     * Show manual knockout setup page.
     */
    public function manualKnockoutSetup(Request $request, Organization $organization, Competition $competition)
    {
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        $competition->load(['tournamentGroups.standings.player']);

        return view('organizations.competitions.manual-knockout-setup', compact('organization', 'competition'));
    }

    /**
     * Automatically generate knockout bracket (JOOLA rules).
     */
    public function autoGenerateKnockout(Request $request, Organization $organization, Competition $competition)
    {
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }



        // Check if all groups are completed
        $incompleteGroups = $competition->tournamentGroups()->where('is_completed', false)->count();
        if ($incompleteGroups > 0) {
            return back()->with('error', "Ne možeš generisati knockout fazu. {$incompleteGroups} grupa još nije završena. Svi mečevi u grupnoj fazi moraju biti odigrani prije nego što generiš knockout fazu.");
        }

        // Check if there are already knockout matches - prevent regeneration if groups are not complete
        $existingKnockoutMatches = CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->count();
        
        if ($existingKnockoutMatches > 0 && $incompleteGroups == 0) {
            return back()->with('error', 'Knockout faza već postoji. Prvo koristi "Resetuj" dugme da obrišeš postojeću knockout fazu ako želiš regenerisati.');
        }

        try {
            $competition->generateKnockoutBracket();
            return back()->with('success', 'Knockout bracket generated successfully using JOOLA rules!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error generating knockout bracket: ' . $e->getMessage());
        }
    }

    /**
     * Save manually created knockout bracket.
     */
    public function saveManualKnockout(Request $request, Organization $organization, Competition $competition)
    {
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        $request->validate([
            'bracket_data' => ['required', 'string'],
        ]);

        try {
            $bracketData = json_decode($request->bracket_data, true);

            if (!$bracketData || !isset($bracketData['matches'])) {
                return back()->with('error', 'Invalid bracket data format.');
            }

            // Delete existing knockout matches
            CompetitionMatch::where('competition_id', $competition->id)
                ->where('phase', 'knockout')
                ->delete();

            // Create knockout matches from array
            $matchOrder = 1;
            $roundNumber = 1;

            foreach ($bracketData['matches'] as $matchData) {
                // Skip completely empty matches
                if (empty($matchData['home_player_id']) && empty($matchData['away_player_id'])) {
                    continue;
                }
                
                // Check if this is a bye match (only one player)
                $homePlayerId = $matchData['home_player_id'] ?? null;
                $awayPlayerId = $matchData['away_player_id'] ?? null;
                $isBye = ($homePlayerId && !$awayPlayerId) || (!$homePlayerId && $awayPlayerId);
                
                CompetitionMatch::create([
                    'competition_id' => $competition->id,
                    'home_player_id' => $homePlayerId,
                    'away_player_id' => $awayPlayerId,
                    'phase' => 'knockout',
                    'round_number' => $roundNumber,
                    'match_order' => $matchOrder,
                    'status' => $isBye ? 'completed' : 'scheduled',
                    'is_bye' => $isBye,
                    'scheduled_at' => now(),
                ]);

                $matchOrder++;
            }

            // Create playoff matches if any
            if (isset($bracketData['playoffMatches']) && is_array($bracketData['playoffMatches'])) {
                foreach ($bracketData['playoffMatches'] as $playoffData) {
                    if (empty($playoffData['home_player_id']) && empty($playoffData['away_player_id'])) {
                        continue;
                    }
                    
                    CompetitionMatch::create([
                        'competition_id' => $competition->id,
                        'home_player_id' => $playoffData['home_player_id'] ?? null,
                        'away_player_id' => $playoffData['away_player_id'] ?? null,
                        'phase' => 'playoff',
                        'round_number' => 1,
                        'match_order' => $matchOrder++,
                        'status' => 'scheduled',
                        'scheduled_at' => now(),
                    ]);
                }
            }

            $competition->update([
                'current_phase' => 'knockout',
                'knockout_bracket' => true,
            ]);

            return redirect()
                ->route('organizations.competitions.show', [$organization, $competition])
                ->with('success', 'Manualna knockout faza sačuvana uspješno!');
        } catch (\Exception $e) {
            \Log::error('Error saving manual knockout: ' . $e->getMessage());
            return back()->with('error', 'Greška prilikom spremanja: ' . $e->getMessage());
        }
    }

    /**
     * Advance to next knockout round after match completion.
     */
    public function advanceKnockoutRound(Request $request, Organization $organization, Competition $competition)
    {
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        try {
            $currentRound = $request->input('current_round', 1);
            $competition->advanceKnockoutRound($currentRound);

            return back()->with('success', 'Knockout round advanced successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error advancing knockout round: ' . $e->getMessage());
        }
    }

    /**
     * Reset knockout phase.
     */
    public function resetKnockout(Request $request, Organization $organization, Competition $competition)
    {
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        try {
            \Log::info('Resetting knockout phase', [
                'competition_id' => $competition->id,
                'user_id' => auth()->id(),
                'organization_id' => $organization->id
            ]);

            // Delete all knockout matches
            CompetitionMatch::where('competition_id', $competition->id)
                ->where('phase', 'knockout')
                ->delete();

            // Reset competition knockout fields and phase
            $competition->update([
                'current_phase' => 'groups', // Reset to groups phase so knockout can be regenerated
                'knockout_bracket' => null,
                'knockout_completed_at' => null,
                'knockout_started_at' => null,
            ]);

            \Log::info('Knockout phase reset successfully', [
                'competition_id' => $competition->id
            ]);

            return back()->with('success', 'Knockout faza je resetovana! Sada možeš ponovo regenerisati knockout bracket.');
        } catch (\Exception $e) {
            \Log::error('Error resetting knockout phase', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Greška pri resetovanju knockout faze: ' . $e->getMessage());
        }
    }

    /**
     * Reset groups phase.
     */
    public function resetGroups(Request $request, Organization $organization, Competition $competition)
    {
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        // PREVENT resetting if groups are not complete (matches still in progress)
        $completedGroups = $competition->tournamentGroups()->where('is_completed', true)->count();
        if ($completedGroups > 0) {
            return back()->with('error', 'Ne možeš resetovati grupnu fazu dok su grupe u toku! Prvo završi sve grupne mečeve ili obriši cijelo takmičenje.');
        }

        try {
            \Log::info('Resetting groups phase', [
                'competition_id' => $competition->id,
                'user_id' => auth()->id(),
                'organization_id' => $organization->id
            ]);

            // Delete all group phase matches
            CompetitionMatch::where('competition_id', $competition->id)
                ->where('phase', 'group')
                ->delete();

            // Delete all standings
            Standing::where('competition_id', $competition->id)->delete();

            // Reset group completion status
            $competition->tournamentGroups()->update([
                'is_completed' => false,
                'completed_at' => null,
                'standings' => []
            ]);

            // Reset competition phase and related fields
            $competition->update([
                'current_phase' => 'groups',
                'groups_completed_at' => null,
                'knockout_bracket' => null,
                'knockout_completed_at' => null,
                'knockout_started_at' => null,
            ]);

            \Log::info('Groups phase reset successfully', [
                'competition_id' => $competition->id
            ]);

            return back()->with('success', 'Grupna faza je resetovana! Sada možete ponovo generisati grupe i mečeve.');
        } catch (\Exception $e) {
            \Log::error('Error resetting groups phase', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage()
            ]);
            return back()->with('error', 'Greška pri resetovanju grupne faze: ' . $e->getMessage());
        }
    }
}

