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
        'organization_id',
        'competition_id',
        'captain_id',
        'status',
    ];

    protected $casts = [
        'organization_id' => 'integer',
        'competition_id' => 'integer',
        'captain_id' => 'integer',
        'status' => 'string',
    ];

    /**
     * Get the organization that owns the team.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the league that owns the team.
     */
    public function league(): BelongsTo
    {
        return $this->belongsTo(Competition::class, 'competition_id');
    }

    /**
     * Get the captain of the team.
     */
    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    /**
     * Get the players for this team (roster).
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'team_player', 'team_id', 'player_id')
                    ->withPivot('role')
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
