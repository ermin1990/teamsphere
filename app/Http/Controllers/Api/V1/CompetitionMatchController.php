<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CompetitionMatchResource;
use App\Models\Competition;
use App\Models\CompetitionMatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompetitionMatchController extends Controller
{
    use ApiResponses;

    /**
     * List matches for a competition. Supports filtering by ?phase=.
     */
    public function index(Request $request, Competition $competition): JsonResponse
    {
        $this->authorize('view', $competition->organization);

        $query = $competition->matches()
            ->with(['homePlayer', 'awayPlayer', 'homeTeam', 'awayTeam']);

        if ($request->filled('phase')) {
            $query->where('phase', $request->input('phase'));
        }

        $matches = $query
            ->orderBy('round_number')
            ->orderBy('match_order')
            ->paginate($this->perPage($request));

        return $this->paginated($matches, CompetitionMatchResource::class);
    }

    /**
     * Show a single match.
     */
    public function show(Competition $competition, CompetitionMatch $match): JsonResponse
    {
        $this->authorize('view', $competition->organization);

        abort_unless($match->competition_id === $competition->id, 404);

        $match->load(['homePlayer', 'awayPlayer', 'homeTeam', 'awayTeam']);

        return $this->ok(new CompetitionMatchResource($match));
    }

    /**
     * Score entry / status update for a match. Matches are structurally
     * generated, not created/deleted via the API. Mirrors
     * CompetitionController::updateMatch's standings-propagation side effects.
     */
    public function update(Request $request, Competition $competition, CompetitionMatch $match): JsonResponse
    {
        $this->authorize('update', $competition->organization);

        abort_unless($match->competition_id === $competition->id, 404);

        $validated = $request->validate([
            'status' => ['sometimes', 'required', 'in:scheduled,in_progress,completed,forfeited,cancelled'],
            'home_score' => ['nullable', 'integer', 'min:0', 'max:10'],
            'away_score' => ['nullable', 'integer', 'min:0', 'max:10'],
            'sets' => ['nullable', 'array'],
            'sets.*.home' => ['required_with:sets', 'integer', 'min:0'],
            'sets.*.away' => ['required_with:sets', 'integer', 'min:0'],
            'forfeited_by' => ['required_if:status,forfeited', 'in:home,away'],
            'played_at' => ['nullable', 'date'],
        ]);

        if (($validated['status'] ?? null) === 'completed' && !empty($validated['sets'])) {
            $homeSetsWon = collect($validated['sets'])->filter(fn ($set) => $set['home'] > $set['away'])->count();
            $awaySetsWon = collect($validated['sets'])->filter(fn ($set) => $set['away'] > $set['home'])->count();

            if (($validated['home_score'] ?? $homeSetsWon) !== $homeSetsWon || ($validated['away_score'] ?? $awaySetsWon) !== $awaySetsWon) {
                return $this->fail('home_score/away_score ne odgovaraju broju pobjeđenih setova.', 422);
            }
        }

        $match->update($validated);

        // Update standings if tournament
        if ($competition->type === 'tournament' && $match->tournamentGroup) {
            $match->refresh();
            $groupService = app(\App\Services\TournamentGroupService::class);
            $groupService->recalculateGroupStandings($match->tournamentGroup);
        }

        // Propagate winner changes in knockout bracket
        if ($competition->type === 'tournament' && $match->phase === 'knockout' && $match->status === 'completed') {
            $competition->propagateWinnerChanges($match);
        }

        // Update standings for individual/team leagues
        if ($competition->isLeague()) {
            app(\App\Services\LeagueStandingsService::class)->rebuildForCompetition($competition);
        }

        return $this->ok(new CompetitionMatchResource($match->fresh(['homePlayer', 'awayPlayer', 'homeTeam', 'awayTeam'])), 'Match updated successfully');
    }
}
