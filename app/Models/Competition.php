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
        'category_id',
        'season_id',
        'city_id',
        'registration_deadline',
        'status',
        'start_date',
        'end_date',
        'max_teams',
        'is_team_based',
        'is_double_round',
        'settings',
        'is_active',
        'is_public',
        'is_recreational',
        // Tournament fields
        'type',
        'max_participants',
        'group_count',
        'players_per_group',
        'players_advancing_per_group',
        'group_rounds',
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
        'organization_id' => 'integer',
        'sport_id' => 'integer',
        'category_id' => 'integer',
        'season_id' => 'integer',
        'city_id' => 'integer',
        'registration_deadline' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'max_teams' => 'integer',
        'is_team_based' => 'boolean',
        'is_double_round' => 'boolean',
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'is_recreational' => 'boolean',
        // Tournament casts
        'max_participants' => 'integer',
        'group_count' => 'integer',
        'players_per_group' => 'integer',
        'players_advancing_per_group' => 'integer',
        'group_rounds' => 'integer',
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
     * Get the category for this competition.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
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
                    ->orderBy('competition_player.joined_at')
                    ->withCasts([
                        'joined_at' => 'datetime'
                    ]);
    }

    /**
     * Generate round-robin team matches for the competition.
     */
    public function generateTeamMatches()
    {
        if (!$this->is_team_based) return;

        $teams = $this->teams->pluck('id')->toArray();
        if (count($teams) < 2) return;

        // Create initial standings for all teams
        foreach ($teams as $teamId) {
            Standing::firstOrCreate([
                'competition_id' => $this->id,
                'team_id' => $teamId,
            ], [
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'points' => 0,
            ]);
        }

        if (count($teams) % 2 != 0) {
            $teams[] = null; // Bye
        }

        $numTeams = count($teams);
        $numRounds = $numTeams - 1;
        $half = $numTeams / 2;

        // First half of the season
        for ($round = 1; $round <= $numRounds; $round++) {
            for ($i = 0; $i < $half; $i++) {
                $home = $teams[$i];
                $away = $teams[$numTeams - 1 - $i];

                if ($home !== null && $away !== null) {
                    $this->teamMatches()->create([
                        'home_team_id' => $home,
                        'away_team_id' => $away,
                        'round' => $round,
                        'status' => 'scheduled',
                    ]);
                }
            }

            // Rotate teams
            $last = array_pop($teams);
            array_splice($teams, 1, 0, [$last]);
        }

        // Second half of the season (if double round)
        if ($this->is_double_round) {
            // Reset teams array for rotation (or just use the current state but swap home/away)
            // Actually, it's better to just repeat the logic but swap home/away and increment round
            
            // We need to reset the teams array to its original state to ensure the same pairings
            $teams = $this->teams->pluck('id')->toArray();
            if (count($teams) % 2 != 0) {
                $teams[] = null;
            }
            
            for ($round = 1; $round <= $numRounds; $round++) {
                for ($i = 0; $i < $half; $i++) {
                    $home = $teams[$i];
                    $away = $teams[$numTeams - 1 - $i];

                    if ($home !== null && $away !== null) {
                        $this->teamMatches()->create([
                            'home_team_id' => $away, // Swapped
                            'away_team_id' => $home, // Swapped
                            'round' => $round + $numRounds, // Next half
                            'status' => 'scheduled',
                        ]);
                    }
                }

                // Rotate teams
                $last = array_pop($teams);
                array_splice($teams, 1, 0, [$last]);
            }
        }
    }

    /**
     * Get the matches for this competition.
     */
    public function matches(): HasMany
    {
        return $this->hasMany(CompetitionMatch::class, 'competition_id');
    }

    /**
     * Get the team matches for this competition.
     */
    public function teamMatches(): HasMany
    {
        return $this->hasMany(TeamMatch::class, 'competition_id');
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
        return $this->hasMany(Standing::class, 'competition_id')->orderBy('position', 'asc');
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

        // If players_per_group is null or 0, distribute players evenly across groups without limit
        if (!$playersPerGroup) {
            $groupCount = $this->group_count;
            for ($i = 0; $i < $groupCount; $i++) {
                $groups[] = [
                    'name' => chr(65 + $i),
                    'group_number' => $i + 1,
                    'player_ids' => [],
                    'standings' => [],
                ];
            }

            // Assign players to groups in alternating order to balance groups
            $playerIndex = 0;
            foreach ($players as $player) {
                $groupIndex = $playerIndex % $groupCount;
                $groups[$groupIndex]['player_ids'][] = $player->id;
                $playerIndex++;
            }
        } else {
            // Use fixed players per group
            for ($i = 0; $i < $this->group_count; $i++) {
                $groupPlayers = array_slice($playerIds, $i * $playersPerGroup, $playersPerGroup);

                $groups[] = [
                    'name' => chr(65 + $i),
                    'group_number' => $i + 1,
                    'player_ids' => $groupPlayers,
                    'standings' => $this->initializeGroupStandings($groupPlayers),
                ];
            }
        }

        // Create groups and standings
        foreach ($groups as $groupData) {
            $group = TournamentGroup::create([
                'competition_id' => $this->id,
                'name' => $groupData['name'],
                'group_number' => $groupData['group_number'],
                'player_ids' => $groupData['player_ids'],
                'standings' => $this->initializeGroupStandings($groupData['player_ids']),
            ]);

            // Create Standing records for each player in the group
            foreach ($groupData['player_ids'] as $playerId) {
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
     * Generate knockout bracket for this competition using JOOLA rules.
     * 
     * JOOLA Rules:
     * - Players are seeded based on group performance
     * - Number of knockout matches is calculated dynamically based on advancing players
     * - Matches are created to reach the final
     */
    public function generateKnockoutBracket()
    {
        if (!$this->isTournament()) {
            throw new \Exception('This is not a tournament.');
        }

        \Log::info('Starting generateKnockoutBracket', [
            'competition_id' => $this->id,
            'current_phase' => $this->current_phase,
            'groups_completed' => $this->isGroupsCompleted()
        ]);

        // Get tournament groups with standings
        $groups = $this->tournamentGroups()
            ->orderBy('group_number')
            ->get();

        \Log::info('Tournament groups loaded', [
            'groups_count' => $groups->count(),
            'groups' => $groups->map(fn($g) => ['id' => $g->id, 'name' => $g->name, 'is_completed' => $g->is_completed])->toArray()
        ]);

        if ($groups->isEmpty()) {
            throw new \Exception('No tournament groups found.');
        }

        // Check if all groups are completed
        $incompleteGroups = $groups->where('is_completed', false);
        if ($incompleteGroups->count() > 0) {
            throw new \Exception('Not all groups are completed. Cannot generate knockout bracket.');
        }

        // Use JOOLA-specific logic instead of generic qualified players
        $this->createJOOLAKnockoutMatches($groups);

        $this->update([
            'current_phase' => 'knockout',
            'knockout_bracket' => true,
            'knockout_started_at' => now(),
        ]);

        \Log::info('Knockout bracket generation completed', [
            'competition_id' => $this->id
        ]);
    }

    /**
     * Create knockout matches using JOOLA rules.
     *
     * JOOLA Rules (generalized for any number of groups):
     * - Winners of first 2 groups (by name) are fixed in positions 1-2
     * - Other group winners are drawn for remaining positions
     * - Runners-up are drawn with restrictions (avoid same group as winners in top positions)
     * - Pairings: 1 vs last, 2 vs second-to-last, 3 vs third-to-last, etc.
     *
     * @param \Illuminate\Database\Eloquent\Collection $groups Tournament groups with standings loaded
     */
    private function createJOOLAKnockoutMatches($groups)
    {
        $totalGroups = $groups->count();
        $playersPerGroup = $this->players_advancing_per_group ?? 2;
        $totalPlayers = $totalGroups * $playersPerGroup;
        
        \Log::info('Creating JOOLA knockout matches', [
            'competition_id' => $this->id,
            'total_groups' => $totalGroups,
            'players_per_group' => $playersPerGroup,
            'total_players' => $totalPlayers
        ]);
        
        // Validate all groups are completed
        foreach ($groups as $group) {
            if (!$group->is_completed) {
                throw new \Exception("Group {$group->name} is not completed yet. All groups must be completed before generating knockout stage.");
            }
        }
        
        // Force recalculation of all group standings
        \Log::info('Forcing recalculation of all group standings before knockout');
        $tournamentGroupService = app(\App\Services\TournamentGroupService::class);
        foreach ($groups as $group) {
            $tournamentGroupService->recalculateGroupStandings($this, $group);
        }
        
        // Sort groups by name for consistent ordering
        $sortedGroups = $groups->sortBy('name')->values();
        
        // Extract advancing players from each group
        $advancingPlayers = [];

        foreach ($sortedGroups as $group) {
            \Log::info('Processing group', [
                'group_id' => $group->id,
                'group_name' => $group->name,
                'standings_count' => $group->standings()->count()
            ]);
            
            // Use Standing models instead of standings array - respect manual_order if set
            $standings = $group->standings()
                ->orderByRaw('CASE WHEN manual_order IS NULL THEN 1 ELSE 0 END ASC, manual_order ASC')
                ->orderByDesc('points')
                ->orderByDesc(\DB::raw('sets_won - sets_lost'))
                ->orderByDesc(\DB::raw('points_won - points_lost'))
                ->orderByDesc('points_won')
                ->orderByDesc('sets_won')
                ->orderByDesc('won')
                ->orderBy('id')
                ->get()
                ->map(function ($standing) {
                    return [
                        'player_id' => $standing->player_id,
                        'points' => $standing->points,
                        'sets_won' => $standing->sets_won,
                        'sets_lost' => $standing->sets_lost,
                    ];
                });

            \Log::info('Group standings loaded', [
                'group_name' => $group->name,
                'standings_count' => $standings->count(),
                'players_per_group' => $playersPerGroup,
                'standings_data' => $standings->toArray()
            ]);

            if ($standings->count() < $playersPerGroup) {
                throw new \Exception("Group {$group->name} has only {$standings->count()} players but needs {$playersPerGroup} to advance.");
            }

            for ($i = 0; $i < min($playersPerGroup, count($standings)); $i++) {
                $playerId = $standings[$i]['player_id'];
                
                // Validate that player exists
                if (!$playerId || !\App\Models\Player::find($playerId)) {
                    \Log::error('Invalid player ID found in standings', [
                        'competition_id' => $this->id,
                        'group_name' => $group->name,
                        'player_id' => $playerId,
                        'standing_index' => $i
                    ]);
                    throw new \Exception("Invalid player ID {$playerId} found in group {$group->name} standings.");
                }
                
                $advancingPlayers[$group->name][] = [
                    'player_id' => $playerId,
                    'group' => $group->name,
                    'position' => $i + 1,
                    'points' => $standings[$i]['points'],
                    'sets_won' => $standings[$i]['sets_won'],
                    'sets_lost' => $standings[$i]['sets_lost']
                ];
                
                \Log::info('Player advancing from group', [
                    'competition_id' => $this->id,
                    'group_name' => $group->name,
                    'position' => $i + 1,
                    'player_id' => $playerId,
                    'points' => $standings[$i]['points'],
                    'set_diff' => $standings[$i]['sets_won'] - $standings[$i]['sets_lost']
                ]);
            }
        }

        \Log::info('Advancing players extracted', [
            'advancing_players' => $advancingPlayers
        ]);

        // Save original advancing players for later lookup (before modifying the array)
        $originalAdvancingPlayers = $advancingPlayers;

        // Separate winners (position 1) and other advancing players
        $winners = [];
        $otherAdvancing = [];

        foreach ($advancingPlayers as $groupName => $players) {
            if (!empty($players)) {
                $winners[$groupName] = array_shift($players); // First player is winner
                $otherAdvancing = array_merge($otherAdvancing, $players); // Rest are other advancing
            }
        }

        // Initialize bracket positions
        $bracketPositions = array_fill(1, $totalPlayers, null);

        // Fixed positions for first 2 groups' winners
        if (isset($winners[$sortedGroups[0]->name])) {
            $bracketPositions[1] = $winners[$sortedGroups[0]->name]['player_id'];
            unset($winners[$sortedGroups[0]->name]);
        }
        if ($totalGroups >= 2 && isset($winners[$sortedGroups[1]->name])) {
            $bracketPositions[2] = $winners[$sortedGroups[1]->name]['player_id'];
            unset($winners[$sortedGroups[1]->name]);
        }

        // Draw remaining winners for positions 3 to totalGroups
        $remainingWinners = array_values($winners);
        shuffle($remainingWinners);

        for ($i = 0; $i < count($remainingWinners) && $i < ($totalGroups - 2); $i++) {
            $bracketPositions[3 + $i] = $remainingWinners[$i]['player_id'];
        }

        // Draw other advancing players for remaining positions with restrictions
        // Avoid same group as winners in positions 1 to totalGroups where possible
        $usedGroups = [];
        for ($pos = 1; $pos <= $totalGroups; $pos++) {
            if ($bracketPositions[$pos]) {
                foreach ($sortedGroups as $group) {
                    $standings = $group->standings()
                        ->orderBy('position')
                        ->get()
                        ->map(function ($standing) {
                            return [
                                'player_id' => $standing->player_id,
                                'points' => $standing->points,
                                'sets_won' => $standing->sets_won,
                                'sets_lost' => $standing->sets_lost,
                            ];
                        });

                    if (count($standings) >= 1 && $standings[0]['player_id'] == $bracketPositions[$pos]) {
                        $usedGroups[] = $group->name;
                        break;
                    }
                }
            }
        }

        // Filter other advancing players to prefer avoiding same group conflicts
        $availableOthers = [];
        $conflictedOthers = [];
        foreach ($otherAdvancing as $player) {
            if (!in_array($player['group'], $usedGroups)) {
                $availableOthers[] = $player;
            } else {
                $conflictedOthers[] = $player;
            }
        }

        // Combine available and conflicted if needed
        $allAvailableOthers = array_merge($availableOthers, $conflictedOthers);
        shuffle($allAvailableOthers);

        for ($i = 0; $i < count($allAvailableOthers) && ($totalGroups + 1 + $i) <= $totalPlayers; $i++) {
            $bracketPositions[$totalGroups + 1 + $i] = $allAvailableOthers[$i]['player_id'];
        }

        // Create matches: pair position 1 vs last, 2 vs second-to-last, 3 vs third-to-last, etc.
        $matches = [];
        for ($i = 1; $i <= floor($totalPlayers / 2); $i++) {
            $homePos = $i;
            $awayPos = $totalPlayers - $i + 1;
            
            if ($homePos < $awayPos) {
                $matches[] = [
                    'home' => $homePos,
                    'away' => $awayPos,
                ];
            }
        }

        $matchOrder = 1;
        foreach ($matches as $pairing) {
            $homePlayerId = $bracketPositions[$pairing['home']] ?? null;
            $awayPlayerId = $bracketPositions[$pairing['away']] ?? null;

            // Find group and position info for home player from original list
            $homeGroup = null;
            $homePosition = null;
            if ($homePlayerId) {
                foreach ($originalAdvancingPlayers as $groupName => $players) {
                    foreach ($players as $player) {
                        if ($player['player_id'] == $homePlayerId) {
                            $homeGroup = $groupName;
                            $homePosition = $player['position'];
                            break 2;
                        }
                    }
                }
            }

            // Find group and position info for away player from original list
            $awayGroup = null;
            $awayPosition = null;
            if ($awayPlayerId) {
                foreach ($originalAdvancingPlayers as $groupName => $players) {
                    foreach ($players as $player) {
                        if ($player['player_id'] == $awayPlayerId) {
                            $awayGroup = $groupName;
                            $awayPosition = $player['position'];
                            break 2;
                        }
                    }
                }
            }

            $isBye = ($homePlayerId && !$awayPlayerId) || (!$homePlayerId && $awayPlayerId);

            CompetitionMatch::create([
                'competition_id' => $this->id,
                'phase' => 'knockout',
                'round_number' => 1,
                'match_order' => $matchOrder++,
                'home_player_id' => $homePlayerId,
                'away_player_id' => $awayPlayerId,
                'status' => $isBye ? 'completed' : 'pending',
                'is_bye' => $isBye,
                'scheduled_at' => now(),
                'home_player_group' => $homeGroup,
                'home_player_position' => $homePosition,
                'away_player_group' => $awayGroup,
                'away_player_position' => $awayPosition,
            ]);
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

    /**
     * Advance to next knockout round by creating matches from winners.
     */
    public function advanceKnockoutRound($currentRound)
    {
        // Get all completed matches from the current round
        $completedMatches = CompetitionMatch::where('competition_id', $this->id)
            ->where('phase', 'knockout')
            ->where('round_number', $currentRound)
            ->where('status', 'completed')
            ->orderBy('match_order')
            ->get();

        if ($completedMatches->isEmpty()) {
            throw new \Exception('No completed matches found in round ' . $currentRound);
        }

        // Check if all matches in current round are completed
        $totalMatches = CompetitionMatch::where('competition_id', $this->id)
            ->where('phase', 'knockout')
            ->where('round_number', $currentRound)
            ->count();

        if ($completedMatches->count() !== $totalMatches) {
            throw new \Exception('Not all matches in round ' . $currentRound . ' are completed yet');
        }

        // Get winners from each match with their group info, sorted by match_order
        $winners = [];
        foreach ($completedMatches->sortBy('match_order') as $match) {
            $winner = $match->getWinner();
            if ($winner) {
                // Determine the group of the winner
                $winnerGroup = null;
                if ($match->home_player_id == $winner->id) {
                    $winnerGroup = $match->home_player_group;
                } elseif ($match->away_player_id == $winner->id) {
                    $winnerGroup = $match->away_player_group;
                }
                
                $winners[] = [
                    'player_id' => $winner->id,
                    'group' => $winnerGroup
                ];
            }
        }

        if (empty($winners)) {
            throw new \Exception('No winners found');
        }

        // If only 1 winner, tournament is completed
        if (count($winners) === 1) {
            $this->completeTournament();
            return;
        }

        // Pair winners: 1st vs 2nd, 3rd vs 4th, etc.
        $pairedWinners = [];
        $winnersCount = count($winners);
        
        for ($i = 0; $i < $winnersCount; $i += 2) {
            if ($i + 1 < $winnersCount) {
                $pairedWinners[] = [
                    'home' => $winners[$i]['player_id'],
                    'home_group' => $winners[$i]['group'],
                    'away' => $winners[$i + 1]['player_id'],
                    'away_group' => $winners[$i + 1]['group']
                ];
            }
        }

        // Create next round matches with group info preserved
        $nextRound = $currentRound + 1;
        $matchOrder = 1;

        foreach ($pairedWinners as $pair) {
            CompetitionMatch::create([
                'competition_id' => $this->id,
                'home_player_id' => $pair['home'],
                'away_player_id' => $pair['away'],
                'phase' => 'knockout',
                'round_number' => $nextRound,
                'match_order' => $matchOrder++,
                'status' => 'scheduled',
                'is_bye' => false,
                'scheduled_at' => now(),
                'home_player_group' => $pair['home_group'],
                'away_player_group' => $pair['away_group'],
            ]);
        }
    }

    /**
     * Regenerate a specific knockout round.
     */
    public function regenerateKnockoutRound(int $roundNumber)
    {
        if (!$this->isTournament()) {
            throw new \Exception('This is not a tournament.');
        }

        // Get winners from the previous round
        $previousRound = $roundNumber - 1;
        if ($previousRound < 1) {
            throw new \Exception('Cannot regenerate the first round.');
        }

        $winners = $this->getRoundWinners($previousRound);

        if (empty($winners)) {
            throw new \Exception('No winners found from previous round.');
        }

        // Delete existing matches in this round
        CompetitionMatch::where('competition_id', $this->id)
            ->where('phase', 'knockout')
            ->where('round_number', $roundNumber)
            ->delete();

        // Create new matches for this round
        $pairedWinners = [];
        $winnersCount = count($winners);

        for ($i = 0; $i < $winnersCount; $i += 2) {
            if ($i + 1 < $winnersCount) {
                $pairedWinners[] = [
                    'home' => $winners[$i],
                    'away' => $winners[$i + 1],
                ];
            }
        }

        $matchOrder = 1;
        foreach ($pairedWinners as $pair) {
            CompetitionMatch::create([
                'competition_id' => $this->id,
                'home_player_id' => $pair['home'],
                'away_player_id' => $pair['away'],
                'phase' => 'knockout',
                'round_number' => $roundNumber,
                'match_order' => $matchOrder++,
                'status' => 'scheduled',
                'is_bye' => false,
                'scheduled_at' => now(),
            ]);
        }

        // Delete all subsequent rounds as they are now invalid
        CompetitionMatch::where('competition_id', $this->id)
            ->where('phase', 'knockout')
            ->where('round_number', '>', $roundNumber)
            ->delete();
    }

    /**
     * Propagate winner changes through knockout bracket after match update.
     */
    public function propagateWinnerChanges(CompetitionMatch $updatedMatch)
    {
        if ($updatedMatch->phase !== 'knockout') {
            return;
        }

        $currentRound = $updatedMatch->round_number;

        // Find all matches that depend on this match's winner
        $dependentMatches = CompetitionMatch::where('competition_id', $this->id)
            ->where('phase', 'knockout')
            ->where('round_number', '>', $currentRound)
            ->get();

        if ($dependentMatches->isEmpty()) {
            return; // No dependent matches
        }

        // Get winners from current round
        $currentRoundWinners = $this->getRoundWinners($currentRound);

        // Update next round matches based on new winners
        $nextRound = $currentRound + 1;
        $nextRoundMatches = $dependentMatches->where('round_number', $nextRound);

        if ($nextRoundMatches->isNotEmpty()) {
            $this->updateNextRoundMatches($nextRoundMatches, $currentRoundWinners);
        }

        // Continue propagating to subsequent rounds if needed
        $maxRound = $dependentMatches->max('round_number');
        for ($round = $nextRound + 1; $round <= $maxRound; $round++) {
            $roundMatches = $dependentMatches->where('round_number', $round);
            if ($roundMatches->isNotEmpty()) {
                $prevRoundWinners = $this->getRoundWinners($round - 1);
                $this->updateNextRoundMatches($roundMatches, $prevRoundWinners);
            }
        }
    }

    /**
     * Get winners from a specific round.
     */
    private function getRoundWinners(int $round): array
    {
        $matches = CompetitionMatch::where('competition_id', $this->id)
            ->where('phase', 'knockout')
            ->where('round_number', $round)
            ->where('status', 'completed')
            ->orderBy('match_order')
            ->get();

        $winners = [];
        foreach ($matches as $match) {
            $winner = $match->getWinner();
            if ($winner) {
                $winners[] = $winner->id;
            }
        }

        return $winners;
    }

    /**
     * Update next round matches with new winners.
     */
    private function updateNextRoundMatches($nextRoundMatches, array $winners): void
    {
        // Sort winners by their match order to maintain pairing
        $sortedWinners = collect($winners)->values();

        // Pair winners: 1st vs 2nd, 3rd vs 4th, etc.
        $pairedWinners = [];
        $winnersCount = count($sortedWinners);

        for ($i = 0; $i < $winnersCount; $i += 2) {
            if ($i + 1 < $winnersCount) {
                $pairedWinners[] = [
                    'home' => $sortedWinners[$i],
                    'away' => $sortedWinners[$i + 1],
                ];
            }
        }

        // Update matches with new pairings
        foreach ($nextRoundMatches->sortBy('match_order') as $index => $match) {
            if (isset($pairedWinners[$index])) {
                $match->update([
                    'home_player_id' => $pairedWinners[$index]['home'],
                    'away_player_id' => $pairedWinners[$index]['away'],
                ]);
            }
        }
    }
}


