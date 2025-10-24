<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\TournamentGroup;
use App\Models\Standing;
use Illuminate\Support\Collection;

class TournamentBracketService
{
    protected $bergerService;
    protected $joolaService;
    
    public function __construct(BergerScheduleService $bergerService, JOOLABracketService $joolaService)
    {
        $this->bergerService = $bergerService;
        $this->joolaService = $joolaService;
    }
    /**
     * Generate matches for a tournament group using Berger system.
     */
    public function generateGroupMatches(Competition $competition, TournamentGroup $group): void
    {
        $this->bergerService->generateGroupMatches($competition, $group);
    }

    /**
     * Get advancing players from all groups.
     */
    public function getAdvancingPlayers(Competition $competition): Collection
    {
        return collect($this->joolaService->getAdvancingPlayers($competition));
    }

    /**
     * Generate JOOLA elimination bracket and matches.
     */
    public function generateJOOLAEliminationBracket(Competition $competition): void
    {
        $bracket = $this->joolaService->generateBracket($competition);
        
        if (empty($bracket)) {
            return;
        }
        
        $this->joolaService->generateMatchesFromBracket($competition, $bracket);
    }

    /**
     * Advance winners to the next round.
     */
    public function advanceToNextRound(Competition $competition, int $currentRound): void
    {
        $currentRoundMatches = CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->where('round_number', $currentRound)
            ->whereIn('status', ['completed', 'forfeited'])
            ->get();

        // Check if all matches in current round are completed
        $totalMatches = CompetitionMatch::where('competition_id', $competition->id)
            ->where('phase', 'knockout')
            ->where('round_number', $currentRound)
            ->count();

        if ($currentRoundMatches->count() !== $totalMatches) {
            return; // Not all matches completed
        }

        // Get winners
        $winners = [];
        foreach ($currentRoundMatches as $match) {
            if ($match->is_bye) {
                // Bye winner is the non-null player
                $winners[] = $match->home_player_id ?: $match->away_player_id;
            } elseif ($match->home_score > $match->away_score) {
                $winners[] = $match->home_player_id;
            } else {
                $winners[] = $match->away_player_id;
            }
        }

        // If only one winner, tournament is complete
        if (count($winners) <= 1) {
            $competition->update([
                'status' => 'completed',
                'current_phase' => 'completed',
                'knockout_completed_at' => now(),
            ]);
            return;
        }

        // Create next round matches
        $nextRound = $currentRound + 1;
        for ($i = 0; $i < count($winners); $i += 2) {
            $homePlayer = $winners[$i];
            $awayPlayer = $winners[$i + 1] ?? null;

            $isBye = $awayPlayer === null;

            CompetitionMatch::create([
                'competition_id' => $competition->id,
                'home_player_id' => $homePlayer,
                'away_player_id' => $awayPlayer,
                'phase' => 'knockout',
                'round_number' => $nextRound,
                'status' => $isBye ? 'completed' : 'scheduled',
                'scheduled_at' => now(),
                'is_bye' => $isBye,
                'home_score' => $isBye ? 1 : 0,
                'away_score' => $isBye ? 0 : 0,
            ]);
        }
    }
}