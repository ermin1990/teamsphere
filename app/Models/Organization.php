<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;

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
     * Resolve route binding with debugging.
     */
    public function resolveRouteBinding($value, $field = null)
    {
        $logFile = public_path('debug_organization.log');
        $timestamp = date('Y-m-d H:i:s');
        
        file_put_contents($logFile, "[$timestamp] Organization::resolveRouteBinding called\n", FILE_APPEND);
        file_put_contents($logFile, "  Value: $value\n", FILE_APPEND);
        file_put_contents($logFile, "  Field: " . ($field ?? 'null') . "\n", FILE_APPEND);
        
        $organization = $this->where($field ?? $this->getRouteKeyName(), $value)->first();
        
        if ($organization) {
            file_put_contents($logFile, "  Organization FOUND: ID={$organization->id}, Name={$organization->name}\n", FILE_APPEND);
        } else {
            file_put_contents($logFile, "  Organization NOT FOUND\n", FILE_APPEND);
        }
        
        file_put_contents($logFile, "\n", FILE_APPEND);
        
        return $organization;
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
     */
    public function leagues(): HasMany
    {
        return $this->hasMany(League::class);
    }

    /**
     * Get the competitions for this organization.
     */
    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class);
    }

    /**
     * Get the organization users for this organization.
     */
    public function organizationUsers(): HasMany
    {
        return $this->hasMany(OrganizationUser::class);
    }

    /**
     * Get the users that belong to this organization (many-to-many).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'organization_user')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Get the players for this organization.
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
     * Get active organizations with caching.
     */
    public static function getActiveOrganizations()
    {
        return Cache::remember('active_organizations', 3600, function () {
            return self::active()->with(['user', 'leagues'])->get();
        });
    }

    /**
     * Get organizations for user with caching.
     */
    public static function getOrganizationsForUser($userId)
    {
        return Cache::remember("user_{$userId}_organizations", 1800, function () use ($userId) {
            return self::where('user_id', $userId)
                      ->with(['leagues', 'players'])
                      ->orderBy('created_at', 'desc')
                      ->get();
        });
    }

    /**
     * Clear organization cache.
     */
    public static function clearOrganizationCache()
    {
        Cache::forget('active_organizations');
        // Clear user-specific caches
        $users = User::pluck('id');
        foreach ($users as $userId) {
            Cache::forget("user_{$userId}_organizations");
        }
    }

    /**
     * Check if organization can create more leagues.
     */
    public function canCreateMoreLeagues()
    {
        $plan = $this->user->currentPlan();
        if (!$plan) return true; // Free plan allows unlimited leagues per organization

        return $this->leagues()->count() < $plan->max_leagues_per_organization;
    }

    /**
     * Check if organization can create more competitions.
     */
    public function canCreateMoreCompetitions()
    {
        $plan = $this->user->currentPlan();
        if (!$plan) return true; // Free plan allows unlimited competitions per organization

        return $this->competitions()->count() < $plan->max_competitions_per_organization;
    }

    /**
     * Get remaining leagues that can be created.
     */
    public function getRemainingLeaguesCount()
    {
        $plan = $this->user->currentPlan();
        if (!$plan) return PHP_INT_MAX; // Free plan allows unlimited

        return max(0, $plan->max_leagues_per_organization - $this->leagues()->count());
    }

    /**
     * Get remaining competitions that can be created.
     */
    public function getRemainingCompetitionsCount()
    {
        $plan = $this->user->currentPlan();
        if (!$plan) return PHP_INT_MAX; // Free plan allows unlimited

        return max(0, $plan->max_competitions_per_organization - $this->competitions()->count());
    }
}
