<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FriendlyMatch extends Model
{
    protected $fillable = [
        'organization_id',
        'home_player_id',
        'away_player_id',
        'home_player_name',
        'away_player_name',
        'sets',
        'set_durations',
        'winner_name',
        'completed_at',
    ];

    protected $casts = [
        'organization_id' => 'integer',
        'home_player_id' => 'integer',
        'away_player_id' => 'integer',
        'sets' => 'array',
        'set_durations' => 'array',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the organization this match belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
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
}
