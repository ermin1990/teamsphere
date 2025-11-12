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
use Illuminate\Database\QueryException;
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
        // Use policy for authorization

        $this->authorize('update', $organization);

        $sports = Sport::active()->get();

        return view('organizations.competitions.create', compact('organization', 'sports'));
    }

    /**
     * Store a newly created competition.
     */
    public function store(Request $request, Organization $organization)
    {
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // EMERGENCY DEBUG
        $logFile = public_path('debug_competition_show.log');
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] === SHOW METHOD CALLED ===\n", FILE_APPEND);
        file_put_contents($logFile, "Organization: {$organization->id} - {$organization->name}\n", FILE_APPEND);
        file_put_contents($logFile, "Competition: {$competition->id} - {$competition->name}\n", FILE_APPEND);
        file_put_contents($logFile, "User: " . auth()->id() . "\n", FILE_APPEND);
        
        try {
            // Use the policy for authorization
            file_put_contents($logFile, "Before authorize...\n", FILE_APPEND);
            $this->authorize('view', $organization);
            file_put_contents($logFile, "After authorize - PASSED\n", FILE_APPEND);

            // Set variables for the view
            $isOwner = $organization->user_id === auth()->id();
            $isPlayer = $organization->players()->where('user_id', auth()->id())->exists();
            $isReferee = $organization->users()
                ->where('users.id', auth()->id())
                ->where('organization_user.role', 'referee')
                ->exists();

            file_put_contents($logFile, "isOwner: " . ($isOwner ? 'YES' : 'NO') . "\n", FILE_APPEND);
            file_put_contents($logFile, "isPlayer: " . ($isPlayer ? 'YES' : 'NO') . "\n", FILE_APPEND);
            file_put_contents($logFile, "isReferee: " . ($isReferee ? 'YES' : 'NO') . "\n", FILE_APPEND);

            // Ensure competition belongs to organization
            if ($competition->organization_id !== $organization->id) {
                file_put_contents($logFile, "ERROR: Competition does not belong to organization!\n", FILE_APPEND);
                abort(404);
            }
            
            file_put_contents($logFile, "Competition belongs to organization - OK\n", FILE_APPEND);
        } catch (\Exception $e) {
            file_put_contents($logFile, "EXCEPTION: " . $e->getMessage() . "\n", FILE_APPEND);
            file_put_contents($logFile, $e->getTraceAsString() . "\n", FILE_APPEND);
            throw $e;
        }

        file_put_contents($logFile, "Starting tournament checks...\n", FILE_APPEND);

        // Load relationships early so they're available throughout the method
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

        // Generate group matches if tournament is active but no matches exist
        if ($competition->isTournament() && $competition->status === 'active' && $competition->tournamentGroups()->count() > 0) {
            file_put_contents($logFile, "Checking group matches...\n", FILE_APPEND);
            $existingMatches = \App\Models\CompetitionMatch::where('competition_id', $competition->id)
                ->whereNotNull('tournament_group_id')
                ->count();
            if ($existingMatches === 0) {
                file_put_contents($logFile, "Generating group matches...\n", FILE_APPEND);
                $competition->generateGroupMatches();
            }
        }

        file_put_contents($logFile, "Checking standings...\n", FILE_APPEND);

        // Ensure standings exist for tournament groups (but don't reset existing ones)
        if ($competition->isTournament() && $competition->tournamentGroups()->count() > 0) {
            file_put_contents($logFile, "Processing tournament groups...\n", FILE_APPEND);
            try {
                $groups = $competition->tournamentGroups()->get();
                file_put_contents($logFile, "Groups count: " . $groups->count() . "\n", FILE_APPEND);
                
                foreach ($groups as $group) {
                    foreach ($group->player_ids as $playerId) {
                        try {
                            // Only create if doesn't exist - don't update/reset existing standings
                            \App\Models\Standing::firstOrCreate(
                                [
                                    'competition_id' => $competition->id,
                                    'tournament_group_id' => $group->id,
                                    'player_id' => $playerId,
                                ],
                                [
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
                                ]
                            );
                        } catch (\Illuminate\Database\QueryException $e) {
                            // Ignore duplicate entry errors (race condition)
                            if ($e->getCode() !== '23000') {
                                throw $e;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                file_put_contents($logFile, "ERROR processing tournament groups: " . $e->getMessage() . "\n", FILE_APPEND);
                file_put_contents($logFile, "Stack trace: " . $e->getTraceAsString() . "\n", FILE_APPEND);
                // Continue execution - don't let this block the page load
            }
        }

        file_put_contents($logFile, "Loading organization players...\n", FILE_APPEND);
        $organization->load('players');

        file_put_contents($logFile, "Loading knockout matches...\n", FILE_APPEND);
        // Load knockout matches
        $knockoutMatches = \App\Models\CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->with('homePlayer', 'awayPlayer')
            ->orderBy('round_number')
            ->orderBy('match_order')
            ->get();

        file_put_contents($logFile, "Returning view...\n", FILE_APPEND);
        file_put_contents($logFile, "=== SUCCESS ===\n\n", FILE_APPEND);

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

        // Route to futsal-specific view if this is a futsal competition
        if ($competition->isFutsal()) {
            // Load knockout matches for futsal tournaments
            $knockoutMatches = \App\Models\CompetitionMatch::where('competition_id', $competition->id)
                ->where('phase', 'knockout')
                ->with('homeTeam', 'awayTeam')
                ->orderBy('round_number')
                ->orderBy('match_order')
                ->get();

            return view('organizations.competitions.futsal.show', compact('organization', 'competition', 'isOwner', 'isPlayer', 'isReferee', 'knockoutMatches'));
        }

        return view('organizations.competitions.show', compact('organization', 'competition', 'isOwner', 'isPlayer', 'isReferee', 'knockoutMatches'));
    }

    /**
     * Update the specified competition.
     */
    public function update(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        \Log::info('saveGroups called', [
            'competition_id' => $competition->id,
            'user_id' => auth()->id(),
        ]);
        
        // Use policy for authorization

        $this->authorize('update', $organization);

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        $request->validate([
            'groups' => ['required', 'array', 'min:1'],
            'groups.*' => ['required', 'array'],
            'groups.*.players' => ['required', 'array', 'min:2'], // Removed max limit
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
                    try {
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
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Ignore duplicate entry errors
                        if ($e->getCode() !== '23000') {
                            throw $e;
                        }
                        \Log::warning('Duplicate standing entry in saveGroups', [
                            'competition_id' => $competition->id,
                            'tournament_group_id' => $group->id,
                            'player_id' => $playerId,
                        ]);
                    }
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
                    try {
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
                    } catch (\Illuminate\Database\QueryException $e) {
                        // Ignore duplicate entry errors
                        if ($e->getCode() !== '23000') {
                            throw $e;
                        }
                        \Log::warning('Duplicate standing entry in saveGroups', [
                            'competition_id' => $competition->id,
                            'tournament_group_id' => $group->id,
                            'player_id' => $playerId,
                        ]);
                    }
                }
            }
        }
        
        // Delete groups that are no longer in the request
        $groupsToDelete = $competition->tournamentGroups()
            ->whereNotIn('group_number', $processedGroupNumbers)
            ->get();
        
        foreach ($groupsToDelete as $groupToDelete) {
            // Delete standings for this group before deleting the group
            Standing::where('competition_id', $competition->id)
                ->where('tournament_group_id', $groupToDelete->id)
                ->delete();
        }
        
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
        // Use policy for authorization

        $this->authorize('update', $organization);

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Route to futsal-specific settings if this is a futsal competition
        if ($competition->isFutsal()) {
            return view('organizations.competitions.futsal.settings', compact('organization', 'competition'));
        }

        return view('organizations.competitions.settings', compact('organization', 'competition'));
    }

    /**
     * Update competition settings.
     */
    public function updateSettings(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        $this->authorize('update', $organization);

        if ($competition->status !== 'draft') {
            return back()->with('error', 'Cannot change settings after competition has started.');
        }

        // Different validation for futsal vs table tennis
        if ($competition->isFutsal()) {
            $request->validate([
                // Futsal match settings
                'match_duration' => ['required', 'integer', 'min:20', 'max:90'],
                'halftime_duration' => ['required', 'integer', 'min:5', 'max:20'],
                'max_players_on_pitch' => ['required', 'integer', 'min:3', 'max:11'],
                'min_players_on_pitch' => ['required', 'integer', 'min:2', 'max:7'],
                'max_substitutes' => ['required', 'integer', 'min:0', 'max:15'],
                'unlimited_substitutions' => ['required', 'integer', 'in:0,1'],
                'yellow_card_limit' => ['required', 'integer', 'min:1', 'max:5'],
                'red_card_suspension' => ['required', 'integer', 'min:0', 'max:10'],
                'has_overtime' => ['boolean'],
                'overtime_duration' => ['nullable', 'integer', 'min:5', 'max:30'],
                'has_penalty_shootout' => ['boolean'],
                // Points system
                'points_for_win' => ['required', 'integer', 'min:0', 'max:10'],
                'points_for_draw' => ['required', 'integer', 'min:0', 'max:10'],
                'points_for_loss' => ['required', 'integer', 'min:0', 'max:10'],
            ]);

            $competition->update([
                'match_duration' => $request->match_duration,
                'halftime_duration' => $request->halftime_duration,
                'max_players_on_pitch' => $request->max_players_on_pitch,
                'min_players_on_pitch' => $request->min_players_on_pitch,
                'max_substitutes' => $request->max_substitutes,
                'unlimited_substitutions' => $request->unlimited_substitutions,
                'yellow_card_limit' => $request->yellow_card_limit,
                'red_card_suspension' => $request->red_card_suspension,
                'has_overtime' => $request->boolean('has_overtime'),
                'overtime_duration' => $request->overtime_duration,
                'has_penalty_shootout' => $request->boolean('has_penalty_shootout'),
                'points_for_win' => $request->points_for_win,
                'points_for_draw' => $request->points_for_draw,
                'points_for_loss' => $request->points_for_loss,
            ]);
        } else {
            // Table tennis settings
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
                'group_rounds' => ['nullable', 'integer', 'min:1', 'max:2'],
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
                'group_rounds' => $request->group_rounds,
            ]);
        }

        return redirect()
            ->route('organizations.competitions.show', [$organization, $competition])
            ->with('success', 'Competition settings updated successfully!');
    }

    /**
     * Start the competition.
     */
    public function startCompetition(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
    public function quickResult(Request $request, CompetitionMatch $match)
    {
        // Get the competition through the match
        $competition = $match->competition;
        $organization = $competition->organization;

        // Use policy for authorization
        $this->authorize('view', $organization);

        $request->validate([
            'home_score' => 'required|integer|min:0|max:10',
            'away_score' => 'required|integer|min:0|max:10',
            'sets' => 'nullable|array',
            'sets.*.home' => 'required_with:sets|integer|min:0',
            'sets.*.away' => 'required_with:sets|integer|min:0',
            'scroll_position' => 'nullable|integer|min:0',
        ]);

        // Save scroll position to session
        if ($request->has('scroll_position')) {
            session(['scroll_position' => $request->scroll_position]);
        }

        // Normalize sets format from home_score/away_score to home/away
        $normalizedSets = [];
        if (!empty($request->sets)) {
            foreach ($request->sets as $set) {
                $normalizedSets[] = [
                    'home' => $set['home_score'] ?? $set['home'] ?? 0,
                    'away' => $set['away_score'] ?? $set['away'] ?? 0,
                ];
            }
        }
        
        // Only generate empty set placeholders if sets were explicitly provided
        // Don't auto-generate 0-0 sets when no sets are entered
        if (empty($normalizedSets) && !empty($request->sets)) {
            $totalSets = max($request->home_score, $request->away_score);
            for ($i = 0; $i < $totalSets; $i++) {
                $normalizedSets[] = [
                    'home' => 0,
                    'away' => 0,
                ];
            }
        }

        // Update match with results
        $match->update([
            'home_score' => $request->home_score,
            'away_score' => $request->away_score,
            'sets' => $normalizedSets,
            'status' => 'completed',
            'played_at' => now(),
        ]);

        // Update standings if tournament
        if ($competition->type === 'tournament' && $match->tournamentGroup) {
            $match->refresh();
            $groupService = app(\App\Services\TournamentGroupService::class);
            $groupService->recalculateGroupStandings($match->tournamentGroup);
        }

        // Propagate winner changes in knockout bracket
        if ($competition->type === 'tournament' && $match->phase === 'knockout' && $match->status === 'completed') {
            $competition->propagateWinnerChanges($match);
        }

        return back()->with('success', 'Match result saved successfully!');
    }

    /**
     * Show match details.
     */
    public function showMatch(Organization $organization, Competition $competition, CompetitionMatch $match)
    {
        // Use policy for authorization
        $this->authorize('view', $organization);

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
        // Use policy for authorization
        $this->authorize('view', $organization);

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
        // Use policy for authorization
        $this->authorize('view', $organization);

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404);
        }

        // Validation
        $validated = $request->validate([
            'status' => 'required|in:scheduled,in_progress,completed,forfeited,cancelled',
            'home_score' => 'nullable|integer|min:0|max:10',
            'away_score' => 'nullable|integer|min:0|max:10',
            'sets' => 'nullable|array',
            'sets.*.home_score' => 'required_with:sets|integer|min:0',
            'sets.*.away_score' => 'required_with:sets|integer|min:0',
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
        } elseif ($validated['status'] === 'in_progress') {
            // Update current scores and sets for live matches
            $updateData['home_score'] = $validated['home_score'] ?? 0;
            $updateData['away_score'] = $validated['away_score'] ?? 0;

            // Normalize sets format from home_score/away_score to home/away
            $normalizedSets = [];
            if (!empty($validated['sets'])) {
                foreach ($validated['sets'] as $set) {
                    $normalizedSets[] = [
                        'home' => $set['home_score'] ?? $set['home'] ?? 0,
                        'away' => $set['away_score'] ?? $set['away'] ?? 0,
                    ];
                }
            }
            $updateData['sets'] = $normalizedSets;
            $updateData['played_at'] = $match->played_at ?? now();
        } elseif ($validated['status'] === 'completed') {
            $updateData['home_score'] = $validated['home_score'] ?? 0;
            $updateData['away_score'] = $validated['away_score'] ?? 0;

            // Normalize sets format from home_score/away_score to home/away
            $normalizedSets = [];
            if (!empty($validated['sets'])) {
                foreach ($validated['sets'] as $set) {
                    $normalizedSets[] = [
                        'home' => $set['home_score'] ?? $set['home'] ?? 0,
                        'away' => $set['away_score'] ?? $set['away'] ?? 0,
                    ];
                }
            }
            $updateData['sets'] = $normalizedSets;
            $updateData['played_at'] = $match->played_at ?? now();
        } elseif ($validated['status'] === 'forfeited') {
            $updateData['forfeited_by'] = $validated['forfeited_by'];
            $updateData['played_at'] = now();
        }

        $match->update($updateData);

        \Log::info('Match updated', [
            'match_id' => $match->id,
            'status' => $validated['status'],
            'competition_type' => $competition->type,
            'has_tournament_group' => !is_null($match->tournamentGroup),
            'tournament_group_id' => $match->tournament_group_id,
        ]);

        // Update standings if tournament (recalculate for any status change)
        if ($competition->type === 'tournament' && $match->tournamentGroup) {
            \Log::info('About to recalculate standings');
            $match->refresh();
            $groupService = app(\App\Services\TournamentGroupService::class);
            $groupService->recalculateGroupStandings($match->tournamentGroup);
            \Log::info('Finished recalculating standings');
        }

        // Propagate winner changes in knockout bracket
        if ($competition->type === 'tournament' && $match->phase === 'knockout' && $match->status === 'completed') {
            $competition->propagateWinnerChanges($match);
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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
            \Log::error('Error generating knockout bracket', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error generating knockout bracket: ' . $e->getMessage());
        }
    }

    /**
     * Save manually created knockout bracket.
     */
    public function saveManualKnockout(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

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
     * Regenerate a specific knockout round.
     */
    public function regenerateKnockoutRound(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        $this->authorize('update', $organization);

        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        $request->validate([
            'round_number' => ['required', 'integer', 'min:2'],
        ]);

        try {
            $competition->regenerateKnockoutRound($request->round_number);

            return back()->with('success', 'Runda ' . $request->round_number . ' je regenerisana uspješno!');
        } catch (\Exception $e) {
            return back()->with('error', 'Greška pri regenerisanju runde: ' . $e->getMessage());
        }
    }

    /**
     * Reset knockout phase.
     */
    public function resetKnockout(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        $this->authorize('update', $organization);

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
        // Use policy for authorization

        $this->authorize('update', $organization);

        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        // Allow resetting groups phase at any time - user knows what they're doing
        
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

    /**
     * Show futsal competition setup form.
     */
    public function setupFutsalCompetition(Organization $organization, Competition $competition)
    {
        $this->authorize('update', $organization);

        if (!$competition->isFutsal()) {
            return redirect()
                ->route('organizations.competitions.show', [$organization, $competition])
                ->with('error', 'Ovo nije futsal takmičenje.');
        }

        $teams = $competition->futsalTeams()->where('is_active', true)->get();

        return view('organizations.competitions.futsal.setup', compact('organization', 'competition', 'teams'));
    }

    /**
     * Generate futsal competition (league or tournament).
     */
    public function generateFutsalCompetition(Request $request, Organization $organization, Competition $competition)
    {
        $this->authorize('update', $organization);

        if (!$competition->isFutsal()) {
            return back()->with('error', 'Ovo nije futsal takmičenje.');
        }

        $validated = $request->validate([
            'competition_format' => 'required|in:league,tournament',
            'round_robin_type' => 'required|in:single,double',
            'group_count' => 'nullable|integer|min:2|max:8',
            'teams_per_group' => 'nullable|integer|min:2|max:8',
            'teams_advancing_per_group' => 'nullable|integer|min:1|max:4',
        ]);

        $futsalService = app(\App\Services\FutsalCompetitionService::class);
        $teams = $competition->futsalTeams()->where('is_active', true)->get();

        if ($teams->count() < 2) {
            return back()->with('error', 'Potrebna su najmanje 2 tima za kreiranje takmičenja.');
        }

        try {
            \DB::beginTransaction();

            if ($validated['competition_format'] === 'league') {
                // Generate league matches
                $doubleRoundRobin = $validated['round_robin_type'] === 'double';
                $futsalService->generateLeagueMatches($competition, $doubleRoundRobin);

                $competition->update([
                    'type' => 'league',
                    'current_phase' => 'league',
                    'status' => 'in_progress',
                ]);

                $message = 'Liga je uspješno generisana sa ' . ($doubleRoundRobin ? 'dvokružnim' : 'jednokružnim') . ' sistemom!';
            } else {
                // Generate tournament with groups
                $groupCount = $validated['group_count'] ?? 2;
                $doubleRoundRobin = $validated['round_robin_type'] === 'double';
                
                $futsalService->generateTournamentGroups($competition, $groupCount, $doubleRoundRobin);

                $competition->update([
                    'type' => 'tournament',
                    'current_phase' => 'groups',
                    'status' => 'in_progress',
                    'group_count' => $groupCount,
                    'players_advancing_per_group' => $validated['teams_advancing_per_group'] ?? 2,
                ]);

                $message = 'Turnir je uspješno generisan sa ' . $groupCount . ' grupe/a (' . ($doubleRoundRobin ? 'dvokružni' : 'jednokružni') . ' sistem)!';
            }

            \DB::commit();

            return redirect()
                ->route('organizations.competitions.futsal.schedule', [$organization, $competition])
                ->with('success', $message);

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error generating futsal competition', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Greška pri generisanju takmičenja: ' . $e->getMessage());
        }
    }

    /**
     * Show futsal groups setup page (drag & drop).
     */
    public function setupFutsalGroups(Organization $organization, Competition $competition)
    {
        $this->authorize('update', $organization);

        if (!$competition->isFutsal()) {
            return back()->with('error', 'Ovo nije futsal takmičenje.');
        }

        $teams = $competition->futsalTeams()->where('is_active', true)->get();
        
        if ($teams->count() < 2) {
            return redirect()
                ->route('organizations.competitions.futsal.teams.index', [$organization, $competition])
                ->with('error', 'Potrebna su najmanje 2 tima za kreiranje grupa.');
        }

        // Get existing groups or create default structure
        $groups = $competition->tournamentGroups()->with('futsalTeams')->get();
        
        if ($groups->isEmpty()) {
            // Create default groups (A, B, C, D)
            $groupCount = min(4, ceil($teams->count() / 3)); // At least 3 teams per group
            $groupLetters = range('A', 'Z');
            
            $groupsData = [];
            for ($i = 0; $i < $groupCount; $i++) {
                $groupsData[] = [
                    'name' => $groupLetters[$i],
                    'teams' => [],
                ];
            }
        } else {
            $groupsData = $groups->map(function ($group) {
                return [
                    'name' => $group->name,
                    'teams' => $group->futsalTeams->pluck('id')->toArray(),
                ];
            })->toArray();
        }

        // Get available teams (not assigned to any group)
        $assignedTeamIds = collect($groupsData)->pluck('teams')->flatten()->toArray();
        $availableTeams = $teams->whereNotIn('id', $assignedTeamIds);

        return view('organizations.competitions.futsal.setup-groups', compact(
            'organization',
            'competition',
            'teams',
            'availableTeams',
            'groups',
            'groupsData'
        ));
    }

    /**
     * Save futsal groups configuration and generate matches.
     */
    public function saveFutsalGroups(Request $request, Organization $organization, Competition $competition)
    {
        $this->authorize('update', $organization);

        if (!$competition->isFutsal()) {
            return back()->with('error', 'Ovo nije futsal takmičenje.');
        }

        $validated = $request->validate([
            'groups' => 'required|array|min:2',
            'groups.*.name' => 'required|string',
            'groups.*.teams' => 'required|array|min:2',
            'groups.*.teams.*' => 'exists:futsal_teams,id',
            'round_robin_type' => 'required|in:single,double',
            'teams_advancing_per_group' => 'nullable|integer|min:1|max:4',
        ]);

        try {
            \DB::beginTransaction();

            // Delete existing groups
            $competition->tournamentGroups()->delete();

            $futsalService = app(\App\Services\FutsalCompetitionService::class);

            // Create groups with teams
            foreach ($validated['groups'] as $index => $groupData) {
                $group = $competition->tournamentGroups()->create([
                    'name' => $groupData['name'],
                    'competition_id' => $competition->id,
                    'group_number' => $index + 1,
                ]);

                // Attach teams to group
                $group->futsalTeams()->attach($groupData['teams']);

                // Create standings for each team
                foreach ($groupData['teams'] as $teamId) {
                    \App\Models\FutsalStanding::create([
                        'competition_id' => $competition->id,
                        'futsal_team_id' => $teamId,
                        'tournament_group_id' => $group->id,
                        'played' => 0,
                        'won' => 0,
                        'drawn' => 0,
                        'lost' => 0,
                        'goals_for' => 0,
                        'goals_against' => 0,
                        'goal_difference' => 0,
                        'points' => 0,
                        'position' => 0,
                    ]);
                }

                // Generate group matches
                $doubleRoundRobin = $validated['round_robin_type'] === 'double';
                $futsalService->generateGroupMatches($group, $doubleRoundRobin);
            }

            // Update competition
            $competition->update([
                'type' => 'tournament',
                'current_phase' => 'groups',
                'status' => 'active',
                'group_count' => count($validated['groups']),
                'players_advancing_per_group' => $validated['teams_advancing_per_group'] ?? 2,
            ]);

            \DB::commit();

            $roundType = $validated['round_robin_type'] === 'double' ? 'dvokružnim' : 'jednokružnim';
            return redirect()
                ->route('organizations.competitions.futsal.schedule', [$organization, $competition])
                ->with('success', "Grupe su uspješno kreirane sa {$roundType} sistemom!");

        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error saving futsal groups', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Greška pri kreiranju grupa: ' . $e->getMessage());
        }
    }

    /**
     * Show futsal competition schedule.
     */
    public function showFutsalSchedule(Organization $organization, Competition $competition)
    {
        $this->authorize('view', $organization);

        if (!$competition->isFutsal()) {
            return redirect()
                ->route('organizations.competitions.show', [$organization, $competition])
                ->with('error', 'Ovo nije futsal takmičenje.');
        }

        if ($competition->type === 'league') {
            // League schedule grouped by rounds
            $matches = $competition->futsalMatches()
                ->with(['homeTeam', 'awayTeam'])
                ->orderBy('round')
                ->orderBy('id')
                ->get()
                ->groupBy('round');

            return view('organizations.competitions.futsal.league-schedule', compact('organization', 'competition', 'matches'));
        } else {
            // Tournament schedule with groups
            $groups = \App\Models\TournamentGroup::where('competition_id', $competition->id)
                ->with([
                    'futsalMatches' => function ($query) {
                        $query->with(['homeTeam', 'awayTeam'])->orderBy('round');
                    },
                    'futsalStandings' => function ($query) {
                        $query->with('futsalTeam')->orderByStandings();
                    }
                ])
                ->orderBy('group_number')
                ->get();

            $knockoutMatches = $competition->futsalMatches()
                ->where('phase', 'knockout')
                ->with(['homeTeam', 'awayTeam'])
                ->orderBy('round')
                ->orderBy('match_order')
                ->get()
                ->groupBy('round');

            return view('organizations.competitions.futsal.tournament-schedule', compact('organization', 'competition', 'groups', 'knockoutMatches'));
        }
    }

    /**
     * Show futsal standings.
     */
    public function showFutsalStandings(Organization $organization, Competition $competition)
    {
        $this->authorize('view', $organization);

        if (!$competition->isFutsal()) {
            return redirect()
                ->route('organizations.competitions.show', [$organization, $competition])
                ->with('error', 'Ovo nije futsal takmičenje.');
        }

        if ($competition->type === 'league') {
            // League standings
            $standings = $competition->futsalStandings()
                ->with('futsalTeam')
                ->orderByStandings()
                ->get();

            return view('organizations.competitions.futsal.league-standings', compact('organization', 'competition', 'standings'));
        } else {
            // Tournament standings by groups
            $groups = \App\Models\TournamentGroup::where('competition_id', $competition->id)
                ->with([
                    'futsalStandings' => function ($query) {
                        $query->with('futsalTeam')->orderByStandings();
                    }
                ])
                ->orderBy('group_number')
                ->get();

            return view('organizations.competitions.futsal.tournament-standings', compact('organization', 'competition', 'groups'));
        }
    }

    /**
     * Generate knockout bracket from group stage.
     */
    public function generateFutsalKnockout(Organization $organization, Competition $competition)
    {
        $this->authorize('update', $organization);

        if (!$competition->isFutsal() || $competition->type !== 'tournament') {
            return back()->with('error', 'Ova akcija je dostupna samo za futsal turnire.');
        }

        try {
            $futsalService = app(\App\Services\FutsalCompetitionService::class);
            $teamsPerGroup = $competition->players_advancing_per_group ?? 2;

            $futsalService->generateKnockoutBracket($competition, $teamsPerGroup);

            $competition->update([
                'current_phase' => 'knockout',
            ]);

            return redirect()
                ->route('organizations.competitions.futsal.schedule', [$organization, $competition])
                ->with('success', 'Knockout faza je uspješno generisana!');

        } catch (\Exception $e) {
            \Log::error('Error generating futsal knockout', [
                'competition_id' => $competition->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Greška: ' . $e->getMessage());
        }
    }

    /**
     * Advance futsal knockout round.
     */
    public function advanceFutsalKnockout(Organization $organization, Competition $competition, Request $request)
    {
        $this->authorize('update', $organization);

        if (!$competition->isFutsal() || $competition->current_phase !== 'knockout') {
            return back()->with('error', 'Ova akcija je dostupna samo u knockout fazi.');
        }

        $validated = $request->validate([
            'current_round' => 'required|integer|min:1',
        ]);

        try {
            $futsalService = app(\App\Services\FutsalCompetitionService::class);
            $futsalService->advanceKnockoutRound($competition, $validated['current_round']);

            return back()->with('success', 'Sljedeća runda je uspješno generisana!');

        } catch (\Exception $e) {
            return back()->with('error', 'Greška: ' . $e->getMessage());
        }
    }

    /**
     * Remove team from futsal competition.
     */
    public function removeFutsalTeam(Organization $organization, Competition $competition, \App\Models\FutsalTeam $team)
    {
        $this->authorize('update', $organization);

        if (!$competition->isFutsal()) {
            return back()->with('error', 'Ova akcija je dostupna samo za futsal takmičenja.');
        }

        try {
            $futsalService = app(\App\Services\FutsalCompetitionService::class);
            $futsalService->removeTeamFromCompetition($competition, $team);

            return back()->with('success', 'Tim ' . $team->name . ' je uklonjen iz takmičenja, svi rezultati su poništeni.');

        } catch (\Exception $e) {
            \Log::error('Error removing futsal team', [
                'competition_id' => $competition->id,
                'team_id' => $team->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Greška pri uklanjanju tima: ' . $e->getMessage());
        }
    }

    /**
     * Award walkover for futsal match.
     */
    public function awardFutsalWalkover(Organization $organization, Competition $competition, \App\Models\FutsalMatch $match, Request $request)
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'winner' => 'required|in:home,away',
        ]);

        try {
            $futsalService = app(\App\Services\FutsalCompetitionService::class);
            $futsalService->awardWalkover($match, $validated['winner']);

            return back()->with('success', 'Službena pobeda (walkover 3-0) je dodijeljena.');

        } catch (\Exception $e) {
            return back()->with('error', 'Greška: ' . $e->getMessage());
        }
    }
}

