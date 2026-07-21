<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrganizationResource;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class OrganizationController extends Controller
{
    use ApiResponses;

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $organizations = Organization::where('user_id', $user->id)
            ->orWhereHas('users', fn ($q) => $q->where('users.id', $user->id))
            ->orderByDesc('created_at')
            ->get();

        return $this->ok(OrganizationResource::collection($organizations));
    }

    public function show(Organization $organization): JsonResponse
    {
        $this->authorize('view', $organization);

        return $this->ok(new OrganizationResource($organization));
    }

    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Organization::class);

        if (!$request->user()->canCreateMoreOrganizations()) {
            return $this->fail('You have reached the maximum number of organizations allowed by your plan.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sport_id' => ['required', 'exists:sports,id'],
            'description' => ['nullable', 'string'],
        ]);

        $organization = Organization::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . Str::random(6),
            'description' => $validated['description'] ?? null,
            'sport_id' => $validated['sport_id'],
            'user_id' => $request->user()->id,
            'is_active' => true,
        ]);

        return $this->created(new OrganizationResource($organization), 'Organization created successfully');
    }

    public function update(Request $request, Organization $organization): JsonResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $organization->update($validated);

        return $this->ok(new OrganizationResource($organization), 'Organization updated successfully');
    }

    public function destroy(Organization $organization): JsonResponse
    {
        $this->authorize('delete', $organization);

        $organization->delete();

        return $this->ok(null, 'Organization deleted successfully');
    }
}
