<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrganizationLinkResource;
use App\Models\Organization;
use App\Models\OrganizationLink;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class OrganizationLinkController extends Controller
{
    use ApiResponses;

    public function index(Request $request, Organization $organization): JsonResponse
    {
        Gate::authorize('view', $organization);

        $links = $organization->links()->paginate($this->perPage($request));

        return $this->paginated($links, OrganizationLinkResource::class);
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        Gate::authorize('update', $organization);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:255'],
        ]);

        $url = strtolower($validated['url']);
        $type = 'other';
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            $type = 'youtube';
        } elseif (str_contains($url, 'facebook.com')) {
            $type = 'facebook';
        } elseif (str_contains($url, 'instagram.com')) {
            $type = 'instagram';
        }

        $link = $organization->links()->create([
            'title' => $validated['title'],
            'url' => $validated['url'],
            'type' => $type,
            'sort_order' => $organization->links()->count() + 1,
        ]);

        return $this->created(new OrganizationLinkResource($link), 'Link added successfully');
    }

    public function destroy(Organization $organization, OrganizationLink $link): JsonResponse
    {
        Gate::authorize('update', $organization);

        if ($link->organization_id !== $organization->id) {
            return $this->fail('Link does not belong to this organization.', 404);
        }

        $link->delete();

        return $this->ok(null, 'Link deleted successfully');
    }
}
