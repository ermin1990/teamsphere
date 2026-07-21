<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\LeagueMatchResource;
use App\Models\League;
use App\Models\LeagueMatch;
use App\Models\Table;
use App\Services\LeagueStandingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeagueMatchController extends Controller
{
    use ApiResponses;

    public function index(League $league): JsonResponse
    {
        $this->authorize('view', $league->organization);

        $matches = LeagueMatch::where('competition_id', $league->id)
            ->orderBy('round')
            ->orderBy('scheduled_at')
            ->get();

        return $this->ok(LeagueMatchResource::collection($matches));
    }

    public function show(League $league, LeagueMatch $match): JsonResponse
    {
        $organization = $league->organization;

        $this->authorize('view', $organization);

        if ($match->competition_id !== $league->id) {
            return $this->fail('Match does not belong to this league.', 404);
        }

        return $this->ok(new LeagueMatchResource($match));
    }

    public function update(Request $request, League $league, LeagueMatch $match, LeagueStandingsService $standingsService): JsonResponse
    {
        $organization = $league->organization;

        $this->authorize('update', $organization);

        if ($match->competition_id !== $league->id) {
            return $this->fail('Match does not belong to this league.', 404);
        }

        $validated = $request->validate([
            'home_score' => ['nullable', 'integer', 'min:0'],
            'away_score' => ['nullable', 'integer', 'min:0'],
            'status' => ['sometimes', 'required', 'string', 'in:scheduled,in_progress,completed,forfeited,cancelled'],
            'sets' => ['nullable', 'array'],
            'sets.*.home' => ['required_with:sets', 'integer', 'min:0'],
            'sets.*.away' => ['required_with:sets', 'integer', 'min:0'],
            'played_at' => ['nullable', 'date'],
            'table_id' => ['nullable', 'exists:tables,id'],
            'referee_user_id' => ['nullable', 'exists:users,id'],
        ]);

        if (array_key_exists('table_id', $validated) && $validated['table_id'] !== null) {
            $validTable = Table::where('id', $validated['table_id'])
                ->where('organization_id', $organization->id)
                ->exists();

            if (!$validTable) {
                return $this->fail('Selected table does not belong to this organization.', 422);
            }
        }

        if (array_key_exists('referee_user_id', $validated) && $validated['referee_user_id'] !== null) {
            $isReferee = $organization->organizationUsers()
                ->where('user_id', $validated['referee_user_id'])
                ->where('role', 'referee')
                ->exists();

            if (!$isReferee) {
                return $this->fail('Selected user is not a referee for this organization.', 422);
            }
        }

        $match->update($validated);

        if (isset($validated['status']) && in_array($validated['status'], ['completed', 'forfeited'])) {
            $standingsService->rebuildForCompetition($match->competition);
        }

        return $this->ok(new LeagueMatchResource($match->refresh()), 'Match updated successfully');
    }
}
