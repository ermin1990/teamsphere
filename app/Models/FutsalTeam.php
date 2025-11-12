<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FutsalTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'competition_id',
        'name',
        'short_name',
        'description',
        'logo_url',
        'primary_color',
        'secondary_color',
        'captain_name',
        'coach_name',
        'home_venue',
        'is_active',
    ];

    protected $casts = [
        'organization_id' => 'integer',
        'competition_id' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the organization that owns the team.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the competition this team belongs to.
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the players (roster) for this team.
     */
    public function players(): HasMany
    {
        return $this->hasMany(FutsalTeamPlayer::class);
    }

    /**
     * Get active players only.
     */
    public function activePlayers(): HasMany
    {
        return $this->hasMany(FutsalTeamPlayer::class)->where('is_active', true);
    }

    /**
     * Get home matches for this team.
     */
    public function homeMatches(): HasMany
    {
        return $this->hasMany(FutsalMatch::class, 'home_team_id');
    }

    /**
     * Get away matches for this team.
     */
    public function awayMatches(): HasMany
    {
        return $this->hasMany(FutsalMatch::class, 'away_team_id');
    }

    /**
     * Get all matches (home + away) for this team.
     */
    public function getAllMatches()
    {
        return FutsalMatch::where('home_team_id', $this->id)
            ->orWhere('away_team_id', $this->id);
    }

    /**
     * Get standings for this team.
     */
    public function standings(): HasMany
    {
        return $this->hasMany(FutsalStanding::class);
    }

    /**
     * Get match events for this team.
     */
    public function matchEvents(): HasMany
    {
        return $this->hasMany(FutsalMatchEvent::class);
    }
}
