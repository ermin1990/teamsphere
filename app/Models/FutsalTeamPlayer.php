<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FutsalTeamPlayer extends Model
{
    protected $fillable = [
        'futsal_team_id',
        'player_name',
        'jersey_number',
        'position',
        'date_of_birth',
        'nationality',
        'is_captain',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'futsal_team_id' => 'integer',
        'jersey_number' => 'integer',
        'date_of_birth' => 'date',
        'is_captain' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the team that this player belongs to
     */
    public function futsalTeam(): BelongsTo
    {
        return $this->belongsTo(FutsalTeam::class);
    }

    /**
     * Get all match events for this player
     */
    public function matchEvents(): HasMany
    {
        return $this->hasMany(FutsalMatchEvent::class, 'player_id');
    }

    /**
     * Get statistics for this player
     */
    public function stats(): HasMany
    {
        return $this->hasMany(FutsalPlayerStats::class);
    }

    /**
     * Scope to get only active players
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get team captain
     */
    public function scopeCaptain($query)
    {
        return $query->where('is_captain', true);
    }

    /**
     * Get player's full display name with jersey number
     */
    public function getDisplayNameAttribute(): string
    {
        return "#{$this->jersey_number} {$this->player_name}";
    }
}
