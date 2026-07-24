<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'user_id',
        'organization_id',
        'date_of_birth',
        'position',
        'jersey_number',
        'is_active',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'organization_id' => 'integer',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that this player represents (if registered).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organization this player belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the teams this player belongs to.
     */
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_player', 'player_id', 'team_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    /**
     * Get the leagues this player participates in.
     *
     * Note: League has a global scope for type='league', so this excludes
     * tournaments - use competitions() when tournaments should be included too
     * (e.g. the public player profile).
     */
    public function leagues(): BelongsToMany
    {
        return $this->belongsToMany(League::class, 'competition_player', 'player_id', 'competition_id')
                    ->withPivot('joined_at')
                    ->withTimestamps()
                    ->withCasts([
                        'joined_at' => 'datetime'
                    ]);
    }

    /**
     * Get all competitions (leagues and tournaments) this player participates
     * in - unlike leagues(), not restricted to type='league'.
     */
    public function competitions(): BelongsToMany
    {
        return $this->belongsToMany(Competition::class, 'competition_player', 'player_id', 'competition_id')
                    ->withPivot('joined_at')
                    ->withTimestamps()
                    ->withCasts([
                        'joined_at' => 'datetime'
                    ]);
    }

    /**
     * Get matches where this player is the home player.
     */
    public function homeMatches(): HasMany
    {
        return $this->hasMany(\App\Models\LeagueMatch::class, 'home_player_id');
    }

    /**
     * Get matches where this player is the away player.
     */
    public function awayMatches(): HasMany
    {
        return $this->hasMany(\App\Models\LeagueMatch::class, 'away_player_id');
    }

    /**
     * Scope a query to only include active players.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include registered players.
     */
    public function scopeRegistered($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope a query to only include unregistered players.
     */
    public function scopeUnregistered($query)
    {
        return $query->whereNull('user_id');
    }

    /**
     * Check if player is registered user.
     */
    public function isRegistered(): bool
    {
        return !is_null($this->user_id);
    }

    /**
     * Get player's age.
     */
    public function getAge(): ?int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    /**
     * Get player's display name.
     */
    public function getDisplayName(): string
    {
        if ($this->jersey_number) {
            return $this->name . ' #' . $this->jersey_number;
        }
        return $this->name;
    }

    /**
     * Get all matches that the player has participated in, either as an individual or as a team member.
     */
    public function matches()
    {
        return \App\Models\LeagueMatch::where(function($q) {
            $q->where('home_player_id', $this->id)
              ->orWhere('away_player_id', $this->id)
              ->orWhereHas('homeTeam.players', function($q2) {
                  $q2->where('players.id', $this->id);
              })
              ->orWhereHas('awayTeam.players', function($q2) {
                  $q2->where('players.id', $this->id);
              });
        });
    }

    /**
     * Get all matches that the player has participated in within an organization.
     * This includes both league matches (from leagues the player participates in) and friendly matches.
     */
    public function matchesInOrganization($organizationId)
    {
        // League matches where the league belongs to the organization and the player participates
        $leagueMatches = \App\Models\LeagueMatch::whereHas('league', function($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->where(function($q) {
                $q->where('home_player_id', $this->id)
                  ->orWhere('away_player_id', $this->id)
                  ->orWhere(function($q2) {
                      $q2->whereHas('homeTeam.players', function($q3) {
                          $q3->where('players.id', $this->id);
                      });
                  })
                  ->orWhere(function($q2) {
                      $q2->whereHas('awayTeam.players', function($q3) {
                          $q3->where('players.id', $this->id);
                      });
                  });
            })
            ->whereNotNull('played_at') // Only completed league matches
            ->with(['league', 'homeTeam', 'awayTeam', 'homePlayer', 'awayPlayer'])
            ->get()
            ->map(function($match) {
                $match->match_type = 'league';
                return $match;
            });

        // Friendly matches from the organization
        $friendlyMatches = \App\Models\FriendlyMatch::where('organization_id', $organizationId)
            ->whereNotNull('completed_at') // Only completed friendly matches
            ->where(function($q) {
                $q->where('home_player_name', 'LIKE', '%' . $this->name . '%')
                  ->orWhere('away_player_name', 'LIKE', '%' . $this->name . '%');
            })
            ->with(['homePlayer', 'awayPlayer'])
            ->get()
            ->map(function($match) {
                $match->match_type = 'friendly';
                return $match;
            });

        // Return as separate collections
        return [
            'league' => $leagueMatches->sortByDesc('played_at'),
            'friendly' => $friendlyMatches->sortByDesc('completed_at')
        ];
    }
}
