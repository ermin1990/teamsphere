<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Standing;
use App\Models\LeagueMatch;

class League extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'organization_id',
        'sport_id',
        'status',
        'start_date',
        'end_date',
        'max_teams',
        'is_team_based',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_teams' => 'integer',
        'is_team_based' => 'boolean',
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the organization that owns the league.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the sport for this league.
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get the teams for this league.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the players for this league.
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'league_player', 'league_id', 'player_id')
                    ->withPivot('joined_at')
                    ->withTimestamps()
                    ->withCasts([
                        'joined_at' => 'datetime'
                    ]);
    }

    /**
     * Get the matches for this league.
     */
    public function matches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class);
    }

    /**
     * Get the standings for this league.
     */
    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class)->orderBy('position');
    }

    /**
     * Scope a query to only include active leagues.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include leagues with specific status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Check if league can accept more teams.
     */
    public function canAcceptMoreTeams()
    {
        return $this->max_teams === null || $this->teams()->count() < $this->max_teams;
    }

    /**
     * Get remaining spots for teams.
     */
    public function getRemainingTeamSpots()
    {
        return max(0, $this->max_teams - $this->teams()->count());
    }

    /**
     * Check if league is currently active (between start and end dates).
     */
    public function isCurrentlyActive()
    {
        $now = now()->toDateString();
        return $this->start_date <= $now && ($this->end_date >= $now || $this->end_date === null);
    }

    /**
     * Update standings table based on completed matches.
     */
    public function updateStandings()
    {
        // Reset all standings for this league
        Standing::where('league_id', $this->id)->update([
            'played' => 0,
            'won' => 0,
            'drawn' => 0,
            'lost' => 0,
            'points' => 0,
            'goals_for' => 0,
            'goals_against' => 0,
            'goal_difference' => 0,
        ]);

        // Get all completed, forfeited matches and cancelled matches with scores
        $completedMatches = LeagueMatch::where('league_id', $this->id)
            ->where(function($query) {
                $query->whereIn('status', ['completed', 'forfeited'])
                      ->orWhere(function($q) {
                          $q->where('status', 'cancelled')
                            ->where(function($sq) {
                                $sq->where('home_score', '>', 0)
                                   ->orWhere('away_score', '>', 0);
                            });
                      });
            })
            ->get();

        foreach ($completedMatches as $match) {
            $homeParticipantId = $this->is_team_based ? $match->home_team_id : $match->home_player_id;
            $awayParticipantId = $this->is_team_based ? $match->away_team_id : $match->away_player_id;

            $homeStanding = Standing::where('league_id', $this->id)
                ->where($this->is_team_based ? 'team_id' : 'player_id', $homeParticipantId)
                ->first();

            $awayStanding = Standing::where('league_id', $this->id)
                ->where($this->is_team_based ? 'team_id' : 'player_id', $awayParticipantId)
                ->first();

            if ($homeStanding && $awayStanding) {
                // Update played games
                $homeStanding->increment('played');
                $awayStanding->increment('played');

                // Handle forfeited matches
                if ($match->status === 'forfeited') {
                    if ($match->forfeited_by === 'home') {
                        // Away wins by forfeit
                        $awayStanding->increment('won');
                        $awayStanding->increment('points', $this->settings['points_win'] ?? 3);
                        $homeStanding->increment('lost');
                        $homeStanding->increment('points', $this->settings['points_loss'] ?? 1);
                    } elseif ($match->forfeited_by === 'away') {
                        // Home wins by forfeit
                        $homeStanding->increment('won');
                        $homeStanding->increment('points', $this->settings['points_win'] ?? 3);
                        $awayStanding->increment('lost');
                        $awayStanding->increment('points', $this->settings['points_loss'] ?? 1);
                    }
                } else {
                    // Regular completed match
                    $homeStanding->increment('goals_for', $match->home_score);
                    $homeStanding->increment('goals_against', $match->away_score);
                    $awayStanding->increment('goals_for', $match->away_score);
                    $awayStanding->increment('goals_against', $match->home_score);

                    if ($match->home_score > $match->away_score) {
                        $homeStanding->increment('won');
                        $awayStanding->increment('lost');
                        $homeStanding->increment('points', $this->settings['points_win'] ?? 3);
                        $awayStanding->increment('points', $this->settings['points_loss'] ?? 1);
                    } elseif ($match->away_score > $match->home_score) {
                        $awayStanding->increment('won');
                        $homeStanding->increment('lost');
                        $awayStanding->increment('points', $this->settings['points_win'] ?? 3);
                        $homeStanding->increment('points', $this->settings['points_loss'] ?? 1);
                    } else {
                        $homeStanding->increment('drawn');
                        $awayStanding->increment('drawn');
                        $homeStanding->increment('points', $this->settings['points_draw'] ?? 0);
                        $awayStanding->increment('points', $this->settings['points_draw'] ?? 0);
                    }
                }

                // Update goal difference
                $homeStanding->goal_difference = $homeStanding->goals_for - $homeStanding->goals_against;
                $awayStanding->goal_difference = $awayStanding->goals_for - $awayStanding->goals_against;

                $homeStanding->save();
                $awayStanding->save();
            }
        }

        // Update positions based on points, goal difference, goals for
        $standings = Standing::where('league_id', $this->id)
            ->orderBy('points', 'desc')
            ->orderBy('goal_difference', 'desc')
            ->orderBy('goals_for', 'desc')
            ->get();

        $position = 1;
        foreach ($standings as $standing) {
            $standing->update(['position' => $position++]);
        }
    }
}
