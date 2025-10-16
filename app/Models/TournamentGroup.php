<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TournamentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'name',
        'group_number',
        'player_ids',
        'standings',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'player_ids' => 'array',
        'standings' => 'array',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the competition that owns this group.
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the matches for this group.
     */
    public function matches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class)->where('phase', 'group');
    }

    /**
     * Get players in this group.
     */
    public function getPlayers()
    {
        return Player::whereIn('id', $this->player_ids ?? [])->get();
    }

    /**
     * Update standings after a match.
     */
    public function updateStandings(CompetitionMatch $match)
    {
        $standings = $this->standings ?? [];

        // Find players in standings
        $homePlayerIndex = collect($standings)->search(function ($standing) use ($match) {
            return $match->home_player_id && $standing['player_id'] == $match->home_player_id;
        });

        $awayPlayerIndex = collect($standings)->search(function ($standing) use ($match) {
            return $match->away_player_id && $standing['player_id'] == $match->away_player_id;
        });

        if ($homePlayerIndex !== false) {
            $standings[$homePlayerIndex]['played']++;
            $standings[$homePlayerIndex]['sets_won'] += $match->home_score ?? 0;
            $standings[$homePlayerIndex]['sets_lost'] += $match->away_score ?? 0;

            if (($match->home_score ?? 0) > ($match->away_score ?? 0)) {
                $standings[$homePlayerIndex]['won']++;
                $standings[$homePlayerIndex]['points'] += 3; // Win = 3 points
            } elseif (($match->home_score ?? 0) < ($match->away_score ?? 0)) {
                $standings[$homePlayerIndex]['lost']++;
                $standings[$homePlayerIndex]['points'] += 0; // Loss = 0 points
            } else {
                $standings[$homePlayerIndex]['points'] += 1; // Draw = 1 point
            }
        }

        if ($awayPlayerIndex !== false) {
            $standings[$awayPlayerIndex]['played']++;
            $standings[$awayPlayerIndex]['sets_won'] += $match->away_score ?? 0;
            $standings[$awayPlayerIndex]['sets_lost'] += $match->home_score ?? 0;

            if (($match->away_score ?? 0) > ($match->home_score ?? 0)) {
                $standings[$awayPlayerIndex]['won']++;
                $standings[$awayPlayerIndex]['points'] += 3;
            } elseif (($match->away_score ?? 0) < ($match->home_score ?? 0)) {
                $standings[$awayPlayerIndex]['lost']++;
                $standings[$awayPlayerIndex]['points'] += 0;
            } else {
                $standings[$awayPlayerIndex]['points'] += 1;
            }
        }

        $this->update(['standings' => $standings]);

        // Check if group is completed
        $this->checkGroupCompletion();
    }

    /**
     * Check if all matches in the group are completed.
     */
    private function checkGroupCompletion()
    {
        $totalPlayers = count($this->player_ids ?? []);
        $requiredMatches = ($totalPlayers * ($totalPlayers - 1)) / 2; // Round-robin formula

        $completedMatches = $this->matches()->where('status', 'completed')->count();

        if ($completedMatches >= $requiredMatches) {
            $this->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Get sorted standings for this group.
     */
    public function getSortedStandings()
    {
        return collect($this->standings ?? [])->sortByDesc(function ($player) {
            return [
                $player['points'] ?? 0,
                ($player['sets_won'] ?? 0) - ($player['sets_lost'] ?? 0),
                $player['sets_won'] ?? 0,
            ];
        })->values();
    }

    /**
     * Get advancing players from this group.
     */
    public function getAdvancingPlayers(int $count)
    {
        return $this->getSortedStandings()->take($count)->pluck('player_id');
    }
}
