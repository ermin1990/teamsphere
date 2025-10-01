<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Standing;
use App\Models\Team;
use App\Models\User;
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
        $participants = $league->is_team_based ? $league->teams : $league->players;
        $participantIds = $participants->pluck('id')->toArray();

        $format = $league->settings['format'] ?? 'round_robin';
        $matches = [];

        switch ($format) {
            case 'round_robin':
                $matches = $this->generateRoundRobinMatches($participantIds, false);
                break;
            case 'dual_robin':
                $matches = $this->generateRoundRobinMatches($participantIds, true);
                break;
            case 'dual_robin_knockout':
                // First round-robin, then knockout - for now just round-robin
                $matches = $this->generateRoundRobinMatches($participantIds, true);
                break;
            case 'knockout':
                // Generate knockout bracket
                $matches = $this->generateKnockoutMatches($participantIds);
                break;
        }

        // Create matches in database
        foreach ($matches as $round => $roundMatches) {
            foreach ($roundMatches as $match) {
                LeagueMatch::create([
                    'league_id' => $league->id,
                    'home_team_id' => $league->is_team_based ? $match['home'] : null,
                    'away_team_id' => $league->is_team_based ? $match['away'] : null,
                    'home_player_id' => !$league->is_team_based ? $match['home'] : null,
                    'away_player_id' => !$league->is_team_based ? $match['away'] : null,
                    'round' => $round + 1,
                    'status' => 'scheduled',
                ]);
            }
        }
    }

    /**
     * Generate round-robin matches.
     */
    private function generateRoundRobinMatches(array $participantIds, bool $doubleRound = false): array
    {
        $matches = [];
        $numParticipants = count($participantIds);

        // If odd number of participants, add a bye
        $hasBye = $numParticipants % 2 !== 0;
        if ($hasBye) {
            $participantIds[] = null; // Bye
            $numParticipants++;
        }

        $rounds = $numParticipants - 1;
        $matchesPerRound = $numParticipants / 2;

        for ($round = 0; $round < $rounds; $round++) {
            $roundMatches = [];

            for ($i = 0; $i < $matchesPerRound; $i++) {
                $home = $participantIds[$i];
                $away = $participantIds[$numParticipants - 1 - $i];

                // Skip bye matches
                if ($home !== null && $away !== null) {
                    $roundMatches[] = ['home' => $home, 'away' => $away];
                }
            }

            $matches[] = $roundMatches;

            // Rotate participants for next round (keep first fixed)
            $first = array_shift($participantIds);
            $participantIds[] = array_pop($participantIds);
            array_unshift($participantIds, $first);
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
    private function generateKnockoutMatches(array $participantIds): array
    {
        shuffle($participantIds); // Randomize bracket
        $matches = [];

        // First round
        $firstRoundMatches = [];
        for ($i = 0; $i < count($participantIds); $i += 2) {
            if (isset($participantIds[$i + 1])) {
                $firstRoundMatches[] = [
                    'home' => $participantIds[$i],
                    'away' => $participantIds[$i + 1]
                ];
            }
        }
        $matches[] = $firstRoundMatches;

        // For now, just return first round. Full knockout would need multiple rounds
        // based on number of participants

        return $matches;
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

        $validated = $request->validate([
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'sets' => 'nullable|array',
            'sets.*.home' => 'required|integer|min:0',
            'sets.*.away' => 'required|integer|min:0',
            'status' => 'required|in:scheduled,in_progress,completed,forfeited,cancelled',
            'forfeited_by' => 'required_if:status,forfeited|in:home,away',
            'played_at' => 'nullable|date',
        ]);

        // Reset scores and related fields when status changes to scheduled
        if ($validated['status'] === 'scheduled') {
            $match->update([
                'home_score' => 0,
                'away_score' => 0,
                'sets' => [],
                'status' => 'scheduled',
                'forfeited_by' => null,
                'played_at' => null,
            ]);
        } else {
            $match->update([
                'home_score' => $validated['home_score'] ?? 0,
                'away_score' => $validated['away_score'] ?? 0,
                'sets' => $validated['sets'] ?? [],
                'status' => $validated['status'],
                'forfeited_by' => $validated['forfeited_by'] ?? null,
                'played_at' => $validated['played_at'] ? now() : $match->played_at,
            ]);
        }

        // Update standings if match is completed or forfeited
        if (in_array($validated['status'], ['completed', 'forfeited'])) {
            $this->updateStandings($league);
        }

        return redirect()->route('organizations.leagues.matches.show', [$organization, $league, $match])
            ->with('success', 'Match results updated successfully.');
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
            'home_score' => 'required_unless:action,forfeit_match|integer|min:0',
            'away_score' => 'required_unless:action,forfeit_match|integer|min:0',
            'sets' => 'nullable|array',
            'sets.*.home' => 'required|integer|min:0',
            'sets.*.away' => 'required|integer|min:0',
            'forfeited_by' => 'required_if:action,forfeit_match|in:home,away',
            'action' => 'required|in:update_score,complete_match,pause_match,forfeit_match',
        ]);

        if ($validated['action'] === 'complete_match') {
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
}
