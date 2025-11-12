<?php

namespace App\Http\Controllers;

use App\Models\FutsalTeam;
use App\Models\FutsalTeamPlayer;
use App\Models\Competition;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FutsalTeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization, Competition $competition)
    {
        $this->authorize('view', $organization);

        // Get all futsal teams for this competition
        $teams = $competition->futsalTeams()
            ->with(['activePlayers', 'standings'])
            ->orderBy('name')
            ->get();

        return view('organizations.competitions.futsal.teams.index', compact('organization', 'competition', 'teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization, Competition $competition)
    {
        $this->authorize('update', $organization);

        return view('organizations.competitions.futsal.teams.create', compact('organization', 'competition'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Organization $organization, Competition $competition)
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'captain_name' => 'nullable|string|max:255',
            'coach_name' => 'nullable|string|max:255',
            'home_venue' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
        ]);

        $validated['organization_id'] = $organization->id;
        $validated['competition_id'] = $competition->id;
        $validated['is_active'] = true;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('futsal-logos', 'public');
            $validated['logo_url'] = Storage::url($path);
        }

        $team = FutsalTeam::create($validated);

        return redirect()
            ->route('organizations.competitions.futsal.teams.show', [$organization, $competition, $team])
            ->with('success', 'Tim je uspješno kreiran!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, Competition $competition, FutsalTeam $team)
    {
        $this->authorize('view', $organization);

        // Load team with all relationships
        $team->load([
            'activePlayers' => function ($query) {
                $query->orderBy('jersey_number');
            },
            'matches' => function ($query) {
                $query->orderBy('scheduled_at', 'desc')->limit(10);
            },
            'standings',
        ]);

        // Get player statistics for this competition
        $playerStats = $team->players()
            ->with(['stats' => function ($query) use ($competition) {
                $query->where('competition_id', $competition->id);
            }])
            ->get();

        return view('organizations.competitions.futsal.teams.show', compact('organization', 'competition', 'team', 'playerStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization, Competition $competition, FutsalTeam $team)
    {
        $this->authorize('update', $organization);

        return view('organizations.competitions.futsal.teams.edit', compact('organization', 'competition', 'team'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Organization $organization, Competition $competition, FutsalTeam $team)
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'captain_name' => 'nullable|string|max:255',
            'coach_name' => 'nullable|string|max:255',
            'home_venue' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($team->logo_url) {
                $oldPath = str_replace('/storage/', '', $team->logo_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('logo')->store('futsal-logos', 'public');
            $validated['logo_url'] = Storage::url($path);
        }

        $team->update($validated);

        return redirect()
            ->route('organizations.competitions.futsal.teams.show', [$organization, $competition, $team])
            ->with('success', 'Tim je uspješno ažuriran!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, Competition $competition, FutsalTeam $team)
    {
        $this->authorize('update', $organization);

        // Delete logo if exists
        if ($team->logo_url) {
            $oldPath = str_replace('/storage/', '', $team->logo_url);
            Storage::disk('public')->delete($oldPath);
        }

        $team->delete();

        return redirect()
            ->route('organizations.competitions.futsal.teams.index', [$organization, $competition])
            ->with('success', 'Tim je uspješno obrisan!');
    }

    /**
     * Add a player to the team roster.
     */
    public function addPlayer(Request $request, Organization $organization, Competition $competition, FutsalTeam $team)
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'player_name' => 'required|string|max:255',
            'jersey_number' => 'required|integer|min:1|max:99',
            'position' => 'required|in:goalkeeper,defender,midfielder,forward',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'is_captain' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if jersey number is already taken
        $existingPlayer = $team->players()
            ->where('jersey_number', $validated['jersey_number'])
            ->where('is_active', true)
            ->first();

        if ($existingPlayer) {
            return back()->withErrors(['jersey_number' => 'Broj dresa ' . $validated['jersey_number'] . ' je već zauzet.']);
        }

        $validated['futsal_team_id'] = $team->id;
        $validated['is_active'] = true;

        // If this player is set as captain, remove captain from others
        if ($request->boolean('is_captain')) {
            $team->players()->update(['is_captain' => false]);
        }

        FutsalTeamPlayer::create($validated);

        return back()->with('success', 'Igrač je uspješno dodat u tim!');
    }

    /**
     * Update a player in the team roster.
     */
    public function updatePlayer(Request $request, Organization $organization, Competition $competition, FutsalTeam $team, FutsalTeamPlayer $player)
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'player_name' => 'required|string|max:255',
            'jersey_number' => 'required|integer|min:1|max:99',
            'position' => 'required|in:goalkeeper,defender,midfielder,forward',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'is_captain' => 'boolean',
            'is_active' => 'boolean',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check if jersey number is already taken by another player
        $existingPlayer = $team->players()
            ->where('jersey_number', $validated['jersey_number'])
            ->where('id', '!=', $player->id)
            ->where('is_active', true)
            ->first();

        if ($existingPlayer) {
            return back()->withErrors(['jersey_number' => 'Broj dresa ' . $validated['jersey_number'] . ' je već zauzet.']);
        }

        // If this player is set as captain, remove captain from others
        if ($request->boolean('is_captain') && !$player->is_captain) {
            $team->players()->where('id', '!=', $player->id)->update(['is_captain' => false]);
        }

        $player->update($validated);

        return back()->with('success', 'Igrač je uspješno ažuriran!');
    }

    /**
     * Remove a player from the team roster.
     */
    public function removePlayer(Organization $organization, Competition $competition, FutsalTeam $team, FutsalTeamPlayer $player)
    {
        $this->authorize('update', $organization);

        $player->update(['is_active' => false]);

        return back()->with('success', 'Igrač je uklonjen iz tima!');
    }

    /**
     * Permanently delete a player from the team roster.
     */
    public function deletePlayer(Organization $organization, Competition $competition, FutsalTeam $team, FutsalTeamPlayer $player)
    {
        $this->authorize('update', $organization);

        $player->delete();

        return back()->with('success', 'Igrač je trajno obrisan!');
    }
}
