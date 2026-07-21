<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\SeasonResource;
use App\Models\Organization;
use App\Models\Season;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    use ApiResponses;

    public function index(Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        $seasons = $organization->seasons()->orderByDesc('starts_at')->get();

        return $this->ok(SeasonResource::collection($seasons));
    }

    public function show(Organization $organization, Season $season): JsonResponse
    {
        $this->authorize('view', $organization);

        return $this->ok(new SeasonResource($season));
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $season = $organization->seasons()->create([
            'name' => $validated['name'],
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return $this->created(new SeasonResource($season), 'Season created successfully');
    }

    public function update(Request $request, Organization $organization, Season $season): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $season->update($validated);

        return $this->ok(new SeasonResource($season), 'Season updated successfully');
    }

    public function destroy(Organization $organization, Season $season): JsonResponse
    {
        $this->authorize('update', $organization);

        $season->delete();

        return $this->ok(null, 'Season deleted successfully');
    }
}
