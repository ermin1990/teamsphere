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
                'type' => ['required', 'in:tournament,knockout'],
                'start_date' => ['required', 'date'],
                // Temporarily simplified validation
                // 'description' => ['nullable', 'string', 'max:1000'],
                // 'end_date' => ['nullable', 'date', 'after:start_date'],
                // 'max_teams' => ['nullable', 'integer', 'min:2', 'max:100'],
                // 'is_team_based' => ['nullable', 'in:0,1'],
                // Tournament specific validation
                // 'max_participants' => ['required_if:type,tournament', 'integer', 'min:4', 'max:128'],
                // 'group_count' => ['required_if:type,tournament', 'integer', 'min:2', 'max:16'],
                // 'players_per_group' => ['required_if:type,tournament', 'integer', 'min:3', 'max:8'],
                // 'players_advancing_per_group' => ['required_if:type,tournament', 'integer', 'min:1', 'max:4'],
                // 'advancement_method' => ['required_if:type,tournament', 'in:automatic,manual'],
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
            // Tournament fields - use defaults if not provided
            'max_participants' => $request->max_participants ?: 16,
            'group_count' => $request->group_count ?: 4,
            'players_per_group' => $request->players_per_group ?: 4,
            'players_advancing_per_group' => $request->players_advancing_per_group ?: 2,
            'advancement_method' => 'automatic',
            'manual_knockout_selection' => (bool) $request->input('manual_knockout_selection', false),
            'current_phase' => 'groups',
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

        return view('organizations.competitions.show', compact('organization', 'competition', 'isOwner', 'isPlayer', 'isReferee'));
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
        
        // Prepare empty groups structure
        $groups = [];
        for ($i = 0; $i < $competition->group_count; $i++) {
            $groups[] = [
                'name' => chr(65 + $i), // A, B, C, etc.
                'number' => $i + 1,
                'players' => []
            ];
        }

        // If groups already exist, populate them
        if ($competition->tournamentGroups->count() > 0) {
            foreach ($competition->tournamentGroups as $group) {
                $groupIndex = $group->group_number - 1;
                if (isset($groups[$groupIndex])) {
                    $groups[$groupIndex]['players'] = $competition->players
                        ->whereIn('id', $group->player_ids)
                        ->values()
                        ->toArray();
                }
            }
        }

        return view('organizations.competitions.setup-groups', compact('organization', 'competition', 'groups'));
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
            'groups' => ['required', 'array'],
            'groups.*' => ['required', 'array'],
            'groups.*.players' => ['required', 'array', 'min:2', 'max:' . $competition->players_per_group],
            'groups.*.players.*' => ['exists:players,id'],
        ]);

        // Delete existing groups
        $competition->tournamentGroups()->delete();

        // Delete existing standings for this competition
        Standing::where('competition_id', $competition->id)->delete();

        // Create new groups
        $groupService = app(\App\Services\TournamentGroupService::class);
        foreach ($request->groups as $index => $groupData) {
            $group = TournamentGroup::create([
                'competition_id' => $competition->id,
                'name' => chr(65 + $index),
                'group_number' => $index + 1,
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
            'group_count' => ['nullable', 'integer', 'min:2', 'max:16'],
            'players_per_group' => ['nullable', 'integer', 'min:3', 'max:8'],
            'players_advancing_per_group' => ['nullable', 'integer', 'min:1', 'max:4'],
            'max_participants' => ['nullable', 'integer', 'min:4', 'max:128'],
            'manual_knockout_selection' => ['boolean'],
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
            'group_count' => $request->group_count,
            'players_per_group' => $request->players_per_group,
            'players_advancing_per_group' => $request->players_advancing_per_group,
            'max_participants' => $request->max_participants,
            'manual_knockout_selection' => $request->boolean('manual_knockout_selection'),
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
        } elseif ($competition->isKnockout()) {
            $currentPhase = 'knockout';
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
        } elseif ($competition->isKnockout()) {
            // Generate knockout bracket for knockout tournaments
            $competition->generateKnockoutBracket();
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
     * Manually advance players from a specific group.
     */
    public function advanceGroupPlayers(Request $request, Organization $organization, Competition $competition, TournamentGroup $group)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

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
            'knockout_bracket' => null,
            'groups_completed_at' => null,
            'knockout_completed_at' => null,
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

        // Check if we need to advance knockout rounds
        if ($competition->type === 'tournament' && $match->phase === 'knockout') {
            $this->advanceKnockoutRound($competition, $match);
        }

        return back()->with('success', 'Match result saved successfully!');
    }

    /**
     * Update group standings after a match.
     */


    private function updateGroupStandings($match)
    {
        $competition = $match->competition;
        $group = $match->tournamentGroup;

        // Get or create standings for both players
        $homeStanding = \App\Models\Standing::firstOrCreate([
            'competition_id' => $competition->id,
            'tournament_group_id' => $group->id,
            'player_id' => $match->home_player_id,
        ], [
            'played' => 0,
            'won' => 0,
            'lost' => 0,
            'points' => 0,
            'sets_won' => 0,
            'sets_lost' => 0,
            'points_won' => 0,
            'points_lost' => 0,
        ]);

        $awayStanding = \App\Models\Standing::firstOrCreate([
            'competition_id' => $competition->id,
            'tournament_group_id' => $group->id,
            'player_id' => $match->away_player_id,
        ], [
            'played' => 0,
            'won' => 0,
            'lost' => 0,
            'points' => 0,
            'sets_won' => 0,
            'sets_lost' => 0,
            'points_won' => 0,
            'points_lost' => 0,
        ]);

        // Update played matches
        $homeStanding->increment('played');
        $awayStanding->increment('played');

        // Determine winner and update standings
        if ($match->home_score > $match->away_score) {
            $homeStanding->increment('won');
            $homeStanding->increment('points', $competition->points_for_win ?? 2);
            $awayStanding->increment('lost');
        } elseif ($match->away_score > $match->home_score) {
            $awayStanding->increment('won');
            $awayStanding->increment('points', $competition->points_for_win ?? 2);
            $homeStanding->increment('lost');
        } else {
            // Draw
            $homeStanding->increment('points', $competition->points_for_draw ?? 1);
            $awayStanding->increment('points', $competition->points_for_draw ?? 1);
        }

        // Update set scores
        $homeStanding->increment('sets_won', $match->home_score);
        $homeStanding->increment('sets_lost', $match->away_score);
        $awayStanding->increment('sets_won', $match->away_score);
        $awayStanding->increment('sets_lost', $match->home_score);

        // Update point scores if sets data is available
        if ($match->sets && is_array($match->sets)) {
            foreach ($match->sets as $set) {
                $homeStanding->increment('points_won', $set['home'] ?? 0);
                $homeStanding->increment('points_lost', $set['away'] ?? 0);
                $awayStanding->increment('points_won', $set['away'] ?? 0);
                $awayStanding->increment('points_lost', $set['home'] ?? 0);
            }
        }

        // Update TournamentGroup standings
        $group->updateStandings($match);
    }

    /**
     * Advance to next knockout round after match completion.
     */
    private function advanceKnockoutRound($competition, $completedMatch)
    {
        $currentRound = $completedMatch->round_number;
        
        // Get all matches in current round
        $roundMatches = CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->where('round_number', $currentRound)
            ->get();

        // Check if all matches are completed
        $allCompleted = $roundMatches->every(function ($match) {
            return $match->status === 'completed';
        });

        if (!$allCompleted) {
            return; // Not all matches in round are done
        }

        // Check if next round already exists
        $nextRoundExists = CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->where('round_number', $currentRound + 1)
            ->exists();

        if ($nextRoundExists) {
            return; // Next round already created
        }

        // Get winners from current round (including bye matches)
        $winners = [];
        foreach ($roundMatches as $match) {
            if ($match->is_bye) {
                // For bye matches, winner is always home_player_id
                $winners[] = $match->home_player_id;
            } elseif ($match->home_score > $match->away_score) {
                $winners[] = $match->home_player_id;
            } else {
                $winners[] = $match->away_player_id;
            }
        }

        // If only 1 winner, tournament is complete
        if (count($winners) <= 1) {
            $competition->update([
                'status' => 'completed',
                'current_phase' => 'completed',
                'knockout_completed_at' => now(),
            ]);
            return;
        }

        // Create next round matches
        for ($i = 0; $i < count($winners); $i += 2) {
            $isBye = ($winners[$i + 1] ?? null) === null;
            
            $match = CompetitionMatch::create([
                'competition_id' => $competition->id,
                'home_player_id' => $winners[$i],
                'away_player_id' => $winners[$i + 1] ?? null,
                'phase' => 'knockout',
                'round_number' => $currentRound + 1,
                'status' => $isBye ? 'completed' : 'scheduled',
                'scheduled_at' => now(),
                'played_at' => $isBye ? now() : null,
                'home_score' => $isBye ? 1 : 0, // Winner gets 1 set
                'away_score' => $isBye ? 0 : 0, // Bye gets 0 sets
                'is_bye' => $isBye,
            ]);

            // If this is a bye match, immediately generate next round
            if ($isBye) {
                $this->advanceKnockoutRound($competition, $match);
            }
        }
    }

    /**
     * Manually generate next knockout round.
     */
    public function generateNextRound(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Get the last completed round
        $lastRound = CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->orderBy('round_number', 'desc')
            ->first();

        if (!$lastRound) {
            return back()->with('error', 'No knockout rounds found.');
        }

        // Create a dummy match to trigger advancement logic
        $this->advanceKnockoutRound($competition, $lastRound);

        return back()->with('success', 'Next round generated successfully!');
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
     * Auto generate knockout bracket from group standings.
     */
    public function autoGenerateBracket(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        if (!$competition->isTournament()) {
            return response()->json(['success' => false, 'message' => 'Not a tournament']);
        }

        // Get advancing players from all groups using Standing model
        $advancingPlayers = [];
        foreach ($competition->tournamentGroups as $group) {
            $groupStandings = \App\Models\Standing::where('competition_id', $competition->id)
                ->where('tournament_group_id', $group->id)
                ->orderByDesc('points')
                ->orderByRaw('(sets_won - sets_lost) desc')
                ->orderByRaw('(points_won - points_lost) desc')
                ->take($competition->players_advancing_per_group)
                ->pluck('player_id')
                ->toArray();

            // If no standings exist or all have 0 points, take the first players from the group
            if (empty($groupStandings)) {
                $groupStandings = collect($group->player_ids)->take($competition->players_advancing_per_group)->toArray();
            }

            $advancingPlayers = array_merge($advancingPlayers, $groupStandings);
        }

        if (empty($advancingPlayers)) {
            return response()->json(['success' => false, 'message' => 'No players found to advance from groups']);
        }

        // Convert to Player models
        $playerModels = \App\Models\Player::whereIn('id', $advancingPlayers)->get();

        // Delete existing knockout matches
        CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->delete();

        // Generate new bracket using JOOLA system
        $bracketService = app(\App\Services\TournamentBracketService::class);
        $bracketService->generateJOOLAEliminationBracket($competition);

        return response()->json(['success' => true]);
    }

    /**
     * Generate knockout bracket with manually selected players.
     */
    public function generateManualKnockout(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        if (!$competition->isTournament()) {
            return response()->json(['success' => false, 'message' => 'Not a tournament']);
        }

        // If requested, create empty match slots for the first knockout round
        if ($request->boolean('create_empty')) {
            // Verify manual selection flag
            if (!$competition->manual_knockout_selection) {
                return response()->json(['success' => false, 'message' => 'Manual knockout selection is not enabled for this competition']);
            }

            // Ensure competition is in knockout phase (or move it there)
            if ($competition->current_phase !== 'knockout') {
                $competition->update(['current_phase' => 'knockout']);
            }

            // Check existing knockout matches
            $existingMatches = CompetitionMatch::where('competition_id', $competition->id)
                ->where('phase', 'knockout')
                ->count();

            if ($existingMatches > 0) {
                return response()->json(['success' => false, 'message' => 'Knockout matches already exist']);
            }

            // Determine advancing players count (estimate from standings) and create empty slots
            $advancingPlayers = [];
            foreach ($competition->tournamentGroups as $group) {
                $standings = \App\Models\Standing::where('competition_id', $competition->id)
                    ->where('tournament_group_id', $group->id)
                    ->orderBy('points', 'desc')
                    ->orderByRaw('(sets_won - sets_lost) desc')
                    ->limit($competition->players_advancing_per_group ?? 2)
                    ->pluck('player_id')
                    ->toArray();
                if (!empty($standings)) {
                    $advancingPlayers = array_merge($advancingPlayers, $standings);
                }
            }

            $advancingPlayers = array_values(array_unique($advancingPlayers));
            $count = count($advancingPlayers);
            if ($count === 0) {
                // If no advancing players found, fallback to using players count
                $count = $competition->players()->count();
            }

            // Number of matches in first round
            $matchesCount = (int) ceil($count / 2);

            $bracket = [];
            $roundNumber = 1;

            for ($i = 0; $i < $matchesCount; $i++) {
                $position = $i + 1;
                $bracket[] = [
                    'round' => $roundNumber,
                    'home_player_id' => null,
                    'away_player_id' => null,
                    'position' => $position,
                ];

                CompetitionMatch::create([
                    'competition_id' => $competition->id,
                    'home_player_id' => null,
                    'away_player_id' => null,
                    'phase' => 'knockout',
                    'round_number' => $roundNumber,
                    'status' => 'scheduled',
                    'scheduled_at' => null,
                    'played_at' => null,
                    'home_score' => 0,
                    'away_score' => 0,
                    'is_bye' => false,
                ]);
            }

            $competition->update(['knockout_bracket' => $bracket]);

            return response()->json(['success' => true]);
        }

        // Default behavior: expect full matches payload with player ids
        $request->validate([
            'matches' => 'required|array|min:1',
            'matches.*.home_player_id' => 'required|integer|exists:players,id',
            'matches.*.away_player_id' => 'required|integer|exists:players,id',
        ]);

        $matches = $request->matches;

        // Verify that manual selection is enabled
        if (!$competition->manual_knockout_selection) {
            return response()->json(['success' => false, 'message' => 'Manual knockout selection is not enabled for this competition']);
        }

        // Verify that competition is in the correct phase
        if ($competition->current_phase !== 'knockout') {
            return response()->json(['success' => false, 'message' => 'Competition must be in knockout phase']);
        }

        // Check that we don't already have knockout matches
        $existingMatches = CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->count();

        if ($existingMatches > 0) {
            return response()->json(['success' => false, 'message' => 'Knockout matches already exist']);
        }

        // Create bracket structure manually
        $bracket = [];
        $roundNumber = 1;

        foreach ($matches as $matchData) {
            $bracket[] = [
                'round' => $roundNumber,
                'home_player_id' => $matchData['home_player_id'],
                'away_player_id' => $matchData['away_player_id'],
                'position' => count($bracket) + 1,
            ];
        }

        // Save bracket to competition
        $competition->update(['knockout_bracket' => $bracket]);

        // Generate matches from bracket
        $bracketService = app(\App\Services\KnockoutBracketService::class);
        $bracketService->generateMatchesFromBracket($competition, $bracket, $roundNumber);

        return response()->json(['success' => true]);
    }

    /**
     * Get available players for bracket editing.
     */
    public function getAvailablePlayers(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            return response()->json(['players' => []]);
        }

        // Get players who advanced from group stage
        $advancingPlayers = [];

        foreach ($competition->tournamentGroups as $group) {
            // Get advancing players from this group (typically top 2 players)
            $advancingPlayerIds = $group->getAdvancingPlayers(2); // Assuming 2 players advance per group

            foreach ($advancingPlayerIds as $playerId) {
                $player = \App\Models\Player::find($playerId);
                if ($player) {
                    $advancingPlayers[] = [
                        'id' => $player->id,
                        'name' => $player->name
                    ];
                }
            }
        }

        return response()->json(['players' => array_unique($advancingPlayers, SORT_REGULAR)]);
    }

    /**
     * Show the form for editing match results.
     */
    public function editMatch(Request $request, Organization $organization, Competition $competition, CompetitionMatch $match)
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
        $isReferee = ($match->referee_user_id === auth()->id());

        return view('organizations.competitions.matches.edit', compact('organization', 'competition', 'match', 'isOwner', 'isReferee'));
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

        $isOwner = $organization->user_id === auth()->id();

        // Validation
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,forfeited,cancelled',
            'home_score' => 'nullable|numeric|min:0',
            'away_score' => 'nullable|numeric|min:0',
            'forfeited_by' => 'nullable|in:home,away',
            'sets' => 'nullable|array',
            'sets.*.home_score' => 'nullable|numeric|min:0',
            'sets.*.away_score' => 'nullable|numeric|min:0',
            'table_id' => 'nullable|exists:tables,id',
            'referee_user_id' => 'nullable|exists:users,id',
        ]);

        // Prepare update data
        $updateData = [
            'status' => $validated['status'],
            'table_id' => $validated['table_id'] ?? null,
            'referee_user_id' => $validated['referee_user_id'] ?? null,
        ];

        if ($validated['status'] === 'scheduled') {
            // Reset everything
            $updateData['home_score'] = 0;
            $updateData['away_score'] = 0;
            $updateData['forfeited_by'] = null;
            $updateData['played_at'] = null;
            $updateData['sets'] = null;
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
            if (isset($validated['sets'])) {
                $updateData['sets'] = $validated['sets'];
            }
            
            // Set played_at for completed or forfeited matches
            if (in_array($validated['status'], ['completed', 'forfeited'])) {
                $updateData['played_at'] = now();
            }
        }

        // Update match
        $match->update($updateData);

        // Update standings if match results changed and it's a tournament group match
        if ($match->tournamentGroup && 
            (isset($validated['home_score']) || isset($validated['away_score']) || 
             in_array($validated['status'], ['completed', 'forfeited']) ||
             ($validated['status'] === 'cancelled' && ($validated['home_score'] > 0 || $validated['away_score'] > 0)))) {
            // Refresh match to get updated values (like LiveScore does)
            $match->refresh();
            // Update TournamentGroup standings first (like LiveScore does)
            $match->tournamentGroup->updateStandings($match);
            // Then recalculate Eloquent standings in database
            $groupService = app(\App\Services\TournamentGroupService::class);
            $groupService->recalculateGroupStandings($match->tournamentGroup);
        }

        return redirect()->route('organizations.competitions.show', [$organization, $competition])
            ->with('success', 'Match updated successfully!');
    }

    /**
     * Check if the current user can access a specific competition match.
     */
    private function canAccessCompetitionMatch(CompetitionMatch $match, Organization $organization): bool
    {
        $user = auth()->user();

        // Owner can always access matches
        if ($organization->user_id === $user->id) {
            return true;
        }

        // Check if user is assigned as referee for this specific match
        if ($match->referee_user_id === $user->id) {
            return true;
        }

        // Check if user is a player in the competition
        if ($organization->players()->where('user_id', $user->id)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Save manual bracket assignments.
     */
    public function saveManualBracket(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        if (!$competition->manual_knockout_selection) {
            return response()->json(['success' => false, 'message' => 'Manual knockout selection is not enabled']);
        }

        $request->validate([
            'matches' => 'required|array',
            'matches.*.home_player_id' => 'nullable|exists:players,id',
            'matches.*.away_player_id' => 'nullable|exists:players,id',
        ]);

        $matches = $request->matches;

        // Update each match with the assigned players
        foreach ($matches as $matchId => $players) {
            $match = CompetitionMatch::find($matchId);
            if (!$match || $match->competition_id !== $competition->id) {
                continue;
            }

            $updateData = [];
            if (isset($players['home'])) {
                $updateData['home_player_id'] = $players['home'];
            }
            if (isset($players['away'])) {
                $updateData['away_player_id'] = $players['away'];
            }

            // Check if this creates a bye match
            $isBye = (!isset($players['away']) || !$players['away']) && isset($players['home']) && $players['home'];
            $updateData['is_bye'] = $isBye;

            if ($isBye) {
                $updateData['status'] = 'completed';
                $updateData['home_score'] = 1;
                $updateData['away_score'] = 0;
                $updateData['played_at'] = now();
            }

            $match->update($updateData);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Show manual knockout setup page.
     */
    public function manualKnockoutSetup(Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            abort(403);
        }

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Ensure this is a tournament
        if (!$competition->isTournament()) {
            return redirect()->route('organizations.competitions.show', [$organization, $competition])
                ->with('error', 'Manual knockout setup is only available for tournaments.');
        }

        // Ensure group stage is completed
        $allGroupMatchesCompleted = \App\Models\CompetitionMatch::where('competition_id', $competition->id)
            ->whereNotNull('tournament_group_id')
            ->where('status', '!=', 'completed')
            ->count() === 0;

        if (!$allGroupMatchesCompleted) {
            return redirect()->route('organizations.competitions.show', [$organization, $competition])
                ->with('error', 'Grupna faza još nije završena.');
        }

        return view('organizations.competitions.manual-knockout-setup', compact('organization', 'competition'));
    }

    /**
     * Toggle manual mode for knockout bracket.
     */
    public function toggleManualMode(Request $request, Organization $organization, Competition $competition)
    {
        // Ensure user owns this organization
        if ($organization->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }

        $competition->update([
            'manual_knockout_selection' => !$competition->manual_knockout_selection
        ]);

        return response()->json(['success' => true]);
    }
}

