<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class League extends Model
{
    use HasFactory;

    protected $table = 'competitions';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'organization_id',
        'sport_id',
        'status',
        'start_date',
        'end_date',
        'max_teams',
        'is_team_based',
        'settings',
        'is_active',
        'type',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_teams' => 'integer',
        'is_team_based' => 'boolean',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Add global scope to only include league type competitions
        static::addGlobalScope('league_type', function ($builder) {
            $builder->where('type', 'league');
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the organization that owns the league.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the sport for this league.
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get the teams for this league.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class, 'competition_id');
    }

    /**
     * Get the players for this league.
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'competition_player', 'competition_id', 'player_id')
                    ->withPivot('joined_at')
                    ->withTimestamps()
                    ->withCasts([
                        'joined_at' => 'datetime'
                    ]);
    }

    /**
     * Get the matches for this league.
     */
    public function matches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'league_id');
    }

    /**
     * Get the standings for this league.
     */
    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class, 'competition_id')->orderBy('position');
    }

    /**
     * Scope a query to only include active leagues.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include leagues with specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get active leagues with caching.
     */
    public static function getActiveLeagues()
    {
        return Cache::remember('active_leagues', 3600, function () {
            return self::active()->with(['organization', 'sport'])->get();
        });
    }

    /**
     * Get leagues for organization with caching.
     */
    public static function getLeaguesForOrganization($organizationId)
    {
        return Cache::remember("organization_{$organizationId}_leagues", 1800, function () use ($organizationId) {
            return self::where('organization_id', $organizationId)
                      ->with(['sport', 'teams'])
                      ->orderBy('created_at', 'desc')
                      ->get();
        });
    }

    /**
     * Clear league cache.
     */
    public static function clearLeagueCache()
    {
        Cache::forget('active_leagues');
        // Clear organization-specific caches
        $organizations = Organization::pluck('id');
        foreach ($organizations as $orgId) {
            Cache::forget("organization_{$orgId}_leagues");
        }
    }

    /**
     * Check if league can accept more teams.
     */
    public function canAcceptMoreTeams()
    {
        return $this->max_teams === null || $this->teams()->count() < $this->max_teams;
    }

    /**
     * Get remaining spots for teams.
     */
    public function getRemainingTeamSpots()
    {
        return max(0, $this->max_teams - $this->teams()->count());
    }

    /**
     * Check if league is currently active (between start and end dates).
     */
    public function isCurrentlyActive()
    {
        $now = now()->toDateString();
        return $this->start_date <= $now && ($this->end_date >= $now || $this->end_date === null);
    }
}
