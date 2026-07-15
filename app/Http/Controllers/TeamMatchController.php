<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Competition;
use App\Models\TeamMatch;
use App\Models\CompetitionMatch;
use App\Models\Team;
use App\Models\Player;
use App\Models\Standing;
use App\Models\Venue;
use Illuminate\Http\Request;

class TeamMatchController extends Controller
{
    public function store(Request $request, Organization $organization, Competition $competition)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id|different:home_team_id',
            'round' => 'required|integer|min:1',
            'scheduled_at' => 'nullable|date',
        ]);

        $competition->teamMatches()->create([
            'home_team_id' => $request->home_team_id,
            'away_team_id' => $request->away_team_id,
            'round' => $request->round,
            'scheduled_at' => $request->scheduled_at,
            'status' => 'scheduled',
        ]);

        // Ensure standings exist for these teams
        foreach ([$request->home_team_id, $request->away_team_id] as $teamId) {
            Standing::firstOrCreate([
                'competition_id' => $competition->id,
                'team_id' => $teamId,
            ], [
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'points' => 0,
            ]);
        }

        return back()->with('success', 'Meč uspješno dodan!');
    }

    public function show(Organization $organization, Competition $competition, TeamMatch $teamMatch)
    {
        $teamMatch->load(['homeTeam', 'awayTeam', 'individualMatches.homePlayer', 'individualMatches.awayPlayer', 'homeCaptain', 'awayCaptain']);
        
        $doublesPlayers = [
            'home_1' => null,
            'home_2' => null,
            'away_1' => null,
            'away_2' => null,
        ];

        if ($teamMatch->lineup) {
            $playerIds = [
                $teamMatch->lineup['home_dubl_1'] ?? null,
                $teamMatch->lineup['home_dubl_2'] ?? null,
                $teamMatch->lineup['away_dubl_1'] ?? null,
                $teamMatch->lineup['away_dubl_2'] ?? null,
            ];
            $players = Player::whereIn('id', array_filter($playerIds))->get()->keyBy('id');
            
            $doublesPlayers = [
                'home_1' => $players->get($teamMatch->lineup['home_dubl_1'] ?? null),
                'home_2' => $players->get($teamMatch->lineup['home_dubl_2'] ?? null),
                'away_1' => $players->get($teamMatch->lineup['away_dubl_1'] ?? null),
                'away_2' => $players->get($teamMatch->lineup['away_dubl_2'] ?? null),
            ];
        }

        $homePlayers = $teamMatch->homeTeam->players;
        $awayPlayers = $teamMatch->awayTeam->players;
        $venues = Venue::orderBy('name')->get();

        return view('organizations.competitions.team-matches.show', compact('organization', 'competition', 'teamMatch', 'doublesPlayers', 'homePlayers', 'awayPlayers', 'venues'));
    }

    public function protocol(Organization $organization, Competition $competition, TeamMatch $teamMatch)
    {
        $this->authorize('update', $organization);
        
        if ($teamMatch->status !== 'scheduled') {
            return redirect()->route('organizations.competitions.team-matches.show', [$organization, $competition, $teamMatch])
                ->with('error', 'Protokol je već popunjen.');
        }

        $homePlayers = $teamMatch->homeTeam->players;
        $awayPlayers = $teamMatch->awayTeam->players;

        return view('organizations.competitions.team-matches.protocol', compact('organization', 'competition', 'teamMatch', 'homePlayers', 'awayPlayers'));
    }

    public function storeProtocol(Request $request, Organization $organization, Competition $competition, TeamMatch $teamMatch)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'home_a' => 'required|exists:players,id',
            'home_b' => 'required|exists:players,id',
            'home_c' => 'required|exists:players,id',
            'away_x' => 'required|exists:players,id',
            'away_y' => 'required|exists:players,id',
            'away_z' => 'required|exists:players,id',
            'home_dubl_1' => 'required|exists:players,id',
            'home_dubl_2' => 'required|exists:players,id',
            'away_dubl_1' => 'required|exists:players,id',
            'away_dubl_2' => 'required|exists:players,id',
        ]);

        $lineup = $request->only(['home_a', 'home_b', 'home_c', 'away_x', 'away_y', 'away_z', 'home_dubl_1', 'home_dubl_2', 'away_dubl_1', 'away_dubl_2']);
        
        $teamMatch->update([
            'lineup' => $lineup,
            'status' => 'in_progress'
        ]);

        // Generate 7 individual matches based on BiH Corbillon system
        $matches = [
            ['home' => $lineup['home_a'], 'away' => $lineup['away_x'], 'code' => 'A-X', 'order' => 1, 'round' => 1],
            ['home' => $lineup['home_b'], 'away' => $lineup['away_y'], 'code' => 'B-Y', 'order' => 2, 'round' => 1],
            ['home' => $lineup['home_c'], 'away' => $lineup['away_z'], 'code' => 'C-Z', 'order' => 3, 'round' => 1],
            ['home' => null, 'away' => null, 'code' => 'Dubl', 'order' => 4, 'round' => 1, 'is_dubl' => true],
            ['home' => $lineup['home_a'], 'away' => $lineup['away_y'], 'code' => 'A-Y', 'order' => 5, 'round' => 2],
            ['home' => $lineup['home_c'], 'away' => $lineup['away_x'], 'code' => 'C-X', 'order' => 6, 'round' => 2],
            ['home' => $lineup['home_b'], 'away' => $lineup['away_z'], 'code' => 'B-Z', 'order' => 7, 'round' => 3],
        ];

        foreach ($matches as $m) {
            $competition->matches()->create([
                'team_match_id' => $teamMatch->id,
                'home_team_id' => $teamMatch->home_team_id,
                'away_team_id' => $teamMatch->away_team_id,
                'home_player_id' => $m['home'],
                'away_player_id' => $m['away'],
                'position_code' => $m['code'],
                'match_order' => $m['order'],
                'round_number' => $m['round'],
                'status' => 'scheduled',
                'sets_to_win' => $competition->sets_to_win ?? 3,
                'points_per_set' => $competition->points_per_set ?? 11,
            ]);
        }

        return redirect()->route('organizations.competitions.team-matches.show', [$organization, $competition, $teamMatch])
            ->with('success', 'Protokol sačuvan i mečevi generisani.');
    }

    public function initializeIndividualMatches(Organization $organization, Competition $competition, TeamMatch $teamMatch)
    {
        $this->authorize('update', $organization);

        $lineup = $teamMatch->lineup ?? [];
        
        $matchDefinitions = [
            ['home' => $lineup['home_a'] ?? null, 'away' => $lineup['away_x'] ?? null, 'code' => 'A-X', 'order' => 1, 'round' => 1],
            ['home' => $lineup['home_b'] ?? null, 'away' => $lineup['away_y'] ?? null, 'code' => 'B-Y', 'order' => 2, 'round' => 1],
            ['home' => $lineup['home_c'] ?? null, 'away' => $lineup['away_z'] ?? null, 'code' => 'C-Z', 'order' => 3, 'round' => 1],
            ['home' => null, 'away' => null, 'code' => 'Dubl', 'order' => 4, 'round' => 1],
            ['home' => $lineup['home_a'] ?? null, 'away' => $lineup['away_y'] ?? null, 'code' => 'A-Y', 'order' => 5, 'round' => 2],
            ['home' => $lineup['home_c'] ?? null, 'away' => $lineup['away_x'] ?? null, 'code' => 'C-X', 'order' => 6, 'round' => 2],
            ['home' => $lineup['home_b'] ?? null, 'away' => $lineup['away_z'] ?? null, 'code' => 'B-Z', 'order' => 7, 'round' => 3],
        ];

        $addedCount = 0;
        foreach ($matchDefinitions as $m) {
            // Check if match with this order already exists
            $exists = $teamMatch->individualMatches()->where('match_order', $m['order'])->exists();
            
            if (!$exists) {
                $competition->matches()->create([
                    'team_match_id' => $teamMatch->id,
                    'home_team_id' => $teamMatch->home_team_id,
                    'away_team_id' => $teamMatch->away_team_id,
                    'home_player_id' => $m['home'],
                    'away_player_id' => $m['away'],
                    'position_code' => $m['code'],
                    'match_order' => $m['order'],
                    'round_number' => $m['round'],
                    'status' => 'scheduled',
                    'sets_to_win' => $competition->sets_to_win ?? 3,
                    'points_per_set' => $competition->points_per_set ?? 11,
                ]);
                $addedCount++;
            }
        }

        if ($teamMatch->status === 'scheduled' && $addedCount > 0) {
            $teamMatch->update(['status' => 'in_progress']);
        }

        return back()->with('success', "Dodano $addedCount mečeva koji su nedostajali.");
    }

    public function addSingleMatch(Request $request, Organization $organization, Competition $competition, TeamMatch $teamMatch)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'match_order' => 'required|integer|min:1|max:20',
            'position_code' => 'required|string|max:10',
        ]);

        $competition->matches()->create([
            'team_match_id' => $teamMatch->id,
            'home_team_id' => $teamMatch->home_team_id,
            'away_team_id' => $teamMatch->away_team_id,
            'position_code' => $request->position_code,
            'match_order' => $request->match_order,
            'round_number' => ceil($request->match_order / 3),
            'status' => 'scheduled',
            'sets_to_win' => $competition->sets_to_win ?? 3,
            'points_per_set' => $competition->points_per_set ?? 11,
        ]);

        if ($teamMatch->status === 'scheduled') {
            $teamMatch->update(['status' => 'in_progress']);
        }

        return back()->with('success', 'Pojedinačni meč uspješno dodan.');
    }

    public function destroyIndividualMatch(Organization $organization, Competition $competition, TeamMatch $teamMatch, CompetitionMatch $match)
    {
        $this->authorize('update', $organization);

        if ($match->team_match_id !== $teamMatch->id) {
            abort(403);
        }

        $match->delete();

        return back()->with('success', 'Pojedinačni meč obrisan.');
    }

    public function updateIndividualPlayers(Request $request, Organization $organization, Competition $competition, TeamMatch $teamMatch, CompetitionMatch $match)
    {
        $this->authorize('update', $organization);

        if ($match->team_match_id !== $teamMatch->id) {
            abort(403);
        }

        $request->validate([
            'home_player_id' => 'nullable|exists:players,id',
            'away_player_id' => 'nullable|exists:players,id',
        ]);

        $match->update($request->only(['home_player_id', 'away_player_id']));

        return response()->json(['success' => true]);
    }

    public function updateLineup(Request $request, Organization $organization, Competition $competition, TeamMatch $teamMatch)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'field' => 'required|string',
            'player_id' => 'nullable|exists:players,id',
        ]);

        $lineup = $teamMatch->lineup ?? [];
        $lineup[$request->field] = $request->player_id;
        
        $teamMatch->update(['lineup' => $lineup]);

        return response()->json(['success' => true]);
    }

    public function updateCaptainsAndReferee(Request $request, Organization $organization, Competition $competition, TeamMatch $teamMatch)
    {
        $this->authorize('update', $organization);

        $request->validate([
            'home_captain_id' => 'nullable|exists:players,id',
            'away_captain_id' => 'nullable|exists:players,id',
            'referee_name' => 'nullable|string|max:255',
        ]);

        // Validate that captains belong to their respective teams
        if ($request->home_captain_id && !in_array($request->home_captain_id, $teamMatch->homeTeam->players->pluck('id')->toArray())) {
            return response()->json(['error' => 'Kapetan domaćina mora biti iz tima domaćina.'], 422);
        }

        if ($request->away_captain_id && !in_array($request->away_captain_id, $teamMatch->awayTeam->players->pluck('id')->toArray())) {
            return response()->json(['error' => 'Kapetan gosta mora biti iz tima gosta.'], 422);
        }

        $teamMatch->update($request->only(['home_captain_id', 'away_captain_id', 'referee_name']));

        return response()->json(['success' => true]);
    }

    public function destroy(Organization $organization, Competition $competition, TeamMatch $teamMatch)
    {
        $this->authorize('update', $organization);

        // Delete all individual matches first
        $teamMatch->individualMatches()->delete();
        
        $teamMatch->delete();

        return back()->with('success', 'Ekipni meč je uspješno obrisan.');
    }
}
