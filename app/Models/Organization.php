<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo',
        'user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the url_slug attribute (alias for slug).
     */
    public function getUrlSlugAttribute()
    {
        return $this->slug;
    }

    /**
     * Get the user that owns the organization.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the leagues for this organization.
     * Note: This relationship should be eager loaded when displaying organization lists
     * to prevent N+1 queries. Use ->with('leagues') when loading organizations.
     */
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }

    /**
     * Get the players for this organization.
     * Note: This relationship should be eager loaded when displaying organization details
     * to prevent N+1 queries. Use ->with('players') when loading organizations.
     */
    public function players(): HasMany
    {
        return $this->hasMany(Player::class);
    }

    /**
     * Scope a query to only include active organizations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if organization can create more leagues.
     */
    public function canCreateMoreLeagues()
    {
        $plan = $this->user->currentPlan();
        if (!$plan) {
            // No active plan - treat as Free plan (1 league per organization)
            return $this->leagues()->count() < 1;
        }

        return $this->leagues()->count() < $plan->max_leagues_per_organization;
    }

    /**
     * Get remaining leagues that can be created.
     */
    public function getRemainingLeaguesCount()
    {
        $plan = $this->user->currentPlan();
        if (!$plan) {
            // No active plan - treat as Free plan (1 league)
            return max(0, 1 - $this->leagues()->count());
        }

        return max(0, $plan->max_leagues_per_organization - $this->leagues()->count());
    }
}
