<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FutsalStanding extends Model
{
    protected $fillable = [
        'competition_id',
        'futsal_team_id',
        'tournament_group_id',
        'position',
        'played',
        'won',
        'drawn',
        'lost',
        'goals_for',
        'goals_against',
        'goal_difference',
        'points',
    ];

    protected $casts = [
        'competition_id' => 'integer',
        'futsal_team_id' => 'integer',
        'tournament_group_id' => 'integer',
        'position' => 'integer',
        'played' => 'integer',
        'won' => 'integer',
        'drawn' => 'integer',
        'lost' => 'integer',
        'goals_for' => 'integer',
        'goals_against' => 'integer',
        'goal_difference' => 'integer',
        'points' => 'integer',
    ];

    /**
     * Get the competition these standings belong to
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the team these standings belong to
     */
    public function futsalTeam(): BelongsTo
    {
        return $this->belongsTo(FutsalTeam::class);
    }

    /**
     * Get the tournament group if this is a group stage
     */
    public function tournamentGroup(): BelongsTo
    {
        return $this->belongsTo(TournamentGroup::class);
    }

    /**
     * Get win percentage
     */
    public function getWinPercentageAttribute(): float
    {
        return $this->played > 0 
            ? round(($this->won / $this->played) * 100, 1) 
            : 0;
    }

    /**
     * Get points per game average
     */
    public function getPointsPerGameAttribute(): float
    {
        return $this->played > 0 
            ? round($this->points / $this->played, 2) 
            : 0;
    }

    /**
     * Get goals per game average
     */
    public function getGoalsPerGameAttribute(): float
    {
        return $this->played > 0 
            ? round($this->goals_for / $this->played, 2) 
            : 0;
    }

    /**
     * Get goals conceded per game average
     */
    public function getGoalsConcededPerGameAttribute(): float
    {
        return $this->played > 0 
            ? round($this->goals_against / $this->played, 2) 
            : 0;
    }

    /**
     * Get team form (based on last 5 matches if needed in the future)
     */
    public function getFormAttribute(): string
    {
        // This could be enhanced to show actual form like "WWDLW"
        // For now, return a simple indicator
        if ($this->played === 0) return '-';
        
        $winRate = $this->win_percentage;
        if ($winRate >= 60) return 'Good';
        if ($winRate >= 40) return 'Average';
        return 'Poor';
    }

    /**
     * Scope to order by standard futsal standings rules
     */
    public function scopeOrderByStandings($query)
    {
        return $query->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->orderBy('won', 'desc');
    }

    /**
     * Check if team is in promotion/qualification zone (top 2)
     */
    public function getIsQualifiedAttribute(): bool
    {
        return $this->position <= 2;
    }

    /**
     * Check if team won the group (1st place)
     */
    public function getIsGroupWinnerAttribute(): bool
    {
        return $this->position === 1;
    }
}
