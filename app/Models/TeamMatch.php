<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Standing;

class TeamMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'competition_id',
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score',
        'status',
        'scheduled_at',
        'played_at',
        'round',
        'lineup',
    ];

    protected $casts = [
        'competition_id' => 'integer',
        'home_team_id' => 'integer',
        'away_team_id' => 'integer',
        'home_score' => 'integer',
        'away_score' => 'integer',
        'scheduled_at' => 'datetime',
        'played_at' => 'datetime',
        'round' => 'integer',
        'lineup' => 'array',
    ];

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function individualMatches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class, 'team_match_id')->orderBy('match_order');
    }

    /**
     * Check if the team match is completed based on BiH rules (first to 4 wins).
     */
    public function checkCompletion(): bool
    {
        // Force reload to get fresh data from DB
        $this->load('individualMatches');
        
        $homeWins = 0;
        $awayWins = 0;

        foreach ($this->individualMatches as $match) {
            if ($match->status === 'completed') {
                if ($match->home_score > $match->away_score) {
                    $homeWins++;
                } elseif ($match->away_score > $match->home_score) {
                    $awayWins++;
                }
            }
        }

        $this->update([
            'home_score' => $homeWins,
            'away_score' => $awayWins
        ]);

        if (($homeWins >= 4 || $awayWins >= 4) && $this->status !== 'completed') {
            $this->update([
                'status' => 'completed',
                'played_at' => now(),
            ]);
            $this->updateStandings();
            return true;
        }
        return false;
    }

    protected function updateStandings()
    {
        $homeStanding = Standing::firstOrCreate([
            'competition_id' => $this->competition_id,
            'team_id' => $this->home_team_id,
        ]);

        $awayStanding = Standing::firstOrCreate([
            'competition_id' => $this->competition_id,
            'team_id' => $this->away_team_id,
        ]);

        $homeStanding->increment('played');
        $awayStanding->increment('played');

        if ($this->home_score > $this->away_score) {
            $homeStanding->increment('won');
            $homeStanding->increment('points', 2);
            $awayStanding->increment('lost');
        } else {
            $awayStanding->increment('won');
            $awayStanding->increment('points', 2);
            $homeStanding->increment('lost');
        }
    }
}
