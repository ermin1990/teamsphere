<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FutsalPlayerStats extends Model
{
    protected $fillable = [
        'competition_id',
        'futsal_team_player_id',
        'futsal_team_id',
        'matches_played',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
        'minutes_played',
    ];

    protected $casts = [
        'competition_id' => 'integer',
        'futsal_team_player_id' => 'integer',
        'futsal_team_id' => 'integer',
        'matches_played' => 'integer',
        'goals' => 'integer',
        'assists' => 'integer',
        'yellow_cards' => 'integer',
        'red_cards' => 'integer',
        'minutes_played' => 'integer',
    ];

    /**
     * Get the competition these stats belong to
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the player these stats belong to
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(FutsalTeamPlayer::class, 'futsal_team_player_id');
    }

    /**
     * Get the team these stats belong to
     */
    public function futsalTeam(): BelongsTo
    {
        return $this->belongsTo(FutsalTeam::class);
    }

    /**
     * Get goals per match average
     */
    public function getGoalsPerMatchAttribute(): float
    {
        return $this->matches_played > 0 
            ? round($this->goals / $this->matches_played, 2) 
            : 0;
    }

    /**
     * Get assists per match average
     */
    public function getAssistsPerMatchAttribute(): float
    {
        return $this->matches_played > 0 
            ? round($this->assists / $this->matches_played, 2) 
            : 0;
    }

    /**
     * Get total goal contributions (goals + assists)
     */
    public function getGoalContributionsAttribute(): int
    {
        return $this->goals + $this->assists;
    }

    /**
     * Get average minutes per match
     */
    public function getMinutesPerMatchAttribute(): float
    {
        return $this->matches_played > 0 
            ? round($this->minutes_played / $this->matches_played, 2) 
            : 0;
    }

    /**
     * Scope to order by goals
     */
    public function scopeTopScorers($query)
    {
        return $query->orderBy('goals', 'desc')->orderBy('assists', 'desc');
    }

    /**
     * Scope to order by assists
     */
    public function scopeTopAssists($query)
    {
        return $query->orderBy('assists', 'desc')->orderBy('goals', 'desc');
    }

    /**
     * Scope to get players with at least one goal
     */
    public function scopeWithGoals($query)
    {
        return $query->where('goals', '>', 0);
    }
}
