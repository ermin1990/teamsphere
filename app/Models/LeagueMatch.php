<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeagueMatch extends Model
{
    use HasFactory;

    protected $table = 'matches';

    protected $fillable = [
        'competition_id',
        'home_team_id',
        'away_team_id',
        'home_player_id',
        'away_player_id',
        'home_score',
        'away_score',
        'scheduled_at',
        'played_at',
        'current_set_started_at',
        'status',
        'round',
        'sets',
        'forfeited_by',
        'first_server',
        'current_server',
        'set_durations',
        'moderator_id',
        'edited_by',
        'completed_by',
        'edited_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'played_at' => 'datetime',
        'current_set_started_at' => 'datetime',
        'sets' => 'array',
        'set_durations' => 'array',
        'home_score' => 'integer',
        'away_score' => 'integer',
        'round' => 'integer',
        'forfeited_by' => 'string',
        'first_server' => 'string',
        'current_server' => 'string',
        'edited_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the league this match belongs to.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class, 'competition_id');
    }

    /**
     * Get the moderator for this match.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Get the user who last edited this match.
     */
    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    /**
     * Get the user who completed this match.
     */
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Get the home team for this match.
     */
    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /**
     * Get the away team for this match.
     */
    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    /**
     * Get the home player for this match.
     */
    public function homePlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'home_player_id');
    }

    /**
     * Get the away player for this match.
     */
    public function awayPlayer(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'away_player_id');
    }

    /**
     * Get the winner of this match.
     */
    public function getWinnerAttribute()
    {
        // Handle forfeited matches
        if ($this->status === 'forfeited') {
            if ($this->forfeited_by === 'home') {
                return $this->away_team_id ? $this->awayTeam : $this->awayPlayer;
            } elseif ($this->forfeited_by === 'away') {
                return $this->home_team_id ? $this->homeTeam : $this->homePlayer;
            }
        }

        // Handle completed matches
        if ($this->home_score > $this->away_score) {
            return $this->home_team_id ? $this->homeTeam : $this->homePlayer;
        } elseif ($this->away_score > $this->home_score) {
            return $this->away_team_id ? $this->awayTeam : $this->awayPlayer;
        }
        return null; // Draw
    }

    /**
     * Check if match is a draw.
     */
    public function isDraw(): bool
    {
        return $this->home_score === $this->away_score;
    }
}
