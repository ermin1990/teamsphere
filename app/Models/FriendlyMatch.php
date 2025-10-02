<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'sets' => 'array',
        'set_durations' => 'array',
        'completed_at' => 'datetime',
    ];
}
