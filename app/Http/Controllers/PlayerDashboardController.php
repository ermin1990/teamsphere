<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionMatch;
use App\Models\Player;
use App\Models\Standing;
use Illuminate\Http\Request;
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

        $playerIds = Player::where('user_id', $userId)->pluck('id');

        $matchesQuery = fn () => CompetitionMatch::where(function ($query) use ($playerIds) {
                $query->whereIn('home_player_id', $playerIds)
                      ->orWhereIn('away_player_id', $playerIds);
            })
            ->with(['competition', 'homePlayer', 'awayPlayer']);

        $upcomingMatches = $matchesQuery()
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->orderByRaw("status = 'in_progress' desc")
            ->orderBy('scheduled_at')
            ->limit(6)
            ->get();

        $completedMatches = $matchesQuery()
            ->where('status', 'completed')
            ->orderByDesc('played_at')
            ->limit(6)
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

        return view('player.dashboard', compact('bySeason', 'rankings', 'upcomingMatches', 'completedMatches', 'playerIds'));
    }

    /**
     * All matches across every competition/organization this player plays
     * in - the per-competition self-entry flow only ever shows one
     * competition at a time, this aggregates across all of them. Filterable
     * by season/league/round (shown above the list) once the player has
     * more than one to choose from.
     */
    public function matches(Request $request)
    {
        $playerIds = Player::where('user_id', auth()->id())->pluck('id');

        $playerMatches = fn () => CompetitionMatch::where(function ($query) use ($playerIds) {
            $query->whereIn('home_player_id', $playerIds)
                  ->orWhereIn('away_player_id', $playerIds);
        });

        $competitionIds = $playerMatches()->distinct()->pluck('competition_id');
        $competitions = Competition::whereIn('id', $competitionIds)->with(['season', 'sport'])->orderBy('name')->get();
        $seasons = $competitions->pluck('season')->filter()->unique('id')->sortBy('name')->values();
        $sports = $competitions->pluck('sport')->filter()->unique('id')->sortBy('name')->values();

        $filteredQuery = fn () => $playerMatches()
            ->when($request->filled('season_id'), function ($query) use ($competitions, $request) {
                $query->whereIn('competition_id', $competitions->where('season_id', $request->season_id)->pluck('id'));
            })
            ->when($request->filled('sport_id'), function ($query) use ($competitions, $request) {
                $query->whereIn('competition_id', $competitions->where('sport_id', $request->sport_id)->pluck('id'));
            })
            ->when($request->filled('competition_id'), fn ($query) => $query->where('competition_id', $request->competition_id));

        $rounds = $filteredQuery()
            ->selectRaw('DISTINCT COALESCE(round_number, round) as round_value')
            ->orderBy('round_value')
            ->pluck('round_value')
            ->filter(fn ($round) => $round !== null)
            ->values();

        $matches = $filteredQuery()
            ->when($request->filled('round'), fn ($query) => $query->whereRaw('COALESCE(round_number, round) = ?', [$request->round]))
            ->with(['competition', 'homePlayer', 'awayPlayer', 'venue'])
            ->orderByRaw('played_at IS NULL, played_at DESC, scheduled_at DESC')
            ->paginate(20)
            ->withQueryString();

        return view('player.matches.index', compact('matches', 'playerIds', 'competitions', 'seasons', 'sports', 'rounds'));
    }
}
