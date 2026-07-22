<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\StandingResource;
use App\Models\Competition;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StandingController extends Controller
{
    use ApiResponses;

    /**
     * List standings for a competition, ordered by position. Read-only -
     * standings are computed by LeagueStandingsService / TournamentGroupService.
     * Optionally filter to a single tournament group via ?group_id=.
     */
    public function index(Request $request, Competition $competition): JsonResponse
    {
        $this->authorize('view', $competition->organization);

        $query = $competition->standings();

        if ($request->filled('group_id')) {
            $query->where('tournament_group_id', $request->input('group_id'));
        }

        $standings = $query->get();

        return $this->ok(StandingResource::collection($standings));
    }
}
