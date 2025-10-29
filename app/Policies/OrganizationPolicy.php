<?php

namespace App\Policies;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    private function debugLog($message, $data = [])
    {
        // Write to public folder for easy access
        $logFile = public_path('debug_organization.log');
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message\n" . print_r($data, true) . "\n\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $this->debugLog('OrganizationPolicy::viewAny called', [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);
        
        return true; // Users can view organizations they have access to
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): bool
    {
        $this->debugLog('OrganizationPolicy::view called', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'organization_id' => $organization->id,
            'organization_name' => $organization->name,
            'organization_user_id' => $organization->user_id,
            'is_owner' => $user->id === $organization->user_id,
        ]);

        // User can view if they own the organization
        if ($user->id === $organization->user_id) {
            $this->debugLog('OrganizationPolicy::view - User is OWNER - ACCESS GRANTED', [
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ]);
            return true;
        }

        // User can view if they are a member of the organization
        $isMember = $organization->organizationUsers()->where('user_id', $user->id)->exists();
        $orgUsers = $organization->organizationUsers()->get();
        
        $this->debugLog('OrganizationPolicy::view - Member check', [
            'user_id' => $user->id,
            'organization_id' => $organization->id,
            'is_member' => $isMember,
            'organization_users_count' => $orgUsers->count(),
            'organization_users' => $orgUsers->toArray(),
        ]);

        if ($isMember) {
            $this->debugLog('OrganizationPolicy::view - User is MEMBER - ACCESS GRANTED', [
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ]);
        } else {
            $this->debugLog('OrganizationPolicy::view - ACCESS DENIED', [
                'user_id' => $user->id,
                'organization_id' => $organization->id,
                'reason' => 'Not owner and not member',
            ]);
        }

        return $isMember;
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
        return $user->id === $organization->user_id;
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
}
