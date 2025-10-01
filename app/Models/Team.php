<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'league_id',
        'captain_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the league that owns the team.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    /**
     * Get the captain of the team.
     */
    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    /**
     * Get the players for this team.
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_user', 'team_id', 'user_id')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    /**
     * Scope a query to only include active teams.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
