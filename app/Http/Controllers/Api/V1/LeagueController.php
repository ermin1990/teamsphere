<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\LeagueResource;
use App\Models\League;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeagueController extends Controller
{
    use ApiResponses;

    public function index(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $leagues = League::where('organization_id', $organization->id)
            ->orderByDesc('created_at')
            ->paginate($this->perPage($request));

        return $this->paginated($leagues, LeagueResource::class);
    }

    public function show(League $league): JsonResponse
    {
        $this->authorize('view', $league->organization);

        return $this->ok(new LeagueResource($league));
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        if (!$organization->canCreateMoreLeagues()) {
            return $this->fail('You have reached the maximum number of leagues for this organization.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sport_id' => ['required', 'exists:sports,id'],
            'is_team_based' => ['required', 'boolean'],
            'status' => ['sometimes', 'string', 'in:draft,active,completed,cancelled'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'max_teams' => ['nullable', 'integer', 'min:2'],
            'settings' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        $league = League::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(6),
            'description' => $validated['description'] ?? null,
            'organization_id' => $organization->id,
            'sport_id' => $validated['sport_id'],
            'type' => 'league',
            'is_team_based' => $validated['is_team_based'],
            'status' => $validated['status'] ?? 'draft',
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'max_teams' => $validated['max_teams'] ?? 8,
            'settings' => $validated['settings'] ?? [],
            'is_active' => $validated['is_active'] ?? true,
            'is_public' => $validated['is_public'] ?? false,
        ]);

        League::clearLeagueCache();

        return $this->created(new LeagueResource($league), 'League created successfully');
    }

    public function update(Request $request, League $league): JsonResponse
    {
        $organization = $league->organization;

        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sport_id' => ['sometimes', 'required', 'exists:sports,id'],
            'is_team_based' => ['sometimes', 'required', 'boolean'],
            'status' => ['sometimes', 'required', 'string', 'in:draft,active,completed,cancelled'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'max_teams' => ['nullable', 'integer', 'min:2'],
            'settings' => ['nullable', 'array'],
            'is_active' => ['sometimes', 'boolean'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        $league->update($validated);

        League::clearLeagueCache();

        return $this->ok(new LeagueResource($league), 'League updated successfully');
    }

    public function destroy(League $league): JsonResponse
    {
        $organization = $league->organization;

        $this->authorize('update', $organization);

        $league->delete();

        League::clearLeagueCache();

        return $this->ok(null, 'League deleted successfully');
    }
}
