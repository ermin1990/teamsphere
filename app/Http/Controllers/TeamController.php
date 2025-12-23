<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Team;
use App\Models\Player;
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
            'description' => 'nullable|string|max:1000',
            'competition_id' => 'nullable|exists:competitions,id',
        ]);

        $organization->teams()->create($request->all());

        $url = route('organizations.teams.index', $organization);
        if ($request->has('competition_id')) {
            $url .= '?competition_id=' . $request->competition_id;
        }

        return redirect($url)->with('success', 'Tim uspješno kreiran.');
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

        return view('organizations.teams.roster', compact('organization', 'team', 'teamPlayers', 'availablePlayers'));
    }

    public function addPlayer(Request $request, Organization $organization, Team $team)
    {
        $this->authorize('update', $organization);
        
        $request->validate([
            'player_id' => 'required|exists:players,id',
        ]);

        $team->players()->attach($request->player_id);

        return redirect()->back()->with('success', 'Igrač dodan u tim.');
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
