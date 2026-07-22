<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrganizationUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class OrganizationUserController extends Controller
{
    /**
     * Display a listing of organization users.
     */
    public function index(Organization $organization)
    {
        Gate::authorize('manage-organization-users', $organization);

        $organizationUsers = $organization->organizationUsers()->with('user')->get();
        $isOwner = $organization->user_id === auth()->id();

        return view('organizations.users.index', compact('organization', 'organizationUsers', 'isOwner'));
    }

    /**
     * Show the form for creating a new organization user.
     */
    public function create(Organization $organization)
    {
        Gate::authorize('manage-organization-users', $organization);

        return view('organizations.users.create', compact('organization'));
    }

    /**
     * Store a newly created organization user.
     */
    public function store(Request $request, Organization $organization)
    {
        Gate::authorize('manage-organization-users', $organization);

        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|in:referee,moderator',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user is already a member
        if ($organization->organizationUsers()->where('user_id', $user->id)->exists()) {
            return back()->withErrors(['email' => 'User is already a member of this organization.']);
        }

        $organization->organizationUsers()->create([
            'user_id' => $user->id,
            'role' => $request->role,
        ]);

        return redirect()->route('organizations.users.index', $organization)
                        ->with('success', 'User added to organization successfully.');
    }

    /**
     * Remove the specified organization user.
     */
    public function destroy(Organization $organization, OrganizationUser $organizationUser)
    {
        Gate::authorize('manage-organization-users', $organization);

        abort_unless($organizationUser->organization_id === $organization->id, 404);

        // Prevent removing the owner
        if ($organizationUser->isOwner()) {
            return back()->withErrors(['error' => 'Cannot remove the organization owner.']);
        }

        $organizationUser->delete();

        return redirect()->route('organizations.users.index', $organization)
                        ->with('success', 'User removed from organization successfully.');
    }
}
