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
        // Match settings
        'sets_to_win',
        'points_per_set',
        'deuce_at',
        'must_win_by_two',
        'points_for_win',
        'points_for_draw',
        'points_for_loss',
        'has_tiebreak',
        'tiebreak_points',
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
        // Match settings casts
        'sets_to_win' => 'integer',
        'points_per_set' => 'integer',
        'deuce_at' => 'integer',
        'must_win_by_two' => 'boolean',
        'points_for_win' => 'integer',
        'points_for_draw' => 'integer',
        'points_for_loss' => 'integer',
        'has_tiebreak' => 'boolean',
        'tiebreak_points' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // No global scope - competitions can be both leagues and tournaments
    }

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
        return $this->hasMany(Team::class, 'competition_id');
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
        return $this->hasMany(CompetitionMatch::class, 'competition_id');
    }

    /**
     * Get the standings for this competition.
     */
    public function standings(): HasMany
    {
        return $this->hasMany(Standing::class, 'competition_id')->orderBy('position');
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
     * Check if this is a knockout tournament.
     */
    public function isKnockout(): bool
    {
        return $this->type === 'knockout';
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

            $group = TournamentGroup::create([
                'competition_id' => $this->id,
                'name' => chr(65 + $i), // A, B, C, etc.
                'group_number' => $i + 1,
                'player_ids' => $groupPlayers,
                'standings' => $this->initializeGroupStandings($groupPlayers),
            ]);

            // Create Standing records for each player in the group
            foreach ($groupPlayers as $playerId) {
                Standing::create([
                    'competition_id' => $this->id,
                    'tournament_group_id' => $group->id,
                    'player_id' => $playerId,
                    'played' => 0,
                    'won' => 0,
                    'drawn' => 0,
                    'lost' => 0,
                    'points' => 0,
                    'sets_won' => 0,
                    'sets_lost' => 0,
                    'points_won' => 0,
                    'points_lost' => 0,
                    'goals_for' => 0,
                    'goals_against' => 0,
                    'goal_difference' => 0,
                ]);
            }

            $groups[] = $group;
        }

        return $groups;
    }

    /**
     * Initialize standings for a group.
     */
    public function initializeGroupStandings(array $playerIds): array
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
     * Generate matches for all tournament groups.
     */
    public function generateGroupMatches()
    {
        if (!$this->isTournament()) {
            return;
        }

        $groups = $this->tournamentGroups;

        foreach ($groups as $group) {
            $playerIds = $group->player_ids;
            
            // Generate round-robin matches for this group
            $matches = [];
            $playerCount = count($playerIds);
            
            for ($i = 0; $i < $playerCount; $i++) {
                for ($j = $i + 1; $j < $playerCount; $j++) {
                    $matches[] = CompetitionMatch::create([
                        'competition_id' => $this->id,
                        'tournament_group_id' => $group->id,
                        'home_player_id' => $playerIds[$i],
                        'away_player_id' => $playerIds[$j],
                        'status' => 'scheduled',
                        'scheduled_at' => now(),
                    ]);
                }
            }
        }

        return true;
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
     * Generate knockout bracket for this competition.
     */
    public function generateKnockoutBracket()
    {
        $playerIds = $this->players()->pluck('id')->toArray();
        $this->generateKnockoutBracketFromPlayers($playerIds);
    }

    /**
     * Generate knockout bracket from advancing players.
     */
    private function generateKnockoutBracketFromPlayers(array $playerIds)
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

        // Generate actual matches from bracket
        $this->generateMatchesFromBracket($bracket, 1);
    }

    /**
     * Generate matches from bracket structure.
     */
    private function generateMatchesFromBracket(array $bracket, int $roundNumber)
    {
        if (isset($bracket['matches'])) {
            // This is a round with matches
            foreach ($bracket['matches'] as $matchData) {
                if (!$matchData['is_bye']) {
                    CompetitionMatch::create([
                        'competition_id' => $this->id,
                        'home_player_id' => $matchData['player1_id'],
                        'away_player_id' => $matchData['player2_id'],
                        'phase' => 'knockout',
                        'round_number' => $roundNumber,
                        'status' => 'scheduled',
                        'scheduled_at' => now(),
                    ]);
                }
            }
        } else {
            // This is a bracket with sub-brackets
            if (isset($bracket['left'])) {
                $this->generateMatchesFromBracket($bracket['left'], $roundNumber);
            }
            if (isset($bracket['right'])) {
                $this->generateMatchesFromBracket($bracket['right'], $roundNumber);
            }
        }
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

    /**
     * Generate matches for league competitions.
     */
    public function generateLeagueMatches()
    {
        if (!$this->isLeague()) {
            return;
        }

        $participants = $this->is_team_based ? $this->teams : $this->players;
        $participantIds = $participants->pluck('id')->toArray();

        if (count($participantIds) < 2) {
            return; // Not enough participants
        }

        // Generate round-robin matches (each participant plays every other participant once)
        $round = 1;
        for ($i = 0; $i < count($participantIds); $i++) {
            for ($j = $i + 1; $j < count($participantIds); $j++) {
                CompetitionMatch::create([
                    'competition_id' => $this->id,
                    'home_team_id' => $this->is_team_based ? $participantIds[$i] : null,
                    'away_team_id' => $this->is_team_based ? $participantIds[$j] : null,
                    'home_player_id' => !$this->is_team_based ? $participantIds[$i] : null,
                    'away_player_id' => !$this->is_team_based ? $participantIds[$j] : null,
                    'round' => $round,
                    'status' => 'scheduled',
                    'scheduled_at' => $this->start_date?->addDays($round - 1),
                ]);
                $round++;
            }
        }
    }

    /**
     * Generate standings table for league competitions.
     */
    public function generateLeagueStandings()
    {
        if (!$this->isLeague()) {
            return;
        }

        $participants = $this->is_team_based ? $this->teams : $this->players;

        $position = 1;
        foreach ($participants as $participant) {
            Standing::create([
                'competition_id' => $this->id,
                'team_id' => $this->is_team_based ? $participant->id : null,
                'player_id' => !$this->is_team_based ? $participant->id : null,
                'position' => $position++,
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'points' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_difference' => 0,
            ]);
        }
    }

    /**
     * Get the next power of two for bracket size.
     */
    private function getNextPowerOfTwo(int $number): int
    {
        return (int) pow(2, ceil(log($number, 2)));
    }

    /**
     * Generate bracket rounds recursively.
     */
    private function generateBracketRounds(array $players, int $roundNumber): array
    {
        $totalPlayers = count($players);

        if ($totalPlayers <= 2) {
            // Final match
            return [
                'round' => $roundNumber,
                'matches' => [
                    [
                        'player1_id' => $players[0],
                        'player2_id' => $players[1] ?? null,
                        'is_bye' => $players[1] === null,
                    ]
                ],
                'final_match' => null,
            ];
        }

        // Split players into two halves
        $half = $totalPlayers / 2;
        $leftPlayers = array_slice($players, 0, $half);
        $rightPlayers = array_slice($players, $half);

        $leftBracket = $this->generateBracketRounds($leftPlayers, $roundNumber);
        $rightBracket = $this->generateBracketRounds($rightPlayers, $roundNumber);

        return [
            'round' => $roundNumber,
            'left' => $leftBracket,
            'right' => $rightBracket,
            'final_match' => [
                'player1_id' => null, // Will be winner of left bracket
                'player2_id' => null, // Will be winner of right bracket
                'is_bye' => false,
            ],
        ];
    }
}
