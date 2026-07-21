<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TeamPlayerResource;
use App\Http\Resources\Api\V1\TeamResource;
use App\Models\Organization;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    use ApiResponses;

    public function index(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $teams = $organization->teams()
            ->withCount('players')
            ->when($request->filled('competition_id'), function ($query) use ($request) {
                $query->where('competition_id', $request->competition_id);
            })
            ->orderByDesc('created_at')
            ->get();

        return $this->ok(TeamResource::collection($teams));
    }

    public function show(Team $team): JsonResponse
    {
        $this->authorize('view', $team->organization);

        $team->load(['players', 'coaches']);

        return $this->ok(new TeamResource($team));
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'competition_id' => ['nullable', 'exists:competitions,id'],
            'captain_id' => ['nullable', 'exists:users,id'],
            'coach' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ]);

        $team = $organization->teams()->create($validated);

        return $this->created(new TeamResource($team), 'Team created successfully');
    }

    public function update(Request $request, Team $team): JsonResponse
    {
        $this->authorize('update', $team->organization);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'competition_id' => ['nullable', 'exists:competitions,id'],
            'captain_id' => ['nullable', 'exists:users,id'],
            'coach' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
        ]);

        $team->update($validated);

        return $this->ok(new TeamResource($team), 'Team updated successfully');
    }

    public function destroy(Team $team): JsonResponse
    {
        $this->authorize('update', $team->organization);

        $team->delete();

        return $this->ok(null, 'Team deleted successfully');
    }

    /**
     * Attach a player to the team's roster.
     */
    public function storePlayer(Request $request, Team $team): JsonResponse
    {
        $this->authorize('update', $team->organization);

        $validated = $request->validate([
            'player_id' => ['required', 'exists:players,id'],
            'role' => ['nullable', 'string', 'max:255'],
        ]);

        $team->players()->syncWithoutDetaching([
            $validated['player_id'] => ['role' => $validated['role'] ?? null],
        ]);

        $player = $team->players()->where('players.id', $validated['player_id'])->first();

        return $this->created(new TeamPlayerResource($player), 'Player added to team successfully');
    }

    /**
     * Detach a player from the team's roster.
     */
    public function destroyPlayer(Team $team, Player $player): JsonResponse
    {
        $this->authorize('update', $team->organization);

        $team->players()->detach($player->id);

        return $this->ok(null, 'Player removed from team successfully');
    }
}
