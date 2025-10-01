<?php

namespace App\Http\Controllers;

use App\Models\League;
use App\Models\Organization;
use App\Models\Sport;
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

        if ($request->has(['points_win', 'points_draw', 'points_loss']) && $league->is_team_based && $league->sport->slug !== 'stoni-tenis') {
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

        // Start the league
        $league->update(['status' => 'active']);

        return redirect()->route('organizations.leagues.show', [$organization, $league])
                       ->with('success', __('League started successfully!'));
    }
}
