<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TeamCoachResource;
use App\Models\Team;
use App\Models\TeamCoach;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TeamCoachController extends Controller
{
    use ApiResponses;

    public function index(Team $team): JsonResponse
    {
        $this->authorize('view', $team->organization);

        $coaches = $team->coaches()
            ->orderByDesc('is_active')
            ->orderByDesc('created_at')
            ->get();

        return $this->ok(TeamCoachResource::collection($coaches));
    }

    public function show(Team $team, TeamCoach $coach): JsonResponse
    {
        $this->authorize('view', $team->organization);

        if ($coach->team_id !== $team->id) {
            abort(404);
        }

        return $this->ok(new TeamCoachResource($coach));
    }

    public function store(Request $request, Team $team): JsonResponse
    {
        $this->authorize('update', $team->organization);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        if ($validated['is_active'] ?? false) {
            $team->coaches()->update(['is_active' => false]);
        }

        $coach = $team->coaches()->create([
            'name' => $validated['name'],
            'is_active' => $validated['is_active'] ?? false,
            'start_date' => $validated['start_date'] ?? now(),
            'end_date' => $validated['end_date'] ?? null,
        ]);

        return $this->created(new TeamCoachResource($coach), 'Coach added successfully');
    }

    public function update(Request $request, Team $team, TeamCoach $coach): JsonResponse
    {
        $this->authorize('update', $team->organization);

        if ($coach->team_id !== $team->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        if (($validated['is_active'] ?? false) === true) {
            $team->coaches()->where('id', '!=', $coach->id)->update(['is_active' => false]);
        }

        $coach->update($validated);

        return $this->ok(new TeamCoachResource($coach), 'Coach updated successfully');
    }

    public function destroy(Team $team, TeamCoach $coach): JsonResponse
    {
        $this->authorize('update', $team->organization);

        if ($coach->team_id !== $team->id) {
            abort(404);
        }

        $coach->delete();

        return $this->ok(null, 'Coach removed successfully');
    }
}
