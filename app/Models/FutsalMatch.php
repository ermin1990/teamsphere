<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FutsalMatch extends Model
{
    protected $fillable = [
        'competition_id',
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score',
        'home_score_ht',
        'away_score_ht',
        'status',
        'scheduled_at',
        'kickoff_at',
        'completed_at',
        'round',
        'phase',
        'tournament_group_id',
        'venue',
        'referee_user_id',
        'notes',
    ];

    protected $casts = [
        'competition_id' => 'integer',
        'home_team_id' => 'integer',
        'away_team_id' => 'integer',
        'home_score' => 'integer',
        'away_score' => 'integer',
        'home_score_ht' => 'integer',
        'away_score_ht' => 'integer',
        'scheduled_at' => 'datetime',
        'kickoff_at' => 'datetime',
        'completed_at' => 'datetime',
        'round' => 'integer',
        'tournament_group_id' => 'integer',
        'referee_user_id' => 'integer',
    ];

    /**
     * Get the competition that this match belongs to
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the home team
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(FutsalTeam::class, 'home_team_id');
    }

    /**
     * Get the away team
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(FutsalTeam::class, 'away_team_id');
    }

    /**
     * Get the tournament group if this is a group stage match
     */
    public function tournamentGroup(): BelongsTo
    {
        return $this->belongsTo(TournamentGroup::class);
    }

    /**
     * Get the referee user
     */
    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_user_id');
    }

    /**
     * Get all match events (goals, cards, substitutions)
     */
    public function events(): HasMany
    {
        return $this->hasMany(FutsalMatchEvent::class)->orderBy('minute');
    }

    /**
     * Get only goal events
     */
    public function goals(): HasMany
    {
        return $this->hasMany(FutsalMatchEvent::class)->where('event_type', 'goal')->orderBy('minute');
    }

    /**
     * Get only card events
     */
    public function cards(): HasMany
    {
        return $this->hasMany(FutsalMatchEvent::class)
            ->whereIn('event_type', ['yellow_card', 'red_card'])
            ->orderBy('minute');
    }

    /**
     * Scope to get only completed matches
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get only live matches
     */
    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    /**
     * Scope to get only scheduled matches
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Get the winner team (null if draw or not completed)
     */
    public function getWinnerAttribute(): ?FutsalTeam
    {
        if ($this->status !== 'completed' || $this->home_score === $this->away_score) {
            return null;
        }

        return $this->home_score > $this->away_score ? $this->homeTeam : $this->awayTeam;
    }

    /**
     * Check if the match is a draw
     */
    public function getIsDrawAttribute(): bool
    {
        return $this->status === 'completed' && $this->home_score === $this->away_score;
    }
}
