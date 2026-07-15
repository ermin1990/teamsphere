<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Determine if the user is an application administrator.
     */
    public function isAdmin(): bool
    {
        return (bool) $this->is_admin;
    }

    /**
     * Get the organizations for this user.
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    /**
     * Get the organization users for this user.
     */
    public function organizationUsers(): HasMany
    {
        return $this->hasMany(OrganizationUser::class);
    }

    /**
     * Get the user plans for this user.
     */
    public function userPlans(): HasMany
    {
        return $this->hasMany(UserPlan::class);
    }

    /**
     * Get the player profile for this user.
     */
    public function playerProfile(): HasOne
    {
        return $this->hasOne(Player::class);
    }

    /**
     * Whether this user owns an organization or is staff (e.g. referee) in
     * one - the generic /dashboard is for these users only; a plain player
     * lands on /moje-lige instead.
     */
    public function isOrganizerOrStaff(): bool
    {
        return $this->organizations()->exists() || $this->organizationUsers()->exists();
    }

    /**
     * Get the current active plan for this user.
     */
    public function currentPlan()
    {
        $userPlan = $this->userPlans()->active()->first();
        return $userPlan ? $userPlan->plan : null;
    }

    /**
     * Check if user can create more organizations.
     */
    public function canCreateMoreOrganizations()
    {
        $plan = $this->currentPlan();
        if (!$plan) return true; // Free plan allows 1 organization

        return $this->organizations()->count() < $plan->max_organizations;
    }

    /**
     * Check if user can create more competitions in an organization.
     */
    public function canCreateMoreCompetitions($organizationId)
    {
        $plan = $this->currentPlan();
        if (!$plan) return true; // Free plan allows unlimited competitions

        $competitionCount = \App\Models\Competition::where('organization_id', $organizationId)->count();
        return $competitionCount < $plan->max_competitions_per_organization;
    }
}
