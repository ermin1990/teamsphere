<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\FriendlyMatchResource;
use App\Models\FriendlyMatch;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FriendlyMatchController extends Controller
{
    use ApiResponses;

    public function index(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $matches = FriendlyMatch::where('organization_id', $organization->id)
            ->orderByDesc('completed_at')
            ->orderByDesc('created_at')
            ->get();

        return $this->ok(FriendlyMatchResource::collection($matches));
    }

    public function show(Organization $organization, FriendlyMatch $friendlyMatch): JsonResponse
    {
        abort_unless($friendlyMatch->organization_id === $organization->id, 404);

        $this->authorize('view', $friendlyMatch->organization);

        return $this->ok(new FriendlyMatchResource($friendlyMatch));
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'home_player_id' => ['required', 'integer', Rule::exists('players', 'id')->where('organization_id', $organization->id)],
            'away_player_id' => ['required', 'integer', Rule::exists('players', 'id')->where('organization_id', $organization->id)],
            'home_player_name' => ['required', 'string', 'max:255'],
            'away_player_name' => ['required', 'string', 'max:255'],
            'sets' => ['required', 'array'],
            'set_durations' => ['required', 'array'],
            'winner_name' => ['required', 'string', 'max:255'],
            'completed_at' => ['required', 'date'],
        ]);

        $match = FriendlyMatch::create([
            'organization_id' => $organization->id,
            'home_player_id' => $validated['home_player_id'],
            'away_player_id' => $validated['away_player_id'],
            'home_player_name' => $validated['home_player_name'],
            'away_player_name' => $validated['away_player_name'],
            'sets' => $validated['sets'],
            'set_durations' => $validated['set_durations'],
            'winner_name' => $validated['winner_name'],
            'completed_at' => $validated['completed_at'],
        ]);

        return $this->created(new FriendlyMatchResource($match), 'Friendly match created successfully');
    }

    public function update(Request $request, Organization $organization, FriendlyMatch $friendlyMatch): JsonResponse
    {
        abort_unless($friendlyMatch->organization_id === $organization->id, 404);

        $this->authorize('update', $friendlyMatch->organization);

        $validated = $request->validate([
            'home_player_id' => ['nullable', 'integer', Rule::exists('players', 'id')->where('organization_id', $organization->id)],
            'away_player_id' => ['nullable', 'integer', Rule::exists('players', 'id')->where('organization_id', $organization->id)],
            'home_player_name' => ['sometimes', 'required', 'string', 'max:255'],
            'away_player_name' => ['sometimes', 'required', 'string', 'max:255'],
            'sets' => ['nullable', 'array'],
            'set_durations' => ['nullable', 'array'],
            'winner_name' => ['nullable', 'string', 'max:255'],
            'completed_at' => ['nullable', 'date'],
        ]);

        $friendlyMatch->update($validated);

        return $this->ok(new FriendlyMatchResource($friendlyMatch), 'Friendly match updated successfully');
    }

    public function destroy(Organization $organization, FriendlyMatch $friendlyMatch): JsonResponse
    {
        abort_unless($friendlyMatch->organization_id === $organization->id, 404);

        $this->authorize('update', $friendlyMatch->organization);

        $friendlyMatch->delete();

        return $this->ok(null, 'Friendly match deleted successfully');
    }
}
