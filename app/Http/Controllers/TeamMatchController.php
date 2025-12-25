<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Competition;
use App\Models\TeamMatch;
use App\Models\CompetitionMatch;
use App\Models\Team;
use App\Models\Player;
use App\Models\Standing;
use Illuminate\Http\Request;

class TeamMatchController extends Controller
{
    public function show(Organization $organization, Competition $competition, TeamMatch $teamMatch)
    {
        $teamMatch->load(['homeTeam', 'awayTeam', 'individualMatches.homePlayer', 'individualMatches.awayPlayer']);
        
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

        return view('organizations.competitions.team-matches.show', compact('organization', 'competition', 'teamMatch', 'doublesPlayers'));
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
}
