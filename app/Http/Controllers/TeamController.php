<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Team;
use App\Models\Player;
use App\Models\TeamCoach;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    public function index(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);
        $teams = $organization->teams()->withCount('players')->get();
        
        $competition = null;
        if ($request->has('competition_id')) {
            $competition = \App\Models\Competition::where('organization_id', $organization->id)
                ->find($request->competition_id);
        }

        // If no competition_id in request, try to find the latest draft competition
        if (!$competition) {
            $competition = $organization->competitions()
                ->where('status', 'draft')
                ->latest()
                ->first();
        }

        return view('organizations.teams.index', compact('organization', 'teams', 'competition'));
    }

    public function create(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);
        $competitionId = $request->query('competition_id');
        return view('organizations.teams.create', compact('organization', 'competitionId'));
    }

    public function store(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'coach' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'competition_id' => 'nullable|exists:competitions,id',
        ]);

        $team = $organization->teams()->create($request->all());

        // Land straight on the roster instead of the team list - the
        // organizer's very next action is always adding the players/pair to
        // the team they just created.
        return redirect()->route('organizations.teams.roster', [$organization, $team])
            ->with('success', 'Tim uspješno kreiran. Sada dodajte igrače.');
    }

    public function edit(Organization $organization, Team $team)
    {
        $this->authorize('update', $organization);
        return view('organizations.teams.edit', compact('organization', 'team'));
    }

    public function update(Request $request, Organization $organization, Team $team)
    {
        $this->authorize('update', $organization);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'coach' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $team->update($request->all());

        return redirect()->route('organizations.teams.index', $organization)
            ->with('success', 'Tim uspješno ažuriran.');
    }

    public function destroy(Organization $organization, Team $team)
    {
        $this->authorize('update', $organization);
        $team->delete();
        return redirect()->route('organizations.teams.index', $organization)
            ->with('success', 'Tim uspješno obrisan.');
    }

    public function roster(Organization $organization, Team $team)
    {
        $this->authorize('update', $organization);
        
        $teamPlayers = $team->players;
        $availablePlayers = $organization->players()
            ->whereNotIn('id', $teamPlayers->pluck('id'))
            ->orderBy('name')
            ->get();

        $coaches = collect();
        try {
            $coaches = $team->coaches()->orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            // Table might not exist on production yet
        }

        // Fallback: if no coaches found in table, but team has a coach string
        if ($coaches->isEmpty() && $team->coach) {
            $coaches->push((object)[
                'id' => 0,
                'name' => $team->coach,
                'is_active' => true,
                'start_date' => null,
                'end_date' => null,
                'team_id' => $team->id
            ]);
        }

        $recentMatches = $team->matches()
            ->with(['homeTeam', 'awayTeam', 'competition'])
            ->orderBy('scheduled_at', 'desc')
            ->take(5)
            ->get();

        return view('organizations.teams.roster', compact('organization', 'team', 'teamPlayers', 'availablePlayers', 'recentMatches', 'coaches'));
    }

    public function bulkAddPlayers(Request $request, Organization $organization, Team $team)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'player_ids' => 'nullable|array',
            'player_ids.*' => 'exists:players,id',
            'names_list' => 'nullable|string',
        ]);

        $addedCount = 0;
        $playerIds = [];

        // Handle existing players
        if ($request->has('player_ids')) {
            $playerIds = array_merge($playerIds, $request->player_ids);
            $addedCount += count($request->player_ids);
        }

        // Handle new players from list - reuse an existing organization player
        // with the same name instead of always creating a new row, otherwise
        // typing a name that was already added elsewhere (e.g. via "Dodaj
        // Igrače" on the competition) creates a duplicate Player record.
        if ($request->filled('names_list')) {
            $names = preg_split('/[\n,]+/', $request->names_list);
            foreach ($names as $name) {
                $name = trim($name);
                if (empty($name)) continue;

                $player = $organization->players()
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                    ->first();

                if (!$player) {
                    $player = $organization->players()->create([
                        'name' => $name,
                        'type' => 'single',
                    ]);
                }

                $playerIds[] = $player->id;
                $addedCount++;
            }
        }

        if (!empty($playerIds)) {
            $team->players()->syncWithoutDetaching($playerIds);
            $this->registerPlayersInTeamCompetition($team, $playerIds);
        }

        return redirect()->back()->with('success', $addedCount . ' igrača dodano u tim.');
    }

    public function addPlayer(Request $request, Organization $organization, Team $team)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'player_id' => 'required|exists:players,id',
        ]);

        $team->players()->syncWithoutDetaching([$request->player_id]);
        $this->registerPlayersInTeamCompetition($team, [$request->player_id]);

        return redirect()->back()->with('success', 'Igrač dodan u tim.');
    }

    /**
     * When a team is linked to a competition (the normal case for a
     * team-based/doubles league like Padel), adding a player to the team's
     * roster should be enough to register them for that competition too -
     * without this, organizers had to separately add every player via
     * "Dodaj Igrače" before creating teams, which is both an extra step and
     * the source of the duplicate-player bug this method also fixes.
     */
    private function registerPlayersInTeamCompetition(Team $team, array $playerIds): void
    {
        if (!$team->competition_id) {
            return;
        }

        $competition = $team->league;
        if (!$competition) {
            return;
        }

        $alreadyRegistered = $competition->players()->pluck('players.id')->all();
        $toRegister = array_diff($playerIds, $alreadyRegistered);

        if (!empty($toRegister)) {
            $competition->players()->attach($toRegister);
        }
    }

    public function addCoach(Request $request, Organization $organization, Team $team)
    {
        $this->authorize('update', $organization);
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            // Deactivate other coaches if this one is active (default)
            $team->coaches()->update(['is_active' => false]);

            $team->coaches()->create([
                'name' => $request->name,
                'is_active' => true,
                'start_date' => now(),
            ]);
        } catch (\Exception $e) {
            // Fallback: update the simple coach field on the team table
            $team->update(['coach' => $request->name]);
        }

        return redirect()->back()->with('success', 'Trener uspješno dodan.');
    }

    public function toggleCoachStatus(Organization $organization, Team $team, TeamCoach $coach)
    {
        $this->authorize('update', $organization);
        
        if ($coach->team_id !== $team->id) abort(403);

        try {
            if (!$coach->is_active) {
                // Deactivate others
                $team->coaches()->where('id', '!=', $coach->id)->update(['is_active' => false]);
                $coach->update(['is_active' => true, 'end_date' => null]);
            } else {
                $coach->update(['is_active' => false, 'end_date' => now()]);
            }
        } catch (\Exception $e) {
            // Table might not exist
            return redirect()->back()->with('error', 'Greška pri ažuriranju statusa trenera.');
        }

        return redirect()->back()->with('success', 'Status trenera ažuriran.');
    }

    public function removeCoach(Organization $organization, Team $team, TeamCoach $coach)
    {
        $this->authorize('update', $organization);
        
        if ($coach->team_id !== $team->id) abort(403);

        try {
            $coach->delete();
        } catch (\Exception $e) {
            // Table might not exist
            return redirect()->back()->with('error', 'Greška pri brisanju trenera.');
        }

        return redirect()->back()->with('success', 'Trener uklonjen.');
    }

    public function removePlayer(Organization $organization, Team $team, Player $player)
    {
        $this->authorize('update', $organization);
        $team->players()->detach($player->id);
        return redirect()->back()->with('success', 'Igrač uklonjen iz tima.');
    }

    public function suggestTeams(Request $request, Organization $organization)
    {
        $this->authorize('update', $organization);
        
        $competitionId = $request->input('competition_id');

        // Get all players in the organization that have a 'position' (club name)
        $playersWithClubs = $organization->players()
            ->whereNotNull('position')
            ->where('position', '!=', '')
            ->get()
            ->groupBy('position');

        $createdCount = 0;
        $assignedCount = 0;

        foreach ($playersWithClubs as $clubName => $players) {
            // Check if team already exists in this organization
            // If we have a competition_id, we prefer a team already linked to it or a free team
            $team = $organization->teams()
                ->where('name', $clubName)
                ->when($competitionId, function($q) use ($competitionId) {
                    return $q->where(function($sq) use ($competitionId) {
                        $sq->where('competition_id', $competitionId)
                           ->orWhereNull('competition_id');
                    });
                })
                ->first();
            
            if (!$team) {
                $team = $organization->teams()->create([
                    'name' => $clubName,
                    'description' => "Automatski kreiran tim iz uvoza igrača.",
                    'competition_id' => $competitionId,
                ]);
                $createdCount++;
            } elseif ($competitionId && !$team->competition_id) {
                $team->update(['competition_id' => $competitionId]);
            }

            foreach ($players as $player) {
                // Check if player is already in this team
                if (!$team->players()->where('player_id', $player->id)->exists()) {
                    $team->players()->attach($player->id);
                    $assignedCount++;
                }
            }
        }

        $redirect = redirect()->back();
        if ($request->has('competition_id')) {
            $redirect = redirect()->route('organizations.teams.index', [
                'organization' => $organization, 
                'competition_id' => $request->competition_id
            ]);
        }

        if ($createdCount === 0 && $assignedCount === 0) {
            return $redirect->with('info', 'Nisu pronađeni novi timovi ili igrači za dodavanje.');
        }

        return $redirect->with('success', "Automatski kreirano {$createdCount} timova i dodijeljeno {$assignedCount} igrača.");
    }
}
