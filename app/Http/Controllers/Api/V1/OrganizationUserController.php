<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\OrganizationUserResource;
use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class OrganizationUserController extends Controller
{
    use ApiResponses;

    public function index(Request $request, Organization $organization): JsonResponse
    {
        Gate::authorize('manage-organization-users', $organization);

        $organizationUsers = $organization->organizationUsers()->with('user')->paginate($this->perPage($request));

        return $this->paginated($organizationUsers, OrganizationUserResource::class);
    }

    public function store(Request $request, Organization $organization): JsonResponse
    {
        Gate::authorize('manage-organization-users', $organization);

        $validated = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'role' => ['required', 'in:referee,moderator'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if ($organization->organizationUsers()->where('user_id', $user->id)->exists()) {
            return $this->fail('User is already a member of this organization.', 422);
        }

        $organizationUser = $organization->organizationUsers()->create([
            'user_id' => $user->id,
            'role' => $validated['role'],
        ]);

        return $this->created(new OrganizationUserResource($organizationUser->load('user')), 'User added to organization successfully');
    }

    public function destroy(Organization $organization, OrganizationUser $organizationUser): JsonResponse
    {
        Gate::authorize('manage-organization-users', $organization);

        abort_unless($organizationUser->organization_id === $organization->id, 404);

        if ($organizationUser->isOwner()) {
            return $this->fail('Cannot remove the organization owner.', 422);
        }

        $organizationUser->delete();

        return $this->ok(null, 'User removed from organization successfully');
    }
}
