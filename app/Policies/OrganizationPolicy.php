<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Users can view organizations they have access to
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): bool
    {
        // User can view if they own the organization
        if ($user->id === $organization->user_id) {
            return true;
        }

        // User can view if they are a member of the organization
        return $organization->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create organizations
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organization $organization): bool
    {
        // Owner can update
        if ($user->id === $organization->user_id) {
            return true;
        }
        
        // Organization members can also update (manage players, competitions, etc.)
        return $organization->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organization $organization): bool
    {
        return $user->id === $organization->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Organization $organization): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Organization $organization): bool
    {
        return false;
    }

    /**
     * Determine whether the user can manage organization users.
     */
    public function manageOrganizationUsers(User $user, Organization $organization): bool
    {
        return $user->id === $organization->user_id;
    }

    /**
     * Determine whether the user can publish/manage announcements and rules
     * for this organization (owner or a moderator - not a referee).
     */
    public function manageAnnouncements(User $user, Organization $organization): bool
    {
        if ($user->id === $organization->user_id) {
            return true;
        }

        return $organization->organizationUsers()
            ->where('user_id', $user->id)
            ->where('role', 'moderator')
            ->exists();
    }
}
