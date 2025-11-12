<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FutsalMatchEvent extends Model
{
    protected $fillable = [
        'futsal_match_id',
        'futsal_team_id',
        'player_id',
        'assist_player_id',
        'event_type',
        'minute',
        'description',
    ];

    protected $casts = [
        'futsal_match_id' => 'integer',
        'futsal_team_id' => 'integer',
        'player_id' => 'integer',
        'assist_player_id' => 'integer',
        'minute' => 'integer',
    ];

    /**
     * Get the match that this event belongs to
     */
    public function futsalMatch(): BelongsTo
    {
        return $this->belongsTo(FutsalMatch::class);
    }

    /**
     * Get the team that this event belongs to
     */
    public function futsalTeam(): BelongsTo
    {
        return $this->belongsTo(FutsalTeam::class);
    }

    /**
     * Get the player who performed the action
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(FutsalTeamPlayer::class, 'player_id');
    }

    /**
     * Get the player who provided the assist (for goals)
     */
    public function assistPlayer(): BelongsTo
    {
        return $this->belongsTo(FutsalTeamPlayer::class, 'assist_player_id');
    }

    /**
     * Scope to get only goals
     */
    public function scopeGoals($query)
    {
        return $query->where('event_type', 'goal');
    }

    /**
     * Scope to get only cards
     */
    public function scopeCards($query)
    {
        return $query->whereIn('event_type', ['yellow_card', 'red_card']);
    }

    /**
     * Scope to get only yellow cards
     */
    public function scopeYellowCards($query)
    {
        return $query->where('event_type', 'yellow_card');
    }

    /**
     * Scope to get only red cards
     */
    public function scopeRedCards($query)
    {
        return $query->where('event_type', 'red_card');
    }

    /**
     * Scope to get only substitutions
     */
    public function scopeSubstitutions($query)
    {
        return $query->whereIn('event_type', ['substitution_in', 'substitution_out']);
    }

    /**
     * Check if this is a goal event
     */
    public function getIsGoalAttribute(): bool
    {
        return $this->event_type === 'goal';
    }

    /**
     * Check if this is a card event
     */
    public function getIsCardAttribute(): bool
    {
        return in_array($this->event_type, ['yellow_card', 'red_card']);
    }

    /**
     * Get formatted event description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        $playerName = $this->player?->player_name ?? 'Unknown';
        
        return match($this->event_type) {
            'goal' => $this->assist_player_id 
                ? "⚽ {$playerName} (Assist: {$this->assistPlayer?->player_name})"
                : "⚽ {$playerName}",
            'yellow_card' => "🟨 {$playerName}",
            'red_card' => "🟥 {$playerName}",
            'substitution_in' => "🔼 {$playerName}",
            'substitution_out' => "🔽 {$playerName}",
            default => $this->description ?? $playerName,
        };
    }
}
