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
            'advancement_method' => $request->advancement_method ?: 'automatic',
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
            'tournamentGroups'
        ]);

        $organization->load('players');

        return view('organizations.competitions.show', compact('organization', 'competition', 'isOwner', 'isPlayer', 'isReferee'));
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
        foreach ($request->groups as $index => $groupData) {
            $group = TournamentGroup::create([
                'competition_id' => $competition->id,
                'name' => chr(65 + $index),
                'group_number' => $index + 1,
                'player_ids' => $groupData['players'],
                'standings' => $competition->initializeGroupStandings($groupData['players']),
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

        if ($competition->status !== 'draft') {
            return back()->with('error', 'Cannot change settings after competition has started.');
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
            'current_phase' => null,
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
        
        // Ensure user owns the organization
        if ($competition->organization->user_id !== auth()->id()) {
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
            $this->updateGroupStandings($match);
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
        $homeStanding = Standing::firstOrCreate([
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

        $awayStanding = Standing::firstOrCreate([
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
        
        // Check if all matches in current round are completed
        $roundMatches = CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->where('round_number', $currentRound)
            ->get();
        
        $allCompleted = $roundMatches->every(function($match) {
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
        
        // Get winners from current round
        $winners = [];
        foreach ($roundMatches as $match) {
            if ($match->home_score > $match->away_score) {
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
            CompetitionMatch::create([
                'competition_id' => $competition->id,
                'home_player_id' => $winners[$i],
                'away_player_id' => $winners[$i + 1] ?? null,
                'phase' => 'knockout',
                'round_number' => $currentRound + 1,
                'status' => 'scheduled',
                'scheduled_at' => now(),
            ]);
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

        // Delete existing knockout matches
        CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->delete();

        // Generate new bracket
        $this->generateKnockoutBracketFromPlayers($competition, $advancingPlayers);

        return response()->json(['success' => true]);
    }

    /**
     * Generate knockout bracket from player IDs.
     */
    private function generateKnockoutBracketFromPlayers($competition, array $playerIds)
    {
        // Handle odd number of players by adding byes
        $totalPlayers = count($playerIds);
        $bracketSize = $this->getNextPowerOfTwo($totalPlayers);

        // Add byes if necessary
        $byesNeeded = $bracketSize - $totalPlayers;
        for ($i = 0; $i < $byesNeeded; $i++) {
            $playerIds[] = null; // null represents a bye
        }

        // Seed the bracket: arrange players so top seeds don't meet early
        // Assuming playerIds is [top1, top2, top3, top4, second1, second2, second3, second4]
        // Seeds: 1-4 tops, 5-8 seconds
        // Bracket order: 1 vs 8, 4 vs 5, 2 vs 7, 3 vs 6
        $seededOrder = [0, 7, 3, 4, 1, 6, 2, 5]; // indices for seeds 1,8,4,5,2,7,3,6
        $seededPlayers = [];
        foreach ($seededOrder as $index) {
            if (isset($playerIds[$index])) {
                $seededPlayers[] = $playerIds[$index];
            }
        }
        $playerIds = $seededPlayers;

        // Create first round matches
        $matchesCreated = 0;
        for ($i = 0; $i < count($playerIds); $i += 2) {
            CompetitionMatch::create([
                'competition_id' => $competition->id,
                'home_player_id' => $playerIds[$i],
                'away_player_id' => $playerIds[$i + 1] ?? null,
                'phase' => 'knockout',
                'round_number' => 1,
                'status' => 'scheduled',
                'scheduled_at' => now(),
                'is_bye' => $playerIds[$i + 1] === null,
            ]);
            $matchesCreated++;
        }

        return $matchesCreated;
    }

    /**
     * Get the next power of 2 for bracket size.
     */
    private function getNextPowerOfTwo(int $number): int
    {
        if ($number <= 1) return 1;
        $power = 1;
        while ($power < $number) {
            $power *= 2;
        }
        return $power;
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

        // Get all players from groups
        $players = [];
        foreach ($competition->tournamentGroups as $group) {
            $groupPlayers = collect($group->standings)->map(function ($standing) {
                return [
                    'id' => $standing['player_id'],
                    'name' => Player::find($standing['player_id'])->name ?? 'Unknown'
                ];
            });
            $players = array_merge($players, $groupPlayers->toArray());
        }

        return response()->json(['players' => array_unique($players, SORT_REGULAR)]);
    }
}
