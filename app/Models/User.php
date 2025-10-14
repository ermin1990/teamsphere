<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'is_admin',
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
     * Get the organizations for this user.
     */
    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    /**
     * Get the user plans for this user.
     */
    public function userPlans(): HasMany
    {
        return $this->hasMany(UserPlan::class);
    }

    /**
     * Get the leagues for this user through organizations.
     */
    public function leagues()
    {
        return League::whereHas('organization', function ($query) {
            $query->where('user_id', $this->id);
        });
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
        if (!$plan) {
            // No active plan - treat as Free plan (1 organization)
            return $this->organizations()->count() < 1;
        }

        return $this->organizations()->count() < $plan->max_organizations;
    }
}
