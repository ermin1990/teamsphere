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

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sport_id' => ['required', 'exists:sports,id'],
            'type' => ['required', 'in:league,tournament'],
            'start_date' => ['required', 'date', 'after:today'],
            'end_date' => ['nullable', 'date', 'after:start_date'],
            'max_teams' => ['nullable', 'integer', 'min:2', 'max:100'],
            'is_team_based' => ['boolean'],
            // Tournament specific validation
            'max_participants' => ['required_if:type,tournament', 'integer', 'min:4', 'max:128'],
            'group_count' => ['required_if:type,tournament', 'integer', 'min:2', 'max:16'],
            'players_per_group' => ['required_if:type,tournament', 'integer', 'min:3', 'max:8'],
            'players_advancing_per_group' => ['required_if:type,tournament', 'integer', 'min:1', 'max:4'],
            'advancement_method' => ['required_if:type,tournament', 'in:automatic,manual'],
        ]);

        $competition = Competition::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name . '-' . time()),
            'description' => $request->description,
            'organization_id' => $organization->id,
            'sport_id' => $request->sport_id,
            'type' => $request->type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'max_teams' => $request->max_teams,
            'is_team_based' => $request->boolean('is_team_based'),
            'status' => 'draft',
            'is_active' => true,
            // Tournament fields
            'max_participants' => $request->max_participants,
            'group_count' => $request->group_count,
            'players_per_group' => $request->players_per_group,
            'players_advancing_per_group' => $request->players_advancing_per_group,
            'advancement_method' => $request->advancement_method,
            'current_phase' => 'groups',
        ]);

        return redirect()
            ->route('organizations.competitions.show', [$organization, $competition])
            ->with('success', __('Competition created successfully!'));
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

        return back()->with('success', 'Tournament groups generated successfully!');
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
}
