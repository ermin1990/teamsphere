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
            'home_score' => ['nullable', 'integer', 'min:0'],
            'away_score' => ['nullable', 'integer', 'min:0'],
            'status' => ['sometimes', 'string'],
            'scheduled_at' => ['nullable', 'date'],
            'played_at' => ['nullable', 'date'],
            'round' => ['sometimes', 'integer', 'min:1'],
            'lineup' => ['nullable', 'array'],
            'home_captain_id' => ['nullable', 'exists:players,id'],
            'away_captain_id' => ['nullable', 'exists:players,id'],
            'referee_name' => ['nullable', 'string', 'max:255'],
        ]);

        $teamMatch->update($validated);
        $teamMatch->checkCompletion();

        return $this->ok(new TeamMatchResource($teamMatch->fresh(['homeTeam', 'awayTeam'])), 'Team match updated successfully');
    }
}
