<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    use HasFactory;

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
        return $this->hasMany(Team::class);
    }

    /**
     * Get the players for this league.
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'league_player', 'league_id', 'player_id')
                    ->withPivot('joined_at')
                    ->withTimestamps()
                    ->withCasts([
                        'joined_at' => 'datetime'
                    ]);
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
     * Check if league can accept more teams.
     */
    public function canAcceptMoreTeams()
    {
        return $this->teams()->count() < $this->max_teams;
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
