<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\TeamMatch;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Standing;
use App\Models\Team;
use App\Models\TournamentGroup;
use App\Http\Controllers\TeamMatchController;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        Gate::authorize('update', $organization);

        $categories = $organization->categories()->active()->get();

        return view('organizations.competitions.create', compact('organization', 'categories'));
    }

    /**
     * Store a newly created competition.
     */
    public function store(Request $request, Organization $organization)
    {
        // Use policy for authorization

        Gate::authorize('update', $organization);

        // Check if user can create more competitions
        if (!auth()->user()->canCreateMoreCompetitions($organization->id)) {
            return back()->withErrors(['limit' => __('You have reached the maximum number of competitions allowed for this organization.')]);
        }

        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'category_id' => ['nullable', 'exists:categories,id'],
                'type' => ['required', 'in:tournament,league'],
                'is_team_based' => ['required_if:type,league', 'boolean'],
                'is_double_round' => ['nullable', 'boolean'],
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
            'sport_id' => $organization->sport_id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_teams' => $request->max_teams ?: 8,
            'is_team_based' => (bool) ($request->input('is_team_based', 0)),
            'is_double_round' => (bool) ($request->input('is_double_round', 0)),
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
        // Use the policy for authorization
        Gate::authorize('view', $organization);

        // Set variables for the view
        $isOwner = $organization->user_id === auth()->id();
        $isPlayer = $organization->players()->where('user_id', auth()->id())->exists();
        $isReferee = $organization->users()
            ->where('users.id', auth()->id())
            ->where('organization_user.role', 'referee')
            ->exists();

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

        // Generate team matches if league is active but no matches exist
        if (!$competition->isTournament() && $competition->is_team_based && $competition->status === 'active') {
            if ($competition->teamMatches()->count() === 0) {
                $competition->generateTeamMatches();
            }
        }

        // Ensure standings exist for tournament groups (but don't reset existing ones)
        if ($competition->isTournament() && $competition->tournamentGroups()->count() > 0) {
            foreach ($competition->tournamentGroups as $group) {
                // Skip if player_ids is null or empty
                if (empty($group->player_ids)) {
                    continue;
                }
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
        }

        // Ensure standings exist for team-based leagues
        if (!$competition->isTournament() && $competition->is_team_based && $competition->status === 'active') {
            foreach ($competition->teams as $team) {
                \App\Models\Standing::firstOrCreate(
                    [
                        'competition_id' => $competition->id,
                        'team_id' => $team->id,
                    ],
                    [
                        'played' => 0,
                        'won' => 0,
                        'drawn' => 0,
                        'lost' => 0,
                        'points' => 0,
                    ]
                );
            }
        }

        $competition->load([
            'sport',
            'teams.players',
            'matches.homeTeam',
            'matches.awayTeam',
            'matches.homePlayer',
            'matches.awayPlayer',
            'teamMatches.homeTeam',
            'teamMatches.awayTeam',
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

        return view('organizations.competitions.show', compact('organization', 'competition', 'isOwner', 'isPlayer', 'isReferee', 'knockoutMatches'));
    }

    /**
     * Update the specified competition.
     */
    public function update(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        Gate::authorize('update', $organization);

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
        // if ($competition->status !== 'draft') {
        //     return back()->withErrors(['general' => __('Cannot modify settings for a competition that has already started.')]);
        // }

        // Allow updating basic settings even if started
        $competition->update($request->only([
            'name', 'description', 'start_date', 'end_date', 
            'max_teams', 'max_participants', 'sets_to_win', 
            'points_per_set', 'deuce_at', 'must_win_by_two',
            'points_for_win', 'points_for_draw', 'points_for_loss'
        ]));

        return back()->with('success', 'Postavke takmičenja uspješno ažurirane!');
    }

    /**
     * Show the manage players page.
     */
    public function managePlayers(Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        Gate::authorize('update', $organization);

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Allow managing players even if started
        // if ($competition->status !== 'draft') {
        //     return redirect()
        //         ->route('organizations.competitions.show', [$organization, $competition])
        //         ->with('error', __('Can only manage players for draft competitions.'));
        // }

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        if (!$competition->isTournament()) {
            return back()->with('error', 'This is not a tournament.');
        }

        // if ($competition->status !== 'draft') {
        //     return back()->with('error', 'Competition has already started.');
        // }

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        $categories = $organization->categories()->active()->get();

        return view('organizations.competitions.settings', compact('organization', 'competition', 'categories'));
    }

    /**
     * Update competition settings.
     */
    public function updateSettings(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        Gate::authorize('update', $organization);

        // Allow changing settings anytime
        // if ($competition->status !== 'draft') {
        //     return back()->with('error', 'Cannot change settings after competition has started.');
        // }

        // Poena/deuce/tiebreak polja se ne prikazuju u formi za sportove sa
        // gemovima/setovima (Tenis, Padel) - njihova pravila su fiksna (vidi
        // TennisLiveScore), pa ne treba da budu obavezna van stonog tenisa.
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'sets_to_win' => ['required', 'integer', 'min:1', 'max:7'],
            'points_per_set' => ['nullable', 'integer', 'min:7', 'max:21'],
            'deuce_at' => ['nullable', 'integer', 'min:5'],
            'must_win_by_two' => ['boolean'],
            'points_for_win' => ['required', 'integer', 'min:0', 'max:10'],
            'points_for_draw' => ['required', 'integer', 'min:0', 'max:10'],
            'points_for_loss' => ['required', 'integer', 'min:0', 'max:10'],
            'has_tiebreak' => ['boolean'],
            'tiebreak_points' => ['nullable', 'integer', 'min:5', 'max:15'],
            'is_double_round' => ['nullable', 'boolean'],
            // Tournament-specific validation
            'players_advancing_per_group' => ['nullable', 'integer', 'min:1', 'max:4'],
            'group_rounds' => ['nullable', 'integer', 'min:1', 'max:2'],
        ]);

        $updateData = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'sets_to_win' => $request->sets_to_win,
            'points_per_set' => $request->points_per_set ?? $competition->points_per_set,
            'deuce_at' => $request->deuce_at ?? $competition->deuce_at,
            'must_win_by_two' => $request->boolean('must_win_by_two'),
            'points_for_win' => $request->points_for_win,
            'points_for_draw' => $request->points_for_draw,
            'points_for_loss' => $request->points_for_loss,
            'has_tiebreak' => $request->boolean('has_tiebreak'),
            'tiebreak_points' => $request->tiebreak_points ?? $competition->tiebreak_points,
            'is_double_round' => $request->boolean('is_double_round'),
        ];

        // Only update tournament-specific fields if it's a tournament
        if ($competition->isTournament()) {
            if ($request->has('players_advancing_per_group')) {
                $updateData['players_advancing_per_group'] = $request->players_advancing_per_group;
            }
            if ($request->has('group_rounds')) {
                $updateData['group_rounds'] = $request->group_rounds;
            }
        }

        $competition->update($updateData);

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

        Gate::authorize('update', $organization);

        if ($competition->status !== 'draft') {
            return back()->with('error', 'Competition has already started.');
        }

        if ($competition->isTournament() && $competition->tournamentGroups()->count() === 0) {
            return back()->with('error', 'Please setup groups before starting the tournament.');
        }

        // Update competition status and current phase
        $currentPhase = 'groups';
        if ($competition->isTournament()) {
            $currentPhase = 'groups';
        } elseif ($competition->isLeague()) {
            $currentPhase = 'league';
        }

        $competition->update([
            'status' => 'active',
            'current_phase' => $currentPhase,
        ]);

        // Generate matches and standings based on competition type
        if ($competition->isTournament()) {
            $competition->generateGroupMatches();
        } elseif ($competition->isLeague()) {
            if ($competition->is_team_based) {
                // Only generate if not requested to be manual
                if (!$request->has('manual_matches')) {
                    $competition->generateTeamMatches();
                } else {
                    // Still initialize standings
                    foreach ($competition->teams as $team) {
                        Standing::firstOrCreate([
                            'competition_id' => $competition->id,
                            'team_id' => $team->id,
                        ], [
                            'played' => 0,
                            'won' => 0,
                            'drawn' => 0,
                            'lost' => 0,
                            'points' => 0,
                        ]);
                    }
                }
            } else {
                if (!$request->has('manual_matches')) {
                    $competition->generateLeagueMatches();
                    $competition->generateLeagueStandings();
                }
            }
        }

        $message = $request->has('manual_matches') 
            ? 'Takmičenje uspješno aktivirano! Sada možete ručno dodavati mečeve.'
            : 'Takmičenje uspješno aktivirano! Mečevi su automatski generisani.';

        return back()->with('success', $message);
    }

    /**
     * Generate tournament groups.
     */
    public function generateGroups(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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
        Gate::authorize('view', $organization);

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
        Gate::authorize('view', $organization);

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404);
        }

        $isOwner = $organization->user_id === auth()->id();

        // Load relationships
        $match->load(['homePlayer', 'awayPlayer', 'homeTeam', 'awayTeam', 'tournamentGroup', 'teamMatch']);

        $doublesPlayers = [
            'home_1' => null,
            'home_2' => null,
            'away_1' => null,
            'away_2' => null,
        ];

        if ($match->position_code === 'Dubl' && $match->teamMatch && $match->teamMatch->lineup) {
            $lineup = $match->teamMatch->lineup;
            $playerIds = [
                $lineup['home_dubl_1'] ?? null,
                $lineup['home_dubl_2'] ?? null,
                $lineup['away_dubl_1'] ?? null,
                $lineup['away_dubl_2'] ?? null,
            ];
            $players = Player::whereIn('id', array_filter($playerIds))->get()->keyBy('id');
            
            $doublesPlayers = [
                'home_1' => $players->get($lineup['home_dubl_1'] ?? null),
                'home_2' => $players->get($lineup['home_dubl_2'] ?? null),
                'away_1' => $players->get($lineup['away_dubl_1'] ?? null),
                'away_2' => $players->get($lineup['away_dubl_2'] ?? null),
            ];
        }

        return view('organizations.competitions.matches.show', compact('organization', 'competition', 'match', 'isOwner', 'doublesPlayers'));
    }

    /**
     * Show edit form for a match.
     */
    public function editMatch(Organization $organization, Competition $competition, CompetitionMatch $match)
    {
        // Use policy for authorization
        Gate::authorize('view', $organization);

        // Ensure match belongs to competition
        if ($match->competition_id !== $competition->id) {
            abort(404);
        }

        $isOwner = $organization->user_id === auth()->id();

        $homePlayers = collect();
        $awayPlayers = collect();

        if ($competition->is_team_based) {
            if ($match->home_team_id) {
                $homePlayers = Team::find($match->home_team_id)->players;
            }
            if ($match->away_team_id) {
                $awayPlayers = Team::find($match->away_team_id)->players;
            }
        }

        // Get unique referee names for autocomplete
        $referees = CompetitionMatch::whereNotNull('referee_name')
            ->distinct()
            ->pluck('referee_name');

        return view('organizations.competitions.matches.edit', compact(
            'organization', 
            'competition', 
            'match', 
            'isOwner', 
            'homePlayers', 
            'awayPlayers',
            'referees'
        ));
    }

    /**
     * Update match results.
     */
    public function updateMatch(Request $request, Organization $organization, Competition $competition, CompetitionMatch $match)
    {
        // Use policy for authorization
        Gate::authorize('view', $organization);

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
            'home_captain_id' => 'nullable|exists:players,id',
            'away_captain_id' => 'nullable|exists:players,id',
            'referee_name' => 'nullable|string|max:255',
        ]);

        // Prepare update data
        $updateData = [
            'status' => $validated['status'],
            'home_captain_id' => $validated['home_captain_id'] ?? null,
            'away_captain_id' => $validated['away_captain_id'] ?? null,
            'referee_name' => $validated['referee_name'] ?? null,
        ];

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
     * Delete a match.
     */
    public function destroyMatch(Organization $organization, Competition $competition, CompetitionMatch $match)
    {
        Gate::authorize('update', $organization);

        if ($match->competition_id !== $competition->id) {
            abort(403);
        }

        $match->delete();

        return back()->with('success', 'Meč je uspješno obrisan.');
    }

    /**
     * Delete the competition.
     */
    public function destroy(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Allow bulk import anytime
        // if ($competition->status !== 'draft') {
        //     return redirect()
        //         ->route('organizations.competitions.show', [$organization, $competition])
        //         ->with('error', __('Can only manage players for draft competitions.'));
        // }

        return view('organizations.competitions.bulk-import', compact('organization', 'competition'));
    }

    /**
     * Process bulk import of players from text input.
     */
    public function bulkImportPlayers(Request $request, Organization $organization, Competition $competition)
    {
        // Use policy for authorization

        Gate::authorize('update', $organization);

        // Ensure competition belongs to organization
        if ($competition->organization_id !== $organization->id) {
            abort(404);
        }

        // Allow bulk import anytime
        // if ($competition->status !== 'draft') {
        //     return back()->with('error', 'Can only manage players for draft competitions.');
        // }

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
            
            // Split by comma to get name and club (club is optional)
            $parts = explode(',', $line, 2);
            
            $name = trim($parts[0]);
            $club = isset($parts[1]) ? trim($parts[1]) : null;
            
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
                    
                    // Update club/position if provided and different
                    if (!empty($playerClub) && $existingPlayer->position !== $playerClub) {
                        $existingPlayer->update(['position' => $playerClub]);
                    }
                    
                    // Add existing player to competition
                    $competition->players()->attach($existingPlayer->id);
                    $imported++;
                } else {
                    // Create new player with club (if provided)
                    $playerData = [
                        'organization_id' => $organization->id,
                        'name' => $playerName,
                        'is_active' => true,
                    ];
                    
                    // Only add position if club is provided
                    if (!empty($playerClub)) {
                        $playerData['position'] = $playerClub;
                    }
                    
                    $player = Player::create($playerData);
                    
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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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

        Gate::authorize('update', $organization);

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
}

