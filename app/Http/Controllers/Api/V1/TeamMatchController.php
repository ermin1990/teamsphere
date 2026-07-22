<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TeamMatchResource;
use App\Models\Competition;
use App\Models\TeamMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamMatchController extends Controller
{
    use ApiResponses;

    public function index(Request $request, Competition $competition): JsonResponse
    {
        $this->authorize('view', $competition->organization);

        $teamMatches = $competition->teamMatches()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('round')
            ->orderBy('scheduled_at')
            ->paginate($this->perPage($request));

        return $this->paginated($teamMatches, TeamMatchResource::class);
    }

    public function show(Competition $competition, TeamMatch $teamMatch): JsonResponse
    {
        $this->authorize('view', $competition->organization);

        if ($teamMatch->competition_id !== $competition->id) {
            abort(404);
        }

        $teamMatch->load(['homeTeam', 'awayTeam']);

        return $this->ok(new TeamMatchResource($teamMatch));
    }

    public function update(Request $request, Competition $competition, TeamMatch $teamMatch): JsonResponse
    {
        $this->authorize('update', $competition->organization);

        if ($teamMatch->competition_id !== $competition->id) {
            abort(404);
        }

        $validated = $request->validate([
            'status' => ['sometimes', 'in:scheduled,in_progress,completed,forfeited,cancelled'],
            'scheduled_at' => ['nullable', 'date'],
            'played_at' => ['nullable', 'date'],
            'round' => ['sometimes', 'integer', 'min:1'],
            'lineup' => ['nullable', 'array'],
            'home_captain_id' => ['nullable', 'exists:players,id'],
            'away_captain_id' => ['nullable', 'exists:players,id'],
            'referee_name' => ['nullable', 'string', 'max:255'],
        ]);

        if (!empty($validated['home_captain_id']) && !$teamMatch->homeTeam->players()->where('players.id', $validated['home_captain_id'])->exists()) {
            return $this->fail('Selected home captain does not play for the home team.', 422);
        }

        if (!empty($validated['away_captain_id']) && !$teamMatch->awayTeam->players()->where('players.id', $validated['away_captain_id'])->exists()) {
            return $this->fail('Selected away captain does not play for the away team.', 422);
        }

        if (!empty($validated['round']) && $validated['round'] !== $teamMatch->round) {
            $collides = $competition->teamMatches()
                ->where('id', '!=', $teamMatch->id)
                ->where('round', $validated['round'])
                ->where(function ($query) use ($teamMatch) {
                    $query->whereIn('home_team_id', [$teamMatch->home_team_id, $teamMatch->away_team_id])
                          ->orWhereIn('away_team_id', [$teamMatch->home_team_id, $teamMatch->away_team_id]);
                })
                ->exists();

            if ($collides) {
                return $this->fail('One of the teams already has a match scheduled in this round.', 422);
            }
        }

        $teamMatch->update($validated);
        $teamMatch->checkCompletion();

        return $this->ok(new TeamMatchResource($teamMatch->fresh(['homeTeam', 'awayTeam'])), 'Team match updated successfully');
    }
}
