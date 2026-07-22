<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, \Illuminate\Auth\MustVerifyEmail;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'theme',
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
     * Get the push-notification device tokens registered by this user
     * (one per installed app instance/device).
     */
    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
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
     * A freshly registered organizer has no organization yet (registration
     * never creates one automatically) and no Player row either - without
     * this check they'd fail isOrganizerOrStaff() and get bounced to the
     * player dashboard on every login until they create their first
     * organization, even though they explicitly chose "organizer" at signup.
     */
    public function needsOrganizationOnboarding(): bool
    {
        return !$this->isOrganizerOrStaff() && !$this->playerProfile()->exists();
    }

    /**
     * Get the current active plan for this user, falling back to the Free
     * plan when the user has no active UserPlan row - a user without an
     * explicit subscription is on Free, not unlimited.
     */
    public function currentPlan()
    {
        $userPlan = $this->userPlans()->active()->first();

        if ($userPlan) {
            return $userPlan->plan;
        }

        return Plan::where('slug', 'free')->first();
    }

    /**
     * Check if user can create more organizations.
     */
    public function canCreateMoreOrganizations()
    {
        $plan = $this->currentPlan();
        if (!$plan) return false;

        return $this->organizations()->count() < $plan->max_organizations;
    }

    /**
     * Check if user can create more competitions in an organization.
     */
    public function canCreateMoreCompetitions($organizationId)
    {
        $plan = $this->currentPlan();
        if (!$plan) return false;

        $competitionCount = \App\Models\Competition::where('organization_id', $organizationId)->count();
        return $competitionCount < $plan->max_competitions_per_organization;
    }

    /**
     * Send the Bosnian-language password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }

    /**
     * Send the Bosnian-language email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification());
    }
}
