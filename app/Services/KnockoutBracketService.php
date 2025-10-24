<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionMatch;

class KnockoutBracketService
{
    /**
     * Generate knockout bracket for a competition.
     */
    public function generateBracket(Competition $competition, array $playerIds = null): array
    {
        $playerIds = $playerIds ?? $competition->players()->pluck('id')->toArray();

        $bracketData = $this->prepareBracketData($playerIds);
        $bracket = $this->buildBracketStructure($bracketData['players'], 1);

        return $bracket;
    }

    /**
     * Prepare bracket data by handling byes and shuffling.
     */
    private function prepareBracketData(array $playerIds): array
    {
        $totalPlayers = count($playerIds);
        $bracketSize = $this->getNextPowerOfTwo($totalPlayers);

        // Add byes if necessary
        $byesNeeded = $bracketSize - $totalPlayers;
        $byePlayers = array_fill(0, $byesNeeded, null);

        $allPlayers = array_merge($playerIds, $byePlayers);

        // Shuffle players for fair bracket
        shuffle($allPlayers);

        return [
            'players' => $allPlayers,
            'bracket_size' => $bracketSize,
            'byes_count' => $byesNeeded,
        ];
    }

    /**
     * Build bracket structure recursively.
     */
    private function buildBracketStructure(array $players, int $roundNumber): array
    {
        $totalPlayers = count($players);

        if ($totalPlayers <= 2) {
            return $this->createFinalRound($players, $roundNumber);
        }

        return $this->createIntermediateRound($players, $roundNumber);
    }

    /**
     * Create final round with one or two players.
     */
    private function createFinalRound(array $players, int $roundNumber): array
    {
        $match = [
            'player1_id' => $players[0],
            'player2_id' => $players[1] ?? null,
            'is_bye' => ($players[1] ?? null) === null,
            'round' => $roundNumber,
        ];

        return [
            'round' => $roundNumber,
            'matches' => [$match],
            'is_final' => $roundNumber === 1,
        ];
    }

    /**
     * Create intermediate round by splitting players.
     */
    private function createIntermediateRound(array $players, int $roundNumber): array
    {
        $half = intdiv(count($players), 2);
        $leftPlayers = array_slice($players, 0, $half);
        $rightPlayers = array_slice($players, $half);

        $leftBracket = $this->buildBracketStructure($leftPlayers, $roundNumber);
        $rightBracket = $this->buildBracketStructure($rightPlayers, $roundNumber);

        return [
            'round' => $roundNumber,
            'left' => $leftBracket,
            'right' => $rightBracket,
            'winner_match' => [
                'player1_id' => null, // Winner of left bracket
                'player2_id' => null, // Winner of right bracket
                'is_bye' => false,
                'round' => $roundNumber + 1,
            ],
        ];
    }

    /**
     * Generate matches from bracket structure.
     */
    public function generateMatchesFromBracket(Competition $competition, array $bracket, int $roundNumber = 1): void
    {
        $this->matchOrder = 1;
        $this->generateMatchesRecursive($competition, $bracket, $roundNumber);
    }
    
    private $matchOrder = 1;

    private function generateMatchesRecursive(Competition $competition, array $bracket, int $roundNumber = 1): void
    {
        if (isset($bracket['matches'])) {
            $this->createMatchesForRound($competition, $bracket['matches'], $roundNumber);
        } else {
            // Handle sub-brackets recursively
            if (isset($bracket['left'])) {
                $this->generateMatchesRecursive($competition, $bracket['left'], $roundNumber);
            }
            if (isset($bracket['right'])) {
                $this->generateMatchesRecursive($competition, $bracket['right'], $roundNumber);
            }
        }
    }

    /**
     * Create matches for a specific round.
     */
    private function createMatchesForRound(Competition $competition, array $matches, int $roundNumber): void
    {
        foreach ($matches as $matchData) {
            if ($matchData['is_bye']) {
                $this->createByeMatch($competition, $matchData, $roundNumber);
            } else {
                $this->createRegularMatch($competition, $matchData, $roundNumber);
            }
            $this->matchOrder++;
        }
    }

    /**
     * Create a bye match.
     */
    private function createByeMatch(Competition $competition, array $matchData, int $roundNumber): void
    {
        CompetitionMatch::create([
            'competition_id' => $competition->id,
            'home_player_id' => $matchData['player1_id'],
            'away_player_id' => null,
            'phase' => 'knockout',
            'round_number' => $roundNumber,
            'match_order' => $this->matchOrder,
            'status' => 'completed',
            'home_score' => 1,
            'away_score' => 0,
            'played_at' => now(),
            'is_bye' => true,
        ]);
    }

    /**
     * Create a regular knockout match.
     */
    private function createRegularMatch(Competition $competition, array $matchData, int $roundNumber): void
    {
        CompetitionMatch::create([
            'competition_id' => $competition->id,
            'home_player_id' => $matchData['player1_id'],
            'away_player_id' => $matchData['player2_id'],
            'phase' => 'knockout',
            'round_number' => $roundNumber,
            'match_order' => $this->matchOrder,
            'status' => 'scheduled',
            'scheduled_at' => now(),
        ]);
    }

    /**
     * Get advancing players from completed groups.
     */
    public function getAdvancingPlayers(Competition $competition): array
    {
        $advancingPlayers = [];

        foreach ($competition->tournamentGroups as $group) {
            $groupAdvancers = $this->getTopPlayersFromGroup($group, $competition->players_advancing_per_group);
            $advancingPlayers = array_merge($advancingPlayers, $groupAdvancers);
        }

        return $advancingPlayers;
    }

    /**
     * Get top players from a group based on standings.
     */
    private function getTopPlayersFromGroup($group, int $count): array
    {
        return collect($group->standings)
            ->sortByDesc(function ($player) {
                return [$player['points'], $player['sets_won'], $player['points_scored']];
            })
            ->take($count)
            ->pluck('player_id')
            ->toArray();
    }

    /**
     * Get the next power of two for bracket size.
     */
    private function getNextPowerOfTwo(int $number): int
    {
        return (int) pow(2, ceil(log($number, 2)));
    }

    /**
     * Validate bracket structure.
     */
    public function validateBracket(array $bracket): bool
    {
        // Basic validation - can be extended
        return isset($bracket['round']) && (isset($bracket['matches']) || isset($bracket['left']));
    }
}