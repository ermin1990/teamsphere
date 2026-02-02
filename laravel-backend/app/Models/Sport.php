<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'rules',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'rules' => 'array',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Scope a query to only include active sports.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Get a specific rule value.
     */
    public function getRule($key, $default = null)
    {
        return data_get($this->rules, $key, $default);
    }

    /**
     * Check if sport uses points-based scoring.
     */
    public function isPointsBased()
    {
        return $this->getRule('game_type') === 'points_based';
    }

    /**
     * Check if sport uses sets and games scoring.
     */
    public function isSetsGamesBased()
    {
        return $this->getRule('game_type') === 'sets_games';
    }

    /**
     * Check if sport uses time-based scoring.
     */
    public function isTimeBased()
    {
        return $this->getRule('game_type') === 'time_based';
    }

    /**
     * Get maximum points per game.
     */
    public function getMaxPointsPerGame()
    {
        return $this->getRule('max_points_per_game', 0);
    }

    /**
     * Get games needed to win match.
     */
    public function getGamesToWin()
    {
        return $this->getRule('games_to_win', 0);
    }

    /**
     * Get sets needed to win match.
     */
    public function getSetsToWin()
    {
        return $this->getRule('sets_to_win', 0);
    }

    /**
     * Get games per set.
     */
    public function getGamesPerSet()
    {
        return $this->getRule('games_per_set', 0);
    }

    /**
     * Get players per team.
     */
    public function getPlayersPerTeam()
    {
        return $this->getRule('players_per_team', 1);
    }

    /**
     * Get scoring information.
     */
    public function getScoringRules()
    {
        return $this->getRule('scoring', []);
    }
}
