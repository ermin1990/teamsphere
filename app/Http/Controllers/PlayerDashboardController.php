<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\Player;
use App\Models\Standing;
use Illuminate\Support\Facades\DB;

class PlayerDashboardController extends Controller
{
    /**
     * Player's own history, grouped by season, plus a simple cumulative
     * ranking within each organization they play in.
     */
    public function dashboard()
    {
        $userId = auth()->id();

        $competitions = Competition::whereHas('players', function ($query) use ($userId) {
                $query->where('players.user_id', $userId);
            })
            ->with(['organization', 'sport', 'season'])
            ->get();

        $bySeason = $competitions->groupBy(function ($competition) {
            return $competition->season->name ?? 'Bez sezone';
        });

        // Cumulative ranking per organization: sum of standings points across
        // all of that organization's competitions, position among its players.
        $organizationIds = $competitions->pluck('organization_id')->unique();
        $playerIdsByOrg = Player::where('user_id', $userId)
            ->whereIn('organization_id', $organizationIds)
            ->pluck('id', 'organization_id');

        $rankings = [];
        foreach ($organizationIds as $organizationId) {
            $playerId = $playerIdsByOrg[$organizationId] ?? null;
            if (!$playerId) {
                continue;
            }

            $ranked = Standing::whereHas('competition', function ($query) use ($organizationId) {
                    $query->where('organization_id', $organizationId);
                })
                ->whereNotNull('player_id')
                ->select('player_id', DB::raw('SUM(points) as total_points'))
                ->groupBy('player_id')
                ->orderByDesc('total_points')
                ->pluck('player_id')
                ->values();

            $position = $ranked->search($playerId);
            if ($position !== false) {
                $rankings[$organizationId] = [
                    'position' => $position + 1,
                    'total' => $ranked->count(),
                ];
            }
        }

        return view('player.dashboard', compact('bySeason', 'rankings'));
    }
}
