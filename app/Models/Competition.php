<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use App\Services\TournamentGroupService;
use App\Services\KnockoutBracketService;

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
        'is_public',
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
        'manual_knockout_selection',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_teams' => 'integer',
        'is_team_based' => 'boolean',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
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
        'manual_knockout_selection' => 'boolean',
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
     * Get the league matches for this competition.
     */
    public function leagueMatches(): HasMany
    {
        return $this->hasMany(LeagueMatch::class, 'competition_id');
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
     * Check if this competition is public.
     */
    public function isPublic(): bool
    {
        return $this->is_public;
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
    private function initializeGroupStandings(array $playerIds): array
    {
        $standings = [];
        
        foreach ($playerIds as $playerId) {
            $standings[] = [
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
            ];
        }
        
        return $standings;
    }


        /**
     * Generate tournament groups for this competition.
     */
    public function generateTournamentGroups(): array
    {
        $groupService = app(TournamentGroupService::class);
        return $groupService->generateGroups($this);
    }

    /**
     * Generate matches for all tournament groups using Berger system.
     */
    public function generateGroupMatches()
    {
        if (!$this->isTournament()) {
            return;
        }

        $bracketService = app(\App\Services\TournamentBracketService::class);

        foreach ($this->tournamentGroups as $group) {
            $bracketService->generateGroupMatches($this, $group);
        }
    }

    /**
     * Advance players from groups to knockout phase.
     */
    public function advanceFromGroups()
    {
        if (!$this->isTournament() || $this->current_phase !== 'groups') {
            return;
        }

        // Only generate knockout bracket automatically if manual selection is disabled
        if (!$this->manual_knockout_selection) {
            $this->generateKnockoutBracket();
        }

        $this->update([
            'current_phase' => 'knockout',
            'groups_completed_at' => now(),
        ]);
    }

    /**
     * Generate knockout bracket for this competition using World Cup-style seeding.
     * 
     * Rules:
     * - Groups alternate (A vs B, B vs A, C vs D, D vs C, etc.)
     * - Group winners are seeded on opposite sides
     * - Players from the same group can't meet until finals
     */
    public function generateKnockoutBracket()
    {
        if (!$this->isTournament()) {
            throw new \Exception('This is not a tournament.');
        }

        // Delete any existing knockout matches
        CompetitionMatch::where('competition_id', $this->id)
            ->where('phase', 'knockout')
            ->delete();

        $groups = $this->tournamentGroups()
            ->with(['standings' => function($q) {
                $q->orderBy('points', 'desc')
                    ->orderByRaw('(sets_won - sets_lost) desc')
                    ->orderByRaw('(points_won - points_lost) desc');
            }])
            ->orderBy('group_number')
            ->get();

        if ($groups->isEmpty()) {
            throw new \Exception('No tournament groups found.');
        }

        $qualifiedPlayers = [];
        
        // Extract winners and runners-up from each group
        foreach ($groups as $group) {
            $standings = $group->standings;
            if ($standings->count() >= 1) {
                $qualifiedPlayers[$group->id][] = [
                    'player_id' => $standings[0]->player_id,
                    'position' => 1, // Winner
                    'group_id' => $group->id,
                ];
            }
            if ($standings->count() >= 2) {
                $qualifiedPlayers[$group->id][] = [
                    'player_id' => $standings[1]->player_id,
                    'position' => 2, // Runner-up
                    'group_id' => $group->id,
                ];
            }
        }

        // Create knockout matches with World Cup seeding
        $this->createWorldCupKnockoutMatches($qualifiedPlayers);

        $this->update([
            'current_phase' => 'knockout',
            'knockout_bracket' => true,
            'knockout_started_at' => now(),
        ]);
    }

    /**
     * Create knockout matches following World Cup seeding rules.
     */
    private function createWorldCupKnockoutMatches($qualifiedPlayers)
    {
        $groupCount = count($qualifiedPlayers);
        
        $winners = [];
        $runnerUps = [];
        
        foreach ($qualifiedPlayers as $groupData) {
            foreach ($groupData as $player) {
                if ($player['position'] == 1) {
                    $winners[] = $player;
                } else {
                    $runnerUps[] = $player;
                }
            }
        }

        // Pair winners and runners-up with World Cup-style alternation
        // A1 vs B2, B1 vs A2, C1 vs D2, D1 vs C2, etc.
        $round = 1;
        $matchOrder = 1;
        
        for ($i = 0; $i < count($winners); $i++) {
            $homePlayer = $winners[$i] ?? null;
            $awayPlayer = $runnerUps[$i] ?? null;

            if ($homePlayer && $awayPlayer) {
                CompetitionMatch::create([
                    'competition_id' => $this->id,
                    'home_player_id' => $homePlayer['player_id'],
                    'away_player_id' => $awayPlayer['player_id'],
                    'phase' => 'knockout',
                    'round_number' => $round,
                    'match_order' => $matchOrder,
                    'status' => 'scheduled',
                    'scheduled_at' => now(),
                ]);

                $matchOrder++;
                
                // Move to next round if we've created enough matches for this round
                $matchesPerRound = count($winners) / pow(2, $round - 1);
                if ($matchOrder > $matchesPerRound) {
                    $round++;
                    $matchOrder = 1;
                }
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

        // Use Berger schedule service to generate round-robin matches
        $bergerService = app(\App\Services\BergerScheduleService::class);
        $bergerService->generateLeagueMatches($this, $participantIds);
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
     * Generate knockout bracket with World Cup-style seeding.
     * 
     * Rules:
     * - Groups alternate (A vs B, B vs A, C vs D, D vs C, etc.)
     * - Group winners are seeded on opposite sides
     * - Players from the same group can't meet until finals
     */
}

