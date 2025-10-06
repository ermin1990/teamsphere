<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Standing extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_id',
        'team_id',
        'player_id',
        'played',
        'won',
        'drawn',
        'lost',
        'points',
        'goals_for',
        'goals_against',
        'goal_difference',
        'position',
    ];

    protected $casts = [
        'played' => 'integer',
        'won' => 'integer',
        'drawn' => 'integer',
        'lost' => 'integer',
        'points' => 'integer',
        'goals_for' => 'integer',
        'goals_against' => 'integer',
        'goal_difference' => 'integer',
        'position' => 'integer',
    ];

    /**
     * Get the league this standing belongs to.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the team for this standing.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the player for this standing.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get the participant (team or player) for this standing.
     */
    public function getParticipantAttribute()
    {
        return $this->team ?? $this->player;
    }

    /**
     * Get the participant name.
     */
    public function getParticipantNameAttribute()
    {
        return $this->participant->name;
    }
}
