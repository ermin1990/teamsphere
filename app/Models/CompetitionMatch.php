<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitionMatch extends Model
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
        'table_id',
        'referee_user_id',
        'first_server',
        'current_server',
        'set_durations',
        // Tournament fields
        'phase',
        'tournament_group_id',
        'round_number',
        'bracket_position',
        'is_bye',
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
        // Tournament casts
        'round_number' => 'integer',
        'bracket_position' => 'integer',
        'is_bye' => 'boolean',
    ];

    /**
     * Get the league this match belongs to (for league matches).
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the competition this match belongs to (for tournament matches).
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the tournament group this match belongs to (if group phase).
     */
    public function tournamentGroup(): BelongsTo
    {
        return $this->belongsTo(TournamentGroup::class);
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
        // Handle bye matches
        if ($this->is_bye) {
            return $this->home_player_id ? $this->homePlayer : $this->homeTeam;
        }

        // Handle forfeited matches
        if ($this->status === 'forfeited') {
            if ($this->forfeited_by === 'home') {
                return $this->away_team_id ? $this->awayTeam : $this->awayPlayer;
            } elseif ($this->forfeited_by === 'away') {
                return $this->home_team_id ? $this->homeTeam : $this->homePlayer;
            }
        }

        // Handle completed matches
        if ($this->status === 'completed') {
            if (($this->home_score ?? 0) > ($this->away_score ?? 0)) {
                return $this->home_team_id ? $this->homeTeam : $this->homePlayer;
            } elseif (($this->home_score ?? 0) < ($this->away_score ?? 0)) {
                return $this->away_team_id ? $this->awayTeam : $this->awayPlayer;
            }
        }

        return null;
    }

    /**
     * Get the loser of this match.
     */
    public function getLoserAttribute()
    {
        // Handle bye matches
        if ($this->is_bye) {
            return null; // Bye has no loser
        }

        $winner = $this->winner;

        if ($winner) {
            if ($this->home_team_id && $winner->id === $this->home_team_id) {
                return $this->awayTeam;
            } elseif ($this->away_team_id && $winner->id === $this->away_team_id) {
                return $this->homeTeam;
            } elseif ($this->home_player_id && $winner->id === $this->home_player_id) {
                return $this->awayPlayer;
            } elseif ($this->away_player_id && $winner->id === $this->away_player_id) {
                return $this->homePlayer;
            }
        }

        return null;
    }

    /**
     * Check if this is a group phase match.
     */
    public function isGroupMatch(): bool
    {
        return $this->phase === 'group';
    }

    /**
     * Check if this is a knockout phase match.
     */
    public function isKnockoutMatch(): bool
    {
        return in_array($this->phase, ['round_of_16', 'quarter_final', 'semi_final', 'final', 'third_place']);
    }

    /**
     * Get the phase display name.
     */
    public function getPhaseDisplayAttribute(): string
    {
        return match($this->phase) {
            'group' => 'Group Stage',
            'round_of_16' => 'Round of 16',
            'quarter_final' => 'Quarter Final',
            'semi_final' => 'Semi Final',
            'final' => 'Final',
            'third_place' => 'Third Place',
            default => 'Unknown Phase'
        };
    }

    /**
     * Get the table for this match.
     */
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_id');
    }

    /**
     * Get the referee for this match.
     */
    public function referee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referee_user_id');
    }
}
