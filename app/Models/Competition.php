<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Competition extends Model
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
        // Tournament fields
        'type',
        'max_participants',
        'group_count',
        'players_per_group',
        'players_advancing_per_group',
        'advancement_method',
        'current_phase',
        'knockout_bracket',
        'groups_completed_at',
        'knockout_completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_teams' => 'integer',
        'is_team_based' => 'boolean',
        'settings' => 'array',
        'is_active' => 'boolean',
        // Tournament casts
        'max_participants' => 'integer',
        'group_count' => 'integer',
        'players_per_group' => 'integer',
        'players_advancing_per_group' => 'integer',
        'knockout_bracket' => 'array',
        'groups_completed_at' => 'datetime',
        'knockout_completed_at' => 'datetime',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get the organization that owns the competition.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the sport for this competition.
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get the teams for this competition.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    /**
     * Get the players for this competition.
     */
    public function players(): BelongsToMany
    {
        return $this->belongsToMany(Player::class, 'competition_player', 'competition_id', 'player_id')
                    ->withPivot('joined_at')
                    ->withTimestamps()
                    ->withCasts([
                        'joined_at' => 'datetime'
                    ]);
    }

    /**
     * Get the matches for this competition.
     */
    public function matches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class);
    }

    /**
     * Get the standings for this competition.
     */
    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class)->orderBy('position');
    }

    /**
     * Get the tournament groups for this competition.
     */
    public function tournamentGroups(): HasMany
    {
        return $this->hasMany(TournamentGroup::class)->orderBy('group_number');
    }

    /**
     * Check if this is a tournament.
     */
    public function isTournament(): bool
    {
        return $this->type === 'tournament';
    }

    /**
     * Check if this is a league.
     */
    public function isLeague(): bool
    {
        return $this->type === 'league';
    }

    /**
     * Check if groups phase is completed.
     */
    public function isGroupsCompleted(): bool
    {
        return $this->current_phase === 'knockout' || $this->current_phase === 'completed';
    }

    /**
     * Check if knockout phase is completed.
     */
    public function isKnockoutCompleted(): bool
    {
        return $this->current_phase === 'completed';
    }

    /**
     * Get the number of players that should advance from groups.
     */
    public function getTotalAdvancingPlayers(): int
    {
        if (!$this->isTournament()) {
            return 0;
        }

        return $this->group_count * $this->players_advancing_per_group;
    }

    /**
     * Generate tournament groups based on registered players.
     */
    public function generateGroups()
    {
        if (!$this->isTournament()) {
            return;
        }

        $players = $this->players()->get();
        $playerIds = $players->pluck('id')->toArray();

        // Shuffle players for random group assignment
        shuffle($playerIds);

        $groups = [];
        $playersPerGroup = $this->players_per_group;

        for ($i = 0; $i < $this->group_count; $i++) {
            $groupPlayers = array_slice($playerIds, $i * $playersPerGroup, $playersPerGroup);

            $groups[] = TournamentGroup::create([
                'competition_id' => $this->id,
                'name' => chr(65 + $i), // A, B, C, etc.
                'group_number' => $i + 1,
                'player_ids' => $groupPlayers,
                'standings' => $this->initializeGroupStandings($groupPlayers),
            ]);
        }

        return $groups;
    }

    /**
     * Initialize standings for a group.
     */
    private function initializeGroupStandings(array $playerIds): array
    {
        $standings = [];
        foreach ($playerIds as $playerId) {
            $standings[] = [
                'player_id' => $playerId,
                'played' => 0,
                'won' => 0,
                'lost' => 0,
                'points' => 0,
                'sets_won' => 0,
                'sets_lost' => 0,
                'points_scored' => 0,
                'points_conceded' => 0,
            ];
        }
        return $standings;
    }

    /**
     * Advance players from groups to knockout phase.
     */
    public function advanceFromGroups()
    {
        if (!$this->isTournament() || $this->current_phase !== 'groups') {
            return;
        }

        $advancingPlayers = [];

        foreach ($this->tournamentGroups as $group) {
            $groupStandings = collect($group->standings)->sortByDesc(function ($player) {
                // Sort by points, then by sets won, then by points scored
                return [$player['points'], $player['sets_won'], $player['points_scored']];
            })->take($this->players_advancing_per_group);

            $advancingPlayers = array_merge($advancingPlayers, $groupStandings->pluck('player_id')->toArray());
        }

        // Generate knockout bracket
        $this->generateKnockoutBracket($advancingPlayers);

        $this->update([
            'current_phase' => 'knockout',
            'groups_completed_at' => now(),
        ]);
    }

    /**
     * Generate knockout bracket from advancing players.
     */
    private function generateKnockoutBracket(array $playerIds)
    {
        // Handle odd number of players by adding byes
        $totalPlayers = count($playerIds);
        $bracketSize = $this->getNextPowerOfTwo($totalPlayers);

        // Add byes if necessary
        $byesNeeded = $bracketSize - $totalPlayers;
        for ($i = 0; $i < $byesNeeded; $i++) {
            $playerIds[] = null; // null represents a bye
        }

        // Shuffle players for bracket
        shuffle($playerIds);

        $bracket = $this->generateBracketRounds($playerIds, 1);

        $this->update(['knockout_bracket' => $bracket]);
    }

    /**
     * Get the next power of 2 for bracket size.
     */
    private function getNextPowerOfTwo(int $number): int
    {
        if ($number <= 1) return 1;
        $power = 1;
        while ($power < $number) {
            $power *= 2;
        }
        return $power;
    }

    /**
     * Generate bracket rounds recursively.
     */
    private function generateBracketRounds(array $players, int $roundNumber): array
    {
        if (count($players) <= 2) {
            return [
                'round' => $roundNumber,
                'matches' => [
                    [
                        'player1_id' => $players[0],
                        'player2_id' => $players[1] ?? null,
                        'winner_id' => null,
                        'is_bye' => $players[1] === null,
                    ]
                ]
            ];
        }

        $mid = count($players) / 2;
        $leftBracket = $this->generateBracketRounds(array_slice($players, 0, $mid), $roundNumber);
        $rightBracket = $this->generateBracketRounds(array_slice($players, $mid), $roundNumber);

        return [
            'round' => $roundNumber,
            'left' => $leftBracket,
            'right' => $rightBracket,
            'final_match' => null, // Will be set when this round's winner is determined
        ];
    }

    /**
     * Complete the tournament.
     */
    public function completeTournament()
    {
        $this->update([
            'current_phase' => 'completed',
            'knockout_completed_at' => now(),
            'status' => 'completed',
        ]);
    }
}
