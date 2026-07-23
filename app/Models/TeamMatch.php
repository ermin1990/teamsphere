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
        'home_captain_id',
        'away_captain_id',
        'referee_name',
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

    public function homeCaptain(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'home_captain_id');
    }

    public function awayCaptain(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'away_captain_id');
    }

    public function individualMatches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class, 'team_match_id')->orderBy('match_order');
    }

    /**
     * Whether this tie is a single match (e.g. Padel - a "team" is just a
     * pair playing one match together) rather than a multi-game tie (e.g.
     * table tennis's BiH Corbillon system).
     */
    public function usesSingleMatchTie(): bool
    {
        return $this->competition?->sport?->usesSingleMatchTies() ?? false;
    }

    /**
     * Create the one individual CompetitionMatch this tie needs when the
     * sport uses single-match ties - the team IDs stand in for the match
     * players (no lineup/protocol step needed since the team already is
     * the pair). Safe to call multiple times: does nothing once it exists.
     */
    public function ensureSingleMatchGame(): void
    {
        if (!$this->usesSingleMatchTie() || $this->individualMatches()->exists()) {
            return;
        }

        $competition = $this->competition;

        $competition->matches()->create([
            'team_match_id' => $this->id,
            'home_team_id' => $this->home_team_id,
            'away_team_id' => $this->away_team_id,
            'position_code' => 'Meč',
            'match_order' => 1,
            'round_number' => 1,
            'status' => 'scheduled',
            'sets_to_win' => $competition->sets_to_win ?? $competition->sport?->getSetsToWin() ?: 2,
            'points_per_set' => $competition->points_per_set ?? 0,
        ]);
    }

    /**
     * Check if the team match is completed. For single-match ties (Padel),
     * this simply mirrors the one individual match's result - no BiH-style
     * "first to 4 wins" threshold applies since there is only one game.
     */
    public function checkCompletion(): bool
    {
        // Force reload to get fresh data from DB
        $this->load('individualMatches');

        if ($this->usesSingleMatchTie()) {
            $match = $this->individualMatches->first();
            if (!$match || $match->status !== 'completed' || $this->status === 'completed') {
                return false;
            }

            $this->update([
                'home_score' => $match->home_score,
                'away_score' => $match->away_score,
                'status' => 'completed',
                'played_at' => $match->played_at ?? now(),
            ]);
            $this->updateStandings();
            return true;
        }

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
